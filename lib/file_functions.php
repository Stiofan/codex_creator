<?php

function codex_creator_allowed_file_types() {
	return array('php','js','css',);
}


function codex_creator_get_file($file) {
	global $wp_filesystem;
    if(!$wp_filesystem){$wp_filesystem = codex_creator_init_filesystem();}
	return $wp_filesystem->get_contents($file);
}

function codex_creator_get_file_array($file) {
	global $wp_filesystem;
    if(!$wp_filesystem){$wp_filesystem = codex_creator_init_filesystem();}
	return $wp_filesystem->get_contents_array($file);
}


function codex_creator_has_file_docblock($file_path) {
	global $wp_filesystem;

	$files_output = codex_creator_get_file_array($file_path);
	$docblock = codex_creator_get_file_docblock($files_output);

	if($docblock){
		return $docblock;
	}else{return false;}

	//echo $files_output;
	//print_r($files_output);
	//$phpdoc = new \phpDocumentor\Reflection\DocBlock($files_output );

	//echo $phpdoc->getShortDescription().'###';

}

function codex_creator_get_file_functions_arr( $file )
{
    $file_output = codex_creator_get_file($file);

    $tokens = token_get_all($file_output);

    $func_arr = array();
    foreach ($tokens as $key => $token) {

        if (is_array($token)) {
            //list($id, $text) = $token;
            //echo token_name($id)."\r\n";
            if ($token[0] == '334') {
                // check for docblock
                $doc_block = '';
                $fnc_name = '';
                if ($tokens[$key - 1][0] == '375' && $tokens[$key - 2][0] == '371') {
                    $doc_block = $tokens[$key - 2][1];


                }

                if ($tokens[$key + 1][0] == '375' && $tokens[$key + 2][0] == '307') {
                    $fnc_name = $tokens[$key + 2][1];
                    $func_arr[] = array($doc_block, $fnc_name, $token[2]);
                }


            }


        }

    }


    if(!empty($func_arr)) {
        return $func_arr;
    }else{
        return false;
    }
}

function codex_creator_get_file_functions( $file ) {

    codex_creator_get_file_functions_arr( $file );

	if(!empty($func_arr)){
		echo '<ul class="cc-function-tree">';
		foreach ( $func_arr as $fnc_name ) {
			if(!empty($fnc_name[0])){
				$func_info = '';//'<i class="fa fa-exclamation-triangle" title="'.__( 'Function does not contain a DocBlock', WP_CODEX_TEXTDOMAIN ).'"></i>';
			}else{
				$func_info = '<i class="fa fa-exclamation-triangle" title="'.__( 'Function does not contain a DocBlock', WP_CODEX_TEXTDOMAIN ).'"></i>';
			}
			echo '<li data-cc-scan-function="'.$file.'" class="cc-file-tree-function">'.$fnc_name[1].' [line: '.$fnc_name[2].']';
			echo '<span class="cc-function-info-bloc">'.$func_info.'</span>';
			echo '</li>';
		}
		echo '</ul>';
	}


	/*foreach ( $file_output as $line ) {
		if (strpos($line,'are') !== false) {
	}*/

}

function codex_creator_post_exits($title, $cat)
{   global $wpdb;

    $term_id = $wpdb->get_var($wpdb->prepare("SELECT term_id FROM $wpdb->terms WHERE 'name'=%s", $cat));
    if(! $term_id ){return false;}

    $post_id = $wpdb->get_var($wpdb->prepare("SELECT p.ID FROM $wpdb->posts p JOIN $wpdb->term_relationships tr on p.ID=tr.object_id Join $wpdb->term_taxonomy tt ON tt.term_taxonomy_id=tr.term_taxonomy_id WHERE p.post_type='codex_creator' AND tt.term_id=%d AND p.post_title=%s",$term_id ,$title));

    if($post_id){return $post_id;}else{return false;}

}