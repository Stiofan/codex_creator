<?php
/**
 * Mostly small functions mostly used to run checks
 *
 * @since 1.0.0
 * @package Codex_Creator
 */

/**
 * Check if a project exists or not.
 *
 * @since 1.0.0
 * @package Codex_Creator
 * @param string $el The name of the project, plugin or theme name.
 * @return bool Return true if exists. False if not exists.
 */
function cdxc_has_codex($el)
{
    $term = term_exists($el, 'codex_project');
    if ($term !== 0 && $term !== null) {
        return true;
    } else {
        return false;
    }
}

/**
 * Outputs the status of a project and gives further options.
 *
 * Used in step 3 above the file tree, this outputs the status of the project and lets the user add the project or do further actions via ajax.
 *
 * @since 1.0.0
 * @package Codex_Creator
 * @param string Optional. $type Not yet implemented, will be either ('plugin','theme').
 * @param string $el This is the name of the project, the name of the plugin or theme.
 */
function cdxc_status_text($type, $el)
{
    $term = cdxc_has_codex($el);

    echo '<div class="cc-add-update-project-bloc">';
    if ($term) {
        /**
         * Called before the project exists ajax message.
         *
         * @since 1.0.0
         */
       do_action('cdxc_before_project_exists_msg');
        ?>

        <h4><?php _e('It looks like this project is already added, select an option below.', CDXC_TEXTDOMAIN); ?></h4>
        <span onclick="cdxc_sync_project_files('<?php echo $type; ?>','<?php echo $el; ?>');"
              class="cc-add-project-btn button button-primary"><?php _e('Sync all files now', CDXC_TEXTDOMAIN); ?></span>


        <?php

        $crons = cdxc_get_cron_projects();
        if(isset($crons[$el])){
            $cron_set = '1';
            $cron_class = 'fa-check-square-o';
        }else{
            $cron_set = '0';
            $cron_class = 'fa-square-o';
        }
        ?>
        <span onclick="cdxc_toggle_project_cron('<?php echo $type; ?>','<?php echo $el; ?>');" data-cron-set="<?php echo $cron_set;?>" data-cc-cron-name="<?php echo $el;?>"
              class="cc-add-project-btn button button-primary "><i class="fa <?php echo $cron_class;?>" ></i> <?php _e('Sync once per day via cron', CDXC_TEXTDOMAIN); ?></span>

        <?php
        // if github then allow to set via a webhook
        if($type=='github'){
        ?>
        <span onclick="cdxc_show_git_sync_info('<?php echo $type; ?>','<?php echo $el; ?>');"
              class="cc-add-project-btn button button-primary"><?php _e('Sync via GitHub webhook', CDXC_TEXTDOMAIN); ?></span>
        <?php }elseif($type=='bitbucket'){
        ?>
        <span onclick="cdxc_show_git_sync_info('<?php echo $type; ?>','<?php echo $el; ?>');"
              class="cc-add-project-btn button button-primary"><?php _e('Sync via Bitbucket webhook', CDXC_TEXTDOMAIN); ?></span>
    <?php }?>
        
        <div id="cdxc-ajax-info"></div>

    <?php

    } else {

        /**
         * Called before the add project ajax message.
         *
         * @since 1.0.0
         */
        do_action('cdxc_before_project_add_msg');
        ?>



        <h4><?php _e('It looks like this is a new project, please click below to add it.', CDXC_TEXTDOMAIN); ?></h4>
        <span onclick="cdxc_add_project('<?php echo $type; ?>','<?php echo $el; ?>');"
              class="cc-add-project-btn button button-primary"><?php _e('Add new project', CDXC_TEXTDOMAIN); ?></span>

    <?php
    }
    echo '</div>';
}

/**
 * Checks if a file is in the allowed file types.
 *
 * @since 1.0.0
 * @package Codex_Creator
 * @param string $file The name of the file, eg: functions.php.
 * @return bool True if allowed. False if not allowed.
 */
function cdxc_is_allowed_file($file)
{
    $allowed = cdxc_allowed_file_types();
    $ext = pathinfo($file, PATHINFO_EXTENSION);
    if (in_array($ext, $allowed)) {
        return true;
    }
    return false;
}

/**
 * Generated the post content from the post metadata and updates the post content with it.
 *
 * @since 1.0.0
 * @package Codex_Creator
 * @param int $post_id The post id to run.
 */
function cdxc_codex_create_content($post_id)
{
    global $wpdb;
    $docblocks = cdxc_suported_docblocks();
    $content = '';

    foreach ($docblocks as $key => $title) {//echo "$key \n";

        $content .= call_user_func('cdxc_' . $key . '_content', $post_id, $title);

    }

    //echo  $content;

    $my_post = array(
        'ID' => $post_id,
        'post_content' => $content
    );

    $return = wp_update_post($my_post);
    if (is_wp_error($return)) {
        echo $return->get_error_message();
    }

}

//cdxc_codex_create_content(1316);

/**
 * Checks if a post exists for the given project.
 *
 * @since 1.0.0
 * @package Codex_Creator
 * @param string $title The title of the post to check.
 * @param string $cat The name of the project to check.
 * @param string $type The type of item to check.
 * @return bool|int Returns the post id if exists. False if project does not exist. False if project exists but post does not exist.
 */
function cdxc_post_exits($title, $cat,$type)
{
    global $wpdb;

    $project_slug = $wpdb->get_var($wpdb->prepare("SELECT slug FROM $wpdb->terms WHERE name=%s", $cat));
    if (!$project_slug) {
        return false;
    }

    $type_term_id = $wpdb->get_var($wpdb->prepare("SELECT term_id FROM $wpdb->terms WHERE slug=%s", $project_slug.'_'.$type));
    if (!$type_term_id) {
        return false;
    }



    $post_id = $wpdb->get_var($wpdb->prepare("SELECT p.ID FROM $wpdb->posts p JOIN $wpdb->term_relationships tr on p.ID=tr.object_id Join $wpdb->term_taxonomy tt ON tt.term_taxonomy_id=tr.term_taxonomy_id WHERE p.post_type='codex_creator' AND tt.term_id=%d AND p.post_title=%s", $type_term_id, $title));

    if ($post_id) {
        return $post_id;
    } else {
        return false;
    }

}


/*
 * we do it this way because the src gets strip slashes which can alter the src like "\n" changes to "n"
 */
add_filter( 'the_content', 'cdxc_add_code_src',5 );
/**
 * Add a icon to the beginning of every post page.
 *
 * @uses is_single()
 */
function cdxc_add_code_src( $content ) {

    if ( is_single() ) {
        global $post;
        if($post->post_type=='codex_creator'){
            $src_code = get_post_meta($post->ID, 'cdxc_meta_code', true);

            $before_code = CDXC_PHP_CODE_START;
            $after_code = CDXC_PHP_CODE_END;
            if(class_exists('SyntaxHighlighter')){
                $start_line = get_post_meta($post->ID, 'cdxc_meta_line', true);
                $before_code = "[php firstline=$start_line]";
                $after_code = "[/php]";
            }
            $src_code = $before_code.$src_code.$after_code;
            $content = str_replace("%%CDXC_SRC_CODE%%",$src_code,$content);
        }

    }else{
        global $post;
        if($post->post_type=='codex_creator') {
            $content = str_replace("%%CDXC_SRC_CODE%%",'',$content);
        }

    }

    // Returns the content.
    return $content;
}

/**
 * Find and format the name of the hook from the parsed object.
 *
 * @param object $hook The parsed hook object.
 * @return string The name of the hook.
 */
function cdxc_get_hook_name($hook){
    $name = '';



    if(isset($hook->args[0]->value)){
        $root = $hook->args[0]->value;
    }elseif(isset($hook->exprs[0]->args[0]->value)){
        $root = $hook->exprs[0]->args[0]->value;
    }elseif(isset($hook->expr->args[0]->value)){
        $root = $hook->expr->args[0]->value;
    }


    if($root->getType()=='Scalar_String' && isset($root->value)){
        $name = $root->value;
    }else{

        $prettyPrinter = new PhpParser\PrettyPrinter\Standard;

        try {

            $tmp_code = array();
            $tmp_code[0]= $root;
            // pretty print
            $code = $prettyPrinter->prettyPrint($tmp_code);

        } catch (PhpParser\Error $e) {
            echo 'Parse Error: ', $e->getMessage();
        }

        $name = str_replace(array(';',' ',' ',"'",'"'),'',$code);

    }


    return $name;

}

function cdxc_build_project_hooks_tree($c_type,$c_name,$file_loc,$func,$hook_type,$hook_name,$hook_line){


    $file_path = str_replace(WP_PLUGIN_DIR . '/', "", $file_loc);
    $project_name = "cdxc_".$c_type."_".$c_name;


    if($p_tree = get_option($project_name)){

    }else{
        $p_tree = array();
    }

    $func_name = '';
    if(isset($func[0][1])){
        $func_name = $func[0][1];
    }


    if($hook_type=='action'){
        $p_tree['actions'][] = array(
            'hook_name' => $hook_name,
            'function_name' => $func_name,
            'hook_line' => $hook_line,
            'file_path' =>  $file_path
        );
    }
    elseif($hook_type=='filter'){
        $p_tree['filters'][] = array(
            'hook_name' => $hook_name,
            'function_name' => $func_name,
            'hook_line' => $hook_line,
            'file_path' =>  $file_path
        );
    }


    update_option($project_name,$p_tree);
}

function cdxc_get_hook_used_by($c_type,$c_name,$hook_type,$hook_name){
    $used = array();
    if($p_tree = get_option("cdxc_".$c_type."_".$c_name)){
        $used = array();
        if(!empty($p_tree)){

            if($hook_type=='action'){
                $hooks = $p_tree['actions'];
            }else{
                $hooks = $p_tree['filters'];
            }

            foreach($hooks as $hook){
                if($hook['hook_name']==$hook_name){
                    $used[] = $hook;
                }

            }

            return $used;

        }

    }
    return $used;
}


function cdxc_get_githubs(){
    return get_option('cdxc_github_repos');
}

function cdxc_set_github($git){

    if(empty($git)){return false;}
    $githubs = get_option('cdxc_github_repos');

    $githubs[$git->name] = array(
        'Name'  => $git->name,
        'url'   => $git->html_url
        );

    update_option('cdxc_github_repos',$githubs);

    return true;
}

function cdxc_parse_github_url($url){
    if(empty($url)){return false;}

    if(filter_var($url, FILTER_VALIDATE_URL) && substr( $url, 0, 18 ) === "https://github.com")
    {
        $parts = parse_url ($url);
        if(isset($parts['path'])){
            return $parts['path'];
        }
    }

    return false;

}

function cdxc_get_github_info($path){

    $response = wp_remote_get( 'https://api.github.com/repos'.$path );
    if( is_array($response) && isset($response['response']['code']) && $response['response']['code']=='200') {
        return json_decode($response['body']); // use the content
    }

    return false;
    
}

function cdxc_generate_key($length = 20) {
    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $charactersLength = strlen($characters);
    $randomString = '';
    for ($i = 0; $i < $length; $i++) {
        $randomString .= $characters[rand(0, $charactersLength - 1)];
    }
    return $randomString;
}