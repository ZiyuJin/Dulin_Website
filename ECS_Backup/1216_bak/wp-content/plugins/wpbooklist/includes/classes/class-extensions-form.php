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
        <p>'.__('Extensions are the easiest way to add additional functionality to your','wpbooklist').'<span class="wpbooklist-color-orange-italic">WPBookList</span> '.__('plugin. Simply purchase the extension of your choice and install it just like you’d install any other WordPress plugin. That’s all there is to it!','wpbooklist').'<br/><br/>
<div class="section group">
  <div class="col span_1_of_2">
     <p class="wpbooklist-extension-title"><img class="wpbooklist-extension-icon-img" src="http://www.wpbooklist.com/wp-content/uploads/wpbooklist/icons/goodreads.svg" />'.__('Goodreads Extension','wpbooklist').'</p>
              <a id="wpbooklist-extensions-page-img-link" href="http://wpbooklist.com/index.php/downloads/extensions-bundle/">
                <div class="wpbooklist-extension-page-ext-div" id="wpbooklist-extension-page-ext-div-6">
                  <img class="wpbooklist-extension-img-bundle-mult" src="http://wpbooklist.com/wp-content/uploads/2017/08/svgs/book.svg"><img class="wpbooklist-extension-img-bundle-mult" src="http://wpbooklist.com/wp-content/uploads/2017/08/svgs/ereader-with-bookmark.svg"><img class="wpbooklist-extension-img-bundle-mult" src="http://wpbooklist.com/wp-content/uploads/2017/08/svgs/profits.svg"><p></p>
                  <p class="wpbooklist-extension-p-bundle-ext">'.__('Extensions Bundle!','wpbooklist').'</p>
                  <p><img class="wpbooklist-extension-img-bundle-mult" src="http://wpbooklist.com/wp-content/uploads/2017/08/svgs/server.svg"><img class="wpbooklist-extension-img-bundle-mult" src="http://wpbooklist.com/wp-content/uploads/2017/08/svgs/ereader.svg"><img class="wpbooklist-extension-img-bundle-mult" src="http://wpbooklist.com/wp-content/uploads/2017/08/svgs/goodreads-letter-logo.svg">
                           </p>
                </div>
             </a>
             <p class="wpbooklist-extension-excerpt"><span class="wpbooklist-excerpt-span">'.__('Can’t decide which Extension to buy? Get ’em all with the','wpbooklist').' <span class="wpbooklist-color-orange-italic">Extensions Bundle!</span></span><span class="wpbooklist-top-line-span"></span></p>
             <div class="wpbooklist-above-purchase-line"></div>
             <p class="wpbooklist-to-download-page"><a href="http://wpbooklist.com/index.php/downloads/extensions-bundle/">'.__('More Details','wpbooklist').'</a></p>
             <div class="wpbooklist-extensions-purchase-button-link"><a href="http://wpbooklist.com/index.php/downloads/extensions-bundle/">'.__('$18.00 - Purchase Now','wpbooklist').'</a></div>
  </div>
  <div class="col span_1_of_2">
   <p class="wpbooklist-extension-title"><img class="wpbooklist-extension-icon-img" src="http://www.wpbooklist.com/wp-content/uploads/wpbooklist/icons/affiliate.svg" />'.__('Affiliates Extension','wpbooklist').'</p>
          <a id="wpbooklist-extensions-page-img-link" href="http://wpbooklist.com/index.php/downloads/affiliate-extension/">
            <div class="wpbooklist-extension-page-ext-div" id="wpbooklist-extension-page-ext-div-2">
           <img class="wpbooklist-extension-img" src="http://wpbooklist.com/wp-content/uploads/2017/08/svgs/profits.svg"  />
           <p class="wpbooklist-extension-p">'.__('Affiliate','wpbooklist').'</p>
          </div>
          </a>
           <p class="wpbooklist-extension-excerpt"><span class="wpbooklist-excerpt-span">'.__('Let WPBookList work for you with the','wpbooklist').' <span class="wpbooklist-color-orange-italic">'.__('WPBookList Affiliates Extension!','wpbooklist').'</span></span><span class="wpbooklist-top-line-span"></span></p>
           <div class="wpbooklist-above-purchase-line"></div>
           <p class="wpbooklist-to-download-page"><a href="http://wpbooklist.com/index.php/downloads/affiliate-extension/">'.__('More Details','wpbooklist').'</a></p>
           <div class="wpbooklist-extensions-purchase-button-link"><a href="http://wpbooklist.com/index.php/downloads/affiliate-extension/">'.__('$5.00 - Purchase Now','wpbooklist').'</a></div>
  </div>
</div>
<div class="section group">
  <div class="col span_1_of_2">
     <p class="wpbooklist-extension-title"><img class="wpbooklist-extension-icon-img" src="http://www.wpbooklist.com/wp-content/uploads/wpbooklist/icons/book.svg" />'.__('StoreFront Extension','wpbooklist').'</p>
           <a id="wpbooklist-extensions-page-img-link" href="http://wpbooklist.com/index.php/downloads/storefront-extension/">
            <div class="wpbooklist-extension-page-ext-div" id="wpbooklist-extension-page-ext-div-1">
           <img class="wpbooklist-extension-img" src="http://wpbooklist.com/wp-content/uploads/2017/08/svgs/book.svg"  />
           <p class="wpbooklist-extension-p">'.__('StoreFront','wpbooklist').'</p>
           </div>
           </a>
           <p class="wpbooklist-extension-excerpt"><span class="wpbooklist-excerpt-span">'.__('Got books to sell? Then you need the','wpbooklist').' <span class="wpbooklist-color-orange-italic">'.__('WPBookList StoreFront Extension!','wpbooklist').'</span></span><span class="wpbooklist-top-line-span"></span></p>
           <div class="wpbooklist-above-purchase-line"></div>
           <p class="wpbooklist-to-download-page"><a href="http://wpbooklist.com/index.php/downloads/storefront-extension/">'.__('More Details','wpbooklist').'</a></p>
           <div class="wpbooklist-extensions-purchase-button-link"><a href="http://wpbooklist.com/index.php/downloads/storefront-extension/">'.__('$10.00 - Purchase Now','wpbooklist').'</a></div>
  </div>
  <div class="col span_1_of_2">
   <p class="wpbooklist-extension-title"><img class="wpbooklist-extension-icon-img" src="http://www.wpbooklist.com/wp-content/uploads/wpbooklist/icons/goodreads.svg" />'.__('TimeSaver Bundle','wpbooklist').'</p>
            <a id="wpbooklist-extensions-page-img-link" href="https://wpbooklist.com/index.php/downloads/timesaver-bundle/">
              <div class="wpbooklist-extension-page-ext-div" id="wpbooklist-extension-page-ext-div-8">
                <img class="wpbooklist-extension-img-bundle-mult" src="http://wpbooklist.com/wp-content/uploads/2017/08/svgs/goodreads-letter-logo.svg"><p></p>
                <p class="wpbooklist-extension-p-bundle-ext">TimeSaver Bundle!</p>
                <p><img class="wpbooklist-extension-img-bundle-mult" src="http://wpbooklist.com/wp-content/uploads/2017/08/svgs/server.svg"><img class="wpbooklist-extension-img-bundle-mult" src="http://wpbooklist.com/wp-content/uploads/2017/08/svgs/ereader.svg">
                         </p>
              </div>
           </a>
           <p class="wpbooklist-extension-excerpt"><span class="wpbooklist-excerpt-span">'.__('Want the best of WPBookList as fast as possible? Then get the ','wpbooklist').'<span class="wpbooklist-color-orange-italic">'.__('TimeSaver Bundle!','wpbooklist').'</span></span><span class="wpbooklist-top-line-span"></span></p>
           <div class="wpbooklist-above-purchase-line"></div>
           <p class="wpbooklist-to-download-page"><a href="https://wpbooklist.com/index.php/downloads/timesaver-bundle/">'.__('More Details','wpbooklist').'</a></p>
           <div class="wpbooklist-extensions-purchase-button-link"><a href="https://wpbooklist.com/index.php/downloads/timesaver-bundle/">'.__('$12.00 - Purchase Now','wpbooklist').'</a></div>
  </div>
</div>
<div class="section group">
  <div class="col span_1_of_2">
   <p class="wpbooklist-extension-title"><img class="wpbooklist-extension-icon-img" src="http://www.wpbooklist.com/wp-content/uploads/wpbooklist/icons/server.svg" />'.__('Bulk-Upload Extension','wpbooklist').'</p>
          <a id="wpbooklist-extensions-page-img-link" href="http://wpbooklist.com/index.php/downloads/bulk-upload-extension/">
            <div class="wpbooklist-extension-page-ext-div" id="wpbooklist-extension-page-ext-div-3">
           <img class="wpbooklist-extension-img" src="http://wpbooklist.com/wp-content/uploads/2017/08/svgs/server.svg"  />
           <p class="wpbooklist-extension-p">'.__('Bulk-Upload','wpbooklist').'</p>
           </div>
           </a>
           <p class="wpbooklist-extension-excerpt"><span class="wpbooklist-excerpt-span">'.__('Tons of Books but no Time? Then get the ','wpbooklist').' <span class="wpbooklist-color-orange-italic">'.__('WPBookList Bulk-Upload Extenison!','wpbooklist').'</span></span><span class="wpbooklist-top-line-span"></span></p>
           <div class="wpbooklist-above-purchase-line"></div>
           <p class="wpbooklist-to-download-page"><a href="http://wpbooklist.com/index.php/downloads/bulk-upload-extension/">'.__('More Details','wpbooklist').'</a></p>
           <div class="wpbooklist-extensions-purchase-button-link"><a href="http://wpbooklist.com/index.php/downloads/bulk-upload-extension/">'.__('$5.00 - Purchase Now','wpbooklist').'</a></div>
  </div>
  <div class="col span_1_of_2">
    <p class="wpbooklist-extension-title"><img class="wpbooklist-extension-icon-img" src="http://www.wpbooklist.com/wp-content/uploads/wpbooklist/icons/ereader.svg" />'.__('Mobile App Extension','wpbooklist').'</p>
            <a id="wpbooklist-extensions-page-img-link" href="http://wpbooklist.com/index.php/downloads/mobile-app-extension/">
             <div class="wpbooklist-extension-page-ext-div" id="wpbooklist-extension-page-ext-div-4">
             <img class="wpbooklist-extension-img" src="http://wpbooklist.com/wp-content/uploads/2017/08/svgs/ereader.svg"  />
             <p class="wpbooklist-extension-p">'.__('Mobile App','wpbooklist').'</p>
             </div>
           </a>
           <p class="wpbooklist-extension-excerpt"><span class="wpbooklist-excerpt-span">'.__('Add books quickly and easily with the','wpbooklist').' <span class="wpbooklist-color-orange-italic">'.__('WPBookList Mobile App Extension!','wpbooklist').'</span></span><span class="wpbooklist-top-line-span"></span></p>
           <div class="wpbooklist-above-purchase-line"></div>
           <p class="wpbooklist-to-download-page"><a href="http://wpbooklist.com/index.php/downloads/mobile-app-extension/">'.__('More Details','wpbooklist').'</a></p>
           <div class="wpbooklist-extensions-purchase-button-link"><a href="http://wpbooklist.com/index.php/downloads/mobile-app-extension/">'.__('$5.00 - Purchase Now','wpbooklist').'</a></div>
  </div>
</div>
<div class="section group">
  <div class="col span_1_of_2">
   <p class="wpbooklist-extension-title"><img class="wpbooklist-extension-icon-img" src="http://www.wpbooklist.com/wp-content/uploads/wpbooklist/icons/goodreads.svg" />'.__('Goodreads Extension','wpbooklist').'</p>
            <a id="wpbooklist-extensions-page-img-link" href="http://wpbooklist.com/index.php/downloads/goodreads-extension/">
             <div class="wpbooklist-extension-page-ext-div" id="wpbooklist-extension-page-ext-div-5">
             <img class="wpbooklist-extension-img" src="http://wpbooklist.com/wp-content/uploads/2017/08/svgs/goodreads-letter-logo.svg"  />
             <p class="wpbooklist-extension-p">'.__('Goodreads','wpbooklist').'</p>
             </div>
           </a>
           <p class="wpbooklist-extension-excerpt"><span class="wpbooklist-excerpt-span">'.__('Got a Goodreads account? Then get the ','wpbooklist').' <span class="wpbooklist-color-orange-italic">'.__('WPBookList Goodreads Extension!','wpbooklist').'</span></span><span class="wpbooklist-top-line-span"></span></p>
           <div class="wpbooklist-above-purchase-line"></div>
           <p class="wpbooklist-to-download-page"><a href="http://wpbooklist.com/index.php/downloads/goodreads-extension/">'.__('More Details','wpbooklist').'</a></p>
           <div class="wpbooklist-extensions-purchase-button-link"><a href="http://wpbooklist.com/index.php/downloads/goodreads-extension/">'.__('$5.00 - Purchase Now','wpbooklist').'</a></div>
  </div>
  <div class="col span_1_of_2">
   <p class="wpbooklist-extension-title"><img class="wpbooklist-extension-icon-img" src="https://wpbooklist.com/wp-content/uploads/edd/2017/09/Screenshot-2017-09-17-14.12.39.png" />'.__('Kindle Preview Extension','wpbooklist').'</p>
            <a id="wpbooklist-extensions-page-img-link" href="https://wpbooklist.com/index.php/downloads/kindle-preview-extension/">
             <div class="wpbooklist-extension-page-ext-div" id="wpbooklist-extension-page-ext-div-7">
             <img class="wpbooklist-extension-img" src="http://wpbooklist.com/wp-content/uploads/2017/08/svgs/ereader-with-bookmark.svg"  />
             <p class="wpbooklist-extension-p">'.__('Kindle Preview','wpbooklist').'</p>
             </div>
           </a>
           <p class="wpbooklist-extension-excerpt"><span class="wpbooklist-excerpt-span">'.__('Provides Kindle Book Previews! Also integrates with the ','wpbooklist').' <a href="http://wpbooklist.com/index.php/downloads/affiliate-extension/"><span class="wpbooklist-color-orange-italic">'.__('WPBookList Affiliate Extension!','wpbooklist').'</span></a></span><span class="wpbooklist-top-line-span"></span></p>
           <div class="wpbooklist-above-purchase-line"></div>
           <p class="wpbooklist-to-download-page"><a href="https://wpbooklist.com/index.php/downloads/kindle-preview-extension/">'.__('More Details','wpbooklist').'</a></p>
           <div class="wpbooklist-extensions-purchase-button-link"><a href="https://wpbooklist.com/index.php/downloads/kindle-preview-extension/">'.__('$3.00 - Purchase Now','wpbooklist').'</a></div>
  </div>
</div>
<div class="section group">
  <div class="col span_1_of_2">
   <p class="wpbooklist-extension-title"><img class="wpbooklist-extension-icon-img" src="http://wpbooklist.com/wp-content/uploads/2017/08/svgs/search-white.svg" />'.__('BookFinder Extension','wpbooklist').'</p>
            <a id="wpbooklist-extensions-page-img-link" href="https://wpbooklist.com/index.php/downloads/bookfinder-extension/">
             <div class="wpbooklist-extension-page-ext-div" id="wpbooklist-extension-page-ext-div-9">
             <img class="wpbooklist-extension-img" src="http://wpbooklist.com/wp-content/uploads/2017/08/svgs/search-white.svg"  />
             <p class="wpbooklist-extension-p">'.__('BookFinder','wpbooklist').'</p>
             </div>
           </a>
           <p class="wpbooklist-extension-excerpt"><span class="wpbooklist-excerpt-span">'.__('Increases user engagement! Let your visitors search and add books with the','wpbooklist').' <span class="wpbooklist-color-orange-italic">'.__('WPBookList BookFinder Extension!','wpbooklist').'</span></span><span class="wpbooklist-top-line-span"></span></p>
           <div class="wpbooklist-above-purchase-line"></div>
           <p class="wpbooklist-to-download-page"><a href="https://wpbooklist.com/index.php/bookfinder-demo/">'.__('Try the Demo!','wpbooklist').'</a></p>
           <div class="wpbooklist-extensions-purchase-button-link"><a href="https://wpbooklist.com/index.php/downloads/bookfinder-extension/">'.__('$5.00 - Purchase Now','wpbooklist').'</a></div>
  </div>
  <div class="col span_1_of_2">
   <p class="wpbooklist-extension-title"><img class="wpbooklist-extension-icon-img" src="http://wpbooklist.com/wp-content/uploads/2017/08/svgs/content-white.svg" />'.__('Stylizer Extension','wpbooklist').'</p>
            <a id="wpbooklist-extensions-page-img-link" href="https://wpbooklist.com/index.php/downloads/stylizer-extension/">
             <div class="wpbooklist-extension-page-ext-div" id="wpbooklist-extension-page-ext-div-10">
             <img class="wpbooklist-extension-img" src="http://wpbooklist.com/wp-content/uploads/2017/08/svgs/content-white.svg"  />
             <p class="wpbooklist-extension-p">'.__('Stylizer','wpbooklist').'</p>
             </div>
           </a>
           <p class="wpbooklist-extension-excerpt"><span class="wpbooklist-excerpt-span">'.__('Easily customize the Look & Feel of your','wpbooklist').' <span class="wpbooklist-color-orange-italic">'.__('WPBookList','wpbooklist').'</span> Book and Library Views!</span><span class="wpbooklist-top-line-span"></span></p>
           <div class="wpbooklist-above-purchase-line"></div>
           <p class="wpbooklist-to-download-page"><a href="https://wpbooklist.com/index.php/downloads/stylizer-extension/">'.__('More Details','wpbooklist').'</a></p>
           <div class="wpbooklist-extensions-purchase-button-link"><a href="https://wpbooklist.com/index.php/downloads/stylizer-extension/">'.__('$5.00 - Purchase Now','wpbooklist').'</a></div>
  </div>
  </div>
</div>

        ';

        return $string1;
  }


}

endif;