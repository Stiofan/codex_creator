<?php
/**
 * Setup codex_creator CPT and it's taxonomies
 *
 * @since 1.0.0
 * @package Codex_Creator
 */

/**
 * Create our CPT and taxonomies.
 *
 * This function creates the custom post type, the project categories and also the version numbers as tags.
 *
 * @since 1.0.0
 * @package Codex_Creator
 */
function cdxc_create_posttype()
{

    $labels = array(
        'name' => __('Codex', CDXC_TEXTDOMAIN),
        'singular_name' => __('Codex', CDXC_TEXTDOMAIN),
        'add_new' => __('Add New', CDXC_TEXTDOMAIN),
        'add_new_item' => __('Add New Page', CDXC_TEXTDOMAIN),
        'edit_item' => __('Edit Page', CDXC_TEXTDOMAIN),
        'new_item' => __('New Page', CDXC_TEXTDOMAIN),
        'view_item' => __('View Page', CDXC_TEXTDOMAIN),
        'search_items' => __('Search Pages', CDXC_TEXTDOMAIN),
        'not_found' => __('No Page Found', CDXC_TEXTDOMAIN),
        'not_found_in_trash' => __('No Page Found In Trash', CDXC_TEXTDOMAIN));

    $codex_defaults = array(
        'labels' => $labels,
        'can_export' => true,
        'capability_type' => 'post',
        'description' => 'Codex post type',
        'has_archive' => true,
        'hierarchical' => false,
        'map_meta_cap' => true,
        //'menu_icon' => $menu_icon,
        'public' => true,
        'query_var' => true,
        'rewrite' => array('slug' => 'codex/%codex_project%', 'with_front' => false, 'hierarchical' => true),
       // 'rewrite' => array('slug' => 'codex/%codex_project%', 'with_front' => false, 'hierarchical' => true),
        //'supports' => array( 'title', 'editor', 'author', 'thumbnail', 'excerpt', 'custom-fields', 'comments', 'revisions', /*'post-formats'*/ ),
        'supports' => array( 'title', 'comments', 'revisions', /*'post-formats'*/ ),
        'taxonomies' => array('codex_project', 'codex_tags'));

    register_post_type('codex_creator', $codex_defaults);

    // Add new taxonomy, make it hierarchical (like categories)
    $labels = array(
        'name' => _x('Codex Project', 'taxonomy general name'),
        'singular_name' => _x('Codex Project', 'taxonomy singular name'),
        'search_items' => __('Search Projects'),
        'all_items' => __('All Projects'),
        'parent_item' => __('Parent Folder'),
        'parent_item_colon' => __('Parent Folder:'),
        'edit_item' => __('Edit Project'),
        'update_item' => __('Update Project'),
        'add_new_item' => __('Add New Project'),
        'new_item_name' => __('New Project Name'),
        'menu_name' => __('Codex Project'),
    );

    $args = array(
        'hierarchical' => true,
        'labels' => $labels,
        'show_ui' => true,
        'show_admin_column' => true,
        'query_var' => true,
        'rewrite' => array('slug' => 'codex_project'),
    );

    register_taxonomy('codex_project', 'codex_creator', $args);

    // Add new taxonomy, NOT hierarchical (like tags)
    $labels = array(
        'name' => _x('Version', 'taxonomy general name'),
        'singular_name' => _x('Versions', 'taxonomy singular name'),
        'search_items' => __('Search Versions'),
        'popular_items' => __('Popular Versions'),
        'all_items' => __('All Versions'),
        'parent_item' => null,
        'parent_item_colon' => null,
        'edit_item' => __('Edit Version'),
        'update_item' => __('Update Version'),
        'add_new_item' => __('Add New Version'),
        'new_item_name' => __('New Version Name'),
        'separate_items_with_commas' => __('Separate versions with commas'),
        'add_or_remove_items' => __('Add or remove versions'),
        'choose_from_most_used' => __('Choose from the most used versions'),
        'not_found' => __('No versions found.'),
        'menu_name' => __('Versions'),
    );

    $args = array(
        'hierarchical' => false,
        'labels' => $labels,
        'show_ui' => true,
        'show_admin_column' => true,
        'update_count_callback' => '_update_post_term_count',
        'query_var' => true,
        'rewrite' => array('slug' => 'codex_version'),
    );

    register_taxonomy('codex_tags', 'codex_creator', $args);
}

// Call the function to creat the CPT
add_action('init', 'cdxc_create_posttype');


// insert the project folder in the permalink
add_filter('post_link', 'cdxc_permalink', 1, 3);
add_filter('post_type_link', 'cdxc_permalink', 1, 3);

/**
 * Rewrite the codex permalink to include the sub category name instead of the main category name
 *
 * @since 1.0.0
 * @package Codex_Creator
 * @internal This is never called direct
 *
 * @param $permalink
 * @param $post_id
 * @return mixed
 */

function cdxc_permalink($permalink, $post_id)
{


    if (strpos($permalink, '%codex_project%') === FALSE) return $permalink;
    // Get post
    $post = get_post($post_id);
    if (!$post) return $permalink;

    // Get taxonomy terms
    $terms = wp_get_object_terms($post->ID, 'codex_project');
    //print_r($terms);
    $taxonomy_slug = '';
    //$type_slug = '';
    if (!is_wp_error($terms) && !empty($terms) && is_object($terms[0])) {

        foreach ($terms as $term) {
            if ($term->parent != '0') {
                $taxonomy_slug = $term->slug;
            }
            //if($term->parent!='0'){$type_slug = $term->slug;}
        }
    }

    if (!$taxonomy_slug) {
        $taxonomy_slug = 'no-project';
    }
    //  if(!$type_slug){$type_slug = 'no-type';}

    //$taxonomy_slug = 'cat1/cat2';

    $permalink = str_replace('%codex_project%', $taxonomy_slug, $permalink);
    //$permalink =  str_replace('%codex_type%', $type_slug, $permalink);

    return $permalink;
}


add_filter( 'post_type_archive_link', 'cdxc_permalink_post_type',2,2 );

function cdxc_permalink_post_type($link, $post_type){

    if($post_type=='codex_creator'){
        $link = str_replace('%codex_project%/', '', $link);
        $link = str_replace('%codex_project%', '', $link);
    }

    return $link;
}

add_filter('generate_rewrite_rules', 'cdxc_rewrite_post_type_base');
function cdxc_rewrite_post_type_base($wp_rewrite) {
    $newrules = array();
    $newrules['codex/?$'] = 'index.php?post_type=codex_creator';
    $wp_rewrite->rules = $newrules + $wp_rewrite->rules;
    return $wp_rewrite;
}


