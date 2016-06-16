/**
 * JavaScript for the backend
 *
 * @since 1.0.0
 * @package Codex_Creator
 */

/**
 * Javascript for step 1
 *
 * @todo make coming soon text translatable
 * @param cType
 * @since 1.0.0
 * @package Codex_Creator
 */
function cdxc_step_1(cType) {

    if (cType == 'theme') {
        alert('coming soon');
    } else if (cType == 'plugin' || cType == 'github') {
        jQuery('#cdxc_type_val').val(cType);
        cdxc_get_step_2(cType);
        cdxc_set_active_step(2);
    }else if(cType == 'bitbucket'){
        cdxc_bitbucket_maybe_auth();
    }

}

function cdxc_set_active_step(step) {
    // remove active class from all
    jQuery(".cc-step .codex-creator-step-content").slideUp("slow", function () {
        jQuery(".cc-step").removeClass("cc-active");
    });

    // add active class from all
    jQuery(".codex-creator-step-" + step + " .codex-creator-step-content").slideDown("slow", function () {
        jQuery(".codex-creator-step-" + step).addClass("cc-active");
        ;
    });

}


function cdxc_get_step_2(cType) {
    if (!cType) {
        return;
    }// bail if no type

    // This does the ajax request
    jQuery.ajax({
        url: ajaxurl,
        data: {
            'action': 'cdxc_get_type_list',
            'c_type': cType
        },
        success: function (data) {
            jQuery('.codex-creator-step-2 .codex-creator-step-content').html(data);
            //console.log(data);
        },
        error: function (errorThrown) {
            console.log(errorThrown);
        }
    });
}

function cdxc_step_3($type, $plugin, $name) {
    // alert($name);
        jQuery('#cdxc_project_name_val').val($name);
        jQuery('#cdxc_project_root_val').val($plugin);
        cdxc_scan_files($type, $plugin, $name);
        cdxc_set_active_step(3);

}


function cdxc_scan_files($type, $plugin, $name) {
    if (!$type) {
        return;
    }// bail if no type

    // This does the ajax request
    jQuery.ajax({
        url: ajaxurl,
        data: {
            'action': 'cdxc_scan',
            'c_type': $type,
            'c_path': $plugin,
            'c_name': $name
        },
        success: function (data) {
            jQuery('.codex-creator-step-3 .codex-creator-step-content').html(data);
            //console.log(data);
        },
        error: function (errorThrown) {
            console.log(errorThrown);
        }
    });
}


function cdxc_add_project($type, $el) {

    if (!$type || !$el) {
        return;
    }// bail if no type

    // This does the ajax request
    jQuery.ajax({
        url: ajaxurl,
        data: {
            'action': 'cdxc_add_project',
            'c_type': $type,
            'c_name': $el
        },
        success: function (data) {
            jQuery('.cc-add-update-project-bloc').remove();
            jQuery('.codex-creator-step-3 .codex-creator-step-content').prepend(data);
            // jQuery('.codex-creator-step-3 .codex-creator-step-content').html(data);
            console.log(data);
        },
        error: function (errorThrown) {
            console.log(errorThrown);
        }
    });

}


cc_curent_sync_file = '';
cc_first_file = true;
function cdxc_sync_project_files($type, $el, $last) {
    if (!$type || !$el) {
        return;
    }// bail if no type

    var total_files = jQuery(".cc-file-tree-file").length;
    jQuery(".cc-file-tree-file").each(function (index) {


        if (jQuery(this).data('sync') == 1) {
            return true;
        }

        cc_curent_sync_file = this;
        jQuery(cc_curent_sync_file).find("i.fa").first().removeClass('fa-file-code-o');
        jQuery(cc_curent_sync_file).find("i.fa").first().addClass('fa-cog fa-spin');


        //move screen to show progress
        cdxc_scroll_to(cc_curent_sync_file);


        file_loc = jQuery(this).data('cc-scan-file');

        // This does the ajax request
        jQuery.ajax({
            url: ajaxurl,
            data: {
                'action': 'cdxc_sync_file',
                'c_type': $type,
                'c_name': $el,
                'file_loc': file_loc,
                'first_file': cc_first_file
            },
            success: function (data) {
                jQuery(this).data('sync', 1);
                console.log(data);
                cc_first_file = false;
                jQuery(cc_curent_sync_file).css('background-color', 'lightcyan');
                jQuery(cc_curent_sync_file).find("i.fa").first().addClass('fa-file-code-o');
                jQuery(cc_curent_sync_file).find("i.fa").first().removeClass('fa-cog fa-spin');


                if (index === total_files - 1) {
                    // this is the last one, so now we sync
                    cdxc_calc_project_posts($type, $el);
                }else{
                    cdxc_sync_project_files($type, $el);
                }

            },
            error: function (errorThrown) {
                console.log(errorThrown);
            }
        });

        jQuery(this).data('sync', 1);
        return false;

    });

}



function cdxc_calc_project_posts($type, $el) {
    // This does the ajax request
    jQuery.ajax({
        url: ajaxurl,
        data: {
            'action': 'cdxc_calc_project_posts',
            'c_type': $type,
            'c_name': $el
        },
        success: function (data) {
            if (data) {
                cdxc_create_loading_bar_content(data);
                cdxc_create_codex_content($type, $el, data);
            }
        },
        error: function (errorThrown) {
            console.log(errorThrown);
        }
    });

}

$cc_progress_count = 0;
$cdxc_last_content_build = '';
function cdxc_create_codex_content($type, $el, $count, $post_id) {

    //add fail safe incase of looping.
    if($cdxc_last_content_build && $cdxc_last_content_build==$post_id){
        alert('Something has gone wrong while building the content for post#'+$cdxc_last_content_build);return;//@todo make the string translatable.
    }
    $cdxc_last_content_build = $post_id;
    // This does the ajax request
    jQuery.ajax({
        url: ajaxurl,
        data: {
            'action': 'cdxc_create_content_ajax',
            'c_type': $type,
            'c_name': $el,
            'count': $count,
            'post_id': $post_id
        },
        success: function (data) {
            $cc_progress_count++;
            //if a post id is returned then loop
            if (data) {
                console.log(data);
                cdxc_create_loading_bar_update($cc_progress_count);
                cdxc_create_codex_content($type, $el, $count, data);

            } else {
                alert('done');
            }


        },
        error: function (errorThrown) {
            console.log(errorThrown);
        }
    });

}

function cdxc_create_loading_bar_content($count) {

    // @todo make this string translatable
    loading_div = '<h4>Please wait while we cross reference everything and build the page content.</h4>' +
    '<div class="cc-loading-div" data-count="' + $count + '">' +
    '<div class="cc-loading-progress cc-loading-progress-striped cc-active"  style="width:0%">' +
    '0%' +
    '</div>' +
    '</div>';

    jQuery('.codex-creator-step-3 .codex-creator-step-content').html(loading_div);


}

function cdxc_create_loading_bar_update($num) {

    $count = jQuery('.cc-loading-div').data("count");


    $percent = $num / $count * 100;


    jQuery('.cc-loading-div .cc-loading-progress').html($percent.toFixed(2) + '%');
    jQuery('.cc-loading-div .cc-loading-progress').width($percent.toFixed(2) + '%');

    if ($num == $count) {
        jQuery('.cc-loading-div .cc-loading-progress').removeClass('cc-active');
    }


}


function cdxc_scroll_to($el) {
    jQuery('html, body').animate({
        scrollTop: jQuery($el).offset().top - 150
    }, 200);
}


function cdxc_get_github_repo(){

    $githubUrl = jQuery('#cdxc_github_url').val();

    if(!$githubUrl){return;} //sanity check

    jQuery.ajax({
        url: ajaxurl,
        data: {
            'action': 'cdxc_get_git_repo_ajax',
            'c_url': $githubUrl,
        },
        success: function (data) {
            if (data) {

                var obj = jQuery.parseJSON(data);
                console.log(obj);
                if(obj.error){
                    alert(obj.error);
                }else{
                    cdxc_step_3('github',obj.name,obj.name);
                }
               // cdxc_create_loading_bar_content(data);
                //cdxc_create_codex_content($type, $el, data);
               //
            }
        },
        error: function (errorThrown) {
            console.log(errorThrown);
        }
    });


}


function cdxc_toggle_project_cron($type, $name) {
    // This does the ajax request

    $set = jQuery('span[data-cc-cron-name="'+$name+'"]').data("cron-set");

    jQuery.ajax({
        url: ajaxurl,
        data: {
            'action': 'cdxc_toggle_project_cron',
            'c_type': $type,
            'c_name':  $name,
            'c_set':  $set
        },
        success: function (data) {
            console.log(data);
            if (data) {
                if(data==1){
                    jQuery('span[data-cc-cron-name="'+$name+'"] i').removeClass( 'fa-square-o' );
                    jQuery('span[data-cc-cron-name="'+$name+'"] i').addClass( 'fa-check-square-o' );
                    jQuery('span[data-cc-cron-name="'+$name+'"]').data("cron-set",1);
                }
                if(data==2){
                    jQuery('span[data-cc-cron-name="'+$name+'"] i').removeClass( 'fa-check-square-o' );
                    jQuery('span[data-cc-cron-name="'+$name+'"] i').addClass( 'fa-square-o' );
                    jQuery('span[data-cc-cron-name="'+$name+'"]').data("cron-set",0);
                }
            }
        },
        error: function (errorThrown) {
            console.log(errorThrown);
        }
    });

}

function cdxc_show_git_sync_info($type, $name) {
    // This does the ajax request


    jQuery.ajax({
        url: ajaxurl,
        data: {
            'action': 'cdxc_show_git_sync_info',
            'c_type': $type,
            'c_name':  $name
        },
        success: function (data) {
            console.log(data);
            if (data) {
                jQuery('#cdxc-ajax-info').html(data);
            }
        },
        error: function (errorThrown) {
            console.log(errorThrown);
        }
    });

}

function cdxc_bitbucket_maybe_auth(){
    jQuery.ajax({
        url: ajaxurl,
        data: {
            'action': 'cdxc_bitbucket_maybe_auth_ajax'
        },
        success: function (data) {
            console.log(data);
            if (data=='1') {
                jQuery('#cdxc_type_val').val('bitbucket');
                cdxc_get_step_2('bitbucket');
                cdxc_set_active_step(2);
                console.log('x1');
            }else{
                // we need to get auth
                cdxc_bitbucket_get_auth();console.log('x2');
            }
        },
        error: function (errorThrown) {
            console.log(errorThrown);
        }
    });
}

function cdxc_bitbucket_get_auth(){
    jQuery.ajax({
        url: ajaxurl,
        data: {
            'action': 'cdxc_bitbucket_get_auth_ajax'
        },
        success: function (data) {
            console.log(data);console.log('x3');
            if (data) {
                jQuery('#cdxc-step1-info').html(data);
            }
        },
        error: function (errorThrown) {
            console.log(errorThrown);
        }
    });
}

function cdxc_bitbucket_do_auth(){
    
    $key = jQuery('#cdxc-bitbucket-key').val();
    if(!$key){alert('please enter key');return;}

    $secret = jQuery('#cdxc-bitbucket-secret').val();
    if(!$key){alert('please enter secret');return;}

    jQuery.ajax({
        url: ajaxurl,
        data: {
            'action': 'cdxc_bitbucket_do_auth_ajax',
            'c_key': $key,
            'c_secret': $secret
        },
        success: function (data) {
            console.log(data);console.log('x4');
            if (data=='1') {
                window.location.replace("https://bitbucket.org/site/oauth2/authorize?client_id="+$key+"&response_type=code");
            }
        },
        error: function (errorThrown) {
            console.log(errorThrown);
        }
    });
}

function cdxc_get_bitbucket_repo($bitname,$biturl){

    if(!$bitname){$bitname = jQuery('#cdxc-bitbucket-repos').val();}
    if(!$biturl){$biturl = jQuery('#cdxc-bitbucket-repos option:selected').attr('data-biturl');}

    if(!$biturl){return;} //sanity check

    jQuery.ajax({
        url: ajaxurl,
        data: {
            'action': 'cdxc_get_bit_repo_ajax',
            'c_url': $biturl,
            'c_name': $bitname
        },
        success: function (data) {
            console.log(data);
            if (data) {

                var obj = jQuery.parseJSON(data);
                console.log(obj);
                if(obj.error){
                    alert(obj.error);
                }else{
                    cdxc_step_3('bitbucket',obj.path,obj.name);
                }
                // cdxc_create_loading_bar_content(data);
                //cdxc_create_codex_content($type, $el, data);
                //
            }
        },
        error: function (errorThrown) {
            console.log(errorThrown);
        }
    });


}