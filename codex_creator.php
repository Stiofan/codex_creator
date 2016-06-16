<?php
/**
 * This is the main Codex Creator plugin file
 *
 * @since 1.0.0
 * @package Codex_Creator
 */

/*
Plugin Name: Codex Creator
Plugin URI: http://www.nomaddevs.com/
Description: This plugin was designed to create codex documentation for WordPress plugins and themes.
Version: 1.0.0
Author: NomadDevs
Author URI: http://www.nomaddevs.com/
*/

/*
ini_set('display_errors',1);
ini_set('display_startup_errors',1);
error_reporting(-1);
*/

/*
 * Don't allow direct access to this file.
 */
if (!defined('ABSPATH')) exit;


/**
 * Defines the plugins constants.
 *
 * This is wrapped in a function and called on the 'plugins_loaded' hook so it can easily be changed.
 *
 * @since 1.0.0
 * @package Codex_Creator
 */
function cdxc_define_constants()
{
    /*
     * Define constants
     */
    if (!defined('CDXC_VERSION')) define('CDXC_VERSION', '1.0.0');
    if (!defined('CDXC_ROOT')) define('CDXC_ROOT', __FILE__);
    if (!defined('CDXC_ROOT_DOC')) define('CDXC_ROOT_DOC', __DIR__);
    if (!defined('CDXC_TEXTDOMAIN')) define('CDXC_TEXTDOMAIN', 'codex_creator');
    if (!defined('CDXC_URL')) define('CDXC_URL', plugin_dir_url(CDXC_ROOT));

    if (!defined('CDXC_TITLE_START')) define('CDXC_TITLE_START', "<h4>");
    if (!defined('CDXC_TITLE_END')) define('CDXC_TITLE_END', "</h4>");
    if (!defined('CDXC_CONTENT_START')) define('CDXC_CONTENT_START', "<p>");
    if (!defined('CDXC_CONTENT_END')) define('CDXC_CONTENT_END', "</p>");

    if (!defined('CDXC_PHP_CODE_START')) define('CDXC_PHP_CODE_START', "<pre>");
    if (!defined('CDXC_PHP_CODE_END')) define('CDXC_PHP_CODE_END', "</pre>");

    if (!defined('CDXC_SAMPLE_OPEN')) define('CDXC_SAMPLE_OPEN', "<pre>");
    if (!defined('CDXC_SAMPLE_CLOSE')) define('CDXC_SAMPLE_CLOSE', "</pre>");
}

add_action('plugins_loaded', 'cdxc_define_constants');


/**
 * Autoloads the phpDocumentor and PhpParser class files.
 *
 * @since 1.0.0
 * @package Codex_Creator
 * @param string $className The class name as a file path.
 */
function cdxc_autoload($className)
{

    if (strpos($className, 'phpDocumentor\\') === false && strpos($className, 'PhpParser\\') === false) {
        return;
    }

    $fileName = '';
    $namespace = '';
    if ($lastNsPos = strripos($className, '\\')) {
        $namespace = substr($className, 0, $lastNsPos);
        $className = substr($className, $lastNsPos + 1);
        $fileName = str_replace('\\', DIRECTORY_SEPARATOR, $namespace) . DIRECTORY_SEPARATOR;
    }

    if (substr($className, -1) == '_') {
        //if (strpos($className,'_.php') === false){
        $fileName = $fileName . $className . '.php';
    } else {
        $fileName .= str_replace('_', DIRECTORY_SEPARATOR, $className) . '.php';
    }


    require $fileName;
}

spl_autoload_register('cdxc_autoload');


/**
 * Include CPT files.
 */
include_once('lib/setup_cpt.php');

/**
 * Setup section in WordPress tools.
 */
include_once('lib/setup_section.php');

/**
 * Codex Creator content output functions.
 */
include_once('lib/content_output_functions.php');

/**
 * Codex Creator general functions.
 */
include_once('lib/general_functions.php');

/**
 * Codex Creator general functions.
 */
include_once('lib/helper_functions.php');

/**
 * Codex Creator file functions.
 */
include_once('lib/file_functions.php');

/**
 * Codex Creator file functions.
 */
include_once('lib/add_meta_boxes.php');

/**
 * Codex Creator cron functions.
 */
include_once('lib/cron_functions.php');

/**
 * Codex Creator Bitbucket functions.
 */
include_once('lib/bitbucket_functions.php');