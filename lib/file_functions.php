<?php
/**
 * Functions for dealing with files on the server
 *
 * @since 1.0.0
 * @package Codex Creator
 */

function codex_creator_allowed_file_types()
{
    return array('php', 'js', 'css',);
}


function codex_creator_get_file($file)
{
    global $wp_filesystem;
    if (!$wp_filesystem) {
        $wp_filesystem = codex_creator_init_filesystem();
    }
    return $wp_filesystem->get_contents($file);
}

function codex_creator_get_file_array($file)
{
    global $wp_filesystem;
    if (!$wp_filesystem) {
        $wp_filesystem = codex_creator_init_filesystem();
    }
    return $wp_filesystem->get_contents_array($file);
}


function codex_creator_has_file_docblock($file_path)
{
    global $wp_filesystem;

    $files_output = codex_creator_get_file_array($file_path);
    $docblock = codex_creator_get_file_docblock($files_output);

    if ($docblock) {
        return $docblock;
    } else {
        return false;
    }

    //echo $files_output;
    //print_r($files_output);
    //$phpdoc = new \phpDocumentor\Reflection\DocBlock($files_output );

    //echo $phpdoc->getShortDescription().'###';

}

function codex_creator_get_file_functions_arr($file)
{
    $file_output = codex_creator_get_file($file);

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

function codex_creator_get_file_functions($file)
{

    $func_arr = codex_creator_get_file_functions_arr($file);

    if (!empty($func_arr)) {
        echo '<ul class="cc-function-tree">';
        foreach ($func_arr as $fnc_name) {
            if (!empty($fnc_name[0])) {
                $func_info = '';//'<i class="fa fa-exclamation-triangle" title="'.__( 'Function does not contain a DocBlock', WP_CODEX_TEXTDOMAIN ).'"></i>';
            } else {
                $func_info = '<i class="fa fa-exclamation-triangle" title="' . __('Function does not contain a DocBlock', WP_CODEX_TEXTDOMAIN) . '"></i>';
            }
            echo '<li data-cc-scan-file="' . $file . '" data-cc-scan-function="' . $fnc_name[1] . '" class="cc-file-tree-function">' . $fnc_name[1] . ' [line: ' . $fnc_name[2] . ']';
            echo '<span class="cc-function-info-bloc">' . $func_info . '</span>';
            echo '</li>';
        }
        echo '</ul>';
    }


    /*foreach ( $file_output as $line ) {
        if (strpos($line,'are') !== false) {
    }*/

}


function codex_creator_get_file_docblock($lines)
{
    if (empty($lines)) {
        return false;
    }

    $i = 0;
    $start = '';
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

    if ($start && $end) {

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