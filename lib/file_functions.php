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
 * Outputs a unordered list of functions from a file for further use by ajax functions.
 *
 * @since 1.0.0
 * @package Codex Creator
 * @param string $file_path The absolute path of the file.
 */
function cdxc_get_file_functions($file)
{
    $func_arr = cdxc_get_file_functions_arr($file);

    if (!empty($func_arr)) {
        echo '<ul class="cc-function-tree">';
        foreach ($func_arr as $fnc_name) {
            if (!empty($fnc_name[0])) {
                $func_info = '';
            } else {
                $func_info = '<i class="fa fa-exclamation-triangle" title="' . __('Function does not contain a DocBlock', CDXC_TEXTDOMAIN) . '"></i>';
            }
            echo '<li data-cc-scan-file="' . $file . '" data-cc-scan-function="' . $fnc_name[1] . '" class="cc-file-tree-function">' . $fnc_name[1] . ' [line: ' . $fnc_name[2] . ']';
            echo '<span class="cc-function-info-bloc">' . $func_info . '</span>';
            echo '</li>';
        }
        echo '</ul>';
    }

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