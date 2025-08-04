<?php
// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) exit;

class PWAFORWP_Utility{

	public function init() {

		add_action("wp_ajax_pwafowp_enable_modules_upgread", array($this, 'enable_modules') );
        }

        public function enable_modules(){
		// phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotValidated, WordPress.Security.ValidatedSanitizedInput.MissingUnslash
		if(!wp_verify_nonce( sanitize_text_field( $_REQUEST['verify_nonce'] ), 'verify_request' ) ) {

			echo wp_json_encode(array("status"=>300,"message"=>esc_html__("Request not valid",'pwa-for-wp')));
			exit();

	    }
	    // Exit if the user does not have proper permissions
	    if(! current_user_can( 'install_plugins' ) ) {
	        echo wp_json_encode(array("status"=>300,"message"=>esc_html__('User Request not valid','pwa-for-wp')));
	        exit();
	    }

	    $plugins = array();
	    $redirectSettingsUrl = '';
		// phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotValidated
	    $currentActivateModule = sanitize_text_field( wp_unslash($_REQUEST['activate']));


	    if(count($plugins)>0){
	       echo wp_json_encode( array( "status"=>200, "message"=>esc_html__("Module successfully Added",'pwa-for-wp'),'redirect_url'=>esc_url($redirectSettingsUrl) , "slug"=>$plugins[0]['name'], 'path'=> $plugins[0]['path'] ) );
	    }else{
	        echo wp_json_encode(array("status"=>300, "message"=>esc_html__("Modules not Found",'pwa-for-wp')));
	    }
	    wp_die();

        }
}

$PWA_UtilityObj = new PWAFORWP_Utility();
$PWA_UtilityObj->init();
