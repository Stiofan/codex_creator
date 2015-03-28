<?php
/**
 * Setup new section in WordPress tools and add our HTML
 *
 * @since 1.0.0
 * @package Codex_Creator
 */


/**
 * Add menu item to the WordPress tools section.
 *
 * @since 1.0.0
 * @package Codex_Creator
 */
function register_my_custom_submenu_page()
{
    add_submenu_page('tools.php', 'Codex Creator', 'Codex Creator', 'manage_options', 'codex-creator', 'cdxc_main_page');
}

/**
 * Content of the main page for Codex Creator
 *
 * @since 1.0.0
 * @package Codex_Creator
 */
function cdxc_main_page()
{
    ?>
    <div class="wrap">
        <div id="icon-tools" class="icon32"></div>
        <h2><?php _e('Codex Creator', CDXC_TEXTDOMAIN); ?></h2>

        <div class="codex-creator-wrap">

            <input type="hidden" id="cdxc_type_val" value=""/>
            <input type="hidden" id="cdxc_project_name_val" value=""/>
            <input type="hidden" id="cdxc_project_root_val" value=""/>


            <div class="codex-creator-step-1 cc-step cc-active">
                <div class="codex-creator-step-title">
                    <h3><?php _e('Step 1 - Plugin or Theme', CDXC_TEXTDOMAIN); ?></h3>
                </div>
                <div class="codex-creator-step-content ">
                    <span class="step-1-plugin button button-primary"
                          onclick="cdxc_step_1('plugin');"><?php _e('Plugin', CDXC_TEXTDOMAIN); ?></span>
                    <span class="step-1-theme button button-primary"
                          onclick="cdxc_step_1('theme');"><?php _e('Theme', CDXC_TEXTDOMAIN); ?></span>
                </div>
            </div>

            <div class="codex-creator-step-2 cc-step">
                <div class="codex-creator-step-title">
                    <h3><?php _e('Step 2 - Select Plugin or Theme', CDXC_TEXTDOMAIN); ?></h3>
                </div>
                <div class="codex-creator-step-content">
                    <i class="fa fa-cog fa-spin"></i>
                </div>
            </div>

            <div class="codex-creator-step-3 cc-step">
                <div class="codex-creator-step-title">
                    <h3><?php _e('Step 3 - Scan Files and Folders', CDXC_TEXTDOMAIN); ?></h3>
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

add_action('admin_menu', 'register_my_custom_submenu_page');

/**
 * Adds the JS and CSS files to the WordPress backend.
 *
 * @since 1.0.0
 * @package Codex_Creator
 */
function cdxc_admin_scripts()
{
    wp_enqueue_style('cdxc_admin_css', CDXC_URL . 'css/codex_creator.css', false, CDXC_VERSION);

    wp_enqueue_style('cdxc_font-awesome', '//netdna.bootstrapcdn.com/font-awesome/4.3.0/css/font-awesome.min.css', array(), '4.3.0');

    wp_enqueue_script('cdxc_script', CDXC_URL . 'js/codex_creator.js', false, CDXC_VERSION);


}

add_action('admin_enqueue_scripts', 'cdxc_admin_scripts');