<?php

/**
 * Adds a box to the main column on the Post and Page edit screens.
 */
function codex_creator_add_meta_box() {


	add_meta_box(
		'codex_creator_meta_box',
		__( 'Codex Creator', WP_CODEX_TEXTDOMAIN ),
		'codex_creator_meta_box_callback',
		'codex_creator'
	);


}
add_action( 'add_meta_boxes', 'codex_creator_add_meta_box' );

/**
 * Prints the box content.
 *
 * @param WP_Post $post The object for the current post/page.
 */
function codex_creator_meta_box_callback( $post ) {

	// Add an nonce field so we can check for it later.
	wp_nonce_field( 'codex_creator_meta_box', 'codex_creator_meta_box_nonce' );

	/*
	 * Use get_post_meta() to retrieve an existing value
	 * from the database and use the value for the form.
	 */

	$dock_blocks = array(
		'summary'      => __( 'Summary', WP_CODEX_TEXTDOMAIN ),
		'description'  => __( 'Description', WP_CODEX_TEXTDOMAIN ),
		'access'       => __( 'Access', WP_CODEX_TEXTDOMAIN ),
		'deprecated'   => __( 'Deprecated', WP_CODEX_TEXTDOMAIN ),
		'global'       => __( 'Global Values', WP_CODEX_TEXTDOMAIN ),
		'internal'     => __( 'Internal', WP_CODEX_TEXTDOMAIN ),
		'ignore'       => __( 'Ignore', WP_CODEX_TEXTDOMAIN ),
		'link'         => __( 'Link', WP_CODEX_TEXTDOMAIN ),
		'method'       => __( 'Method', WP_CODEX_TEXTDOMAIN ),
		'package'      => __( 'Package', WP_CODEX_TEXTDOMAIN ),
		'param'        => __( 'Params', WP_CODEX_TEXTDOMAIN ),
		'return'       => __( 'Returns', WP_CODEX_TEXTDOMAIN ),
		'see'          => __( 'See', WP_CODEX_TEXTDOMAIN ),
		'since'        => __( 'Since', WP_CODEX_TEXTDOMAIN ),
		'subpackage'   => __( 'Subpackage', WP_CODEX_TEXTDOMAIN ),
		'todo'         => __( 'Todo', WP_CODEX_TEXTDOMAIN ),
		'type'         => __( 'Type', WP_CODEX_TEXTDOMAIN ),
		'uses'         => __( 'Uses', WP_CODEX_TEXTDOMAIN ),
		'var'          => __( 'Var', WP_CODEX_TEXTDOMAIN ),

	);

	foreach ( $dock_blocks as $key => $title ) {

		$textarea = array( 'summary', 'description' );

		if ( in_array( $key, $textarea ) ) {//textarea
			$value = get_post_meta( $post->ID, 'codex_creator_' . $key, true );
			echo '<label for="codex_creator_' . $key . '">' . $title . '</label>';
			echo '<textarea id="codex_creator_' . $key . '" name="codex_creator_' . $key . '">' . $value . '</textarea>';

		} else {//input
			$value = get_post_meta( $post->ID, 'codex_creator_' . $key, true );
			echo '<label for="codex_creator_' . $key . '">' . $title . '</label>';
			echo '<input type="text" id="codex_creator_' . $key . '" name="codex_creator_' . $key . '" value="' . $value . '" />';

		}


	}

}


/**
 * When the post is saved, saves our custom data.
 *
 * @param int $post_id The ID of the post being saved.
 */
function codex_creator_save_meta_box_data( $post_id ) {

	/*
	 * We need to verify this came from our screen and with proper authorization,
	 * because the save_post action can be triggered at other times.
	 */

	// Check if our nonce is set.
	if ( ! isset( $_POST['codex_creator_meta_box_nonce'] ) ) {
		return;
	}

	// Verify that the nonce is valid.
	if ( ! wp_verify_nonce( $_POST['codex_creator_meta_box_nonce'], 'codex_creator_meta_box' ) ) {
		return;
	}

	// If this is an autosave, our form has not been submitted, so we don't want to do anything.
	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
		return;
	}

	// Check the user's permissions.
	if ( isset( $_POST['post_type'] ) && 'page' == $_POST['post_type'] ) {

		if ( ! current_user_can( 'edit_page', $post_id ) ) {
			return;
		}

	} else {

		if ( ! current_user_can( 'edit_post', $post_id ) ) {
			return;
		}
	}

	/* OK, it's safe for us to save the data now. */



	$dock_blocks = array(
		'summary'      => __( 'Summary', WP_CODEX_TEXTDOMAIN ),
		'description'  => __( 'Description', WP_CODEX_TEXTDOMAIN ),
		'access'       => __( 'Access', WP_CODEX_TEXTDOMAIN ),
		'deprecated'   => __( 'Deprecated', WP_CODEX_TEXTDOMAIN ),
		'global'       => __( 'Global Values', WP_CODEX_TEXTDOMAIN ),
		'internal'     => __( 'Internal', WP_CODEX_TEXTDOMAIN ),
		'ignore'       => __( 'Ignore', WP_CODEX_TEXTDOMAIN ),
		'link'         => __( 'Link', WP_CODEX_TEXTDOMAIN ),
		'method'       => __( 'Method', WP_CODEX_TEXTDOMAIN ),
		'package'      => __( 'Package', WP_CODEX_TEXTDOMAIN ),
		'param'        => __( 'Params', WP_CODEX_TEXTDOMAIN ),
		'return'       => __( 'Returns', WP_CODEX_TEXTDOMAIN ),
		'see'          => __( 'See', WP_CODEX_TEXTDOMAIN ),
		'since'        => __( 'Since', WP_CODEX_TEXTDOMAIN ),
		'subpackage'   => __( 'Subpackage', WP_CODEX_TEXTDOMAIN ),
		'todo'         => __( 'Todo', WP_CODEX_TEXTDOMAIN ),
		'type'         => __( 'Type', WP_CODEX_TEXTDOMAIN ),
		'uses'         => __( 'Uses', WP_CODEX_TEXTDOMAIN ),
		'var'          => __( 'Var', WP_CODEX_TEXTDOMAIN ),

	);

	foreach($dock_blocks as $key=>$title){
		if(isset( $_POST['codex_creator_'.$key] )) {
			$my_data = sanitize_text_field( $_POST['codex_creator_'.$key] );
			update_post_meta( $post_id, 'codex_creator_'.$key, $my_data );
		}
	}





}
add_action( 'save_post', 'codex_creator_save_meta_box_data' );