/**
* For loaders
*/

var showLoader = false;
if (pwaforwp_is_mobile() && pwaforwp_js_obj.loader_mobile == "1" && screen.availWidth < 521) {
    console.log(screen.availWidth);
    pwaforwp_play_loader();
}
if (pwaforwp_js_obj.loader_desktop == "1" && screen.availWidth > 520) {
    pwaforwp_play_loader();
}
if (pwaforwp_js_obj.loader_admin == "1" && pwaforwp_js_obj.user_admin == '1') {
    pwaforwp_play_loader();
}
if (pwaforwp_js_obj.loader_only_pwa == "1") {
    if (window.matchMedia('(display-mode: standalone)').matches || window.matchMedia('(display-mode: fullscreen)').matches || window.matchMedia('(display-mode: minimal-ui)').matches) {
        pwaforwp_play_loader();
    }
}
function pwaforwp_play_loader() {

    var isSafari = !!navigator.userAgent.match(/Version\/[\d\.]+.*Safari/);
    var iOS = /iPad|iPhone|iPod/.test(navigator.userAgent) && !window.MSStream;
    if (isSafari && iOS) {
        //iOS and safari beforeunload not working fix

        let btn_link_clicks = document.querySelectorAll('a,input,button');
        for (let i = 0; i < btn_link_clicks.length; i++) {
            btn_link_clicks[i].addEventListener("click", handle_ios_clicks_for_loader);
        }

        if (window.localStorage.getItem('pwaforwp_defaultload') != 'disabled') {
            if (document.getElementsByClassName('pwaforwp-loading-wrapper') && typeof document.getElementsByClassName('pwaforwp-loading-wrapper')[0] !== 'undefined') {
                document.getElementsByClassName('pwaforwp-loading-wrapper')[0].style.display = "flex";
            }
            if (document.getElementById('pwaforwp_loading_div')) {
                document.getElementById('pwaforwp_loading_div').style.display = "flex";
            }
            if (document.getElementById('pwaforwp_loading_icon')) {
                document.getElementById('pwaforwp_loading_icon').style.display = "flex";
            }
            setInterval(function () {
                if (document.getElementById('pwaforwp_loading_div')) {
                    document.getElementById('pwaforwp_loading_div').style.display = "none";
                }
                if (document.getElementById('pwaforwp_loading_icon')) {
                    document.getElementById('pwaforwp_loading_icon').style.display = "none";
                }
                if (document.getElementsByClassName('pwaforwp-loading-wrapper') && document.getElementsByClassName('pwaforwp-loading-wrapper').length) {
                    const collection2 = document.getElementsByClassName("pwaforwp-loading-wrapper");
                    for (let i = 0; i < collection2.length; i++) {
                        collection2[i].style.display = "none";
                    }

                }
            },
                1000, true);
        }

        // fix for loader showing for infinite time when back button is pressed
        window.addEventListener("pagehide", function () {
            if (document.getElementById('pwaforwp_loading_div')) {
                document.getElementById('pwaforwp_loading_div').style.display = "none";
            }
            if (document.getElementById('pwaforwp_loading_icon')) {
                document.getElementById('pwaforwp_loading_icon').style.display = "none";
            }
            if (document.getElementsByClassName('pwaforwp-loading-wrapper') && document.getElementsByClassName('pwaforwp-loading-wrapper').length) {
                const collection2 = document.getElementsByClassName("pwaforwp-loading-wrapper");
                for (let i = 0; i < collection2.length; i++) {
                    collection2[i].style.display = "none";
                }
            }

        });

        window.localStorage.setItem('pwaforwp_defaultload', 'enabled');
    }
    else {
        window.addEventListener("beforeunload", function () {
            if (document.getElementsByClassName('pwaforwp-loading-wrapper') && typeof document.getElementsByClassName('pwaforwp-loading-wrapper')[0] !== 'undefined') {
                document.getElementsByClassName('pwaforwp-loading-wrapper')[0].style.display = "flex";
            }
            if (document.getElementById('pwaforwp_loading_div')) {
                document.getElementById('pwaforwp_loading_div').style.display = "flex";
            }
            if (document.getElementById('pwaforwp_loading_icon')) {
                document.getElementById('pwaforwp_loading_icon').style.display = "flex";
            }

        });
        setInterval(function () {
            if (document.getElementsByClassName('pwaforwp-loading-wrapper') && document.getElementsByClassName('pwaforwp-loading-wrapper').length > 0) {
                var tot = document.getElementsByClassName('pwaforwp-loading-wrapper');
                for (var i = 0; i < tot.length; i++) {
                    tot[i].style.display = "none";
                }
            }
            if (document.getElementById('pwaforwp_loading_div')) {
                document.getElementById('pwaforwp_loading_div').style.display = "none";
            }
            if (document.getElementById('pwaforwp_loading_icon')) {
                document.getElementById('pwaforwp_loading_icon').style.display = "none";
            }
        }, 5000, true);
    }
}

/*
* For Add to home screen Popup    
* Start
*/
var close_btns = document.getElementsByClassName('.pwaforwp_add_home_close');
if (close_btns.length) {
    close_btns[0].addEventListener('click', function (event) {
        document.cookie = "pwaforwp_prompt_close=" + new Date();
        close_btns[0].parentNode.style.display = "none";
    });

    if (close_btns[0].offsetWidth == 0 && close_btns[0].offsetHeight == 0) {
        document.getElementsByClassName('pwaforwp-sticky-banner').style.display = "none";
    } else {
        document.getElementsByClassName('pwaforwp-sticky-banner').style.display = "block";
    }
    if (pwaforwp_js_obj.reset_cookies == 1) {
        document.cookie = 'pwaforwp_prompt_close=; Path=/; Expires=Thu, 01 Jan 1970 00:00:01 GMT;'
    }
}


function pwaforwp_is_mobile() {
    var isMobile = false; //initiate as false
    // device detection
    if (/(android|bb\d+|meego).+mobile|avantgo|bada\/|blackberry|blazer|compal|elaine|fennec|hiptop|iemobile|ip(hone|od)|ipad|iris|kindle|Android|Silk|lge |maemo|midp|mmp|netfront|opera m(ob|in)i|palm( os)?|phone|p(ixi|re)\/|plucker|pocket|psp|series(4|6)0|symbian|treo|up\.(browser|link)|vodafone|wap|windows (ce|phone)|xda|xiino/i.test(navigator.userAgent)
        || /1207|6310|6590|3gso|4thp|50[1-6]i|770s|802s|a wa|abac|ac(er|oo|s\-)|ai(ko|rn)|al(av|ca|co)|amoi|an(ex|ny|yw)|aptu|ar(ch|go)|as(te|us)|attw|au(di|\-m|r |s )|avan|be(ck|ll|nq)|bi(lb|rd)|bl(ac|az)|br(e|v)w|bumb|bw\-(n|u)|c55\/|capi|ccwa|cdm\-|cell|chtm|cldc|cmd\-|co(mp|nd)|craw|da(it|ll|ng)|dbte|dc\-s|devi|dica|dmob|do(c|p)o|ds(12|\-d)|el(49|ai)|em(l2|ul)|er(ic|k0)|esl8|ez([4-7]0|os|wa|ze)|fetc|fly(\-|_)|g1 u|g560|gene|gf\-5|g\-mo|go(\.w|od)|gr(ad|un)|haie|hcit|hd\-(m|p|t)|hei\-|hi(pt|ta)|hp( i|ip)|hs\-c|ht(c(\-| |_|a|g|p|s|t)|tp)|hu(aw|tc)|i\-(20|go|ma)|i230|iac( |\-|\/)|ibro|idea|ig01|ikom|im1k|inno|ipaq|iris|ja(t|v)a|jbro|jemu|jigs|kddi|keji|kgt( |\/)|klon|kpt |kwc\-|kyo(c|k)|le(no|xi)|lg( g|\/(k|l|u)|50|54|\-[a-w])|libw|lynx|m1\-w|m3ga|m50\/|ma(te|ui|xo)|mc(01|21|ca)|m\-cr|me(rc|ri)|mi(o8|oa|ts)|mmef|mo(01|02|bi|de|do|t(\-| |o|v)|zz)|mt(50|p1|v )|mwbp|mywa|n10[0-2]|n20[2-3]|n30(0|2)|n50(0|2|5)|n7(0(0|1)|10)|ne((c|m)\-|on|tf|wf|wg|wt)|nok(6|i)|nzph|o2im|op(ti|wv)|oran|owg1|p800|pan(a|d|t)|pdxg|pg(13|\-([1-8]|c))|phil|pire|pl(ay|uc)|pn\-2|po(ck|rt|se)|prox|psio|pt\-g|qa\-a|qc(07|12|21|32|60|\-[2-7]|i\-)|qtek|r380|r600|raks|rim9|ro(ve|zo)|s55\/|sa(ge|ma|mm|ms|ny|va)|sc(01|h\-|oo|p\-)|sdk\/|se(c(\-|0|1)|47|mc|nd|ri)|sgh\-|shar|sie(\-|m)|sk\-0|sl(45|id)|sm(al|ar|b3|it|t5)|so(ft|ny)|sp(01|h\-|v\-|v )|sy(01|mb)|t2(18|50)|t6(00|10|18)|ta(gt|lk)|tcl\-|tdg\-|tel(i|m)|tim\-|t\-mo|to(pl|sh)|ts(70|m\-|m3|m5)|tx\-9|up(\.b|g1|si)|utst|v400|v750|veri|vi(rg|te)|vk(40|5[0-3]|\-v)|vm40|voda|vulc|vx(52|53|60|61|70|80|81|83|85|98)|w3c(\-| )|webc|whit|wi(g |nc|nw)|wmlb|wonu|x700|yas\-|your|zeto|zte\-/i.test(navigator.userAgent.substr(0, 4))) {
        isMobile = true;
    }
    return isMobile;
}

function handle_ios_clicks_for_loader(e) {
    var anchor_href = "";
    var input_type = "";
    anchor_href = e.target.getAttribute("href");
    input_type = e.target.getAttribute("type");
    if (is_valid_url(anchor_href) || input_type == "submit") {
        window.localStorage.setItem('pwaforwp_defaultload', 'disabled');
        if (document.getElementsByClassName('pwaforwp-loading-wrapper') && document.getElementsByClassName('pwaforwp-loading-wrapper').length) {
            document.getElementsByClassName('pwaforwp-loading-wrapper')[0].style.display = "flex";
        }

        if (document.getElementById('pwaforwp_loading_div')) {
            document.getElementById('pwaforwp_loading_div').style.display = "flex";
        }
        if (document.getElementById('pwaforwp_loading_icon')) {
            document.getElementById('pwaforwp_loading_icon').style.display = "flex";
        }
    }
}

function is_valid_url(urlString) {
    var urlPattern = new RegExp('^(https?:\\/\\/)?' + // validate protocol
        '((([a-z\\d]([a-z\\d-]*[a-z\\d])*)\\.)+[a-z]{2,}|' + // validate domain name
        '((\\d{1,3}\\.){3}\\d{1,3}))' + // validate OR ip (v4) address
        '(\\:\\d+)?(\\/[-a-z\\d%_.~+]*)*' + // validate port and path
        '(\\?[;&a-z\\d%_.~+=-]*)?' + // validate query string
        '(\\#[-a-z\\d_]*)?$', 'i'); // validate fragment locator
    return !!urlPattern.test(urlString);
}

document.addEventListener('DOMContentLoaded', function() {
    // Only run if touch is supported, swipe_navigation is enabled, and on mobile devices
    if (!('ontouchstart' in window) || pwaforwp_js_obj.swipe_navigation != '1' || window.innerWidth > 768 || !pwaforwp_js_obj.prev_post_url && !pwaforwp_js_obj.next_post_url) return;

    let touchstartX = 0;
    let touchendX = 0;
    const threshold = 100; // Minimum px distance to trigger swipe
  
    // Create gradient overlay elements
    const leftGradient = document.createElement('div');
    leftGradient.id = 'swipe-gradient-left';
    Object.assign(leftGradient.style, {
      position: 'fixed',
      top: '0',
      bottom: '0',
      left: '0',
      width: '50%',
      pointerEvents: 'none',
      zIndex: '9999',
      background: 'linear-gradient(to right, rgba(0,0,0,0.5), transparent)',
      opacity: '0',
      transition: 'opacity 0.1s'
    });
  
    const rightGradient = document.createElement('div');
    rightGradient.id = 'swipe-gradient-right';
    Object.assign(rightGradient.style, {
      position: 'fixed',
      top: '0',
      bottom: '0',
      right: '0',
      width: '50%',
      pointerEvents: 'none',
      zIndex: '9999',
      background: 'linear-gradient(to left, rgba(0,0,0,0.5), transparent)',
      opacity: '0',
      transition: 'opacity 0.1s'
    });
  
    document.body.appendChild(leftGradient);
    document.body.appendChild(rightGradient);
  
    // Hide both gradients
    function hideGradients() {
      leftGradient.style.opacity = '0';
      rightGradient.style.opacity = '0';
    }
  
  
    document.addEventListener('touchstart', function(event) {
      touchstartX = event.changedTouches[0].screenX;
      hideGradients(); // Reset gradients on new touch
    });
  

    document.addEventListener('touchmove', function(event) {

      const currentX = event.changedTouches[0].screenX;
      const deltaX = currentX - touchstartX;
      const fraction = Math.min(Math.abs(deltaX) / threshold, 1); // fraction: 0 to 1
        if(pwaforwp_js_obj.prev_post_url || pwaforwp_js_obj.next_post_url) {
            if (deltaX > 0) {
                // Swipe right: update left gradient
                leftGradient.style.opacity = fraction.toString();
                rightGradient.style.opacity = '0';
            } else if (deltaX < 0) {
                // Swipe left: update right gradient
                rightGradient.style.opacity = fraction.toString();
                leftGradient.style.opacity = '0';
            }
        }
    });
  
    document.addEventListener('touchend', function(event) {
      touchendX = event.changedTouches[0].screenX;
      const deltaX = touchendX - touchstartX;
      const fraction = Math.min(Math.abs(deltaX) / threshold, 1);
  
      if (fraction < 1) {
        hideGradients();
      } else {
        if (deltaX > 0 && pwaforwp_js_obj.prev_post_url) {
          leftGradient.style.opacity = '1';
          setTimeout(function() {
            window.location.href = pwaforwp_js_obj.prev_post_url;
          }, 300);
        } else if (deltaX < 0 && pwaforwp_js_obj.next_post_url) {
          rightGradient.style.opacity = '1';
          setTimeout(function() {
            window.location.href = pwaforwp_js_obj.next_post_url;
          }, 300);
        }
      }
    });
  });
  // make sure only pwaforwp manifest is loaded
  window.addEventListener('load', function() {
    let manifest_name = (typeof pwaforwp_js_obj !== 'undefined' && pwaforwp_js_obj.pwa_manifest_name) 
                        ? pwaforwp_js_obj.pwa_manifest_name 
                        : 'pwa-manifest.json';

    // Remove all existing manifest link tags
    document.querySelectorAll('link[rel="manifest"]').forEach(link => link.remove());

    // Add new manifest link
    const link = document.createElement('link');
    link.rel = 'manifest';
    link.href = '/' + manifest_name;
    document.head.appendChild(link);
});
