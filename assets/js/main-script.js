function pwaforwpGetParamByName(name, url) {
    if (!url) url = window.location.href;
    name = name.replace(/[\[\]]/g, "\\$&");
    var regex = new RegExp("[?&]" + name + "(=([^&#]*)|&|#|$)"),
        results = regex.exec(url);
    if (!results) return null;
    if (!results[2]) return '';
    return decodeURIComponent(results[2].replace(/\+/g, " "));
}

jQuery(document).ready(function($){
    jQuery(".pwaforwp-colorpicker").wpColorPicker(); // Color picker
    jQuery(".pwaforwp-fcm-push-icon-upload").click(function(e) { // Application Icon upload
        e.preventDefault();
        var pwaforwpMediaUploader = wp.media({
            title: pwaforwp_obj.uploader_title,
            button: {
                text: pwaforwp_obj.uploader_button
            },
            multiple: false,  // Set this to true to allow multiple files to be selected
                        library:{type : 'image'}
        })
        .on("select", function() {
            var attachment = pwaforwpMediaUploader.state().get("selection").first().toJSON();
            jQuery(".pwaforwp-fcm-push-icon").val(attachment.url);
        })
        .open();
    });
    jQuery(".pwaforwp-fcm-push-budge-icon-upload").click(function(e) { // Application Icon upload
        e.preventDefault();
        var pwaforwpMediaUploader = wp.media({
            title: pwaforwp_obj.uploader_title,
            button: {
                text: pwaforwp_obj.uploader_button
            },
            multiple: false,  // Set this to true to allow multiple files to be selected
            library:{type : 'image'}
        })
        .on("select", function() {
            var attachment = pwaforwpMediaUploader.state().get("selection").first().toJSON();
            jQuery(".pwaforwp-fcm-push-budge-icon").val(attachment.url);
        })
        .open();
    });
    jQuery(".pwaforwp-icon-upload").click(function(e) {  // Application Icon upload
        e.preventDefault();
        var pwaforwpMediaUploader = wp.media({
            title: pwaforwp_obj.uploader_title,
            button: {
                text: pwaforwp_obj.uploader_button
            },
            multiple: false,  // Set this to true to allow multiple files to be selected
                        library:{type : 'image'}
        })
        .on("select", function() {
            var attachment = pwaforwpMediaUploader.state().get("selection").first().toJSON();
            jQuery(".pwaforwp-icon").val(attachment.url);
         })
        .open();
    });
    jQuery(".pwaforwp_whitelable_logo").click(function(e) {  // Application Icon upload
        e.preventDefault();
        var pwaforwpMediaUploader = wp.media({
            title: pwaforwp_obj.uploader_title,
            button: {
                text: pwaforwp_obj.uploader_button
            },
            multiple: false,  // Set this to true to allow multiple files to be selected
                        library:{type : 'image'}
        })
        .on("select", function() {
            var attachment = pwaforwpMediaUploader.state().get("selection").first().toJSON();
            jQuery(".pwaforwp_whitelable_logo").val(attachment.url);
         })
        .open();
    });
    jQuery(".pwaforwp-monochrome-upload").click(function(e) {  // monochrome upload
        e.preventDefault();
        var pwaforwpMediaUploader = wp.media({
            title: pwaforwp_obj.uploader_title,
            button: {
                text: pwaforwp_obj.uploader_button
            },
            multiple: false,  // Set this to true to allow multiple files to be selected
                        library:{type : 'image'}
        })
        .on("select", function() {
            var attachment = pwaforwpMediaUploader.state().get("selection").first().toJSON();
            jQuery(".pwaforwp-monochrome").val(attachment.url);
         })
        .open();
    });
    
    jQuery(".pwaforwp-screenshots-upload").click(function(e) {  // Application screenshots upload
        e.preventDefault();
        this__ = jQuery(this).parents('.js_clone_div');
        var pwaforwpMediaUploader = wp.media({
            title: pwaforwp_obj.uploader_title,
            button: {
                text: pwaforwp_obj.uploader_button
            },
            multiple: false,  // Set this to true to allow multiple files to be selected
                        library:{type : 'image'}
        })
        .on("select", function() {
            var attachment = pwaforwpMediaUploader.state().get("selection").first().toJSON();
            this__.find(".pwaforwp-screenshots").val(attachment.url);
        })
        .open();
    });
    

    jQuery(".pwaforwp-splash-icon-upload").click(function(e) {   // Splash Screen Icon upload
        e.preventDefault();
        var pwaforwpMediaUploader = wp.media({
            title: pwaforwp_obj.splash_uploader_title,
            button: {
                text: pwaforwp_obj.uploader_button
            },
            multiple: false,  // Set this to true to allow multiple files to be selected
                        library:{type : 'image'}
        })
        .on("select", function() {
            var attachment = pwaforwpMediaUploader.state().get("selection").first().toJSON();
            jQuery(".pwaforwp-splash-icon").val(attachment.url);
        })
        .open();
    });

    jQuery(".pwaforwp-tabs a").click(function(e){
        e.preventDefault();
        var href = jQuery(this).attr("href");
        var currentTab = pwaforwpGetParamByName("tab",href);
        if(!currentTab){
            currentTab = "dashboard";
        }
        jQuery(this).siblings().removeClass("nav-tab-active");
        jQuery(this).addClass("nav-tab-active");
            jQuery(".form-wrap").find(".pwaforwp-"+currentTab).siblings().hide();
            jQuery(".form-wrap .pwaforwp-"+currentTab).show();       
            window.history.pushState("", "", href);
            if(currentTab=='features'){
                jQuery('.pwaforwp-settings-form').find('p.submit').hide();
            }else{
                 jQuery('.pwaforwp-settings-form').find('p.submit').show();
            }
            return false;
        }
    });
    var url      = window.location.href;     // Returns full URL
    var currentTab = pwaforwpGetParamByName("tab",url);
    if(currentTab=='features'){
        jQuery('.pwaforwp-settings-form').find('p.submit').hide();
    }
        
        jQuery(".pwaforwp-activate-service").on("click", function(e){
            jQuery(".pwaforwp-settings-form #submit").click();
            jQuery(this).hide();
        });
        jQuery(".pwaforwp-service-activate").on("click", function(){  
            
        var filetype = jQuery(this).attr("data-id");                
        
        if(filetype){
            
            jQuery.ajax({
                    url:ajaxurl,
                    dataType: "json",
                    data:{filetype:filetype, action:"pwaforwp_download_setup_files", pwaforwp_security_nonce:pwaforwp_obj.pwaforwp_security_nonce},
                    success:function(response){
                        if(response["status"]=="t"){
                            jQuery(".pwaforwp-service-activate[data-id="+filetype+"]").hide();
                            jQuery(".pwaforwp-service-activate[data-id="+filetype+"]").siblings(".dashicons").removeClass("dashicons-no-alt");
                            jQuery(".pwaforwp-service-activate[data-id="+filetype+"]").siblings(".dashicons").addClass("dashicons-yes");
                            jQuery(".pwaforwp-service-activate[data-id="+filetype+"]").siblings(".dashicons").css("color", "#46b450");
                        }else{
                            jQuery(".pwaforwp-service-activate[data-id="+filetype+"]").parent().next().removeClass("pwaforwp-hide");
                        }  
                    }                
                });
            
        }
                
        return false;
    });
        
            }
        }                   
        
    });
        
        jQuery(document).on("click",".pwaforwp-reset-settings", function(e){
                e.preventDefault();
             
                var reset_confirm = confirm("Are you sure?");
             
                if(reset_confirm == true){
                    
                jQuery.ajax({
                            type: "POST",    
                            url:ajaxurl,                    
                            dataType: "json",
                            data:{action:"pwaforwp_reset_all_settings", pwaforwp_security_nonce:pwaforwp_obj.pwaforwp_security_nonce},
                            success:function(response){                               
                                setTimeout(function(){ location.reload(); }, 1000);
                            },
                            error: function(response){                    
                            console.log(response);
                            }
                            }); 
                
                }
                                                                 

        });
        jQuery(".pwaforwp-manual-notification").on("click", function(e){
        e.preventDefault();   
        var message = jQuery("#pwaforwp_notification_message").val(); 
        var pn_title   = jQuery("#pwaforwp_notification_message_title").val(); 
        var pn_url   = jQuery("#pwaforwp_notification_message_url").val(); 
        var pn_image_url   = jQuery("#pwaforwp_notification_message_image_url").val(); 
            
            if(jQuery.trim(message) !=''){
                jQuery(".pwaforwp-manual-notification").prop('disabled', true);
                jQuery(".pwaforwp-manual-notification").text('Sending...');
                jQuery.ajax({
                        type: "POST",    
                        url: ajaxurl,                    
                        dataType: "json",
                        data:{action:"pwaforwp_send_notification_manually", message:message, title:pn_title, pwaforwp_security_nonce:pwaforwp_obj.pwaforwp_security_nonce,url:pn_url, image_url: pn_image_url},
                        success:function(response){                                 
                          if(response['status'] =='t'){
                                var html = '<span style="color:green">Success: '+response['success']+'</span><br>';
                                    //html +='<span style="color:red;">Failure: '+response['failure']+'</span>';
                            jQuery(".pwaforwp-notification-success").show();
                                jQuery(".pwaforwp-notification-success").html(html);
                            jQuery(".pwaforwp-notification-error").hide();
                            jQuery(".pwaforwp-manual-notification").prop('disabled', false);
                            jQuery(".pwaforwp-manual-notification").text('Send');
                          }else{
                            var html = '<span style="color:red">Failed: '+response['mesg']+'</span><br>';
                            jQuery(".pwaforwp-notification-error").html(html);
                            jQuery(".pwaforwp-notification-success").hide();  
                            jQuery(".pwaforwp-notification-error").show();
                            jQuery(".pwaforwp-manual-notification").prop('disabled', false);
                            jQuery(".pwaforwp-manual-notification").text('Send');
                          }
                        },
                        error: function(response){                    
                        console.log(response);
                        jQuery(".pwaforwp-manual-notification").prop('disabled', false);
                        jQuery(".pwaforwp-manual-notification").text('Send');
                        }
                        });

                       
                
            }else{
                alert('Please enter the message');
            }
            
                    
        
    });
        
    jQuery("#pwaforwp_settings_utm_setting").click(function(){
        
        if(jQuery(this).prop("checked")){
            jQuery('.pwawp_utm_values_class').fadeIn();
        }else{
            jQuery('.pwawp_utm_values_class').fadeOut(200);
        }
    });
        
        
        jQuery("#pwaforwp_settings_precaching_automatic").change(function(){ 
        if(jQuery(this).prop("checked")){
            jQuery("#pwaforwp_settings_precaching_post_count").parent().parent().fadeIn(); 
                        jQuery(".pwaforwp-pre-cache-table").parent().parent().fadeIn(); 
        }else{
            jQuery("#pwaforwp_settings_precaching_post_count").parent().parent().fadeOut(200);
                        jQuery(".pwaforwp-pre-cache-table").parent().parent().fadeOut(200);
        }
    }).change();
        
        jQuery("#pwaforwp_settings_precaching_manual").change(function(){    
        if(jQuery(this).prop("checked")){
            jQuery("#pwaforwp_settings_precaching_urls").parent().parent().parent().fadeIn();                                                
        }else{
            jQuery("#pwaforwp_settings_precaching_urls").parent().parent().parent().fadeOut(200);;
        }
    }).change();
        
        jQuery(document).on("click", ".pwaforwp-update-pre-caching-urls", function(e){
            e.preventDefault();
            var current = jQuery(this);
             jQuery.ajax({
                    url:ajaxurl,
                    dataType: "json",
                    data:{action:"pwaforwp_update_pre_caching_urls", pwaforwp_security_nonce:pwaforwp_obj.pwaforwp_security_nonce},
                    success:function(response){
                        if(response["status"]=="t"){
                                current.parent().parent().hide();
                        }else{
                                alert('Something went wrong');
                        }   
                    }                
                });
            
        })
        
        jQuery("#pwaforwp_precaching_method_selector").change(function(){
    
        if(jQuery(this).val() === 'automatic'){
            jQuery('.pwaforwp_precaching_table tr').eq(1).fadeIn();
                        jQuery('.pwaforwp_precaching_table tr').eq(2).fadeOut(200);
        }else{
                        jQuery('.pwaforwp_precaching_table tr').eq(1).fadeOut(200);
                        jQuery('.pwaforwp_precaching_table tr').eq(2).fadeIn();
            
        }
    });
        
        jQuery(".pwaforwp-add-to-home-banner-settings").click(function(){
        
        if(jQuery(this).prop("checked")){
            jQuery('.pwaforwp-enable-on-desktop').removeClass('afw_hide');
        }else{
            jQuery('.pwaforwp-enable-on-desktop').addClass('afw_hide');
                        jQuery('#enable_add_to_home_desktop_setting').prop('checked', false); // Checks it

        }
    });
        jQuery(".pwaforwp-onesignal-support").click(function(){      
        if(jQuery(this).prop("checked")){
            jQuery('.pwaforwp-onesignal-instruction').fadeIn();
        }else{
            jQuery('.pwaforwp-onesignal-instruction').fadeOut(200);
        }
    });
    jQuery('.pwawp_utm_values_class').find('input').focusout(function(){
        if(jQuery(this).attr('data-val')!==jQuery(this).val()){
            jQuery("#pwa-utm_change_track").val('1');
        }
    });
        
        jQuery(".pwaforwp-fcm-checkbox").click(function(){
            
                if(jQuery(this).prop("checked")){
                    jQuery(this).parent().find('p').removeClass('pwaforwp-hide');
        }else{
                    jQuery(this).parent().find('p').addClass('pwaforwp-hide');
        }
            
        });
    jQuery('.pwaforwp-checkbox-tracker').change(function(e){
        var respectiveId = jQuery(this).attr('data-id');
        var chval = 0;
        if(jQuery(this).is(":checked")){
            chval = jQuery(this).val();
        }
        console.log(jQuery(this).parent('label').find('#'+respectiveId), chval);
        jQuery(this).parent('label').find('input[name="'+respectiveId+'"]').val(chval);
    })
        
        //Licensing jquery starts here
    jQuery(document).on("click",".pwaforwp_license_activation", function(e){
                e.preventDefault();
                var current = jQuery(this);
                current.addClass('updating-message');
                var license_status = jQuery(this).attr('license-status');
                var add_on         = jQuery(this).attr('add-on');
                var license_key    = jQuery("#"+add_on+"_addon_license_key").val();
               
            if(license_status && add_on && license_key){
                
                jQuery.ajax({
                            type: "POST",    
                            url:ajaxurl,                    
                            dataType: "json",
                            data:{action:"pwaforwp_license_status_check",license_key:license_key,license_status:license_status, add_on:add_on, pwaforwp_security_nonce:pwaforwp_obj.pwaforwp_security_nonce},
                            success:function(response){
                               jQuery("#"+add_on+"_addon_license_key_status").val(response['status']);
                                                                
                              if(response['status'] =='active'){ 
                               jQuery(".saswp-"+add_on+"-dashicons").addClass('dashicons-yes');
                               jQuery(".saswp-"+add_on+"-dashicons").removeClass('dashicons-no-alt');
                               jQuery(".saswp-"+add_on+"-dashicons").css("color", "green");
                               
                               jQuery(".pwaforwp_license_activation[add-on='" + add_on + "']").attr("license-status", "inactive");
                               jQuery("span.addon-inactive_" + add_on + "").text('Active');
                               jQuery("span.addon-inactive_" + add_on + "").css("color", "green");
                               jQuery("span.addon-inactive_" + add_on + "").removeClass("addon-inactive_" + add_on + "").addClass("addon-activated_" + add_on + "");
                               jQuery(".pwaforwp_license_activation[add-on='" + add_on + "']").text("Deactivate");
                               jQuery(".pwaforwp_license_status_msg[add-on='" + add_on + "']").text('Activated');
                               jQuery(".pwaforwp_license_status_msg[add-on='" + add_on + "']").css("color", "green");
                               jQuery(".pwaforwp_license_status_msg[add-on='" + add_on + "']").text(response['message']);
                           }
                              else if(response['status'] =='expired'){
                               jQuery(".addon-inactive_" + add_on + "").text('Expired');
                                    jQuery(".addon-inactive_" + add_on + "").css("color","red");
                              }else{
                                var invalid_lic = response.message;
                                if ( invalid_lic == 'Invalid license.') {
                                    jQuery(".addon-inactive_" + add_on + "").text('Invalid');
                                    jQuery(".addon-inactive_" + add_on + "").css("color","red");
                                }
                               jQuery(".saswp-"+add_on+"-dashicons").addClass('dashicons-no-alt');
                               jQuery(".saswp-"+add_on+"-dashicons").removeClass('dashicons-yes');
                               jQuery(".saswp-"+add_on+"-dashicons").css("color", "red");
                               
                               jQuery(".pwaforwp_license_activation[add-on='" + add_on + "']").attr("license-status", "active");
                               jQuery(".pwaforwp_license_activation[add-on='" + add_on + "']").text("Activate");
                               
                               jQuery(".pwaforwp_license_status_msg[add-on='" + add_on + "']").css("color", "red"); 
                               jQuery(".pwaforwp_license_status_msg[add-on='" + add_on + "']").text(response['message']);
                               jQuery("span.addon-activated_" + add_on + "").text('Inactive');
                               jQuery("span.addon-activated_" + add_on + "").css("color", "#bebfc0");
                               jQuery("span.addon-activated_" + add_on + "").removeClass("addon-activated_" + add_on + "").addClass("addon-inactive_" + add_on + "");
                              }
                                jQuery(".message_addon-inactive_" + add_on + "").text(response.message);
                                jQuery(".message_addon-inactive_" + add_on + "").css("color","red");
                                current.removeClass ('updating-message');                                                           
                            },
                            error: function(response){                    
                                console.log(response);
                            }
                            });
                        }
                            else{
                current.removeClass('updating-message'); 
            }

        });

        // Start Usermanual Check

        jQuery(document).on("click","#user_refresh-", function(e){

                e.preventDefault();
                var current = jQuery(this);
                document.getElementById("user_refresh").classList.add("spin")
                var license_status = 'active';
                var add_on         = jQuery(this).attr('add-on');
                var license_key    = jQuery("#"+add_on+"_addon_license_key").val();
               
            if(license_status && add_on && license_key){
                
                jQuery.ajax({
                            type: "POST",    
                            url:ajaxurl,                    
                            dataType: "json",
                            data:{action:"pwaforwp_license_status_check",license_key:license_key,license_status:license_status, add_on:add_on, pwaforwp_security_nonce:pwaforwp_obj.pwaforwp_security_nonce},
                            success:function(response){                               
                               
                               jQuery("#"+add_on+"_addon_license_key_status").val(response['status']);
                                                                
                              if(response['status'] =='active'){ 
                              var days_remaining = response['days_remaining'];
                              if (days_remaining>7) {

                                jQuery("span.addon-activated_" + add_on + "").text('Active');
                                jQuery("span.addon-activated_" + add_on + "").css("color", "green");
                                
                                jQuery("span.lessthan_0").text('License is');
                                jQuery("span.lessthan_0").css("color", "black");

                                jQuery("span.expiredinner_span").text('Active');
                                jQuery("span.expiredinner_span").css("color", "green");
                                jQuery("span.before_msg_active").text('Your license is');

                                jQuery("span.pwaforwp_addon_icon").css("display", "none");

                                jQuery(".renewal-license").css("display", "none");

                              }
                              
                              document.getElementById("user_refresh").classList.remove("spin")
                          }else{
                                  console.log('Failed');
                                  document.getElementById("user_refresh").classList.remove("spin")
                               
                              }
                               current.removeClass('updating-message');                                                           
                            },
                            error: function(response){                    
                                console.log(response);
                            }
                            })
                            
            }else{
                current.removeClass('updating-message'); 
            }

        });

        // End Usermanual Check

        // Start Single Addon check
function PWAforwpreadCookie(name) {
    var nameEQ = name + "=";
    var ca = document.cookie.split(";");
    for(var i=0;i < ca.length;i++) {
        var c = ca[i];
        while (c.charAt(0)==" ") c = c.substring(1,c.length);
        if (c.indexOf(nameEQ) == 0) return c.substring(nameEQ.length,c.length);
    }
    return null;
}
        jQuery(document).on("click",".pwaforwp_user_refresh_single_addon", function(e){

                var currentThis = jQuery(this);
                e.preventDefault();
                var license_status = 'active';
                var add_on         = currentThis.attr('add-on');
                var remaining_days_org         = currentThis.attr('remaining_days_org');
                var license_key    = jQuery("#"+add_on+"_addon_license_key").val();
                document.getElementById("user_refresh_" + add_on + "").classList.add("spin")
                    
                    var today = new Date();

                    var previous_check = PWAforwpreadCookie('pwaforwp_addon_refresh_check');

                    previous_check = new Date(previous_check);
                    console.log('previous_check '+previous_check+ " true");
                    var diffDays = -1;
                    if( typeof previous_check != undefined){
                        var diffTime = Math.abs(today.getTime() - previous_check.getTime());
                        var diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24)); 
                    }
                    var expireDate = new Date(remaining_days_org);
                    var diffTime = Math.abs( expireDate.getTime()-today.getTime() );
                    var expireDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24));
                    if( diffDays==-1 || diffDays>1 || expireDays<1 ){
                    document.cookie = "pwaforwp_addon_refresh_check="+today;jQuery.ajax({
                            type: "POST",    
                            url:ajaxurl,                    
                            dataType: "json",
                            data:{action:"pwaforwp_license_status_check",license_key:license_key,license_status:license_status, add_on:add_on, pwaforwp_security_nonce:pwaforwp_obj.pwaforwp_security_nonce},
                            success:function(response){
                                jQuery("#"+add_on+"_addon_license_key_status").val(response['status']);
                                document.getElementById("user_refresh_"+add_on+"").classList.remove("spin")
                                currentThis.removeClass('updating-message');
                            },
                            error: function(response){                    
                                console.log(response);
                            }
                            })
                }
                else{  
                setTimeout( function() {
                    jQuery(".dashicons").removeClass( 'spin' );}, 0 );   
                previous_check = Math.abs(previous_check.getDate()+1)+'/'+Math.abs(previous_check.getMonth()+1) +'/'+previous_check.getFullYear()+' '+previous_check.getHours()+':'+previous_check.getMinutes()+':'+previous_check.getSeconds();
                alert('Please try after '+ previous_check);
    }

        });

        // End Single Addon check

        // Start Auto Check when expired 
        setTimeout(function() {
            jQuery("#refresh_expired_addon-").trigger('click'); 
        }, 1000)


        jQuery(document).on("click","#refresh_expired_addon-", function(e){

                e.preventDefault();
                var current = jQuery(this);
                document.getElementById("refresh_expired_addon").classList.add("spin")
                var license_status = 'active';
                var add_on         = jQuery(this).attr('add-on');
                var license_key    = jQuery("#"+add_on+"_addon_license_key").val();
               
            if(license_status && add_on && license_key){
                
                jQuery.ajax({
                            type: "POST",    
                            url:ajaxurl,                    
                            dataType: "json",
                            data:{action:"pwaforwp_license_status_check",license_key:license_key,license_status:license_status, add_on:add_on, pwaforwp_security_nonce:pwaforwp_obj.pwaforwp_security_nonce},
                            success:function(response){                               
                               
                               jQuery("#"+add_on+"_addon_license_key_status").val(response['status']);
                                                                
                              if(response['status'] =='active'){ 
                              var days_remaining = response['days_remaining'];
                              if (days_remaining>0) {

                                jQuery("span.addon-activated_" + add_on + "").text('Active');
                                jQuery("span.addon-activated_" + add_on + "").css("color", "green");
                                
                                jQuery("span.lessthan_0").text('License is');
                                jQuery("span.lessthan_0").css("color", "black");

                                jQuery("span.expiredinner_span").text('Active');
                                jQuery("span.expiredinner_span").css("color", "green");

                                jQuery("span.pwaforwp_addon_icon").css("display", "none");
                                jQuery("span.dashicons-warning").css("display", "none");

                                jQuery(".renewal-license").css("display", "none");

                              }
                              document.getElementById("refresh_expired_addon").classList.remove("spin")
                          }else{
                                  console.log('Failedd');
                               
                              }
                               current.removeClass('updating-message');                                                           
                            },
                            error: function(response){                    
                                console.log(response);
                            }
                            }),
                 jQuery.ajax({
                type: "POST",    
                            url:ajaxurl,                    
                            dataType: "json",
                            data:{action:"pwaforwp_license_transient",license_key:license_key,license_status:license_status, add_on:add_on, pwaforwp_security_nonce:pwaforwp_obj.pwaforwp_security_nonce},
                success: function(s) {
                    JSON.parse(s)
                }
            })
                            
            }else{
                current.removeClass('updating-message'); 
            }

        });// End Auto Check when expired 

        // Start Auto-check if user had done renewal between 0-7 days
        setTimeout(function() {
            jQuery("#auto_refresh-").trigger('click'); 
        }, 1000)

        jQuery(document).on("click","#auto_refresh-", function(e){
            var days_remaining = document.getElementById('activated-plugins-days_remaining');
                var days_remaining_value = days_remaining.getAttribute('days_remaining');
                if ( days_remaining_value >= 0 &&  days_remaining_value <= 7 ) {
                e.preventDefault();
                var current = jQuery(this);
                document.getElementById("auto_refresh").classList.add("spin")
                var license_status = 'active';
                var add_on         = jQuery(this).attr('add-on');
                var license_key    = jQuery("#"+add_on+"_addon_license_key").val();

               
            if(license_status && add_on && license_key){
                
                jQuery.ajax({
                            type: "POST",    
                            url:ajaxurl,                    
                            dataType: "json",
                            data:{action:"pwaforwp_license_status_check",license_key:license_key,license_status:license_status, add_on:add_on, pwaforwp_security_nonce:pwaforwp_obj.pwaforwp_security_nonce},
                            success:function(response){                               
                               
                               jQuery("#"+add_on+"_addon_license_key_status").val(response['status']);
                                                                
                              if(response['status'] =='active'){
                              var days_remaining = response['days_remaining'];
                              if (days_remaining>7) {

                                jQuery("span.zero_to_seven").text('License is');
                                jQuery("span.zero_to_seven").css("color", "black");

                                jQuery("span.pwaforwp-addon-alert").css("color", "green");

                                jQuery("span.pwaforwp-addon-alert").text('Active');

                                jQuery(".renewal-license").css("display", "none");

                              }
                              document.getElementById("auto_refresh").classList.remove("spin")
                          }else{
                                  console.log('Failedd');
                               
                              }
                               current.removeClass('updating-message');                                                           
                            },
                            error: function(response){                    
                                console.log(response);
                            }
                            }),
                 jQuery.ajax({
                type: "POST",    
                            url:ajaxurl,                    
                            dataType: "json",
                            data:{action:"pwaforwp_license_transient_zto7",license_key:license_key,license_status:license_status, add_on:add_on, pwaforwp_security_nonce:pwaforwp_obj.pwaforwp_security_nonce},
                success: function(s) {
                    JSON.parse(s)
                }
            })
                            
            }else{
                current.removeClass('updating-message'); 
            }
        }

        });
        // Auto-check End

        //Licensing jquery ends here
        
        jQuery('.pwaforwp-sub-tab-headings span').click(function(){
            var tabId = jQuery(this).attr('data-tab-id');
            jQuery(this).parents('.pwaforwp-subheading-wrap').find('.pwaforwp-subheading').find('div.selected').removeClass('selected').addClass('pwaforwp-hide');
            jQuery(this).parents('.pwaforwp-subheading-wrap').find('.pwaforwp-subheading').find('#'+tabId).removeClass('pwaforwp-hide').addClass('selected');
            //tab head
            jQuery(this).parent('.pwaforwp-sub-tab-headings').find('span.selected').removeClass('selected');
            jQuery(this).addClass('selected');
        });

        jQuery(".pwaforwp-checkbox").click(function(){
            
                    var data_id = jQuery(this).attr('data-id');
                    console.log(data_id);
            if(jQuery(this).prop("checked")){
                jQuery('.pwaforwp_'+data_id).removeClass('pwaforwp-hide');
            }else{
                jQuery('.pwaforwp_'+data_id).addClass('pwaforwp-hide');
            }
        });

    //ios splash screen start
    jQuery(".switch_apple_splash_screen").click(function(){
        if(jQuery(this).is(':checked')){
            jQuery('.pwaforwp-ios-splash-images').show();
        }else{
            jQuery('.pwaforwp-ios-splash-images').hide();
        }
    });
    jQuery(".pwaforwp-ios-splash-icon-upload").click(function(e) {   // Splash Screen Icon upload
        e.preventDefault();
        var self = jQuery(this);
        var splash_uploader_title = self.parent('.ios-splash-images-field').find('label').text();
        var pwaforwpMediaUploader = wp.media({
            title: splash_uploader_title,
            button: {
                text: 'Select image'
            },
            multiple: false,  // Set this to true to allow multiple files to be selected
                        library:{type : 'image'}
        })
        .on("select", function() {
            var attachment = pwaforwpMediaUploader.state().get("selection").first().toJSON();
            self.parent('.ios-splash-images-field').find(".pwaforwp-splash-icon").val(attachment.url);
        })
        .open();
    });
    //ios splash screen End



    jQuery('.pwaforwp-change-data').click(function(e){
        e.preventDefault();
        if(!jQuery(this).parents('.card-action').find('label').find('input[type="checkbox"]').prop('checked')){
            return false;
        }
        var opt = jQuery(this).attr('data-option');
        var optTitle = jQuery(this).attr('title');
        pwaforwp_showpopup(optTitle, opt, '.pwaforwp-submit-feature-opt');
        //tb_show(optTitle, "#TB_inline?width=740&height=450&inlineId="+opt);
        datafeatureSubmit(opt);
    });

    jQuery('.card-action input[type="checkbox"]').change(function(){
        var value = 0;
        if(jQuery(this).is(':checked')){
            jQuery(this).parents('.card-action').find('.card-action-settings').css({opacity: 1})
            var value = 1;
            //jQuery(this).parents('.card-action').find('.pwaforwp-change-data').click();
        }else{
            jQuery(this).parents('.card-action').find('.card-action-settings').css({opacity: 0});
        }
        let fields = [];
        var name = jQuery(this).attr('name');
        pwaforwp_dependent_features_section(name, value);
        fields.push({var_name: name, var_value: value});
        pwaforwp_ajaxcall_submitdata(pwaforwp_obj, fields);
    })

    /**
    * Push notification options selection
    */
    jQuery(document).on("change", ".pwaforwp-pn-service", function(){
        var self = jQuery(this);
        var currentSelected = self.val();
        pushnotificationIntegrationLogic('notification-contents');
        switch(currentSelected){
            case 'pushnotifications_io':
                jQuery('.pwaforwp-push-notificatoin-table').hide();
                jQuery('.pwaforwp-notification-condition-section').hide();
                jQuery('.pwaforwp-pn-recommended-options').show();
                //self.parents('.pwaforwp-wrap').find('.footer').hide();
            break;
            case 'fcm_push':
                jQuery('.pwaforwp-push-notificatoin-table').show();
                jQuery('.pwaforwp-notification-condition-section').show();
                jQuery('.pwaforwp-pn-recommended-options').hide();
                //self.parents('.pwaforwp-wrap').find('.footer').show();
            break;
            default:
                jQuery('.pwaforwp-push-notificatoin-table').hide();
                jQuery('.pwaforwp-notification-condition-section').hide();
                jQuery('.pwaforwp-pn-recommended-options').hide();
            break
        }
        jQuery('.notification-wrap-tb').find('.footer button').trigger('click')
    });

    jQuery("#ios-splash-color").wpColorPicker();




    
});
var pushnotificationIntegrationLogic = function(opt){
    if(opt==='notification-contents'){
            var optNotif = jQuery('.pwaforwp-pn-service').val()
            if(optNotif==='' || optNotif==='pushnotifications_io'){
                jQuery('.notification-wrap-tb').find('.footer').hide()
            }else{
                jQuery('.notification-wrap-tb').find('.footer').show()
            }
        }
}
var datafeatureSubmit = function(opt){
        pushnotificationIntegrationLogic(opt)
        jQuery('.pwaforwp-submit-feature-opt').click(function(e){
            e.preventDefault();
            var self = jQuery(this);
            var fields = [];
            self.parents('.thickbox-fetures-wrap')
                .find('input').each( function(k,v){
                    var type = jQuery(this).attr('type').toLowerCase();
                    var name = jQuery(this).attr('name');//.replace(/pwaforwp_settings\[/,'').replace(/\]/, '');

                    if(type=='checkbox'){
                        if(jQuery(this).is(':checked')){
                            var value = jQuery(this).val();
                        }else{
                            var value = (jQuery(this).attr('data-uncheck-val')) ? jQuery(this).attr('data-uncheck-val') : 0;
                        }
                        if(name){
                            pwaforwp_dependent_features_section(name, value)
                            fields.push({var_name: name, var_value: value});
                        }
                    }
                    if(type=='radio'){
                        if(jQuery(this).is(':checked')){
                            var value = jQuery(this).val();
                        }else{
                            var value = (jQuery(this).attr('data-uncheck-val')) ? jQuery(this).attr('data-uncheck-val') : 0;
                        }
                        if(name){
                            fields.push({var_name: name, var_value: value});
                        }
                    }
                    if(type!='checkbox' && type!='radio' ){
                       var value = jQuery(this).val();
                        if(name){
                            fields.push({var_name: name, var_value: value});
                        }
                    }

                });
            self.parents('.thickbox-fetures-wrap')
                .find('textarea').each( function(k,v){
                    var name = jQuery(this).attr('name');
                    var value = jQuery(this).val();
                    if(name){
                        fields.push({var_name: name, var_value: value});
                    }
                })
            self.parents('.thickbox-fetures-wrap')
                .find('select').each( function(k,v){
                    var name = jQuery(this).attr('name');
                    var value = jQuery(this).val();
                    if(name){
                        fields.push({var_name: name, var_value: value});
                        /*only for push notificatio opt*/
                        if( name==='pwaforwp_settings[notification_options]' ){
                            if(value!==''){
                                jQuery('#notification-opt-stat').hide();
                            }else{
                                jQuery('#notification-opt-stat').show();
                            }
                        }
                    }
                })


            pwaforwp_ajaxcall_submitdata(pwaforwp_obj, fields)
                
        });
    }

    function pwaforwp_ajaxcall_submitdata(pwaforwp_security_nonce, fields){
        if(!staticAjaxCalled){
            var staticAjaxCalled = true;
        }
        if(staticAjaxCalled){
            var data = {action:"pwaforwp_update_features_options", pwaforwp_security_nonce:pwaforwp_obj.pwaforwp_security_nonce,fields_data: fields};
                jQuery.ajax({
                    url:ajaxurl,
                    method:'post',
                    dataType: "json",
                    data:data,
                    success:function(response){
                        staticAjaxCalled = false;
                        if(response["status"]==200){
                            if (fields[0].var_name == "pwaforwp_settings[data_analytics]") {
                                location.reload();
                            }
                            pwaforwp_show_message_toast('success', response.message);
                        }else{
                            pwaforwp_show_message_toast('error', response.message);
                        }
                    }                
                });
        }
    }

    function pwaforwp_show_message_toast(type, message){
        var classes = "pwaforwp-toast-error"
        if(type=='success'){
            classes="pwaforwp-toast-success"
        }
        if(jQuery('.pwaforwp-toast-wrap').length){
            jQuery('.pwaforwp-toast-wrap').remove();
        }

        var messageDiv = '<div class="pwaforwp-toast-wrap bottom-left"><div class="pwaforwp-toast-single '+classes+'" style="text-align: left;"><span class="pwaforwp-toast-loader pwaforwp-toast-loaded" style="-webkit-transition: width 2.6s ease-in;                       -o-transition: width 2.6s ease-in;                       transition: width 2.6s ease-in;                       background-color: #9EC600;"></span>'+message+'<span class="close-pwaforwp-toast-single">×</span></div></div>';
        jQuery('body').append(messageDiv);

        setTimeout(function(){
            jQuery('.pwaforwp-toast-wrap').remove();
        }, 3000);
        jQuery('.close-pwaforwp-toast-single').click(function(){
            jQuery(this).parents('.pwaforwp-toast-wrap').remove();
        })
    }


var pwaforwp_dependent_features_section = function(fieldname, fieldValue){
    switch(fieldname){
        case 'pwaforwp_settings[precaching_feature]': 
            if(fieldValue==1){
                jQuery('input[name="pwaforwp_settings[precaching_automatic]"]').trigger('click');
                jQuery('input[name="pwaforwp_settings[precaching_automatic_post]"]').trigger('click');
            }else{
                jQuery('input[name="pwaforwp_settings[precaching_automatic]"]').trigger('click');
                jQuery('input[name="pwaforwp_settings[precaching_automatic_post]"]').trigger('click');
            }

        break;

        case 'pwaforwp_settings[precaching_automatic]': 
            if(fieldValue==1){
                jQuery('input[name="pwaforwp_settings[precaching_feature]"]').prop('checked', true);
            }else{
                jQuery('input[name="pwaforwp_settings[precaching_feature]"]').prop('checked', false);
                jQuery('input[name="pwaforwp_settings[precaching_feature]"]').parents('.card-action').find('.card-action-settings').css({opacity: 0});
            }

        break;

        case 'pwaforwp_settings[addtohomebanner_feature]': 
            if(fieldValue==1){
                jQuery('input[name="pwaforwp_settings[custom_add_to_home_setting]"]').trigger('click');
            }else{
                jQuery('input[name="pwaforwp_settings[custom_add_to_home_setting]"]').trigger('click');
            }

        break;

        case 'pwaforwp_settings[custom_add_to_home_setting]': 
            if(fieldValue==1){
                jQuery('input[name="pwaforwp_settings[addtohomebanner_feature]"]').prop('checked', true);
            }else{
                jQuery('input[name="pwaforwp_settings[addtohomebanner_feature]"]').prop('checked', false);
                jQuery('input[name="pwaforwp_settings[addtohomebanner_feature]"]').parents('.card-action').find('.card-action-settings').css({opacity: 0});
            }

        break;

        case 'pwaforwp_settings[loader_feature]': 
            if(fieldValue==1){
                jQuery('input[name="pwaforwp_settings[loading_icon]"]').trigger('click');
            }else{
                jQuery('input[name="pwaforwp_settings[loading_icon]"]').trigger('click');
            }

        break;

        case 'pwaforwp_settings[loading_icon]': 
            if(fieldValue==1){
                jQuery('input[name="pwaforwp_settings[loader_feature]"]').prop('checked', true);
            }else{
                jQuery('input[name="pwaforwp_settings[loader_feature]"]').prop('checked', false);
                jQuery('input[name="pwaforwp_settings[loader_feature]"]').parents('.card-action').find('.card-action-settings').css({opacity: 0});
            }

        break;

        case 'pwaforwp_settings[utmtracking_feature]': 
            if(fieldValue==1){
                jQuery('input[name="pwaforwp_settings[utm_setting]"]').trigger('click');
            }else{
                jQuery('input[name="pwaforwp_settings[utm_setting]"]').trigger('click');
            }

        break;
        case 'pwaforwp_settings[utm_setting]': 
            if(fieldValue==1){
                jQuery('input[name="pwaforwp_settings[utmtracking_feature]"]').prop('checked', true);
            }else{
                jQuery('input[name="pwaforwp_settings[utmtracking_feature]"]').prop('checked', false);
                jQuery('input[name="pwaforwp_settings[utmtracking_feature]"]').parents('.card-action').find('.card-action-settings').css({opacity: 0});
            }

        break;
    }
}




var pwaforwp_showpopup = function(caption, inlineId, submitClass){
    if(caption===null){caption="";}
    jQuery(".pwawp-modal-mask").find(".pwawp-popup-title").html(caption);
    jQuery(".pwawp-modal-mask").find(".pwawp-modal-settings").append(jQuery('#' + inlineId).children());
    //to show poup
    jQuery(".pwawp-modal-mask").attr("data-parent", inlineId);
    jQuery(".pwawp-modal-mask").attr("data-parent-submit", submitClass);
    jQuery(".pwawp-modal-mask").find(submitClass).addClass("pwaforwp-hide");
    jQuery(".pwawp-modal-mask").removeClass("pwaforwp-hide");
    
    //Click on cross button
    jQuery(".pwawp-modal-mask").find(".pwawp-media-modal-close, .pwawp-close-btn-modal").click(function(){
        var inlineIdData = jQuery(this).parents(".pwawp-modal-mask").attr("data-parent");
        var submitClassData = jQuery(this).parents(".pwawp-modal-mask").attr("data-parent-submit");
        jQuery('#' + inlineIdData).append( jQuery(".pwawp-modal-mask").find(".pwawp-modal-settings").children() ); 
        jQuery(".pwawp-modal-mask").addClass("pwaforwp-hide");
    });
    //click  on save button
    jQuery(".pwawp-modal-mask").find(".pwawp-save-btn-modal").click(function(e){
        e.preventDefault();
        var inlineIdData = jQuery(this).parents(".pwawp-modal-mask").attr("data-parent");
        var submitClassData = jQuery(this).parents(".pwawp-modal-mask").attr("data-parent-submit");
        jQuery(".pwawp-modal-mask").find(submitClassData).click();
    });
}



    //iosSplashIcon
    var optSelection=document.getElementById('ios-splash-gen-opt');
    optSelection.addEventListener('change', onpwaiosOptSelection,false);
    if(typeof Event=='function'){
        var event = new Event('change');
        optSelection.dispatchEvent(event);
    }else{
        onpwaiosOptSelection(optSelection);
    }
    function onpwaiosOptSelection(e){
        if(e.target){
            var selected = e.target.value
        }else{ var selected = e.value; }
        if(selected==""){
            document.getElementById('generate-auto-1').setAttribute("class", "panel pwaforwp-hide");
            document.getElementById('manually-1').setAttribute("class", "panel pwaforwp-hide");
            return;
             }
        if(selected=='generate-auto'){
            document.getElementById(selected+'-1').setAttribute("class", "panel");
            document.getElementById('manually-1').setAttribute("class", "panel pwaforwp-hide");
        }else{
            document.getElementById(selected+'-1').setAttribute("class", "panel");
            document.getElementById('generate-auto-1').setAttribute("class", "panel pwaforwp-hide");
        }
    }
    var image='';
    document.addEventListener('DOMContentLoaded',function(){
        var elmFileUpload=document.getElementById('file-upload-ios');
        if(elmFileUpload){
            elmFileUpload.addEventListener('change',onFileUploadChange,false);
        }
    });
    function onFileUploadChange(e){
        var file=e.target.files[0];
        var fr=new FileReader();
        fr.onload=onFileReaderLoad;
        fr.readAsDataURL(file);
    }
    function onFileReaderLoad(e){
        image=e.target.result;
        document.getElementById('thumbnail').src=e.target.result;
        //console.log(image);
    };
    function pwa_getimageZip(e){
        e.disabled = true;
        if(image==''){alert("Please Select Image");return;}
        var imageMessage = document.getElementById("pwa-ios-splashmessage")
        imageMessage.innerHTML = 'Generating splash screen...';
        imageMessage.setAttribute("class", "updating-message");

        var zip=new JSZip();
        var folder=zip.folder('splashscreens');
        var canvas=document.createElement('canvas'),ctx=canvas.getContext('2d');
        var img=new Image();
        img.src=image;
        Object.keys(pwaforwp_obj.iosSplashIcon).forEach(function(key, index) {
            var phone = pwaforwp_obj.iosSplashIcon[key];
            var ws=key.split("x")[0];
            var hs=key.split("x")[1];
            canvas.width=ws;
            canvas.height=hs;
            var wi=img.width;
            var hi=img.height;
            var wnew=wi;
            var hnew=hi;
            
            ctx.fillStyle = document.getElementById('ios-splash-color').value;
            ctx.fillRect(0,0,canvas.width,canvas.height);
            
            ctx.drawImage(img,(ws-wnew)/2,(hs-hnew)/2,wnew,hnew);
            var img2=canvas.toDataURL();
            folder.file(phone.file,img2.split(';base64,')[1],{base64:true});
        });
        zip.generateAsync({type:'blob'}).then(function(content){
            //saveAs(content,'splashscreens.zip');
            var request = new XMLHttpRequest();
            request.open("POST", ajaxurl+"?action=pwaforwp_splashscreen_uploader&pwaforwp_security_nonce="+pwaforwp_obj.pwaforwp_security_nonce);
            request.send(content);
            request.onreadystatechange = function() {
                if (request.readyState === 4) {
                    var reponse = JSON.parse(request.response);
                  if(reponse.status==200){
                    imageMessage.innerHTML = 'Splash Screen generated';
                    imageMessage.setAttribute("class", "dashicons dashicons-yes");
                    imageMessage.style.color = "#46b450";
                    setTimeout(function(){
                        window.location.reload();
                    }, 1000);
                  }
                }
              }
        });
        e.disabled = false;
    }

var accordion = document.getElementsByClassName("accordion");
var i;

for (i = 0; i < accordion.length; i++) {
  accordion[i].addEventListener("click", function() {
    this.classList.toggle("active");
    var panel = this.nextElementSibling;
    if (panel.style.maxHeight) {
      panel.style.maxHeight = null;
    } else {
      panel.style.maxHeight = panel.scrollHeight + "px";
    }
  });
}

function get_include_pages(){
    var include_type = jQuery(".visibility_options_select_include").val();
    jQuery(".pwaforwp-visibility-loader").css("display","flex");
    var data = {action:"pwaforwp_include_visibility_setting_callback",pwaforwp_security_nonce:pwaforwp_obj.pwaforwp_security_nonce, include_type:include_type};
    jQuery.ajax({
            url: ajaxurl,
            type: 'post',
            data: data,
          
            success: function(response) {
                var jd = jQuery.parseJSON(response);
                if (jd.success == 1) {
                    jQuery(".visibility_include_select_type").html(jd.option);
                    jQuery(".pwaforwp-visibility-loader").css("display","none");
                    pwa_for_wp_select2(include_type);
                }
                
            }
        });
    
}

function get_exclude_pages(){
    var include_type = jQuery(".visibility_options_select_exclude").val();
    jQuery(".pwaforwp-visibility-loader").css("display","flex");
    var data = {action:"pwaforwp_include_visibility_setting_callback",pwaforwp_security_nonce:pwaforwp_obj.pwaforwp_security_nonce, include_type:include_type};
    jQuery.ajax({
            url: ajaxurl,
            type: 'post',
            data: data,
            success: function(response) {
                var jd = jQuery.parseJSON(response);
                if (jd.success == 1) {
                    jQuery(".visibility_exclude_select_type").html(jd.option);
                    jQuery(".pwaforwp-visibility-loader").css("display","none");
                    pwa_for_wp_select2_exclude(include_type)
                }
            }
        });
}

function add_included_condition(){
    var include_targeting_type = jQuery(".visibility_options_select_include").val();
    var include_targeting_data = jQuery(".visibility_include_select_type").val();
    jQuery(".include_error").html('&nbsp;');
    jQuery(".include_type_error").html('&nbsp;');
    var duplicate_error = false;
    jQuery('.visibility-include-target-item-list > span').each(function () {
        var include_targeting_data_value = jQuery(this).find('input[name="include_targeting_data"]').val();
        if (include_targeting_data == include_targeting_data_value) {
            jQuery(".include_type_error").html('Data alredy selected').css('color','red');
            duplicate_error =  true;
        }
    })
    if(include_targeting_type==''){
        jQuery(".include_error").html('Please select visibility type').css('color','red');
        setTimeout(function(){
            jQuery(".include_error").html('&nbsp;');
        },5000);
        return false;
    }
    if(include_targeting_data==''){
        jQuery(".include_type_error").html('Please select type').css('color','red');
        setTimeout(function(){
            jQuery(".include_type_error").html('&nbsp;');
        },5000);
        return false;
    }
    if (duplicate_error == false) {
        var data = {action:"pwaforwp_include_visibility_condition_callback",pwaforwp_security_nonce:pwaforwp_obj.pwaforwp_security_nonce, include_targeting_type:include_targeting_type,include_targeting_data:include_targeting_data};
        jQuery(".pwaforwp-visibility-loader").css("display","flex");
        jQuery.ajax({
                url: ajaxurl,
                type: 'post',
                data: data,
              
                success: function(response) {
                    var jd = jQuery.parseJSON(response);
                    if (jd.success == 1) {
                        jQuery(".visibility-include-target-item-list").append(jd.option);
                        jQuery(".pwaforwp-visibility-loader").css("display","none");
                        apply_or_condition('visibility-include-target-item-list');
                    } 
    
                }
            });
        
    }
}

function add_exclude_condition(){
    var exclude_targeting_type = jQuery(".visibility_options_select_exclude").val();
    var exclude_targeting_data = jQuery(".visibility_exclude_select_type").val();
    jQuery(".exclude_error").html('&nbsp;');
    jQuery(".exclude_type_error").html('&nbsp;');

    var duplicate_error = false;
    jQuery('.visibility-exclude-target-item-list > span').each(function () {
        var exclude_targeting_data_value = jQuery(this).find('input[name="exclude_targeting_data"]').val();
        if (exclude_targeting_data == exclude_targeting_data_value) {
            jQuery(".exclude_type_error").html('Data alredy selected').css('color','red');
            duplicate_error =  true;
        }
    })

    if(exclude_targeting_type==''){
        jQuery(".include_error").html('Please select visibility type').css('color','red');
        setTimeout(function(){
            jQuery(".exclude_error").html('&nbsp;');
        },5000);
        return false;
    }
    if(exclude_targeting_data==''){
        jQuery(".exclude_type_error").html('Please select type').css('color','red');
        setTimeout(function(){
            jQuery(".exclude_type_error").html('&nbsp;');
        },5000);
        return false;
    }

    if (duplicate_error == false) {
        var data = {action:"pwaforwp_exclude_visibility_condition_callback",pwaforwp_security_nonce:pwaforwp_obj.pwaforwp_security_nonce, exclude_targeting_type:exclude_targeting_type,exclude_targeting_data:exclude_targeting_data};
        jQuery(".pwaforwp-visibility-loader").css("display","flex");
        jQuery.ajax({
                url: ajaxurl,
                type: 'post',
                data: data,
            
                success: function(response) {
                    var jd = jQuery.parseJSON(response);
                    if (jd.success == 1) {
                        jQuery(".visibility-exclude-target-item-list").append(jd.option);
                        jQuery(".pwaforwp-visibility-loader").css("display","none");
                        apply_or_condition('visibility-exclude-target-item-list');
                        
                    } 

                }
            });
    }
}

apply_or_condition('visibility-include-target-item-list');
apply_or_condition('visibility-exclude-target-item-list');
function apply_or_condition(class_name){
    jQuery("."+class_name).find('.pwa_visibility_or').remove();
    var span_length = jQuery( "."+class_name ).children('span').length;
    span_length = span_length-1;
    jQuery( "."+class_name ).children('span').each(function(k) {
        if (k < span_length) {
            jQuery(this).append('<b class="pwa_visibility_or">OR</b>');
        }
    })
}

function removeIncluded_visibility(sr){
    jQuery(".pwaforwp-visibility-target-icon-"+sr).empty();
}

// after added add more functionality form multiple screenshots
jQuery("body").on('click','.pwaforwp-screenshots-multiple-upload',function(e) {  // Application screenshots upload
    e.preventDefault();
    this_ = jQuery(this).parents('.js_clone_div');
    var pwaforwpMediaUploader = wp.media({
        title: pwaforwp_obj.uploader_title,
        button: {
            text: pwaforwp_obj.uploader_button
        },
        multiple: false,  // Set this to true to allow multiple files to be selected
                    library:{type : 'image'}
    })
    .on("select", function() {
        var attachment = pwaforwpMediaUploader.state().get("selection").first().toJSON();
        this_.find(".pwaforwp-screenshots").val(attachment.url);
    })
    .open();
});

jQuery("#screenshots_add_more").click(function(e) {  // Add more screenshots
    e.preventDefault();
    clone_tr = jQuery(this).parents('.js_clone_div:first').clone();
    clone_tr.find('input').val("")
    clone_tr.find('input').prop('name','pwaforwp_settings[screenshots_multiple][]')
    clone_tr.find('select').prop('name','pwaforwp_settings[form_factor_multiple][]')
    clone_tr.find('select').prop('selectedIndex', 0)
    clone_tr.find("#screenshots_add_more").remove()
    clone_tr.find(".js_remove_screenshot").show()
    clone_tr.find('.js_choose_button').addClass('pwaforwp-screenshots-multiple-upload')
    clone_tr.find('.js_choose_button').removeClass('pwaforwp-screenshots-upload')
    clone_tr.insertAfter('.js_clone_div:last')
});
jQuery("body").on('click','.js_remove_screenshot',function(e) {  // Add more screenshots
    e.preventDefault();
    jQuery(this).parents('.js_clone_div').remove()
});
if(jQuery('#prefer_related_applications').prop("checked")){
    jQuery('#related_applications_div').parents('tr').find('th').show();
}else{
    jQuery('#related_applications_div').parents('tr').find('th').hide();
}
jQuery("body").on('click','#prefer_related_applications',function(e) { //Prefer Related Application
    if(jQuery(this).prop("checked")){
        jQuery('#related_applications_div').parents('tr').find('th').show();
        jQuery('#related_applications_div').show();
    }else{
        jQuery('#related_applications_div').hide();
        jQuery('#related_applications_div').parents('tr').find('th').hide();
    }
});

function pwa_for_wp_select2(type){
    var $select2 = jQuery('.pwa_for_wp-select2');
    
    if($select2.length > 0){
        jQuery($select2).each(function(i, obj) {
            var currentP = jQuery(this);  
            var $defaultResults = jQuery('option[value]:not([selected])', currentP);  
            
            var defaultResults = [];
            $defaultResults.each(function () {
                var $option = jQuery(this);
                defaultResults.push({
                    id: $option.attr('value'),
                    text: $option.text()
                });
            });
            var ajaxnewurl = ajaxurl + '?action=pwaforwp_get_select2_data&pwaforwp_security_nonce='+pwaforwp_obj.pwaforwp_security_nonce+'&type='+type;

            currentP.select2({           
                ajax: {             
                    url: ajaxnewurl,
                    delay: 250, 
                    cache: false,
                },            
                minimumInputLength: 2, 
                minimumResultsForSearch : 50,
                dataAdapter: jQuery.fn.select2.amd.require('select2/data/extended-ajax'),
                defaultResults: defaultResults
            });

        });

    }                    
    
}

function pwa_for_wp_select2_exclude(type){
    var $select2 = jQuery('.pwa_for_wp-select2_exclude');
    
    if($select2.length > 0){
        jQuery($select2).each(function(i, obj) {
            var currentP = jQuery(this);  
            var $defaultResults = jQuery('option[value]:not([selected])', currentP);  
            
            var defaultResults = [];
            $defaultResults.each(function () {
                var $option = jQuery(this);
                defaultResults.push({
                    id: $option.attr('value'),
                    text: $option.text()
                });
            });
            var ajaxnewurl = ajaxurl + '?action=pwaforwp_get_select2_data&pwaforwp_security_nonce='+pwaforwp_obj.pwaforwp_security_nonce+'&type='+type;

            currentP.select2({           
                ajax: {             
                    url: ajaxnewurl,
                    delay: 250, 
                    cache: false,
                },            
                minimumInputLength: 2, 
                minimumResultsForSearch : 50,
                dataAdapter: jQuery.fn.select2.amd.require('select2/data/extended-ajax'),
                defaultResults: defaultResults
            });

        });

    }                    
    
}

jQuery('.pwaforwp-maskable-icon-upload').click(function(e) {	// Application Icon upload
    e.preventDefault();
    var t = jQuery(this);
    var pwaforwp_meda_uploader = wp.media({
        title: 'Maskable Icon',
        button: {
            text: 'Select Icon'
        },
        multiple: false  // Set this to true to allow multiple files to be selected
    })
    .on('select', function() {
        var attachment = pwaforwp_meda_uploader.state().get('selection').first().toJSON();
        t.parents('td').find('.pwaforwp-maskable-input').val(attachment.url);
        pwaforwp_check_maskable_input();
    })
    .open();
});

jQuery('.pwaforwp_js_remove_maskable').click(function(e) {
    e.preventDefault();
    jQuery(this).parents('td').find('.pwaforwp-maskable-input').val("");
    jQuery(this).hide();
});

jQuery('.pwaforwp-maskable-input').keyup(function() {
    if ( jQuery(this).val() == null || jQuery(this).val() == "") {
        jQuery(this).parents('td').find('.pwaforwp_js_remove_maskable').hide();
    }else{
        jQuery(this).parents('td').find('.pwaforwp_js_remove_maskable').show();
    }
})


function pwaforwp_check_maskable_input() {
    jQuery('.pwaforwp-maskable-input').each(function() {
        console.log(jQuery(this).val())
        if ( jQuery(this).val() == null || jQuery(this).val() == "") {
            jQuery(this).parents('td').find('.pwaforwp_js_remove_maskable').hide();
        }else{
            jQuery(this).parents('td').find('.pwaforwp_js_remove_maskable').show();
        }
    });
}
pwaforwp_check_maskable_input();

jQuery(document).ready(function($){
    $('#fcm_service_account_json').on('change', function(e){
        var file = this.files[0];
        if (!file) return;

        var formData = new FormData();
        formData.append('action', 'pwaforwp_upload_fcm_json');
        formData.append('pwaforwp_security_nonce', pwaforwp_obj.pwaforwp_security_nonce);
        formData.append('fcm_service_account_json', file);

        $.ajax({
            url: ajaxurl,
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function(resp){
                try {
                    if(resp.status === 1){
                        $('#fcm_server_key').val(resp.path);
                        $('#fcm_server_key_url').val(resp.path);
                        alert('File uploaded successfully!');
                    } else {
                        alert(resp.message || 'Upload failed');
                    }
                } catch(e){
                    alert('Upload failed');
                }
            },
            error: function(){
                alert('Upload failed');
            }
        });
    });
});