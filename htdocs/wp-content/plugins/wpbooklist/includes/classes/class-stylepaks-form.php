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
        <p><p>'.__('What\'s a','wpbooklist').' <span class="wpbooklist-color-orange-italic">StylePak</span> '.__('you ask?','wpbooklist').' <span class="wpbooklist-color-orange-italic">StylePaks</span> '.__('are the best way to instantly change the look and feel of your','wpbooklist').' <span class="wpbooklist-color-orange-italic">'.__('WPBookList','wpbooklist').'</span> '.__('plugin!','wpbooklist').'</p><br/><br/>
<div class="section group">
  <div class="col span_1_of_2">
     <p class="wpbooklist-extension-title"><img class="wpbooklist-extension-icon-img" src="http://wpbooklist.com/wp-content/uploads/2017/08/svgs/librarystylepak.svg" />'.__('StoreFront Extension','wpbooklist').'</p>
           <a id="wpbooklist-extensions-page-img-link" href="http://wpbooklist.com/index.php/downloads/library-stylepak-bundle/">
            <div class="wpbooklist-extension-page-ext-div" id="wpbooklist-extension-page-ext-div-6">
           <img class="wpbooklist-extension-img" src="http://wpbooklist.com/wp-content/uploads/2017/08/svgs/librarystylepak.svg"  />
           <p class="wpbooklist-extension-p">'.__('Library StylePak Bundle!','wpbooklist').'</p>
           </div>
           </a>
           <p class="wpbooklist-extension-excerpt"><span class="wpbooklist-excerpt-span">'.__('Can\'t decide on just one StylePak? Get \'em all with the','wpbooklist').'<span class="wpbooklist-color-orange-italic"> '.__('Library StylePak Bundle!','wpbooklist').'</span></span><span class="wpbooklist-top-line-span"></span></p>
           <div class="wpbooklist-above-purchase-line"></div>
           <p class="wpbooklist-to-download-page"><a href="http://wpbooklist.com/index.php/downloads/library-stylepak-bundle/">'.__('More Details','wpbooklist').'</a></p>
           <div class="wpbooklist-extensions-purchase-button-link"><a href="http://wpbooklist.com/index.php/downloads/library-stylepak-bundle/">'.__('$8.00 - Purchase Now!','wpbooklist').'</a></div>
  </div>
  <div class="col span_1_of_2">
   <p class="wpbooklist-extension-title"><img class="wpbooklist-extension-icon-img" src="http://wpbooklist.com/wp-content/uploads/2017/08/svgs/librarystylepak.svg" />'.__('Library StylePak 1','wpbooklist').'</p>
          <a id="wpbooklist-extensions-page-img-link" href="http://wpbooklist.com/index.php/downloads/library-stylepak-1/">
            <div class="wpbooklist-extension-page-ext-div" id="wpbooklist-extension-page-ext-div-1">
           <img class="wpbooklist-extension-img" src="http://wpbooklist.com/wp-content/uploads/2017/08/svgs/librarystylepak.svg"  />
           <p class="wpbooklist-extension-p">'.__('Library StylePak 1','wpbooklist').'</p>
          </div>
          </a>
           <p class="wpbooklist-extension-excerpt"><span class="wpbooklist-excerpt-span"> '.__('Provides larger images, and also removes the book title from being displayed.','wpbooklist').'</span><span class="wpbooklist-top-line-span"></span></p>
           <div class="wpbooklist-above-purchase-line"></div>
           <p class="wpbooklist-to-download-page"><a href="http://wpbooklist.com/index.php/library-stylepak-1/">'.__('View Demo','wpbooklist').'</a></p>
           <div class="wpbooklist-extensions-purchase-button-link"><a href="http://wpbooklist.com/index.php/downloads/library-stylepak-1/">'.__('$2.00 - Purchase Now!','wpbooklist').'</a></div>
  </div>
  </div>
  <div class="section group">
  <div class="col span_1_of_2">
   <p class="wpbooklist-extension-title"><img class="wpbooklist-extension-icon-img" src="http://wpbooklist.com/wp-content/uploads/2017/08/svgs/librarystylepak.svg" />'.__('Library StylePak 2','wpbooklist').'</p>
          <a id="wpbooklist-extensions-page-img-link" href="http://wpbooklist.com/index.php/downloads/library-stylepak-2/">
            <div class="wpbooklist-extension-page-ext-div" id="wpbooklist-extension-page-ext-div-2">
           <img class="wpbooklist-extension-img" src="http://wpbooklist.com/wp-content/uploads/2017/08/svgs/librarystylepak.svg"  />
           <p class="wpbooklist-extension-p">'.__('Library StylePak 2','wpbooklist').'</p>
           </div>
           </a>
           <p class="wpbooklist-extension-excerpt"><span class="wpbooklist-excerpt-span">'.__('Combines the larger images of StylePak 1 while also displaying longer titles.','wpbooklist').'</span><span class="wpbooklist-top-line-span"></span></p>
           <div class="wpbooklist-above-purchase-line"></div>
           <p class="wpbooklist-to-download-page"><a href="http://wpbooklist.com/index.php/library-stylepak-2/">'.__('View Demo','wpbooklist').'</a></p>
           <div class="wpbooklist-extensions-purchase-button-link"><a href="http://wpbooklist.com/index.php/downloads/library-stylepak-2/">'.__('$2.00 - Purchase Now!','wpbooklist').'</a></div>
  </div>
  <div class="col span_1_of_2">
    <p class="wpbooklist-extension-title"><img class="wpbooklist-extension-icon-img" src="http://wpbooklist.com/wp-content/uploads/2017/08/svgs/librarystylepak.svg" />'.__('Library StylePak 3','wpbooklist').'</p>
            <a id="wpbooklist-extensions-page-img-link" href="http://wpbooklist.com/index.php/downloads/library-stylepak-3/">
             <div class="wpbooklist-extension-page-ext-div" id="wpbooklist-extension-page-ext-div-3">
             <img class="wpbooklist-extension-img" src="http://wpbooklist.com/wp-content/uploads/2017/08/svgs/librarystylepak.svg"  />
             <p class="wpbooklist-extension-p">'.__('Library StylePak 3','wpbooklist').'</p>
             </div>
           </a>
           <p class="wpbooklist-extension-excerpt"><span class="wpbooklist-excerpt-span"> '.__('Strives for a cleaner look. Perfect for sites with colored backgrounds.','wpbooklist').'</span><span class="wpbooklist-top-line-span"></span></p>
           <div class="wpbooklist-above-purchase-line"></div>
           <p class="wpbooklist-to-download-page"><a href="http://wpbooklist.com/index.php/library-stylepak-3/">'.__('View Demo','wpbooklist').'</a></p>
           <div class="wpbooklist-extensions-purchase-button-link"><a href="http://wpbooklist.com/index.php/downloads/library-stylepak-3/">'.__('$2.00 - Purchase Now!','wpbooklist').'</a></div>
  </div>
  </div>
  <div class="section group">
   <div class="col span_1_of_2">
   <p class="wpbooklist-extension-title"><img class="wpbooklist-extension-icon-img" src="http://wpbooklist.com/wp-content/uploads/2017/08/svgs/librarystylepak.svg" />'.__('Library StylePak 4','wpbooklist').'</p>
            <a id="wpbooklist-extensions-page-img-link" href="http://wpbooklist.com/index.php/downloads/library-stylepak-4/">
             <div class="wpbooklist-extension-page-ext-div" id="wpbooklist-extension-page-ext-div-4">
             <img class="wpbooklist-extension-img" src="http://wpbooklist.com/wp-content/uploads/2017/08/svgs/librarystylepak.svg"  />
             <p class="wpbooklist-extension-p">'.__('Library StylePak 4','wpbooklist').'</p>
             </div>
           </a>
           <p class="wpbooklist-extension-excerpt"><span class="wpbooklist-excerpt-span">'.__('Introduces portrait-type book covers, and reduces the font-size of the titles.','wpbooklist').'</span><span class="wpbooklist-top-line-span"></span></p>
           <div class="wpbooklist-above-purchase-line"></div>
           <p class="wpbooklist-to-download-page"><a href="http://wpbooklist.com/index.php/library-stylepak-4/">'.__('View Demo','wpbooklist').'</a></p>
           <div class="wpbooklist-extensions-purchase-button-link"><a href="http://wpbooklist.com/index.php/downloads/library-stylepak-4/">'.__('$2.00 - Purchase Now!','wpbooklist').'</a></div>
    </div>
    <div class="col span_1_of_2">
   <p class="wpbooklist-extension-title"><img class="wpbooklist-extension-icon-img" src="http://wpbooklist.com/wp-content/uploads/2017/08/svgs/librarystylepak.svg" />'.__('Library StylePak 5','wpbooklist').'</p>
            <a id="wpbooklist-extensions-page-img-link" href="http://wpbooklist.com/index.php/downloads/library-stylepak-5/">
             <div class="wpbooklist-extension-page-ext-div" id="wpbooklist-extension-page-ext-div-5">
             <img class="wpbooklist-extension-img" src="http://wpbooklist.com/wp-content/uploads/2017/08/svgs/librarystylepak.svg"  />
             <p class="wpbooklist-extension-p">'.__('Library StylePak 5','wpbooklist').'</p>
             </div>
           </a>
           <p class="wpbooklist-extension-excerpt"><span class="wpbooklist-excerpt-span">'.__('Introduces a fixed wood-grain background emulating a bookshelf.','wpbooklist').'</span><span class="wpbooklist-top-line-span"></span></p>
           <div class="wpbooklist-above-purchase-line"></div>
           <p class="wpbooklist-to-download-page"><a href="http://wpbooklist.com/index.php/library-stylepak-5/">'.__('View Demo','wpbooklist').'</a></p>
           <div class="wpbooklist-extensions-purchase-button-link"><a href="http://wpbooklist.com/index.php/downloads/library-stylepak-5/">'.__('$2.00 - Purchase Now!','wpbooklist').'</a></div>
    </div>
  </div>
  <div class="section group">
   <div class="col span_1_of_2">
   <p class="wpbooklist-extension-title"><img class="wpbooklist-extension-icon-img" src="http://wpbooklist.com/wp-content/uploads/2017/08/svgs/librarystylepak.svg" />'.__('Library StylePak 4','wpbooklist').'</p>
            <a id="wpbooklist-extensions-page-img-link" href="http://wpbooklist.com/index.php/downloads/library-stylepak-6/">
             <div class="wpbooklist-extension-page-ext-div" id="wpbooklist-extension-page-ext-div-8">
             <img class="wpbooklist-extension-img" src="http://wpbooklist.com/wp-content/uploads/2017/08/svgs/librarystylepak.svg"  />
             <p class="wpbooklist-extension-p">'.__('Library StylePak 6','wpbooklist').'</p>
             </div>
           </a>
           <p class="wpbooklist-extension-excerpt"><span class="wpbooklist-excerpt-span">'.__('Features smaller cover images and longer, bolder titles, allowing more books per row to be displayed.','wpbooklist').'</span><span class="wpbooklist-top-line-span"></span></p>
           <div class="wpbooklist-above-purchase-line"></div>
           <p class="wpbooklist-to-download-page"><a href="http://wpbooklist.com/index.php/library-stylepak-6/">'.__('View Demo','wpbooklist').'</a></p>
           <div class="wpbooklist-extensions-purchase-button-link"><a href="http://wpbooklist.com/index.php/downloads/library-stylepak-6/">'.__('$2.00 - Purchase Now!','wpbooklist').'</a></div>
    </div>

    </div>





</div>

        ';

        return $string1;
  }


}

endif;