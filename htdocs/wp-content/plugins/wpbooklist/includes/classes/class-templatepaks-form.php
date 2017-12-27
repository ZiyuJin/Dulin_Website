<?php
/**
 * WPBookList Add-Edit-Book-Form Tab Class
 *
 * @author   Jake Evans
 * @category Admin
 * @package  Includes/Classes
 * @version  1
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

if ( ! class_exists( 'WPBookList_Add_Book_Form', false ) ) :
/**
 * WPBookList_Admin_Menu Class.
 */
class WPBookList_Add_Book_Form {

  public static function output_add_book_form(){

    // Perform check for previously-saved Amazon Authorization
    global $wpdb;
    $table_name = $wpdb->prefix . 'wpbooklist_jre_user_options';
    $opt_results = $wpdb->get_row("SELECT * FROM $table_name");

    $table_name = $wpdb->prefix . 'wpbooklist_jre_list_dynamic_db_names';
    $db_row = $wpdb->get_results("SELECT * FROM $table_name");

    // For grabbing an image from media library
    wp_enqueue_media();
    $string1 = '<div id="wpbooklist-addbook-container">
        <p><p>'.__('What\'s a','wpbooklist').' <span class="wpbooklist-color-orange-italic">Template Pak</span> '.__('you ask?','wpbooklist').' <span class="wpbooklist-color-orange-italic">Template Paks</span> '.__('are the best way to instantly change the look and feel of your','wpbooklist').' <span class="wpbooklist-color-orange-italic">'.__('WPBookList','wpbooklist').'</span> '.__('Pages and Posts!','wpbooklist').'</p><br/><br/>
<div class="section group">
  <div class="col span_1_of_2">
     <p class="wpbooklist-extension-title"><img class="wpbooklist-extension-icon-img" src="http://wpbooklist.com/wp-content/uploads/2017/08/svgs/seo-template-white.svg" />'.__('StoreFront Extension','wpbooklist').'</p>
           <a id="wpbooklist-extensions-page-img-link" href="https://wpbooklist.com/index.php/downloads/template-pak-bundle/">
            <div class="wpbooklist-extension-page-ext-div" id="wpbooklist-extension-page-ext-div-6">
           <img class="wpbooklist-extension-img" src="http://wpbooklist.com/wp-content/uploads/2017/08/svgs/seo-template-white.svg"  />
           <p class="wpbooklist-extension-p">'.__('Template Pak Bundle!','wpbooklist').'</p>
           </div>
           </a>
           <p class="wpbooklist-extension-excerpt"><span class="wpbooklist-excerpt-span">'.__('Can\'t decide on just one Template Pak? Get \'em all with the','wpbooklist').'<span class="wpbooklist-color-orange-italic"> '.__('Template Pak Bundle!','wpbooklist').'</span></span><span class="wpbooklist-top-line-span"></span></p>
           <div class="wpbooklist-above-purchase-line"></div>
           <p class="wpbooklist-to-download-page"><a href="https://wpbooklist.com/index.php/downloads/template-pak-bundle/">'.__('More Details','wpbooklist').'</a></p>
           <div class="wpbooklist-extensions-purchase-button-link"><a href="https://wpbooklist.com/index.php/downloads/template-pak-bundle/">'.__('$8.00 - Purchase Now!','wpbooklist').'</a></div>
  </div>
  <div class="col span_1_of_2">
   <p class="wpbooklist-extension-title"><img class="wpbooklist-extension-icon-img" src="http://wpbooklist.com/wp-content/uploads/2017/08/svgs/seo-template-white.svg" />'.__('Template Pak 1','wpbooklist').'</p>
          <a id="wpbooklist-extensions-page-img-link" href="https://wpbooklist.com/index.php/downloads/template-pak-1/">
            <div class="wpbooklist-extension-page-ext-div" id="wpbooklist-extension-page-ext-div-1">
           <img class="wpbooklist-extension-img" src="http://wpbooklist.com/wp-content/uploads/2017/08/svgs/seo-template-white.svg"  />
           <p class="wpbooklist-extension-p">'.__('Template Pak 1','wpbooklist').'</p>
          </div>
          </a>
           <p class="wpbooklist-extension-excerpt"><span class="wpbooklist-excerpt-span"> '.__('Moves Book Details and Purchase Links to the left side, placing focus on the Book Description and Amazon Reviews.','wpbooklist').'</span><span class="wpbooklist-top-line-span"></span></p>
           <div class="wpbooklist-above-purchase-line"></div>
           <p class="wpbooklist-to-download-page"><a href="https://wpbooklist.com/index.php/downloads/template-pak-1/">'.__('More Details','wpbooklist').'</a></p>
           <div class="wpbooklist-extensions-purchase-button-link"><a href="https://wpbooklist.com/index.php/downloads/template-pak-1/">'.__('$2.00 - Purchase Now!','wpbooklist').'</a></div>
  </div>
  </div>
  <div class="section group">
  <div class="col span_1_of_2">
   <p class="wpbooklist-extension-title"><img class="wpbooklist-extension-icon-img" src="http://wpbooklist.com/wp-content/uploads/2017/08/svgs/seo-template-white.svg" />'.__('Template Pak 2','wpbooklist').'</p>
          <a id="wpbooklist-extensions-page-img-link" href="https://wpbooklist.com/index.php/downloads/template-pak-2/">
            <div class="wpbooklist-extension-page-ext-div" id="wpbooklist-extension-page-ext-div-2">
           <img class="wpbooklist-extension-img" src="http://wpbooklist.com/wp-content/uploads/2017/08/svgs/seo-template-white.svg"  />
           <p class="wpbooklist-extension-p">'.__('Template Pak 2','wpbooklist').'</p>
           </div>
           </a>
           <p class="wpbooklist-extension-excerpt"><span class="wpbooklist-excerpt-span">'.__('Features a larger cover image, a horizontal Book Details section, and a centered left column.','wpbooklist').'</span><span class="wpbooklist-top-line-span"></span></p>
           <div class="wpbooklist-above-purchase-line"></div>
           <p class="wpbooklist-to-download-page"><a href="https://wpbooklist.com/index.php/downloads/template-pak-2/">'.__('More Details','wpbooklist').'</a></p>
           <div class="wpbooklist-extensions-purchase-button-link"><a href="https://wpbooklist.com/index.php/downloads/template-pak-2/">'.__('$2.00 - Purchase Now!','wpbooklist').'</a></div>
  </div>
  <div class="col span_1_of_2">
    <p class="wpbooklist-extension-title"><img class="wpbooklist-extension-icon-img" src="http://wpbooklist.com/wp-content/uploads/2017/08/svgs/seo-template-white.svg" />'.__('Template Pak 3','wpbooklist').'</p>
            <a id="wpbooklist-extensions-page-img-link" href="https://wpbooklist.com/index.php/downloads/template-pak-3/">
             <div class="wpbooklist-extension-page-ext-div" id="wpbooklist-extension-page-ext-div-3">
             <img class="wpbooklist-extension-img" src="http://wpbooklist.com/wp-content/uploads/2017/08/svgs/seo-template-white.svg"  />
             <p class="wpbooklist-extension-p">'.__('Template Pak 3','wpbooklist').'</p>
             </div>
           </a>
           <p class="wpbooklist-extension-excerpt"><span class="wpbooklist-excerpt-span"> '.__('Places the Social icons vertically, moves the purchase links to the right, and aligns the left column under the cover image','wpbooklist').'</span><span class="wpbooklist-top-line-span"></span></p>
           <div class="wpbooklist-above-purchase-line"></div>
           <p class="wpbooklist-to-download-page"><a href="https://wpbooklist.com/index.php/downloads/template-pak-3/">'.__('More Details','wpbooklist').'</a></p>
           <div class="wpbooklist-extensions-purchase-button-link"><a href="https://wpbooklist.com/index.php/downloads/template-pak-3/">'.__('$2.00 - Purchase Now!','wpbooklist').'</a></div>
  </div>
  </div>
  <div class="section group">
   <div class="col span_1_of_2">
   <p class="wpbooklist-extension-title"><img class="wpbooklist-extension-icon-img" src="http://wpbooklist.com/wp-content/uploads/2017/08/svgs/seo-template-white.svg" />'.__('Template Pak 4','wpbooklist').'</p>
            <a id="wpbooklist-extensions-page-img-link" href="https://wpbooklist.com/index.php/downloads/template-pak-4/">
             <div class="wpbooklist-extension-page-ext-div" id="wpbooklist-extension-page-ext-div-4">
             <img class="wpbooklist-extension-img" src="http://wpbooklist.com/wp-content/uploads/2017/08/svgs/seo-template-white.svg"  />
             <p class="wpbooklist-extension-p">'.__('Template Pak 4','wpbooklist').'</p>
             </div>
           </a>
           <p class="wpbooklist-extension-excerpt"><span class="wpbooklist-excerpt-span">'.__('Larger images, vertical social icons, aligns the Book Details, Description, and Amazon reviews further right.','wpbooklist').'</span><span class="wpbooklist-top-line-span"></span></p>
           <div class="wpbooklist-above-purchase-line"></div>
           <p class="wpbooklist-to-download-page"><a href="https://wpbooklist.com/index.php/downloads/template-pak-4/">'.__('More Details','wpbooklist').'</a></p>
           <div class="wpbooklist-extensions-purchase-button-link"><a href="https://wpbooklist.com/index.php/downloads/template-pak-4/">'.__('$2.00 - Purchase Now!','wpbooklist').'</a></div>
    </div>
    <div class="col span_1_of_2">
   <p class="wpbooklist-extension-title"><img class="wpbooklist-extension-icon-img" src="http://wpbooklist.com/wp-content/uploads/2017/08/svgs/seo-template-white.svg" />'.__('Template Pak 5','wpbooklist').'</p>
            <a id="wpbooklist-extensions-page-img-link" href="https://wpbooklist.com/index.php/downloads/template-pak-5/">
             <div class="wpbooklist-extension-page-ext-div" id="wpbooklist-extension-page-ext-div-5">
             <img class="wpbooklist-extension-img" src="http://wpbooklist.com/wp-content/uploads/2017/08/svgs/seo-template-white.svg"  />
             <p class="wpbooklist-extension-p">'.__('Template Pak 5','wpbooklist').'</p>
             </div>
           </a>
           <p class="wpbooklist-extension-excerpt"><span class="wpbooklist-excerpt-span">'.__('Takes a cue from <a href="https://wpbooklist.com/index.php/downloads/library-stylepak-5/" class="targetpop-predictions-link-tracker-class">Library StylePak 5</a>, sporting white text and a fixed wood-grain background.  </p>','wpbooklist').'</span><span class="wpbooklist-top-line-span"></span></p>
           <div class="wpbooklist-above-purchase-line"></div>
           <p class="wpbooklist-to-download-page"><a href="https://wpbooklist.com/index.php/downloads/template-pak-5/">'.__('More Details','wpbooklist').'</a></p>
           <div class="wpbooklist-extensions-purchase-button-link"><a href="https://wpbooklist.com/index.php/downloads/template-pak-5/">'.__('$2.00 - Purchase Now!','wpbooklist').'</a></div>
    </div>
  </div>
  





</div>

        ';

        return $string1;
  }


}

endif;