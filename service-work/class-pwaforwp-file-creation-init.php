<?php 
if ( ! defined( 'ABSPATH' ) ) exit;
/**
 * Class PWAFORWP_File_Creation_Init
 */
class PWAFORWP_File_Creation_Init {
    
    public $wppath;
    public $fileCreation;
    public $swjs_init;
    public $minifest_init;
    public $swr_init;
    public $swjs_init_amp;
    public $minifest_init_amp;
    public $swhtml_init_amp;  
             
    public function __construct(){
        
        $this->wppath                 = str_replace("//","/",str_replace("\\","/",realpath(ABSPATH))."/"); 
        $this->wppath                 = apply_filters("pwaforwp_file_creation_path", $this->wppath);
        $this->fileCreation           = new PWAforwp_File_Creation();
        $this->swjs_init              = $this->wppath.apply_filters('pwaforwp_sw_name_modify',           "pwa-sw".pwaforwp_multisite_postfix().".js");
        $this->minifest_init          = $this->wppath.apply_filters('pwaforwp_manifest_file_name',     "pwa-manifest".pwaforwp_multisite_postfix().".json");
        $this->swr_init               = $this->wppath.apply_filters('pwaforwp_sw_file_name',           "pwa-register-sw".pwaforwp_multisite_postfix().".js");
        $this->swjs_init_amp          = $this->wppath.apply_filters('pwaforwp_amp_sw_file_name',       "pwa-amp-sw".pwaforwp_multisite_postfix().".js");
        $this->minifest_init_amp      = $this->wppath.apply_filters('pwaforwp_amp_manifest_file_name', "pwa-amp-manifest".pwaforwp_multisite_postfix().".json");
        $this->swhtml_init_amp        = $this->wppath.apply_filters('pwaforwp_amp_sw_html_file_name',  "pwa-amp-sw".pwaforwp_multisite_postfix().".html");
    }
    
        
    public function pwaforwp_swjs_init($action = null){
        
        $swjsContent = $this->fileCreation->pwaforwp_swjs();
        return pwaforwp_write_a_file($this->swjs_init, $swjsContent, $action);
                                
    }
    
    public function pwaforwp_manifest_init($action = null){
        
        $swHtmlContent  = $this->fileCreation->pwaforwp_manifest();
        $swHtmlContent  = str_replace("&#038;", '&', $swHtmlContent);
        $settings = pwaforwp_defaultSettings();
        $pro_extension_exists = function_exists('pwaforwp_is_any_extension_active')?pwaforwp_is_any_extension_active():false;
        if (!$pro_extension_exists) {
            return pwaforwp_write_a_file($this->minifest_init, $swHtmlContent, $action);
        }else{
            if(isset( $settings['start_page'] ) && $settings['start_page'] != 'active_url'){
                return pwaforwp_write_a_file($this->minifest_init, $swHtmlContent, $action);
            }
        }
    }
    
    public function pwaforwp_swr_init($action = null){   
        
        $swjsContent    = $this->fileCreation->pwaforwp_swr();
        return pwaforwp_write_a_file($this->swr_init, $swjsContent, $action);
                       
    }
    
    public function pwaforwp_swjs_init_amp($action = null){  
        
        $swjsContent    = $this->fileCreation->pwaforwp_swjs(true);
        return pwaforwp_write_a_file($this->swjs_init_amp, $swjsContent, $action);
        
     }
     public function pwaforwp_manifest_init_amp($action = null){
         
         $swHtmlContent = $this->fileCreation->pwaforwp_manifest(true);
         return pwaforwp_write_a_file($this->minifest_init_amp, $swHtmlContent, $action);
        
    }    
    public function pwaforwp_swhtml_init_amp($action = null){  
        
        $swHtmlContent = $this->fileCreation->pwaforwp_swhtml(true);
        return pwaforwp_write_a_file($this->swhtml_init_amp, $swHtmlContent, $action);
                 
    }
}

add_action('wp_ajax_pwaforwp_download_setup_files', 'pwaforwp_download_setup_files');

function pwaforwp_download_setup_files(){
    if ( ! current_user_can( pwaforwp_current_user_can() ) ) {
		return;
	}
    
    if ( ! isset( $_GET['pwaforwp_security_nonce'] ) ){
        return; 
    }
    // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.MissingUnslash, WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
    if ( !wp_verify_nonce( $_GET['pwaforwp_security_nonce'], 'pwaforwp_ajax_check_nonce' ) ){
        return;  
    } 
    // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotValidated, WordPress.Security.ValidatedSanitizedInput.MissingUnslash
    $file_type = sanitize_text_field($_GET['filetype']);    
    $file_creation_init_obj = new PWAFORWP_File_Creation_Init(); 
    $result = '';  
    
    switch($file_type){
        case 'pwa-sw':                
            $result = $file_creation_init_obj->pwaforwp_swjs_init(); 
            $result = $file_creation_init_obj->pwaforwp_swr_init(); 
            break;
        case 'pwa-manifest':                
            $result = $file_creation_init_obj->pwaforwp_manifest_init();  
            break;
        case 'pwa-amp-sw':                
           $result = $file_creation_init_obj->pwaforwp_swjs_init_amp();
           $result = $file_creation_init_obj->pwaforwp_swhtml_init_amp();
            break;
        case 'pwa-amp-manifest':               
           $result = $file_creation_init_obj->pwaforwp_manifest_init_amp();
            break;   
        default:
            //code
            break;
    }            
    if($result){
      echo wp_json_encode(array('status'=>'t', 'message'=>esc_html__( 'File has been created', 'pwa-for-wp' )));  
    }else{
      echo wp_json_encode(array('status'=>'f', 'message'=>esc_html__( 'Check permission or download from manual', 'pwa-for-wp' )));  
    }
    wp_die();           
}