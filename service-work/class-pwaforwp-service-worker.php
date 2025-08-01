<?php  
if ( ! defined( 'ABSPATH' ) ) exit;

class PWAFORWP_Service_Worker {
	
        public $is_amp = false;       
        /**
         * Initialize whole front end system
         */
        public function __construct() {
            /* Check & initialize AMP is Activated or not*/
            $this->pwaforwp_is_amp_activated();
           
            /*Grab the settings of PWA*/
            $settings = pwaforwp_defaultSettings();
            $showPWA  = true;
            if( ( isset($settings['avoid_loggedin_users']) && !empty($settings['avoid_loggedin_users']) && $settings['avoid_loggedin_users']==1 && is_user_logged_in() ) ){
                $showPWA = false;
            }
            $showPWA = apply_filters("pwaforwp_show_pwa", $showPWA);
            if($showPWA){
                add_action( 'wp', array($this, 'pwaforwp_service_worker_init'), 1);
                if(isset($settings['custom_add_to_home_setting']) && isset($settings['normal_enable']) && $settings['normal_enable']==1){
                 add_action('wp_footer', array($this, 'pwaforwp_custom_add_to_home_screen'));   
                }        
                    
                if( isset( $settings['amp_enable'] ) && $settings['amp_enable'] == 1 ) {
                    add_action( 'pre_amp_render_post', array($this, 'pwaforwp_amp_entry_point' ) );
                    //Automattic AMP will be done here
                    add_action( 'wp', array($this, 'pwaforwp_automattic_amp_entry_point' ) );      
                    //pixelative Amp 
                    add_action( 'wp', array($this, 'pixelative_amp_entry_point' ) );      
                }  
                
                add_action( 'publish_post', array($this, 'pwaforwp_store_latest_post_ids'), 10, 2 );
                add_action( 'publish_page', array($this, 'pwaforwp_store_latest_post_ids'), 10, 2 );
                add_action( 'wp_ajax_pwaforwp_update_pre_caching_urls', array($this, 'pwaforwp_update_pre_caching_urls'));
                if ( isset( $settings['one_signal_support_setting'] ) && $settings['one_signal_support_setting'] == 1 ){
        		    add_action( 'init',  array($this,'pwaforwp_onesignal_rewrite' ));
                }
                if ( isset($settings['pushnami_support_setting'] ) && $settings['pushnami_support_setting'] == 1 ) {
                    add_action( 'init',  array($this,'pwaforwp_pushnami_rewrite' ));
                }
                
                /*
                * load manifest on using Rest API
                * This change for manifest
                */
                add_action( 'rest_api_init', array( $this, 'register_manifest_rest_route' ) );                                
                add_action( 'init', array($this, 'pwa_add_error_template_query_var') );
                add_action( 'parse_query', array($this, 'pwaforwp_load_service_worker') );
                
                      
                if ( $settings['default_caching']=='cacheFirst' && isset( $settings['change_default_on_login'] ) && $settings['change_default_on_login'] == 1 ) {
                    add_action( 'wp_login', array($this,'on_user_logged_in' ) );
                }
                /**
                 * Remove apple-touch-icon from theme side
                 */
                add_filter( "site_icon_meta_tags", array($this, 'site_icon_apple_touch_remove' ) );
                
            }

            if ( $GLOBALS['pagenow'] === 'wp-login.php' ) {
                add_action( 'init',  array($this,'load_scripts' ) );
            }
        }
        
        public function load_scripts( $hooks ) {
            $suffix = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';
            wp_enqueue_script( 'pwa-cache-clear-js', PWAFORWP_PLUGIN_URL . '/assets/js/clear-cache'.$suffix.'.js',array(),PWAFORWP_PLUGIN_VERSION, true );
        }

        public static function loadalernative_script_load_method() {
            add_action( 'wp_ajax_pwaforwp_sw_files', array('PWAFORWP_Service_Worker', 'pwaforwp_load_service_worker_ajax') );
            add_action( 'wp_ajax_nopriv_pwaforwp_sw_files', array('PWAFORWP_Service_Worker', 'pwaforwp_load_service_worker_ajax') );
        }
		
		public function pwaforwp_onesignal_rewrite() {

			add_rewrite_rule("onesignal_js/([0-9]{1,})?$", 'index.php?'.pwaforwp_query_var('sw_query_var').'=1&'.pwaforwp_query_var('sw_file_var').'='.'dynamic_onesignal'."&".pwaforwp_query_var('site_id_var').'=$matches[1]', 'top');
            add_rewrite_rule("onesignal_js/?$", 'index.php?'.pwaforwp_query_var('sw_query_var').'=1&'.pwaforwp_query_var('sw_file_var').'='.'dynamic_onesignal'."&".pwaforwp_query_var('site_id_var').'=normal', 'top');

		}

        public function pwaforwp_pushnami_rewrite() {
            add_rewrite_rule("pushnami_js/([0-9]{1,})?$", 'index.php?'.pwaforwp_query_var('sw_query_var').'=1&'.pwaforwp_query_var('sw_file_var').'='.'dynamic_pushnami'."&".pwaforwp_query_var('site_id_var').'=$matches[1]', 'top');
            add_rewrite_rule("pushnami_js/?$", 'index.php?'.pwaforwp_query_var('sw_query_var').'=1&'.pwaforwp_query_var('sw_file_var').'='.'dynamic_pushnami'."&".pwaforwp_query_var('site_id_var').'=normal', 'top');
        }

        /*
        * This function will work similar as "pwaforwp_load_service_worker" 
        * Only for Ajax time
        */
        public static function pwaforwp_load_service_worker_ajax() {
            // phpcs:ignore WordPress.Security.NonceVerification.Recommended -- we are not processing form here
            $returnFile = ( ( isset($_GET[pwaforwp_query_var('sw_query_var')]) && isset($_GET[pwaforwp_query_var('sw_file_var')]) ) || isset($_GET[pwaforwp_query_var('site_id_var')]) );
            if ( $returnFile ) {
                                
                @header( 'Cache-Control: no-cache' );
                @header( 'Content-Type: application/javascript; charset=utf-8' );
                // phpcs:ignore WordPress.Security.NonceVerification.Recommended, WordPress.Security.ValidatedSanitizedInput.MissingUnslash -- we are not processing form here
                $fileRawName = $filename =  sanitize_file_name($_GET[pwaforwp_query_var('sw_file_var')]);
                if($filename == 'dynamic_onesignal' || in_array($filename, array('OneSignalSDKWorker-'.get_current_blog_id().'.js.php', 'OneSignalSDKWorker-'.get_current_blog_id().'.js_.php')) ){//work with onesignal only
                    $filename = str_replace(".js_",".js", $filename);
                    if(file_exists(ABSPATH.$filename)){
                        require_once ABSPATH.$filename;
                    }

                    header("Service-Worker-Allowed: /");
                    header("Content-Type: application/javascript");
                    header("X-Robots-Tag: none");
                    exit;
                }elseif($filename == 'dynamic_pushnami'){//work with pushnami only
                    $home_url = pwaforwp_home_url();
                    // phpcs:ignore WordPress.Security.NonceVerification.Recommended, WordPress.Security.ValidatedSanitizedInput.MissingUnslash -- we are not processing form here
                    $site_id = sanitize_text_field( $_GET[ pwaforwp_query_var('site_id_var') ] );
                    if($site_id=='normal'){ $site_id = ''; }else{ $site_id = "-".$site_id; }

                    $pn_options = \WPPushnami::get_script_options();
                    $pn_api_key = $pn_options->api_key;

                    $url = ($home_url.'?'.pwaforwp_query_var('sw_query_var').'=1&'.pwaforwp_query_var('sw_file_var').'='.'pwa-sw'.$site_id.'-js');
                    $url = pwaforwp_service_workerUrls($url, 'pwa-sw'.$site_id.'-js');
                    header("Service-Worker-Allowed: /");
                    header("Content-Type: application/javascript");
                    header("X-Robots-Tag: none");
                    $content_escaped .= "importScripts('".esc_js($url)."')".PHP_EOL;
                    $content_escaped .= "importScripts('https://api.pushnami.com/scripts/v2/pushnami-sw/".esc_js($pn_api_key)."')".PHP_EOL;
                    $content_escaped = preg_replace('/\s+/', ' ', $content_escaped);
                    // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped	-- already escaped all contents
                    echo $content_escaped; 
                    exit;
                }
                if( strrpos($filename, '-js', -3) !== false ){
                    $filename = str_replace("-js", ".js", $filename);
                }if( strrpos($filename, '-html', -5) !== false ){
                    $filename = str_replace("-html", ".html", $filename);
                    @header( 'Content-Type: text/html; charset=utf-8' );
                }

                $filename = apply_filters('pwaforwp_file_creation_path', ABSPATH).$filename;
                $path_info = pathinfo($filename);
                if ( !isset($path_info['extension']) 
                    || (
                     (isset($path_info['extension']) && ($path_info['extension']!=='js' && $path_info['extension']!=='html')) 
                        )
                ) {
                    status_header( 304 );
                    return;
                }
                $file_data_escaped = '';
                if ( file_exists( $filename ) ) {
                    header("Service-Worker-Allowed: /");
                    header("X-Robots-Tag: none");
                    $file_data_escaped = pwaforwp_local_file_get_contents( $filename );
                } else {
                    $fileCreation = new PWAforwp_File_Creation();
                    if( strrpos($fileRawName, '-js', -3) !== false ){
                        $fileRawName = str_replace("-js", ".js", $fileRawName);
                    }if( strrpos($filename, '-html', -5) !== false ){
                        $fileRawName = str_replace("-html", ".html", $fileRawName);
                    }
                    switch ($fileRawName) {
                        case apply_filters('pwaforwp_sw_name_modify', "pwa-sw".pwaforwp_multisite_postfix().".js"):
                            header("Service-Worker-Allowed: /");
                            $file_data_escaped = $fileCreation->pwaforwp_swjs();
                            break;
                        case apply_filters('pwaforwp_sw_file_name', "pwa-register-sw".pwaforwp_multisite_postfix().".js"):
                            $file_data_escaped = $fileCreation->pwaforwp_swr();
                            break;
                        case apply_filters('pwaforwp_amp_sw_file_name',       "pwa-amp-sw".pwaforwp_multisite_postfix().".js"):
                            header("Service-Worker-Allowed: /");
                            $file_data_escaped = $fileCreation->pwaforwp_swjs(true);
                            break;
                        case apply_filters('pwaforwp_amp_sw_html_file_name',  "pwa-amp-sw".pwaforwp_multisite_postfix().".html"):
                            @header( 'Content-Type: text/html; charset=utf-8' );
                            $file_data_escaped = $fileCreation->pwaforwp_swhtml(true);
                            break;
                        
                        default:
                            # code...
                            break;
                    }
                }         
                // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped	-- all file data is already escaped       
                echo $file_data_escaped;

                exit;
            }
        }

        public function pwaforwp_load_service_worker( WP_Query $query ) {

            if ( $query->is_main_query() && $query->get( pwaforwp_query_var('sw_query_var') )) {
                
                @header( 'Cache-Control: no-cache' );
                @header( 'Content-Type: application/javascript; charset=utf-8' );
                $fileRawName = $filename = sanitize_file_name( $query->get( pwaforwp_query_var('sw_file_var') ) );
               if($filename == 'dynamic_onesignal' || in_array($filename, array('OneSignalSDKWorker-'.get_current_blog_id().'.js.php', 'OneSignalSDKWorker-'.get_current_blog_id().'.js_.php')) ){//work with onesignal only
                    $filename = str_replace(".js_",".js", $filename);
                    if(file_exists(ABSPATH.$filename)){
                        require_once ABSPATH.$filename;
                    }

                    header("Service-Worker-Allowed: /");
                    header("Content-Type: application/javascript");
                    header("X-Robots-Tag: none");
                    exit;
                }elseif($filename == 'dynamic_pushnami'){//work with pushnami only
                    $home_url = pwaforwp_home_url();
                    $site_id = sanitize_text_field( $query->get( pwaforwp_query_var('site_id_var') ) );
                    if($site_id=='normal'){ $site_id = ''; }else{ $site_id = "-".$site_id; }

                    $url = ($home_url.'?'.pwaforwp_query_var('sw_query_var').'=1&'.pwaforwp_query_var('sw_file_var').'='.'pwa-sw'.$site_id.'-js');
                    header("Service-Worker-Allowed: /");
                    header("Content-Type: application/javascript");
                    header("X-Robots-Tag: none");
                    $content_escaped .= "importScripts('".esc_js($url)."')".PHP_EOL;
                    $content_escaped .= "importScripts('https://api.pushnami.com/scripts/v2/pushnami-sw/".esc_js($pn_api_key)."')".PHP_EOL;
                    $content_escaped = preg_replace('/\s+/', ' ', $content_escaped);
                    // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped	-- already escaped all contents
                    echo $content_escaped;
                    exit;
                }
                if( strrpos($filename, '-js', -3) !== false ){
                    $filename = str_replace("-js", ".js", $filename);
                    header("Service-Worker-Allowed: /");
                    header("X-Robots-Tag: none");
                }if( strrpos($filename, '-html', -5) !== false ){
                    $filename = str_replace("-html", ".html", $filename);
                    @header( 'Content-Type: text/html; charset=utf-8' );
                }

                $filename = apply_filters('pwaforwp_file_creation_path', ABSPATH).$filename;
                $path_info = pathinfo($filename);
                if ( !isset($path_info['extension']) 
                    || (
                     (isset($path_info['extension']) && ($path_info['extension']!=='js' && $path_info['extension']!=='html')) 
                        )
                ) {
                    status_header( 304 );
                    return;
                }
                $file_data_escaped = '';
                if( file_exists($filename) ){
                    header("Service-Worker-Allowed: /");
                    header("X-Robots-Tag: none");
                    $file_data_escaped = pwaforwp_local_file_get_contents( $filename );
                }else{
                    $fileCreation = new PWAforwp_File_Creation();
                    if( strrpos($fileRawName, '-js', -3) !== false ){
                        $fileRawName = str_replace("-js", ".js", $fileRawName);
                    }if( strrpos($filename, '-html', -5) !== false ){
                        $fileRawName = str_replace("-html", ".html", $fileRawName);
                    }
                    switch ($fileRawName) {
                        case apply_filters('pwaforwp_sw_file_name', "pwa-sw".pwaforwp_multisite_postfix().".js"):
                            header("Service-Worker-Allowed: /");
                            $file_data_escaped = $fileCreation->pwaforwp_swjs();
                            break;
                        case apply_filters('pwaforwp_sw_file_name', "pwa-register-sw".pwaforwp_multisite_postfix().".js"):
                            $file_data_escaped = $fileCreation->pwaforwp_swr();
                            break;
                        case apply_filters('pwaforwp_amp_sw_file_name',       "pwa-amp-sw".pwaforwp_multisite_postfix().".js"):
                            header("Service-Worker-Allowed: /");
                            $file_data_escaped = $fileCreation->pwaforwp_swjs(true);
                            break;
                        case apply_filters('pwaforwp_amp_sw_html_file_name',  "pwa-amp-sw".pwaforwp_multisite_postfix().".html"):
                            @header( 'Content-Type: text/html; charset=utf-8' );
                            $file_data_escaped = $fileCreation->pwaforwp_swhtml(true);
                            break;
                        
                        default:
                            # code...
                            break;
                    }
                }
                // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped	-- all file data is already escaped
                echo $file_data_escaped;

                exit;
            }
        }

        public function pwa_add_error_template_query_var() {
            global $wp;
            $allQueryVar = pwaforwp_query_var();
            if(is_array($allQueryVar) && !empty($allQueryVar)){
                foreach ($allQueryVar as $key => $value) {
                    $wp->add_query_var( $value );
                }
            }
        }
        
        public function pwaforwp_service_worker_init(){
            
            $settings = pwaforwp_defaultSettings();
			if ( pwaforwp_is_enabled_pwa_wp() ) { return; }
            
            if(isset($settings['amp_enable']) && $settings['amp_enable']==1 && pwaforwp_amp_takeover_status()){
                add_action('wp_footer',array($this, 'pwaforwp_service_worker'));
                add_filter('amp_post_template_data',array($this, 'pwaforwp_service_worker_script'),35);
                add_action('wp_head',array($this, 'pwaforwp_paginated_post_add_homescreen_amp'),1);                
                add_action('wp_head',array($this, 'apple_icons_support'),99);                
                
            }else{
                
                if(isset($settings['normal_enable']) && $settings['normal_enable']==1){
                    $is_desplay = pwaforwp_visibility_check();
                    if($is_desplay==1){
                        add_action('wp_enqueue_scripts',array($this, 'pwaforwp_service_worker_non_amp'),35); 
                        add_action('wp_head',array($this, 'pwaforwp_paginated_post_add_homescreen'),1);  
                        add_action('wp_head',array($this, 'apple_icons_support'),99);  
                    }
                } 
               
            }
            $active_plugins = get_option( "active_plugins" );
		    $plugin_path = 'mediapress/mediapress.php';
		
		    if(isset($settings['share_target']) && $settings['share_target'] == 1 && in_array( $plugin_path, $active_plugins ) ) {
                wp_enqueue_script( 'pwa-share-target', PWAFORWP_PLUGIN_URL . 'assets/js/share_target.js', array('jquery'), PWAFORWP_PLUGIN_VERSION, true );
                $current_user = wp_get_current_user();
		        $user_login = $current_user->user_nicename;
                $object_name = array(
                    'user_path'                  => $user_login,
                    'ajax_url'                  => admin_url( 'admin-ajax.php' ),
                    'pwaforwp_security_nonce'   => wp_create_nonce('pwaforwp_ajax_check_nonce'),
                );
                wp_localize_script( 'pwa-share-target', 'pwa_share_target',  $object_name);
            }
        }
        public function pwaforwp_update_pre_caching_urls(){
            if ( ! current_user_can( pwaforwp_current_user_can() ) ) {
                return;
            }           
            if ( ! isset( $_GET['pwaforwp_security_nonce'] ) ){
                return; 
            }
            // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized, WordPress.Security.ValidatedSanitizedInput.MissingUnslash     
            if ( !wp_verify_nonce( $_GET['pwaforwp_security_nonce'], 'pwaforwp_ajax_check_nonce' ) ){
               return;  
            } 
            
            $file_creation_init_obj = new PWAFORWP_File_Creation_Init(); 
            $file_creation_init_obj->pwaforwp_swjs_init();
            $file_creation_init_obj->pwaforwp_swjs_init_amp();
            
            update_option('pwaforwp_update_pre_cache_list', 'disable'); 
            delete_transient( 'pwaforwp_pre_cache_post_ids' );
            echo wp_json_encode(array('status' => 't'));
            
            wp_die();   
        }
        public function pwaforwp_store_latest_post_ids(){
           
           if ( ! current_user_can( 'edit_posts' ) ) {
                 return;
           }
           
           $post_ids = array();           
           $settings = pwaforwp_defaultSettings();
           
           if(isset($settings['precaching_automatic']) && $settings['precaching_automatic']==1){
           
                $post_count = 10;
                
                if(isset($settings['precaching_post_count']) && $settings['precaching_post_count'] !=''){
                   $post_count =$settings['precaching_post_count']; 
                }                
                $post_args = array( 'numberposts' => $post_count, 'post_status'=> 'publish', 'post_type'=> 'post'  );                      
                $page_args = array( 'number'       => $post_count, 'post_status'=> 'publish', 'post_type'=> 'page' );
                                        
                if(isset($settings['precaching_automatic_post']) && $settings['precaching_automatic_post']==1){
                    $postslist = get_posts( $post_args );
                    if(is_array($postslist) && !empty($postslist)){
                        foreach ($postslist as $post){
                            $post_ids[] = $post->ID;
                        }
                    }
                }
                
                if(isset($settings['precaching_automatic_page']) && $settings['precaching_automatic_page']==1){
                    $pageslist = get_pages( $page_args );
                    if(is_array($pageslist) && !empty($pageslist)){
                        foreach ($pageslist as $post){
                         $post_ids[] = $post->ID;
                       }               
                    }         
                }   
                $previousIds = get_transient('pwaforwp_pre_cache_post_ids');
                if($post_ids){
                    if($previousIds){
                        $previousIds = json_decode($previousIds);
                        if(array_diff($post_ids, $previousIds)){
                            set_transient('pwaforwp_pre_cache_post_ids', wp_json_encode($post_ids));
                            update_option('pwaforwp_update_pre_cache_list', 'enable');
                            $file_creation_init_obj = new PWAFORWP_File_Creation_Init(); 
                            $file_creation_init_obj->pwaforwp_swjs_init();
                            $file_creation_init_obj->pwaforwp_swjs_init_amp();
                            update_option('pwaforwp_update_pre_cache_list', 'disable');
                        }
                    }else{
                        set_transient('pwaforwp_pre_cache_post_ids', wp_json_encode($post_ids));
                        $file_creation_init_obj = new PWAFORWP_File_Creation_Init(); 
                        $file_creation_init_obj->pwaforwp_swjs_init();
                        $file_creation_init_obj->pwaforwp_swjs_init_amp();
                    }
                }

                              
           }                                  
        }
        public function pwaforwp_custom_add_to_home_screen(){
            
            $settings        = pwaforwp_defaultSettings();
            $button_text     = esc_html__( 'Add', 'pwa-for-wp' );
            $banner_title    = esc_html__( 'Add', 'pwa-for-wp' ).' '.get_bloginfo().' '.esc_html__( 'to your Homescreen!', 'pwa-for-wp' );
                        
            if(isset($settings['custom_banner_title']) && $settings['custom_banner_title'] != ''){
                $banner_title = $settings['custom_banner_title'];
                $banner_title = preg_replace('/\\\\/', '', $banner_title);
            }
            
            if(isset($settings['custom_banner_button_text']) && $settings['custom_banner_button_text'] !=''){
                $button_text = $settings['custom_banner_button_text'];
            }
                                                
            if ((function_exists( 'ampforwp_is_amp_endpoint' ) && ampforwp_is_amp_endpoint()) || function_exists( 'is_amp_endpoint' ) && is_amp_endpoint()) {                  
            }else{                             
                    echo '<div id="pwaforwp-add-to-home-click" style="background-color:'.esc_attr($settings['custom_banner_background_color']).'" class="pwaforwp-footer-prompt pwaforwp-bounceInUp pwaforwp-animated"> <span id="pwaforwp-prompt-close" class="pwaforwp-prompt-close"></span>'
                       . '<h3 style="color:'.esc_attr($settings['custom_banner_title_color']).'">'. esc_html($banner_title).'</h3>'
                       . '<div style="background-color:'.esc_attr($settings['custom_banner_btn_color']).'; color:'.esc_attr($settings['custom_banner_btn_text_color']).'" class="pwaforwp-btn pwaforwp-btn-add-to-home">'.esc_html($button_text).'</div>'
                       . '</div>'; 
            }
            
        }
        public function pwaforwp_amp_entry_point(){  
            
            add_action('amp_post_template_footer',array($this, 'pwaforwp_service_worker'), 15);
            add_filter('amp_post_template_data',array($this, 'pwaforwp_service_worker_script'),35);
            add_action('amp_post_template_head',array($this, 'pwaforwp_paginated_post_add_homescreen_amp'),1); 
            add_action('amp_post_template_head',array($this, 'apple_icons_support'),99); 
            
        }
        public function pwaforwp_automattic_amp_entry_point(){  
            if ( pwaforwp_is_automattic_amp() ) {
                add_action('wp_footer',array($this, 'pwaforwp_service_worker'));
                add_filter('amp_post_template_data',array($this, 'pwaforwp_service_worker_script'),35);
                add_action('wp_head',array($this, 'pwaforwp_paginated_post_add_homescreen_amp'),1); 
                add_action('wp_head',array($this, 'apple_icons_support'),99); 
            }
            
        }
        public function pixelative_amp_entry_point(){  
            if ( function_exists('is_amp_endpoint') && is_amp_endpoint() && defined('AMP_WP_DIR_PATH') ) {
                add_action('amp_wp_template_footer',array($this, 'pwaforwp_service_worker'), 15);
                amp_wp_enqueue_script( 'amp-install-serviceworker', 'https://cdn.ampproject.org/v0/amp-install-serviceworker-0.1.js' );
                add_action('amp_wp_template_head',array($this, 'pwaforwp_paginated_post_add_homescreen_amp'),1); 
                add_action('amp_wp_template_head',array($this, 'apple_icons_support'),99); 
            }
            
        }	        
	    public function pwaforwp_service_worker(){ 
                            
                //$swjs_path_amp     = pwaforwp_site_url().'pwa-amp-sw'.pwaforwp_multisite_postfix().'.js';
                $swhtml            = pwaforwp_site_url().'pwa-amp-sw'.pwaforwp_multisite_postfix().'.html';
                $swhtml            = pwaforwp_service_workerUrls($swhtml, 'pwa-amp-sw'.pwaforwp_multisite_postfix().'.html');

                $url = pwaforwp_site_url();
                $home_url = pwaforwp_home_url();
                if( is_multisite() || trim($url)!==trim($home_url) || !pwaforwp_is_file_inroot()){
                    $filename = apply_filters('pwaforwp_amp_sw_name_modify', 'pwa-amp-sw'.pwaforwp_multisite_postfix().'.js');
                    $swjs_path_amp   = $home_url.'?'.pwaforwp_query_var('sw_query_var').'=1&'.pwaforwp_query_var('sw_file_var').'='.$filename;   

                    $swjs_path_amp = pwaforwp_service_workerUrls($swjs_path_amp, $filename);
                }else{
                    $swjs_path_amp     = pwaforwp_site_url().'pwa-amp-sw'.pwaforwp_multisite_postfix().'.js';
                }
            
                ?>
                        <amp-install-serviceworker data-scope="<?php echo esc_attr( trailingslashit( pwaforwp_home_url() ) ); ?>" 
                            src="<?php echo esc_url_raw($swjs_path_amp); ?>" 
                            data-iframe-src="<?php echo esc_url_raw($swhtml); ?>"  
                            layout="nodisplay">
			            </amp-install-serviceworker>
		    <?php
                
	}	

	public function pwaforwp_service_worker_script( $data ){
            
		if ( empty( $data['amp_component_scripts']['amp-install-serviceworker'] ) ) {
			$data['amp_component_scripts']['amp-install-serviceworker'] = 'https://cdn.ampproject.org/v0/amp-install-serviceworker-0.1.js';
		}
		return $data;
                
	}       	
	public function pwaforwp_service_worker_non_amp() {

        $url 			 = pwaforwp_site_url();	
		$settings 		 = pwaforwp_defaultSettings();
		$manualfileSetup         = $settings['manualfileSetup'];
                
		if ( $manualfileSetup ) {
            
            $filename = apply_filters('pwaforwp_sw_file_name', "pwa-register-sw".pwaforwp_multisite_postfix().".js");
            $url = $url.$filename;
            $url = pwaforwp_service_workerUrls($url, $filename);
             
            wp_register_script( "pwa-main-script", esc_url_raw($url), array(), PWAFORWP_PLUGIN_VERSION, true );
            wp_enqueue_script( "pwa-main-script");     
                           
		}  
                
	}                  
        public function pwaforwp_paginated_post_add_homescreen_amp() {  
            
		$url 			 = pwaforwp_site_url();	
		$settings 		 = pwaforwp_defaultSettings();
		$manualfileSetup         = $settings['manualfileSetup'];
		
		if ( $manualfileSetup ) {
                    		    
            echo '<link rel="manifest" href="'. esc_url( pwaforwp_manifest_json_url(true) ).'">'.PHP_EOL;
            if (isset($settings['icon']) && ! empty( $settings['icon'] ) ) : 
                echo '<link rel="apple-touch-icon-precomposed" sizes="192x192" href="'.esc_url($settings['icon']).'">'.PHP_EOL;
            endif;

		}

	}

	public function pwaforwp_paginated_post_add_homescreen() {    
            
		$url 			 = pwaforwp_site_url();	
		$settings 		 = pwaforwp_defaultSettings();
		$manualfileSetup         = $settings['manualfileSetup'];
		
		if($manualfileSetup){
            

            if(isset($settings['prefetch_manifest_setting']) && $settings['prefetch_manifest_setting']==1){
                echo '<link rel="prefetch" href="'. esc_url( pwaforwp_manifest_json_url() ).'">'.PHP_EOL;
            }
            $pro_extension_exists = false;

            
            $manifest_url = pwaforwp_add_manifest_variables(pwaforwp_manifest_json_url());
            
            if($pro_extension_exists && isset( $settings['start_page'] ) && $settings['start_page'] == 'active_url'){
                $manifest_url = pwaforwp_add_manifest_variables(pwaforwp_manifest_url( 'src' ));
            }
            echo '<link rel="manifest" href="'. esc_url($manifest_url).'">'.PHP_EOL;
            if (isset($settings['screenshots']) && ! empty( $settings['screenshots'] ) ) : 
                echo '<link rel="apple-touch-icon" sizes="512x512" href="'.esc_url($settings['screenshots']).'">'.PHP_EOL;
            endif;
            if (isset($settings['icon']) && ! empty( $settings['icon'] ) ) : 
                echo '<link rel="apple-touch-icon-precomposed" sizes="192x192" href="'.esc_url($settings['icon']).'">'.PHP_EOL;
            endif;
		}
                
	}

    public function apple_icons_support(){

        $settings        = pwaforwp_defaultSettings();

        echo '<meta name="pwaforwp" content="wordpress-plugin"/>
        <meta name="theme-color" content="'.esc_attr($settings['theme_color']).'">
        <meta name="apple-mobile-web-app-title" content="'.esc_attr($settings['app_blog_name']).'">
        <meta name="application-name" content="'.esc_attr($settings['app_blog_name']).'">
        <meta name="apple-mobile-web-app-capable" content="yes">
        <meta name="apple-mobile-web-app-status-bar-style" content="'.esc_attr(@$settings['ios_status_bar']).'">
        <meta name="mobile-web-app-capable" content="yes">
        <meta name="apple-touch-fullscreen" content="yes">'.PHP_EOL;
        
        $linktags_escaped = '';

        if ( isset( $settings['icon'] ) && ! empty( $settings['icon'] ) ) :             
            $linktags_escaped .= '<link rel="apple-touch-icon" sizes="192x192" href="' . esc_url( pwaforwp_https( $settings['icon'] ) ) . '">'.PHP_EOL;
        endif; 
                
        if ( isset( $settings['splash_icon'] ) && ! empty( $settings['splash_icon'] ) ) {
            $linktags_escaped .=  '<link rel="apple-touch-icon" sizes="512x512" href="' . esc_url( pwaforwp_https( $settings['splash_icon'] ) ) . '">'.PHP_EOL;
        }
        // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped	-- data is fully escaped
        echo apply_filters( 'pwaforwp_apple_touch_icons', $linktags_escaped );

        $this->iosSplashScreen();
    }
    public function pwaforwp_is_amp_activated() {    
		
        if ( function_exists( 'ampforwp_is_amp_endpoint' ) || function_exists( 'is_amp_endpoint' ) ) {
                $this->is_amp = true;
        }
		  
    }

    /**
     * Registers the rest route to get the manifest.
     */
    public function register_manifest_rest_route() {

        $rest_namepace = 'pwa-for-wp/v2';
        $route = 'pwa-manifest-json';

        register_rest_route(
            $rest_namepace,
            'pwa-manifest-json',
            array(
                'methods'             => 'GET',
                'callback'            => array( $this, 'get_manifest' ),
                'permission_callback' => array( $this, 'rest_permission' ),
            )
        );
        register_rest_route(
            $rest_namepace,
            $route.'/(?P<is_amp>[a-zA-Z0-9-]+)',
            array(
                'methods'             => 'GET',
                'callback'            => array( $this, 'get_manifest' ),
                'permission_callback' => array( $this, 'rest_permission' ),
            )
        );
    }   

    /**
     * Registers the rest route to get the manifest.
     *
     * Mainly copied from WP_REST_Posts_Controller::get_items_permissions_check().
     * This should ndt allow a request in the 'edit' context.
     *
     * @param WP_REST_Request $request Full details about the request.
     * @return true|WP_Error True if the request is allowed, WP_Error if the request is in the 'edit' context.
     */
    public function rest_permission( WP_REST_Request $request ) {

        if ( 'edit' === $request['context'] ) {
            return new WP_Error( 'rest_forbidden_context', __( 'Sorry, you are not allowed to edit the manifest.', 'pwa-for-wp' ), array( 'status' => rest_authorization_required_code() ) );
        }

        return true;
    }

    public function get_manifest( $request ) {

        $dataObj = new PWAforwp_File_Creation();

        if(isset($request['is_amp']) && $request['is_amp'] == 'amp' && defined('AMP_QUERY_VAR')){
            return json_decode($dataObj->pwaforwp_manifest(true),true);
        }else{
            return json_decode($dataObj->pwaforwp_manifest(),true);
        }

    }    
    /**
     * Sort icon sizes.
     *
     * Used as a callback in usort(), called from the manifest_link_and_meta() method.
     *
     * @param array $a The 1st icon item in our comparison.
     * @param array $b The 2nd icon item in our comparison.
     * @return int
     */
    public function sort_icons_callback( $a, $b ) {
        return (int) strtok( $a['sizes'], 'x' ) - (int) strtok( $b['sizes'], 'x' );
    }  

    /**
     * 
     * @return splash screen for header section
     */
    protected function iosSplashScreen() {

        $startup_images_escaped   = '';
        $settings        = pwaforwp_defaultSettings();
        
        if( isset( $settings['switch_apple_splash_screen'] ) && $settings['switch_apple_splash_screen'] ) {

            $otherData = pwaforwp_ios_splashscreen_files_data();

            if( is_array( $settings['ios_splash_icon'] ) && ! empty( $settings['ios_splash_icon'] ) ) {

            foreach ( $settings['ios_splash_icon'] as $key => $value ) {

                if ( ! empty( $value ) && ! empty( $key ) && isset( $otherData[ $key ] ) ) {

                    $screenData = $otherData[ $key ];
                    $startup_images_escaped .= '<link rel="apple-touch-startup-image" media="screen and (device-width: '.esc_attr( $screenData['device-width'] ).') and (device-height: '.esc_attr( $screenData['device-height'] ).') and (-webkit-device-pixel-ratio: '.esc_attr( $screenData['ratio'] ).') and (orientation: '.esc_attr( $screenData['orientation'] ).')" href="'.esc_url( $value ).'"/>'."\n";

                }//if closed
            }//foreach closed
        }
            // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped	-- data is already escaped
            echo apply_filters( "pwaforwp_apple_startup_images", $startup_images_escaped );

        }//if closed

    }//function iosSplashScreen closed

    protected function screenshotScreen() {

        $startup_images_escaped = '';
        $settings        = pwaforwp_defaultSettings();
        
        if ( isset( $settings['screenshots'] ) && $settings['screenshots'] ) {

                    $screenData = $settings['screenshots'];
                    $startup_images_escaped .= '<link rel="apple-touch-startup-image" href="'.esc_url( $screenData ).'"/>'."\n";
                    // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped	-- data is already escaped
                    echo apply_filters( "pwaforwp_apple_startup_images", $startup_images_escaped );

        }//if closed
    }//function iosSplashScreen closed

    public function on_user_logged_in(){

        $settings = pwaforwp_defaultSettings();

        if($settings['default_caching']=='cacheFirst' && isset($settings['change_default_on_login']) && $settings['change_default_on_login']==1){

            $settings['default_caching'] = 'networkFirst';
            global $pwaforwp_settings;
            update_option( 'pwaforwp_settings', $settings ) ;
            $pwaforwp_settings = $settings;
            pwaforwp_required_file_creation();
        }
    }
    /**
     * purpose to remove apple-touch-icon link from meta tag generated by customizer
     * @param  array {$meta_tags} array of meta tags  generated by core
     * @return array             array without apple-touch-icon meta tag
     */
    public function site_icon_apple_touch_remove( $meta_tags ) {

        if ( is_customize_preview() && is_admin() ) {

            return $meta_tags;

        }
        if ( is_array( $meta_tags ) && ! empty( $meta_tags ) ) {

        foreach ( $meta_tags as $key => $value ) {
                if ( strpos( $value, 'apple-touch-icon' ) !== false ) {

                    unset( $meta_tags[$key] );

                }
            }
        }

        return $meta_tags;

    }
                
}

if ( class_exists( 'PWAFORWP_Service_Worker' ) ) {

	$pwaServiceWorker = new PWAFORWP_Service_Worker;

    if ( wp_doing_ajax() ) {

        PWAFORWP_Service_Worker::loadalernative_script_load_method();

    }
  
};
