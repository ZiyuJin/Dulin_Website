<?php
/**
 * WPBookList Posts Display Options Form Tab Class
 *
 * @author   Jake Evans
 * @category Admin
 * @package  Includes/Classes
 * @version  1
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

if ( ! class_exists( 'WPBookList_Posts_Display_Options_Form', false ) ) :
/**
 * WPBookList_Admin_Menu Class.
 */
class WPBookList_Posts_Display_Options_Form {

    public static function output_add_edit_form(){
        global $wpdb;

        // Getting the settings for posts
        $table_name = $wpdb->prefix . 'wpbooklist_jre_post_options';
        $options_row = $wpdb->get_row("SELECT * FROM $table_name");

        // Getting the settings for the Default library
        $table_name = $wpdb->prefix . 'wpbooklist_jre_user_options';
        $default_options_row = $wpdb->get_row("SELECT * FROM $table_name");


        # All settings properties
        $enablepurchase = $options_row->enablepurchase;
        $hidefacebook = $options_row->hidefacebook;
        $hidetwitter = $options_row->hidetwitter;
        $hidegoogleplus = $options_row->hidegoogleplus;
        $hidemessenger = $options_row->hidemessenger;
        $hidepinterest = $options_row->hidepinterest;
        $hideemail = $options_row->hideemail;
        $hidefrontendbuyimg = $options_row->hidefrontendbuyimg;
        $hidefrontendbuyprice = $options_row->hidefrontendbuyprice;
        $hidecolorboxbuyimg = $options_row->hidecolorboxbuyimg;
        $hidecolorboxbuyprice = $options_row->hidecolorboxbuyprice;
        $hideamazonreview = $options_row->hideamazonreview;
        $hidedescription = $options_row->hidedescription;
        $hidebookimage = $options_row->hidebookimage;
        $hidefinished = $options_row->hidefinished;
        $hidesimilar = $options_row->hidesimilar;
        $hidebooktitle = $options_row->hidetitle;
        $hideauthor = $options_row->hideauthor;
        $hidecategory = $options_row->hidecategory;
        $hidepages = $options_row->hidepages;
        $hidepublisher = $options_row->hidepublisher;
        $hidepubdate = $options_row->hidepubdate;
        $hidesigned = $options_row->hidesigned;
        $hidefirstedition = $options_row->hidefirstedition;
        $hidefeaturedtitles = $options_row->hidefeaturedtitles;
        $hidenotes = $options_row->hidenotes;
        $hidequote = $options_row->hidequote;
        $hiderating = $options_row->hiderating;
        $amazoncountryinfo = $options_row->amazoncountryinfo;
        $amazonaff = $default_options_row->amazonaff;
        $itunesaff = $default_options_row->itunesaff;
        $hidegooglepurchase = $options_row->hidegooglepurchase;
        $hideamazonpurchase = $options_row->hideamazonpurchase;
        $hidebnpurchase = $options_row->hidebnpurchase;
        $hidebampurchase = $options_row->hidebampurchase;
        $hidekobopurchase = $options_row->hidekobopurchase;
        $hideitunespurchase = $options_row->hideitunespurchase;
        $hidekindle = $options_row->hidekindleprev;


        $string1 = '<div id="wpbooklist-custom-libraries-container">
    <div class="wpbooklist-spinner" id="wpbooklist-spinner-2"></div>
        <table id="wpbooklist-jre-backend-options-table">
            <tbody>
              <tr>
                <td><label>Hide the Amazon Purchase Link</label></td>
                <td class="wpbooklist-margin-right-td"><input type="checkbox" name="hide-amazon-purchase"';

                $string2 = '';
                if($hideamazonpurchase != null && $hideamazonpurchase != 0){
                    $string2 = esc_attr('checked="checked"');
                }

                $string3 = '></input></td>
                <td><label>Hide the iTunes Purchase Link</label></td>
                <td class="wpbooklist-margin-right-td"><input type="checkbox" name="hide-itunes-purchase"';

                $string4 = '';
                if($hideitunespurchase != null && $hideitunespurchase != 0){
                    $string4 = esc_attr('checked="checked"');
                } 

               $string5 = '></input></td>
              </tr>
              <tr>';

              $string63 = '<td><label>Hide the Kobo Purchase Link</label></td>
              <td class="wpbooklist-margin-right-td"><input type="checkbox" name="hide-kobo-purchase"';
              if($hidekobopurchase != null && $hidekobopurchase != 0){
                $string63 = $string63.esc_attr('checked="checked"');
              }

              $string64 = '></input></td>';
  
              $string65 = '<td><label>Hide Books-A-Million Purchase Link</label></td>
              <td class="wpbooklist-margin-right-td"><input type="checkbox" name="hide-bam-purchase"';
              if($hidebampurchase != null && $hidebampurchase != 0){
                $string65 = $string65.esc_attr('checked="checked"');
              }

              $string66 = '></input></td>
              </tr>
              <tr>';

              $string6 = '<td><label>Hide the B&N Purchase Link</label></td>
                <td class="wpbooklist-margin-right-td"><input type="checkbox" name="hide-bn-purchase"';
                if($hidebnpurchase != null && $hidebnpurchase != 0){
                    $string6 = $string6.esc_attr('checked="checked"');
                }

               $string7 = '></input></td>
            <td><label>Hide the Facebook Share Button</label></td>
            <td class="wpbooklist-margin-right-td"><input type="checkbox" name="hide-facebook"';

            $string8 = '';
            if($hidefacebook != null && $hidefacebook != 0){
                $string8 = esc_attr('checked="checked"');
            }

            $string9 = '></input></td>
              </tr>
              <tr>
             <td><label>Hide the Facebook Messenger Button</label></td>
            <td class="wpbooklist-margin-right-td"><input type="checkbox" name="hide-messenger"';

            $string10 = '';
            if($hidemessenger != null && $hidemessenger != 0){
                $string10 = esc_attr('checked="checked"');
            }

            $string11 ='></input></td>
            <td><label>Hide the Google+ Share Button</label></td>
            <td class="wpbooklist-margin-right-td"><input type="checkbox" name="hide-googleplus"';

            $string12 = '';
            if($hidegoogleplus != null && $hidegoogleplus != 0){
                $string12 = esc_attr('checked="checked"');
            }

            $string13 = '></input></td>
            </tr>
              <tr>
            <td><label>Hide the Pinterest Share Button</label></td>
            <td class="wpbooklist-margin-right-td"><input type="checkbox" name="hide-pinterest"';

            $string14 = '';
            if($hidepinterest != null && $hidepinterest != 0){
                $string14 = esc_attr('checked="checked"');
            }

            $string15 = '></input></td>
            <td><label>Hide the Email Share Button</label></td>
            <td class="wpbooklist-margin-right-td"><input type="checkbox" name="hide-email"';

            $string16 = '';
            if($hideemail != null && $hideemail != 0){
                $string16 = esc_attr('checked="checked"');
            }

            $string17 = '></input></td>
            </tr>
              <tr>
            <td><label>Hide the Featured Titles Section</label></td>
            <td class="wpbooklist-margin-right-td"><input type="checkbox" name="hide-featured-titles"';

            $string18 = '';
            if($hidefeaturedtitles != null && $hidefeaturedtitles != 0){
                $string18 = esc_attr('checked="checked"');
            }

            $string19 = '></input></td>
            <td><label>Hide the Twitter Share Button</label></td>
            <td class="wpbooklist-margin-right-td"><input type="checkbox" name="hide-twitter"';

            $string20 = '';
            if($hidetwitter != null && $hidetwitter != 0){
                $string20 = esc_attr('checked="checked"');
            }

            $string21 = '></input></td>
            </tr>
              <tr>
            <td><label>Hide the Amazon Reviews</label></td>
            <td class="wpbooklist-margin-right-td"><input type="checkbox" name="hide-amazon-review"';

            $string22 = '';
            if($hideamazonreview != null && $hideamazonreview != 0){
                $string22 = esc_attr('checked="checked"');
            }

            $string23 = '></input></td>
            <td><label>Hide the Book Notes</label></td>
            <td class="wpbooklist-margin-right-td"><input type="checkbox" name="hide-notes"';

            $string24 = '';
            if($hidenotes != null && $hidenotes != 0){
                $string24 = esc_attr('checked="checked"');
            }

            $string25 = '></input></td>
            </tr>
              <tr>
            <td><label>Hide the Book Description</label></td>
            <td class="wpbooklist-margin-right-td"><input type="checkbox" name="hide-description"';

            $string26 = '';
            if($hidedescription != null && $hidedescription != 0){
                $string26 = esc_attr('checked="checked"');
            }

            $string27 = '></input></td>
            <td><label>Hide the Google Purchase Link</label></td>
            <td class="wpbooklist-margin-right-td"><input type="checkbox" name="hide-google-purchase"';

            $string28 = '';
            if($hidegooglepurchase != null && $hidegooglepurchase != 0){
                $string28 = esc_attr('checked="checked"');
            }

            $string29 = '></input></td>
            </tr>
              <tr>
            <td><label>Hide the Quote Area</label></td>
            <td class="wpbooklist-margin-right-td"><input type="checkbox" name="hide-quote"';

            $string30 = '';
            if($hidequote != null && $hidequote != 0){
                $string30 = esc_attr('checked="checked"');
            }

            $string31 = '></input></td>
            <td><label>Hide the Review Stars</label></td>
            <td class="wpbooklist-margin-right-td"><input type="checkbox" name="hide-rating"';

            $string32 = '';
            if($hiderating != null && $hiderating != 0){
                $string32 = esc_attr('checked="checked"');
            }

            $string33 = '></input></td>
            </tr>
            <tr>
            <td><label>Hide First Edition</label></td>
            <td class="wpbooklist-margin-right-td"><input type="checkbox" name="hide-first-edition"';

            $string34 = '';
            if($hidefirstedition != null && $hidefirstedition != 0){
                $string34 = esc_attr('checked="checked"');
            }

            $string35 = '></input></td>
            <td><label>Hide the Book Title</label></td>
            <td class="wpbooklist-margin-right-td"><input type="checkbox" name="hide-book-title"';

            $string36 = '';
            if($hidebooktitle != null && $hidebooktitle != 0){
                $string36 = esc_attr('checked="checked"');
            }

            $string37 = '></input></td>
            </tr>
             <tr>
            <td><label>Hide the Similar Titles Section</label></td>
            <td class="wpbooklist-margin-right-td"><input type="checkbox" name="hide-similar"';

            $string38 = '';
            if($hidesimilar != null && $hidesimilar != 0){
              $string38 = esc_attr('checked="checked"');
            }

            $string39 = '></input></td>
            <td><label>Hide the Author</label></td>
            <td class="wpbooklist-margin-right-td"><input type="checkbox" name="hide-author"';

            $string40 = '';
            if($hideauthor != null && $hideauthor != 0){
              $string40 = esc_attr('checked="checked"');
            }

            $string41 = '></input></td>
            </tr>
             <tr>
            <td><label>Hide the Book Cover Image</label></td>
            <td class="wpbooklist-margin-right-td"><input type="checkbox" name="hide-book-image"';

            $string42 = '';
            if($hidebookimage != null && $hidebookimage != 0){
              $string42 = esc_attr('checked="checked"');
            }

            $string43 = '></input></td>
            <td><label>Hide Book Finished</label></td>
            <td class="wpbooklist-margin-right-td"><input type="checkbox" name="hide-finished"';

            $string44 = '';
            if($hidefinished != null && $hidefinished != 0){
              $string44 = esc_attr('checked="checked"');
            }

            $string45 = '></input></td>
            </tr>
             <tr>
            <td><label>Hide the Category</label></td>
            <td class="wpbooklist-margin-right-td"><input type="checkbox" name="hide-category"';

            $string46 = '';
            if($hidecategory != null && $hidecategory != 0){
              $string46 = esc_attr('checked="checked"');
            }

            $string47 = '></input></td>
            <td><label>Hide the Pages</label></td>
            <td class="wpbooklist-margin-right-td"><input type="checkbox" name="hide-pages"';

            $string48 = '';
            if($hidepages != null && $hidepages != 0){
              $string48 = esc_attr('checked="checked"');
            }

            $string49 = '></input></td>
            </tr>
             <tr>
            <td><label>Hide the Publisher</label></td>
            <td class="wpbooklist-margin-right-td"><input type="checkbox" name="hide-publisher"';

            $string50 = '';
            if($hidepublisher != null && $hidepublisher != 0){
              $string50 = esc_attr('checked="checked"');
            }

            $string51 = '></input></td>
            <td><label>Hide the Publication Date</label></td>
            <td class="wpbooklist-margin-right-td"><input type="checkbox" name="hide-pub-date"';

            $string52 = '';
            if($hidepubdate != null && $hidepubdate != 0){
              $string52 = esc_attr('checked="checked"');
            }

            $string53 = '></input></td>
            </tr>
             <tr>
            <td><label>Hide Signed</label></td>
            <td class="wpbooklist-margin-right-td"><input type="checkbox" name="hide-signed"';

            $string54 = '';
            if($hidesigned != null && $hidesigned != 0){
              $string54 = esc_attr('checked="checked"');
            }

            $string55 = '';

            $string56 = '';

            $string57 = '></input></td>';

            $hide_array = array($hidefrontendbuyimg,$hidefrontendbuyprice,$hidecolorboxbuyimg,$hidecolorboxbuyprice);

            if(has_filter('wpbooklist_add_to_post_display_options')) {
                $string57 = $string57.apply_filters('wpbooklist_add_to_post_display_options', $hide_array);
            }

            if(has_filter('wpbooklist_add_to_library_display_options_post_kindle')) {
                $string57 = $string57.apply_filters('wpbooklist_add_to_library_display_options_post_kindle', $hidekindle);
            }
            
            $string58 = '';

            $string59 = '</tbody></table>';

            $string60 = '<div id="wpbooklist-display-opt-check-div">
                            <label>Check All</label>
                            <input id="wpbooklist-check-all" type="checkbox" name="check-all"/>
                            <label>Uncheck All</label>
                            <input id="wpbooklist-uncheck-all" type="checkbox" name="uncheck-all"/>
                        </div>';

            $string61 = '';
            if(has_filter('wpbooklist_append_to_display_options_post_enable_purchase')) {
                $string61 = apply_filters('wpbooklist_append_to_display_options_post_enable_purchase', $string61);
            }
        

        $string62 = '<button id="wpbooklist-save-post-backend" name="save-backend" type="button">Save Changes</button></div>';


        echo $string1.$string2.$string3.$string4.$string5.$string63.$string64.$string65.$string66.$string6.$string7.$string8.$string9.$string10.$string11.$string12.$string13.$string14.$string15.$string16.$string17.$string18.$string19.$string20.$string21.$string22.$string23.$string24.$string25.$string26.$string27.$string28.$string29.$string30.$string31.$string32.$string33.$string34.$string35.$string36.$string37.$string38.$string39.$string40.$string41.$string42.$string43.$string44.$string45.$string46.$string47.$string48.$string49.$string50.$string51.$string52.$string53.$string54.$string55.$string56.$string57.$string58.$string59.$string60.$string61.$string62;
        
    }


}

endif;