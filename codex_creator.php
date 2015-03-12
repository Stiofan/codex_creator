<?php
/**
 * This is the main Codex Creator plugin file
 *
 * @since 1.0.0
 * @package Codex Creator
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
 * For testing only, should be removed for release.
 * @todo remove this for release.
 */
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(-1);

if (!defined('ABSPATH')) exit;

/**
 * Define constants
 */
define('CDXC_VERSION', '1.0.0');
define('CDXC_ROOT', __FILE__);
define('CDXC_ROOT_DOC', __DIR__);
define('CDXC_TEXTDOMAIN', 'codex_creator');
define('CDXC_URL', plugin_dir_url(CDXC_ROOT));

define('CDXC_TITLE_START', "<h4>");
define('CDXC_TITLE_END', "</h4>");
define('CDXC_CONTENT_START', "<p>");
define('CDXC_CONTENT_END', "</p>");

define('CDXC_SAMPLE_OPEN', "<pre>");
define('CDXC_SAMPLE_CLOSE', "</pre>");



/**
 * DocBlock Class for reading DocBlocks
 */
include_once('phpDocumentor/Reflection/DocBlock.php');

/*
 * include only what we need from phpdocumentor.
 */
$cdxc_docblock_include_arr = array(
    "/phpDocumentor/Reflection/DocBlock/Description.php",
    "/phpDocumentor/Reflection/DocBlock/Tag.php",
    "/phpDocumentor/Reflection/DocBlock/Tag/ReturnTag.php",
    "/phpDocumentor/Reflection/DocBlock/Tag/VersionTag.php",
    "/phpDocumentor/Reflection/DocBlock/Tag/SinceTag.php",
    "/phpDocumentor/Reflection/DocBlock/Tag/ParamTag.php",
    "/phpDocumentor/Reflection/DocBlock/Tag/AuthorTag.php",
    "/phpDocumentor/Reflection/DocBlock/Tag/LinkTag.php",
    "/phpDocumentor/Reflection/DocBlock/Tag/VarTag.php",
    "/phpDocumentor/Reflection/DocBlock/Tag/ThrowsTag.php",
    "/phpDocumentor/Reflection/DocBlock/Tag/SeeTag.php",
    "/phpDocumentor/Reflection/DocBlock/Tag/UsesTag.php",
    "/phpDocumentor/Reflection/DocBlock/Type/Collection.php",
    "/phpDocumentor/Reflection/DocBlock/Context.php",
);


foreach ($cdxc_docblock_include_arr as $cdx_inc) {
    require_once(CDXC_ROOT_DOC . $cdx_inc);
}



/**
 * Include CPT files
 */
include_once('lib/setup_cpt.php');

/**
 * Setup section in WordPress tools
 */
include_once('lib/setup_section.php');


/**
 * Codex Creator content output functions
 */
include_once('lib/content_output_functions.php');


/**
 * Codex Creator general functions
 */
include_once('lib/general_functions.php');

/**
 * Codex Creator general functions
 */
include_once('lib/helper_functions.php');

/**
 * Codex Creator file functions
 */
include_once('lib/file_functions.php');


/**
 * Codex Creator file functions
 */
include_once('lib/add_meta_boxes.php');