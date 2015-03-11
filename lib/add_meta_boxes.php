<?php
/**
 * Functions for adding metaboxes to the codex CPT
 *
 * @since 1.0.0
 * @package Codex Creator
 */

/**
 * Adds a box to the main section on the Post and Page edit screens.
 *
 * @since 1.0.0
 * @package Codex Creator
 */
function codex_creator_add_meta_box()
{

    add_meta_box(
        'codex_creator_meta_box_usage',
        __('Codex Creator - Usage', WP_CODEX_TEXTDOMAIN),
        'codex_creator_meta_box_usage_callback',
        'codex_creator'
    );

    add_meta_box(
        'codex_creator_meta_box_example',
        __('Codex Creator - Example', WP_CODEX_TEXTDOMAIN),
        'codex_creator_meta_box_example_callback',
        'codex_creator'
    );

    add_meta_box(
        'codex_creator_meta_box',
        __('Codex Creator', WP_CODEX_TEXTDOMAIN),
        'codex_creator_meta_box_callback',
        'codex_creator'
    );


}

add_action('add_meta_boxes', 'codex_creator_add_meta_box');

/**
 * Outputs the custom meta boxes on the edit post page for Codex Creator CPT
 *
 * @since 1.0.0
 * @package Codex Creator
 * @param int|WP_Post $post The object for the current post/page.
 */
function codex_creator_meta_box_callback($post)
{

    // Add an nonce field so we can check for it later.
    wp_nonce_field('codex_creator_meta_box', 'codex_creator_meta_box_nonce');

    /*
     * Use get_post_meta() to retrieve an existing value
     * from the database and use the value for the form.
     */

    $dock_blocks = codex_creator_suported_docblocks();

    foreach ($dock_blocks as $key => $title) {

        if (in_array($key, array('usage', 'example'))) {
            continue;
        }

        $textarea = array('summary', 'description');

        if (in_array($key, $textarea)) {//textarea
            $value = get_post_meta($post->ID, 'codex_creator_' . $key, true);
            echo '<label for="codex_creator_' . $key . '">' . $title . '</label>';
            echo '<textarea id="codex_creator_' . $key . '" name="codex_creator_' . $key . '">' . $value . '</textarea>';

        } else {//input
            $value = get_post_meta($post->ID, 'codex_creator_' . $key, true);
            echo '<label for="codex_creator_' . $key . '">' . $title . '</label>';
            if (is_array($value)) {
                echo '<input type="text" id="no_save_codex_creator_' . $key . '" name="no_save_codex_creator_' . $key . '" value="ARRAY" />';
            } else {
                echo '<input type="text" id="codex_creator_' . $key . '" name="codex_creator_' . $key . '" value="' . $value . '" />';
            }

        }


    }

}


/**
 * When the post is saved, this saves our custom data.
 *
 * @since 1.0.0
 * @package Codex Creator
 * @param int $post_id The ID of the post being saved.
 */
function codex_creator_save_meta_box_data($post_id)
{

    // unhook this function so it doesn't loop infinitely
    remove_action('save_post', 'codex_creator_save_meta_box_data');


    /*
     * We need to verify this came from our screen and with proper authorization,
     * because the save_post action can be triggered at other times.
     */

    // Check if our nonce is set.
    if (!isset($_POST['codex_creator_meta_box_nonce'])) {
        return;
    }

    // Verify that the nonce is valid.
    if (!wp_verify_nonce($_POST['codex_creator_meta_box_nonce'], 'codex_creator_meta_box')) {
        return;
    }

    // If this is an autosave, our form has not been submitted, so we don't want to do anything.
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
        return;
    }

    // Check the user's permissions.
    if (isset($_POST['post_type']) && 'page' == $_POST['post_type']) {

        if (!current_user_can('edit_page', $post_id)) {
            return;
        }

    } else {

        if (!current_user_can('edit_post', $post_id)) {
            return;
        }
    }

    /* OK, it's safe for us to save the data now. */


    $dock_blocks = codex_creator_suported_docblocks();

    foreach ($dock_blocks as $key => $title) {
        if (isset($_POST['codex_creator_' . $key])) {
            $my_data = sanitize_text_field($_POST['codex_creator_' . $key]);
            update_post_meta($post_id, 'codex_creator_' . $key, $my_data);
        }
    }


    // update the content
    codex_creator_codex_create_content($post_id);


    // re-hook this function
    add_action('save_post', 'codex_creator_save_meta_box_data');


}

add_action('save_post', 'codex_creator_save_meta_box_data');

/**
 * Adds new usage custom input box to edit post page for Codex Creator CPT.
 *
 * @since 1.0.0
 * @package Codex Creator
 * @param int|WP_Post $post The post object.
 */
function codex_creator_meta_box_usage_callback($post)
{


    $content = get_post_meta($post->ID, 'codex_creator_usage', true);
    $editor_id = 'codex_creator_usage';

    wp_editor($content, $editor_id);

}

/**
 * Adds new example custom input box to edit post page for Codex Creator CPT.
 *
 * @since 1.0.0
 * @package Codex Creator
 * @param int|WP_Post $post The post object.
 */
function codex_creator_meta_box_example_callback($post)
{


    $content = get_post_meta($post->ID, 'codex_creator_example', true);
    $editor_id = 'codex_creator_example';

    wp_editor($content, $editor_id);

}