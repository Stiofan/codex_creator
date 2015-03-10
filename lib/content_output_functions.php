<?php

function codex_creator_summary_content($post_id,$title){
    $content = '';
    $meta_value = get_post_meta($post_id, 'codex_creator_summary',true);
    if(!$meta_value){return;}
    $content .= WP_CODEX_TITLE_START.$title.WP_CODEX_TITLE_END;
    $content .= WP_CODEX_CONTENT_START.$meta_value.WP_CODEX_CONTENT_END;

    return apply_filters('codex_creator_summary_content',$content,$post_id,$title);
}


function codex_creator_description_content($post_id,$title){
    $content = '';
    $meta_value = get_post_meta($post_id, 'codex_creator_description',true);
    if(!$meta_value){return;}
    $content .= WP_CODEX_TITLE_START.$title.WP_CODEX_TITLE_END;
    $content .= WP_CODEX_CONTENT_START.$meta_value.WP_CODEX_CONTENT_END;

    return apply_filters('codex_creator_description_content',$content,$post_id,$title);
}

function codex_creator_usage_content($post_id,$title){
    $content = '';
    $meta_value = get_post_meta($post_id, 'codex_creator_usage',true);
    if(!$meta_value){return;}
    $content .= WP_CODEX_TITLE_START.$title.WP_CODEX_TITLE_END;
    $content .= WP_CODEX_CONTENT_START.$meta_value.WP_CODEX_CONTENT_END;

    return apply_filters('codex_creator_usage_content',$content,$post_id,$title);
}

function codex_creator_access_content($post_id,$title){
    $content = '';
    $meta_value = get_post_meta($post_id, 'codex_creator_access',true);
    if(!$meta_value){return;}
    $content .= WP_CODEX_TITLE_START.$title.WP_CODEX_TITLE_END;
    $content .= WP_CODEX_CONTENT_START.$meta_value.WP_CODEX_CONTENT_END;

    return apply_filters('codex_creator_access_content',$content,$post_id,$title);
}

function codex_creator_deprecated_content($post_id,$title){
    $content = '';
    $meta_value = get_post_meta($post_id, 'codex_creator_deprecated',true);
    if(!$meta_value){return;}
    $content .= WP_CODEX_TITLE_START.$title.WP_CODEX_TITLE_END;
    $content .= WP_CODEX_CONTENT_START.$meta_value.WP_CODEX_CONTENT_END;

    return apply_filters('codex_creator_deprecated_content',$content,$post_id,$title);
}

function codex_creator_global_content($post_id,$title){
    $content = '';
    $meta_value = get_post_meta($post_id, 'codex_creator_global',true);
    if(!$meta_value){return;}
    $content .= WP_CODEX_TITLE_START.$title.WP_CODEX_TITLE_END;
    if(is_array($meta_value)){
        foreach($meta_value as $value){
            $content .= WP_CODEX_CONTENT_START.$value.WP_CODEX_CONTENT_END;
        }
    }else{
        $content .= WP_CODEX_CONTENT_START.$meta_value.WP_CODEX_CONTENT_END;
    }


    return apply_filters('codex_creator_global_content',$content,$post_id,$title);
}

function codex_creator_internal_content($post_id,$title){
    $content = '';
    $meta_value = get_post_meta($post_id, 'codex_creator_internal',true);
    if(!$meta_value){return;}
    $content .= WP_CODEX_TITLE_START.$title.WP_CODEX_TITLE_END;
    $content .= WP_CODEX_CONTENT_START.$meta_value.WP_CODEX_CONTENT_END;

    return apply_filters('codex_creator_internal_content',$content,$post_id,$title);
}

function codex_creator_ignore_content($post_id,$title){
    $content = '';
    $meta_value = get_post_meta($post_id, 'codex_creator_ignore',true);
    if(!$meta_value){return;}
    $content .= WP_CODEX_TITLE_START.$title.WP_CODEX_TITLE_END;
    $content .= WP_CODEX_CONTENT_START.$meta_value.WP_CODEX_CONTENT_END;

    return apply_filters('codex_creator_ignore_content',$content,$post_id,$title);
}

function codex_creator_link_content($post_id,$title){
    $content = '';
    $meta_value = get_post_meta($post_id, 'codex_creator_link',true);
    if(!$meta_value){return;}
    $content .= WP_CODEX_TITLE_START.$title.WP_CODEX_TITLE_END;
    $content .= WP_CODEX_CONTENT_START.$meta_value.WP_CODEX_CONTENT_END;

    return apply_filters('codex_creator_link_content',$content,$post_id,$title);
}

function codex_creator_method_content($post_id,$title){
    $content = '';
    $meta_value = get_post_meta($post_id, 'codex_creator_method',true);
    if(!$meta_value){return;}
    $content .= WP_CODEX_TITLE_START.$title.WP_CODEX_TITLE_END;
    $content .= WP_CODEX_CONTENT_START.$meta_value.WP_CODEX_CONTENT_END;

    return apply_filters('codex_creator_method_content',$content,$post_id,$title);
}

function codex_creator_package_content($post_id,$title){
    $content = '';
    $meta_value = get_post_meta($post_id, 'codex_creator_package',true);
    if(!$meta_value){return;}
    $content .= WP_CODEX_TITLE_START.$title.WP_CODEX_TITLE_END;
    $content .= WP_CODEX_CONTENT_START.$meta_value.WP_CODEX_CONTENT_END;

    return apply_filters('codex_creator_package_content',$content,$post_id,$title);
}

function codex_creator_param_content($post_id,$title){
    $content = '';
    $meta_value = get_post_meta($post_id, 'codex_creator_param',true);
    if(!$meta_value){return;}
    $content .= WP_CODEX_TITLE_START.$title.WP_CODEX_TITLE_END;
    if(is_array($meta_value)){
        foreach($meta_value as $value){
            $content .= WP_CODEX_CONTENT_START.$value.WP_CODEX_CONTENT_END;
        }
    }else{
        $content .= WP_CODEX_CONTENT_START.$meta_value.WP_CODEX_CONTENT_END;
    }

    return apply_filters('codex_creator_param_content',$content,$post_id,$title);
}

function codex_creator_example_content($post_id,$title){
    $content = '';
    $meta_value = get_post_meta($post_id, 'codex_creator_example',true);
    if(!$meta_value){return;}
    $content .= WP_CODEX_TITLE_START.$title.WP_CODEX_TITLE_END;
    $content .= WP_CODEX_CONTENT_START.$meta_value.WP_CODEX_CONTENT_END;

    return apply_filters('codex_creator_example_content',$content,$post_id,$title);
}

function codex_creator_return_content($post_id,$title){
    $content = '';
    $meta_value = get_post_meta($post_id, 'codex_creator_return',true);
    if(!$meta_value){return;}
    $content .= WP_CODEX_TITLE_START.$title.WP_CODEX_TITLE_END;
    $content .= WP_CODEX_CONTENT_START.$meta_value.WP_CODEX_CONTENT_END;

    return apply_filters('codex_creator_return_content',$content,$post_id,$title);
}

function codex_creator_see_content($post_id,$title){
    $content = '';
    $meta_value = get_post_meta($post_id, 'codex_creator_see',true);
    if(!$meta_value){return;}
    $content .= WP_CODEX_TITLE_START.$title.WP_CODEX_TITLE_END;
    $content .= WP_CODEX_CONTENT_START.$meta_value.WP_CODEX_CONTENT_END;

    return apply_filters('codex_creator_see_content',$content,$post_id,$title);
}

function codex_creator_since_content($post_id,$title){
    $content = '';
    $meta_value = get_post_meta($post_id, 'codex_creator_since',true);
    if(!$meta_value){return;}
    $content .= WP_CODEX_TITLE_START.$title.WP_CODEX_TITLE_END;
    $tags = array();
    $tags_set_arr = array();
    if(is_array($meta_value)){
        foreach($meta_value as $value){

            if($pieces = explode(" ", $value)){
                $ver = $pieces[0];
                unset($pieces[0]);
                $text = join(' ',$pieces);
                $content .= WP_CODEX_CONTENT_START.'<a href="%'.$ver.'%">'.$ver.'</a>'.' '.$text.WP_CODEX_CONTENT_END;
                $tags[]=$ver;
            }else{
                $content .= WP_CODEX_CONTENT_START.'<a href="%'.$value.'%">'.$value.'</a>'.WP_CODEX_CONTENT_END;
                $tags[]=$value;
            }



        }
    }else{
        $content .= WP_CODEX_CONTENT_START.'<a href="%'.$value.'%">'.$value.'</a>'.WP_CODEX_CONTENT_END;
        $tags[]=$meta_value;
    }

    // get the project slug
    $project_slug = 'project-not-found';
    $project_terms = wp_get_object_terms($post_id, 'codex_project');
    if (!is_wp_error( $project_terms) && !empty( $project_terms) && is_object( $project_terms[0])) {
        foreach( $project_terms as $p_term){
            if($p_term->parent=='0'){$project_slug = $p_term->slug;}
        }
    }


    //set all tags to have prefix of the project
    $alt_tags = array();
    foreach($tags as $temp_tag){
        $alt_tags[] = $project_slug.'_'.$temp_tag;
    }
    $tags = $alt_tags;


    $tags_arr = wp_set_post_terms( $post_id, $tags, 'codex_tags', false );

    //print_r($tags);exit;

    if(is_array($tags_arr)){
        foreach($tags_arr as $key=>$tag_id){
            $term = get_term( $tag_id, 'codex_tags');
            $tag_link = get_term_link( $term, 'codex_tags' );
            $orig_ver  = str_replace($project_slug.'_', '',$tags[$key]);
            $content = str_replace('%'.$orig_ver.'%', $tag_link, $content);
        }
    }

   // print_r($tags_arr);exit;

    return apply_filters('codex_creator_since_content',$content,$post_id,$title);
}

function codex_creator_subpackage_content($post_id,$title){
    $content = '';
    $meta_value = get_post_meta($post_id, 'codex_creator_subpackage',true);
    if(!$meta_value){return;}
    $content .= WP_CODEX_TITLE_START.$title.WP_CODEX_TITLE_END;
    $content .= WP_CODEX_CONTENT_START.$meta_value.WP_CODEX_CONTENT_END;

    return apply_filters('codex_creator_subpackage_content',$content,$post_id,$title);
}

function codex_creator_todo_content($post_id,$title){
    $content = '';
    $meta_value = get_post_meta($post_id, 'codex_creator_todo',true);
    if(!$meta_value){return;}
    $content .= WP_CODEX_TITLE_START.$title.WP_CODEX_TITLE_END;
    $content .= WP_CODEX_CONTENT_START.$meta_value.WP_CODEX_CONTENT_END;

    return apply_filters('codex_creator_todo_content',$content,$post_id,$title);
}

function codex_creator_type_content($post_id,$title){
    $content = '';
    $meta_value = get_post_meta($post_id, 'codex_creator_type',true);
    if(!$meta_value){return;}
    $content .= WP_CODEX_TITLE_START.$title.WP_CODEX_TITLE_END;
    $content .= WP_CODEX_CONTENT_START.$meta_value.WP_CODEX_CONTENT_END;

    return apply_filters('codex_creator_type_content',$content,$post_id,$title);
}

function codex_creator_uses_content($post_id,$title){
    $content = '';
    $meta_value = get_post_meta($post_id, 'codex_creator_uses',true);
    if(!$meta_value){return;}
    $content .= WP_CODEX_TITLE_START.$title.WP_CODEX_TITLE_END;
    $content .= WP_CODEX_CONTENT_START.$meta_value.WP_CODEX_CONTENT_END;

    return apply_filters('codex_creator_uses_content',$content,$post_id,$title);
}

function codex_creator_var_content($post_id,$title){
    $content = '';
    $meta_value = get_post_meta($post_id, 'codex_creator_var',true);
    if(!$meta_value){return;}
    $content .= WP_CODEX_TITLE_START.$title.WP_CODEX_TITLE_END;
    $content .= WP_CODEX_CONTENT_START.$meta_value.WP_CODEX_CONTENT_END;

    return apply_filters('codex_creator_var_content',$content,$post_id,$title);
}