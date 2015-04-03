<?php
/**
 * Functions for building the page content for each codex page
 *
 * @since 1.0.0
 * @package Codex_Creator
 */

/**
 * Get and format content output for summary section of the codex page.
 *
 * @since 1.0.0
 * @package Codex_Creator
 * @param int $post_id Post ID of the post content required.
 * @param string $title Title for the content section.
 * @return string The formatted content.
 */
function cdxc_summary_content($post_id, $title)
{
    $content = '';
    $meta_value = get_post_meta($post_id, 'cdxc_summary', true);
    if (!$meta_value) {
        return;
    }
    $content .= CDXC_TITLE_START . $title . CDXC_TITLE_END;
    $content .= CDXC_CONTENT_START . $meta_value . CDXC_CONTENT_END;

    /**
     * Filter the content returned by the function.
     *
     * @since 1.0.0
     * @param string $content The content to be output.
     * @param int $post_id The post ID.
     * @param string $title The title for the content.
     */
    return apply_filters('cdxc_summary_content', $content, $post_id, $title);
}

/**
 * Get and format content output for description section of the codex page.
 *
 * @since 1.0.0
 * @package Codex_Creator
 * @param int $post_id Post ID of the post content required.
 * @param string $title Title for the content section.
 * @return string The formatted content.
 */
function cdxc_description_content($post_id, $title)
{
    $content = '';
    //$meta_value = get_post_meta($post_id, 'cdxc_description', true);
    $meta_value = get_post_meta($post_id, 'cdxc_meta_docblock', true);
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
        $line_arr[$sample_open] = CDXC_SAMPLE_OPEN.$line_arr[$sample_open];
        $line_arr[$sample_open] = $line_arr[$sample_close].CDXC_SAMPLE_CLOSE;
    }

    $meta_value = implode(PHP_EOL, $line_arr);

    $content .= CDXC_TITLE_START . $title . CDXC_TITLE_END;
    $content .= CDXC_CONTENT_START . $meta_value . CDXC_CONTENT_END;

    /**
     * Filter the content returned by the function.
     *
     * @since 1.0.0
     * @param string $content The content to be output.
     * @param int $post_id The post ID.
     * @param string $title The title for the content.
     */
    return apply_filters('cdxc_description_content', $content, $post_id, $title);
}

/**
 * Get and format content output for usage section of the codex page.
 *
 * @since 1.0.0
 * @package Codex_Creator
 * @param int $post_id Post ID of the post content required.
 * @param string $title Title for the content section.
 * @return string The formatted content.
 */
function cdxc_usage_content($post_id, $title)
{
    $content = '';
    $meta_value = get_post_meta($post_id, 'cdxc_usage', true);
    if (!$meta_value) {
        return;
    }
    $content .= CDXC_TITLE_START . $title . CDXC_TITLE_END;
    $content .= CDXC_CONTENT_START . $meta_value . CDXC_CONTENT_END;

    /**
     * Filter the content returned by the function.
     *
     * @since 1.0.0
     * @param string $content The content to be output.
     * @param int $post_id The post ID.
     * @param string $title The title for the content.
     */
    return apply_filters('cdxc_usage_content', $content, $post_id, $title);
}

/**
 * Get and format content output for access section of the codex page.
 *
 * @since 1.0.0
 * @package Codex_Creator
 * @param int $post_id Post ID of the post content required.
 * @param string $title Title for the content section.
 * @return string The formatted content.
 */
function cdxc_access_content($post_id, $title)
{
    $content = '';
    $meta_value = get_post_meta($post_id, 'cdxc_access', true);
    if (!$meta_value) {
        return;
    }
    $content .= CDXC_TITLE_START . $title . CDXC_TITLE_END;
    $content .= CDXC_CONTENT_START . $meta_value . CDXC_CONTENT_END;

    /**
     * Filter the content returned by the function.
     *
     * @since 1.0.0
     * @param string $content The content to be output.
     * @param int $post_id The post ID.
     * @param string $title The title for the content.
     */
    return apply_filters('cdxc_access_content', $content, $post_id, $title);
}

/**
 * Get and format content output for deprecated section of the codex page.
 *
 * @since 1.0.0
 * @package Codex_Creator
 * @param int $post_id Post ID of the post content required.
 * @param string $title Title for the content section.
 * @return string The formatted content.
 */
function cdxc_deprecated_content($post_id, $title)
{
    $content = '';
    $meta_value = get_post_meta($post_id, 'cdxc_deprecated', true);
    if (!$meta_value) {
        return;
    }
    $content .= CDXC_TITLE_START . $title . CDXC_TITLE_END;
    $content .= CDXC_CONTENT_START . $meta_value . CDXC_CONTENT_END;

    /**
     * Filter the content returned by the function.
     *
     * @since 1.0.0
     * @param string $content The content to be output.
     * @param int $post_id The post ID.
     * @param string $title The title for the content.
     */
    return apply_filters('cdxc_deprecated_content', $content, $post_id, $title);
}

/**
 * Get and format content output for global section of the codex page.
 *
 * @since 1.0.0
 * @package Codex_Creator
 * @param int $post_id Post ID of the post content required.
 * @param string $title Title for the content section.
 * @return string The formatted content.
 */
function cdxc_global_content($post_id, $title)
{
    $content = '';
    $meta_value = get_post_meta($post_id, 'cdxc_global', true);
    if (!$meta_value) {
        return;
    }
    $content .= CDXC_TITLE_START . $title . CDXC_TITLE_END;
    if (is_array($meta_value)) {
        foreach ($meta_value as $value) {
            $value = cdxc_global_content_helper($value);
            $content .= CDXC_CONTENT_START . $value . CDXC_CONTENT_END;
        }
    } else {
        $meta_value = cdxc_global_content_helper($meta_value);
        $content .= CDXC_CONTENT_START . $meta_value . CDXC_CONTENT_END;
    }

    /**
     * Filter the content returned by the function.
     *
     * @since 1.0.0
     * @param string $content The content to be output.
     * @param int $post_id The post ID.
     * @param string $title The title for the content.
     */
    return apply_filters('cdxc_global_content', $content, $post_id, $title);
}

/**
 * Arrange a param value into a usable HTML output.
 *
 * @since 1.0.0
 * @package Codex_Creator
 * @param string $param The param value to be used.
 * @return string Formatted HTML on success.
 */
function cdxc_global_content_helper($param)
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
    $optional = '(<i>'.__('required', CDXC_TEXTDOMAIN).'</i>)';
    if(!empty($param_arr[2])){
        $opt = $param_arr[2];
        if($opt=='Optional.' || $opt=='optional.'){
            $optional = '(<i>'.__('optional', CDXC_TEXTDOMAIN).'</i>)';
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
    $default = '<dl><dd>'.__('Default', CDXC_TEXTDOMAIN).': <i>'.__('None', CDXC_TEXTDOMAIN).'</i></dd></dl>';
    foreach ($param_arr as $bit) {
        $bit = trim($bit);
        //echo '#'.$bit.'#';
        if(substr( $bit, 0, 7) === 'Default'){
            $bits = explode('Default',$bit);
            $default = '<dl><dd>'.__('Default', CDXC_TEXTDOMAIN).': <i>'.$bits[1].'</i></dd></dl>';

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
 * Get and format content output for internal section of the codex page.
 *
 * @since 1.0.0
 * @package Codex_Creator
 * @param int $post_id Post ID of the post content required.
 * @param string $title Title for the content section.
 * @return string The formatted content.
 */
function cdxc_internal_content($post_id, $title)
{
    $content = '';
    $meta_value = get_post_meta($post_id, 'cdxc_internal', true);
    if (!$meta_value) {
        return;
    }
    $content .= CDXC_TITLE_START . $title . CDXC_TITLE_END;
    $content .= CDXC_CONTENT_START . $meta_value . CDXC_CONTENT_END;

    /**
     * Filter the content returned by the function.
     *
     * @since 1.0.0
     * @param string $content The content to be output.
     * @param int $post_id The post ID.
     * @param string $title The title for the content.
     */
    return apply_filters('cdxc_internal_content', $content, $post_id, $title);
}

/**
 * Get and format content output for ignore section of the codex page.
 *
 * @since 1.0.0
 * @package Codex_Creator
 * @param int $post_id Post ID of the post content required.
 * @param string $title Title for the content section.
 * @return string The formatted content.
 */
function cdxc_ignore_content($post_id, $title)
{
    $content = '';
    $meta_value = get_post_meta($post_id, 'cdxc_ignore', true);
    if (!$meta_value) {
        return;
    }
    $content .= CDXC_TITLE_START . $title . CDXC_TITLE_END;
    $content .= CDXC_CONTENT_START . $meta_value . CDXC_CONTENT_END;

    /**
     * Filter the content returned by the function.
     *
     * @since 1.0.0
     * @param string $content The content to be output.
     * @param int $post_id The post ID.
     * @param string $title The title for the content.
     */
    return apply_filters('cdxc_ignore_content', $content, $post_id, $title);
}

/**
 * Get and format content output for link section of the codex page.
 *
 * @since 1.0.0
 * @package Codex_Creator
 * @param int $post_id Post ID of the post content required.
 * @param string $title Title for the content section.
 * @return string The formatted content.
 */
function cdxc_link_content($post_id, $title)
{
    $content = '';
    $meta_value = get_post_meta($post_id, 'cdxc_link', true);
    if (!$meta_value) {
        return;
    }
    /*
     * @todo add checking for parsing links
     */
    $content .= CDXC_TITLE_START . $title . CDXC_TITLE_END;
    $content .= CDXC_CONTENT_START ."<a href='$meta_value' >".$meta_value . "</a>".CDXC_CONTENT_END;

    /**
     * Filter the content returned by the function.
     *
     * @since 1.0.0
     * @param string $content The content to be output.
     * @param int $post_id The post ID.
     * @param string $title The title for the content.
     */
    return apply_filters('cdxc_link_content', $content, $post_id, $title);
}

/**
 * Get and format content output for method section of the codex page.
 *
 * @since 1.0.0
 * @package Codex_Creator
 * @param int $post_id Post ID of the post content required.
 * @param string $title Title for the content section.
 * @return string The formatted content.
 */
function cdxc_method_content($post_id, $title)
{
    $content = '';
    $meta_value = get_post_meta($post_id, 'cdxc_method', true);
    if (!$meta_value) {
        return;
    }
    $content .= CDXC_TITLE_START . $title . CDXC_TITLE_END;
    $content .= CDXC_CONTENT_START . $meta_value . CDXC_CONTENT_END;

    /**
     * Filter the content returned by the function.
     *
     * @since 1.0.0
     * @param string $content The content to be output.
     * @param int $post_id The post ID.
     * @param string $title The title for the content.
     */
    return apply_filters('cdxc_method_content', $content, $post_id, $title);
}

/**
 * Get and format content output for package section of the codex page.
 *
 * @since 1.0.0
 * @package Codex_Creator
 * @param int $post_id Post ID of the post content required.
 * @param string $title Title for the content section.
 * @return string The formatted content.
 */
function cdxc_package_content($post_id, $title)
{
    $content = '';
    $meta_value = get_post_meta($post_id, 'cdxc_package', true);
    if (!$meta_value) {
        return;
    }
    $content .= CDXC_TITLE_START . $title . CDXC_TITLE_END;
    $content .= CDXC_CONTENT_START . $meta_value . CDXC_CONTENT_END;

    /**
     * Filter the content returned by the function.
     *
     * @since 1.0.0
     * @param string $content The content to be output.
     * @param int $post_id The post ID.
     * @param string $title The title for the content.
     */
    return apply_filters('cdxc_package_content', $content, $post_id, $title);
}

/**
 * Get and format content output for param section of the codex page.
 *
 * @since 1.0.0
 * @package Codex_Creator
 * @param int $post_id Post ID of the post content required.
 * @param string $title Title for the content section.
 * @return string The formatted content.
 */
function cdxc_param_content($post_id, $title)
{
    $content = '';
    $meta_value = get_post_meta($post_id, 'cdxc_param', true);
    if (!$meta_value) {
        return;
    }
    $content .= CDXC_TITLE_START . $title . CDXC_TITLE_END;
    if (is_array($meta_value)) {
        foreach ($meta_value as $value) {
            $value = cdxc_param_content_helper($value);
            $content .= CDXC_CONTENT_START . $value . CDXC_CONTENT_END;
        }
    } else {
        $meta_value = cdxc_param_content_helper($meta_value);
        $content .= CDXC_CONTENT_START . $meta_value . CDXC_CONTENT_END;
    }

    /**
     * Filter the content returned by the function.
     *
     * @since 1.0.0
     * @param string $content The content to be output.
     * @param int $post_id The post ID.
     * @param string $title The title for the content.
     */
    return apply_filters('cdxc_param_content', $content, $post_id, $title);
}

/**
 * Arrange a param value into a usable HTML output.
 *
 * @since 1.0.0
 * @package Codex_Creator
 * @param string $param The param value to be used.
 * @return string Formatted HTML on success.
 */
function cdxc_param_content_helper($param)
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
    $optional = '(<i>'.__('required', CDXC_TEXTDOMAIN).'</i>)';
    if(!empty($param_arr[2])){
        $opt = $param_arr[2];
        if($opt=='Optional.' || $opt=='optional.'){
            $optional = '(<i>'.__('optional', CDXC_TEXTDOMAIN).'</i>)';
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
    $default = '<dl><dd>'.__('Default', CDXC_TEXTDOMAIN).': <i>'.__('None', CDXC_TEXTDOMAIN).'</i></dd></dl>';
    foreach ($param_arr as $bit) {
        $bit = trim($bit);
        //echo '#'.$bit.'#';
        if(substr( $bit, 0, 7) === 'Default'){
            $bits = explode('Default',$bit);
            $default = '<dl><dd>'.__('Default', CDXC_TEXTDOMAIN).': <i>'.$bits[1].'</i></dd></dl>';

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
 * @package Codex_Creator
 * @param int $post_id Post ID of the post content required.
 * @param string $title Title for the content section.
 * @return string The formatted content.
 */
function cdxc_example_content($post_id, $title)
{
    $content = '';
    $meta_value = get_post_meta($post_id, 'cdxc_example', true);
    if (!$meta_value) {
        return;
    }
    $content .= CDXC_TITLE_START . $title . CDXC_TITLE_END;
    $content .= CDXC_CONTENT_START . $meta_value . CDXC_CONTENT_END;

    /**
     * Filter the content returned by the function.
     *
     * @since 1.0.0
     * @param string $content The content to be output.
     * @param int $post_id The post ID.
     * @param string $title The title for the content.
     */
    return apply_filters('cdxc_example_content', $content, $post_id, $title);
}

/**
 * Get and format content output for return section of the codex page.
 *
 * @since 1.0.0
 * @package Codex_Creator
 * @param int $post_id Post ID of the post content required.
 * @param string $title Title for the content section.
 * @return string The formatted content.
 */
function cdxc_return_content($post_id, $title)
{
    $content = '';
    $meta_value = get_post_meta($post_id, 'cdxc_return', true);
    if (!$meta_value) {
        return;
    }
    $content .= CDXC_TITLE_START . $title . CDXC_TITLE_END;
    $meta_value = cdxc_return_content_helper($meta_value);
    $content .= CDXC_CONTENT_START . $meta_value . CDXC_CONTENT_END;

    /**
     * Filter the content returned by the function.
     *
     * @since 1.0.0
     * @param string $content The content to be output.
     * @param int $post_id The post ID.
     * @param string $title The title for the content.
     */
    return apply_filters('cdxc_return_content', $content, $post_id, $title);
}

/**
 * Arrange a return value into a usable HTML output.
 *
 * @since 1.0.0
 * @package Codex_Creator
 * @param string $value The string value to be used.
 * @return string Formatted HTML on success.
 */
function cdxc_return_content_helper($value)
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

    /**
     * Filter the content returned by the function.
     *
     * @since 1.0.0
     * @param string $content The content to be output.
     * @param int $post_id The post ID.
     * @param string $title The title for the content.
     */
    return $output;
}

/**
 * Get and format content output for see section of the codex page.
 *
 * @since 1.0.0
 * @package Codex_Creator
 * @param int $post_id Post ID of the post content required.
 * @param string $title Title for the content section.
 * @return string The formatted content.
 */
function cdxc_see_content($post_id, $title)
{
    $content = '';
    $meta_value = get_post_meta($post_id, 'cdxc_see', true);
    if (!$meta_value) {
        return;
    }


    $content .= CDXC_TITLE_START . $title . CDXC_TITLE_END;
    if (is_array($meta_value)) {
        foreach ($meta_value as $value) {
            $value = cdxc_see_content_helper($value);
            $content .= CDXC_CONTENT_START . $value . CDXC_CONTENT_END;
        }
    } else {
        $meta_value = cdxc_see_content_helper($meta_value);
        $content .= CDXC_CONTENT_START . $meta_value . CDXC_CONTENT_END;
    }

    /**
     * Filter the content returned by the function.
     *
     * @since 1.0.0
     * @param string $content The content to be output.
     * @param int $post_id The post ID.
     * @param string $title The title for the content.
     */
    return apply_filters('cdxc_see_content', $content, $post_id, $title);
}

/**
 * Arrange a see value into a usable HTML output.
 *
 * @since 1.0.0
 * @package Codex_Creator
 * @param string $value The string value to be used.
 * @return string Formatted HTML on success.
 * @todo make this format URL's.
 */
function cdxc_see_content_helper($text)
{
    if ($text == '') {
        return '';
    }

    if (strpos($text,'"') !== false || strpos($text,"'") !== false) {// we have a hook

        $new_text = str_replace(array('"',"'"),'',$text);
        $page = get_page_by_title($new_text, OBJECT, 'codex_creator');
        if($page) {
            $link = get_permalink($page->ID);
            $text = "<a href='$link' >$text</a>";
        }

    }elseif(strpos($text,'()') !== false){

        $new_text = str_replace('()','',$text);
        $page = get_page_by_title($new_text, OBJECT, 'codex_creator');
        if($page) {
            $link = get_permalink($page->ID);
            $text = "<a href='$link' >$text</a>";
        }

    }

    return $text;
}

/**
 * Get and format content output for since section of the codex page.
 *
 * @since 1.0.0
 * @package Codex_Creator
 * @param int $post_id Post ID of the post content required.
 * @param string $title Title for the content section.
 * @return string The formatted content.
 */
function cdxc_since_content($post_id, $title)
{
    $content = '';
    $meta_value = get_post_meta($post_id, 'cdxc_since', true);
    if (!$meta_value) {
        return;
    }
    if (is_array($meta_value) && empty($meta_value)) {
        return false;
    }
    $content .= CDXC_TITLE_START . $title . CDXC_TITLE_END;
    $tags = array();
    if (is_array($meta_value)) {
        $i=0;
        foreach ($meta_value as $value) {

            if($i==0){$since = __('Since', CDXC_TEXTDOMAIN).': ';}else{$since='';}

            if ($pieces = explode(" ", $value)) {
                $ver = $pieces[0];
                unset($pieces[0]);
                $text = join(' ', $pieces);
                $content .= CDXC_CONTENT_START .$since. '<a href="%' . $ver . '%">' . $ver . '</a>' . ' ' . $text . CDXC_CONTENT_END;
                $tags[] = $ver;
            } else {
                $content .= CDXC_CONTENT_START . '<a href="%' . $value . '%">' . $value . '</a>' . CDXC_CONTENT_END;
                $tags[] = $value;
            }
            $i++;

        }
    } else {
        $content .= CDXC_CONTENT_START .__('Since', CDXC_TEXTDOMAIN). ': <a href="%' . $meta_value . '%">' . $meta_value . '</a>' . CDXC_CONTENT_END;
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

    //print_r($tags_arr);//exit;

    if (is_array($tags_arr)) {
        foreach ($tags_arr as $key => $tag_id) {
            //$term = get_term($tag_id, 'codex_tags');
            $term = get_term_by('term_taxonomy_id', $tag_id, 'codex_tags');

            $tag_link = get_term_link($term, 'codex_tags');
            $orig_ver = str_replace($project_slug . '_', '', $tags[$key]);

            $content = str_replace('%' . $orig_ver . '%', $tag_link, $content);
        }
    }

    // print_r($tags_arr);exit;

    /**
     * Filter the content returned by the function.
     *
     * @since 1.0.0
     * @param string $content The content to be output.
     * @param int $post_id The post ID.
     * @param string $title The title for the content.
     */
    return apply_filters('cdxc_since_content', $content, $post_id, $title);
}

/**
 * Get and format content output for subpackage section of the codex page.
 *
 * @since 1.0.0
 * @package Codex_Creator
 * @param int $post_id Post ID of the post content required.
 * @param string $title Title for the content section.
 * @return string The formatted content.
 */
function cdxc_subpackage_content($post_id, $title)
{
    $content = '';
    $meta_value = get_post_meta($post_id, 'cdxc_subpackage', true);
    if (!$meta_value) {
        return;
    }
    $content .= CDXC_TITLE_START . $title . CDXC_TITLE_END;
    $content .= CDXC_CONTENT_START . $meta_value . CDXC_CONTENT_END;

    /**
     * Filter the content returned by the function.
     *
     * @since 1.0.0
     * @param string $content The content to be output.
     * @param int $post_id The post ID.
     * @param string $title The title for the content.
     */
    return apply_filters('cdxc_subpackage_content', $content, $post_id, $title);
}

/**
 * Get and format content output for todo section of the codex page.
 *
 * @since 1.0.0
 * @package Codex_Creator
 * @param int $post_id Post ID of the post content required.
 * @param string $title Title for the content section.
 * @return string The formatted content.
 */
function cdxc_todo_content($post_id, $title)
{
    $content = '';
    $meta_value = get_post_meta($post_id, 'cdxc_todo', true);
    if (!$meta_value) {
        return;
    }
    $content .= CDXC_TITLE_START . $title . CDXC_TITLE_END;
    $content .= CDXC_CONTENT_START . $meta_value . CDXC_CONTENT_END;

    /**
     * Filter the content returned by the function.
     *
     * @since 1.0.0
     * @param string $content The content to be output.
     * @param int $post_id The post ID.
     * @param string $title The title for the content.
     */
    return apply_filters('cdxc_todo_content', $content, $post_id, $title);
}

/**
 * Get and format content output for type section of the codex page.
 *
 * @since 1.0.0
 * @package Codex_Creator
 * @param int $post_id Post ID of the post content required.
 * @param string $title Title for the content section.
 * @return string The formatted content.
 */
function cdxc_type_content($post_id, $title)
{
    $content = '';
    $meta_value = get_post_meta($post_id, 'cdxc_type', true);
    if (!$meta_value) {
        return;
    }
    $content .= CDXC_TITLE_START . $title . CDXC_TITLE_END;
    $content .= CDXC_CONTENT_START . $meta_value . CDXC_CONTENT_END;

    /**
     * Filter the content returned by the function.
     *
     * @since 1.0.0
     * @param string $content The content to be output.
     * @param int $post_id The post ID.
     * @param string $title The title for the content.
     */
    return apply_filters('cdxc_type_content', $content, $post_id, $title);
}

/**
 * Get and format content output for uses section of the codex page.
 *
 * @since 1.0.0
 * @package Codex_Creator
 * @param int $post_id Post ID of the post content required.
 * @param string $title Title for the content section.
 * @return string The formatted content.
 */
function cdxc_uses_content($post_id, $title)
{   return;// @todo make this work with arrays.
    $content = '';
    $meta_value = get_post_meta($post_id, 'cdxc_uses', true);
    if (!$meta_value) {
        return;
    }
    $content .= CDXC_TITLE_START . $title . CDXC_TITLE_END;
    $content .= CDXC_CONTENT_START . $meta_value . CDXC_CONTENT_END;

    /**
     * Filter the content returned by the function.
     *
     * @since 1.0.0
     * @param string $content The content to be output.
     * @param int $post_id The post ID.
     * @param string $title The title for the content.
     */
    return apply_filters('cdxc_uses_content', $content, $post_id, $title);
}

/**
 * Get and format content output for var section of the codex page.
 *
 * @since 1.0.0
 * @package Codex_Creator
 * @param int $post_id Post ID of the post content required.
 * @param string $title Title for the content section.
 * @return string The formatted content.
 */
function cdxc_var_content($post_id, $title)
{
    $content = '';
    $meta_value = get_post_meta($post_id, 'cdxc_var', true);
    if (!$meta_value) {
        return;
    }
    $content .= CDXC_TITLE_START . $title . CDXC_TITLE_END;
    $content .= CDXC_CONTENT_START . $meta_value . CDXC_CONTENT_END;

    /**
     * Filter the content returned by the function.
     *
     * @since 1.0.0
     * @param string $content The content to be output.
     * @param int $post_id The post ID.
     * @param string $title The title for the content.
     */
    return apply_filters('cdxc_var_content', $content, $post_id, $title);
}

/**
 * Get and format content output for functions section of the codex page.
 *
 * @since 1.0.0
 * @package Codex_Creator
 * @param int $post_id Post ID of the post content required.
 * @param string $title Title for the content section.
 * @return string The formatted content.
 */
function cdxc_functions_content($post_id, $title)
{
    $content = '';
    $meta_value = get_post_meta($post_id, 'cdxc_meta_functions', true);
    if (!$meta_value) {
        return;
    }
    $content .= CDXC_TITLE_START . $title . CDXC_TITLE_END;
    //$content .= CDXC_CONTENT_START.$meta_value.CDXC_CONTENT_END;

    if (is_array($meta_value)) {
        foreach ($meta_value as $func) {

            $func_arr = get_page_by_title($func[1], OBJECT, 'codex_creator');

            // print_r($func_arr);exit;

            if (is_object($func_arr)) {
                $link = get_permalink($func_arr->ID);
                $content .= CDXC_CONTENT_START . '<a href="' . $link . '">' . $func[1] . '()</a> [' . __('Line', CDXC_TEXTDOMAIN) . ': ' . $func[2] . ']' . CDXC_CONTENT_END;
            } else {
                $content .= CDXC_CONTENT_START . $func[1] . '() [' . __('Line', CDXC_TEXTDOMAIN) . ': ' . $func[2] . ']' . CDXC_CONTENT_END;

            }

        }


    }
    //$content .= CDXC_CONTENT_START.print_r($meta_value,true).CDXC_CONTENT_END;

    /**
     * Filter the content returned by the function.
     *
     * @since 1.0.0
     * @param string $content The content to be output.
     * @param int $post_id The post ID.
     * @param string $title The title for the content.
     */
    return apply_filters('cdxc_functions_content', $content, $post_id, $title);
}


/**
 * Get and format content output for actions section of the codex page.
 *
 * @since 1.0.0
 * @package Codex_Creator
 * @param int $post_id Post ID of the post content required.
 * @param string $title Title for the content section.
 * @return string The formatted content.
 */
function cdxc_actions_content($post_id, $title)
{
    $content = '';
    $meta_value = get_post_meta($post_id, 'cdxc_meta_actions', true);
    if (!$meta_value) {
        return;
    }
    $content .= CDXC_TITLE_START . $title . CDXC_TITLE_END;

    if (is_array($meta_value)) {
        foreach ($meta_value as $func) {

            $func_arr = get_page_by_title($func[1], OBJECT, 'codex_creator');

            //print_r($func_arr);exit;
            $name = "'".$func[1]."'";
            if (is_object($func_arr)) {
                $link = get_permalink($func_arr->ID);

                $content .= CDXC_CONTENT_START . '<a href="' . $link . '">' .  $name . ' </a> [' . __('Line', CDXC_TEXTDOMAIN) . ': ' . $func[2] . ']' . CDXC_CONTENT_END;
            } else {
                $content .= CDXC_CONTENT_START . $name . ' [' . __('Line', CDXC_TEXTDOMAIN) . ': ' . $func[2] . ']' . CDXC_CONTENT_END;

            }

        }


    }
    //$content .= CDXC_CONTENT_START.print_r($meta_value,true).CDXC_CONTENT_END;

    /**
     * Filter the content returned by the function.
     *
     * @since 1.0.0
     * @param string $content The content to be output.
     * @param int $post_id The post ID.
     * @param string $title The title for the content.
     */
    return apply_filters('cdxc_actions_content', $content, $post_id, $title);
}

/**
 * Get and format content output for filters section of the codex page.
 *
 * @since 1.0.0
 * @package Codex_Creator
 * @param int $post_id Post ID of the post content required.
 * @param string $title Title for the content section.
 * @return string The formatted content.
 */
function cdxc_filters_content($post_id, $title)
{
    $content = '';
    $meta_value = get_post_meta($post_id, 'cdxc_meta_filters', true);
    if (!$meta_value) {
        return;
    }
    $content .= CDXC_TITLE_START . $title . CDXC_TITLE_END;

    if (is_array($meta_value)) {
        foreach ($meta_value as $func) {

            $func_arr = get_page_by_title($func[1], OBJECT, 'codex_creator');

            // print_r($func_arr);exit;
            $name = "'".$func[1]."'";
            if (is_object($func_arr)) {
                $link = get_permalink($func_arr->ID);

                $content .= CDXC_CONTENT_START . '<a href="' . $link . '">' .  $name . ' </a> [' . __('Line', CDXC_TEXTDOMAIN) . ': ' . $func[2] . ']' . CDXC_CONTENT_END;
            } else {
                $content .= CDXC_CONTENT_START . $name . ' [' . __('Line', CDXC_TEXTDOMAIN) . ': ' . $func[2] . ']' . CDXC_CONTENT_END;

            }

        }


    }
    //$content .= CDXC_CONTENT_START.print_r($meta_value,true).CDXC_CONTENT_END;

    /**
     * Filter the content returned by the function.
     *
     * @since 1.0.0
     * @param string $content The content to be output.
     * @param int $post_id The post ID.
     * @param string $title The title for the content.
     */
    return apply_filters('cdxc_filters_content', $content, $post_id, $title);
}

/**
 * Get and format content output for location section of the codex page.
 *
 * @since 1.0.0
 * @package Codex_Creator
 * @param int $post_id Post ID of the post content required.
 * @param string $title Title for the content section.
 * @return string The formatted content.
 */
function cdxc_location_content($post_id, $title)
{
    $content = '';
    $meta_type = get_post_meta($post_id, 'cdxc_meta_type', true);
    if ($meta_type == 'file' || $meta_type == 'action' || $meta_type == 'filter') {
        return false;
    }
    $meta_value = get_post_meta($post_id, 'cdxc_meta_path', true);
    if (!$meta_value) {
        return;
    }
    $line = get_post_meta($post_id, 'cdxc_meta_line', true);
    $content .= CDXC_TITLE_START . $title . CDXC_TITLE_END;

    $file_name = basename($meta_value);
    $func_arr = get_post($post_id);
    $func_name = $func_arr->post_title;

    if($meta_type=='function'){
        $func_name_n = $func_name. '() ';
    }else{
        $func_name_n = "'".$func_name. "' ";
    }

    $file_arr = get_page_by_title($file_name, OBJECT, 'codex_creator');
    if (is_object($file_arr)) {
        $link = get_permalink($file_arr->ID);
        $content .= CDXC_CONTENT_START .$func_name_n . __('is located in', CDXC_TEXTDOMAIN) . ' <a href="' . $link . '">' . $meta_value . '</a> [' . __('Line', CDXC_TEXTDOMAIN) . ': ' . $line . ']' . CDXC_CONTENT_END;
    } else {
        $content .= CDXC_CONTENT_START . $func_name_n . __('is located in', CDXC_TEXTDOMAIN) . ' ' . $meta_value . ' [' . __('Line', CDXC_TEXTDOMAIN) . ': ' . $line . ']' . CDXC_CONTENT_END;
    }

    /**
     * Filter the content returned by the function.
     *
     * @since 1.0.0
     * @param string $content The content to be output.
     * @param int $post_id The post ID.
     * @param string $title The title for the content.
     */
    return apply_filters('cdxc_location_content', $content, $post_id, $title);
}


/**
 * Get and format content output for source code section of the codex page.
 *
 * @since 1.0.0
 * @package Codex_Creator
 * @param int $post_id Post ID of the post content required.
 * @param string $title Title for the content section.
 * @return string The formatted content.
 */
function cdxc_code_content($post_id, $title)
{
    $content = '';
    $meta_value = get_post_meta($post_id, 'cdxc_meta_code', true);


    if (!$meta_value) {
        return;
    }
    $meta_value = "%%CDXC_SRC_CODE%%";
    $content .= CDXC_TITLE_START . $title . CDXC_TITLE_END;
    //$content .= CDXC_PHP_CODE_START . $meta_value . CDXC_PHP_CODE_END;
    $content .= CDXC_CONTENT_START . $meta_value . CDXC_CONTENT_END;

    /**
     * Filter the content returned by the function.
     *
     * @since 1.0.0
     * @param string $content The content to be output.
     * @param int $post_id The post ID.
     * @param string $title The title for the content.
     */
    return apply_filters('cdxc_code_content', $content, $post_id, $title);
}

/**
 * Get and format content output for filters section of the codex page.
 *
 * @since 1.0.0
 * @package Codex_Creator
 * @param int $post_id Post ID of the post content required.
 * @param string $title Title for the content section.
 * @return string The formatted content.
 */
function cdxc_used_by_content($post_id, $title)
{
    $content = '';
    $meta_value = get_post_meta($post_id, 'cdxc_meta_used_by', true);
    if (!$meta_value) {
        return;
    }
    $content .= CDXC_TITLE_START . $title . CDXC_TITLE_END;

    if (is_array($meta_value)) {
        foreach ($meta_value as $func) {

            $func_arr = get_page_by_title($func['function_name'], OBJECT, 'codex_creator');

            // print_r($func_arr);exit;
            $name = '';
            if($func['function_name']){
                $name = "".$func['function_name']."()";
            }
            if (is_object($func_arr)) {
                $link = get_permalink($func_arr->ID);

                $content .= CDXC_CONTENT_START . $func['file_path'].': <a href="' . $link . '">' .  $name . ' </a> [' . __('Line', CDXC_TEXTDOMAIN) . ': ' . $func['hook_line'] . ']' . CDXC_CONTENT_END;
            } else {
                $content .= CDXC_CONTENT_START .$func['file_path'].': '. $name . ' [' . __('Line', CDXC_TEXTDOMAIN) . ': ' . $func['hook_line'] . ']' . CDXC_CONTENT_END;

            }

        }


    }

    //$content .= CDXC_CONTENT_START.print_r($meta_value,true).CDXC_CONTENT_END;

    /**
     * Filter the content returned by the function.
     *
     * @since 1.0.0
     * @param string $content The content to be output.
     * @param int $post_id The post ID.
     * @param string $title The title for the content.
     */
    return apply_filters('cdxc_used_by_content', $content, $post_id, $title);
}