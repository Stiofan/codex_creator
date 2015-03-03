<?php
/*
Plugin Name: WordPress Codex Creator
Plugin URI: http://www.nomaddevs.com/
Description: This plugin was designed to create codex documentation for WordPress plugins and themes.s
Version: 0.0.1
Author: NomadDevs
Author URI: http://www.nomaddevs.com/
*/


ini_set('display_errors',1);
ini_set('display_startup_errors',1);
error_reporting(-1);

if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Define constants
 */
define( 'WP_CODED_VERSION', '0.0.1' );
define( 'WP_CODEX_ROOT', __FILE__ );
define( 'WP_CODEX_ROOT_DOC', __DIR__ );
define( 'WP_CODEX_TEXTDOMAIN', 'codex_creator' );
define( 'WP_CODEX_URL', plugin_dir_url( WP_CODEX_ROOT ) );


/**
 * DocBlock Class for reading DocBlocks
 */
include_once('phpDocumentor/Reflection/DocBlock.php');



$codex_creator_docblock_include_arr = array(
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



foreach($codex_creator_docblock_include_arr as $cdx_inc){
	require_once(WP_CODEX_ROOT_DOC.$cdx_inc);
}
/*
foreach (glob(WP_CODEX_ROOT_DOC."/phpDocumentor/Reflection/DocBlock/Tag/*.php") as $filename)
{
	require_once $filename;
}
*/




/**
 * Include CPT files
 */
include_once('lib/setup_cpt.php');

/**
 * Setup section in WordPress tools
 */
include_once('lib/setup_section.php');




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