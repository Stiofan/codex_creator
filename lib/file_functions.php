<?php
/**
 * Functions for dealing with files on the server
 *
 * @since 1.0.0
 * @package Codex Creator
 */

/**
 * Returns an array of allowed file types.
 * @since 1.0.0
 * @package Codex Creator
 * @return array An array of allowed file types.
 */
function cdxc_allowed_file_types()
{
    return array('php', 'js', 'css',);
}

/**
 * Get the contents of a file on the server as a variable.
 *
 * @since 1.0.0
 * @package Codex Creator
 * @param string $file The absolute path of the file.
 * @return mixed The contents of the file if successful. False on failure.
 */
function cdxc_get_file($file)
{
    global $wp_filesystem;
    if (!$wp_filesystem) {
        $wp_filesystem = cdxc_init_filesystem();
    }
    return $wp_filesystem->get_contents($file);
}

/**
 * Get the contents of a file on the server as an array of file lines.
 *
 * @since 1.0.0
 * @package Codex Creator
 * @param string $file The absolute path of the file.
 * @return mixed The contents of the file as an array if successful. False on failure.
 */
function cdxc_get_file_array($file)
{
    global $wp_filesystem;
    if (!$wp_filesystem) {
        $wp_filesystem = cdxc_init_filesystem();
    }
    return $wp_filesystem->get_contents_array($file);
}

/**
 * Check if a file has a DocBlock and if so return it.
 *
 * @since 1.0.0
 * @package Codex Creator
 * @param string $file_path The absolute path of the file.
 * @return bool|string The DocBlock if successful. False on failure.
 */
function cdxc_has_file_docblock($file_path)
{
    global $wp_filesystem;

    $files_output = cdxc_get_file_array($file_path);
    $docblock = cdxc_get_file_docblock($files_output);

    if ($docblock) {
        return $docblock;
    } else {
        return false;
    }

}

/**
 * Reads a file for function names and DocBlocks.
 *
 * Returns an array containing the function DocBlock, function name and the file line number where the function starts.
 *
 *    array('DOCBLOCK_CONTENT','FUNCTION_NAME','FUNCTION_LINE_NUMBER');
 *
 * @since 1.0.0
 * @package Codex Creator
 * @param string $file_path The absolute path of the file.
 * @return array|bool The array of DocBlock info if successful. False if no functions present in file.
 */
function cdxc_get_file_functions_arr($file)
{
    $file_output = cdxc_get_file($file);

    $tokens = token_get_all($file_output);
    //print_r($tokens);
    $func_arr = array();
    foreach ($tokens as $key => $token) {

        if (is_array($token)) {

            //if we detect a class assume there are no functions
            if (token_name((int)$token[0]) == 'T_CLASS') {
                break;
            }


            if (token_name((int)$token[0]) == 'T_FUNCTION') {
                // check for docblock
                $doc_block = '';
                $fnc_name = '';
                if (token_name((int)$tokens[$key - 1][0]) == 'T_WHITESPACE' && token_name((int)$tokens[$key - 2][0]) == 'T_DOC_COMMENT') {
                    $doc_block = $tokens[$key - 2][1];


                }


                if (token_name((int)$tokens[$key + 1][0]) == 'T_WHITESPACE' && token_name((int)$tokens[$key + 2][0]) == 'T_STRING') {
                    $fnc_name = $tokens[$key + 2][1];
                    $func_arr[] = array($doc_block, $fnc_name, $token[2]);
                }


            }


        }

    }

    if (!empty($func_arr)) {
        return $func_arr;
    } else {
        return false;
    }
}

/**
 * Returns a unordered list of functions from a file for further use by ajax functions.
 *
 * @since 1.0.0
 * @package Codex Creator
 * @param string $file_path The absolute path of the file.
 * @return string Returns li elements of functions. Empty string if no functions present
 */
function cdxc_get_file_functions($file)
{
    $func_arr = cdxc_get_file_functions_arr($file);
    $func_content = '';
    if (!empty($func_arr)) {

        foreach ($func_arr as $fnc_name) {
            if (!empty($fnc_name[0])) {
                $func_info = '';
            } else {
                $func_info = '<i class="fa fa-exclamation-triangle" title="' . __('Function does not contain a DocBlock', CDXC_TEXTDOMAIN) . '"></i>';
            }
            $func_content .= '<li data-cc-bit-type="function" data-cc-scan-file="' . $file . '" data-cc-scan-bit="' . $fnc_name[1] . '" class="cc-file-tree-function cc-file-tree-code-bit" ><i class="fa fa-cogs" title="' . __('Function', CDXC_TEXTDOMAIN) . '"></i> ' . $fnc_name[1] . ' [line: ' . $fnc_name[2] . ']';
            $func_content .= '<span class="cc-function-info-bloc">' . $func_info . '</span>';
            $func_content .= '</li>';
        }

    }

    return $func_content;

}

/**
 * Read and return the file DocBlock from a file.
 *
 * @since 1.0.0
 * @package Codex Creator
 * @param array $lines A array of lines from a file.
 * @return bool|string Returns a string of the DocBlock on success. False if no DocBlock.
 */
function cdxc_get_file_docblock($lines)
{
    if (empty($lines)) {
        return false;
    }

    $i = 0;
    $start = false;
    $end = '';
    $docblock = '';

    // find the starting point if any
    foreach ($lines as $line => $value) {
        if ($i == 5) {
            return false;
        }// if we dont find a file opening block within the first 5 lines then bail

        if (strpos($value, '/**') !== false) {
            $start = $line;
            break;
        }
        $i++;
    }

    // find the end point
    foreach ($lines as $line => $value) {

        if (strpos($value, '*/') !== false) {
            $end = $line;
            break;
        }
    }

    if ($start!==false && $end) {

        while ($start <= $end) {
            $docblock .= $lines[$start];
            $start++;
        }

    }

    if ($docblock) {
        return $docblock;
    }

    return false;// if we get here something has gone wrong so bail

}

/**
 * Reads a file for WordPress actions (do_action) and it's DocBlocks.
 *
 * Returns an array containing the action DocBlock, action name and the file line number where the action starts.
 *
 *    array('DOCBLOCK_CONTENT','ACTION_NAME','ACTION_LINE_NUMBER');
 *
 * @since 1.0.0
 * @package Codex Creator
 * @param string $file_path The absolute path of the file.
 * @return array|bool The array of DocBlock info if successful. False if no actions present in file.
 */
function cdxc_get_file_actions_arr($file)
{
    $file_output = cdxc_get_file($file);

    $tokens = token_get_all($file_output);
    //print_r($tokens);
    $action_arr = array();
    foreach ($tokens as $key => $token) {

        if (is_array($token)) {

            //if we detect a class assume there are no functions
           /* if (token_name((int)$token[0]) == 'T_CLASS') {
                break;
            }*/

            /*
             * check if we have a action. do_action(
             */
            if (token_name((int)$token[0]) == 'T_STRING' && $token[1]=='do_action' && !is_array($tokens[$key + 1]) && $tokens[$key + 1]=='(') {
                $doc_block ='';
                /*
                 * check for DocBlock
                 */
                if (token_name((int)$tokens[$key - 1][0]) == 'T_WHITESPACE' && token_name((int)$tokens[$key - 2][0]) == 'T_DOC_COMMENT') {
                    $doc_block = $tokens[$key - 2][1];
                }

                /*
                 * Get action tag name, check the next 5 tokes, if it's not there then it may be a false alarm.
                 */
                if (token_name((int)$tokens[$key + 1][0]) == 'T_CONSTANT_ENCAPSED_STRING'){$action_tag = $tokens[$key + 1][1];}
                elseif (token_name((int)$tokens[$key + 2][0]) == 'T_CONSTANT_ENCAPSED_STRING'){$action_tag = $tokens[$key + 2][1];}
                elseif (token_name((int)$tokens[$key + 3][0]) == 'T_CONSTANT_ENCAPSED_STRING'){$action_tag = $tokens[$key + 3][1];}
                elseif (token_name((int)$tokens[$key + 4][0]) == 'T_CONSTANT_ENCAPSED_STRING'){$action_tag = $tokens[$key + 4][1];}
                elseif (token_name((int)$tokens[$key + 5][0]) == 'T_CONSTANT_ENCAPSED_STRING'){$action_tag = $tokens[$key + 5][1];}
                else{continue;}



                if ($action_tag) {
                    $action_tag = str_replace(array("'",'"'),array('',''),$action_tag);
                    $action_arr[] = array($doc_block, $action_tag, $token[2]);
                }


            }


        }

    }

    if (!empty($action_arr)) {
        return $action_arr;
    } else {
        return false;
    }
}

/**
 * Outputs a unordered list of actions from a file for further use by ajax functions.
 *
 * @since 1.0.0
 * @package Codex Creator
 * @param string $file_path The absolute path of the file.
 * @return string Returns li elements of actions. Empty string if no actions present
 */
function cdxc_get_file_actions($file)
{
    $action_arr = cdxc_get_file_actions_arr($file);
    $func_content = '';
    if (!empty($action_arr)) {

        foreach ($action_arr as $act_name) {
            if (!empty($act_name[0])) {
                $act_info = '';
            } else {
                $act_info = '<i class="fa fa-exclamation-triangle" title="' . __('Action does not contain a DocBlock', CDXC_TEXTDOMAIN) . '"></i>';
            }
            $func_content .=  '<li data-cc-bit-type="action" data-cc-scan-file="' . $file . '" data-cc-scan-bit="' . $act_name[1] . '" class="cc-file-tree-action cc-file-tree-code-bit"><i class="fa fa-bolt" title="' . __('Action', CDXC_TEXTDOMAIN) . '"></i> ' . $act_name[1] . ' [line: ' . $act_name[2] . ']';
            $func_content .=  '<span class="cc-action-info-bloc">' . $act_info . '</span>';
            $func_content .=  '</li>';
        }

    }
    return $func_content;
}


/**
 * Reads a file for WordPress filters (apply_filters) and it's DocBlocks.
 *
 * Returns an array containing the action DocBlock, filter name and the file line number where the filter starts.
 *
 *    array('DOCBLOCK_CONTENT','FILTER_NAME','FILTER_LINE_NUMBER');
 *
 * @since 1.0.0
 * @package Codex Creator
 * @param string $file_path The absolute path of the file.
 * @return array|bool The array of DocBlock info if successful. False if no filters present in file.
 */
function cdxc_get_file_filters_arr($file)
{
    $file_output = cdxc_get_file($file);

    $tokens = token_get_all($file_output);
    //print_r($tokens);
    $action_arr = array();
    foreach ($tokens as $key => $token) {

        if (is_array($token)) {

            //if we detect a class assume there are no functions
            /* if (token_name((int)$token[0]) == 'T_CLASS') {
                 break;
             }*/

            /*
             * check if we have a filter. apply_filters(
             */
            if (token_name((int)$token[0]) == 'T_STRING' && $token[1]=='apply_filters' && !is_array($tokens[$key + 1]) && $tokens[$key + 1]=='(') {
                $doc_block ='';
                /*
                 * check for DocBlock, this MUST be on the proceeding line but there can be much stuff in-between so we have to do some funky checking.
                 */
                $line_before = $token[2]-1;
                $current_key = $key;
                while($current_key >= 0) {

                    if(token_name((int)$tokens[$current_key][0]) == 'T_DOC_COMMENT'){
                        /*
                         * we found a preceeding DocBlock, now lets see if its the correct one.
                         */
                        $lines = substr_count($tokens[$current_key][1], PHP_EOL );
                        if($line_before==((int)$tokens[$current_key][2]+$lines)){
                            $doc_block = $tokens[$current_key][1];
                        }

                        $current_key=0;// we have the DocBlock so stop.
                    }
                    elseif(isset( $tokens[$current_key][2]) && $tokens[$current_key][2]< ($line_before-40)){// at most check 40 lines back
                        //gone to far let's stop the loop
                        $current_key=0;
                    }
                    $current_key--;

                }


                /*
                 * Get action tag name, check the next 5 tokes, if it's not there then it may be a false alarm.
                 */
                if (token_name((int)$tokens[$key + 1][0]) == 'T_CONSTANT_ENCAPSED_STRING'){$action_tag = $tokens[$key + 1][1];}
                elseif (token_name((int)$tokens[$key + 2][0]) == 'T_CONSTANT_ENCAPSED_STRING'){$action_tag = $tokens[$key + 2][1];}
                elseif (token_name((int)$tokens[$key + 3][0]) == 'T_CONSTANT_ENCAPSED_STRING'){$action_tag = $tokens[$key + 3][1];}
                elseif (token_name((int)$tokens[$key + 4][0]) == 'T_CONSTANT_ENCAPSED_STRING'){$action_tag = $tokens[$key + 4][1];}
                elseif (token_name((int)$tokens[$key + 5][0]) == 'T_CONSTANT_ENCAPSED_STRING'){$action_tag = $tokens[$key + 5][1];}
                else{continue;}


                if ($action_tag) {
                    $action_tag = str_replace(array("'",'"'),array('',''),$action_tag);
                    $action_arr[] = array($doc_block, $action_tag, $token[2]);
                }


            }


        }

    }

    if (!empty($action_arr)) {
        return $action_arr;
    } else {
        return false;
    }
}

/**
 * Outputs a unordered list of filters from a file for further use by ajax functions.
 *
 * @since 1.0.0
 * @package Codex Creator
 * @param string $file_path The absolute path of the file.
 * @return string Returns li elements of filters. Empty string if no filters present
 */
function cdxc_get_file_filters($file)
{
    $action_arr = cdxc_get_file_filters_arr($file);
    $func_content = '';
    if (!empty($action_arr)) {

        foreach ($action_arr as $act_name) {
            if (!empty($act_name[0])) {
                $act_info = '';
            } else {
                $act_info = '<i class="fa fa-exclamation-triangle" title="' . __('Filter does not contain a DocBlock', CDXC_TEXTDOMAIN) . '"></i>';
            }
            $func_content .=  '<li data-cc-bit-type="filter" data-cc-scan-file="' . $file . '" data-cc-scan-bit="' . $act_name[1] . '" class="cc-file-tree-action cc-file-tree-code-bit"><i class="fa fa-filter" title="' . __('Filter', CDXC_TEXTDOMAIN) . '"></i> ' . $act_name[1] . ' [line: ' . $act_name[2] . ']';
            $func_content .=  '<span class="cc-action-info-bloc">' . $act_info . '</span>';
            $func_content .=  '</li>';
        }

    }
    return $func_content;
}


function cdxc_get_parse_file($file){

    $code =  cdxc_get_file($file);

    $parser = new PhpParser\Parser(new PhpParser\Lexer\Emulative);

    try {
        $stmts = $parser->parse($code);
        // $stmts is an array of statement nodes
        //echo $nodeDumper->dump($stmts), "\n";
    } catch (PhpParser\Error $e) {
        return 'Parse Error: '. $e->getMessage();
    }

   //print_r($stmts);
    return $stmts;
}

function cdxc_parse_file_for_file($file,$c_name,$c_type)
{

    $stmts = cdxc_get_parse_file($file);
    $functions_arr = array();
    $actions_arr = array();
    $filters_arr = array();
    if(!is_array($stmts)){echo $stmts;exit;}
    else{
        $file_arr = cdxc_get_file_array($file);
        //print_r($stmts);
        foreach($stmts as $part){
            //print_r($part);
            //echo $part->getType()." \n\r";
            //echo $part->name ." \n\r";
           // echo $part->getDocComment();
           // print_r($part->getAttributes());
            //echo '#####################################'." \n\r";
            /*
             * If a main function.
             */
            if($part->getType()=='Stmt_Function'){

                $func = array();

                $doc_com =  $part->getDocComment();
                if(is_object($doc_com)){
                    $doc_com = $part->getDocComment()->getText();
                }

                $func[0] = $doc_com;
                $func[1] = $part->name;
                $func[2] = $part->getLine();
                $functions_arr[]=$func;


                $hooks_arr = array();
                foreach ($part->stmts as $func_part) {

                    //echo $func_part->getType()." \n\r";
                    //print_r($func_part);
                    //echo '@@#####################################'." \n\r";

                    if($func_part->getType()=='Expr_Assign' && isset($func_part->expr->name->parts[0]) && $func_part->expr->name->parts[0]=='apply_filters' ){// check for apply filters being assigned to var
                       // echo '@@@@@@@@@@@@@@@#####################################'." \n\r";

                        //print_r($func_part);
                        $hook_doc_com =  $func_part->getDocComment();
                        if(is_object($hook_doc_com)){
                            $hook_doc_com = $func_part->getDocComment()->getText();
                        }

                        /*
                         * Build the source code section.
                         */
                        $hook_start = $func_part->getLine();
                        $hook_end = $func_part->getAttribute('endLine');
                        $code = '';
                        $line = $hook_start-1;
                        if ($line && $hook_end) {
                            if ($hook_start == $hook_end) {
                                $code = $file_arr[$line];
                            } else {

                                while ($line <= $hook_end) {
                                    $code .= $file_arr[$line];
                                    $line++;
                                }
                            }
                        }
                        $code = addslashes_gpc($code);

                        $hooks = array();
                        $hooks[0] = $hook_doc_com;
                        $hooks[1] = $func_part->expr->args[0]->value->value;
                        $hooks[2] = $func_part->getLine();
                        $hooks[3] = 'filter';
                        $hooks_arr[]=$hooks;
                        //print_r($hooks);
                        $filters_arr[]=$hooks;
                        cdxc_sync_filter($file,$hooks,$c_name,$func,$c_type,$code);
                    }
                    elseif($func_part->getType()=='Stmt_Echo' && isset($func_part->exprs[0]->name->parts[0]) && $func_part->exprs[0]->name->parts[0]=='apply_filters' ){// check for apply filters being assigned to var
                        //echo '@@@@@@@@@@@@@@@#####################################'." \n\r";
                        //print_r($func_part);
                        $hook_doc_com =  $func_part->getDocComment();
                        if(is_object($hook_doc_com)){
                            $hook_doc_com = $func_part->getDocComment()->getText();
                        }

                        /*
                         * Build the source code section.
                         */
                        $hook_start = $func_part->getLine();
                        $hook_end = $func_part->getAttribute('endLine');
                        $code = '';
                        $line = $hook_start-1;
                        if ($line && $hook_end) {
                            if ($hook_start == $hook_end) {
                                $code = $file_arr[$line];
                            } else {
                                while ($line <= $hook_end) {
                                    $code .= $file_arr[$line];
                                    $line++;
                                }
                            }
                        }

                        $code = addslashes_gpc($code);

                        $hooks = array();
                        $hooks[0] = $hook_doc_com;
                        $hooks[1] = $func_part->exprs[0]->args[0]->value->value;
                        $hooks[2] = $func_part->getLine();
                        $hooks[3] = 'filter';
                        $hooks_arr[]=$hooks;
                        //print_r($hooks);
                        $filters_arr[]=$hooks;
                        cdxc_sync_filter($file,$hooks,$c_name,$func,$c_type,$code);
                    }
                    elseif($func_part->getType()=='Expr_FuncCall' && isset($func_part->name->parts[0]) && $func_part->name->parts[0]=='do_action') {

                        $hook_doc_com =  $func_part->getDocComment();
                        if(is_object($hook_doc_com)){
                            $hook_doc_com = $func_part->getDocComment()->getText();
                        }

                        /*
                         * Build the source code section.
                         */
                        $hook_start = $func_part->getLine();
                        $hook_end = $func_part->getAttribute('endLine');
                        $code = '';
                        $line = $hook_start-1;
                        if ($line && $hook_end) {
                            if ($hook_start == $hook_end) {
                                $code = $file_arr[$line];
                            } else {
                                while ($line <= $hook_end) {
                                    $code .= $file_arr[$line];
                                    $line++;
                                }
                            }
                        }
                        $code = addslashes_gpc($code);

                        $hooks = array();
                        $hooks[0] = $hook_doc_com;
                        $hooks[1] = $func_part->args[0]->value->value;
                        $hooks[2] = $func_part->getLine();
                        $hooks[3] = 'action';
                        $hooks_arr[]=$hooks;
                        $actions_arr[]=$hooks;
                        cdxc_sync_action($file,$hooks,$c_name,$func,$c_type,$code);

                    }
                }

                /*
                 * Build the source code section.
                 */
                $func_start = $part->getLine();
                $func_end = $part->getAttribute('endLine');
                $code = '';
                $line = $func_start-1;
                if ($line && $func_end) {
                    if ($func_start == $func_end) {
                        $code = $file_arr[$line];
                    } else {
                        while ($line <= $func_end) {
                            $code .= $file_arr[$line];
                            $line++;
                        }
                    }
                }
                $code = addslashes_gpc($code);


                /*
                 * save the function
                 */
                cdxc_sync_function($file,$func,$c_name,$hooks_arr,$c_type,$code);


            }elseif($part->getType()=='Stmt_Function'){


            }
        }

    }

   //print_r($functions_arr);

    $ele = array($functions_arr, $actions_arr, $filters_arr);
    return $ele;

}

//function cdxc_