<?php
/**
 * Cron functions for Codex Creator
 *
 * These functions help with silently running Codex Creator via a WP cron.
 *
 * @since 1.0.1
 * @package Codex_Creator
 */


function cdsx_run_cron(){
    //@todo make this run a wp cron job
}

function cdxc_set_cron_project($type,$name,$url=''){

    $cron = get_option('cdxc_cron_projects');

    $cron[$name] = array('name'=>$name,'type'=>$type,'url'=>$url);

    return update_option('cdxc_cron_projects',$cron);

}

function cdxc_get_cron_projects(){

    return get_option('cdxc_cron_projects');

}

function cdxc_unset_cron_project($type,$name,$url=''){

    $cron = get_option('cdxc_cron_projects');

    if(isset($cron[$name]) && $cron[$name]['type']==$type){
       unset($cron[$name]);
    }

    return update_option('cdxc_cron_projects',$cron);

}


function cdxc_toggle_project_cron()
{
    global $wpdb;
    $type = wp_strip_all_tags($_REQUEST['c_type'],true);
    $name = wp_strip_all_tags($_REQUEST['c_name'],true);
    $url = wp_strip_all_tags($_REQUEST['c_url'],true);
    $set =  intval($_REQUEST['c_set']);

    if($type=='github'){
        $githubs = get_option('cdxc_github_repos');
        if(isset($githubs[$name]) && isset($githubs[$name]['url']) && $githubs[$name]['url']){
            $url = $githubs[$name]['url'];
        }else{
            $set = 'error';
        }
    }

    if($set=='0'){
        cdxc_set_cron_project($type,$name,$url);
        echo '1';
    }elseif($set=='1'){
        cdxc_unset_cron_project($type,$name,$url);
        echo '2';
    }else{echo '0';}

    die();
}

add_action('wp_ajax_cdxc_toggle_project_cron', 'cdxc_toggle_project_cron');



function cdxc_scan_project_loop($c_type,$c_name,$first_run, $files, $path, $folder = '')
{

    foreach ($files as $file) {

        if ($file['type'] == 'd') {// directory

            //skip hidden directories and directories containing .ccignore or ccignore.txt
            if (substr($file['name'], 0, 1) === '.' || isset($file['files']['.ccignore']) || isset($file['files']['ccignore.txt'])) {
                continue;
            }

            if ($file['files']) {
                $folder_org = $folder;
                $folder .= $file['name'] . '/';
                cdxc_scan_project_loop($c_type,$c_name,$first_run,$file['files'], $path, $folder);
                $folder = $folder_org;
            }


        } elseif ($file['type'] == 'f') {// file

            // if file not an allowed file type skip it
            if (!cdxc_is_allowed_file($file['name'])) {
                continue;
            }

            $file_path = $path . "/" . $folder . $file['name'];

            cdxc_sync_file($c_type,$c_name,$file_path,$first_run);
            $first_run = false;



        }

    }

}




function cdxc_scan_project($type,$name,$key='')
{
    global $wp_filesystem;
    $wp_filesystem = cdxc_init_filesystem();
    if (!$wp_filesystem) {
        _e('Codex Creator can not access the filesystem.', CDXC_TEXTDOMAIN);
        exit;
    }

    $scan_path = '';
    if ($type == 'plugin') {
        $plugins = get_plugins();
       // print_r($plugins);
        foreach ($plugins as $path => $plugin) {
            if ($plugin['Name'] == $name) {

                if ($pieces = explode("/", $path)) {
                    $scan_path = $wp_filesystem->wp_plugins_dir() . $pieces[0];
                } else {
                    // single file
                    $scan_path = $wp_filesystem->wp_plugins_dir() . $path;
                }

            }
        }

        if (!$scan_path) {
            _e('Codex Creator can not find project.', CDXC_TEXTDOMAIN);
            exit;

        }


    } elseif ($type == 'github') {

        $githubs = get_option('cdxc_github_repos');
        if(isset($githubs[$name]) && isset($githubs[$name]['url']) && $githubs[$name]['url']){
            $url = $githubs[$name]['url'];

            if($key){
                if(isset($githubs[$name]['key']) && $githubs[$name]['key'] && $githubs[$name]['key']==$key){

                    $info = cdxc_get_git_repo_ajax( $url ,true);

                    if(isset( $info['path']) && $info['path']){
                        $scan_path = $info['path'];
                    }else{
                        _e('something went wrong.', CDXC_TEXTDOMAIN);
                        exit;
                    }



                }else{
                    _e('Invalid key provided.', CDXC_TEXTDOMAIN);
                    exit;
                }
            }


        }else{
            _e('Codex Creator can not find project.', CDXC_TEXTDOMAIN);
            exit;
        }

    } elseif ($type == 'bitbucket') {


        $githubs = get_option('cdxc_bitbucket_repos');
        if(isset($githubs[$name]) && isset($githubs[$name]['url']) && $githubs[$name]['url']){
            $url = $githubs[$name]['url'];

            if($key){
                if(isset($githubs[$name]['key']) && $githubs[$name]['key'] && $githubs[$name]['key']==$key){

                    $info = cdxc_get_bit_repo_ajax($url,$name,true);

                    if(isset( $info['path']) && $info['path']){

                        $destination = wp_upload_dir();
                        $destination_path = $destination['basedir']. '/cdxc_temp/'.$info['path'];
                        $scan_path = $destination_path;
                    }else{
                        _e('something went wrong.', CDXC_TEXTDOMAIN);
                        exit;
                    }



                }else{
                    _e('Invalid key provided.', CDXC_TEXTDOMAIN);
                    exit;
                }
            }


        }else{
            _e('Codex Creator can not find project.', CDXC_TEXTDOMAIN);
            exit;
        }

    } else {
        _e('Codex Creator can not find project.', CDXC_TEXTDOMAIN);
        exit;
    }



    $files = $wp_filesystem->dirlist($scan_path, true, true);
    //print_r($files);
    //echo '###' . $scan_path;

    $name = cdxc_get_project_by_name($name);

    $parent_id_arr = term_exists($name, 'codex_project');
    if (!isset($parent_id_arr['term_id'])) {
        cdxc_add_project($type,$name);// add parent project name if not exists
    }


    cdxc_scan_project_loop($type,$name,true,$files, $scan_path,'');

    cdxc_sync_project_files($name);

    if($type=='github'){
        $destination = wp_upload_dir();
        $destination_path = $destination['basedir']. '/cdxc_temp/';
        //lets delete the folder first for housekeeping
        $wp_filesystem->rmdir($destination_path,true);
    }

}

function cdxc_sync_project_files($project){

    global $wpdb;

    //$project = cdxc_get_project_by_name($project);


    $term_id = $wpdb->get_var($wpdb->prepare("SELECT term_id FROM $wpdb->terms WHERE name=%s", $project));

    if (!$term_id) {
        $term_id = $wpdb->get_var($wpdb->prepare("SELECT term_id FROM $wpdb->terms WHERE slug=%s", $project));
    }

    if (!$term_id) {
        return false;
    }
    $posts = $wpdb->get_results($wpdb->prepare("SELECT p.ID FROM $wpdb->posts p JOIN $wpdb->term_relationships tr on p.ID=tr.object_id Join $wpdb->term_taxonomy tt ON tt.term_taxonomy_id=tr.term_taxonomy_id WHERE p.post_type='codex_creator' AND tt.term_id=%d ORDER BY p.ID ASC", $term_id));

    if(!empty($posts)){
        foreach($posts as $p){
            cdxc_codex_create_content($p->ID);
        }
    }

    return true;

}


function cdxc_run_scan_action($type='',$name='',$key='')
{

    //print_r(wp_upload_dir());exit;
    if(!$type && isset($_REQUEST['c_type'])){$type = $_REQUEST['c_type'];}
    if(!$name && isset($_REQUEST['c_name'])){$name = $_REQUEST['c_name'];}
    if(!$key && isset($_REQUEST['c_key'])){$key= $_REQUEST['c_key'];}

    if($type && $name && $key){
        if(!$key){echo '0';}else{
            cdxc_scan_project($type,$name,$key);
            echo '1';
        }
    }else{
        echo '0';
    }



    die();
}

add_action('wp_ajax_cdxc_run_scan_action', 'cdxc_run_scan_action');
add_action('wp_ajax_nopriv_cdxc_run_scan_action', 'cdxc_run_scan_action');


function cdxc_show_git_sync_info()
{
    global $wpdb;
    $type = wp_strip_all_tags($_REQUEST['c_type'],true);
    $name = wp_strip_all_tags($_REQUEST['c_name'],true);

    $key ='';
    if($type=='github'){
        $githubs = get_option('cdxc_github_repos');
        if(isset($githubs[$name]) && isset($githubs[$name]['key']) && $githubs[$name]['key']){
            $key = $githubs[$name]['key'];

        }elseif(isset($githubs[$name])){
            $key = cdxc_generate_key();
            $githubs[$name]['key'] = $key;
            update_option('cdxc_github_repos',$githubs);
        }
    }
    elseif($type=='bitbucket'){
        $githubs = get_option('cdxc_bitbucket_repos');
        if(isset($githubs[$name]) && isset($githubs[$name]['key']) && $githubs[$name]['key']){
            $key = $githubs[$name]['key'];

        }elseif(isset($githubs[$name])){
            $key = cdxc_generate_key();
            $githubs[$name]['key'] = $key;
            update_option('cdxc_bitbucket_repos',$githubs);
        }
    }

    if($key){
        if($type=='github'){
            echo cdxc_show_github_sync_info($name,$key);
        }else{
            echo cdxc_show_bitbucket_sync_info($name,$key);
        }

        
    }else{
        _e('Something went wrong, could not find project.', CDXC_TEXTDOMAIN);
    }



    die();
}

add_action('wp_ajax_cdxc_show_git_sync_info', 'cdxc_show_git_sync_info');


function cdxc_show_github_sync_info($name,$key){
    $github_sync_url = admin_url('admin-ajax.php');
    $github_sync_url = add_query_arg( 'action', 'cdxc_run_scan_action', $github_sync_url );
    $github_sync_url = add_query_arg( 'c_type', 'github', $github_sync_url );
    $github_sync_url = add_query_arg( 'c_name', $name, $github_sync_url );
    $github_sync_url = add_query_arg( 'c_key', $key, $github_sync_url );


    $output ='';
    $output .= "<ol>";

    $output .= "<li>";
    $output .=  __('Go to your GitHub project > Settings tab > Webhooks &amp; Services > Add Webhook', CDXC_TEXTDOMAIN);
    $output .= "</li>";

    $output .= "<li>";
    $output .=  __('For Payload URL enter:', CDXC_TEXTDOMAIN);
    $output .=  "<input type='text' value='$github_sync_url' style='width: 100%;background: #ccc' />";
    $output .= "</li>";

    $output .= "<li>";
    $output .=  __('You can then select what webhook to fire on, i suggest the `Release` event only but this is your preference. Click add webhook.', CDXC_TEXTDOMAIN);
    $output .= "</li>";

    $output .= "</ol>";

    return $output;

}





