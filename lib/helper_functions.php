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
        ?>

        <h4><?php _e('It looks like this project is already added, select an option below.', CDXC_TEXTDOMAIN); ?></h4>
        <span onclick="cdxc_sync_project_files('<?php echo $type; ?>','<?php echo $el; ?>');"
              class="cc-add-project-btn button button-primary"><?php _e('Sync all files', CDXC_TEXTDOMAIN); ?></span>

        <?php // @todo add tools such as add link to docblock to codex page, create basic docblocks for pages/functions ?>
        <h4><?php _e('Other tools to be added soon.', CDXC_TEXTDOMAIN); ?></h4>

    <?php

    } else { ?>

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

    }

    // Returns the content.
    return $content;
}


function cdxc_get_hook_name($hook){
    $name = '';



    if(isset($hook->args[0]->value)){
        $root = $hook->args[0]->value;
    }elseif(isset($hook->exprs[0]->args[0]->value)){
        $root = $hook->exprs[0]->args[0]->value;
    }elseif(isset($hook->expr->args[0]->value)){
        $root = $hook->expr->args[0]->value;
    }

   // echo '>>>'.$root->getType().'<<<';





    if($root->getType()=='Scalar_String' && isset($root->value)){
        $name = $root->value;
    }else{

        $prettyPrinter = new PhpParser\PrettyPrinter\Standard;

        try {

            $tmp_code = array();
            $tmp_code[0]= $root;
            // pretty print
            $code = $prettyPrinter->prettyPrint($tmp_code);

            //echo '>>>#'.$code.'#<<<';
        } catch (PhpParser\Error $e) {
            echo 'Parse Error: ', $e->getMessage();
        }

        $name = str_replace(array(';',' ',' ',"'",'"'),'',$code);
       // $name = str_replace(array('"',"'"),"'",$name);


    }



    /*elseif($root->getType()=='Scalar_Encapsed'){
        foreach($root->parts as $part){
            if(is_string($part)){
                $name .= $part;
            }elseif($part->getType()=='Expr_Variable'){
                $name .= "{".'$'.$part->name."}";
            }
        }

    }elseif($root->getType()=='Expr_BinaryOp_Concat'){

    }*/


    //echo '<<<'.$name.'>>>'." \r\n";

    return $name;

}