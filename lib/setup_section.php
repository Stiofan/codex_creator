<?php
/**
 * Setup new section in WordPress tools
 *
 * @since 1.0.0
 * @package Codex Creator
 */


/**
 * Add menu item to the WordPress tools section.
 *
 * @since 1.0.0
 * @package Codex Creator
 */
function register_my_custom_submenu_page() {
	add_submenu_page( 'tools.php', 'Codex Creator', 'Codex Creator', 'manage_options', 'codex-creator', 'codex_creator_main_page' );
}

/**
 * Content of the main page for Codex Creator
 *
 * @since 1.0.0
 * @package Codex Creator
 */
function codex_creator_main_page() {
?>
	<div class="wrap">
		<div id="icon-tools" class="icon32"></div>
		<h2><?php _e( 'Codex Creator', WP_CODEX_TEXTDOMAIN ); ?></h2>

		<div class="codex-creator-wrap">

			<input type="hidden" id="codex_creator_type_val" value="" />
			<input type="hidden" id="codex_creator_project_name_val" value="" />
			<input type="hidden" id="codex_creator_project_root_val" value="" />


			<div class="codex-creator-step-1 cc-step cc-active">
				<div class="codex-creator-step-title">
					<h3><?php _e( 'Step 1 - Plugin or Theme', WP_CODEX_TEXTDOMAIN ); ?></h3>
				</div>
				<div class="codex-creator-step-content ">
					<span class="step-1-plugin" onclick="codex_creator_step_1('plugin');"><?php _e( 'Plugin', WP_CODEX_TEXTDOMAIN ); ?></span>
					<span class="step-1-theme" onclick="codex_creator_step_1('theme');"><?php _e( 'Theme', WP_CODEX_TEXTDOMAIN ); ?></span>
				</div>
			</div>

			<div class="codex-creator-step-2 cc-step">
				<div class="codex-creator-step-title">
					<h3><?php _e( 'Step 2 - Select Plugin or Theme', WP_CODEX_TEXTDOMAIN ); ?></h3>
				</div>
				<div class="codex-creator-step-content">
					<i class="fa fa-cog fa-spin"></i>
				</div>
			</div>

			<div class="codex-creator-step-3 cc-step">
				<div class="codex-creator-step-title">
					<h3><?php _e( 'Step 3 - Scan Files and Folders', WP_CODEX_TEXTDOMAIN ); ?></h3>
				</div>
				<div class="codex-creator-step-content">
					<div class="codex-creator-step-content">
						<i class="fa fa-cog fa-spin"></i>
					</div>
				</div>
			</div>
		</div>

	</div>
<?php
}

add_action( 'admin_menu', 'register_my_custom_submenu_page' );



function codex_creator_admin_scripts() {
	wp_enqueue_style( 'codex_creator_admin_css', WP_CODEX_URL . 'css/codex_creator.css', false, WP_CODED_VERSION );

	wp_enqueue_style( 'codex_creator_font-awesome', '//netdna.bootstrapcdn.com/font-awesome/4.3.0/css/font-awesome.min.css', array(), '4.3.0' );

	wp_enqueue_script( 'codex_creator_script', WP_CODEX_URL . 'js/codex_creator.js', false, WP_CODED_VERSION );


}
add_action( 'admin_enqueue_scripts', 'codex_creator_admin_scripts' );