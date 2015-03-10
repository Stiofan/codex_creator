<?php
/**
 * Small functions mostly used to run checks
 *
 * @since 1.0.0
 * @package Codex Creator
 */


function codex_creator_has_codex($el)
{
    $term = term_exists($el, 'codex_project');
    if ($term !== 0 && $term !== null) {
        return true;
    } else {
        return false;
    }
}

function codex_creator_status_text($type, $el)
{
    $term = codex_creator_has_codex($el);

    echo '<div class="cc-add-update-project-bloc">';
    if ($term) {
        ?>

        <h4><?php _e('It looks like this project is already added, select an option below.', WP_CODEX_TEXTDOMAIN); ?></h4>
        <span onclick="codex_creator_sync_project_files('<?php echo $type; ?>','<?php echo $el; ?>');"
              class="cc-add-project-btn button button-primary"><?php _e('Sync all files', WP_CODEX_TEXTDOMAIN); ?></span>

        <?php // @todo add tools such as add link to docblock to codex page, create basic docblocks for pages/functions ?>
        <h4><?php _e('Other tools to be added soon.', WP_CODEX_TEXTDOMAIN); ?></h4>

    <?php

    } else { ?>

        <h4><?php _e('It looks like this is a new project, please click below to add it.', WP_CODEX_TEXTDOMAIN); ?></h4>
        <span onclick="codex_creator_add_project('<?php echo $type; ?>','<?php echo $el; ?>');"
              class="cc-add-project-btn button button-primary"><?php _e('Add new project', WP_CODEX_TEXTDOMAIN); ?></span>

    <?php
    }
    echo '</div>';
}


function codex_creator_is_allowed_file($file)
{
    $allowed = codex_creator_allowed_file_types();
    $ext = pathinfo($file, PATHINFO_EXTENSION);
    if (in_array($ext, $allowed)) {
        return true;
    }
    return false;
}


function codex_creator_codex_create_content($post_id)
{
    global $wpdb;
    $docblocks = codex_creator_suported_docblocks();
    $content = '';

    foreach ($docblocks as $key => $title) {//echo "$key \n";

        $content .= call_user_func('codex_creator_' . $key . '_content', $post_id, $title);

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

//codex_creator_codex_create_content(1316);

function codex_creator_post_exits($title, $cat)
{
    global $wpdb;

    $term_id = $wpdb->get_var($wpdb->prepare("SELECT term_id FROM $wpdb->terms WHERE name=%s", $cat));

    if (!$term_id) {
        return false;
    }

    $post_id = $wpdb->get_var($wpdb->prepare("SELECT p.ID FROM $wpdb->posts p JOIN $wpdb->term_relationships tr on p.ID=tr.object_id Join $wpdb->term_taxonomy tt ON tt.term_taxonomy_id=tr.term_taxonomy_id WHERE p.post_type='codex_creator' AND tt.term_id=%d AND p.post_title=%s", $term_id, $title));

    if ($post_id) {
        return $post_id;
    } else {
        return false;
    }

}