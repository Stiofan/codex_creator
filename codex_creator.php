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


/**
 * zzzzxxx change Stores the GeoDirectory widget locations in the theme widget areas.
 *
 * zzzzxxx change This function loops through the GeoDirectory widgets and saves their locations in the widget areas to an option
 * so they can be restored later. This is called via hook.
 *
 *    add_action('switch_theme', 'geodir_store_sidebars');
 *
 * @since 1.0.0
 * @package GeoDirectory
 */
function testing_yo()
{
    $nothing = '';
    /**
     * Nothing filter.
     *
     * @since 1.0.0
     * @param string $nothing Nothing.
     */
    $nothing = apply_filters('nothing_filter',$nothing);
    return $nothing;

}


/**
 * Get nothing.
 *
 * This function returns a empty string that can be filtered.
 *
 * @uses 'nothing_filter' Filters the nothing variable before return.
 * @uses 'nothing_action' Action called just before the return.
 * @since 1.0.0
 * @since 1.0.1 Added the 'nothing_action' action.
 * @return string The filtered nothing string.
 */
function ex_get_nothing(){
    $nothing = '';
    /**
     * Nothing filter.
     *
     * @since 1.0.0
     * @param string $nothing Empty string.
     */
    $nothing = apply_filters('nothing_filter',$nothing);

    /**
     * Action before the return value of the {@see ex_get_nothing()} function.
     *
     * @since 1.0.1
     * @param string $nothing Empty string.
     */
    do_action('nothing_action',$nothing);
    return $nothing;
}

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

function cdxc_autoload($className)
{

    if (strpos($className,'phpDocumentor') === false && strpos($className,'PhpParser') === false ) {
        return;
    }


    $fileName  = '';
    $namespace = '';
    if ($lastNsPos = strripos($className, '\\')) {
        $namespace = substr($className, 0, $lastNsPos);
        $className = substr($className, $lastNsPos + 1);
        $fileName  = str_replace('\\', DIRECTORY_SEPARATOR, $namespace) . DIRECTORY_SEPARATOR;
    }

    if(substr($className, -1)=='_'){
    //if (strpos($className,'_.php') === false){
        $fileName = $fileName .$className.  '.php';
    }else {
        $fileName .= str_replace('_', DIRECTORY_SEPARATOR, $className) . '.php';
    }


    require $fileName;
}

spl_autoload_register('cdxc_autoload');

//include_once('phpDocumentor/Reflection/DocBlock.php');



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
    "/phpDocumentor/Reflection/DocBlock/Tag/DeprecatedTag.php",
    "/phpDocumentor/Reflection/DocBlock/Tag/MethodTag.php",
    "/phpDocumentor/Reflection/DocBlock/Tag/PropertyTag.php",
    "/phpDocumentor/Reflection/DocBlock/Tag/SourceTag.php",
    "/phpDocumentor/Reflection/DocBlock/Tag/ExampleTag.php",

);

/*
foreach ($cdxc_docblock_include_arr as $cdx_inc) {
    require_once(CDXC_ROOT_DOC . $cdx_inc);
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





/*
//$code = '<?php echo "hi"; // some code';
$code =  cdxc_get_file('/home/stiofan/public_html/wp-content/plugins/codex_creator/codex_creator.php');

$parser = new PhpParser\Parser(new PhpParser\Lexer\Emulative);

try {
    $stmts = $parser->parse($code);
    // $stmts is an array of statement nodes
} catch (PhpParser\Error $e) {
    echo 'Parse Error: ', $e->getMessage();
}

print_r($stmts);