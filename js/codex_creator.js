/**
 * Javascript for step 1
 *
 * @todo make coming soon text translatable
 * @param cType
 * @since 1.0.0
 * @package Codex Creator
 */
function codex_creator_step_1(cType) {

    if (cType == 'theme') {
        alert('coming soon');
    } else if (cType == 'plugin') {
        jQuery('#codex_creator_type_val').val(cType);
        codex_creator_get_step_2('plugin');
        codex_creator_set_active_step(2);
    }

}

function codex_creator_set_active_step(step) {
    // remove active class from all
    jQuery( ".cc-step .codex-creator-step-content" ).slideUp( "slow", function() {
        jQuery( ".cc-step" ).removeClass( "cc-active" );
    });

    // add active class from all
    jQuery( ".codex-creator-step-"+step+" .codex-creator-step-content" ).slideDown( "slow", function() {
        jQuery( ".codex-creator-step-"+step ).addClass( "cc-active" );;
    });

}


function codex_creator_get_step_2(cType) {
    if(!cType){return;}// bail if no type

    // This does the ajax request
    jQuery.ajax({
        url: ajaxurl,
        data: {
            'action':'codex_creator_get_type_list',
            'c_type' : cType
        },
        success:function(data) {
            jQuery('.codex-creator-step-2 .codex-creator-step-content').html(data);
            console.log(data);
        },
        error: function(errorThrown){
            console.log(errorThrown);
        }
    });
}

function codex_creator_step_3($type,$plugin,$name) {
       // alert($name);
    jQuery('#codex_creator_project_name_val').val($name);
    jQuery('#codex_creator_project_root_val').val($plugin);
    codex_creator_scan_files($type,$plugin,$name)
    codex_creator_set_active_step(3);
}


function codex_creator_scan_files($type,$plugin,$name) {
    if(!$type){return;}// bail if no type

    // This does the ajax request
    jQuery.ajax({
        url: ajaxurl,
        data: {
            'action':'codex_creator_scan',
            'c_type' : $type,
            'c_path' : $plugin,
            'c_name' : $name
        },
        success:function(data) {
            jQuery('.codex-creator-step-3 .codex-creator-step-content').html(data);
            console.log(data);
        },
        error: function(errorThrown){
            console.log(errorThrown);
        }
    });
}


function codex_creator_add_project($type,$el) {

    if(!$type || !$el){return;}// bail if no type

    // This does the ajax request
    jQuery.ajax({
        url: ajaxurl,
        data: {
            'action':'codex_creator_add_project',
            'c_type' : $type,
            'c_name' : $el
        },
        success:function(data) {
            jQuery('.cc-add-update-project-bloc').remove();
            jQuery('.codex-creator-step-3 .codex-creator-step-content').prepend(data);
           // jQuery('.codex-creator-step-3 .codex-creator-step-content').html(data);
            console.log(data);
        },
        error: function(errorThrown){
            console.log(errorThrown);
        }
    });

}


cc_curent_sync_file = '';
function codex_creator_sync_project_files($type,$el,$last) {
    if(!$type || !$el){return;}// bail if no type

    var total_files =  jQuery( ".cc-file-tree-file" ).length;
    jQuery( ".cc-file-tree-file" ).each(function(index) {


        if(jQuery(this).data('sync')==1){return true;}

        cc_curent_sync_file = this;


        file_loc = jQuery(this).data('cc-scan-file');

        // This does the ajax request
        jQuery.ajax({
            url: ajaxurl,
            data: {
                'action':'codex_creator_sync_file',
                'c_type' : $type,
                'c_name' : $el,
                'file_loc' : file_loc
            },
            success:function(data) {
                jQuery(this).data('sync',1);
                console.log(data);

                jQuery( cc_curent_sync_file).css('background-color', 'red');
                console.log('#'+index+'@'+total_files);
                if (index === total_files - 1) {
                    // this is the last one
                    codex_creator_sync_project_functions($type,$el,cc_curent_sync_file,1);
                }else {
                    codex_creator_sync_project_functions($type,$el,cc_curent_sync_file,0);
                }

            },
            error: function(errorThrown){
                console.log(errorThrown);
            }
        });

        jQuery(this).data('sync',1);
        return false;

    });

}

cc_curent_sync_function = '';
function codex_creator_sync_project_functions($type,$el,$file,$last) {
    if(!$type || !$el){return;}// bail if no type

    funcsP = jQuery( $file ).next();


    if(funcsP.attr("class")=='cc-function-tree'){// if the file has functions
        var total_func =  jQuery( funcsP).children(".cc-file-tree-function").length;
        jQuery( funcsP).children(".cc-file-tree-function").each(function(index) {

            jQuery( this ).css('background-color', 'red');



            if(jQuery(this).data('sync')==1){return true;}

            cc_curent_sync_function = this;

            file_loc = jQuery(this).data('cc-scan-file');
            function_name = jQuery(this).data('cc-scan-function');

            // This does the ajax request
            jQuery.ajax({
                url: ajaxurl,
                data: {
                    'action':'codex_creator_sync_function',
                    'c_type' : $type,
                    'c_name' : $el,
                    'file_loc' : file_loc,
                    'function_name' : function_name
                },
                success:function(data) {
                    jQuery(this).data('sync',1);
                    console.log(data);

                    jQuery( cc_curent_sync_function ).css('background-color', 'red');

                    if (index === total_func - 1) {
                        // this is the last one

                        if($last){
                            //alert('done0');
                            codex_creator_calc_project_posts($type,$el);

                        }else {
                            codex_creator_sync_project_files($type,$el);
                        }

                    }else {
                        codex_creator_sync_project_functions($type, $el, $file,$last);
                    }
                },
                error: function(errorThrown){
                    console.log(errorThrown);
                }
            });

            jQuery(this).data('sync',1);
            return false;





        });


    }else{// else continue to next file;
        if($last){
            alert('done1');

        }else {
            codex_creator_sync_project_files($type, $el);
        }
    }






}

function codex_creator_calc_project_posts($type,$el){
    // This does the ajax request
    jQuery.ajax({
        url: ajaxurl,
        data: {
            'action':'codex_creator_calc_project_posts',
            'c_type' : $type,
            'c_name' : $el
        },
        success:function(data) {
            if(data){codex_creator_create_codex_content($type,$el,data);}
        },
        error: function(errorThrown){
            console.log(errorThrown);
        }
    });

}


function codex_creator_create_codex_content($type,$el,$count){

    // This does the ajax request
    jQuery.ajax({
        url: ajaxurl,
        data: {
            'action':'codex_creator_create_content',
            'c_type' : $type,
            'c_name' : $el,
            'count'  : $count
        },
        success:function(data) {
            alert('done'+data);
            console.log(data);


        },
        error: function(errorThrown){
            console.log(errorThrown);
        }
    });

}

