<?php
/**
 * Functions related to Bitbucket
 *
 * @since 1.0.0
 * @package Codex_Creator
 */

function cdxc_bitbucket_maybe_auth()
{

    $bitbucket = get_option('cdxc_bitbucket');

    if(isset($bitbucket['code']) && $bitbucket['code'] && isset($bitbucket['key']) && $bitbucket['key']){
        // looks like we have auth
        // @todo we can add a live api call to test
        return true;
    }

    return false;

}

function cdxc_bitbucket_maybe_auth_ajax()
{
    if(cdxc_bitbucket_maybe_auth()){
        echo '1';
    }else{
        echo '0';
    }
    die();
}

add_action('wp_ajax_cdxc_bitbucket_maybe_auth_ajax', 'cdxc_bitbucket_maybe_auth_ajax');

function cdxc_bitbucket_get_auth_ajax()
{
    ?>
    <h3><?php _e('We need to get authorisation', CDXC_TEXTDOMAIN); ?></h3>
<ol>
    <li><?php _e('Login to your Bitbucket account and go to > Bitbucket Settings > Access Management > oAuth > Add Consumer', CDXC_TEXTDOMAIN); ?></li>
    <li><?php _e('Enter any name (Codex Creator?), for the `Callback URL` enter:', CDXC_TEXTDOMAIN); ?>
    <input type="text" value="<?php echo admin_url('tools.php?page=codex-creator');?>" style="width:100%;background: #ccc" />
        <?php _e('For permissions the minimum needed is `Repositories:Read` and `Webhooks:Read and write`', CDXC_TEXTDOMAIN); ?>
    </li>
    <li><?php _e('Once you save you will then be given a Key and a Secret (maybe click on the name) please enter them below', CDXC_TEXTDOMAIN); ?>
        <input id="cdxc-bitbucket-key" type="text" value="" style="width:100%;background: #ccc" placeholder="<?php _e('Key', CDXC_TEXTDOMAIN); ?>" />
        <input id="cdxc-bitbucket-secret" type="text" value="" style="width:100%;background: #ccc" placeholder="<?php _e('Secret', CDXC_TEXTDOMAIN); ?>" />
        <span class="step-1-theme button button-primary" onclick="cdxc_bitbucket_do_auth();"><?php _e('Authorize', CDXC_TEXTDOMAIN); ?></span>

    </li>
</ol>
<?php
    die();
}

add_action('wp_ajax_cdxc_bitbucket_get_auth_ajax', 'cdxc_bitbucket_get_auth_ajax');


function cdxc_bitbucket_do_auth_ajax()
{

    $key = esc_attr($_REQUEST['c_key']);
    $secret = esc_attr($_REQUEST['c_secret']);

    if($key && $secret){
        //first lets save the info for later use
        $bitbucket = get_option('cdxc_bitbucket');
        $bitbucket['key'] = $key;
        $bitbucket['secret'] = $secret;
        update_option('cdxc_bitbucket',$bitbucket);
       echo '1';
    }else{
        echo '0';
    }


    die();
}

add_action('wp_ajax_cdxc_bitbucket_do_auth_ajax', 'cdxc_bitbucket_do_auth_ajax');

function cdxc_get_bitbuckets(){
    return get_option('cdxc_bitbucket_repos');
}


function cdxc_get_bitbuckets_api(){

    $at = cdxc_bitbucket_get_at();

    $bitbucket = get_option('cdxc_bitbucket');

    if(isset($bitbucket['code'])){
        // pagelen =100

        $url = "https://api.bitbucket.org/2.0/repositories?role=admin&pagelen=100";
        //$url = "https://bitbucket.org/GeoDirectory/geodir_location_manager/get/master.zip";
        $response = wp_remote_post( $url, array(
                'method' => 'GET',
                'timeout' => 15,
                'redirection' => 5,
                'httpversion' => '1.0',
                'blocking' => true,
                'headers' => array('Authorization' => "Bearer $at"),
                'body' => ''
            )
        );

       // print_r($response);

        if(isset($response['response']['code']) && $response['response']['code']=='200'){
            $r = json_decode($response['body']);

            return $r;

        }else{
            $r = json_decode($response['body']);
             _e('Something went wrong: ', CDXC_TEXTDOMAIN);
        }




    }





}

function cdxc_bitbucket_get_at(){


    $bitbucket = get_option('cdxc_bitbucket');


    if(isset($bitbucket['access_token_expire']) && $bitbucket['access_token_expire']>time()){
        return $bitbucket['access_token'];
    }



    if(isset($bitbucket['code'])){

        $auth = base64_encode( $bitbucket['key'] . ':' . $bitbucket['secret'] );

        $grant_type = (isset($bitbucket['refresh_token'])) ? 'refresh_token' : 'authorization_code';
        $body['grant_type'] = $grant_type;
        if($grant_type=='refresh_token'){
            $body['refresh_token'] = $bitbucket['refresh_token'];

        }else{
            $body['code'] = $bitbucket['code'];
        }
        $response = wp_remote_post( 'https://bitbucket.org/site/oauth2/access_token', array(
                'method' => 'POST',
                'timeout' => 15,
                'redirection' => 5,
                'httpversion' => '1.0',
                'blocking' => true,
                'headers' => array('Authorization' => "Basic $auth"),
                'body' => $body
            )
        );

        if(isset($response['response']['code']) && $response['response']['code']=='200'){
            $r = json_decode($response['body']);
            $bitbucket['refresh_token']= $r->refresh_token;
            $bitbucket['access_token']= $r->access_token;
            $bitbucket['access_token_expire']= (time()+$r->expires_in);
            update_option('cdxc_bitbucket',$bitbucket);
            return $bitbucket['access_token'];
        }

    }

    return false;
}

function cdxc_get_bitbuckets_select(){

    $repos = cdxc_get_bitbuckets_api();
    $html = '';
    if(is_object($repos) && !empty($repos->values)){

      // print_r($repos);

        $html .= "<select id='cdxc-bitbucket-repos'>";
        $html .=  "<option value=''>".__('Select Repo', CDXC_TEXTDOMAIN)."</option>";
        foreach($repos->values as $repo){

            $name = $repo->name;
            $url = $repo->links->downloads->href;
            $url = str_replace('https://api.bitbucket.org/2.0/repositories','https://bitbucket.org',$url);
            $url = str_replace('/downloads','',$url);

            $html .=  "<option value='$name' data-biturl='$url'>";
            //$html .=  "<option value='$url'>";
            $html .=  $name;
            $html .=  "</option>";
        }

        $html .=  "</select>";
    }

    return $html;

}


function cdxc_set_bitbucket($name,$url){

    if(empty($name)){return false;}
    $githubs = get_option('cdxc_bitbucket_repos');

    $githubs[$name] = array(
        'Name'  => $name,
        'url'   => $url
    );

    update_option('cdxc_bitbucket_repos',$githubs);

    return true;
}

/**
 * Ajax function to get a github repo.
 *
 * @since 1.0.1
 * @package Codex_Creator
 */
function cdxc_get_bit_repo_ajax($c_url='',$c_name='',$cron='')
{
    global $wpdb,$wp_filesystem;
    $wp_filesystem = cdxc_init_filesystem();
    if (!$wp_filesystem) {
        _e('Codex Creator can not access the filesystem.', CDXC_TEXTDOMAIN);
        exit;
    }


    if(!$c_url){$c_url = $_REQUEST['c_url'];}
    if(!$c_name){$c_name = $_REQUEST['c_name'];}

    $error = '';
    $info = array();
    $bit_url = cdxc_parse_bitbucket_url($c_url);
    //$github = cdxc_get_github_info($github_path);
    if(!$cron){cdxc_set_bitbucket($c_name,$bit_url);}// add the repo for easier usage next time
    if(!$bit_url){
        $error = __('Bitbucket URL not valid.', CDXC_TEXTDOMAIN);
    }else{
        $github_url = trailingslashit($bit_url)."get/master.zip";
        //$zip_file = download_url($github_url);
        $zip_file = '';
        $at = cdxc_bitbucket_get_at();

        $bitbucket = get_option('cdxc_bitbucket');

        if(isset($bitbucket['code'])){
            // pagelen =100

            $url = $github_url;
            $tmpfname = wp_tempnam($url);
            $response = wp_safe_remote_get( $url, array(
                    'method' => 'GET',
                    'timeout' => 15,
                    'redirection' => 5,
                    'httpversion' => '1.0',
                    'blocking' => true,
                    'headers' => array('Authorization' => "Bearer $at"),
                    'body' => '',
                    'filename' => $tmpfname,
                    'stream' => true
                )
            );


           // print_r($response);
            if ( is_wp_error( $response ) ) {
                $error =  $response->get_error_message();
            } else {
                if(isset($response['headers']['content-disposition'])){
                    $name_parts = explode('=',$response['headers']['content-disposition']);
                    $name_parts = explode('.zip',$name_parts[1]);
                    $folder_name = $name_parts[0];
                }


                $zip_file = $tmpfname;
            }


        }

        if (!$zip_file ) {
            $error = $response->get_error_message();
        }else{

            $destination = wp_upload_dir();
            $destination_path = $destination['basedir']. '/cdxc_temp/';

            //lets delete the folder first for housekeeping
            $wp_filesystem->rmdir($destination_path,true);

            /* Now we can use $plugin_path in all our Filesystem API method calls */
            if(!$wp_filesystem->is_dir($destination_path)) //@todo set chmod for non execute
            {
                /* directory didn't exist, so let's create it */
                $wp_filesystem->mkdir($destination_path);
            }

            $zip_path  = $destination_path.$c_name.".zip";
            //$project_path  = $destination_path.$folder_name;
            $project_path  = $folder_name;
            $move_file = $wp_filesystem->move( $zip_file, $zip_path );
            if(!$move_file){
                $error = __('Failed to move file', CDXC_TEXTDOMAIN);
                unlink($zip_file);
            }else{
                $unzip = unzip_file( $zip_path , $destination_path);
                if(!$unzip ){
                    $error = __('Failed to unzip on server', CDXC_TEXTDOMAIN);
                    unlink($zip_path);//delete zip
                }else{
                    $info['name'] = $c_name;
                    $info['full_name'] = $c_name;
                    $info['path'] = $project_path;
                    unlink($zip_file);
                }


            }

        }



    }

    if(!$error && $cron){
        return $info;
    }

    if($error){
        echo json_encode(array('error'=>$error));
    }else{
        echo json_encode($info );
    }

    //unlink($zip_file);
    die();
}

add_action('wp_ajax_cdxc_get_bit_repo_ajax', 'cdxc_get_bit_repo_ajax');

function cdxc_parse_bitbucket_url($url){
    if(empty($url)){return false;}

    if(filter_var($url, FILTER_VALIDATE_URL) && substr( $url, 0, 21 ) === "https://bitbucket.org")
    {
        return $url;
    }

    return false;

}

function cdxc_show_bitbucket_sync_info($name,$key){
    $github_sync_url = admin_url('admin-ajax.php');
    $github_sync_url = add_query_arg( 'action', 'cdxc_run_scan_action', $github_sync_url );
    $github_sync_url = add_query_arg( 'c_type', 'bitbucket', $github_sync_url );
    $github_sync_url = add_query_arg( 'c_name', $name, $github_sync_url );
    $github_sync_url = add_query_arg( 'c_key', $key, $github_sync_url );


    $output ='';
    $output .= "<ol>";

    $output .= "<li>";
    $output .=  __('Go to your Bitbucket project > Settings tab > Webhooks > Add Webhook', CDXC_TEXTDOMAIN);
    $output .= "</li>";

    $output .= "<li>";
    $output .=  __('For URL enter:', CDXC_TEXTDOMAIN);
    $output .=  "<input type='text' value='$github_sync_url' style='width: 100%;background: #ccc' />";
    $output .= "</li>";

    $output .= "<li>";
    $output .=  __('You can then select what triggers to fire on, i suggest the `Repository push` event only but this is your preference. Click add webhook.', CDXC_TEXTDOMAIN);
    $output .= "</li>";

    $output .= "</ol>";

    return $output;

}