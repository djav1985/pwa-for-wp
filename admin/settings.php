<?php
if (! defined('ABSPATH') ) { exit;
}


function pwaforpw_add_menu_links()
{

    // Main menu page
    $pwa_title = apply_filters('pwaforwp_whitelabel_title', __('PWA', 'pwa-for-wp'));
    $pwa_logo = apply_filters('pwaforwp_whitelabel_logo', PWAFORWP_PLUGIN_URL.'images/menu-icon.svg');
        add_menu_page(
            __('Progressive Web Apps For WP', 'pwa-for-wp'),
            esc_html($pwa_title),
            pwaforwp_current_user_can(),                
            'pwaforwp',
            'pwaforwp_admin_interface_render',
            PWAFORWP_PLUGIN_URL.'images/menu-icon.svg', 100 
        );
    // Settings page - Same as main menu page
    add_submenu_page(
        'pwaforwp',
        esc_html__('Progressive Web Apps For WP', 'pwa-for-wp'),
        esc_html__('Settings', 'pwa-for-wp'),
        pwaforwp_current_user_can(),                
        'pwaforwp',
        'pwaforwp_admin_interface_render'
    );    
                                
}
add_action('admin_menu', 'pwaforpw_add_menu_links');
add_action('admin_head', 'pwaforwp_add_menu_styles');

/**
 * Add styles for the menu img
 */
function pwaforwp_add_menu_styles()
{
    ?>
    <style>
        #toplevel_page_pwaforwp .wp-menu-image img {
            padding : 15% 0 0;
        }
    </style>
    <?php
}

function pwaforwp_admin_interface_render()
{
    if (! current_user_can(pwaforwp_current_user_can()) ) {
        return;
    }            
    // Handing save settings
	// phpcs:ignore WordPress.Security.NonceVerification.Recommended -- we are not processing form here
    if (isset($_GET['settings-updated']) ) {    
                                                                                    
        $service_worker = new PWAFORWP_Service_Worker();
        $service_worker->pwaforwp_store_latest_post_ids();
        update_option('pwaforwp_update_pre_cache_list', 'disable');
        pwaforwp_required_file_creation();                                 
        settings_errors();
    }
        $tab = pwaforwp_get_tab('dashboard', array('dashboard','general', 'features','push_notification', 'other_setting', 'precaching_setting', 'tools', 'help'));
                                                                        
    ?>
    <div class="wrap pwaforwp-wrap">            
    <?php
    $pwa_longtext = apply_filters('pwaforwp_whitelabel_longtext', __('Progressive Web Apps For WP', 'pwa-for-wp'));
    ?>
    <h1><?php echo esc_html($pwa_longtext, 'pwa-for-wp'); ?></h1>

            <div class="pwaforwp-main-wrapper">
                <h2 class="nav-tab-wrapper pwaforwp-tabs">
                    <?php
                    echo '<a href="' . esc_url(pwaforwp_admin_link('dashboard')) . '" class="nav-tab ' . esc_attr($tab == 'dashboard' ? 'nav-tab-active' : '') . '"><span class="dashicons dashicons-dashboard"></span> ' . esc_html__('Dashboard', 'pwa-for-wp') . '</a>';

                    echo '<a href="' . esc_url(pwaforwp_admin_link('general')) . '" class="nav-tab ' . esc_attr($tab == 'general' ? 'nav-tab-active' : '') . '"><span class="dashicons dashicons-welcome-view-site"></span> ' . esc_html__('Setup', 'pwa-for-wp') . '</a>';

                    echo '<a href="' . esc_url(pwaforwp_admin_link('features')) . '" class="nav-tab ' . esc_attr($tab == 'features' ? 'nav-tab-active' : '') . '"><span class="dashicons dashicons-admin-generic"></span> ' . esc_html__('Features', 'pwa-for-wp') . '</a>';
                    
                                        echo '<a href="' . esc_url(pwaforwp_admin_link('push_notification')) . '" class="nav-tab ' . esc_attr($tab == 'push_notification' ? 'nav-tab-active' : '') . '\"><span class="dashicons dashicons-bell"></span> ' . esc_html__('Push Notifications', 'pwa-for-wp') . '</a>';
                    echo '<a href="' . esc_url(pwaforwp_admin_link('tools')) . '" class="nav-tab ' . esc_attr($tab == 'tools' ? 'nav-tab-active' : '') . '"><span class="dashicons dashicons-admin-tools"></span> ' . esc_html__('Tools', 'pwa-for-wp') . '</a>';


                    echo '<a href="' . esc_url(pwaforwp_admin_link('other_setting')) . '" class="nav-tab ' . esc_attr($tab == 'other_setting' ? 'nav-tab-active' : '') . '"><span class="dashicons dashicons-admin-settings"></span> ' . esc_html__('Advance', 'pwa-for-wp') . '</a>';

                                // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- all data already escapped.

                    ?>
                                </h2>
                                <?php
                                if ($tab == 'push_notification' && class_exists('Push_Notification_Admin') ) {
                                        $pn_admin = new Push_Notification_Admin();
                                        $pn_admin->settings_init();
                                        $pn_admin->admin_interface_render();
                                        return;
                                }
                                ?>
                    <form action="options.php" method="post" enctype="multipart/form-data" class="pwaforwp-settings-form">
                    <div class="form-wrap">
                        <?php
                        // Output nonce, action, and option_page fields for a settings page.
                        settings_fields('pwaforwp_setting_dashboard_group');                        
                        
                        echo "<div class='pwaforwp-dashboard' ".( $tab != 'dashboard' ? 'style="display:none;"' : '').">";
                        // Status
                        do_settings_sections('pwaforwp_dashboard_section');    // Page slug
                        echo "</div>";

                        echo "<div class='pwaforwp-general pwaforwp-subheading-wrap' ".( $tab != 'general' ? 'style="display:none;"' : '').">";
                        /*Sub menu tabs*/

                        echo '<div class="pwaforwp-sub-tab-headings">
									<span data-tab-id="subtab-general" class="selected">'.esc_html__('General', 'pwa-for-wp').'</span>&nbsp;|&nbsp;
									<span data-tab-id="subtab-design">'.esc_html__('Design', 'pwa-for-wp').'</span>
								</div>';
                        echo '<div class="pwaforwp-subheading">';
                                // general Application Settings
                                echo '<div id="subtab-general" class="selected">';
                                        do_settings_sections('pwaforwp_general_section');
                                echo '</div>';
                                echo '<div id="subtab-design" class="pwaforwp-hide">';
                                        do_settings_sections('pwaforwp_design_section');
                                echo '</div>';
                        echo '</div>';

                        echo "</div>";

                        //feature
                        echo "<div class='pwaforwp-features' ".( $tab != 'features' ? 'style="display:none;"' : '').">";
                        // design Application Settings
                        pwaforwp_features_settings();
                            
                        echo "</div>";
                                    
                        
                                    
                        echo "<div class='pwaforwp-tools pwaforwp-subheading-wrap' ".( $tab != 'tools' ? 'style="display:none;"' : '').">";
                        // other_setting Application Settings


                                /*Sub menu tabs*/
                                echo '<div class="pwaforwp-sub-tab-headings">
										<span data-tab-id="subtab-tools" class="selected">'.esc_html__('Tools', 'pwa-for-wp').'</span>&nbsp;|&nbsp;
										<span data-tab-id="subtab-compatibility">'.esc_html__('Compatibility', 'pwa-for-wp').'</span>
									</div>';
                                echo '<div class="pwaforwp-subheading">';
                                    // general Application Settings
                                    echo '<div id="subtab-tools" class="selected">';
                                            do_settings_sections('pwaforwp_tools_section');    // Page slug
                                    echo '</div>';
                                    echo '<div id="subtab-compatibility" class="pwaforwp-hide">';
                                            do_settings_sections('pwaforwp_compatibility_setting_section');
                                    echo '</div>';
                                echo '</div>';






                                    
                        echo "<div class='pwaforwp-other_setting' ".( $tab != 'other_setting' ? 'style="display:none;"' : '').">";
                        // other_setting Application Settings
                        do_settings_sections('pwaforwp_other_setting_section');    // Page slug
                        echo "</div>";
                                   

                        ?>
                    </div>
                    <div class="button-wrapper">
                                    <input type="hidden" name="pwaforwp_settings[manualfileSetup]" value="1">
                        <?php
                        // Output save settings button
                        submit_button(esc_html__('Save Settings', 'pwa-for-wp'));
                        ?>
                    </div>
                </form>

            </div>
    <?php
} // end pwaforwp_admin_interface_render
/* WP Settings API */
add_action('admin_init', 'pwaforwp_settings_init');

function pwaforwp_settings_init()
{
    $settings = pwaforwp_defaultSettings(); 
    if(isset($settings['loading_icon_display_admin']) && $settings['loading_icon_display_admin'] && is_admin() ) {
        add_action('admin_footer', 'pwaforwp_loading_icon');
        add_action('admin_print_footer_scripts', 'pwaforwp_loading_icon_scripts');
        add_action('admin_print_styles', 'pwaforwp_loading_icon_styles');
    }
    add_action('admin_print_styles', 'pwaforwp_loading_select2_styles');
    register_setting('pwaforwp_setting_dashboard_group', 'pwaforwp_settings', 'pwaforwp_sanitize_fields');

    add_settings_section(
        'pwaforwp_dashboard_section', esc_html__('Installation Status', 'pwa-for-wp').'<span class="pwafw-tooltip"><i class="dashicons dashicons-editor-help"></i> 
                        <span class="pwafw-help-subtitle">'.esc_html__('PWA status verification', 'pwa-for-wp').' <a href="https://pwa-for-wp.com/docs/article/how-to-install-setup-pwa-in-amp/" target="_blank">'.esc_html__('Learn more', 'pwa-for-wp').'</a></span>
                    </span>', '__return_false', 'pwaforwp_dashboard_section'
    );
        // Manifest status
        add_settings_field(
            'pwaforwp_manifest_status',                                // ID
            '',            // Title
            'pwaforwp_files_status_callback',                    // Callback
            'pwaforwp_dashboard_section',                            // Page slug
            'pwaforwp_dashboard_section'                            // Settings Section ID
        );

                // HTTPS status                

    add_settings_section('pwaforwp_general_section', __return_false(), '__return_false', 'pwaforwp_general_section');

        // Application Name
        add_settings_field(
            'pwaforwp_app_name',                                    // ID
            esc_html__('App Name', 'pwa-for-wp'),    // Title
            'pwaforwp_app_name_callback',                                    // CB
            'pwaforwp_general_section',                        // Page slug
            'pwaforwp_general_section'                        // Settings Section ID
        );

        // Application Short Name
        add_settings_field(
            'pwaforwp_app_short_name',                                // ID
            esc_html__('App Short Name', 'pwa-for-wp'),    // Title
            'pwaforwp_app_short_name_callback',                            // CB
            'pwaforwp_general_section',                        // Page slug
            'pwaforwp_general_section'                        // Settings Section ID
        );

        // Description
        add_settings_field(
            'pwaforwp_app_description',                                    // ID
            esc_html__('App Description', 'pwa-for-wp'),        // Title
            'pwaforwp_description_callback',                                // CB
            'pwaforwp_general_section',                        // Page slug
            'pwaforwp_general_section'                        // Settings Section ID
        );
        
        // Application Icon
        add_settings_field(
            'pwaforwp_app_icons',                                        // ID
            esc_html__('App Icon', 'pwa-for-wp'),    // Title
            'pwaforwp_app_icon_callback',                                    // Callback function
            'pwaforwp_general_section',                        // Page slug
            'pwaforwp_general_section'                        // Settings Section ID
        );
        
        // Application Maskable Icon
        add_settings_field(
            'pwaforwp_app_maskable_icons',                                        // ID
            esc_html__('App Maskable Icon', 'pwa-for-wp'),    // Title
            'pwaforwp_app_maskable_icon_callback',                                    // Callback function
            'pwaforwp_general_section',                        // Page slug
            'pwaforwp_general_section'                        // Settings Section ID
        );
        
        // Monochrome Icon
        add_settings_field(
            'pwaforwp_monochrome',                                        // ID
            esc_html__('Monochrome Icon', 'pwa-for-wp'),    // Title
            'pwaforwp_monochrome_callback',                                    // Callback function
            'pwaforwp_general_section',                        // Page slug
            'pwaforwp_general_section'                        // Settings Section ID
        );
        
        // Splash Screen Icon
        add_settings_field(
            'pwaforwp_app_splash_icon',                                    // ID
            esc_html__('App Splash Screen Icon', 'pwa-for-wp'),    // Title
            'pwaforwp_splash_icon_callback',                                // Callback function
            'pwaforwp_general_section',                        // Page slug
            'pwaforwp_general_section'                        // Settings Section ID
        );
        
        // Splash Screen Icon
        add_settings_field(
            'pwaforwp_app_splash_maskable_icon',                                    // ID
            esc_html__('App Splash Maskable Icon', 'pwa-for-wp'),    // Title
            'pwaforwp_splash_maskable_icon_callback',                                // Callback function
            'pwaforwp_general_section',                        // Page slug
            'pwaforwp_general_section'                        // Settings Section ID
        );

        // Screenshot Icon
        add_settings_field(
            'pwaforwp_app_screenshots',                                       // ID
            esc_html__('APP Screenshots', 'pwa-for-wp'),   // Title
            'pwaforwp_app_screenshots_callback',                                   // Callback function
            'pwaforwp_general_section',                     // Page slug
            'pwaforwp_general_section'                      // Settings Section ID
        );

        // Offline Page
        add_settings_field(
            'pwaforwp_offline_page',                                // ID
            esc_html__('Offline Page', 'pwa-for-wp'),        // Title
            'pwaforwp_offline_page_callback',                                // CB
            'pwaforwp_general_section',                        // Page slug
            'pwaforwp_general_section'                        // Settings Section ID
        );

        // 404 Page
        add_settings_field(
            'pwaforwp_404_page',                                // ID
            esc_html__('404 Page', 'pwa-for-wp'),        // Title
            'pwaforwp_404_page_callback',                                // CB
            'pwaforwp_general_section',                        // Page slug
            'pwaforwp_general_section'                        // Settings Section ID
        );
                
                // Start page
        add_settings_field(
            'pwaforwp_start_page',                                // ID
            esc_html__('Start Page', 'pwa-for-wp'),        // Title
            'pwaforwp_start_page_callback',                                // CB
            'pwaforwp_general_section',                        // Page slug
            'pwaforwp_general_section'                        // Settings Section ID
        );
        
        // Orientation
        add_settings_field(
            'pwaforwp_orientation',                                    // ID
            esc_html__('Orientation', 'pwa-for-wp'),        // Title
            'pwaforwp_orientation_callback',                                // CB
            'pwaforwp_general_section',                        // Page slug
            'pwaforwp_general_section'                        // Settings Section ID
        ); 

        // Display
        add_settings_field(
            'pwaforwp_display',                                    // ID
            esc_html__('Display', 'pwa-for-wp'),        // Title
            'pwaforwp_display_callback',                                // CB
            'pwaforwp_general_section',                        // Page slug
            'pwaforwp_general_section'                        // Settings Section ID
        );
        // Apple mobile web app status bar style
        add_settings_field(
            'pwaforwp_ios_status_bar',                                    // ID
            esc_html__('iOS APP Status Bar', 'pwa-for-wp'),        // Title
            'pwaforwp_apple_status_bar_callback',                                // CB
            'pwaforwp_general_section',                        // Page slug
            'pwaforwp_general_section'                        // Settings Section ID
        );
        
        add_settings_field(
            'pwaforwp_prefer_related_applications',                                    // ID
            esc_html__('Prefer Related Application', 'pwa-for-wp'),    // Title
            'pwaforwp_prefer_related_applications_callback',                                // Callback function
            'pwaforwp_general_section',                        // Page slug
            'pwaforwp_general_section'                        // Settings Section ID
        );
        add_settings_field(
            'pwaforwp_app_related_applications',                                    // ID
            esc_html__('Related Application', 'pwa-for-wp'),    // Title
            'pwaforwp_related_applications_callback',                                // Callback function
            'pwaforwp_general_section',                        // Page slug
            'pwaforwp_general_section'                        // Settings Section ID
        );

    add_settings_section('pwaforwp_design_section', 'Splash Screen', '__return_false', 'pwaforwp_design_section');
        // Splash Screen Background Color
        add_settings_field(
            'pwaforwp_background_color',                            // ID
            esc_html__('Background Color', 'pwa-for-wp'),    // Title
            'pwaforwp_background_color_callback',                            // CB
            'pwaforwp_design_section',                        // Page slug
            'pwaforwp_design_section'                        // Settings Section ID
        );
        // Splash Screen Theme Color
        add_settings_field(
            'pwaforwp_theme_color',                            // ID
            esc_html__('Theme Color', 'pwa-for-wp'),    // Title
            'pwaforwp_theme_color_callback',                            // CB
            'pwaforwp_design_section',                        // Page slug
            'pwaforwp_design_section'                        // Settings Section ID
        );        
                
                                                
    add_settings_section('pwaforwp_tools_section', ' ', '__return_false', 'pwaforwp_tools_section');
                                                
        add_settings_field(
            'pwaforwp_reset_setting',                            // ID
            esc_html__('Reset', 'pwa-for-wp'),    // Title
            'pwaforwp_reset_setting_callback',                            // CB
            'pwaforwp_tools_section',                        // Page slug
            'pwaforwp_tools_section'                        // Settings Section ID
        );

        add_settings_field(
            'pwaforwp_cleandataonuninstall_setting',                           // ID
            '<label for="pwaforwp_settings_navigation_uninstall_setting"><b>'.esc_html__('Remove Data on Uninstall?', 'pwa-for-wp').'</b></label>',  // Title
            'pwaforwp_cleandataonuninstall_setting_callback',                          // CB
            'pwaforwp_tools_section',                       // Page slug
            'pwaforwp_tools_section'                        // Settings Section ID
        );


        //Misc tabs
        add_settings_section('pwaforwp_other_setting_section', ' ', '__return_false', 'pwaforwp_other_setting_section');
        add_settings_field(
            'pwaforwp_cdn_setting',                            // ID
            '<label for="pwaforwp_settings_cdn_setting"><b>'.esc_html__('CDN Compatibility', 'pwa-for-wp').'</b></label>',    // Title
            'pwaforwp_cdn_setting_callback',                            // CB
            'pwaforwp_other_setting_section',                        // Page slug
            'pwaforwp_other_setting_section'                        // Settings Section ID
        );                
        add_settings_field(
            'pwaforwp_offline_google_setting',                            // ID
            '<label for="pwaforwp_settings[offline_google_setting]"><b>'.esc_html__('Offline Google Analytics', 'pwa-for-wp').'</b></label>',    // Title
            'pwaforwp_offline_google_setting_callback',                            // CB
            'pwaforwp_other_setting_section',                        // Page slug
            'pwaforwp_other_setting_section'                        // Settings Section ID
        );
        add_settings_field(
            'pwaforwp_prefetch_manifest_setting',                            // ID
            '<label for="pwaforwp_settings[prefetch_manifest_setting]"><b>'.esc_html__('Prefetch manifest URL link', 'pwa-for-wp').'</b></label>',    // Title
            'pwaforwp_prefetch_manifest_setting_callback',                            // CB
            'pwaforwp_other_setting_section',                        // Page slug
            'pwaforwp_other_setting_section'                        // Settings Section ID
        );
                add_settings_field(
                    'pwaforwp_force_update_sw_setting_setting',                            // ID
                    esc_html__('Force Update Service Worker', 'pwa-for-wp'),    // Title
                    'pwaforwp_force_update_sw_setting_callback',                            // CB
                    'pwaforwp_other_setting_section',                        // Page slug
                    'pwaforwp_other_setting_section'                        // Settings Section ID
                );                
        add_settings_field(
            'pwaforwp_add_to_home',                                    // ID
            esc_html__('Add To Home On Element Click', 'pwa-for-wp'),        // Title
            'pwaforwp_add_to_home_callback',                                // CB
            'pwaforwp_other_setting_section',                        // Page slug
            'pwaforwp_other_setting_section'                        // Settings Section ID
        );

        add_settings_section('pwaforwp_addtohomescreen_setting_section', ' ', '__return_false', 'pwaforwp_addtohomescreen_setting_section');
        add_settings_field(
            'pwaforwp_custom_add_to_home',                                    // ID
            esc_html__('Custom Add To Home Banner', 'pwa-for-wp'),        // Title
            'pwaforwp_custom_add_to_home_callback',                                // CB
            'pwaforwp_addtohomescreen_setting_section',                        // Page slug
            'pwaforwp_addtohomescreen_setting_section'                        // Settings Section ID
        );
       
        add_settings_field(
            'pwaforwp_cache_external_links_setting',                            // ID
            '<label for="pwaforwp_settings_external_links_setting"><b>'.esc_html__('Cache External Links', 'pwa-for-wp').'</b></label>',    // Title
            'pwaforwp_cache_external_links_setting_callback',                            // CB
            'pwaforwp_other_setting_section',                        // Page slug
            'pwaforwp_other_setting_section'                        // Settings Section ID
        );
        add_settings_section('pwaforwp_utmtracking_setting_section', ' ', '__return_false', 'pwaforwp_utmtracking_setting_section');
        add_settings_field(
            'pwaforwp_utm_setting',                            // ID
            esc_html__('UTM Tracking', 'pwa-for-wp'),    // Title
            'pwaforwp_utm_setting_callback',                            // CB
            'pwaforwp_utmtracking_setting_section',                        // Page slug
            'pwaforwp_utmtracking_setting_section'                        // Settings Section ID
        );                
                add_settings_field(
                    'pwaforwp_exclude_url_setting',                            // ID
                    esc_html__('Urls Exclude From Cache List', 'pwa-for-wp'),    // Title
                    'pwaforwp_url_exclude_from_cache_list_callback',                            // CB
                    'pwaforwp_other_setting_section',                        // Page slug
                    'pwaforwp_other_setting_section'                        // Settings Section ID
                );
        add_settings_field(
            'pwaforwp_cache_time_setting',                            // ID
            esc_html__('Cached time', 'pwa-for-wp'),    // Title
            'pwaforwp_cache_time_setting_callback',                            // CB
            'pwaforwp_other_setting_section',                        // Page slug
            'pwaforwp_other_setting_section'                        // Settings Section ID
        );
        add_settings_field(
            'pwaforwp_avoid_default_banner_setting',                            // ID
            '<label for="pwaforwp_settings[avoid_default_banner]"><b>'.esc_html__('Remove default banner', 'pwa-for-wp').'</b></label>',    // Title
            'pwaforwp_avoid_default_banner_setting_callback',                            // CB
            'pwaforwp_other_setting_section',                        // Page slug
            'pwaforwp_other_setting_section'                        // Settings Section ID
        );
        add_settings_field(
            'pwaforwp_avoid_pwa_loggedin_setting',                            // ID
            '<label for="pwaforwp_settings[avoid_loggedin_users]"><b>'.esc_html__('Remove pwa for logged in users', 'pwa-for-wp').'</b></label>',    // Title
            'pwaforwp_avoid_pwa_loggedin_setting_callback',                            // CB
            'pwaforwp_other_setting_section',                        // Page slug
            'pwaforwp_other_setting_section'                        // Settings Section ID
        );
        add_settings_field(
            'pwaforwp_swipe_navigation',                            // ID
            '<label for="pwaforwp_settings[swipe_navigation]"><b>'.esc_html__('Swipe Navigation in PWA', 'pwa-for-wp').'</b></label>',    // Title
            'pwaforwp_swipe_navigation_setting_callback',                            // CB
            'pwaforwp_other_setting_section',                        // Page slug
            'pwaforwp_other_setting_section'                        // Settings Section ID
        );
        add_settings_field(
            'pwaforwp_serve_cache_method_setting',                            // ID
            '<label for="pwaforwp_settings[serve_js_cache_menthod]"><b>'.esc_html__('PWA alternative method', 'pwa-for-wp').'</b></label>',    // Title
            'pwaforwp_serve_cache_method_setting_callback',                            // CB
            'pwaforwp_other_setting_section',                        // Page slug
            'pwaforwp_other_setting_section'                        // Settings Section ID
        );
        add_settings_field(
            'pwaforwp_reset_cookies_method_setting',                            // ID
            '<label for="pwaforwp_settings[reset_cookies]"><b>'.esc_html__('Reset cookies', 'pwa-for-wp').'</b></label>',    // Title
            'pwaforwp_reset_cookies_method_setting_callback',                            // CB
            'pwaforwp_other_setting_section',                        // Page slug
            'pwaforwp_other_setting_section'                        // Settings Section ID
        );
        add_settings_field(
            'pwaforwp_disallow_data_tracking_setting',                            // ID
            esc_html__('Share Anonymous data for improving the UX', 'pwa-for-wp'),    // Title
            'pwaforwp_disallow_data_tracking_setting_callback',                            // CB
            'pwaforwp_other_setting_section',                        // Page slug
            'pwaforwp_other_setting_section'                        // Settings Section ID
        );
    if(function_exists('is_super_admin') &&  is_super_admin() ) {
        add_settings_field(
            'pwaforwp_role_based_access_setting',                            // ID
            esc_html__('Role Based Access', 'pwa-for-wp'),    // Title
            'pwaforwp_role_based_access_setting_callback',                            // CB
            'pwaforwp_other_setting_section',                        // Page slug
            'pwaforwp_other_setting_section'                        // Settings Section ID
        );
    }
        add_settings_field(
            'pwaforwp_offline_message_setting',                            // ID
            '<label for="pwaforwp_settings[offline_message_setting]"><b>'.esc_html__('Offline Message', 'pwa-for-wp').'</b></label>',    // Title
            'pwaforwp_offline_message_setting_callback',                            // CB
            'pwaforwp_other_setting_section',                        // Page slug
            'pwaforwp_other_setting_section'                        // Settings Section ID
        );
        add_settings_field(
            'pwaforwp_scrollbar_setting',                            // ID
            '<label for="pwaforwp_settings[scrollbar_setting]"><b>'.esc_html__('Disable Scrollbar', 'pwa-for-wp').'</b></label>',    // Title
            'pwaforwp_scrollbar_setting_callback',                            // CB
            'pwaforwp_other_setting_section',                        // Page slug
            'pwaforwp_other_setting_section'                        // Settings Section ID
        );
                add_settings_field(
                    'pwaforwp_enable_pull_to_refresh_setting',             // ID
                    '<label for="pwaforwp_settings[enable_pull_to_refresh]"><b>'.esc_html__('Pull to Refresh', 'pwa-for-wp').'</b></label>',   // Title
                    'pwaforwp_enable_pull_to_refresh_setting_callback',    // CB
                    'pwaforwp_other_setting_section',                      // Page slug
                    'pwaforwp_other_setting_section'                       // Settings Section ID
                );
        add_settings_field(
            'pwaforwp_force_rememberme_setting',                            // ID
            '<label for="pwaforwp_settings[force_rememberme]"><b>'.esc_html__('Force Remember me', 'pwa-for-wp').'</b></label>',    // Title
            'pwaforwp_force_rememberme_setting_callback',                            // CB
            'pwaforwp_other_setting_section',                        // Page slug
            'pwaforwp_other_setting_section'                        // Settings Section ID
        );
        add_settings_section('pwaforwp_loaders_setting_section', ' ', '__return_false', 'pwaforwp_loaders_setting_section');
        add_settings_field(
            'pwaforwp_loading_setting',                            // ID
            esc_html__('Loader', 'pwa-for-wp'),    // Title
            'pwaforwp_loading_setting_callback',                            // CB
            'pwaforwp_loaders_setting_section',                        // Page slug
            'pwaforwp_loaders_setting_section'                        // Settings Section ID
        );
        add_settings_field(
            'pwaforwp_loading_color_setting',                            // ID
            esc_html__('Loader color', 'pwa-for-wp'),    // Title
            'pwaforwp_loading_color_setting_callback',                            // CB
            'pwaforwp_loaders_setting_section',                        // Page slug
            'pwaforwp_loaders_setting_section'                        // Settings Section ID
        );
        add_settings_field(
            'pwaforwp_loading_background_color_setting',                            // ID
            esc_html__('Loader background color', 'pwa-for-wp'),    // Title
            'pwaforwp_loading_background_color_setting_callback',                            // CB
            'pwaforwp_loaders_setting_section',                        // Page slug
            'pwaforwp_loaders_setting_section'                        // Settings Section ID
        );
        add_settings_field(
            'pwaforwp_show_pwa_option_setting',                            // ID
            esc_html__('Show only in PWA', 'pwa-for-wp'),    // Title
            'pwaforwp_loading_display_inpwa_setting_callback',                            // CB
            'pwaforwp_loaders_setting_section',                        // Page slug
            'pwaforwp_loaders_setting_section'                        // Settings Section ID
        );
        add_settings_field(
            'pwaforwp_loading_display_option_setting',                            // ID
            esc_html__('Loader enable on', 'pwa-for-wp'),    // Title
            'pwaforwp_loading_display_setting_callback',                            // CB
            'pwaforwp_loaders_setting_section',                        // Page slug
            'pwaforwp_loaders_setting_section'                        // Settings Section ID
        );
        do_action("pwaforwp_loading_icon_libraries", 'pwaforwp_loaders_setting_section');

                
        add_settings_field(
            'pwaforwp_caching_strategies_setting',                            // ID
            '<h2>'.esc_html__('Caching Strategies', 'pwa-for-wp').'
            <span class="pwafw-tooltip"><i class="dashicons dashicons-editor-help"></i> 
                        <span class="pwafw-help-subtitle">Caching preferences <a href="'.esc_url('https://pwa-for-wp.com/docs/article/what-is-caching-strategies-in-pwa-and-how-to-use-it/').'" target="_blank">'.esc_html__('Learn more', 'pwa-for-wp').'</a></span>
                    </span>
            </h2>',    // Title
            'pwaforwp_caching_strategies_setting_callback',                            // CB
            'pwaforwp_other_setting_section',                        // Page slug
            'pwaforwp_other_setting_section'                        // Settings Section ID
        );

        add_settings_section('pwaforwp_compatibility_setting_section', '', '__return_false', 'pwaforwp_compatibility_setting_section');
                add_settings_field(
                    'pwaforwp_one_signal_support',                                    // ID
                    '<label for="pwaforwp_settings[one_signal_support_setting]"><b>'.esc_html__('OneSignal', 'pwa-for-wp').'</b></label>',        // Title
                    'pwaforwp_one_signal_support_callback',                                // CB
                    'pwaforwp_compatibility_setting_section',                        // Page slug
                    'pwaforwp_compatibility_setting_section'                        // Settings Section ID
                );
        add_settings_field(
            'pwaforwp_pushnami_support',                            // ID
            '<label for="pwaforwp_settings[pushnami_support_setting]"><b>'.esc_html__('Pushnami', 'pwa-for-wp').'</b></label>',                    // Title
            'pwaforwp_pushnami_support_callback',                    // CB
            'pwaforwp_compatibility_setting_section',                // Page slug
            'pwaforwp_compatibility_setting_section'                // Settings Section ID
        );
        add_settings_field(
            'pwaforwp_webpushr_support',                            // ID
            '<label for="pwaforwp_settings[webpusher_support_setting]"><b>'.esc_html__('Webpushr', 'pwa-for-wp').'</b></label>',                    // Title
            'pwaforwp_webpushr_support_callback',                    // CB
            'pwaforwp_compatibility_setting_section',                // Page slug
            'pwaforwp_compatibility_setting_section'                // Settings Section ID
        );

        add_settings_field(
            'pwaforwp_wphide_support',                            // ID
            '<label for="pwaforwp_settings[wphide_support_setting]"><b>'.esc_html__('WP Hide & Security Enhancer', 'pwa-for-wp').'</b></label>',                    // Title
            'pwaforwp_wphide_support_callback',                    // CB
            'pwaforwp_compatibility_setting_section',                // Page slug
            'pwaforwp_compatibility_setting_section'                // Settings Section ID
        );
                               
        add_settings_section('pwaforwp_visibility_setting_section', '', '__return_false', 'pwaforwp_visibility_setting_section');
        add_settings_field(
            'pwaforwp_visibility_setting',                            // ID
            '',    
            'pwaforwp_visibility_setting_callback',                            // CB
            'pwaforwp_visibility_setting_section',                        // Page slug
            'pwaforwp_visibility_setting_section'                        // Settings Section ID
        );  

        add_settings_section('pwaforwp_precaching_setting_section', '', '__return_false', 'pwaforwp_precaching_setting_section');
        add_settings_field(
            'pwaforwp_precaching_setting',                          // ID
            '', 
            'pwaforwp_precaching_setting_callback',                         // CB
            'pwaforwp_precaching_setting_section',                      // Page slug
            'pwaforwp_precaching_setting_section'                       // Settings Section ID
        );  
        add_settings_section('pwaforwp_urlhandler_setting_section', '', '__return_false', 'pwaforwp_urlhandler_setting_section');
        add_settings_field(
            'pwaforwp_urlhandler_setting',                            // ID
            esc_html__('Enter URLs (with similar origin)', 'pwa-for-wp'),    
            'pwaforwp_urlhandler_setting_callback',                            // CB
            'pwaforwp_urlhandler_setting_section',                        // Page slug
            'pwaforwp_urlhandler_setting_section'                        // Settings Section ID
        );  
                
                
                add_settings_section('pwaforwp_push_notification_section', '', '__return_false', 'pwaforwp_push_notification_section');
        // Splash Screen Background Color
        add_settings_field(
            'pwaforwp_push_notification',                            // ID
            '',    
            'pwaforwp_push_notification_callback',                            // CB
            'pwaforwp_push_notification_section',                        // Page slug
            'pwaforwp_push_notification_section'                        // Settings Section ID
        );
                
                
                
        
}

function pwaforwp_sanitize_fields($inputs=array())
{
    $fields_type_data = pwaforwp_fields_and_type('type');

    foreach ($inputs as $key => $value) {
        if (isset($fields_type_data[$key])) {
            $fields_type = $fields_type_data[$key];
            if (is_array($value)) {
                if($key == 'shortcut') {
                    foreach ($value as $k => $vals) {
                        foreach ($vals as $kc => $vc) {
                            $value[sanitize_key($k)][sanitize_key($kc)] = sanitize_text_field($vc);
                        }
                    }
                }else{
                    foreach ($value as $k => $val) {
                        $value[sanitize_key($k)] = sanitize_text_field($val);
                    }
                }
                $inputs[sanitize_key($key)] = $value;
            }else{
                switch ($fields_type) {
                case 'text':
                    $inputs[sanitize_key($key)] = sanitize_text_field($value);
                    break;
                case 'textarea':
                    $inputs[sanitize_key($key)] = sanitize_textarea_field($value);
                    break;
                case 'checkbox':
                    $inputs[sanitize_key($key)] = filter_var($value, FILTER_SANITIZE_NUMBER_INT);
                    break;
                    
                default:
                    $inputs[sanitize_key($key)] = sanitize_text_field($value);
                    break;
                }
                
            }
        }else{
            if (is_array($value)) {
                foreach ($value as $k => $val) {
                    $value[sanitize_key($k)] = sanitize_text_field($val);
                }        
                $inputs[sanitize_key($key)] = $value;
            }else{
                $inputs[sanitize_key($key)] = sanitize_text_field($value);
            }
        }
    }
    return $inputs;
    
}


function pwaforwp_caching_strategies_setting_callback()
{
    $settings = pwaforwp_defaultSettings();
    $arrayOPT = array(
        'staleWhileRevalidate' => 'Stale While Revalidate',
        'networkFirst'         => 'Network First',
        'cacheFirst'           => 'Cache First',
        'networkOnly'          => 'Network Only',
    );
    ?>
    <tr>
        <td><label><b><?php echo esc_html__('Default caching strategy', 'pwa-for-wp'); ?></b></label></td>
        <td><select name="pwaforwp_settings[default_caching]">
    <?php if (is_array($arrayOPT) && !empty($arrayOPT)) {
        foreach ($arrayOPT as $key => $opval) {
            $sel = '';
            if ($settings['default_caching'] == $key) {
                $sel = 'selected';
            }
            echo '<option value="'.esc_attr($key).'" '.esc_attr($sel).'>'.esc_html($opval).'</option>';
        }
    }
    ?>
        </select>
        <br/>
        <label style="padding-top: 5px;">
            <input type="checkbox" name="pwaforwp_settings[change_default_on_login]" value="1" <?php if (isset($settings['change_default_on_login']) && $settings['change_default_on_login'] == 1) { echo 'checked'; 
} ?>>
        <p><?php echo esc_html__('If you have a login for normal users (it help users to get updates content)', 'pwa-for-wp'); ?></p></label>
        </td>
    </tr>
    <tr>
        <td><label><b><?php echo esc_html__('Caching strategy for CSS and JS Files', 'pwa-for-wp'); ?></b></label></td>
        <td><select name="pwaforwp_settings[default_caching_js_css]">
    <?php if (is_array($arrayOPT) && !empty($arrayOPT)) {
        foreach ($arrayOPT as $key => $opval) {
            $sel = '';
            if ($settings['default_caching_js_css'] == $key) {
                $sel = 'selected';
            }
            echo '<option value="'.esc_attr($key).'" '.esc_attr($sel).'>'.esc_html($opval).'</option>';
        }
    }
    ?>
        </select></td>
    </tr>
    <tr>
        <td><label><b><?php echo esc_html__('Caching strategy for images', 'pwa-for-wp'); ?></b></label></td>
        <td><select name="pwaforwp_settings[default_caching_images]">
    <?php if (is_array($arrayOPT) && !empty($arrayOPT)) {
        foreach ($arrayOPT as $key => $opval) {
            $sel = '';
            if ($settings['default_caching_images'] == $key) {
                $sel = 'selected';
            }
            echo '<option value="'.esc_attr($key).'" '.esc_attr($sel).'>'.esc_html($opval).'</option>';
        }
    }
    ?>
        </select></td>
    </tr>
    <tr>
        <td><label><b><?php echo esc_html__('Caching strategy for fonts', 'pwa-for-wp'); ?></b></label></td>
        <td><select name="pwaforwp_settings[default_caching_fonts]">
    <?php if (is_array($arrayOPT) && !empty($arrayOPT)) {
        foreach ($arrayOPT as $key => $opval) {
            $sel = '';
            if ($settings['default_caching_fonts'] == $key) {
                $sel = 'selected';
            }
            echo '<option value="'.esc_attr($key).'" '.esc_attr($sel).'>'.esc_html($opval).'</option>';
        }
    }
    ?>
        </select></td>
    </tr>
    <?php
}
function pwaforwp_cache_time_setting_callback()
{
    // Get Settings
    $settings = pwaforwp_defaultSettings(); 
    ?>
    <p><?php echo esc_html__('Set max cache time for Html Default:', 'pwa-for-wp'); ?> <code>3600</code> <?php echo esc_html__('in seconds;', 'pwa-for-wp'); ?> <?php echo esc_html__('You need to enter time in seconds', 'pwa-for-wp'); ?></p>
        <input type="text" name="pwaforwp_settings[cached_timer][html]" id="pwaforwp_settings[cached_timer][html]" class=""  value="<?php echo (isset($settings['cached_timer'])? esc_attr($settings['cached_timer']['html']) : '3600'); ?>">
    <p><?php echo esc_html__('Set max cache time for JS, CSS, JSON Default:', 'pwa-for-wp'); ?> <code>86400</code> <?php echo esc_html__('in seconds;', 'pwa-for-wp'); ?> <?php echo esc_html__('You need to enter time in seconds', 'pwa-for-wp'); ?></p>
        <input type="text" name="pwaforwp_settings[cached_timer][css]" id="pwaforwp_settings[cached_timer][css]" class=""  value="<?php echo (isset($settings['cached_timer'])? esc_attr($settings['cached_timer']['css']) : '86400'); ?>">
    <?php
}

function pwaforwp_avoid_default_banner_setting_callback()
{
    // Get Settings
    $settings = pwaforwp_defaultSettings(); 
    $avoid_default_banner_checked = '';
    if(isset($settings['avoid_default_banner']) &&  $settings['avoid_default_banner'] == 1 ) {
        $avoid_default_banner_checked = 'checked';
    }
    ?>
    <input type="checkbox" name="pwaforwp_settings[avoid_default_banner]" id="pwaforwp_settings[avoid_default_banner]" class="" <?php echo esc_attr($avoid_default_banner_checked); ?> data-uncheck-val="0" value="1">
    <p><?php echo esc_html__('Enable(check) it when you don\'t want to load default PWA Banner', 'pwa-for-wp'); ?></p>
    <?php
}

function pwaforwp_avoid_pwa_loggedin_setting_callback()
{
    // Get Settings
    $settings = pwaforwp_defaultSettings(); 
    $avoid_loggedin_users_checked = '';
    if(isset($settings['avoid_loggedin_users']) && $settings['avoid_loggedin_users'] == 1 ) {
        $avoid_loggedin_users_checked = 'checked';
    }
    ?>
    <input type="checkbox" name="pwaforwp_settings[avoid_loggedin_users]" id="pwaforwp_settings[avoid_loggedin_users]" class=""  <?php echo esc_attr($avoid_loggedin_users_checked); ?> data-uncheck-val="0" value="1">
    <p><?php echo esc_html__('(check) it, if you want disable PWA for loggedin users', 'pwa-for-wp'); ?></p>
    <?php
}

function pwaforwp_swipe_navigation_setting_callback()
{
    // Get Settings
    $settings = pwaforwp_defaultSettings();
    $swipe_navigation_checked = ( isset($settings['swipe_navigation']) && $settings['swipe_navigation'] == '1' ) ? 'checked' : '';
    ?>
        
    <input type="checkbox" name="pwaforwp_settings[swipe_navigation]" id="pwaforwp_settings[swipe_navigation]" class="" <?php echo esc_attr($swipe_navigation_checked); ?> data-uncheck-val="0" value="1">
    <p><?php echo esc_html__('This option adds swipe left / right feature to load next and previous articles.', 'pwa-for-wp'); ?></p>
    <?php
}

function pwaforwp_serve_cache_method_setting_callback()
{
    // Get Settings
    $settings = pwaforwp_defaultSettings(); 
    ?>
    <input type="checkbox" name="pwaforwp_settings[serve_js_cache_menthod]" id="pwaforwp_settings[serve_js_cache_menthod]" class=""  <?php echo (isset($settings['serve_js_cache_menthod']) && $settings['serve_js_cache_menthod']=='true'? esc_attr('checked') : ''); ?> data-uncheck-val="0" value="true">
    <p><?php echo esc_html__('Enable(check) it when PWA with OneSignal or root permission functionality not working because of Cache', 'pwa-for-wp'); ?></p>
    <?php
}

function pwaforwp_reset_cookies_method_setting_callback()
{
    // Get Settings
    $settings = pwaforwp_defaultSettings(); 
    ?>
    <input type="checkbox" name="pwaforwp_settings[reset_cookies]" id="pwaforwp_settings[reset_cookies]" class=""  <?php echo (isset($settings['reset_cookies']) && $settings['reset_cookies']=='1'? esc_attr('checked') : ''); ?> data-uncheck-val="0" value="1">
    <p><?php echo esc_html__('Check this to delete cookies', 'pwa-for-wp'); ?></p>
    <?php
}
function pwaforwp_role_based_access_setting_callback()
{
    if(function_exists('is_super_admin') &&  is_super_admin() ) {
        $settings = pwaforwp_defaultSettings(); 
        ?>
            <select id="pwaforwp_role_based_access" class="regular-text" name="pwaforwp_settings[pwaforwp_role_based_access][]" multiple="multiple">
        <?php
        foreach (pwaforwp_get_user_roles() as $key => $opval) {
            $selected = "";
            if (isset($settings['pwaforwp_role_based_access']) && in_array($key, $settings['pwaforwp_role_based_access'])) {
                $selected = "selected";
            }
            ?>
                        
                        <option value="<?php echo esc_attr($key);?>" <?php echo esc_html($selected);?>><?php echo esc_html($opval); ?></option>
        <?php }
        ?>
                </select><br/><p>
        <?php
        echo esc_html__('Choose the users whom you want to allow full access of this plugin', 'pwa-for-wp');
        ?>
                </p>
        <?php

        
        
    } 
}
function pwaforwp_disallow_data_tracking_setting_callback()
{
    // Get Settings
    $settings = pwaforwp_defaultSettings(); 
    $allow_tracking = get_option('wisdom_allow_tracking');
    $plugin = basename(PWAFORWP_PLUGIN_FILE, '.php');

    $checked = "";$tracker_url = '';
	// phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized, WordPress.Security.ValidatedSanitizedInput.MissingUnslash, WordPress.Security.ValidatedSanitizedInput.InputNotValidated
    $live_url = '//' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
    if(isset($allow_tracking[$plugin])) {
        $checked = "checked";
        $tracker_url = $url_no = add_query_arg(
            array(
            'plugin'         => $plugin,
            'plugin_action'    => 'no',
            ), $live_url
        );
    }else{
        $tracker_url = $yes_args = add_query_arg(
            array(
            'plugin'         => $plugin,
            'plugin_action'    => 'yes'
            ), $live_url
        );
    }
    ?>
    <input type="checkbox" <?php echo esc_attr($checked); ?> onclick="window.location = '<?php echo esc_js($tracker_url); ?>'">
    <p><?php echo esc_html__('We guarantee no sensitive data is collected', 'pwa-for-wp'); ?>. <a target="_blank" href="https://pwa-for-wp.com/docs/article/usage-data-tracking/" target="_blank"><?php echo esc_html__('Learn more', 'pwa-for-wp'); ?></a>.</p>
    <?php
}

function pwaforwp_url_exclude_from_cache_list_callback()
{
    // Get Settings
    $settings = pwaforwp_defaultSettings(); 
    ?>
        <label><textarea placeholder="<?php esc_attr('https://example.com/admin.php?page=newpage, https://example.com/admin.php?page=newpage2') ?>"  rows="4" cols="70" id="pwaforwp_settings[excluded_urls]" name="pwaforwp_settings[excluded_urls]"><?php echo (isset($settings['excluded_urls']) ? esc_attr($settings['excluded_urls']): ''); ?></textarea></label>
        <p><?php echo esc_html__('Note: Put in comma separated, do not add enter in urls', 'pwa-for-wp'); ?></p>
    <p><?php echo esc_html__('Put the list of urls which you do not want to cache by service worker', 'pwa-for-wp'); ?></p>    
    
    <?php
}

function pwaforwp_urlhandler_setting_callback()
{
    $settings = pwaforwp_defaultSettings(); 
    echo "<textarea name='pwaforwp_settings[urlhandler]' rows='10' cols='80' placeholder='".esc_attr('https://music.example.com\nhttps://*.music.example.com\nhttps://chat.example.com\nhttps://*.music.example.com')."'>". (isset($settings['urlhandler'])? esc_attr($settings['urlhandler']): '') ."</textarea>";
    ?><p><?php echo esc_html__('Note: Put one url in single line', 'pwa-for-wp'); ?></p>
    <br>
    <?php
    if(isset($settings['urlhandler']) && !empty($settings['urlhandler'])) {
        $urls = explode("\n", $settings['urlhandler']);
        if(is_array($urls)) {
            foreach($urls as $url){
                $fileData[] = array(
                "manifest"=> $url,
                "details"=> array(
                "paths"=> array("/*"),
                "exclude_paths"=> array("/wp-admin/*"),
                    )
                    );
            }
            $data = array("web_apps"=>$fileData);
            echo "<p>".esc_html__("Create \"web-app-origin-association\" file for the apple and android.  Need to place the web-app-origin-association file in the /.well-known/ folder at the root of the app. \n example URL https://example.com/.well-known/web-app-origin-association", "pwa-for-wp")." <a href='https://pwa-for-wp.com/docs/article/how-to-use-urlhandler-for-pwa/'>".esc_html__('Learn more', 'pwa-for-wp')."</a></p>";
            echo "<textarea cols='100' rows='20' readonly>".wp_json_encode($data, JSON_PRETTY_PRINT)."</textarea>";
        }
                
    }
    ?>
    <?php
}

function pwaforwp_precaching_setting_callback()
{
    
    $settings = pwaforwp_defaultSettings(); 
        
        $arrayOPT = array(                    
                        'automatic'=>'Automatic',
                        'manual'=>'Manual',            
                     );
        ?>
            
        <tr>
                    <th><strong><?php echo esc_html__('Automatic', 'pwa-for-wp'); ?></strong>
                        <span class="pwafw-tooltip"><i class="dashicons dashicons-editor-help"></i> 
                            <span class="pwafw-help-subtitle"><a href="https://pwa-for-wp.com/docs/article/setting-up-precaching-in-pwa/"><?php echo esc_html__('For details click here', 'pwa-for-wp'); ?></a></span>
                        </span>
                    </th>
                        <td>
                          <input type="checkbox" name="pwaforwp_settings[precaching_automatic]" id="pwaforwp_settings_precaching_automatic" class="" <?php echo (isset($settings['precaching_automatic']) &&  $settings['precaching_automatic'] == 1 ? 'checked="checked"' : ''); ?> data-uncheck-val="0" value="1">   
                        </td>
        </tr>
                <tr>
                <td></td>
                <td> 
                    <table class="pwaforwp-pre-cache-table">
                     <tr>
                         <td>
                          <?php echo esc_html__('Post', 'pwa-for-wp') ?>                             
                         </td>
                         <td>                         
                         <input type="checkbox" name="pwaforwp_settings[precaching_automatic_post]" id="pwaforwp_settings_precaching_automatic_post" class="" <?php echo (isset($settings['precaching_automatic_post']) &&  $settings['precaching_automatic_post'] == 1 ? 'checked="checked"' : ''); ?> data-uncheck-val="0" value="1">     
                         </td>
                         <td>
                         <?php echo esc_html__('Page', 'pwa-for-wp') ?>   
                         </td>
                         <td>
                         <input type="checkbox" name="pwaforwp_settings[precaching_automatic_page]" id="pwaforwp_settings_precaching_automatic_page" class="" <?php echo (isset($settings['precaching_automatic_page']) &&  $settings['precaching_automatic_page'] == 1 ? 'checked="checked"' : ''); ?> data-uncheck-val="0" value="1">         
                         </td>
                         <td>                          
                         <?php echo esc_html__('Custom Post', 'pwa-for-wp') ?>   
                         </td>
                         <td>
                         <input type="checkbox" name="pwaforwp_settings[precaching_automatic_custom_post]" id="pwaforwp_settings_precaching_automatic_custom_post" class="" <?php echo (isset($settings['precaching_automatic_custom_post']) &&  $settings['precaching_automatic_custom_post'] == 1 ? 'checked="checked"' : ''); ?> data-uncheck-val="0" value="1">         
                         </td>                     
                     </tr>
                     
                    </table>
                </td>    
                </tr>
                <tr>
                    <td><strong><?php echo esc_html__('Enter Post Count', 'pwa-for-wp'); ?></strong></td>
                   <td>
                       <input id="pwaforwp_settings_precaching_post_count" name="pwaforwp_settings[precaching_post_count]" value="<?php if(isset($settings['precaching_post_count'])) { echo esc_attr($settings['precaching_post_count']);
} ?>" type="number" min="0">   
                   </td>
                </tr>
                <tr>
                    <td><strong><?php echo esc_html__('Manual', 'pwa-for-wp'); ?> </strong>
                        <span class="pwafw-tooltip"><i class="dashicons dashicons-editor-help"></i> 
                            <span class="pwafw-help-subtitle"><a href="https://pwa-for-wp.com/docs/article/setting-up-precaching-in-pwa/"><?php echo esc_html__('For details click here', 'pwa-for-wp'); ?></a></span>
                        </span>
                    </td>
                        <td>
                         <input type="checkbox" name="pwaforwp_settings[precaching_manual]" id="pwaforwp_settings_precaching_manual" class="" <?php echo (isset($settings['precaching_manual']) &&  $settings['precaching_manual'] == 1 ? 'checked="checked"' : ''); ?> data-uncheck-val="0" value="1">    
                        </td>
        </tr>                
                <tr>    
                    <td> <strong> <?php echo esc_html__('Enter Urls To Be Cached', 'pwa-for-wp'); ?> </strong></td>
                   <td>
                       <label><textarea placeholder="<?php esc_attr('https://example.com/2019/06/06/hello-world/, https://example.com/2019/06/06/hello-world-2/')?>"  rows="4" cols="50" id="pwaforwp_settings_precaching_urls" name="pwaforwp_settings[precaching_urls]"><?php if(isset($settings['precaching_urls'])) { echo esc_attr($settings['precaching_urls']);
} ?></textarea></label>
                       <p><?php echo esc_html__('Note: Put in comma separated', 'pwa-for-wp'); ?></p>
                       <p><?php echo esc_html__('Put the list of urls which you want to pre cache by service worker', 'pwa-for-wp'); ?></p>
                   </td>
                </tr>
        
    
    <?php
}

function pwaforwp_visibility_setting_callback()
{
    
    $settings = pwaforwp_defaultSettings();

    $arrayOPT = array(
                    'post_type'     => 'Post Type',
                    'globally'      => 'Globally',
                    'post'          => 'Post',
                    'post_category' => 'Post Category',
                    'page'          => 'Page',
                    'taxonomy'      => 'Taxonomy Terms',
                    'tags'          => 'Tags',
                    'page_template' => 'Page Template',
                    'user_type'     => 'Logged in User Type'
                );
    
    ?>
        <tr>
            <th colspan="2"><?php echo esc_html__('Which Page Would You Like To Display', 'pwa-for-wp');?></th>
        </tr>

        <tr>
            <th><?php echo esc_html__('Included On', 'pwa-for-wp'); ?> <i class="dashicons dashicons-plus-alt"></i></th> 
        </tr>
        <tr>
            <td colspan="3">
                    
                <div class="visibility-include-target-item-list">
                    <?php $rand = time().wp_rand(000, 999);
                    
                    if(!empty($settings['include_targeting_type'])) {
                        $expo_include_type = explode(',', $settings['include_targeting_type']);
                        $expo_include_data = explode(',', $settings['include_targeting_value']);
                        for ($i=0; $i<count($expo_include_type); $i++) {
                            echo '<span class="pwaforwp-visibility-target-icon-'.esc_attr($rand).'"><input type="hidden" name="include_targeting_type" value="'.esc_attr($expo_include_type[$i]).'">
                                <input type="hidden" name="include_targeting_data" value="'.esc_attr($expo_include_data[$i]).'">';
                            $expo_include_type_test = pwaforwpRemoveExtraValue($expo_include_type[$i]);
                            $expo_include_data_test = pwaforwpRemoveExtraValue($expo_include_data[$i]);
                            echo '<span class="pwaforwp-visibility-target-item"><span class="visibility-include-target-label">'.esc_html($expo_include_type_test.' - '.$expo_include_data_test).'</span>
                            <span class="pwaforwp-visibility-target-icon" data-index="0"><span class="dashicons dashicons-no-alt " aria-hidden="true" onclick="removeIncluded_visibility('.esc_js($rand).')"></span></span></span></span>';
                            $rand++;
                        }
                    }?>
                </div>
            </td>
        </tr>
        <tr>
            <td>
                <select id="pwaforwp_settings[visibility_included_post_options]" class="regular-text pwaforwp_visibility_options_select visibility_options_select_include" onchange="get_include_pages()">
                    <option value=""><?php echo esc_html__("Select Visibility Type", 'pwa-for-wp');?></option>
                    <?php if(is_array($arrayOPT) && !empty($arrayOPT)) {
                        foreach ($arrayOPT as $key => $opval) {?>
                            <option value="<?php echo esc_attr($key);?>"><?php echo esc_html($opval); ?></option>
                        <?php }
                    } ?>
                </select>
                <div class="include_error">&nbsp;</div>
            </td>
            <td class="visibility_options_select">
                <select  id="pwaforwp_settings[visibility_included_options]" class="regular-text pwaforwp_visibility_options_select visibility_include_select_type pwa_for_wp-select2">
                    <option value=""><?php echo esc_html__("Select Visibility Type", 'pwa-for-wp');?></option>                    
                </select>
                <div class="include_type_error">&nbsp;</div>
            </td>
            <input type="hidden" name="pwaforwp_visibility_flag" id="pwaforwp_visibility_flag" value="1">
            <td class="include-btn-box"><a class="pwaforwp-include-btn button-primary" onclick="add_included_condition()"><?php echo esc_html__('ADD', 'pwa-for-wp'); ?></a></td>
        </tr> 

        <!-- Excluded -->
        <tr>
            <th><?php echo esc_html__('Excluded On', 'pwa-for-wp'); ?> <i class="dashicons dashicons-plus-alt"></i></th> 
        </tr>

        <tr>
            <td colspan="3">
                    
                <div class="visibility-exclude-target-item-list">
                    <?php $rand = time().wp_rand(000, 999);
                    if(!empty($settings['exclude_targeting_type'])) {
                        $expo_exclude_type = explode(',', $settings['exclude_targeting_type']);
                        $expo_exclude_data = explode(',', $settings['exclude_targeting_value']);
                        for ($i=0; $i < count($expo_exclude_type); $i++) {
                            echo '<span class="pwaforwp-visibility-target-icon-'.esc_attr($rand).'"><input type="hidden" name="exclude_targeting_type" value="'.esc_attr($expo_exclude_type[$i]).'">
                                <input type="hidden" name="exclude_targeting_data" value="'.esc_attr($expo_exclude_data[$i]).'">';
                            $expo_exclude_type_test = pwaforwpRemoveExtraValue($expo_exclude_type[$i]);
                            $expo_exclude_data_test = pwaforwpRemoveExtraValue($expo_exclude_data[$i]);

                            echo '<span class="pwaforwp-visibility-target-item"><span class="visibility-include-target-label">'.esc_html($expo_exclude_type_test.' - '.$expo_exclude_data_test).'</span>
                            <span class="pwaforwp-visibility-target-icon" data-index="0"><span class="dashicons dashicons-no-alt " aria-hidden="true" onclick="removeIncluded_visibility('.esc_attr($rand).')"></span></span></span></span>';
                            $rand++;
                        }
                    }?>
                </div>
            </td>
        </tr>
        <tr>
            <td>
                <select  id="pwaforwp_settings[visibility_excluded_post_options]" class="regular-text pwaforwp_visibility_options_select visibility_options_select_exclude" onchange="get_exclude_pages()">
                    <option value=""><?php echo esc_html__("Select Visibility Type", 'pwa-for-wp');?></option>

                    <?php if(is_array($arrayOPT) && !empty($arrayOPT)) {
                        foreach ($arrayOPT as $key => $opval) {?>
                            <option value="<?php echo esc_attr($key);?>"><?php echo esc_html($opval); ?></option> 
                            
                        <?php }
                    } ?>
                                     
                </select>
                 <div class="exclude_error">&nbsp;</div>
            </td>

            <td class="visibility_options_select">
                <select  class="regular-text pwaforwp_visibility_options_select visibility_exclude_select_type pwa_for_wp-select2_exclude">
                    <option value=""><?php echo esc_html__("Select Visibility Type", 'pwa-for-wp');?></option>
                    
                    
                </select>
                 <div class="exclude_type_error">&nbsp;</div>
            </td>
            <td class="include-btn-box"><a class="pwaforwp-exclude-btn button-primary" onclick="add_exclude_condition()"><?php echo esc_html__('ADD', 'pwa-for-wp'); ?></a></td>
        </tr>   
    <?php
}

function pwaforwp_utm_setting_callback()
{
    // Get Settings
    $settings = pwaforwp_defaultSettings(); 
    $style    = "none";
        
    if(isset($settings['utm_setting']) && $settings['utm_setting']) {
        $style="block";
    }
        
    $utm_source  = $utm_medium = $utm_term = $utm_content = $utm_campaign = ''; 
    $utm_url     = pwaforwp_home_url();
    $utm_url_amp = (function_exists('ampforwp_url_controller')? ampforwp_url_controller(pwaforwp_home_url()) : pwaforwp_home_url()."amp");
        
    if(isset($settings['utm_details'])) {
            
        $utm_source     = $settings['utm_details']['utm_source'];
        $utm_medium     = $settings['utm_details']['utm_medium'];
                $utm_campaign   = $settings['utm_details']['utm_campaign'];
        $utm_term       = $settings['utm_details']['utm_term'];
        $utm_content    = $settings['utm_details']['utm_content'];
                
        $queryArg['utm_source']   = $utm_source;
        $queryArg['utm_medium']   = $utm_medium;
                $queryArg['utm_campaign'] = $utm_campaign;
        $queryArg['utm_term']     = $utm_term;
        $queryArg['utm_content']  = $utm_content;
                
        $queryArg    = array_filter($queryArg);
        $utm_url     = $utm_url."?".http_build_query($queryArg);
        $utm_url_amp = $utm_url_amp."?".http_build_query($queryArg);

    }
        
    $queryArg = 'utm_source=&utm_medium=&utm_medium=&utm_term=&utm_content'
                
    ?>
                
    <label><input type="checkbox" name="pwaforwp_settings[utm_setting]" id="pwaforwp_settings_utm_setting" class="" <?php echo (isset($settings['utm_setting']) &&  $settings['utm_setting'] == 1 ? 'checked="checked"' : ''); ?> data-uncheck-val="0" value="1"><?php echo esc_html__('Enable UTM Tracking', 'pwa-for-wp'); ?></label>
    <p> <?php echo esc_html__('To identify users are coming from your App', 'pwa-for-wp'); ?></p>
    <table class="form-table">
        <tr class="pwawp_utm_values_class" style="display:<?php echo esc_attr($style); ?>;">
            <th><?php echo esc_html__('UTM Source', 'pwa-for-wp'); ?></th>
            <td><input type="text" name="pwaforwp_settings[utm_details][utm_source]" value="<?php echo esc_attr($utm_source); ?>" data-val="<?php echo esc_attr($utm_source); ?>"/></td>
        </tr>
        <tr class="pwawp_utm_values_class" style="display:<?php echo esc_attr($style); ?>;">
            <th><?php echo esc_html__('UTM Medium', 'pwa-for-wp'); ?></th>
            <td><input type="text" name="pwaforwp_settings[utm_details][utm_medium]" value="<?php echo esc_attr($utm_medium); ?>" data-val="<?php echo esc_attr($utm_medium); ?>"/></td>
        </tr>
                <tr class="pwawp_utm_values_class" style="display:<?php echo esc_attr($style); ?>;">
            <th><?php echo esc_html__('UTM Campaign', 'pwa-for-wp'); ?></th>
            <td><input type="text" name="pwaforwp_settings[utm_details][utm_campaign]" value="<?php echo esc_attr($utm_campaign); ?>" data-val="<?php echo esc_attr($utm_campaign); ?>"/></td>
        </tr>
        <tr class="pwawp_utm_values_class" style="display:<?php echo esc_attr($style); ?>;">
            <th><?php echo esc_html__('UTM Term', 'pwa-for-wp'); ?></th>
            <td><input type="text" name="pwaforwp_settings[utm_details][utm_term]" value="<?php echo esc_attr($utm_term); ?>" data-val="<?php echo esc_attr($utm_term); ?>"/></td>
        </tr>
        <tr class="pwawp_utm_values_class" style="display:<?php echo esc_attr($style); ?>;">
            <th><?php echo esc_html__('UTM Content', 'pwa-for-wp'); ?></th>
            <td><input type="text" name="pwaforwp_settings[utm_details][utm_content]" value="<?php echo esc_attr($utm_content); ?>" data-val="<?php echo esc_attr($utm_content); ?>"/></td>
        </tr>
        <tr class="pwawp_utm_values_class expectedValues" style="display:<?php echo esc_attr($style); ?>;">
            <th><?php echo esc_html__('UTM Non-amp Url', 'pwa-for-wp'); ?></th>
            <td><code><?php echo esc_url($utm_url); ?></code></td>
        </tr>
        <tr class="pwawp_utm_values_class expectedValues" style="display:<?php echo esc_attr($style); ?>;">
            <th><?php echo esc_html__('UTM amp Url', 'pwa-for-wp'); ?></th>
            <td><code><?php echo esc_url($utm_url_amp); ?></code></td>
        </tr>
    </table>
    <input type="hidden" name="pwaforwp_settings[utm_details][pwa_utm_change_track]" id="pwa-utm_change_track" value="0">
    <?php
}

function pwaforwp_offline_google_setting_callback()
{
    // Get Settings
    $settings = pwaforwp_defaultSettings(); 
    ?>
        
    <input type="checkbox" name="pwaforwp_settings[offline_google_setting]" id="pwaforwp_settings[offline_google_setting]" class="" <?php echo (isset($settings['offline_google_setting']) &&  $settings['offline_google_setting'] == 1 ? 'checked="checked"' : ''); ?> data-uncheck-val="0" value="1">
    <p><?php echo esc_html__('Offline analytics is a module that will use background sync to ensure that requests to Google Analytics are made regardless of the current network condition', 'pwa-for-wp'); ?></p>
    <?php
}
function pwaforwp_offline_message_setting_callback()
{
    // Get Settings
    $settings = pwaforwp_defaultSettings();
    $offline_message_checked = 'checked="checked';
    if(!isset($settings['offline_message_setting']) || $settings['offline_message_setting'] == 0) {
        $offline_message_checked = '';
    }
    ?>
        
    <input type="checkbox" name="pwaforwp_settings[offline_message_setting]" id="pwaforwp_settings[offline_message_setting]" class="" <?php echo esc_attr($offline_message_checked); ?> data-uncheck-val="0" value="1">
    <p><?php echo esc_html__('To check whether user is offline and display message You are offline', 'pwa-for-wp'); ?></p>
    <?php
}
function pwaforwp_scrollbar_setting_callback()
{
        // Get Settings
        $settings = pwaforwp_defaultSettings();
        $scrollbar_checked = 'checked="checked';
    if(!isset($settings['scrollbar_setting']) || $settings['scrollbar_setting'] == 0) {
            $scrollbar_checked = '';
    }
    ?>

        <input type="checkbox" name="pwaforwp_settings[scrollbar_setting]" id="pwaforwp_settings[scrollbar_setting]" class="" <?php echo esc_attr($scrollbar_checked); ?> data-uncheck-val="0" value="1">
        <p><?php echo esc_html__('To hide scrollbar in pwa', 'pwa-for-wp'); ?></p>
        <?php
}

function pwaforwp_enable_pull_to_refresh_setting_callback()
{
        $settings = pwaforwp_defaultSettings();
        $checked = '';
    if(isset($settings['enable_pull_to_refresh']) && $settings['enable_pull_to_refresh'] == 1 ) {
            $checked = 'checked';
    }
    ?>
        <input type="checkbox" name="pwaforwp_settings[enable_pull_to_refresh]" id="pwaforwp_settings[enable_pull_to_refresh]" class="" <?php echo esc_attr($checked); ?> data-uncheck-val="0" value="1">
        <p><?php echo esc_html__('Reloads the page when pulled down on touch devices.', 'pwa-for-wp'); ?></p>
        <?php
}

function pwaforwp_force_rememberme_setting_callback()
{
    // Get Settings
    $settings = pwaforwp_defaultSettings();
    $rememberme_checked = 'checked="checked';
    if(!isset($settings['force_rememberme']) || $settings['force_rememberme'] == 0) {
        $rememberme_checked = '';
    }
    ?>
        
    <input type="checkbox" name="pwaforwp_settings[force_rememberme]" id="pwaforwp_settings[force_rememberme]" class="" <?php echo esc_attr($rememberme_checked); ?> data-uncheck-val="0" value="1">
    <p><?php echo esc_html__('This option forces remember me while log in. Use this option when user is getting logged out while reopening PWA app.', 'pwa-for-wp'); ?></p>
    <?php
}
function pwaforwp_prefetch_manifest_setting_callback()
{
    // Get Settings
    $settings = pwaforwp_defaultSettings(); 
    ?>
        
    <input type="checkbox" name="pwaforwp_settings[prefetch_manifest_setting]" id="pwaforwp_settings[prefetch_manifest_setting]" class="" <?php echo (isset($settings['prefetch_manifest_setting']) &&  $settings['prefetch_manifest_setting'] == 1 ? 'checked="checked"' : ''); ?> data-uncheck-val="0" value="1">
    <p><?php echo esc_html__('Prefetch manifest URLs provides some control over the request priority', 'pwa-for-wp'); ?></p>
    <?php
}
function pwaforwp_force_update_sw_setting_callback()
{
    // Get Settings
    $settings = pwaforwp_defaultSettings(); 
    if(isset($settings['force_update_sw_setting'])) { 
        if(!version_compare($settings['force_update_sw_setting'], PWAFORWP_PLUGIN_VERSION, '>=') ) {
            $settings['force_update_sw_setting'] = PWAFORWP_PLUGIN_VERSION;
        }
        // echo esc_attr($settings['force_update_sw_setting']);
        $force_update_sw_setting_value = $settings['force_update_sw_setting'];
    }else{ 
        $force_update_sw_setting_value = PWAFORWP_PLUGIN_VERSION;
    }    
    ?>
        <label><input type="text" id="pwaforwp_settings[force_update_sw_setting]" name="pwaforwp_settings[force_update_sw_setting]" value="<?php echo esc_attr($force_update_sw_setting_value); ?>"></label>      
        <code><?php echo esc_html__('Current Version', 'pwa-for-wp'); ?> <?php echo esc_attr($force_update_sw_setting_value); ?></code>  
    <p><?php echo esc_html__('Change the version. It will automatically update the service worker for all the users', 'pwa-for-wp'); ?></p>
    <?php
}

function pwaforwp_cdn_setting_callback()
{
    // Get Settings
    $settings = pwaforwp_defaultSettings(); 
    ?>
    <input type="checkbox" name="pwaforwp_settings[cdn_setting]" id="pwaforwp_settings_cdn_setting" class="" <?php echo (isset($settings['cdn_setting']) &&  $settings['cdn_setting'] == 1 ? 'checked="checked"' : ''); ?> value="1">
    <p><?php echo esc_html__('This helps you remove conflict with the CDN', 'pwa-for-wp'); ?></p>
    <?php
}

function pwaforwp_reset_setting_callback()
{        
    ?>              
        <button class="button pwaforwp-reset-settings">
            <?php echo esc_html__('Reset', 'pwa-for-wp'); ?>
        </button>
        
    <?php
}

function pwaforwp_cleandataonuninstall_setting_callback()
{  
    // Get Settings
    $settings = pwaforwp_defaultSettings(); 
    ?>            
        <input type="checkbox" name="pwaforwp_settings[pwa_uninstall_data]" id="pwaforwp_settings_navigation_uninstall_setting" class="" <?php echo (isset($settings['pwa_uninstall_data']) &&  $settings['pwa_uninstall_data'] == 1 ? 'checked="checked"' : ''); ?> value="1">
        <p><?php echo esc_html__('Check this box if you would like to completely remove all of its data when the plugin is deleted.', 'pwa-for-wp'); ?></p>
        
    <?php
}

function pwaforwp_loading_setting_callback()
{    
    
        $settings = pwaforwp_defaultSettings();
        
    ?>              
        <input type="checkbox" name="pwaforwp_settings[loading_icon]" id="pwaforwp_settings_loading_icon_setting" class="" <?php echo (isset($settings['loading_icon']) &&  $settings['loading_icon'] == 1 ? 'checked="checked"' : ''); ?> value="1">
    <p><?php echo esc_html__('This helps show loading icon on page or post load', 'pwa-for-wp'); ?></p>
        
    <?php
}
function pwaforwp_loading_color_setting_callback()
{    
    $settings = pwaforwp_defaultSettings(); ?>
    <input type="text" name="pwaforwp_settings[loading_icon_color]" id="pwaforwp_settings[loading_icon_color]" class="pwaforwp-colorpicker" data-alpha-enabled="true" value="<?php echo isset($settings['loading_icon_color']) ? esc_attr($settings['loading_icon_color']) : '#3498db'; ?>" data-default-color="#3498db">
    <p><?php echo esc_html__('Change the icon color of loader', 'pwa-for-wp'); ?></p><?php
}
function pwaforwp_loading_background_color_setting_callback()
{    
    $settings = pwaforwp_defaultSettings(); ?>
    <input type="text" name="pwaforwp_settings[loading_icon_bg_color]" id="pwaforwp_settings[loading_icon_bg_color]" class="pwaforwp-colorpicker" data-alpha-enabled="true" value="<?php echo isset($settings['loading_icon_bg_color']) ? esc_attr($settings['loading_icon_bg_color']) : '#ffffff'; ?>" data-default-color="#ffffff">
    <p><?php echo esc_html__('Change the background color of loader', 'pwa-for-wp'); ?></p><?php
}
function pwaforwp_loading_display_inpwa_setting_callback()
{    
    $settings = pwaforwp_defaultSettings();
    ?>
    <label><input type="checkbox" name="pwaforwp_settings[loading_icon_display_pwa]" id="pwaforwp_settings[loading_icon_display_pwa]" class="" value="1" <?php echo isset($settings['loading_icon_display_pwa']) && $settings['loading_icon_display_pwa']==1 ? 'checked' : ''; ?> ><?php echo esc_html__('Only on PWA', 'pwa-for-wp'); ?></label>
    <?php
}
function pwaforwp_loading_display_setting_callback()
{    
    $settings = pwaforwp_defaultSettings(); 
    if(!isset($settings['loading_icon_display_desktop']) && $settings['loading_icon']==1) {
        $settings['loading_icon_display_desktop'] = 1;
    }
    if(!isset($settings['loading_icon_display_mobile']) && $settings['loading_icon']==1) {
        $settings['loading_icon_display_mobile'] = 1;
    }
    if(!isset($settings['loading_icon_display_admin']) && $settings['loading_icon']==1) {
        $settings['loading_icon_display_admin'] = 0;
    }
    ?>
    <label><input type="checkbox" name="pwaforwp_settings[loading_icon_display_desktop]" id="pwaforwp_settings[loading_icon_display_desktop]" class="" value="1" <?php echo isset($settings['loading_icon_display_desktop']) && $settings['loading_icon_display_desktop']==1 ? 'checked' : ''; ?> ><?php echo esc_html__('Desktop', 'pwa-for-wp'); ?></label>
    <label><input type="checkbox" name="pwaforwp_settings[loading_icon_display_mobile]" id="pwaforwp_settings[loading_icon_display_mobile]" class="" value="1" <?php echo isset($settings['loading_icon_display_mobile']) && $settings['loading_icon_display_mobile']==1 ? 'checked' : ''; ?> ><?php echo esc_html__('Mobile', 'pwa-for-wp'); ?></label>
    <label><input type="checkbox" name="pwaforwp_settings[loading_icon_display_admin]" id="pwaforwp_settings[loading_icon_display_admin]" class="" value="1" <?php echo isset($settings['loading_icon_display_admin']) && $settings['loading_icon_display_admin']==1 ? 'checked' : ''; ?> ><?php echo esc_html__('Admin', 'pwa-for-wp'); ?></label>
    <?php
}

function pwaforwp_cache_external_links_setting_callback()
{
    // Get Settings
    $settings = pwaforwp_defaultSettings(); 
    ?>
    <input type="checkbox" name="pwaforwp_settings[external_links_setting]" id="pwaforwp_settings_external_links_setting" class="" <?php echo (isset($settings['external_links_setting']) &&  $settings['external_links_setting'] == 1 ? 'checked="checked"' : ''); ?> value="1">
    <p><?php echo esc_html__('Caches external link\'s resource which are in html', 'pwa-for-wp'); ?></p>
    <?php
}

//Design Settings
function pwaforwp_background_color_callback()
{
    // Get Settings
    $settings = pwaforwp_defaultSettings(); ?>
    
    <!-- Background Color -->
        <input type="text" name="pwaforwp_settings[background_color]" id="pwaforwp_settings[background_color]" class="pwaforwp-colorpicker" data-alpha-enabled="true" value="<?php echo isset($settings['background_color']) ? esc_attr($settings['background_color']) : '#D5E0EB'; ?>" data-default-color="#D5E0EB">
        <p class="description"><?php echo esc_html__('Select the splash screen background color.', 'pwa-for-wp'); ?></p>
    <?php
}
function pwaforwp_theme_color_callback()
{
    // Get Settings
    $settings = pwaforwp_defaultSettings(); ?>
    
    <!-- Background Color -->
    <input type="text" name="pwaforwp_settings[theme_color]" id="pwaforwp_settings[theme_color]" class="pwaforwp-colorpicker" data-alpha-enabled="true" value="<?php echo isset($settings['theme_color']) ? esc_attr($settings['theme_color']) : '#D5E0EB'; ?>" data-default-color="#D5E0EB">
    <p class="description"><?php echo esc_html__('Select the theme color for the browser toolbar.', 'pwa-for-wp'); ?></p>
    <?php
}

function pwaforwp_push_notification_callback()
{    
    
    $settings = pwaforwp_defaultSettings(); 
    $selectedService = 'pushnotifications_io';
    $pushnotifications_style = 'style="display:block;"';
    $fcm_service_style = 'style="display:none;"'; 
    if((isset($settings['fcm_server_key']) && !empty($settings['fcm_server_key']) && !isset($settings['notification_options'])) 
        || (isset($settings['notification_options']) && $settings['notification_options']=="fcm_push")
    ) {
        $selectedService = "fcm_push";
        $pushnotifications_style = 'style="display:none;"';
        $fcm_service_style = 'style="display:block;"';
    }
    if(isset($settings['notification_options']) ) {
        $selectedService = $settings['notification_options'];
        if(empty($selectedService)) {
            $selectedService = "";
            $pushnotifications_style = 'style="display:none;"';
            $fcm_service_style = 'style="display:none;"';
        }
    }


    ?>        
        
        <div class="pwafowwp-server-key-section">
            <table class="pwaforwp-pn-options">
                <tbody>
                    <th><?php echo esc_html__('Push notification integration', 'pwa-for-wp');?></th>
                    <td>
                        <select name="pwaforwp_settings[notification_options]" id="pwaforwp_settings[notification_options]" class="regular-text pwaforwp-pn-service">
                            <option value=""><?php echo esc_html__('Select', 'pwa-for-wp') ?></option>
                            <option value="pushnotifications_io" <?php selected('pushnotifications_io', $selectedService) ?>><?php echo esc_html__('PushNotifications.io (Recommended)', 'pwa-for-wp') ?></option>
                            <option value="fcm_push" <?php selected('fcm_push', $selectedService) ?> ><?php echo esc_html__('FCM push notification', 'pwa-for-wp') ?></option>
                        </select>
                    </td>
                </tbody>
            </table>
            <table class="pwaforwp-push-notificatoin-table" <?php 
			// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- using style in variable no need to esc.
            echo $fcm_service_style; ?>>
                <tbody>
                   
                    <tr>
                        <th><?php echo esc_html__('Firebase Config', 'pwa-for-wp') ?></th>  
                        <td>
                            <p><?php echo esc_html__('Note: You need to Create a new firebase project on ', 'pwa-for-wp') ?> <a href="https://firebase.google.com/" target="_blank"><?php echo esc_html__('firebase', 'pwa-for-wp') ?></a> <?php echo esc_html__('console, its completly free by google with some limitations.', 'pwa-for-wp') ?></p>
                            <textarea class="regular-text" placeholder="{ <?php echo "\n"; ?>apiKey: '<Your Api Key>', <?php echo "\n"; ?>authDomain: '<Your Auth Domain>',<?php echo "\n"; ?>databaseURL: '<Your Database URL>',<?php echo "\n"; ?>projectId: '<Your Project Id>',<?php echo "\n"; ?>storageBucket: '<Your Storage Bucket>', <?php echo "\n"; ?>messagingSenderId: '<Your Messaging Sender Id>' <?php echo "\n"; ?>}" rows="8" cols="60" id="pwaforwp_settings[fcm_config]" name="pwaforwp_settings[fcm_config]"><?php echo isset($settings['fcm_config']) ? esc_attr($settings['fcm_config']) : ''; ?></textarea>

                            <p><?php echo esc_html__('Go to Firebase Console → Project Settings → Your Apps. Create a web app. You will get the config under SDK setup and configuration.', 'pwa-for-wp') ?></p>
                           
                        </td>
                    </tr> 
                    <tr>
                        <th><?php echo esc_html__('FCM Service Account', 'pwa-for-wp'); ?></th>
                        <td>
                            <input type="file" id="fcm_service_account_json" accept=".json">
                            <?php if (!empty($settings['fcm_server_key'])) : ?>
                                <p class="description"><b><?php echo esc_html__('File uploaded:', 'pwa-for-wp') . ' <span id="fcm_server_key_url" style="color:#000;">' . esc_html(basename($settings['fcm_server_key'])); ?></span></b></p>
                            <?php endif; ?>
                            <p class="description"><?php echo esc_html__('Upload your Firebase service account JSON file. It will be stored securely.', 'pwa-for-wp'); ?></p>
                            <p class="description"><?php echo esc_html__('Go to Firebase Console → Project Settings → Cloud Messaging → Manage Service Accounts. Select the Service account and click three dots then Manage Keys . Create a new key and project-name.json file will be downloaded automatically', 'pwa-for-wp'); ?></p>
                        </td>
                    </tr>
                    <tr>
                        <th><?php echo esc_html__('FCM Push Notification Icon', 'pwa-for-wp') ?></th>  
                        <td>
                            <input type="text" name="pwaforwp_settings[fcm_push_icon]" id="pwaforwp_settings[fcm_push_icon]" class="pwaforwp-fcm-push-icon regular-text" size="50" value="<?php echo isset($settings['fcm_push_icon']) ? esc_attr(pwaforwp_https($settings['fcm_push_icon'])) : ''; ?>">
                            <button type="button" class="button pwaforwp-fcm-push-icon-upload" data-editor="content">
                                <span class="dashicons dashicons-format-image" style="margin-top: 4px;"></span> <?php echo esc_html__('Choose Icon', 'pwa-for-wp'); ?> 
                            </button>
                            <p><?php echo esc_html__('Change Firebase push notification icon. Default: PWA icon', 'pwa-for-wp') ?> </p>
                        </td>
                    </tr>
                    <tr>
                        <th><?php echo esc_html__('FCM Notification Budge Icon', 'pwa-for-wp') ?></th>  
                        <td>
                            <input type="text" name="pwaforwp_settings[fcm_budge_push_icon]" id="pwaforwp_settings[fcm_budge_push_icon]" class="pwaforwp-fcm-push-budge-icon regular-text" value="<?php echo isset($settings['fcm_budge_push_icon']) ? esc_attr(pwaforwp_https($settings['fcm_budge_push_icon'])) : ''; ?>">
                            <button type="button" class="button pwaforwp-fcm-push-budge-icon-upload" data-editor="content">
                                <span class="dashicons dashicons-format-image" style="margin-top: 4px;"></span> <?php echo esc_html__('Choose Icon', 'pwa-for-wp'); ?> 
                            </button>
                            <p><?php echo esc_html__('Change Firebase push notification budge icon 96x96. Default: Chrome icon', 'pwa-for-wp') ?> </p>
                        </td>
                    </tr>                                                            
                </tbody>   
            </table>                   
            <div class="pwaforwp-pn-recommended-options" <?php 
			// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- its has style only.
            echo $pushnotifications_style; ?>>
                <div class="notification-banner" style="width:90%">
                        <?php if(class_exists('Push_Notification_Admin')) { 
                            $auth_settings = push_notification_auth_settings();
                            if(!isset($auth_settings['user_token'])) {
                                echo '<div class="pwaforwp-center"><p>'.esc_html__('This feature requires to setup Push Notification', 'pwa-for-wp').' </p> <a href="'.esc_url_raw(admin_url('admin.php?page=push-notification')).'" target="_blank" class="button button-primary">'.esc_html__('Go to setup', 'pwa-for-wp').'</a></div>';
                            }else{
                                echo '<div class="pwaforwp-center"><p>'.esc_html__('Push notifications has it\'s separate options view', 'pwa-for-wp').'</p><a href="'. esc_url_raw(admin_url('admin.php?page=push-notification')).'" class="button button-primary">'.esc_html__(' View Settings', 'pwa-for-wp').'</a></div>';
                            }
                            ?>
                        
                        <?php } ?>
</div>
</div>
</div>
        <div class="pwaforwp-notification-condition-section" <?php 
		// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- using style only in this variable.
        echo $fcm_service_style; ?> >
        <div>
            <h2><?php echo esc_html__('Send Notification On', 'pwa-for-wp') ?></h2>
            <table class="pwaforwp-push-notificatoin-table">
                <tbody>
                    <tr>
                        <th><?php echo esc_html__('Add New Post', 'pwa-for-wp') ?></th>  
                        <td>
                            <input  type="checkbox" name="pwaforwp_settings[on_add_post]" id="pwaforwp_settings[on_add_post]" class="pwaforwp-fcm-checkbox" <?php echo (isset($settings['on_add_post']) &&  $settings['on_add_post'] == 1 ? 'checked="checked"' : ''); ?> value="1">
                            <?php
                            if(isset($settings['on_add_post']) && $settings['on_add_post'] == 1) {
                                echo '<p>'.esc_html__('Notification Title', 'pwa-for-wp').' <input type="text" name="pwaforwp_settings[on_add_post_notification_title]" id="on_add_post_notification_title" placeholder="'.esc_attr__('New Post', 'pwa-for-wp').'" value="'.esc_attr($settings['on_add_post_notification_title']).'"></p>';   
                            }else{
                                echo  '<p class="pwaforwp-hide">'.esc_html__('Notification Title', 'pwa-for-wp').' <input type="text" name="pwaforwp_settings[on_add_post_notification_title]" id="on_add_post_notification_title" placeholder="'.esc_attr__('New Post', 'pwa-for-wp').'" value="'.esc_attr($settings['on_add_post_notification_title']).'"></p>';  
                            }
                            ?>
                            
                        </td>
                    </tr>
                    <tr>
                        <th><?php echo esc_html__('Update Post', 'pwa-for-wp') ?></th>  
                        <td><input type="checkbox" name="pwaforwp_settings[on_update_post]" id="pwaforwp_settings[on_update_post]" class="pwaforwp-fcm-checkbox" <?php echo (isset($settings['on_update_post']) &&  $settings['on_update_post'] == 1 ? 'checked="checked"' : ''); ?> value="1">
                            <?php
                            if(isset($settings['on_update_post']) && $settings['on_update_post']== 1) {
                                echo '<p>'.esc_html__('Notification Title', 'pwa-for-wp').' <input type="text" name="pwaforwp_settings[on_update_post_notification_title]" id="on_update_post_notification_title" placeholder="'.esc_attr__("Update Post", "pwa-for-wp").'" value="'.(isset($settings['on_update_post_notification_title']) ? esc_attr($settings['on_update_post_notification_title']): '').'"></p>';   
                            }else{
                                echo  '<p class="pwaforwp-hide">'.esc_html__('Notification Title', 'pwa-for-wp').' <input type="text" name="pwaforwp_settings[on_update_post_notification_title]" id="on_update_post_notification_title" placeholder="'.esc_attr__("Update Post", "pwa-for-wp").'" value="'.(isset($settings['on_update_post_notification_title']) ? esc_attr($settings['on_update_post_notification_title']) : '').'"></p>';  
                            }
                            ?>
                        </td>
                    </tr>
                     <tr>
                        <th><?php echo esc_html__('Add New Page', 'pwa-for-wp') ?></th>  
                        <td><input type="checkbox" name="pwaforwp_settings[on_add_page]" id="pwaforwp_settings[on_add_page]" class="pwaforwp-fcm-checkbox" <?php echo (isset($settings['on_add_page']) &&  $settings['on_add_page'] == 1 ? 'checked="checked"' : ''); ?> value="1">
                            
                            <?php
                            if(isset($settings['on_add_page']) && $settings['on_add_page'] == 1) {
                                echo '<p>'.esc_html__('Notification Title', 'pwa-for-wp').' <input type="text" name="pwaforwp_settings[on_add_page_notification_title]" id="on_add_page_notification_title" placeholder="'.esc_attr__("New Page", "pwa-for-wp").'" value="'.(isset($settings['on_add_page_notification_title']) ? esc_attr($settings['on_add_page_notification_title']) : '').'"></p>';   
                            }else{
                                echo  '<p class="pwaforwp-hide">'.esc_html__('Notification Title', 'pwa-for-wp').' <input type="text" name="pwaforwp_settings[on_add_page_notification_title]" id="on_add_page_notification_title" placeholder="'.esc_attr__("New Page", "pwa-for-wp").'" value="'.(isset($settings['on_add_page_notification_title']) ? esc_attr($settings['on_add_page_notification_title']) : '').'"></p>';  
                            }
                            ?>
                            
                        </td>
                    </tr>
                    <tr>
                        <th><?php echo esc_html__('Update Page', 'pwa-for-wp') ?></th>  
                        <td><input type="checkbox" name="pwaforwp_settings[on_update_page]" id="pwaforwp_settings[on_update_page]" class="pwaforwp-fcm-checkbox" <?php echo (isset($settings['on_update_page']) &&  $settings['on_update_page'] == 1 ? 'checked="checked"' : ''); ?> value="1">
                            <?php
                            if(isset($settings['on_update_page']) && $settings['on_update_page'] == 1) {
                                echo '<p>'.esc_html__('Notification Title', 'pwa-for-wp').' <input type="text" name="pwaforwp_settings[on_update_page_notification_title]" id="on_update_page_notification_title" placeholder="'.esc_attr__("Update Post", "pwa-for-wp").'" value="'.(isset($settings['on_update_page_notification_title']) ? esc_attr($settings['on_update_page_notification_title']) : '').'"></p>';   
                            }else{
                                echo  '<p class="pwaforwp-hide">'.esc_html__('Notification Title', 'pwa-for-wp').' <input type="text" name="pwaforwp_settings[on_update_page_notification_title]" id="on_update_page_notification_title" placeholder="'.esc_attr__("Update Post", "pwa-for-wp").'" value="'.(isset($settings['on_update_page_notification_title']) ? esc_attr($settings['on_update_page_notification_title']) : '').'"></p>';  
                            }
                            ?>
                        </td>
                    </tr>                                                            
                </tbody>   
            </table>                   
        </div>        
        <div>
            <h2><?php echo esc_html__('Send Manual Notification', 'pwa-for-wp') ?></h2>
            <table class="pwaforwp-push-notificatoin-table">
                <tbody>
                    
                    <tr>
                        <th><?php echo esc_html__('Title', 'pwa-for-wp') ?>:<br/><input style="width: 100%" placeholder="<?php esc_attr__("Title", "pwa-for-wp") ?>" type="text" id="pwaforwp_notification_message_title" name="pwaforwp_notification_message_title" value="<?php echo esc_attr(get_bloginfo()); ?>">
                            <br>
                               
                        </th>  
                        <td></td>
                    </tr>
                     <tr>
                        <th>
                            <?php echo esc_html__('Redirection Url Onclick of notification', 'pwa-for-wp') ?>:<br/>
                            <input style="width: 100%" placeholder="<?php esc_attr__("URL", "pwa-for-wp") ?>" type="text" id="pwaforwp_notification_message_url" name="pwaforwp_notification_message_url" value="<?php echo esc_attr(pwaforwp_home_url()); ?>">
                            <br>
                               
                        </th>  
                        <td></td>
                    </tr>
                    <tr>
                        <th>
                            <?php echo esc_html__('Image Url', 'pwa-for-wp') ?>:<br/>
                            <input style="width: 100%" placeholder="<?php esc_attr__("Image URL", "pwa-for-wp") ?>" type="text" id="pwaforwp_notification_message_image_url" name="pwaforwp_notification_message_image_url" value="">
                            <br>
                               
                        </th>  
                        <td></td>
                    </tr>   
                    <tr>
                        <th><?php echo esc_html__('Message', 'pwa-for-wp') ?>:<br/><textarea rows="5" cols="60" id="pwaforwp_notification_message" name="pwaforwp_notification_message"> </textarea>
                            <button class="button pwaforwp-manual-notification"> <?php echo esc_html__('Send', 'pwa-for-wp'); ?> </button>
                            <br>
                                <div class="pwaforwp-notification-success pwa_hide"></div>
                                <p class="pwaforwp-notification-error pwa_hide"></p>
                        </th>  
                        <td></td>
                    </tr>
                                                                                               
                </tbody>   
            </table>                   
        </div>
        </div>    
    <?php
}

function pwaforwp_custom_banner_design_callback()
{
    
        $settings = pwaforwp_defaultSettings(); ?>           
        
        <h2><?php echo esc_html__('Custom Add To Homescreen Customization', 'pwa-for-wp') ?></h2>
        <table class="" style="display: block;">
            <tr><th><strong><?php echo esc_html__('Title', 'pwa-for-wp'); ?></strong></th><td><input type="text" name="pwaforwp_settings[custom_banner_title]" id="pwaforwp_settings[custom_banner_title]" class="" value="<?php echo isset($settings['custom_banner_title']) ? esc_attr($settings['custom_banner_title']) : 'Add '.esc_attr(get_bloginfo()).' to your Homescreen!'; ?>"></td></tr> 
            <tr><th><strong><?php echo esc_html__('Button Text', 'pwa-for-wp'); ?></strong></th><td><input type="text" name="pwaforwp_settings[custom_banner_button_text]" id="pwaforwp_settings[custom_banner_button_text]" class="" value="<?php echo isset($settings['custom_banner_button_text']) ? esc_attr($settings['custom_banner_button_text']) : 'Add'; ?>"></td></tr> 
            <tr><th><strong><?php echo esc_html__('Banner Background Color', 'pwa-for-wp'); ?></strong></th><td><input type="text" name="pwaforwp_settings[custom_banner_background_color]" id="pwaforwp_settings[custom_banner_background_color]" class="pwaforwp-colorpicker" data-alpha-enabled="true" value="<?php echo isset($settings['custom_banner_background_color']) ? esc_attr($settings['custom_banner_background_color']) : '#D5E0EB'; ?>" data-default-color="#D5E0EB"></td></tr> 
            <tr><th><strong><?php echo esc_html__('Banner Title Color', 'pwa-for-wp'); ?></strong></th><td><input type="text" name="pwaforwp_settings[custom_banner_title_color]" id="pwaforwp_settings[custom_banner_title_color]" class="pwaforwp-colorpicker" data-alpha-enabled="true" value="<?php echo isset($settings['custom_banner_title_color']) ? esc_attr($settings['custom_banner_title_color']) : '#000'; ?>" data-default-color="#000"></td></tr> 
            <tr><th><strong><?php echo esc_html__('Button Text Color', 'pwa-for-wp'); ?></strong></th><td><input type="text" name="pwaforwp_settings[custom_banner_btn_text_color]" id="pwaforwp_settings[custom_banner_btn_text_color]" class="pwaforwp-colorpicker" data-alpha-enabled="true" value="<?php echo isset($settings['custom_banner_btn_text_color']) ? esc_attr($settings['custom_banner_btn_text_color']) : '#fff'; ?>" data-default-color="#fff"></td></tr> 
            <tr><th><strong><?php echo esc_html__('Button Background Color', 'pwa-for-wp'); ?></strong></th><td><input type="text" name="pwaforwp_settings[custom_banner_btn_color]" id="pwaforwp_settings[custom_banner_btn_color]" class="pwaforwp-colorpicker" data-alpha-enabled="true" value="<?php echo isset($settings['custom_banner_btn_color']) ? esc_attr($settings['custom_banner_btn_color']) : '#006dda'; ?>" data-default-color="#006dda"></td></tr>                         
        </table>
        <?php
}

//General settings
function pwaforwp_app_name_callback()
{
    // Get Settings
    $settings = pwaforwp_defaultSettings(); ?>
    
    <fieldset>
        <input type="text" name="pwaforwp_settings[app_blog_name]" class="regular-text" value="<?php if (isset($settings['app_blog_name']) && ( ! empty($settings['app_blog_name']) ) ) { echo esc_attr($settings['app_blog_name']);
} ?>"/>
    </fieldset>

    <?php
}

function pwaforwp_app_short_name_callback()
{
    // Get Settings
    $settings = pwaforwp_defaultSettings(); ?>
    
    <fieldset>
        <input type="text" name="pwaforwp_settings[app_blog_short_name]" class="regular-text" value="<?php if (isset($settings['app_blog_short_name']) && ( ! empty($settings['app_blog_short_name']) ) ) { echo esc_attr($settings['app_blog_short_name']);
} ?>"/>
        
    </fieldset>
    <?php
}

function pwaforwp_description_callback()
{
    // Get Settings
    $settings = pwaforwp_defaultSettings(); ?>
    <fieldset>
        <input type="text" name="pwaforwp_settings[description]" class="regular-text" value="<?php if (isset($settings['description']) && ( ! empty($settings['description']) ) ) { echo esc_attr($settings['description']);
} ?>"/>                
    </fieldset>

    <?php
}

function pwaforwp_app_icon_callback()
{
    // Get Settings
    $settings = pwaforwp_defaultSettings(); ?>
    
    <!-- Application Icon -->
        <input type="text" name="pwaforwp_settings[icon]" id="pwaforwp_settings[icon]" class="pwaforwp-icon regular-text" size="50" value="<?php echo isset($settings['icon']) ? esc_attr(pwaforwp_https($settings['icon'])) : ''; ?>">
    <button type="button" class="button pwaforwp-icon-upload" data-editor="content">
        <span class="dashicons dashicons-format-image" style="margin-top: 4px;"></span> <?php echo esc_html__('Choose Icon', 'pwa-for-wp'); ?> 
    </button>
    
    <p class="description">
    <?php echo sprintf(
        '%s <strong>%s</strong><br/> %s',
        esc_html__('Icon of your application when installed on the phone. Must be a PNG image exactly', 'pwa-for-wp'),
        esc_html__('192x192 in size.', 'pwa-for-wp'),
        esc_html__('- For Apple mobile exact sizes is necessary', 'pwa-for-wp')
    );
    ?>
    </p>
    <?php
}

function pwaforwp_app_maskable_icon_callback()
{
    // Get Settings
    $settings = pwaforwp_defaultSettings(); ?>
    
    <!-- Application Icon -->
    <input type="text" name="pwaforwp_settings[app_maskable_icon]" id="pwaforwp_settings[app_maskable_icon]" class="pwaforwp-icon regular-text pwaforwp-maskable-input" size="50" value="<?php echo isset($settings['app_maskable_icon']) ? esc_attr(pwaforwp_https($settings['app_maskable_icon'])) : ''; ?>">
    <button type="button" class="button pwaforwp-maskable-icon-upload" data-editor="content">
        <span class="dashicons dashicons-format-image" style="margin-top: 4px;"></span> <?php echo esc_html__('Choose Icon', 'pwa-for-wp'); ?> 
    </button>
    <button type="button" style="background-color: red; border-color: red; color: #fff; display:none;" class="button pwaforwp_js_remove_maskable" > <?php echo esc_html__('Remove', 'pwa-for-wp'); ?></button>
    
    <p class="description">
    <?php echo sprintf(
        '%s <strong>%s</strong><br/> %s',
        esc_html__('Icon of your application when installed on the phone. Must be a PNG image exactly', 'pwa-for-wp'),
        esc_html__('192x192 in size.', 'pwa-for-wp'),
        esc_html__('- For Apple mobile exact sizes is necessary', 'pwa-for-wp')
    );
    ?>
    </p>
    <?php
}

function pwaforwp_monochrome_callback()
{
    // Get Settings
    $settings = pwaforwp_defaultSettings(); ?>
    
    <!-- monochrome Icon -->
        <input type="text" name="pwaforwp_settings[monochrome]" id="pwaforwp_settings[monochrome]" class="pwaforwp-monochrome regular-text" size="50" value="<?php echo isset($settings['monochrome']) ? esc_attr(pwaforwp_https($settings['monochrome'])) : ''; ?>">
    <button type="button" class="button pwaforwp-monochrome-upload" data-editor="content">
        <span class="dashicons dashicons-format-image" style="margin-top: 4px;"></span> <?php echo esc_html__('Choose Monochrome Icon', 'pwa-for-wp'); ?> 
    </button>
    
    <p class="description">
    <?php echo sprintf(
        '%s <strong>%s</strong><br/> %s',
        esc_html__('Monochrome Icon for the application .Must be PNG having transparent background', 'pwa-for-wp'),
        esc_html__('512x512 in size.', 'pwa-for-wp'),
        esc_html__('- For Apple mobile exact sizes is necessary', 'pwa-for-wp')
    );
    ?>
    </p>
    <?php
}

function pwaforwp_splash_icon_callback()
{
    // Get Settings
    $settings = pwaforwp_defaultSettings(); ?>
    
    <!-- Splash Screen Icon -->
        <input type="text" name="pwaforwp_settings[splash_icon]" id="pwaforwp_settings[splash_icon]" class="pwaforwp-splash-icon regular-text" size="50" value="<?php echo isset($settings['splash_icon']) ? esc_attr(pwaforwp_https($settings['splash_icon'])) : ''; ?>">
    <button type="button" class="button pwaforwp-splash-icon-upload" data-editor="content">
        <span class="dashicons dashicons-format-image" style="margin-top: 4px;"></span> <?php echo esc_html__('Choose Icon', 'pwa-for-wp'); ?>
    </button>
    
    <p class="description">
    <?php echo sprintf(
        '%s <strong>%s</strong>',
        esc_html__('Icon displayed on the splash screen of your APPLICATION on supported devices. Must be a PNG image size exactly', 'pwa-for-wp'),
        esc_html__('512x512 in size.', 'pwa-for-wp')
    );
    ?>
    </p>
    <label>
    <input type="checkbox" class="switch_apple_splash_screen" name="pwaforwp_settings[switch_apple_splash_screen]" value="1" <?php if(isset($settings['switch_apple_splash_screen']) && $settings['switch_apple_splash_screen']==1) { echo "checked"; 
} ?> ><?php echo esc_html__('Setup Splash Screen for iOS', 'pwa-for-wp') ?></label>
    <div class="pwaforwp-ios-splash-images" <?php if(isset($settings['switch_apple_splash_screen']) && !$settings['switch_apple_splash_screen']) { echo 'style="display:none"'; 
}?>>
        <div class="field" style="margin-bottom: 10px;">
            <label style="display: inline-block;width: 50%;"><?php echo esc_html__('iOS Splash Screen Method', 'pwa-for-wp') ?></label>
            <select name="pwaforwp_settings[iosSplashScreenOpt]" id="ios-splash-gen-opt">
                <option value=""><?php echo esc_html__('Select', 'pwa-for-wp') ?></option>
                <option value="generate-auto" <?php echo isset($settings['iosSplashScreenOpt']) && $settings['iosSplashScreenOpt']=='generate-auto'? 'selected': ''; ?>><?php echo esc_html__('Automatic', 'pwa-for-wp'); ?></option>
                <option  value="manually" <?php echo isset($settings['iosSplashScreenOpt']) && $settings['iosSplashScreenOpt']=='manually'? 'selected': ''; ?>><?php echo esc_html__('Manual', 'pwa-for-wp'); ?></option>
            </select>
        </div>

    <?php
    $currentpic = $splashIcons = pwaforwp_ios_splashscreen_files_data();
    $previewImg = '';
    if(isset($settings['ios_splash_icon'][key($currentpic)]) ) {
       //phpcs:ignore PluginCheck.CodeAnalysis.ImageFunctions.NonEnqueuedImage
                       $previewImg = '<img src="'.pwaforwp_https($settings['ios_splash_icon'][key($currentpic)]) .'?test='.wp_rand(00, 99).'" width="60" height="40">';
    }
		// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- using custom html.
    echo '<div class="panel pwaforwp-hide" id="generate-auto-1"  style="max-height: 100%;">
				<div class="pwaforwp-ios-splash-screen-creator" style="display:inline-block; width:90%">
					<div class="field"><label>'.esc_html__('Select image (Only PNG)', 'pwa-for-wp').'</label><input type="file" id="file-upload-ios" accept="image/png"><img style="display:none" id="thumbnail"></div>
					<div class="field"><label>'.esc_html__('Background color', 'pwa-for-wp').'</label><input type="text" id="ios-splash-color" value="#FFFFFF"></div>
					<div style="padding-left: 25%;"><input type="button" onclick="pwa_getimageZip(this)" class="button" value="'.esc_attr__('Generate', 'pwa-for-wp').'">
					<span id="pwa-ios-splashmessage" style="font-size:17px"> </span></div>
				</div>
				<div class="splash_preview_wrp" style="display:inline-block; width:9%">'.
				// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- i am using custom html in variable
                $previewImg.'
				</div>
			</div>
			
			';
    ?>
        <div class="panel pwaforwp-hide" id="manually-1" style="max-height: 100%;">
    <?php
    if(is_array($splashIcons) && !empty($splashIcons)) {
        foreach ($splashIcons as $key => $splashValue) {
            
            ?>
            <div class="pwaforwp-ios-splash-images-field">
                <label><?php echo esc_html($splashValue['name']." ($key) [".ucfirst($splashValue['orientation'])."]") ?></label>
                <input type="text" name="pwaforwp_settings[ios_splash_icon][<?php echo esc_attr($key) ?>]" id="pwaforwp_settings[ios_splash_icon][<?php echo esc_attr($key) ?>]" class="pwaforwp-splash-icon regular-text" size="50" value="<?php echo isset($settings['ios_splash_icon'][$key]) ? esc_attr(pwaforwp_https($settings['ios_splash_icon'][$key])) : ''; ?>">
                <button type="button" class="button pwaforwp-ios-splash-icon-upload" data-editor="content">
                    <span class="dashicons dashicons-format-image" style="margin-top: 4px;"></span> <?php echo esc_html__('Choose Icon', 'pwa-for-wp'); ?>
                </button>
            </div>
        <?php } 
    } ?>
        </div>
        
    </div>

    <?php
}

function pwaforwp_splash_maskable_icon_callback()
{
    // Get Settings
    $settings = pwaforwp_defaultSettings(); ?>
    
    <!-- Splash  Maskable Screen Icon -->
    <input type="text" name="pwaforwp_settings[splash_maskable_icon]" id="pwaforwp_settings[splash_maskable_icon]" class="pwaforwp-splash-icon regular-text pwaforwp-maskable-input" size="50" value="<?php echo isset($settings['splash_maskable_icon']) ? esc_attr(pwaforwp_https($settings['splash_maskable_icon'])) : ''; ?>">
    <button type="button" class="button pwaforwp-maskable-icon-upload" data-editor="content">
    <span class="dashicons dashicons-format-image" style="margin-top: 4px;"></span> <?php echo esc_html__('Choose Icon', 'pwa-for-wp'); ?>
    </button>
    <button type="button" style="background-color: red; border-color: red; color: #fff; display:none;" class="button pwaforwp_js_remove_maskable" > <?php echo esc_html__('Remove', 'pwa-for-wp'); ?></button>
    
    <p class="description">
    <?php echo sprintf(
        '%s <strong>%s</strong>',
        esc_html__('Icon displayed on the splash screen of your application on supported devices. Must be a PNG image size exactly', 'pwa-for-wp'),
        esc_html__('512x512 in size.', 'pwa-for-wp')
    );
    ?>
    </p>
    <?php
}

function pwaforwp_app_screenshots_callback()
{
    // Get Settings
    $settings = pwaforwp_defaultSettings();
    ?>
    <div class="js_clone_div" style="margin-top: 10px;">
        <input type="text" name="pwaforwp_settings[screenshots]" id="pwaforwp_settings[screenshots]"  class="pwaforwp-screenshots"  value="<?php echo isset($settings['screenshots']) ? esc_attr(pwaforwp_https($settings['screenshots'])) : ''; ?>">
        <button type="button" class="button js_choose_button pwaforwp-screenshots-upload" data-editor="content">
            <span class="dashicons dashicons-format-image" style="margin-top: 4px;"></span> <?php echo esc_html__('Choose Screenshot', 'pwa-for-wp'); ?> 
        </button>
        <select name="pwaforwp_settings[form_factor]" class="pwaforwp_settings_form_factor" style="width:8em;vertical-align:top;">
            <option value="" ><?php esc_html_e('Form Factor', 'pwa-for-wp'); ?>
                </option>
            <option value="narrow" <?php if (isset($settings['form_factor']) ) { selected($settings['form_factor'], 'narrow'); 
} ?>>
                <?php esc_html_e('Narrow', 'pwa-for-wp'); ?>
            </option>
            <option value="wide" <?php if (isset($settings['form_factor']) ) { selected($settings['form_factor'], 'wide'); 
} ?>>
                <?php esc_html_e('Wide', 'pwa-for-wp'); ?>
            </option>
        </select>
        <button type="button" class="button button-primary" id="screenshots_add_more"> <?php echo esc_html__('Add', 'pwa-for-wp'); ?> </button>
        <button type="button" style="background-color: red; border-color: red; color: #fff; display:none;" class="button js_remove_screenshot" > <?php echo esc_html__('Remove', 'pwa-for-wp'); ?> 
        </button>
    </div>
    <?php
    if (isset($settings['screenshots_multiple']) && is_array($settings['screenshots_multiple']) && !empty($settings['screenshots_multiple'])) {
        foreach ($settings['screenshots_multiple'] as $key => $screenshot) {
            ?>    
        <div class="js_clone_div" style="margin-top: 10px;">
            <input type="text" name="pwaforwp_settings[screenshots_multiple][]"  class="pwaforwp-screenshots" value="<?php echo isset($screenshot) ? esc_attr(pwaforwp_https($screenshot)) : ''; ?>">
            <button type="button" class="button js_choose_button pwaforwp-screenshots-multiple-upload" data-editor="content">
                <span class="dashicons dashicons-format-image" style="margin-top: 4px;"></span> <?php echo esc_html__('Choose Screenshot', 'pwa-for-wp'); ?> 
            </button>
            <select name="pwaforwp_settings[form_factor_multiple][]" class="pwaforwp_settings_form_factor_multiple" style="width:8em;vertical-align:top;">
                <option value="" ><?php esc_html_e('Form Factor', 'pwa-for-wp'); ?>
                </option>
                <option value="narrow" <?php if (isset($settings['form_factor_multiple'][$key]) ) { selected($settings['form_factor_multiple'][$key], 'narrow'); 
} ?>>
            <?php esc_html_e('Narrow', 'pwa-for-wp'); ?>
                </option>
                <option value="wide" <?php if (isset($settings['form_factor_multiple'][$key]) ) { selected($settings['form_factor_multiple'][$key], 'wide'); 
} ?>>
            <?php esc_html_e('Wide', 'pwa-for-wp'); ?>
                </option>
            </select>
            <button type="button" style="background-color: red; border-color: red; color: #fff;" class="button js_remove_screenshot" > <?php echo esc_html__('Remove', 'pwa-for-wp'); ?> 
                </button>
        </div>
            <?php
        }
    }
    ?>
    <p class="description">
        <?php echo sprintf(
            '%s <strong>%s</strong><br/> %s',
            esc_html__('Screenshots of your application when installed on the phone. Must be a PNG image exactly', 'pwa-for-wp'),
            esc_html__('512x512 in size.', 'pwa-for-wp'),
            esc_html__('- For all mobiles exact sizes is necessary', 'pwa-for-wp')
        );
        ?>
    </p>
    
    <?php
}

function pwaforwp_offline_page_callback()
{
    // Get Settings
    $settings = pwaforwp_defaultSettings(); ?>
    <!-- WordPress Pages Dropdown -->
    <label for="pwaforwp_settings[offline_page]">
    <?php 
        $allowed_html = pwaforwp_expanded_allowed_tags();
    $selected = isset($settings['offline_page']) ? esc_attr($settings['offline_page']) : '';
    $showother = 'disabled';$selectedother = '';$selecteddefault = '';$pro = '';
                $extension_active = false;
    if($selected=='other') { $selectedother= 'selected';
    } 
    if($selected=='0') { $selecteddefault= 'selected';
    } 
    if($extension_active) {$showother="";$pro="style='visibility:hidden'";
    } 
        $selectHtml = wp_kses(
            wp_dropdown_pages(
                array( 
                'name'              => 'pwaforwp_settings[offline_page]', 
                'class'             => 'pwaforwp_select_with_other', 
                'echo'              => 0, 
                'selected'          =>  esc_attr($selected),
                )
            ), $allowed_html
        );
		// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- using custom html
        echo preg_replace('/<select(.*?)>(.*?)<\/select>/s', "<select$1><option value='0' ".esc_attr($selecteddefault)."> ".esc_html__('&mdash; Default &mdash;', 'pwa-for-wp')." </option><option value='other' ".esc_attr($selectedother)."> ".esc_html__('Custom URL', 'pwa-for-wp')." </option>$2</select>", $selectHtml); 
        
    
    ?>
    <div class="pwaforwp-sub-tab-headings pwaforwp_dnone"><input type="text" name="pwaforwp_settings[offline_page_other]" id="offline_page_other" class="regular-text" <?php echo esc_attr($showother); ?> placeholder="<?php echo esc_attr__('Custom offline page (Must be in same origin)', 'pwa-for-wp'); ?>" value="<?php echo isset($settings['offline_page_other']) ? esc_attr($settings['offline_page_other']) : ''; ?>"></div>
    
    </label>
    
    
    <p class="description">
    <?php
    /* translators: %s: offline page */
    printf(esc_html__('Offline page is displayed, when the device is offline and the requested page is not already cached. Current offline page is %s', 'pwa-for-wp'), get_permalink($settings['offline_page']) ? esc_url(get_permalink($settings['offline_page'])) : esc_url(get_bloginfo('wpurl'))); ?>
    </p>

    <?php
}

function pwaforwp_404_page_callback()
{
    // Get Settings
    $settings = pwaforwp_defaultSettings(); ?>
    <!-- WordPress Pages Dropdown -->
    <label for="pwaforwp_settings[404_page]">
    <?php 
        $allowed_html = pwaforwp_expanded_allowed_tags();
    $selected = isset($settings['404_page']) ? esc_attr($settings['404_page']) : '';
    $showother = 'disabled';$selectedother = '';$selecteddefault = '';$pro = '';
                $extension_active = false;
    if($selected=='other') { $selectedother= 'selected';
    } 
    if($selected=='0') { $selecteddefault= 'selected';
    } 
    if($extension_active) {$showother="";$pro="style='visibility:hidden'";
    } 
        $selectHtml = wp_kses(
            wp_dropdown_pages(
                array( 
                'name'              => 'pwaforwp_settings[404_page]', 
                'class'             => 'pwaforwp_select_with_other', 
                'echo'              => 0,
                'selected'          => isset($settings['404_page']) ? esc_attr($settings['404_page']) : '',
                )
            ), $allowed_html
        ); 
		// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- using custom html
        echo preg_replace('/<select(.*?)>(.*?)<\/select>/s', "<select$1><option value='0' ".esc_attr($selecteddefault)."> ".esc_html__('&mdash; Default &mdash;', 'pwa-for-wp')." </option><option value='other' ".esc_attr($selectedother)."> ".esc_html__('Custom URL', 'pwa-for-wp')." </option>$2</select>", $selectHtml); 
        
    ?>
        <div class="pwaforwp-sub-tab-headings pwaforwp_dnone"><input type="text" name="pwaforwp_settings[404_page_other]" id="404_page_other" class="regular-text"  <?php echo esc_attr($showother); ?> placeholder="<?php echo esc_attr__('Custom 404 page (Must be in same origin)', 'pwa-for-wp'); ?>" value="<?php echo isset($settings['404_page_other']) ? esc_attr($settings['404_page_other']) : ''; ?>"></div>
    
    </label>
    
    <p class="description">
    <?php
    /* translators: %s: 404 page */
                printf(esc_html__('404 page is displayed and the requested page is not found. Current 404 page is %s', 'pwa-for-wp'), esc_url(get_permalink($settings['404_page']) ? get_permalink($settings['404_page']) : '')); echo ' '; esc_html_e('Leaving this empty on a block theme uses the theme\'s 404 template.', 'pwa-for-wp'); ?>
    </p>

    <?php
}
function pwaforwp_start_page_callback()
{
    // Get Settings
    $settings = pwaforwp_defaultSettings();
    ?>
    <!-- WordPress Pages Dropdown -->
    <label for="pwaforwp_settings[start_page]">
    <?php 
        $allowed_html = pwaforwp_expanded_allowed_tags();  
    $selected = isset($settings['start_page']) ? esc_attr($settings['start_page']) : '';
    $showother = 'disabled';$selectedother = '';$selecteddefault = '';$selectedActiveUrl = '';$pro = '';
                $extension_active = false;
    if($selected=='other') { $selectedother= 'selected';
    } 
    if($selected=='active_url') {
        $selectedActiveUrl= 'selected';
        $delete_permission = current_user_can('delete_posts');
        if(file_exists(ABSPATH.'pwa-manifest.json') && $extension_active && $delete_permission) {
            wp_delete_file(ABSPATH.'pwa-manifest.json');
        }
    } 
    if($selected=='0') { $selecteddefault= 'selected';
    } 
    if($extension_active) {$showother="";$pro="style='visibility:hidden'";
    } 
        $selectHtml = wp_kses(
            wp_dropdown_pages(
                array( 
                'name'              => 'pwaforwp_settings[start_page]', 
                'class'             => 'pwaforwp_select_with_other', 
                'echo'              => 0,
                'selected'          => isset($settings['start_page']) ? esc_attr($settings['start_page']) : '',
                )
            ), $allowed_html
        ); 

		// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- using custom html
            echo preg_replace('/<select(.*?)>(.*?)<\/select>/s', "<select$1><option value='0' ".esc_attr($selectedother)."> ".esc_html__('&mdash; Homepage &mdash;', 'pwa-for-wp')." </option><option value='other' ".esc_attr($selectedother)."> ".esc_html__('Custom URL', 'pwa-for-wp')." </option><option value='active_url' ".esc_attr($selectedActiveUrl)."> ".esc_html__('Dynamic URL', 'pwa-for-wp')." </option>$2</select>", $selectHtml); 
        
    ?>
        <div class="pwaforwp-sub-tab-headings pwaforwp_dnone" ><input type="text" name="pwaforwp_settings[start_page_other]" id="start_page_other" class="regular-text" <?php echo esc_attr($showother); ?> placeholder="<?php echo esc_attr__('Custom Start page (Must be in same origin)', 'pwa-for-wp'); ?>" value="<?php echo isset($settings['start_page_other']) ? esc_attr($settings['start_page_other']) : ''; ?>"></div> 
        
    </label>
    <p class="description">
    <?php
    $current_page = isset($settings['start_page'])? get_permalink($settings['start_page']):''; 
    echo esc_html__('From where you want to launch PWA APP. Current start page is ', 'pwa-for-wp') . esc_url($current_page); 
    ?>
    </p>

    <?php
}

function pwaforwp_orientation_callback()
{
    
    $settings = pwaforwp_defaultSettings();         
    ?>
    
    <!-- Orientation Dropdown -->
    <label for="pwaforwp_settings[orientation]">
        <select name="pwaforwp_settings[orientation]" id="pwaforwp_settings[orientation]">
            <option value="" <?php if (isset($settings['orientation']) ) { selected($settings['orientation'], ''); 
} ?>>
                <?php echo esc_html__('Follow Device Orientation', 'pwa-for-wp'); ?>
            </option>
            <option value="portrait" <?php if (isset($settings['orientation']) ) { selected($settings['orientation'], 'portrait'); 
} ?>>
                <?php echo esc_html__('Portrait', 'pwa-for-wp'); ?>
            </option>
            <option value="landscape" <?php if (isset($settings['orientation']) ) { selected($settings['orientation'], 'landscape'); 
} ?>>
                <?php echo esc_html__('Landscape', 'pwa-for-wp'); ?>
            </option>
            <option value="any" <?php if (isset($settings['orientation']) ) { selected($settings['orientation'], 'any'); selected($settings['orientation'], 'any'); 
} ?>>
                <?php echo esc_html__('Auto', 'pwa-for-wp'); ?>
            </option>
            <option value="landscape-primary" <?php if (isset($settings['orientation']) ) { selected($settings['orientation'], 'landscape-primary'); 
} ?>>
                <?php echo esc_html__('Landscape-primary', 'pwa-for-wp'); ?>
            </option>
            <option value="landscape-secondary" <?php if (isset($settings['orientation']) ) { selected($settings['orientation'], 'landscape-secondary'); 
} ?>>
                <?php echo esc_html__('Landscape-secondary', 'pwa-for-wp'); ?>
            </option>
            <option value="portrait-primary" <?php if (isset($settings['orientation']) ) { selected($settings['orientation'], 'portrait-primary'); 
} ?>>
                <?php echo esc_html__('Portrait-primary', 'pwa-for-wp'); ?>
            </option>
            <option value="portrait-secondary" <?php if (isset($settings['orientation']) ) { selected($settings['orientation'], 'portrait-secondary'); 
} ?>>
                <?php echo esc_html__('Portrait-secondary', 'pwa-for-wp'); ?>
            </option>
        </select>
    </label>
    
    <p class="description">
        <?php echo esc_html__('Orientation of application on devices. When set to Follow Device Orientation your application will rotate as the device is rotated.', 'pwa-for-wp'); ?>
    </p>

<?php
}

function pwaforwp_display_callback() {
    $settings = pwaforwp_defaultSettings();
    ?>
    <!-- Display Dropdown -->
    <label for="pwaforwp_settings[display]">
        <select name="pwaforwp_settings[display]" id="pwaforwp_settings[display]">
            <option value="" <?php echo (isset($settings['display']) && $settings['display'] === '') ? 'selected="selected"' : ''; ?>><?php echo esc_html__('Device display', 'pwa-for-wp'); ?></option>
            <option value="fullscreen" <?php echo (isset($settings['display']) && $settings['display'] === 'fullscreen') ? 'selected="selected"' : ''; ?>><?php echo esc_html__('Fullscreen', 'pwa-for-wp'); ?></option>
            <option value="standalone" <?php echo (isset($settings['display']) && $settings['display'] === 'standalone') ? 'selected="selected"' : ''; ?>><?php echo esc_html__('Standalone', 'pwa-for-wp'); ?></option>
            <option value="minimal-ui" <?php echo (isset($settings['display']) && $settings['display'] === 'minimal-ui') ? 'selected="selected"' : ''; ?>><?php echo esc_html__('Minimal-ui', 'pwa-for-wp'); ?></option>
            <option value="browser" <?php echo (isset($settings['display']) && $settings['display'] === 'browser') ? 'selected="selected"' : ''; ?>><?php echo esc_html__('Browser', 'pwa-for-wp'); ?></option>
        </select>
    </label>
    <p class="description">
        <?php echo esc_html__('Select how the app should appear when launched from the home screen (fullscreen, standalone, minimal-ui, or browser).', 'pwa-for-wp'); ?>
    </p>
    <?php
}

function pwaforwp_apple_status_bar_callback()
{
    $settings = pwaforwp_defaultSettings();         
    ?>
    <!-- iOS status bar -->
    <label for="pwaforwp_settings[ios_status_bar]">
        <select name="pwaforwp_settings[ios_status_bar]" id="pwaforwp_settings[ios_status_bar]">
            <option value="default" <?php if (isset($settings['ios_status_bar']) ) { selected($settings['ios_status_bar'], 'default'); 
} ?>>
                <?php echo esc_html__('Default', 'pwa-for-wp'); ?>
            </option>
            <option value="black" <?php if (isset($settings['ios_status_bar']) ) { selected($settings['ios_status_bar'], 'black'); 
} ?>>
                <?php echo esc_html__('Black', 'pwa-for-wp'); ?>
            </option>
            <option value="black-translucent" <?php if (isset($settings['ios_status_bar']) ) { selected($settings['ios_status_bar'], 'black-translucent'); 
} ?>>
                <?php echo esc_html__('Black translucent', 'pwa-for-wp'); ?>
            </option>
        </select>
    </label>
    
    <p class="description">
        <?php echo esc_html__('The status bar at the top of the screen (which usually displays the time and battery status).', 'pwa-for-wp'); ?>
    </p>

    <?php
}

function pwaforwp_related_applications_callback()
{
    // Get Settings
    $settings = pwaforwp_defaultSettings();
    $related_applications_div = 'none';
    if(isset($settings['prefer_related_applications']) && $settings['prefer_related_applications'] == 1) {
        $related_applications_div = '';
    }
    
    ?>
    <div id="related_applications_div" style="display:<?php echo esc_attr($related_applications_div); ?>">
    <fieldset>
        <label for="pwaforwp_settings[related_applications]"><?php echo esc_html__('PlayStore App ID', 'pwa-for-wp'); ?></label>&nbsp;
        <input type="text" name="pwaforwp_settings[related_applications]" class="regular-text" placeholder="<?php esc_attr__("com.example.app", "pwa-for-wp") ?>" value="<?php if (isset($settings['related_applications']) && ( ! empty($settings['related_applications']) ) ) { echo esc_attr($settings['related_applications']);
} ?>"/>
    </fieldset>
    <fieldset>
        <label for="pwaforwp_settings[related_applications_ios]"><?php echo esc_html__('AppStore App ID', 'pwa-for-wp'); ?></label>&nbsp;
        <input type="text" name="pwaforwp_settings[related_applications_ios]" placeholder="<?php esc_attr__("id123456789", "pwa-for-wp") ?>" class="regular-text" value="<?php if (isset($settings['related_applications_ios']) && ( ! empty($settings['related_applications_ios']) ) ) { echo esc_attr($settings['related_applications_ios']);
} ?>"/>
    </fieldset>
    </div>

    <?php
}

function pwaforwp_prefer_related_applications_callback()
{
    // Get Settings
    $settings = pwaforwp_defaultSettings();    
    $prefer_related_applications = '';
    if(isset($settings['prefer_related_applications']) && $settings['prefer_related_applications'] == 1) {
        $prefer_related_applications = 'checked="checked';
    }
    ?>        
    <input type="checkbox" name="pwaforwp_settings[prefer_related_applications]" id="prefer_related_applications" class="" <?php echo esc_attr($prefer_related_applications); ?> data-uncheck-val="0" value="1">
    <?php
}

function pwaforwp_one_signal_support_callback()
{
    // Get Settings
    $settings = pwaforwp_defaultSettings(); 
    ?>
    <input type="checkbox" name="pwaforwp_settings[one_signal_support_setting]" id="pwaforwp_settings[one_signal_support_setting]" class="pwaforwp-onesignal-support" <?php echo (isset($settings['one_signal_support_setting']) &&  $settings['one_signal_support_setting'] == 1 ? 'checked="checked"' : ''); ?> value="1">
               
    <?php
}
function pwaforwp_pushnami_support_callback()
{
    // Get Settings
    $settings = pwaforwp_defaultSettings();
    ?>
    <input type="checkbox" name="pwaforwp_settings[pushnami_support_setting]" id="pwaforwp_settings[pushnami_support_setting]" class="pwaforwp-pushnami-support" <?php echo (isset($settings['pushnami_support_setting']) &&  $settings['pushnami_support_setting'] == 1 ? 'checked="checked"' : ''); ?> value="1">

    <?php
}

function pwaforwp_webpushr_support_callback()
{
    // Get Settings
    $settings = pwaforwp_defaultSettings();
    ?>
    <input type="checkbox" name="pwaforwp_settings[webpusher_support_setting]" id="pwaforwp_settings[webpusher_support_setting]" class="pwaforwp-pushnami-support" <?php echo (isset($settings['webpusher_support_setting']) &&  $settings['webpusher_support_setting'] == 1 ? 'checked="checked"' : ''); ?> value="1">

    <?php
}

function pwaforwp_wphide_support_callback()
{
    // Get Settings
    $settings = pwaforwp_defaultSettings();
    ?>
    <input type="checkbox" name="pwaforwp_settings[wphide_support_setting]" id="pwaforwp_settings[wphide_support_setting]" class="pwaforwp-wphide-support" <?php echo (isset($settings['wphide_support_setting']) &&  $settings['wphide_support_setting'] == 1 ? 'checked="checked"' : ''); ?> value="1">

    <?php
}

function pwaforwp_custom_add_to_home_callback()
{
    // Get Settings
    $settings = pwaforwp_defaultSettings(); 
    ?>
    <input type="checkbox" name="pwaforwp_settings[custom_add_to_home_setting]" id="pwaforwp_settings[custom_add_to_home_setting]" class="pwaforwp-add-to-home-banner-settings" <?php echo (isset($settings['custom_add_to_home_setting']) &&  $settings['custom_add_to_home_setting'] == 1 ? 'checked="checked"' : ''); ?> value="1">
    <p><?php echo esc_html__('Show custom responsive add to home banner popup', 'pwa-for-wp'); ?></p>
        <?php if(isset($settings['custom_add_to_home_setting']) &&  $settings['custom_add_to_home_setting'] == 1) {  ?>
        <div class="pwaforwp-enable-on-desktop">
            <input type="checkbox" name="pwaforwp_settings[enable_add_to_home_desktop_setting]" id="enable_add_to_home_desktop_setting" class="" <?php echo (isset($settings['enable_add_to_home_desktop_setting']) &&  $settings['enable_add_to_home_desktop_setting'] == 1 ? 'checked="checked"' : ''); ?> value="1"><strong><?php echo esc_html__('Enable On Desktop', 'pwa-for-wp'); ?></strong>
            <p><?php echo esc_html__('Note: By default pop up will appear on mobile device, to appear on desktop check enable on desktop', 'pwa-for-wp'); ?></p>
        </div>
        <?php }else{ ?>
        <div class="afw_hide pwaforwp-enable-on-desktop"><input type="checkbox" name="pwaforwp_settings[enable_add_to_home_desktop_setting]" id="enable_add_to_home_desktop_setting" class="" <?php echo (isset($settings['enable_add_to_home_desktop_setting']) &&  $settings['enable_add_to_home_desktop_setting'] == 1 ? 'checked="checked"' : ''); ?> value="1"><strong><?php echo esc_html__('Enable On Desktop', 'pwa-for-wp'); ?></strong>
            <p><?php echo esc_html__('Note: By default pop up will appear on mobile device, to appear on desktop check enable on desktop', 'pwa-for-wp'); ?></p>
        </div>
        <?php }
        //option for static websites
        ?>
        <div class="show-banner-on-static-website">
            <input type="checkbox" name="pwaforwp_settings[show_banner_without_scroll]" id="show_banner_without_scroll" value="1" <?php echo (isset($settings['show_banner_without_scroll']) &&  $settings['show_banner_without_scroll'] == 1 ? 'checked="checked"' : ''); ?> >
            <label for="show_banner_without_scroll" style="font-weight:600"><?php echo esc_html__('Show banner without scroll', 'pwa-for-wp');?></label>
            <p><?php echo esc_html__('By default pop up will appear on scroll', 'pwa-for-wp'); ?></p>
        </div>


    <?php
    pwaforwp_custom_banner_design_callback();
}
function pwaforwp_add_to_home_callback()
{
    
    $settings = pwaforwp_defaultSettings();         
    ?>        
        <input type="text" name="pwaforwp_settings[add_to_home_selector]" id="pwaforwp_settings[add_to_home_selector]" class="pwaforwp-add-to-home-selector regular-text" size="50" value="<?php echo isset($settings['add_to_home_selector']) ? esc_attr($settings['add_to_home_selector']) : ''; ?>">
    <p><?php echo esc_html__('jQuery selector (.element) or (#element)', 'pwa-for-wp'); ?></p>    
        <p><?php echo esc_html__('Note: It is currently available in non AMP', 'pwa-for-wp'); ?></p>    
        <p><?php echo esc_html__('Note: In IOS devices this functionality will not work.', 'pwa-for-wp'); ?></p>    
    <?php
}

// Dashboard
function pwaforwp_files_status_callback()
{
    
       $serviceWorkerObj = new PWAFORWP_Service_Worker();
       $is_amp   = $serviceWorkerObj->is_amp;             
       $settings = pwaforwp_defaultSettings();

       $nonAmpStatusMsg = $nonampStatusIcon = $nonAmpLearnMoreLink = '';

    if(!isset($settings['normal_enable']) || (isset($settings['normal_enable']) && $settings['normal_enable'] != 1) ) {
        $nonAmpStatusMsg = 'PWA is disabled';
    }
       
        $nonamp_manifest_status = true;
    if(!pwaforwp_is_enabled_pwa_wp()) {
        $swUrl = esc_url(pwaforwp_manifest_json_url());
        $nonamp_manifest_status = @pwaforwp_checkStatus($swUrl);
    }
    if(!$nonamp_manifest_status && $nonAmpStatusMsg=='') {
        $nonAmpStatusMsg = 'Manifest not working';
    }

    $swFile = apply_filters('pwaforwp_sw_name_modify', "pwa-sw".pwaforwp_multisite_postfix().".js");
    $nonamp_sw_status = true;
    if(!pwaforwp_is_enabled_pwa_wp()) {
        $swUrl = esc_url(pwaforwp_home_url().$swFile);
        $swUrl = pwaforwp_service_workerUrls($swUrl, $swFile);
        $nonamp_sw_status = @pwaforwp_checkStatus($swUrl);
    }
    if(!$nonamp_sw_status && $nonAmpStatusMsg=='') {
        $nonAmpStatusMsg = 'Service Worker not working';
    }
    if (!is_ssl() && $nonAmpStatusMsg=='' ) {
        $nonAmpStatusMsg = esc_html__('PWA failed to initialized, the site is not HTTPS', 'pwa-for-wp');
        $nonAmpLearnMoreLink = '<a href="https://pwa-for-wp.com/docs/article/site-need-https-for-pwa/" target="_blank">'.esc_html__('Learn more', 'pwa-for-wp').'</a>';
    }

    if($nonAmpStatusMsg=='') {
        $nonampStatusIcon = '<span class="dashicons dashicons-yes" style="color: #46b450;"></span>';
        $nonAmpStatusMsg = esc_html__('PWA is working', 'pwa-for-wp');
    }

    if($is_amp) {
        $ampStatusMsg = $ampStatusIcon = '';
        if(!isset($settings['amp_enable']) || (isset($settings['amp_enable']) && $settings['amp_enable'] != 1) ) {
            $ampStatusMsg = esc_html__('PWA is disabled', 'pwa-for-wp');
        }

        $amp_manifest_status = true;
        if(!pwaforwp_is_enabled_pwa_wp()) {
            $swUrl = esc_url(pwaforwp_manifest_json_url(true));
            $amp_manifest_status = @pwaforwp_checkStatus($swUrl);
        }
        if(!$amp_manifest_status && $ampStatusMsg=='') {
            $ampStatusMsg = esc_html__('Manifest not working', 'pwa-for-wp');
        }
            
            $swFile = "pwa-amp-sw".pwaforwp_multisite_postfix().".js";
            $amp_sw_status = true;
        if(!pwaforwp_is_enabled_pwa_wp()) {
            $swUrl = esc_url(pwaforwp_home_url().$swFile);
            $swUrl = pwaforwp_service_workerUrls($swUrl, $swFile);
            $amp_sw_status = @pwaforwp_checkStatus($swUrl);
        }
        if(!$amp_sw_status && $ampStatusMsg=='') {
            $ampStatusMsg = esc_html__('Service Worker not working', 'pwa-for-wp');
        }
            
        if (!is_ssl() && $ampStatusMsg=='') {
            $ampStatusMsg = '';
            if(isset($settings['normal_enable']) && $settings['normal_enable'] != 1) {
                $ampStatusMsg = esc_html__('PWA failed to initialized, the site is not HTTPS', 'pwa-for-wp');
            }
        }elseif($ampStatusMsg=='') {
            $ampStatusIcon = '<span class="dashicons dashicons-yes" style="color: #46b450;"></span>';
            $ampStatusMsg = esc_html__('PWA is working on AMP', 'pwa-for-wp');
        }
    }
       
    ?>
        <table class="pwaforwp-files-table">
            <tbody>
                <?php if($is_amp) { ?>
                <tr>
                    <th></th>
                    <th><?php echo esc_html__('WordPress (Non-AMP)', 'pwa-for-wp') ?></th>
                    <th><?php echo esc_html__('AMP', 'pwa-for-wp'); ?></th>
                </tr>    
                <?php } ?>
                <tr>
                    <th style="width:20%"><?php echo esc_html__('Status', 'pwa-for-wp') ?></th>
                    <td style="width:40%"><p><?php 
					// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- using custom html
                    echo $nonampStatusIcon .' '. esc_html($nonAmpStatusMsg). ' '.$nonAmpLearnMoreLink ?></p></td>
                    <?php if($is_amp) { ?>
                    <td style="width:40%"><p><?php
					// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- using custom html
                    echo $ampStatusIcon.' '.esc_html($ampStatusMsg); ?></p></td>
                    <?php } ?>
                </tr>
                
                <tr>
                    <th><label for="pwaforwp_settings_normal_enable"><b><?php echo esc_html__('Enable / Disable', 'pwa-for-wp') ?></label></b></th>
                    <td>
                        <label>
                            <input type="checkbox"  <?php echo (isset($settings['normal_enable']) && $settings['normal_enable'] == 1 ? 'checked="checked"' : ''); ?> value="1" class="pwaforwp-checkbox-tracker" data-id="pwaforwp_settings[normal_enable]" id="pwaforwp_settings_normal_enable"> 
                            <input type="hidden" name="pwaforwp_settings[normal_enable]" id="pwaforwp_settings[normal_enable]" value="<?php echo esc_attr($settings['normal_enable']); ?>" >
                        </label>
                       </td>
                    <td>
                        <?php if($is_amp) { ?>
                        <label><input type="checkbox"  <?php echo (isset($settings['amp_enable']) &&  $settings['amp_enable'] == 1 ? 'checked="checked"' : ''); ?> value="1"  class="pwaforwp-checkbox-tracker" data-id="pwaforwp_settings[amp_enable]"> 
                            <input type="hidden" name="pwaforwp_settings[amp_enable]" id="pwaforwp_settings[amp_enable]" value="<?php echo esc_attr($settings['amp_enable']); ?>" >
                        </label>
                        <?php } ?>
                    </td>    
                    
                </tr>
            <tr>
                <th>
                 <?php echo esc_html__('Manifest', 'pwa-for-wp') ?> 
                </th>
                <td>
                   <?php
                   
                    if(!$nonamp_manifest_status) {
                        printf(
                            '<p><span class="dashicons dashicons-no-alt" style="color: #dc3232;"></span><a class="pwaforwp-service-activate" data-id="pwa-manifest" href="#">'.esc_html__('Click here to setup', 'pwa-for-wp').'</a> </p>'
                            .'<p class="pwaforwp-ins-note pwaforwp-hide">'.esc_html__('Change the permission or downlad the file', 'pwa-for-wp').' <a target="_blank" href="http://pwa-for-wp.com/docs/article/how-to-download-required-files-manually-and-place-it-in-root-directory-or-change-the-permission/">'.esc_html__('Instruction', 'pwa-for-wp').'</a></p>' 
                        );
                    }else{
                         printf('<p><span class="dashicons dashicons-yes" style="color: #46b450;"></span> </p>', 'manifest url');
                    }
                    ?>   
                </td>
                <td>
                  <?php
                    if($is_amp) {
                        if(!$amp_manifest_status) {                                                                
                            printf(
                                '<p><span class="dashicons dashicons-no-alt" style="color: #dc3232;"></span><a class="pwaforwp-service-activate" data-id="pwa-amp-manifest" href="#">'.esc_html__('Click here to setup', 'pwa-for-wp').'</a></p>'
                                . '<p class="pwaforwp-ins-note pwaforwp-hide">'.esc_html__('Change the permission or downlad the file', 'pwa-for-wp').' <a target="_blank" href="http://pwa-for-wp.com/docs/article/how-to-download-required-files-manually-and-place-it-in-root-directory-or-change-the-permission/">'.esc_html__('Instruction', 'pwa-for-wp').'</a></p>' 
                            );
                        }else{
                            printf('<p><span class="dashicons dashicons-yes" style="color: #46b450;"></span> </p>', 'manifest url');
                        }    
                    }
                  
                    ?>  
                </td>
                
            </tr>
            <tr>
                <th>                 
             <?php echo esc_html__('Service Worker', 'pwa-for-wp'); ?>  
                </th>
                 <td>
                    <?php
                      
                    if(!$nonamp_sw_status) {
                        printf(
                            '<p><span class="dashicons dashicons-no-alt" style="color: #dc3232;"></span> <a class="pwaforwp-service-activate" data-id="pwa-sw" href="#">'.esc_html__('Click here to setup', 'pwa-for-wp').'</a></p>'
                            . '<p class="pwaforwp-ins-note pwaforwp-hide">'.esc_html__('Change the permission or downlad the file', 'pwa-for-wp').' <a target="_blank" href="http://pwa-for-wp.com/docs/article/how-to-download-required-files-manually-and-place-it-in-root-directory-or-change-the-permission/">'.esc_html__('Instruction', 'pwa-for-wp').'</a></p>' 
                        );
                    }else{
                        printf('<p><span class="dashicons dashicons-yes" style="color: #46b450;"></span> </p>', 'manifest url');
                    }
                    ?>  
                </td>
                <td>
                  <?php
                    if($is_amp) {
                      
                    
                        if(!$amp_sw_status) {
                            printf(
                                '<p><span class="dashicons dashicons-no-alt" style="color: #dc3232;"></span><a class="pwaforwp-service-activate" data-id="pwa-amp-sw" href="#">'.esc_html__('Click here to setup', 'pwa-for-wp').'</a> </p>'
                                . '<p class="pwaforwp-ins-note pwaforwp-hide">'.esc_html__('Change the permission or downlad the file', 'pwa-for-wp').' <a target="_blank" href="http://pwa-for-wp.com/docs/article/how-to-download-required-files-manually-and-place-it-in-root-directory-or-change-the-permission/">'.esc_html__('Instruction', 'pwa-for-wp').'</a></p>' 
                            );
                        }else{
                            printf('<p><span class="dashicons dashicons-yes" style="color: #46b450;"></span> </p>');
                        }    
                    }
                    
                    ?>  
                </td>
               
            </tr>
            <tr>
                <th>                 
              <?php echo esc_html__('HTTPS', 'pwa-for-wp') ?> 
                </th>
                <td>
                  <?php
                    if (is_ssl() ) {
                            echo '<p><span class="dashicons dashicons-yes" style="color: #46b450;"></span> </p>';
                    } else {
                            echo '<p><span class="dashicons dashicons-no-alt" style="color: #dc3232;"></span> </p><p>'.esc_html__('This site is not configure with https', 'pwa-for-wp').' <a href="https://pwa-for-wp.com/docs/article/site-need-https-for-pwa/" target="_blank">'.esc_html__('Learn more', 'pwa-for-wp').'</a></p>';                                     
                    }
                    ?>  
                </td>
                <td>
                  <?php
                    if ($is_amp && is_ssl() ) {
                            echo '<p><span class="dashicons dashicons-yes" style="color: #46b450;"></span> </p>';
                    } 
                    ?>  
                </td>
            </tr>
            
            </tbody>    
        </table>
        
        <?php
}

function pwaforwp_amp_status_callback()
{
                
        $swUrl        = esc_url(site_url()."/sw".pwaforwp_multisite_postfix().".js");
    $file_headers = @pwaforwp_checkStatus($swUrl);    
        
    if(!$file_headers) {
        printf('<p><span class="dashicons dashicons-no-alt" style="color: #dc3232;"></span> </p>');
    }else{
        printf('<p><span class="dashicons dashicons-yes" style="color: #46b450;"></span> </p>');
    }
}

function pwaforwp_checkStatus($swUrl)
{
    
        $settings = pwaforwp_defaultSettings();
        $manualfileSetup = "";

    if(array_key_exists('manualfileSetup', $settings)) {
        $manualfileSetup = $settings['manualfileSetup'];    
    }    
    
    if($manualfileSetup) {
        if(!pwaforwp_is_file_inroot() || is_multisite() ) {
            $response = wp_remote_get(esc_url_raw($swUrl));
            $response_code       = wp_remote_retrieve_response_code($response);
            $response_message = wp_remote_retrieve_response_message($response);

            if (200 != $response_code && ! empty($response_message) ) {
                return false;
            } elseif (200 != $response_code ) {
                return false;
            } else {
                return true;
            }
        }else{

            $wppath               = str_replace("//", "/", str_replace("\\", "/", realpath(ABSPATH))."/");
            $wppath               = apply_filters("pwaforwp_file_creation_path", $wppath);
            $swjsFile             = $wppath."pwa-amp-sw".pwaforwp_multisite_postfix().".js";
            $swHtmlFile           = $wppath."pwa-amp-sw".pwaforwp_multisite_postfix().".html";
            $swrFile              = $wppath."pwa-register-sw".pwaforwp_multisite_postfix().".js";
            $swmanifestFile       = $wppath."pwa-amp-manifest".pwaforwp_multisite_postfix().".json";                
            $swjsFileNonAmp       = $wppath."pwa-sw".pwaforwp_multisite_postfix().".js";
            $swmanifestFileNonAmp = $wppath."pwa-manifest".pwaforwp_multisite_postfix().".json";
            
            switch ($swUrl) {
            case pwaforwp_manifest_json_url(true):
                if(file_exists($swmanifestFile)) {
                        return true;
                }
                break;
            case pwaforwp_home_url()."pwa-amp-sw".pwaforwp_multisite_postfix().".js":
                if(file_exists($swjsFile)) {
                    return true;
                }
                break;
            case pwaforwp_home_url()."pwa-sw".pwaforwp_multisite_postfix().".js":
                if(file_exists($swjsFileNonAmp)) {
                    return true;
                }
                break;
            case pwaforwp_manifest_json_url():
                if(file_exists($swmanifestFileNonAmp)) {
                    return true;
                }
                break;
            case pwaforwp_home_url()."pwa-amp-sw".pwaforwp_multisite_postfix().".html":
                if(file_exists($swHtmlFile)) {
                    return true;
                }
                break;  
            case pwaforwp_home_url()."pwa-register-sw".pwaforwp_multisite_postfix().".js":
                if(file_exists($swrFile)) {
                    return true;
                }
                break;          
                                    
            default:
                // code...
                break;
            }
        }
    }
    $ret = true;
    $file_headers = @get_headers($swUrl);       
    if(!$file_headers || $file_headers[0] == 'HTTP/1.0 404 Not Found' || $file_headers[0] == 'HTTP/1.0 301 Moved Permanently' || $file_headers[0] == 'HTTP/1.1 404 Not Found' || $file_headers[0] == 'HTTP/1.1 301 Moved Permanently') {
        $ret = false;
    }
        
    return $ret;
    // Handle $response here. */
}

/**
 * Enqueue CSS and JS
 */
function pwaforwp_enqueue_style_js( $hook )
{
    // Load only on pwaforwp plugin pages
    if (!is_admin() ) {
        return;
    }
    $suffix = defined('SCRIPT_DEBUG') && SCRIPT_DEBUG ? '' : '.min';

    wp_register_script('pwaforwp-all-page-js', PWAFORWP_PLUGIN_URL . 'assets/js/all-page-script'.$suffix.'.js', array( ), PWAFORWP_PLUGIN_VERSION, true);
        
        $object_name = array(
            'ajax_url'                  => admin_url('admin-ajax.php'),
            'uploader_title'            => esc_html__('Application Icon', 'pwa-for-wp'),
            'splash_uploader_title'     => esc_html__('Splash Screen Icon', 'pwa-for-wp'),
            'uploader_button'           => esc_html__('Select Icon', 'pwa-for-wp'),
            'file_status'               => esc_html__('Check permission or download from manual', 'pwa-for-wp'),
            'pwaforwp_security_nonce'   => wp_create_nonce('pwaforwp_ajax_check_nonce'),
            'iosSplashIcon'                => pwaforwp_ios_splashscreen_files_data(),
        );
        
        $object_name = apply_filters('pwaforwp_localize_filter', $object_name, 'pwaforwp_obj');
        
        wp_localize_script('pwaforwp-all-page-js', 'pwaforwp_obj', $object_name);
        wp_enqueue_script('pwaforwp-all-page-js');


        if($hook!='toplevel_page_pwaforwp') {return ; 
        }
        // Color picker CSS
        // @refer https://make.wordpress.org/core/2012/11/30/new-color-picker-in-wp-3-5/
        wp_enqueue_style('wp-color-picker');    
        // Everything needed for media upload
        wp_enqueue_media();   
        add_thickbox();     
        include_once ABSPATH . 'wp-admin/includes/class-wp-upgrader.php';
        wp_update_plugins();
        //wp_enqueue_script('thickbox', null, array('jquery'));
        wp_enqueue_script('wp-color-picker-alpha', PWAFORWP_PLUGIN_URL . 'assets/js/wp-color-picker-alpha.min.js', array( 'wp-color-picker' ), PWAFORWP_PLUGIN_VERSION, true);


        wp_enqueue_style('pwaforwp-main-css', PWAFORWP_PLUGIN_URL . 'assets/css/main-css'.$suffix.'.css', array(), PWAFORWP_PLUGIN_VERSION, 'all');      
        wp_style_add_data('pwaforwp-main-css', 'rtl', 'replace');      
        // Main JS
        wp_enqueue_script('pwaforwp-zip-js', PWAFORWP_PLUGIN_URL . 'assets/js/jszip.min.js', array(), PWAFORWP_PLUGIN_VERSION, true);
        wp_register_script('pwaforwp-main-js', PWAFORWP_PLUGIN_URL . 'assets/js/main-script'.$suffix.'.js', array( 'wp-color-picker', 'wp-color-picker-alpha', 'plugin-install', 'wp-util', 'wp-a11y','updates' ), PWAFORWP_PLUGIN_VERSION, true);
        
        wp_enqueue_script('pwaforwp-main-js');
}
add_action('admin_enqueue_scripts', 'pwaforwp_enqueue_style_js');



/**
 * This is a ajax handler function for sending email from user admin panel to us. 
 *
 * @return type json string
 */
function pwaforwp_orientation_callback() {
    $settings = pwaforwp_defaultSettings();
    ?>
    <!-- Orientation Dropdown -->
    <label for="pwaforwp_settings[orientation]">
        <select name="pwaforwp_settings[orientation]" id="pwaforwp_settings[orientation]">
            <option value="" <?php echo (isset($settings['orientation']) && $settings['orientation'] === '') ? 'selected="selected"' : ''; ?>><?php echo esc_html__('Follow Device Orientation', 'pwa-for-wp'); ?></option>
            <option value="portrait" <?php echo (isset($settings['orientation']) && $settings['orientation'] === 'portrait') ? 'selected="selected"' : ''; ?>><?php echo esc_html__('Portrait', 'pwa-for-wp'); ?></option>
            <option value="landscape" <?php echo (isset($settings['orientation']) && $settings['orientation'] === 'landscape') ? 'selected="selected"' : ''; ?>><?php echo esc_html__('Landscape', 'pwa-for-wp'); ?></option>
            <option value="any" <?php echo (isset($settings['orientation']) && $settings['orientation'] === 'any') ? 'selected="selected"' : ''; ?>><?php echo esc_html__('Auto', 'pwa-for-wp'); ?></option>
            <option value="landscape-primary" <?php echo (isset($settings['orientation']) && $settings['orientation'] === 'landscape-primary') ? 'selected="selected"' : ''; ?>><?php echo esc_html__('Landscape-primary', 'pwa-for-wp'); ?></option>
            <option value="landscape-secondary" <?php echo (isset($settings['orientation']) && $settings['orientation'] === 'landscape-secondary') ? 'selected="selected"' : ''; ?>><?php echo esc_html__('Landscape-secondary', 'pwa-for-wp'); ?></option>
            <option value="portrait-primary" <?php echo (isset($settings['orientation']) && $settings['orientation'] === 'portrait-primary') ? 'selected="selected"' : ''; ?>><?php echo esc_html__('Portrait-primary', 'pwa-for-wp'); ?></option>
            <option value="portrait-secondary" <?php echo (isset($settings['orientation']) && $settings['orientation'] === 'portrait-secondary') ? 'selected="selected"' : ''; ?>><?php echo esc_html__('Portrait-secondary', 'pwa-for-wp'); ?></option>
        </select>
    </label>
    <p class="description">
        <?php echo esc_html__('Orientation of application on devices. When set to Follow Device Orientation your application will rotate as the device is rotated.', 'pwa-for-wp'); ?>
    </p>
    <?php
}
                                        'tooltip_link'  => 'https://pwa-for-wp.com/docs/article/setting-up-visibility-in-pwa/',
                                        )
        );
                
    $featuresHtml = '';
    if(is_array($feturesArray) && !empty($feturesArray)) {
        foreach ($feturesArray as $key => $featureVal) {
            echo '<div id="'.esc_attr($key).'-contents" class="pwaforwp-hide">';
            echo '<div class="pwaforwp-wrap thickbox-fetures-wrap '.esc_attr($key).'-wrap-tb">';
            do_settings_sections($featureVal['section_name']);
            echo '<div class="footer tab_view_submitbtn" style=""><button type="submit" class="button button-primary pwaforwp-submit-feature-opt">'.esc_html__('Submit', 'pwa-for-wp').'</button></div>';
            echo '</div>';
            echo '</div>';
            $settingsHtml = $tooltipHtml = $warnings = '';
            if($key=='notification' && empty($settings['notification_options'])) {
                $warnings = "<span class='pwafw-tooltip'><i id='notification-opt-stat' class='dashicons dashicons-warning' style='color: #ffb224d1;' title=''></i><span class='pwafw-help-subtitle'>".esc_html__('Need integration', 'pwa-for-wp')."</span></span>";
            }
            if(isset($settings[$featureVal['enable_field']]) && $settings[$featureVal['enable_field']]) {
                $settingsHtml = 'style="opacity:1;"';
            }else{
                $settingsHtml = 'style="opacity:0;"';
            }
            if(isset($featureVal['tooltip_option'])) {
                $tooltipHtml = '<span class="pwafw-tooltip"><i class="dashicons dashicons-editor-help"></i> 
	            <span class="pwafw-help-subtitle">%5$s
	            '.(isset($featureVal['tooltip_link']) && !empty($featureVal['tooltip_link'])? '<a href="'.esc_url($featureVal['tooltip_link']).'" target="_blank">'.esc_html__('Learn more', 'pwa-for-wp').'</a>': '').'
	            </span>
	        </span>';
            }

            $premium_alert  = '<div class="card-action">
				<label class="switch">
				  <input type="checkbox" %3$s name="pwaforwp_settings[%4$s]" value="1">
				  <span class="pwaforwp_slider pwaforwp_round"></span>
				</label>
				<div class="card-action-settings" data-content="%2$s-contents" '.$settingsHtml.'>
					<span class="pwaforwp-change-data pwaforwp-setting-icon-tab dashicons dashicons-admin-generic" href="#" data-option="%2$s-contents" title="%1$s"></span>
				</div>
			</div>';

            $premium_alert = "";

            $featuresHtml .= sprintf(
                '<li class="pwaforwp-card-wrap %6$s" %7$s>
								<div class="pwaforwp-card-content">
									<div class="pwaforwp-tlt-sw">
										<h2>%1$s 
											'.$tooltipHtml.' %8$s
										</h2>
										'.$premium_alert.'
									</div>
									
								</div>
							</li>',
                esc_html($featureVal['setting_title']),
                esc_attr($key),
                (isset($settings[$featureVal['enable_field']]) && $settings[$featureVal['enable_field']] ) ? esc_html("checked") : '',
                $featureVal['enable_field'],
                isset($featureVal['tooltip_option'])? esc_html($featureVal['tooltip_option']): '',
                (isset($settings[$featureVal['enable_field']]) && $settings[$featureVal['enable_field']]? esc_attr('pwaforwp-feature-enabled') : ''),
                $settingsHtml,
                $warnings
            );
        }
    }
    echo '<ul class="pwaforwp-feature-cards">
			'.
			// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- using custom html
    $featuresHtml.'
		</ul>
		<div class="pwawp-modal-mask pwaforwp-hide">
    <div class="pwawp-modal-wrapper">
        <div class="pwawp-modal-container">
			<div class="pwaforwp-visibility-loader">
				<div class="pwaforwp-pwaforwp-visibility-loader-box"></div>
			</div>
            <button type="button" class="pwawp-media-modal-close"><span class="pwawp-media-modal-icon"></span></button>
            <div class="pwawp-modal-content">
                
                <div class="pwawp-modal-header">
                    <h3 class="pwawp-popup-title"></h3>
                </div>
                <div class="pwawp-modal-body">
                    <div class="pwawp-modal-settings">
                        
                    </div>
                </div>
                <div class="clearfix"></div>
            </div>
            <div class="pwawp-modal-footer">
                <!---->
                <button type="button" class="button pwawp-modal-default-button pwawp-save-btn-modal  button-primary">
                    '.esc_html__('Save Changes', 'pwa-for-wp').'
                </button>
                <button type="button" class="button pwawp-close-btn-modal pwawp-modal-default-button">
                    '.esc_html__('Close', 'pwa-for-wp').'
                </button>
            </div>
        </div>
    </div>
</div>

		';
}

function whitelable_for_pwa_custom_config_file($data)
{
    if (! function_exists('request_filesystem_credentials') ) {
        include_once ABSPATH . 'wp-admin/includes/file.php';
    }

    $creds = request_filesystem_credentials(site_url());
    if (! WP_Filesystem($creds) ) {
        return false;
    }

    global $wp_filesystem;

    $file_path = ABSPATH . 'pwa-config.php';

    // Delete if it already exists
    if ($wp_filesystem->exists($file_path) ) {
        $wp_filesystem->delete($file_path);
    }

    // Prepare file content
    $content = "<?php\n";
    $content .= "// Custom configuration for PWA for WP\n";

    if (isset($data['pwaforwp_whitelable_title'])) {
        $whitelabel_title = addslashes(sanitize_text_field($data['pwaforwp_whitelable_title']));
        $content .= "define('PWA_TITLE', '$whitelabel_title');\n";
    }

    if (isset($data['pwaforwp_whitelable_description'])) {
        $whitelabel_description = addslashes(sanitize_text_field($data['pwaforwp_whitelable_description']));
        $content .= "define('PWA_DESCRIPTION', '$whitelabel_description');\n";
    }

    if (isset($data['pwaforwp_whitelable_logo'])) {
        $whitelabel_logo = addslashes(sanitize_text_field($data['pwaforwp_whitelable_logo']));
        $content .= "define('PWA_LOGO', '$whitelabel_logo');\n";
    }

    $content .= "?>";

    // Write the file using WP_Filesystem
    return $wp_filesystem->put_contents(
        $file_path,
        $content,
        FS_CHMOD_FILE
    );
}


add_action("wp_ajax_pwaforwp_update_features_options", 'pwaforwp_update_features_options');
function pwaforwp_update_features_options()
{    
	// phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized, WordPress.Security.ValidatedSanitizedInput.MissingUnslash, WordPress.Security.ValidatedSanitizedInput.InputNotValidated
    if(!wp_verify_nonce($_POST['pwaforwp_security_nonce'], 'pwaforwp_ajax_check_nonce')) {
            echo wp_json_encode(array('status'=> 503, 'message'=> esc_html__('Unauthorized access, CSRF token not matched', 'pwa-for-wp')));
            wp_die();
    }
    if(!isset($_POST['fields_data']) || !is_array($_POST['fields_data'])) {
            echo wp_json_encode(array('status'=> 502, 'message'=> esc_html__('Feature settings not have any fields.', 'pwa-for-wp')));
            wp_die();
    }
    if (! current_user_can('manage_options') ) {
        echo wp_json_encode(array('status'=> 501, 'message'=> esc_html__('Unauthorized access, permission not allowed', 'pwa-for-wp')));
            wp_die();
    }
	// phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
    $allFields = wp_unslash($_POST['fields_data']);    
    $actualFields = array();
    $navigation_bar_data = array();
    $whitelabel_data = array();
    $utm_trackings = array();
    $quick_action = array();
    
    if(is_array($allFields) && !empty($allFields)) {
        foreach ($allFields as $key => $field) {
            if (isset($field['var_name']) && $field['var_name'] == 'pwaforwp_settings[pwaforwp_whitelable_title]') {
                $whitelabel_data['pwaforwp_whitelable_title'] = sanitize_text_field($field['var_value']);
            }
            if (isset($field['var_name']) && $field['var_name'] == 'pwaforwp_settings[pwaforwp_whitelable_logo]') {
                $whitelabel_data['pwaforwp_whitelable_logo'] = sanitize_text_field($field['var_value']);
            }
            if (isset($field['var_name']) && $field['var_name'] == 'pwaforwp_settings[pwaforwp_whitelable_logo]') {
                $whitelabel_data['pwaforwp_whitelable_logo'] = sanitize_text_field($field['var_value']);
            }
            if (isset($field['var_name']) && $field['var_name'] == 'pwaforwp_settings[pwaforwp_whitelable_description]') {
                $whitelabel_data['pwaforwp_whitelable_description'] = sanitize_text_field($field['var_value']);
            }
            // navigation bar features start            
            if (isset($field['var_name']) && $field['var_name'] == 'pwaforwp_settings[navigation][text_font_size]') {
                $navigation_bar_data['navigation']['text_font_size'] = sanitize_text_field($field['var_value']);
            }
            if (isset($field['var_name']) && $field['var_name'] == 'pwaforwp_settings[navigation][text_font_color]') {
                $navigation_bar_data['navigation']['text_font_color'] = sanitize_text_field($field['var_value']);
            }
            if (isset($field['var_name']) && $field['var_name'] == 'pwaforwp_settings[navigation][selected_text_font_color]') {
                $navigation_bar_data['navigation']['selected_text_font_color'] = sanitize_text_field($field['var_value']);
            }
            if (isset($field['var_name']) && $field['var_name'] == 'pwaforwp_settings[navigation][selected_menu_background_color]') {
                $navigation_bar_data['navigation']['selected_menu_background_color'] = sanitize_text_field($field['var_value']);
            }
            if (isset($field['var_name']) && $field['var_name'] == 'pwaforwp_settings[navigation][text_background_color]') {
                $navigation_bar_data['navigation']['text_background_color'] = sanitize_text_field($field['var_value']);
            }
            if (isset($field['var_name']) && $field['var_name'] == 'pwaforwp_settings[navigation][excluded_pages]') {
                if (!empty($field['var_value']) && is_array($field['var_value'])) {
                    $navigation_bar_data['navigation']['excluded_pages'] = sanitize_text_field(implode(',', $field['var_value']));
                }
            }
            // navigation bar features end

            // UTM Tracking features start
            if (isset($field['var_name']) && $field['var_name'] == 'pwaforwp_settings[utm_details][utm_source]') {
                $utm_trackings['utm_details']['utm_source'] = sanitize_text_field($field['var_value']);
            }
            if (isset($field['var_name']) && $field['var_name'] == 'pwaforwp_settings[utm_details][utm_medium]') {
                $utm_trackings['utm_details']['utm_medium'] = sanitize_text_field($field['var_value']);
            }
            if (isset($field['var_name']) && $field['var_name'] == 'pwaforwp_settings[utm_details][utm_campaign]') {
                $utm_trackings['utm_details']['utm_campaign'] = sanitize_text_field($field['var_value']);
            }
            if (isset($field['var_name']) && $field['var_name'] == 'pwaforwp_settings[utm_details][utm_term]') {
                $utm_trackings['utm_details']['utm_term'] = sanitize_text_field($field['var_value']);
            }
            if (isset($field['var_name']) && $field['var_name'] == 'pwaforwp_settings[utm_details][utm_content]') {
                $utm_trackings['utm_details']['utm_content'] = sanitize_text_field($field['var_value']);
            }
            if (isset($field['var_name']) && $field['var_name'] == 'pwaforwp_settings[utm_details][pwa_utm_change_track]') {
                $utm_trackings['utm_details']['pwa_utm_change_track'] = sanitize_text_field($field['var_value']);
            }
            // UTM Tracking features end
                    
            $variable = str_replace(array('pwaforwp_settings[', ']'), array('',''), $field['var_name']);
            if(strpos($variable, '[')!==false) {
                $varArray = explode("[", $variable);
                $newArr = preg_replace('/\\\\/', '', sanitize_textarea_field($field['var_value']));
                if(is_array($newArr) && !empty($newArr)) {
                    foreach (array_reverse($varArray) as $key) {
                        $newArr = [$key => $newArr];
                    }
                    
                    $actualFields = pwaforwp_merge_recursive_ex($actualFields, $newArr);
                }else{
                    
                    if (isset($actualFields[$varArray[0]][$varArray[1]])) {
                        $actualFields[$varArray[0]][$varArray[1]] = preg_replace('/\\\\/', '', sanitize_textarea_field($field['var_value']));
                    }

                    // for quick action or holding three array index
                    
                    if (isset($varArray[0]) && isset($varArray[1]) && isset($varArray[2]) ) {
                        $quick_action[$varArray[0]][$varArray[1]][$varArray[2]] = preg_replace('/\\\\/', '', sanitize_textarea_field($field['var_value']));
                    }
                }
                
            }else{
                $actualFields[$variable] = preg_replace('/\\\\/', '', sanitize_textarea_field($field['var_value']));
            }

        }
        if(!empty($whitelabel_data)) {
            whitelable_for_pwa_custom_config_file($whitelabel_data);
        }
        if(!empty($navigation_bar_data)) {
            if(isset($navigation_bar_data['navigation']) && count($navigation_bar_data['navigation']) >= 3) {
                $pre_settings = pwaforwp_defaultSettings();
                $actualFields = wp_parse_args($navigation_bar_data, $pre_settings);
            }
        }
        if(!empty($quick_action)) {
            $pre_settings = pwaforwp_defaultSettings();
            $actualFields = wp_parse_args($quick_action, $pre_settings);
        }
        if(!empty($utm_trackings) && isset($utm_trackings['utm_details'])) {
            $pre_settings = pwaforwp_defaultSettings();
            $actualFields = wp_parse_args($utm_trackings, $pre_settings);
        }

        if(isset($actualFields['precaching_feature'])) {
            if($actualFields['precaching_feature']==1) {
                $actualFields['precaching_automatic'] = 1;
                $actualFields['precaching_automatic_post'] = 1;
            }elseif($actualFields['precaching_feature']==0) {
                $actualFields['precaching_automatic'] = 0;
                $actualFields['precaching_automatic_post'] = 0;
            }
        }
        $include_targeting_type_array = array();
        $include_targeting_value_array = array();
        $visibility_flag = false;
        $visibility_data = false;
        
        if(!empty($allFields) && is_array($allFields)) {
            foreach ($allFields as $key => $value) {
                $key = sanitize_key($key);
                if($value['var_name']=="include_targeting_type") {
                     $include_targeting_type_array[] = sanitize_text_field($value['var_value']);
                    $visibility_data = true;
                }
                if($value['var_name']=="include_targeting_data") {
                            $include_targeting_value_array[] = sanitize_text_field($value['var_value']);
                }

                if($value['var_name']=="pwaforwp_visibility_flag") {
                    $visibility_flag = true;
                }
            }
        }
        
        if (!empty($include_targeting_type_array) && is_array($include_targeting_type_array)) {
            $include_targeting_type = implode(',', $include_targeting_type_array);
            $actualFields['include_targeting_type'] = $include_targeting_type; 
        }else{
            $actualFields['include_targeting_type'] = '';
        } 
        if (!empty($include_targeting_value_array) && is_array($include_targeting_value_array)) {
            $include_targeting_value = implode(',', $include_targeting_value_array);
            $actualFields['include_targeting_value'] = $include_targeting_value; 
        }else{
            $actualFields['include_targeting_value'] = '';
        }
        
        $exclude_targeting_type_array = array();
        $exclude_targeting_value_array = array();
        if(!empty($allFields) && is_array($allFields)) {
            foreach ($allFields as $key => $value) {
                if($value['var_name']=="exclude_targeting_type") {
                    $exclude_targeting_type_array[] = sanitize_text_field($value['var_value']);
                    $visibility_data = true;
                }
                if($value['var_name']=="exclude_targeting_data") {
                    $exclude_targeting_value_array[] = sanitize_text_field($value['var_value']);
                }
                if($value['var_name']=="pwaforwp_visibility_flag") {
                    $visibility_flag = true;
                }
            }
        }
        if (!empty($exclude_targeting_type_array) && is_array($exclude_targeting_type_array)) {
            $exclude_targeting_type = implode(',', $exclude_targeting_type_array);
            $actualFields['exclude_targeting_type'] = $exclude_targeting_type; 
        }else{
            $actualFields['exclude_targeting_type'] = '';
        }
        if (!empty($exclude_targeting_value_array) && is_array($exclude_targeting_value_array)) {
            $exclude_targeting_value = implode(',', $exclude_targeting_value_array);
            $actualFields['exclude_targeting_value'] = $exclude_targeting_value; 
        }else{
            $actualFields['exclude_targeting_value'] = '';
        }
        if(isset($actualFields['addtohomebanner_feature'])) {
            if($actualFields['addtohomebanner_feature']==1) {
                $actualFields['custom_add_to_home_setting'] = 1;
            }elseif($actualFields['addtohomebanner_feature']==0) {
                $actualFields['custom_add_to_home_setting'] = 0;
            }
        }
        if(isset($actualFields['loader_feature'])) {
            if($actualFields['loader_feature']==1) {
                $actualFields['loading_icon'] = 1;
            }elseif($actualFields['loader_feature']==0) {
                $actualFields['loading_icon'] = 0;
            }
        }
        if(isset($actualFields['utmtracking_feature'])) {
            if($actualFields['utmtracking_feature']==1) {
                $actualFields['utm_setting'] = 1;
            }elseif($actualFields['utmtracking_feature']==0) {
                $actualFields['utm_setting'] = 0;
            }
        }
        if(isset($actualFields['fcm_config']) && $actualFields['fcm_config']) {
            $actualFields['fcm_config'] = wp_unslash($actualFields['fcm_config']);
        }
        
        $pre_settings = pwaforwp_defaultSettings();
        $actualFields = wp_parse_args($actualFields, $pre_settings);
        
        if($visibility_flag === true && $visibility_data === false ) {
            
            $actualFields['include_targeting_type'] = '';
            $actualFields['include_targeting_value'] = '';
            $actualFields['include_targeting_data'] = '';
            $actualFields['exclude_targeting_type'] = '';
            $actualFields['exclude_targeting_value'] = '';
            $actualFields['exclude_targeting_data'] = '';
        }


        //dependent settings
        if(isset($actualFields['utm_setting']) && $actualFields['utm_setting']==0) {
            $actualFields['utmtracking_feature'] = $actualFields['utm_setting'];
        }
        if(isset($actualFields['loading_icon']) && $actualFields['loading_icon']==0) {
            $actualFields['loader_feature'] = $actualFields['loading_icon'];
        }

        
        if(isset($actualFields['custom_add_to_home_setting']) && $actualFields['custom_add_to_home_setting']==0) {
            $actualFields['addtohomebanner_feature'] = $actualFields['custom_add_to_home_setting'];
        }
        

        $actualFields = apply_filters('pwaforwp_features_update_data_save', $actualFields);

        update_option('pwaforwp_settings', $actualFields);
        global $pwaforwp_settings;
        $pwaforwp_settings = array();
        pwaforwp_required_file_creation();
                echo wp_json_encode(array('status'=> 200, 'message'=> esc_html__('Settings Saved.', 'pwa-for-wp'), 'options'=>$actualFields));
                        wp_die();
    }else{
            echo wp_json_encode(array('status'=> 503, 'message'=> esc_html__('Fields not defined', 'pwa-for-wp')));
            wp_die();
    }
}

add_action('activated_plugin', 'pwaforwp_active_update_transient');
function pwaforwp_active_update_transient($plugin)
{
    delete_transient('pwaforwp_restapi_check'); 
}
add_action('deactivated_plugin', 'pwaforwp_deactivate_update_transient');
function pwaforwp_deactivate_update_transient($plugin)
{
    delete_transient('pwaforwp_restapi_check'); 
}

add_action("wp_ajax_pwaforwp_include_visibility_setting_callback", 'pwaforwp_include_visibility_setting_callback');
function pwaforwp_include_visibility_setting_callback()
{
    if (! current_user_can(pwaforwp_current_user_can()) ) {
        return;
    }
    if (! isset($_POST['pwaforwp_security_nonce']) ) {
        return; 
    }
	// phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized, WordPress.Security.ValidatedSanitizedInput.MissingUnslash
    if (!wp_verify_nonce($_POST['pwaforwp_security_nonce'], 'pwaforwp_ajax_check_nonce') ) {
        return;  
    } 
    // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized, WordPress.Security.ValidatedSanitizedInput.MissingUnslash, WordPress.Security.ValidatedSanitizedInput.InputNotValidated
        $include_type = sanitize_text_field($_POST['include_type']);

    if($include_type == 'post' || $include_type == 'page') {
        $args = array(
            'post_type' => $include_type,
            'post_status' => 'publish',
            'posts_per_page' => 50,
         );  
        $query = new WP_Query($args);
        $option ='<option value="">Select '.esc_html($include_type).' Type</option>';
        while ($query->have_posts()) : $query->the_post();
                    
            $option .= '<option value="'.get_the_title().'">'.get_the_title().'</option>';
        endwhile; 
        wp_reset_postdata();
    }
    if(in_array($include_type, array('post_type','globally'))) {
        if($include_type == 'post_type') {
            // $get_option = array('post', 'page', 'product');
            $get_option = get_post_types();;
            $option ='<option value="">'.esc_html__('Select Post Type', 'pwa-for-wp').'</option>';
        }
        if($include_type == 'globally') { 
            $get_option = array('Globally');
            $option ='<option value="">'.esc_html__('Select Global Type', 'pwa-for-wp').'</option>';
        }
        if(!empty($get_option) && is_array($get_option)) {        
            foreach ($get_option as $options_array) {
                $option .= '<option value="'.esc_attr($options_array).'">'.esc_html($options_array).'</option>';
            }
        }
    }

    if($include_type == 'post_category') {
        $get_option = get_categories(
            array(
            'hide_empty' => true,
            )
        );
        $option ='<option value="">'.esc_html__('Select Post Category', 'pwa-for-wp').'</option>';
        if(!empty($get_option) && is_array($get_option)) {   
            foreach ($get_option as $options_array) {
                $option .= '<option value="'.esc_attr($options_array->name).'">'.esc_html($options_array->name).'</option>';
            }
        }
       
    }
    if($include_type == 'taxonomy') { 
        $get_option = get_terms(
            array(
            'hide_empty' => true,
            ) 
        );
        $option ='<option value="">'.esc_html__('Select Taxonomy', 'pwa-for-wp').'</option>';
        if(!empty($get_option) && is_array($get_option)) {  
            foreach ($get_option as $options_array) {
                $option .= '<option value="'.esc_attr($options_array->name).'">'.esc_html($options_array->name).'</option>';
            }
        }
    }

    if($include_type == 'tags') { 
        $get_option = get_tags(
            array(
            'hide_empty' => false
            )
        );
        $option ='<option value="">'.esc_html__('Select Tag', 'pwa-for-wp').'</option>';
        if(!empty($get_option) && is_array($get_option)) {  
            foreach ($get_option as $options_array) {
                $option .= '<option value="'.esc_attr($options_array->name).'">'.esc_html($options_array->name).'</option>';
            }
        }

    }

    if($include_type == 'user_type') { 
        $get_options = array("administrator"=>"Administrator", "editor"=>"Editor", "author"=>"Author", "contributor"=>"Contributor","subscriber"=>"Subscriber");
        $get_option = $get_options;
        $option ='<option value="">'.esc_html__('Select User', 'pwa-for-wp').'</option>';
        if(!empty($get_option) && is_array($get_option)) {   
            foreach ($get_option as $key => $value) {
                $option .= '<option value="'.esc_attr($key).'">'.esc_html($value).'</option>';
            }
        }

    }

    if($include_type == 'page_template') { 
        $get_option = wp_get_theme()->get_page_templates();
        $option ='<option value="">'.esc_html__('Select Page Template', 'pwa-for-wp').'</option>';
        if(!empty($get_option) && is_array($get_option)) {   
            foreach ($get_option as $key => $value) {
                $option .= '<option value="'.esc_attr($value).'">'.esc_html($value).'</option>';
            }
        }
    }

    $data = array('success' => 1,'message'=>esc_html__('Success', 'pwa-for-wp'),'option'=>$option );
    echo wp_json_encode($data);    exit;

}

add_action("wp_ajax_pwaforwp_include_visibility_condition_callback", 'pwaforwp_include_visibility_condition_callback');

function pwaforwp_include_visibility_condition_callback()
{
    if (! current_user_can(pwaforwp_current_user_can()) ) {
        return;
    }
    if (! isset($_POST['pwaforwp_security_nonce']) ) {
        return; 
		// phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized, WordPress.Security.ValidatedSanitizedInput.MissingUnslash
    }
	// phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized, WordPress.Security.ValidatedSanitizedInput.MissingUnslash
    if (!wp_verify_nonce($_POST['pwaforwp_security_nonce'], 'pwaforwp_ajax_check_nonce') ) {
        return;  
    }
    // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized, WordPress.Security.ValidatedSanitizedInput.MissingUnslash, WordPress.Security.ValidatedSanitizedInput.InputNotValidated
    $include_targeting_type = sanitize_text_field($_POST['include_targeting_type']);
	// phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized, WordPress.Security.ValidatedSanitizedInput.MissingUnslash, WordPress.Security.ValidatedSanitizedInput.InputNotValidated
    $include_targeting_data = sanitize_text_field($_POST['include_targeting_data']);

    $rand = time().wp_rand(000, 999);
    $option .= '<span class="pwaforwp-visibility-target-icon-'.esc_attr($rand).'">
    <input type="hidden" name="include_targeting_type" value="'.esc_attr($include_targeting_type).'">
    <input type="hidden" name="include_targeting_data" value="'.esc_attr($include_targeting_data).'">';
    $include_targeting_type = pwaforwpRemoveExtraValue($include_targeting_type);
    $include_targeting_data = pwaforwpRemoveExtraValue($include_targeting_data);
    $option .= '<span class="pwaforwp-visibility-target-item"><span class="visibility-include-target-label">'.esc_html($include_targeting_type.' - '.$include_targeting_data).'</span>
        <span class="pwaforwp-visibility-target-icon" data-index="0"><span class="dashicons dashicons-no-alt " aria-hidden="true" onclick="removeIncluded_visibility('.esc_attr($rand).')"></span></span></span></span>';

    $data = array('success' => 1,'message'=>esc_html__('Success', 'pwa-for-wp'),'option'=>$option );
    echo wp_json_encode($data);    exit;
}

add_action("wp_ajax_pwaforwp_exclude_visibility_condition_callback", 'pwaforwp_exclude_visibility_condition_callback');

function pwaforwp_exclude_visibility_condition_callback()
{
    if (! current_user_can(pwaforwp_current_user_can()) ) {
        return;
    }
    if (! isset($_POST['pwaforwp_security_nonce']) ) {
        return; 
    }
	// phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized, WordPress.Security.ValidatedSanitizedInput.MissingUnslash, WordPress.Security.ValidatedSanitizedInput.InputNotValidated
    if (!wp_verify_nonce($_POST['pwaforwp_security_nonce'], 'pwaforwp_ajax_check_nonce') ) {
        return;  
    } 
    // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized, WordPress.Security.ValidatedSanitizedInput.MissingUnslash, WordPress.Security.ValidatedSanitizedInput.InputNotValidated
    $exclude_targeting_type = sanitize_text_field($_POST['exclude_targeting_type']);
	// phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized, WordPress.Security.ValidatedSanitizedInput.MissingUnslash, WordPress.Security.ValidatedSanitizedInput.InputNotValidated
    $exclude_targeting_data = sanitize_text_field($_POST['exclude_targeting_data']);

    $rand = time().wp_rand(000, 999);
    $option .= '<span class="pwaforwp-visibility-target-icon-'.esc_attr($rand).'">
    <input type="hidden" name="exclude_targeting_type" value="'.esc_attr($exclude_targeting_type).'">
    <input type="hidden" name="exclude_targeting_data" value="'.esc_attr($exclude_targeting_data).'">';

    $exclude_targeting_type = pwaforwpRemoveExtraValue($exclude_targeting_type);
    $exclude_targeting_data = pwaforwpRemoveExtraValue($exclude_targeting_data);
    $option .= '<span class="pwaforwp-visibility-target-item"><span class="visibility-include-target-label">'.esc_html($exclude_targeting_type.' - '.$exclude_targeting_data).'</span>
        <span class="pwaforwp-visibility-target-icon" data-index="0"><span class="dashicons dashicons-no-alt " aria-hidden="true" onclick="removeIncluded_visibility('.esc_attr($rand).')"></span></span></span></span>';

    $data = array('success' => 1,'message'=>esc_html__('Success', 'pwa-for-wp'),'option'=>$option );
    echo wp_json_encode($data);    exit;
}

function pwaforwpRemoveExtraValue($val)
{
    $val = str_replace("_", " ", $val);
    $val = str_replace(".php", "", $val);
    $val = ucwords($val);
    return $val;
}

/**
* Function Create images dynamically
 *
* @param Array $old_value previous values
* @param Array $new_value new updated values of save
*/
add_action('update_option_pwaforwp_settings', 'pwaforwp_resize_images', 10, 3);
function pwaforwp_resize_images( $old_value, $new_value, $option='' )
{
    
    if(isset($new_value['ios_splash_icon']['2048x1496']) && !empty($new_value['ios_splash_icon']['2048x1496']) && strrpos($new_value['ios_splash_icon']['2048x1496'], 'uploads/') ) {
        $uploadPath = wp_upload_dir();
        $filename = str_replace($uploadPath['baseurl'], $uploadPath['basedir'], $new_value['ios_splash_icon']['2048x1496']);
        if(file_exists($filename) ) {
            //Check there is need of file creation
            $createImage = array();
            if(!empty($new_value['ios_splash_icon']) && is_array($new_value['ios_splash_icon'])) {   
                foreach ($new_value['ios_splash_icon'] as $key => $value) {
                    if(empty($value)) {
                          $createImage[$key] = '';
                    }
                }
            }
            if(count($createImage)>0) {
                $editor = wp_get_image_editor($filename, array());
                if(!empty($createImage) && is_array($createImage)) {   
                    foreach ($createImage as $newkey => $newimages) {
                    
                        // Grab the editor for the file and the size of the original image.
                        if (!is_wp_error($editor) ) {
                             // Get the dimensions for the size of the current image.
                             $dimensions = $editor->get_size();
                             $width = $dimensions['width'];
                             $height = $dimensions['height'];
                        

                             // Calculate the new dimensions for the image.
                             $keyDim = explode('x', $newkey);
                             $newWidth = $keyDim[0];
                             $newHeight = $keyDim[1];

                             // Resize the image.
                             $result = $editor->resize($newWidth, $newHeight, true);

                             // If there's no problem, save it; otherwise, print the problem.
                            if (!is_wp_error($result)) {
                                $newImage = $editor->save($editor->generate_filename());
                                $newfilename = str_replace($uploadPath['basedir'], $uploadPath['baseurl'], $newImage['path']);
                                $new_value['ios_splash_icon'][$newkey] = sanitize_text_field($newfilename);
                            }else{
                 // phpcs:ignore WordPress.PHP.DevelopmentFunctions.error_log_error_log
                                error_log($result->get_error_message()." Width: ".$newWidth." Height:".$newHeight);
                            }
                        }

                    }
                }//Foreach closed
                update_option('pwaforwp_settings', $new_value);

            }
        }
    }

    

}



if (! function_exists('pwaforwp_splashscreen_uploader') ) {
    add_action('wp_ajax_pwaforwp_splashscreen_uploader', 'pwaforwp_splashscreen_uploader');

    function pwaforwp_splashscreen_uploader()
    {
        if (! isset($_GET['pwaforwp_security_nonce']) ) {
            echo wp_json_encode(array( "status" => 500, "message" => esc_html__('Failed! Security check not active', 'pwa-for-wp') ));
            wp_die();
        }
     // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotValidated, WordPress.Security.ValidatedSanitizedInput.MissingUnslash, WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
        if (! wp_verify_nonce($_GET['pwaforwp_security_nonce'], 'pwaforwp_ajax_check_nonce') ) {
                echo wp_json_encode(array( "status" => 500, "message" => esc_html__("Failed! Security check", 'pwa-for-wp') ));
                wp_die();
        }
        if(! current_user_can('manage_options') ) {
                echo wp_json_encode(array( "status" => 401, "message" => esc_html__("Failed! you are not autherized to save", 'pwa-for-wp') ));
                wp_die();
        }
        $pwaforwp_settings = pwaforwp_defaultSettings();

        // 
        $upload = wp_upload_dir();
        $path = $upload['basedir'] . "/pwa-splash-screen/";
        
        // Ensure WP_Filesystem is initialized
        if (! function_exists('WP_Filesystem') ) {
            include_once ABSPATH . 'wp-admin/includes/file.php';
        }
        
        global $wp_filesystem;
        WP_Filesystem();
        
        // Create the directory using WP_Filesystem
        $wp_filesystem->mkdir($path);
        
        // Write the index.html file using WP_Filesystem
        $wp_filesystem->put_contents($path . '/index.html', '', FS_CHMOD_FILE);
        
        // Define the zip file path
        $zipfilename = $path . "file.zip";
        
        // Open input stream
        $input = fopen('php://input', 'rb');
        
        // Capture the content from the input stream
        $content = stream_get_contents($input);
        
        // Write the content to the ZIP file using WP_Filesystem
        $wp_filesystem->put_contents($zipfilename, $content, FS_CHMOD_FILE);
        
        // Close the input stream
     // phpcs:ignore WordPress.WP.AlternativeFunctions.file_system_operations_fclose -- it’s the correct and necessary way to close the resource, it should be accepted.
        fclose($input);

        unzip_file($zipfilename, $path);
        $pathURL = $upload['baseurl']."/pwa-splash-screen/splashscreens/";
        $iosdata = pwaforwp_ios_splashscreen_files_data();

        if (is_array($iosdata) && ! empty($iosdata) ) {
            foreach ( $iosdata as $key => $value ) {
                $pwaforwp_settings['ios_splash_icon'][sanitize_key($key)] = sanitize_text_field($pathURL.$value['file']);
            }
        }

        $pwaforwp_settings['iosSplashScreenOpt'] = 'generate-auto';

        update_option('pwaforwp_settings', $pwaforwp_settings);
        wp_delete_file($zipfilename);
                echo wp_json_encode(array( "status" => 200, "message" => esc_html__("Splash screen uploaded successfully", "pwa-for-wp") ));
                wp_die();
    }
}

add_filter('pre_update_option_pwaforwp_settings', 'pwaforwp_update_force_update', 10, 3); 
function pwaforwp_update_force_update( $value, $old_value, $option)
{
    if(! function_exists('wp_get_current_user')) {
        return $value;
    }
    $user = wp_get_current_user();
    $allowed_roles = array('administrator');
    if(! array_intersect($allowed_roles, $user->roles) ) {
        return $value;
    }
    if(isset($value['force_update_sw_setting'])) {
        $version = $value['force_update_sw_setting'];
        if($version) {
            $version = explode(".", $version);
            if(count($version)<=3) {
                $version = implode(".", (array)$version).".1";
            }else{
                $version[count($version)-1] = $version[count($version)-1]+1;
                $version = implode(".", (array)$version);
            }
        }
        $value['force_update_sw_setting'] = $version;
    }
    return $value;
}

/**
 * Show the loaders on admin section
 *
 * @return Javascript/text [print required javascript to show loader] 
 */
function pwaforwp_loading_icon_scripts()
{
    echo "<script type='text/javascript'>window.addEventListener('beforeunload', function(){
    if(document.getElementsByClassName('pwaforwp-loading-wrapper') && typeof document.getElementsByClassName('pwaforwp-loading-wrapper')[0]!=='undefined'){
      document.getElementsByClassName('pwaforwp-loading-wrapper')[0].style.display = 'flex';
    }
    if(document.getElementById('pwaforwp_loading_div')){
      document.getElementById('pwaforwp_loading_div').style.display = 'flex';
    }
    if(document.getElementById('pwaforwp_loading_icon')){
      document.getElementById('pwaforwp_loading_icon').style.display = 'flex';
    }
  });
  if(document.getElementsByClassName('pwaforwp-loading-wrapper') && document.getElementsByClassName('pwaforwp-loading-wrapper').length > 0){
    var tot = document.getElementsByClassName('pwaforwp-loading-wrapper');
    for (var i = 0; i < tot.length; i++) {
      tot[i].style.display = 'none';
    }
  }
  if(document.getElementById('pwaforwp_loading_div')){
    document.getElementById('pwaforwp_loading_div').style.display = 'none';
  }
  if(document.getElementById('pwaforwp_loading_icon')){
    document.getElementById('pwaforwp_loading_icon').style.display = 'none';
  }</script>";
}
/**
 * Show the loaders on admin section
 *
 * @return css/text [print required styles to show loader] 
 */
function pwaforwp_loading_icon_styles()
{
    echo '<style>#pwaforwp_loading_div {width: 100%;height: 200%;position: fixed;top: 0;left: 0;background-color: white;z-index: 9999;}
	.pwaforwp-loading-wrapper{display:none;}
	#pwaforwp_loading_icon {position: fixed;left: 50%;top: 50%;z-index: 10000;margin: -60px 0 0 -60px;border: 16px solid #f3f3f3;border-radius: 50%;border-top: 16px solid #3498db;width: 120px;height: 120px;-webkit-animation: spin 2s linear infinite;animation: spin 2s linear infinite;}

	@-webkit-keyframes spin {0% { -webkit-transform: rotate(0deg); }100% { -webkit-transform: rotate(360deg); }}
	@keyframes spin {0% { transform: rotate(0deg); }100% { transform: rotate(360deg); }}
	</style>';
}

function pwaforwp_loading_select2_styles()
{
    echo '<style>
	.select2-container .select2-selection--single {
		height:44px !important;
		vertical-align: middle;
	}
	.select2-container--default .select2-selection--single .select2-selection__rendered {
		line-height: 40px !important;
	}
	.select2-container{z-index:999999}
	</style>';
}

/**
 * pwaforwp_merge_recursive_ex merge any multidimensional Array
 *
 * @param Array1(array) Array2(array)
 */
function pwaforwp_merge_recursive_ex(array $array1, array $array2)
{
    $merged = $array1;
    if(is_array($array2) && !empty($array2)) {
        foreach ($array2 as $key => & $value) {
            $key = sanitize_key($key);
            $value = sanitize_text_field($value);
            if (is_array($value) && isset($merged[$key]) && is_array($merged[$key])) {
                 $merged[$key] = pwaforwp_merge_recursive_ex($merged[$key], $value);
            } else if (is_numeric($key)) {
                if (!in_array($value, $merged)) {
                    $merged[] = $value;
                }
            } else {
                $merged[$key] = $value;
            }
        }
    }

    return $merged;
}

function pwaforwp_get_data_by_type($include_type='post',$search=null)
{
    $result = array();
    $posts_per_page = 50;
    
    if($include_type == 'post' || $include_type == 'page') {
        $args = array(
        'post_type' => $include_type,
        'post_status' => 'publish',
        'posts_per_page' => $posts_per_page,
        );
        if(!empty($search)) {
            $args['s']    = $search;
        }

        $meta_query = new WP_Query($args);        
            
        if($meta_query->have_posts()) {
            while($meta_query->have_posts()) {
                $meta_query->the_post();
                $result[] = array('id' => get_the_ID(), 'text' => get_the_title());
            }
            wp_reset_postdata();
        }
        
    }
    if(in_array($include_type, array('post_type','globally'))) {
        if($include_type == 'post_type') {
            $args['public'] = true;
            if(!empty($search)) {
                $args['name']    = $search;
            }
            $get_option = get_post_types($args, 'names');
        }
        if($include_type == 'globally') { 
            $get_option = array('Globally');
        }
        if(!empty($get_option) && is_array($get_option)) {        
            foreach ($get_option as $options_array) {
                $result[] = array('id' => $options_array, 'text' => $options_array);
            }
        }
    }

    if ($include_type == 'post_category' ) {

        $args = array( 
        'taxonomy'   => 'category',
        'hide_empty' => true,
        'number'     => $posts_per_page, 
        );

        if(!empty($search)) {
            $args['name__like'] = $search;
        }

        $get_option = get_terms($args);

        if (! empty($get_option) && is_array($get_option) ) {   

            foreach ( $get_option as $options_array ) {
                $result[] = array( 'id' => $options_array->name, 'text' => $options_array->name );
            }
        }
       
    }

    if($include_type == 'taxonomy') {
        $args = array( 
        'hide_empty' => true,
        'number'     => $posts_per_page, 
        );

        if(!empty($search)) {
            $args['name__like'] = $search;
        }
        $get_option = get_terms($args);
        if(!empty($get_option) && is_array($get_option)) {  
            foreach ($get_option as $options_array) {
                $result[] = array('id' => $options_array->name, 'text' => $options_array->name);
            }
        }
    }

    if($include_type == 'tags') {
        $args['hide_empty'] = false;
        $get_option = get_tags($args);
        if(!empty($get_option) && is_array($get_option)) {  
            foreach ($get_option as $options_array) {
                $result[] = array('id' => $options_array->name, 'text' => $options_array->name);
            }
        }
    }

    if($include_type == 'user_type') { 
        $get_options = array("administrator"=>"Administrator", "editor"=>"Editor", "author"=>"Author", "contributor"=>"Contributor","subscriber"=>"Subscriber");
        $get_option = $get_options;
        if(!empty($get_option) && is_array($get_option)) {   
            foreach ($get_option as $key => $value) {
                $result[] = array('id' => $key, 'text' => $value);
            }
        }

    }

    if($include_type == 'page_template') { 
        $get_option = wp_get_theme()->get_page_templates();
        if(!empty($get_option) && is_array($get_option)) {   
            foreach ($get_option as $key => $value) {
                $result[] = array('id' => $value, 'text' => $value);
            }
        }
    }

    return $result;
}


function pwaforwp_get_select2_data()
{
    if (! isset($_GET['pwaforwp_security_nonce']) ) {
        return; 
    }
    if (! current_user_can(pwaforwp_current_user_can()) ) {
        return;
    }
	// phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotValidated, WordPress.Security.ValidatedSanitizedInput.MissingUnslash, WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
    if ((wp_verify_nonce($_GET['pwaforwp_security_nonce'], 'pwaforwp_ajax_check_nonce') )) {
     // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.MissingUnslash
        $search        = isset($_GET['q']) ? sanitize_text_field($_GET['q']) : '';
     // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.MissingUnslash                                   
        $type          = isset($_GET['type']) ? sanitize_text_field($_GET['type']) : '';
        
        $result = pwaforwp_get_data_by_type($type, $search);        
        wp_send_json(['results' => $result]);            

    }else{
        return;  
    }
    wp_die();
}

add_action('wp_ajax_pwaforwp_get_select2_data', 'pwaforwp_get_select2_data');

function pwaforwp_enqueue_select2_js( $hook )
{
    if($hook  == 'toplevel_page_pwaforwp') {

        wp_dequeue_script('select2-js');   
        wp_dequeue_script('select2');
        wp_deregister_script('select2');
        //conflict with jupitor theme fixed starts here
        wp_dequeue_script('mk-select2');
        wp_deregister_script('mk-select2');                
        //conflict with jupitor theme fixed ends here                
        wp_dequeue_script('wds-shared-ui');
        wp_deregister_script('wds-shared-ui');
        wp_dequeue_script('pum-admin-general');
        wp_deregister_script('pum-admin-general');
        //Hide vidoe pro select2 on schema type dashboard
        wp_dequeue_script('cmb-select2');
        wp_deregister_script('cmb-select2');

        wp_enqueue_style('pwaforwp-select2-style', PWAFORWP_PLUGIN_URL. 'assets/css/select2.min.css', false, PWAFORWP_PLUGIN_VERSION);
        wp_enqueue_script('select2', PWAFORWP_PLUGIN_URL. 'assets/js/select2.min.js', array( 'jquery'), PWAFORWP_PLUGIN_VERSION, true);
        wp_enqueue_script('select2-extended-script', PWAFORWP_PLUGIN_URL. 'assets/js/select2-extended.min.js', array( 'jquery' ), PWAFORWP_PLUGIN_VERSION, true);
    }

}
add_action('admin_enqueue_scripts', 'pwaforwp_enqueue_select2_js', 9999);
