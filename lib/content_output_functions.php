<?php
/**
 * Functions for building the page content for each codex page
 *
 * @since 1.0.0
 * @package Codex Creator
 */

/**
 * Get and format content output for summary section of the codex page.
 *
 * @since 1.0.0
 * @package Codex Creator
 * @param int $post_id Post ID of the post content required.
 * @param string $title Title for the content section.
 * @return string The formatted content.
 */
function codex_creator_summary_content($post_id, $title)
{
    $content = '';
    $meta_value = get_post_meta($post_id, 'codex_creator_summary', true);
    if (!$meta_value) {
        return;
    }
    $content .= WP_CODEX_TITLE_START . $title . WP_CODEX_TITLE_END;
    $content .= WP_CODEX_CONTENT_START . $meta_value . WP_CODEX_CONTENT_END;

    return apply_filters('codex_creator_summary_content', $content, $post_id, $title);
}

/**
 * Get and format content output for description section of the codex page.
 *
 * @since 1.0.0
 * @package Codex Creator
 * @param int $post_id Post ID of the post content required.
 * @param string $title Title for the content section.
 * @return string The formatted content.
 */
function codex_creator_description_content($post_id, $title)
{
    $content = '';
    //$meta_value = get_post_meta($post_id, 'codex_creator_description', true);
    $meta_value = get_post_meta($post_id, 'codex_creator_meta_docblock', true);
    if (!$meta_value) {
        return '';
    }

    $phpdoc = new \phpDocumentor\Reflection\DocBlock($meta_value);

    $line_arr = explode(PHP_EOL, $phpdoc->getLongDescription()->getContents());
    $line_arr = array_values(array_filter($line_arr));

    if (empty($line_arr)) {
        return '';
    }
    $sample_open = false;
    $sample_close = false;
    foreach ($line_arr as $key=>$line) {

        //check for code sample opening
        if($line=='' && substr($line_arr[$key+1], 0, 3) === '   '){// we have found a opening code sample
            $sample_open = $key+1;
        }

        //check for code sample closing
        if($sample_open && substr($line_arr[$key], 0, 3) === '   '){// we have found a closing code sample
            $sample_close = $key;
        }

    }

    if ($sample_open && $sample_close) {
        $line_arr[$sample_open] = WP_CODEX_SAMPLE_OPEN.$line_arr[$sample_open];
        $line_arr[$sample_open] = $line_arr[$sample_close].WP_CODEX_SAMPLE_CLOSE;
    }

    $meta_value = implode(PHP_EOL, $line_arr);

    $content .= WP_CODEX_TITLE_START . $title . WP_CODEX_TITLE_END;
    $content .= WP_CODEX_CONTENT_START . $meta_value . WP_CODEX_CONTENT_END;

    return apply_filters('codex_creator_description_content', $content, $post_id, $title);
}

/**
 * Get and format content output for usage section of the codex page.
 *
 * @since 1.0.0
 * @package Codex Creator
 * @param int $post_id Post ID of the post content required.
 * @param string $title Title for the content section.
 * @return string The formatted content.
 */
function codex_creator_usage_content($post_id, $title)
{
    $content = '';
    $meta_value = get_post_meta($post_id, 'codex_creator_usage', true);
    if (!$meta_value) {
        return;
    }
    $content .= WP_CODEX_TITLE_START . $title . WP_CODEX_TITLE_END;
    $content .= WP_CODEX_CONTENT_START . $meta_value . WP_CODEX_CONTENT_END;

    return apply_filters('codex_creator_usage_content', $content, $post_id, $title);
}

/**
 * Get and format content output for access section of the codex page.
 *
 * @since 1.0.0
 * @package Codex Creator
 * @param int $post_id Post ID of the post content required.
 * @param string $title Title for the content section.
 * @return string The formatted content.
 */
function codex_creator_access_content($post_id, $title)
{
    $content = '';
    $meta_value = get_post_meta($post_id, 'codex_creator_access', true);
    if (!$meta_value) {
        return;
    }
    $content .= WP_CODEX_TITLE_START . $title . WP_CODEX_TITLE_END;
    $content .= WP_CODEX_CONTENT_START . $meta_value . WP_CODEX_CONTENT_END;

    return apply_filters('codex_creator_access_content', $content, $post_id, $title);
}

/**
 * Get and format content output for deprecated section of the codex page.
 *
 * @since 1.0.0
 * @package Codex Creator
 * @param int $post_id Post ID of the post content required.
 * @param string $title Title for the content section.
 * @return string The formatted content.
 */
function codex_creator_deprecated_content($post_id, $title)
{
    $content = '';
    $meta_value = get_post_meta($post_id, 'codex_creator_deprecated', true);
    if (!$meta_value) {
        return;
    }
    $content .= WP_CODEX_TITLE_START . $title . WP_CODEX_TITLE_END;
    $content .= WP_CODEX_CONTENT_START . $meta_value . WP_CODEX_CONTENT_END;

    return apply_filters('codex_creator_deprecated_content', $content, $post_id, $title);
}

/**
 * Get and format content output for global section of the codex page.
 *
 * @since 1.0.0
 * @package Codex Creator
 * @param int $post_id Post ID of the post content required.
 * @param string $title Title for the content section.
 * @return string The formatted content.
 */
function codex_creator_global_content($post_id, $title)
{
    $content = '';
    $meta_value = get_post_meta($post_id, 'codex_creator_global', true);
    if (!$meta_value) {
        return;
    }
    $content .= WP_CODEX_TITLE_START . $title . WP_CODEX_TITLE_END;
    if (is_array($meta_value)) {
        foreach ($meta_value as $value) {
            $content .= WP_CODEX_CONTENT_START . $value . WP_CODEX_CONTENT_END;
        }
    } else {
        $content .= WP_CODEX_CONTENT_START . $meta_value . WP_CODEX_CONTENT_END;
    }


    return apply_filters('codex_creator_global_content', $content, $post_id, $title);
}

/**
 * Get and format content output for internal section of the codex page.
 *
 * @since 1.0.0
 * @package Codex Creator
 * @param int $post_id Post ID of the post content required.
 * @param string $title Title for the content section.
 * @return string The formatted content.
 */
function codex_creator_internal_content($post_id, $title)
{
    $content = '';
    $meta_value = get_post_meta($post_id, 'codex_creator_internal', true);
    if (!$meta_value) {
        return;
    }
    $content .= WP_CODEX_TITLE_START . $title . WP_CODEX_TITLE_END;
    $content .= WP_CODEX_CONTENT_START . $meta_value . WP_CODEX_CONTENT_END;

    return apply_filters('codex_creator_internal_content', $content, $post_id, $title);
}

/**
 * Get and format content output for ignore section of the codex page.
 *
 * @since 1.0.0
 * @package Codex Creator
 * @param int $post_id Post ID of the post content required.
 * @param string $title Title for the content section.
 * @return string The formatted content.
 */
function codex_creator_ignore_content($post_id, $title)
{
    $content = '';
    $meta_value = get_post_meta($post_id, 'codex_creator_ignore', true);
    if (!$meta_value) {
        return;
    }
    $content .= WP_CODEX_TITLE_START . $title . WP_CODEX_TITLE_END;
    $content .= WP_CODEX_CONTENT_START . $meta_value . WP_CODEX_CONTENT_END;

    return apply_filters('codex_creator_ignore_content', $content, $post_id, $title);
}

/**
 * Get and format content output for link section of the codex page.
 *
 * @since 1.0.0
 * @package Codex Creator
 * @param int $post_id Post ID of the post content required.
 * @param string $title Title for the content section.
 * @return string The formatted content.
 */
function codex_creator_link_content($post_id, $title)
{
    $content = '';
    $meta_value = get_post_meta($post_id, 'codex_creator_link', true);
    if (!$meta_value) {
        return;
    }
    $content .= WP_CODEX_TITLE_START . $title . WP_CODEX_TITLE_END;
    $content .= WP_CODEX_CONTENT_START . $meta_value . WP_CODEX_CONTENT_END;

    return apply_filters('codex_creator_link_content', $content, $post_id, $title);
}

/**
 * Get and format content output for method section of the codex page.
 *
 * @since 1.0.0
 * @package Codex Creator
 * @param int $post_id Post ID of the post content required.
 * @param string $title Title for the content section.
 * @return string The formatted content.
 */
function codex_creator_method_content($post_id, $title)
{
    $content = '';
    $meta_value = get_post_meta($post_id, 'codex_creator_method', true);
    if (!$meta_value) {
        return;
    }
    $content .= WP_CODEX_TITLE_START . $title . WP_CODEX_TITLE_END;
    $content .= WP_CODEX_CONTENT_START . $meta_value . WP_CODEX_CONTENT_END;

    return apply_filters('codex_creator_method_content', $content, $post_id, $title);
}

/**
 * Get and format content output for package section of the codex page.
 *
 * @since 1.0.0
 * @package Codex Creator
 * @param int $post_id Post ID of the post content required.
 * @param string $title Title for the content section.
 * @return string The formatted content.
 */
function codex_creator_package_content($post_id, $title)
{
    $content = '';
    $meta_value = get_post_meta($post_id, 'codex_creator_package', true);
    if (!$meta_value) {
        return;
    }
    $content .= WP_CODEX_TITLE_START . $title . WP_CODEX_TITLE_END;
    $content .= WP_CODEX_CONTENT_START . $meta_value . WP_CODEX_CONTENT_END;

    return apply_filters('codex_creator_package_content', $content, $post_id, $title);
}

/**
 * Get and format content output for param section of the codex page.
 *
 * @since 1.0.0
 * @package Codex Creator
 * @param int $post_id Post ID of the post content required.
 * @param string $title Title for the content section.
 * @return string The formatted content.
 */
function codex_creator_param_content($post_id, $title)
{
    $content = '';
    $meta_value = get_post_meta($post_id, 'codex_creator_param', true);
    if (!$meta_value) {
        return;
    }
    $content .= WP_CODEX_TITLE_START . $title . WP_CODEX_TITLE_END;
    if (is_array($meta_value)) {
        foreach ($meta_value as $value) {
            $value = codex_creator_param_content_helper($value);
            $content .= WP_CODEX_CONTENT_START . $value . WP_CODEX_CONTENT_END;
        }
    } else {
        $meta_value = codex_creator_param_content_helper($meta_value);
        $content .= WP_CODEX_CONTENT_START . $meta_value . WP_CODEX_CONTENT_END;
    }

    return apply_filters('codex_creator_param_content', $content, $post_id, $title);
}

/**
 * Arrange a param value into a usable HTML output.
 *
 * @since 1.0.0
 * @package Codex Creator
 * @param string $param The param value to be used.
 * @return string Formatted HTML on success.
 */
function codex_creator_param_content_helper($param)
{   if($param==''){return '';}
    $output = '';
    $param_arr = explode(' ',$param);
    $param_arr = array_values(array_filter($param_arr));
    //print_r($param_arr);

    $output .= '<dl>';
    //variable
    if(!empty($param_arr[1])){
        $var = $param_arr[1];
        $output .= '<dt><b>'.$var.'</b></dt>';
        unset($param_arr[1]);
    }

    $output .= '<dd>';
    //datatype
    if(!empty($param_arr[0])){
        $datatype = $param_arr[0];
        $link_open = '';
        $link_close = '';
        if($datatype=='string' || $datatype=='String'){
            $link_open = '<a href="http://codex.wordpress.org/How_to_Pass_Tag_Parameters#String" target="_blank">';
            $link_close = '</a>';
        }
        if($datatype=='int'){
            $link_open = '<a href="http://codex.wordpress.org/How_to_Pass_Tag_Parameters#Integer" target="_blank">';
            $link_close = '</a>';
        }
        if($datatype=='bool'){
            $link_open = '<a href="http://codex.wordpress.org/How_to_Pass_Tag_Parameters#Boolean" target="_blank">';
            $link_close = '</a>';
        }

        $output .= '('.$link_open.'<i>'.$datatype.'</i>'.$link_close.') ';
        unset($param_arr[0]);

    }



    //optional
    $optional = '(<i>'.__('required', WP_CODEX_TEXTDOMAIN).'</i>)';
    if(!empty($param_arr[2])){
        $opt = $param_arr[2];
        if($opt=='Optional.' || $opt=='optional.'){
            $optional = '(<i>'.__('optional', WP_CODEX_TEXTDOMAIN).'</i>)';
            unset($param_arr[2]);
        }
    }
    $output .= $optional.' ';

    //we now split into descriptions.
    $param_str = implode(' ',$param_arr);
    $param_arr = explode('.',$param_str);
    $param_arr = array_filter($param_arr);
    //print_r($param_arr);

    //description/default
    $default = '<dl><dd>'.__('Default', WP_CODEX_TEXTDOMAIN).': <i>'.__('None', WP_CODEX_TEXTDOMAIN).'</i></dd></dl>';
    foreach ($param_arr as $bit) {
        $bit = trim($bit);
        //echo '#'.$bit.'#';
        if(substr( $bit, 0, 7) === 'Default'){
            $bits = explode('Default',$bit);
            $default = '<dl><dd>'.__('Default', WP_CODEX_TEXTDOMAIN).': <i>'.$bits[1].'</i></dd></dl>';

        }else{
            $output .= $bit.'. ';
        }
    }

    $output .= $default;

    $output .= '</dd>';
    $output .= '</dl>';
    return $output;
}

/**
 * Get and format content output for example section of the codex page.
 *
 * @since 1.0.0
 * @package Codex Creator
 * @param int $post_id Post ID of the post content required.
 * @param string $title Title for the content section.
 * @return string The formatted content.
 */
function codex_creator_example_content($post_id, $title)
{
    $content = '';
    $meta_value = get_post_meta($post_id, 'codex_creator_example', true);
    if (!$meta_value) {
        return;
    }
    $content .= WP_CODEX_TITLE_START . $title . WP_CODEX_TITLE_END;
    $content .= WP_CODEX_CONTENT_START . $meta_value . WP_CODEX_CONTENT_END;

    return apply_filters('codex_creator_example_content', $content, $post_id, $title);
}

/**
 * Get and format content output for return section of the codex page.
 *
 * @since 1.0.0
 * @package Codex Creator
 * @param int $post_id Post ID of the post content required.
 * @param string $title Title for the content section.
 * @return string The formatted content.
 */
function codex_creator_return_content($post_id, $title)
{
    $content = '';
    $meta_value = get_post_meta($post_id, 'codex_creator_return', true);
    if (!$meta_value) {
        return;
    }
    $content .= WP_CODEX_TITLE_START . $title . WP_CODEX_TITLE_END;
    $meta_value = codex_creator_return_content_helper($meta_value);
    $content .= WP_CODEX_CONTENT_START . $meta_value . WP_CODEX_CONTENT_END;

    return apply_filters('codex_creator_return_content', $content, $post_id, $title);
}

/**
 * Arrange a return value into a usable HTML output.
 *
 * @since 1.0.0
 * @package Codex Creator
 * @param string $value The string value to be used.
 * @return string Formatted HTML on success.
 */
function codex_creator_return_content_helper($value)
{
    if($value==''){return '';}
    $output = '';
    $param_arr = explode(' ',$value);
    $param_arr = array_values(array_filter($param_arr));
    //print_r($param_arr);

    $output .= '<dl>';



    //datatype
    if(!empty($param_arr[0])){
        $datatype = $param_arr[0];
        $link_open = '';
        $link_close = '';
        if($datatype=='string' || $datatype=='String'){
            $link_open = '<a href="http://codex.wordpress.org/How_to_Pass_Tag_Parameters#String" target="_blank">';
            $link_close = '</a>';
        }
        if($datatype=='int'){
            $link_open = '<a href="http://codex.wordpress.org/How_to_Pass_Tag_Parameters#Integer" target="_blank">';
            $link_close = '</a>';
        }
        if($datatype=='bool'){
            $link_open = '<a href="http://codex.wordpress.org/How_to_Pass_Tag_Parameters#Boolean" target="_blank">';
            $link_close = '</a>';
        }

        $output .= '<dt><b>('.$link_open.'<i>'.$datatype.'</i>'.$link_close.')</b></dt>';
        unset($param_arr[0]);

    }


    $output .= '<ul>';

    //we now split into descriptions.
    $param_str = implode(' ',$param_arr);
    $param_arr = explode('.',$param_str);
    $param_arr = array_filter($param_arr);
    //print_r($param_arr);

    //description/default
    foreach ($param_arr as $bit) {
        $bit = trim($bit);

            $output .= '<li>'.$bit.'. </li>';

    }

    $output .= '</ul>';
    $output .= '</dl>';

    return $output;
}

/**
 * Get and format content output for see section of the codex page.
 *
 * @since 1.0.0
 * @package Codex Creator
 * @param int $post_id Post ID of the post content required.
 * @param string $title Title for the content section.
 * @return string The formatted content.
 */
function codex_creator_see_content($post_id, $title)
{
    $content = '';
    $meta_value = get_post_meta($post_id, 'codex_creator_see', true);
    if (!$meta_value) {
        return;
    }
    $content .= WP_CODEX_TITLE_START . $title . WP_CODEX_TITLE_END;
    $content .= WP_CODEX_CONTENT_START . $meta_value . WP_CODEX_CONTENT_END;

    return apply_filters('codex_creator_see_content', $content, $post_id, $title);
}

/**
 * Get and format content output for since section of the codex page.
 *
 * @since 1.0.0
 * @package Codex Creator
 * @param int $post_id Post ID of the post content required.
 * @param string $title Title for the content section.
 * @return string The formatted content.
 */
function codex_creator_since_content($post_id, $title)
{
    $content = '';
    $meta_value = get_post_meta($post_id, 'codex_creator_since', true);
    if (!$meta_value) {
        return;
    }
    if (is_array($meta_value) && empty($meta_value)) {
        return false;
    }
    $content .= WP_CODEX_TITLE_START . $title . WP_CODEX_TITLE_END;
    $tags = array();
    if (is_array($meta_value)) {
        $i=0;
        foreach ($meta_value as $value) {

            if($i==0){$since = __('Since', WP_CODEX_TEXTDOMAIN).': ';}else{$since='';}

            if ($pieces = explode(" ", $value)) {
                $ver = $pieces[0];
                unset($pieces[0]);
                $text = join(' ', $pieces);
                $content .= WP_CODEX_CONTENT_START .$since. '<a href="%' . $ver . '%">' . $ver . '</a>' . ' ' . $text . WP_CODEX_CONTENT_END;
                $tags[] = $ver;
            } else {
                $content .= WP_CODEX_CONTENT_START . '<a href="%' . $value . '%">' . $value . '</a>' . WP_CODEX_CONTENT_END;
                $tags[] = $value;
            }
            $i++;

        }
    } else {
        $content .= WP_CODEX_CONTENT_START .__('Since', WP_CODEX_TEXTDOMAIN). ': <a href="%' . $meta_value . '%">' . $meta_value . '</a>' . WP_CODEX_CONTENT_END;
        $tags[] = $meta_value;
    }

    // get the project slug
    $project_slug = 'project-not-found';
    $project_terms = wp_get_object_terms($post_id, 'codex_project');
    if (!is_wp_error($project_terms) && !empty($project_terms) && is_object($project_terms[0])) {
        foreach ($project_terms as $p_term) {
            if ($p_term->parent == '0') {
                $project_slug = $p_term->slug;
            }
        }
    }


    //set all tags to have prefix of the project
    $alt_tags = array();
    foreach ($tags as $temp_tag) {
        $alt_tags[] = $project_slug . '_' . $temp_tag;
    }
    $tags = $alt_tags;


    $tags_arr = wp_set_post_terms($post_id, $tags, 'codex_tags', false);

    //print_r($tags);exit;

    if (is_array($tags_arr)) {
        foreach ($tags_arr as $key => $tag_id) {
            $term = get_term($tag_id, 'codex_tags');
            $tag_link = get_term_link($term, 'codex_tags');
            $orig_ver = str_replace($project_slug . '_', '', $tags[$key]);
            $content = str_replace('%' . $orig_ver . '%', $tag_link, $content);
        }
    }

    // print_r($tags_arr);exit;

    return apply_filters('codex_creator_since_content', $content, $post_id, $title);
}

/**
 * Get and format content output for subpackage section of the codex page.
 *
 * @since 1.0.0
 * @package Codex Creator
 * @param int $post_id Post ID of the post content required.
 * @param string $title Title for the content section.
 * @return string The formatted content.
 */
function codex_creator_subpackage_content($post_id, $title)
{
    $content = '';
    $meta_value = get_post_meta($post_id, 'codex_creator_subpackage', true);
    if (!$meta_value) {
        return;
    }
    $content .= WP_CODEX_TITLE_START . $title . WP_CODEX_TITLE_END;
    $content .= WP_CODEX_CONTENT_START . $meta_value . WP_CODEX_CONTENT_END;

    return apply_filters('codex_creator_subpackage_content', $content, $post_id, $title);
}

/**
 * Get and format content output for todo section of the codex page.
 *
 * @since 1.0.0
 * @package Codex Creator
 * @param int $post_id Post ID of the post content required.
 * @param string $title Title for the content section.
 * @return string The formatted content.
 */
function codex_creator_todo_content($post_id, $title)
{
    $content = '';
    $meta_value = get_post_meta($post_id, 'codex_creator_todo', true);
    if (!$meta_value) {
        return;
    }
    $content .= WP_CODEX_TITLE_START . $title . WP_CODEX_TITLE_END;
    $content .= WP_CODEX_CONTENT_START . $meta_value . WP_CODEX_CONTENT_END;

    return apply_filters('codex_creator_todo_content', $content, $post_id, $title);
}

/**
 * Get and format content output for type section of the codex page.
 *
 * @since 1.0.0
 * @package Codex Creator
 * @param int $post_id Post ID of the post content required.
 * @param string $title Title for the content section.
 * @return string The formatted content.
 */
function codex_creator_type_content($post_id, $title)
{
    $content = '';
    $meta_value = get_post_meta($post_id, 'codex_creator_type', true);
    if (!$meta_value) {
        return;
    }
    $content .= WP_CODEX_TITLE_START . $title . WP_CODEX_TITLE_END;
    $content .= WP_CODEX_CONTENT_START . $meta_value . WP_CODEX_CONTENT_END;

    return apply_filters('codex_creator_type_content', $content, $post_id, $title);
}

/**
 * Get and format content output for uses section of the codex page.
 *
 * @since 1.0.0
 * @package Codex Creator
 * @param int $post_id Post ID of the post content required.
 * @param string $title Title for the content section.
 * @return string The formatted content.
 */
function codex_creator_uses_content($post_id, $title)
{
    $content = '';
    $meta_value = get_post_meta($post_id, 'codex_creator_uses', true);
    if (!$meta_value) {
        return;
    }
    $content .= WP_CODEX_TITLE_START . $title . WP_CODEX_TITLE_END;
    $content .= WP_CODEX_CONTENT_START . $meta_value . WP_CODEX_CONTENT_END;

    return apply_filters('codex_creator_uses_content', $content, $post_id, $title);
}

/**
 * Get and format content output for var section of the codex page.
 *
 * @since 1.0.0
 * @package Codex Creator
 * @param int $post_id Post ID of the post content required.
 * @param string $title Title for the content section.
 * @return string The formatted content.
 */
function codex_creator_var_content($post_id, $title)
{
    $content = '';
    $meta_value = get_post_meta($post_id, 'codex_creator_var', true);
    if (!$meta_value) {
        return;
    }
    $content .= WP_CODEX_TITLE_START . $title . WP_CODEX_TITLE_END;
    $content .= WP_CODEX_CONTENT_START . $meta_value . WP_CODEX_CONTENT_END;

    return apply_filters('codex_creator_var_content', $content, $post_id, $title);
}

/**
 * Get and format content output for functions section of the codex page.
 *
 * @since 1.0.0
 * @package Codex Creator
 * @param int $post_id Post ID of the post content required.
 * @param string $title Title for the content section.
 * @return string The formatted content.
 */
function codex_creator_functions_content($post_id, $title)
{
    $content = '';
    $meta_value = get_post_meta($post_id, 'codex_creator_meta_functions', true);
    if (!$meta_value) {
        return;
    }
    $content .= WP_CODEX_TITLE_START . $title . WP_CODEX_TITLE_END;
    //$content .= WP_CODEX_CONTENT_START.$meta_value.WP_CODEX_CONTENT_END;

    if (is_array($meta_value)) {
        foreach ($meta_value as $func) {

            $func_arr = get_page_by_title($func[1], OBJECT, 'codex_creator');

            // print_r($func_arr);exit;

            if (is_object($func_arr)) {
                $link = get_permalink($func_arr->ID);
                $content .= WP_CODEX_CONTENT_START . '<a href="' . $link . '">' . $func[1] . '()</a> [' . __('Line', WP_CODEX_TEXTDOMAIN) . ': ' . $func[2] . ']' . WP_CODEX_CONTENT_END;
            } else {
                $content .= WP_CODEX_CONTENT_START . $func[1] . '() [' . __('Line', WP_CODEX_TEXTDOMAIN) . ': ' . $func[2] . ']' . WP_CODEX_CONTENT_END;

            }

        }


    }
    //$content .= WP_CODEX_CONTENT_START.print_r($meta_value,true).WP_CODEX_CONTENT_END;

    return apply_filters('codex_creator_functions_content', $content, $post_id, $title);
}

/**
 * Get and format content output for location section of the codex page.
 *
 * @since 1.0.0
 * @package Codex Creator
 * @param int $post_id Post ID of the post content required.
 * @param string $title Title for the content section.
 * @return string The formatted content.
 */
function codex_creator_location_content($post_id, $title)
{
    $content = '';
    $meta_type = get_post_meta($post_id, 'codex_creator_meta_type', true);
    if ($meta_type == 'file') {
        return false;
    }
    $meta_value = get_post_meta($post_id, 'codex_creator_meta_path', true);
    if (!$meta_value) {
        return;
    }
    $line = get_post_meta($post_id, 'codex_creator_meta_line', true);
    $content .= WP_CODEX_TITLE_START . $title . WP_CODEX_TITLE_END;

    $file_name = basename($meta_value);
    $func_arr = get_post($post_id);
    $func_name = $func_arr->post_title;

    $file_arr = get_page_by_title($file_name, OBJECT, 'codex_creator');
    if (is_object($file_arr)) {
        $link = get_permalink($file_arr->ID);
        $content .= WP_CODEX_CONTENT_START . $func_name . '() ' . __('is located in', WP_CODEX_TEXTDOMAIN) . ' <a href="' . $link . '">' . $meta_value . '</a> [' . __('Line', WP_CODEX_TEXTDOMAIN) . ': ' . $line . ']' . WP_CODEX_CONTENT_END;
    } else {
        $content .= WP_CODEX_CONTENT_START . $func_name . '() ' . __('is located in', WP_CODEX_TEXTDOMAIN) . ' ' . $meta_value . ' [' . __('Line', WP_CODEX_TEXTDOMAIN) . ': ' . $line . ']' . WP_CODEX_CONTENT_END;
    }


    return apply_filters('codex_creator_location_content', $content, $post_id, $title);
}