<?php
/**
 * this file contains functions for the main plugin.
 *
 * @param $type
 */

/*
 * Step 2 functions
 */


/**
 * @param $type
 */
function codex_creator_get_type_list( $type ) {
	if(isset($_REQUEST['c_type']) && $_REQUEST['c_type']=='plugin'){
	//print_r(get_plugins());
		$plugins = get_plugins();

		echo "<ul>";
		foreach ( $plugins as $name=>$plugin ) {
			echo "<li data-plugin='".$name."' onclick=\"codex_creator_step_3('plugin','$name','".$plugin['Name']."');\">".$plugin['Name']."</li>";
		}
		echo "<ul>";

	}else{

	}

	die();
}
// add function to ajax
add_action( 'wp_ajax_codex_creator_get_type_list', 'codex_creator_get_type_list' );


function codex_creator_scan() {
	global $wp_filesystem;
	$wp_filesystem = codex_creator_init_filesystem();
	if(!$wp_filesystem){_e( 'Codex Creator can not access the filesystem.', WP_CODEX_TEXTDOMAIN );exit;}


	if ( isset( $_REQUEST['c_path'] ) && $_REQUEST['c_path'] ) {
		// is in directory
		if ( $pieces = explode( "/", $_REQUEST['c_path'] ) ) {
			$path = $wp_filesystem->wp_plugins_dir().$pieces[0];
		}else{
			// single file
			$path = $wp_filesystem->wp_plugins_dir().$_REQUEST['c_path'];

		}

		// CHeck the status and display the apropriate actions
		codex_creator_status_text( $_REQUEST['c_type'], $_REQUEST['c_name'] );

		$files = $wp_filesystem->dirlist($path,true,true);

		codex_creator_scan_output( $files,$path );



	}
	print_r( $_REQUEST );
	//echo $wp_filesystem->get_contents($wp_filesystem->wp_plugins_dir().$_REQUEST['c_path']);
	die();
}
// add function to ajax
add_action( 'wp_ajax_codex_creator_scan', 'codex_creator_scan' );





function codex_creator_scan_output( $files,$path,$folder='' ) {
	echo "<ul class='cc-file-tree'>";
	foreach ( $files as $file ) {
		//print_r($file);

		if($file['type']=='d'){// directory


			echo "<li class='cc-file-tree-folder'><i class='fa fa-folder-open-o'></i> ".$file['name'];
			if($file['files']){
				$folder_org = $folder ;
				$folder .= $file['name'].'/';
				codex_creator_scan_output( $file['files'],$path,$folder);
				$folder =$folder_org;
			}
			 echo "</li>";


			}elseif($file['type']=='f') {// file
			if(!codex_creator_is_allowed_file( $file['name'] )){continue;}// if file not an allowed file type skip it
			$file_info = '';
			$file_path = $path."/".$folder.$file['name'];
			$has_docblock = codex_creator_has_file_docblock($file_path);
			if($has_docblock){

				//check if the file
				//$file_info = '<i class="fa fa-exclamation-triangle" title="'.__( 'Save', WP_CODEX_TEXTDOMAIN ).'"></i>';


			}else{
				$file_info = '<i class="fa fa-exclamation-triangle" title="'.__( 'File does not contain a file header DocBlock', WP_CODEX_TEXTDOMAIN ).'"></i>';
			}
			echo "<li data-cc-scan-file='".$file_path."' class='cc-file-tree-file' ><i class='fa fa-file-o'></i> $folder".$file['name'];
			echo '<span class="cc-file-info-bloc">'.$file_info.'</span>';

			echo "</li>";

			/**
			 * @todo this is wrong childing, this should be inside the above <li>
			 */
			codex_creator_get_file_functions( $file_path );// get all the functions in the file



		}

	}
	echo "</ul>";

}



function codex_creator_get_file_header( $out ) {
	if(empty($out)){return false;}

	//$file_header = get_file_data( $file, $default_headers, $context = '' )
}



function codex_creator_output_file( $file) {

}

function codex_creator_init_filesystem() {
	$access_type = get_filesystem_method();
	if ( $access_type === 'direct' ) {
		/* you can safely run request_filesystem_credentials() without any issues and don't need to worry about passing in a URL */
		$creds = request_filesystem_credentials( site_url() . '/wp-admin/', '', false, false, array() );

		/* initialize the API */
		if ( ! WP_Filesystem( $creds ) ) {
			/* any problems and we exit */
			return '@@@3';
			return false;
		}

		global $wp_filesystem;
		return $wp_filesystem;
		/* do our file manipulations below */
	}elseif(defined('FTP_USER')) {
		$creds = request_filesystem_credentials( site_url() . '/wp-admin/', '', false, false, array() );

		/* initialize the API */
		if ( ! WP_Filesystem( $creds ) ) {
			/* any problems and we exit */
			return '@@@33';
			return false;
		}

		global $wp_filesystem;
		return '@@@1';
		return $wp_filesystem;

	}else {return '@@@2';
		/* don't have direct write access. Prompt user with our notice */
		add_action( 'admin_notice', 'codex_creator_filesystem_notice' );
		return false;
	}

}
add_action( 'admin_init','codex_creator_filesystem_notice');

function codex_creator_filesystem_notice() {
	$access_type = get_filesystem_method();
	if ( $access_type === 'direct' ) {}elseif(!defined('FTP_USER')){
	?>
	<div class="error">
		<p><?php _e( 'Codex Creator does not have access to your filesystem. Please define your details in wp-config.php as explained here', WP_CODEX_TEXTDOMAIN ); ?> <a target="_blank" href="http://codex.wordpress.org/Editing_wp-config.php#WordPress_Upgrade_Constants">http://codex.wordpress.org/Editing_wp-config.php#WordPress_Upgrade_Constants</a></p>
	</div>
<?php }
}


function codex_creator_add_project() {

	if(!isset( $_REQUEST['c_name'])){ _e('There was a problem adding this project.', WP_CODEX_TEXTDOMAIN );die();}
	$project = array('cat_name' => $_REQUEST['c_name'], 'category_description' => '', 'taxonomy' => 'codex_project' );
	$result = wp_insert_category( $project);

	if($result){
		if ( is_wp_error( $result ) ) {
			$error_string = $result->get_error_message();
			echo '<div id="message" class="error"><p>' . $error_string . '</p></div>';
		}else {
			codex_creator_status_text( $_REQUEST['c_type'], $_REQUEST['c_name'] );
		}

	}else{
		_e('There was a problem adding this project.', WP_CODEX_TEXTDOMAIN );
	}
	die();
}
// add function to ajax
add_action( 'wp_ajax_codex_creator_add_project', 'codex_creator_add_project' );


function codex_creator_sync_file() {

	echo '@@@';
	print_r($_REQUEST);
	die();
	
	if(!isset( $_REQUEST['c_name'])){ _e('There was a problem adding this project.', WP_CODEX_TEXTDOMAIN );die();}
	$project = array('cat_name' => $_REQUEST['c_name'], 'category_description' => '', 'taxonomy' => 'codex_project' );
	$result = wp_insert_category( $project);

	if($result){
		if ( is_wp_error( $result ) ) {
			$error_string = $result->get_error_message();
			echo '<div id="message" class="error"><p>' . $error_string . '</p></div>';
		}else {
			codex_creator_status_text( $_REQUEST['c_type'], $_REQUEST['c_name'] );
		}

	}else{
		_e('There was a problem adding this project.', WP_CODEX_TEXTDOMAIN );
	}
	die();
}
// add function to ajax
add_action( 'wp_ajax_codex_creator_sync_file', 'codex_creator_sync_file' );


function codex_creator_sync_function() {



	echo '@@@';
	print_r($_REQUEST);
	die();
	if(!isset( $_REQUEST['c_name'])){ _e('There was a problem adding this project.', WP_CODEX_TEXTDOMAIN );die();}
	$project = array('cat_name' => $_REQUEST['c_name'], 'category_description' => '', 'taxonomy' => 'codex_project' );
	$result = wp_insert_category( $project);

	if($result){
		if ( is_wp_error( $result ) ) {
			$error_string = $result->get_error_message();
			echo '<div id="message" class="error"><p>' . $error_string . '</p></div>';
		}else {
			codex_creator_status_text( $_REQUEST['c_type'], $_REQUEST['c_name'] );
		}

	}else{
		_e('There was a problem adding this project.', WP_CODEX_TEXTDOMAIN );
	}
	die();
}
// add function to ajax
add_action( 'wp_ajax_codex_creator_sync_function', 'codex_creator_sync_function' );