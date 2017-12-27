<?php
/**
 * WPBookList Admin UI Template Class
 *
 * @author   Jake Evans
 * @category Admin
 * @package  Includes/Classes
 * @version  1
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

if ( ! class_exists( 'WPBookList_Admin_UI_Template', false ) ) :
/**
 * WPBookList_Admin_Menu Class.
 */
class WPBookList_Admin_UI_Template {

    public static function output_open_admin_container($title, $iconurl){
    	return '<div class="wpbooklist-admin-tp-container">
    				<p class="wpbooklist-admin-tp-top-title"><img class="wpbooklist-admin-tp-title-icon" src="'.$iconurl.'" />'.$title.'</p>
    				<div class="wpbooklist-admin-tp-inner-container">';
    }

    public static function output_close_admin_container(){
    	return '</div></div>';
    }

    public static function output_template_advert(){
    	return '<div class="wpbooklist-admin-tp-container">
        			<div id="wpbooklist-admin-tp-advert-site-div">
          				<div id="wpbooklist-admin-tp-advert-visit-me-title">For Everything WPBookList</div>
                  <a target="_blank" id="wpbooklist-admin-tp-advert-visit-me-link" href="http://wpbooklist.com/">
                    <img src="http://wpbooklist.com/wp-content/uploads/2017/08/Screenshot-2017-08-29-14.24.55.png">
                    WPBookList.com
                  </a>
        			</div>
        			<p id="wpbooklist-admin-tp-advert-email-me">E-mail with questions, issues, concerns, suggestions, or anything else at <a href="mailto:general@wpbooklist.com">General@wpbooklist.com</a></p>
              <div id="wpbooklist-facebook-link-div">
                <a href="https://www.facebook.com/WPBookList-490463747966630/" target="_blank"><img height="34" style="border:0px;height:34px;" src="https://wpbooklist.com/wp-content/uploads/2017/11/fb-art.png" border="0" alt="Visit WPBookList of Facebook!"></a>
              </div>
        			<div id="wpbooklist-admin-tp-advert-money-container">
          				<form action="https://www.paypal.com/cgi-bin/webscr" method="post" target="_top">
                    <input type="hidden" name="cmd" value="_s-xclick">
                    <input type="hidden" name="hosted_button_id" value="VUVFXRUQ462UU">
                    <input type="image" src="https://www.paypalobjects.com/en_US/i/btn/btn_donate_LG.gif" border="0" name="submit" alt="PayPal - The safer, easier way to pay online!">
                    <img alt="" border="0" src="https://www.paypalobjects.com/en_US/i/scr/pixel.gif" width="1" height="1">
                  </form>

          				<a target="_blank" id="wpbooklist-patreon-link" href="http://patreon.com/user?u=3614120"><img id="wpbooklist-patreon-img" src="https://www.jakerevans.com/wp-content/plugins/wpbooklist/assets/img/patreon.png"></a>
          				<a href="https://ko-fi.com/A8385C9" target="_blank"><img height="34" style="border:0px;height:34px;" src="https://www.jakerevans.com/wp-content/plugins/wpbooklist/assets/img/kofi1.png" border="0" alt="Buy Me a Coffee at ko-fi.com"></a>
          				<p>And be sure to <a target="_blank" href="https://wordpress.org/support/plugin/wpbooklist/reviews/">leave a 5-star review of WPBookList!</a></p>
        			</div>
        		</div>';
    }

}

endif;


