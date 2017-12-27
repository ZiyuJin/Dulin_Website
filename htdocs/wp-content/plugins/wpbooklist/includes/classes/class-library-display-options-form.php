<?php
/**
 * WPBookList Library Display Options Form Tab Class
 *
 * @author   Jake Evans
 * @category Admin
 * @package  Includes/Classes
 * @version  1
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

if ( ! class_exists( 'WPBookList_Library_Display_Options_Form', false ) ) :
/**
 * WPBookList_Admin_Menu Class.
 */
class WPBookList_Library_Display_Options_Form {

    public static function output_add_edit_form(){
        global $wpdb;
        // Getting all user-created libraries
        $table_name = $wpdb->prefix . 'wpbooklist_jre_list_dynamic_db_names';
        $db_row = $wpdb->get_results("SELECT * FROM $table_name");

        // Getting the settings for the Default library
        $table_name = $wpdb->prefix . 'wpbooklist_jre_user_options';
        $options_row = $wpdb->get_row("SELECT * FROM $table_name");


        # All settings properties
        $enablepurchase = $options_row->enablepurchase;
        $hidesearch = $options_row->hidesearch;
        $hidefacebook = $options_row->hidefacebook;
        $hidetwitter = $options_row->hidetwitter;
        $hidegoogleplus = $options_row->hidegoogleplus;
        $hidemessenger = $options_row->hidemessenger;
        $hidepinterest = $options_row->hidepinterest;
        $hideemail = $options_row->hideemail;
        $hidestats = $options_row->hidestats;
        $hidegoodreadswidget = $options_row->hidegoodreadswidget;
        $hideamazonreview = $options_row->hideamazonreview;
        $hidedescription = $options_row->hidedescription;
        $hidebookimage = $options_row->hidebookimage;
        $hidefinished = $options_row->hidefinished;
        $hidesimilar = $options_row->hidesimilar;
        $hidebooktitle = $options_row->hidebooktitle;
        $hidelibrarytitle = $options_row->hidelibrarytitle;
        $hideauthor = $options_row->hideauthor;
        $hidecategory = $options_row->hidecategory;
        $hidepages = $options_row->hidepages;
        $hidebookpage = $options_row->hidebookpage;
        $hidebookpost = $options_row->hidebookpost;
        $hidefrontendbuyimg = $options_row->hidefrontendbuyimg;
        $hidefrontendbuyprice = $options_row->hidefrontendbuyprice;
        $hidecolorboxbuyimg = $options_row->hidecolorboxbuyimg;
        $hidecolorboxbuyprice = $options_row->hidecolorboxbuyprice;
        $hidepublisher = $options_row->hidepublisher;
        $hidepubdate = $options_row->hidepubdate;
        $hidesigned = $options_row->hidesigned;
        $hidefirstedition = $options_row->hidefirstedition;
        $hidefeaturedtitles = $options_row->hidefeaturedtitles;
        $hidenotes = $options_row->hidenotes;
        $hidesortby = $options_row->hidesortby;
        $hidebottompurchase = $options_row->hidebottompurchase;
        $hidequotebook = $options_row->hidequotebook;
        $hidequote = $options_row->hidequote;
        $hiderating = $options_row->hiderating;
        $hideratingbook = $options_row->hideratingbook;
        $amazoncountryinfo = $options_row->amazoncountryinfo;
        $amazonaff = $options_row->amazonaff;
        $itunesaff = $options_row->itunesaff;
        $hidegooglepurchase = $options_row->hidegooglepurchase;
        $hideamazonpurchase = $options_row->hideamazonpurchase;
        $hidebnpurchase = $options_row->hidebnpurchase;
        $hideitunespurchase = $options_row->hideitunespurchase;
        $hidekobopurchase = $options_row->hidekobopurchase;
        $hidebampurchase = $options_row->hidebampurchase;
        $hidekindle = $options_row->hidekindleprev;
        $sortoption = $options_row->sortoption;
        $booksonpage = $options_row->booksonpage;

        $string1 = '<div id="wpbooklist-custom-libraries-container">
                        <p id="wpbooklist-library-display-p">Select a Library to Apply These Display Options to</p>
                        <select id="wpbooklist-library-settings-select">
                            <option value="'.$wpdb->prefix.'wpbooklist_jre_saved_book_log">Default Library</option>';

        $string2 = '';
        foreach($db_row as $db){
            if(($db->user_table_name != "") || ($db->user_table_name != null)){
                $string2 = $string2.'<option value="'.$wpdb->prefix.'wpbooklist_jre_'.$db->user_table_name.'">'.ucfirst($db->user_table_name).'</option>';
            }
        }

        $string3 = '</select>
    <div class="wpbooklist-spinner" id="wpbooklist-spinner-2"></div>
        <table id="wpbooklist-jre-backend-options-table">
            <tbody>
              <tr>
                <td><label>Hide the Search area</label></td>
                <td class="wpbooklist-margin-right-td"><input type="checkbox" name="hide-search"';

                $string4 = '';
                        if($hidesearch != null && $hidesearch != 0){
                            $string4 = esc_attr('checked="checked"');
                        }

                $string5 = '></input></td>
                <td><label>Hide the Statistics Area</label></td>
                <td class="wpbooklist-margin-right-td"><input type="checkbox" name="hide-stats"';

                $string6 = '';
                if($hidestats != null && $hidestats != 0){
                    $string6 = esc_attr('checked="checked"');
                } 

               $string7 = '></input></td>
              </tr>
              <tr>
                <td><label>Hide the \'Sort By...\' drop-down box</label></td>
                <td class="wpbooklist-margin-right-td"><input type="checkbox" name="hide-sort-by"';

                $string8 = '';
                if($hidesortby != null && $hidesortby != 0){
                    $string8 = esc_attr('checked="checked"');
                }

               $string9 = '></input></td>
            <td><label>Hide the Facebook Share Button</label></td>
            <td class="wpbooklist-margin-right-td"><input type="checkbox" name="hide-facebook"';

            $string10 = '';
            if($hidefacebook != null && $hidefacebook != 0){
                $string10 = esc_attr('checked="checked"');
            }

            $string11 = '></input></td>
              </tr>
              <tr>
             <td><label>Hide the Facebook Messenger Button</label></td>
            <td class="wpbooklist-margin-right-td"><input type="checkbox" name="hide-messenger"';

            $string12 = '';
            if($hidemessenger != null && $hidemessenger != 0){
                $string12 = esc_attr('checked="checked"');
            }

            $string13 ='></input></td>
            <td><label>Hide the Google+ Share Button</label></td>
            <td class="wpbooklist-margin-right-td"><input type="checkbox" name="hide-googleplus"';

            $string14 = '';
            if($hidegoogleplus != null && $hidegoogleplus != 0){
                $string14 = esc_attr('checked="checked"');
            }

            $string15 = '></input></td>
            </tr>
              <tr>
            <td><label>Hide the Pinterest Share Button</label></td>
            <td class="wpbooklist-margin-right-td"><input type="checkbox" name="hide-pinterest"';

            $string16 = '';
            if($hidepinterest != null && $hidepinterest != 0){
                $string16 = esc_attr('checked="checked"');
            }

            $string17 = '></input></td>
            <td><label>Hide the Email Share Button</label></td>
            <td class="wpbooklist-margin-right-td"><input type="checkbox" name="hide-email"';

            $string18 = '';
            if($hideemail != null && $hideemail != 0){
                $string18 = esc_attr('checked="checked"');
            }

            $string19 = '></input></td>
            </tr>
              <tr>
            <td><label>Hide the GoodReads Widget</label></td>
            <td class="wpbooklist-margin-right-td"><input type="checkbox" name="hide-goodreads"';

            $string20 = '';
            if($hidegoodreadswidget != null && $hidegoodreadswidget != 0){
                $string20 = esc_attr('checked="checked"');
            }

            $string21 = '></input></td>
            <td><label>Hide the Twitter Share Button</label></td>
            <td class="wpbooklist-margin-right-td"><input type="checkbox" name="hide-twitter"';

            $string22 = '';
            if($hidetwitter != null && $hidetwitter != 0){
                $string22 = esc_attr('checked="checked"');
            }

            $string23 = '></input></td>
            </tr>
              <tr>
            <td><label>Hide the Amazon Reviews</label></td>
            <td class="wpbooklist-margin-right-td"><input type="checkbox" name="hide-amazon-review"';

            $string24 = '';
            if($hideamazonreview != null && $hideamazonreview != 0){
                $string24 = esc_attr('checked="checked"');
            }

            $string25 = '></input></td>
            <td><label>Hide the Book Notes</label></td>
            <td class="wpbooklist-margin-right-td"><input type="checkbox" name="hide-notes"';

            $string26 = '';
            if($hidenotes != null && $hidenotes != 0){
                $string26 = esc_attr('checked="checked"');
            }

            $string27 = '></input></td>
            </tr>
              <tr>
            <td><label>Hide the Book Description</label></td>
            <td class="wpbooklist-margin-right-td"><input type="checkbox" name="hide-description"';

            $string28 = '';
            if($hidedescription != null && $hidedescription != 0){
                $string28 = esc_attr('checked="checked"');
            }

            $string29 = '></input></td>
            <td><label>Hide the Quote Area (book view)</label></td>
            <td class="wpbooklist-margin-right-td"><input type="checkbox" name="hide-quote-book"';

            $string30 = '';
            if($hidequotebook != null && $hidequotebook != 0){
                $string30 = esc_attr('checked="checked"');
            }

            $string31 = '></input></td>
            </tr>
              <tr>
            <td><label>Hide the Quote Area (library view) </label></td>
            <td class="wpbooklist-margin-right-td"><input type="checkbox" name="hide-quote"';

            $string32 = '';
            if($hidequote != null && $hidequote != 0){
                $string32 = esc_attr('checked="checked"');
            }

            $string33 = '></input></td>
            <td><label>Hide the Review Stars (book view)</label></td>
            <td class="wpbooklist-margin-right-td"><input type="checkbox" name="hide-rating-book"';

            $string34 = '';
            if($hideratingbook != null && $hideratingbook != 0){
                $string34 = esc_attr('checked="checked"');
            }

            $string35 = '></input></td>
            </tr>
            <tr>
            <td><label>Hide the Review Stars (library view)</label></td>
            <td class="wpbooklist-margin-right-td"><input type="checkbox" name="hide-rating"';

            $string36 = '';
            if($hiderating != null && $hiderating != 0){
                $string36 = esc_attr('checked="checked"');
            }

            $string37 = '></input></td>
            <td><label>Hide the Book Title (library view)</label></td>
            <td class="wpbooklist-margin-right-td"><input type="checkbox" name="hide-library-title"';

            $string38 = '';
            if($hidelibrarytitle != null && $hidelibrarytitle != 0){
                $string38 = esc_attr('checked="checked"');
            }

            $string39 = '></input></td>
            </tr>
             <tr>
            <td><label>Hide the Book Title (book view)</label></td>
            <td class="wpbooklist-margin-right-td"><input type="checkbox" name="hide-book-title"';

            $string40 = '';
            if($hidebooktitle != null && $hidebooktitle != 0){
              $string40 = esc_attr('checked="checked"');
            }

            $string41 = '></input></td>
            <td><label>Hide the Author</label></td>
            <td class="wpbooklist-margin-right-td"><input type="checkbox" name="hide-author"';

            $string42 = '';
            if($hideauthor != null && $hideauthor != 0){
              $string42 = esc_attr('checked="checked"');
            }

            $string43 = '></input></td>
            </tr>
             <tr>
            <td><label>Hide the Book Cover Image (book view)</label></td>
            <td class="wpbooklist-margin-right-td"><input type="checkbox" name="hide-book-image"';

            $string44 = '';
            if($hidebookimage != null && $hidebookimage != 0){
              $string44 = esc_attr('checked="checked"');
            }

            $string45 = '></input></td>
            <td><label>Hide Book Finished</label></td>
            <td class="wpbooklist-margin-right-td"><input type="checkbox" name="hide-finished"';

            $string46 = '';
            if($hidefinished != null && $hidefinished != 0){
              $string46 = esc_attr('checked="checked"');
            }

            $string47 = '></input></td>
            </tr>
             <tr>
            <td><label>Hide the Category</label></td>
            <td class="wpbooklist-margin-right-td"><input type="checkbox" name="hide-category"';

            $string48 = '';
            if($hidecategory != null && $hidecategory != 0){
              $string48 = esc_attr('checked="checked"');
            }

            $string49 = '></input></td>
            <td><label>Hide the Pages</label></td>
            <td class="wpbooklist-margin-right-td"><input type="checkbox" name="hide-pages"';

            $string50 = '';
            if($hidepages != null && $hidepages != 0){
              $string50 = esc_attr('checked="checked"');
            }

            $string51 = '></input></td>
            </tr>
             <tr>
            <td><label>Hide the Publisher</label></td>
            <td class="wpbooklist-margin-right-td"><input type="checkbox" name="hide-publisher"';

            $string52 = '';
            if($hidepublisher != null && $hidepublisher != 0){
              $string52 = esc_attr('checked="checked"');
            }

            $string53 = '></input></td>
            <td><label>Hide the Publication Date</label></td>
            <td class="wpbooklist-margin-right-td"><input type="checkbox" name="hide-pub-date"';

            $string54 = '';
            if($hidepubdate != null && $hidepubdate != 0){
              $string54 = esc_attr('checked="checked"');
            }

            $string55 = '></input></td>
            </tr>
             <tr>
            <td><label>Hide Signed</label></td>
            <td class="wpbooklist-margin-right-td"><input type="checkbox" name="hide-signed"';

            $string56 = '';
            if($hidesigned != null && $hidesigned != 0){
              $string56 = esc_attr('checked="checked"');
            }

            $string57 = '></input></td>
            <td><label>Hide First Edition</label></td>
            <td class="wpbooklist-margin-right-td"><input type="checkbox" name="hide-first-edition"';

            $string58 = '';
            if($hidefirstedition != null && $hidefirstedition != 0){
              $string58 = esc_attr('checked="checked"');
            }

            $string59 = '></input></td>
            </tr>
              <tr>
            <td><label>Hide the Similar Titles Section</label></td>
            <td class="wpbooklist-margin-right-td"><input type="checkbox" name="hide-similar"';

            $string60 = '';
            if($hidesimilar != null && $hidesimilar != 0){
                $string60 = esc_attr('checked="checked"');
            }

            $string61 = '></input></td>
            <td><label>Hide the Google Purchase Link</label></td>
            <td class="wpbooklist-margin-right-td"><input type="checkbox" name="hide-google-purchase"';

            $string62 = '';
            if($hidegooglepurchase != null && $hidegooglepurchase != 0){
                $string62 = esc_attr('checked="checked"');
            }

            $string63 = '></input></td>
            </tr>
              <tr>
            <td><label>Hide the iTunes Purchase Link</label></td>
            <td class="wpbooklist-margin-right-td"><input type="checkbox" name="hide-itunes-purchase"';

            $string64 = '';
            if($hideitunespurchase != null && $hideitunespurchase != 0){
                $string64 = esc_attr('checked="checked"');
            }

            $string65 = '></input></td>
            <td><label>Hide the B&N Purchase Link</label></td>
            <td class="wpbooklist-margin-right-td"><input type="checkbox" name="hide-bn-purchase"';

            $string66 = '';
            if($hidebnpurchase != null && $hidebnpurchase != 0){
                $string66 = esc_attr('checked="checked"');
            }

            $string67 = '></input></td>
            </tr>
            <tr>';

            $string100 = '<td><label>Hide the Kobo Purchase Link</label></td>
            <td class="wpbooklist-margin-right-td"><input type="checkbox" name="hide-kobo-purchase"';
            if($hidekobopurchase != null && $hidekobopurchase != 0){
                $string100 = $string100.esc_attr('checked="checked"');
            }

            $string101 = '></input></td>';

            $string102 = '<td><label>Hide Books-A-Million Purchase Link</label></td>
            <td class="wpbooklist-margin-right-td"><input type="checkbox" name="hide-bam-purchase"';
            if($hidebampurchase != null && $hidebampurchase != 0){
                $string102 = $string102.esc_attr('checked="checked"');
            }

            $string103 = '></input></td>
            </tr>
            <tr>';


            $string68 = '<td><label>Hide the Amazon Purchase Link</label></td>
            <td class="wpbooklist-margin-right-td"><input type="checkbox" name="hide-amazon-purchase"';

            if($hideamazonpurchase != null && $hideamazonpurchase != 0){
                $string68 = $string68.esc_attr('checked="checked"');
            }

            $string69 = '></input></td>
            <td><label>Hide the Featured Titles Section</label></td>
            <td class="wpbooklist-margin-right-td"><input type="checkbox" name="hide-featured-titles"';

            $string70 = '';
            if($hidefeaturedtitles != null && $hidefeaturedtitles != 0){
                $string70 = esc_attr('checked="checked"');
            }

            $string71 = '></input></td>
            </tr>
            <tr>
            <td><label>Hide the Book Page Link</label></td>
            <td class="wpbooklist-margin-right-td"><input type="checkbox" name="hide-book-page"';

            $string72 = '';
            if($hidebookpage != null && $hidebookpage != 0){
              $string72 = esc_attr('checked="checked"');
            }

            $string73 = '></input></td>
            <td><label>Hide the Book Post Link</label></td>
            <td class="wpbooklist-margin-right-td"><input type="checkbox" name="hide-book-post"';

            $string74 = '';
            if($hidebookpost != null && $hidebookpost != 0){
              $string74 = esc_attr('checked="checked"');
            }

            $string75 = '';

            $string76 = '';

            $string77 = '></input></td>
            </tr>';

            $hide_array = array($hidefrontendbuyimg,$hidefrontendbuyprice,$hidecolorboxbuyimg,$hidecolorboxbuyprice);

            if(has_filter('wpbooklist_add_to_library_display_options')) {
                $string77 = $string77.apply_filters('wpbooklist_add_to_library_display_options', $hide_array);
            }

            if(has_filter('wpbooklist_add_to_library_display_options_kindle')) {
                $string77 = $string77.apply_filters('wpbooklist_add_to_library_display_options_kindle', $hidekindle);
            }

            $string78 = '</tbody></table>';

            $string79 = '<div id="wpbooklist-display-opt-check-div">
                            <label>Check All</label>
                            <input id="wpbooklist-check-all" type="checkbox" name="check-all"/>
                            <label>Uncheck All</label>
                            <input id="wpbooklist-uncheck-all" type="checkbox" name="uncheck-all"/>
                        </div>';

            $string80 ='<table id="wpbooklist-library-options-lower-table"><tbody><tr>';

            if(has_filter('wpbooklist_append_to_display_options_library_enable_purchase')) {
                $string80 = apply_filters('wpbooklist_append_to_display_options_library_enable_purchase', $string80);
            }

              $string83 = '</tr><tr>
              <td class="wpbooklist-display-bottom-4"><label>Set Default Sorting</label></td>
              <td class="wpbooklist-display-bottom-4">
                <select name="sort-value" id="wpbooklist-jre-sorting-select"><option ';

                  $string84 = '';
                  if ($sortoption == 'default'){ 
                    $string84 = 'selected="selected"'; 
                  }   

                  $string85 = 'value="default">Default</option>
                  <option ';

                  $string86 = '';
                  if ($sortoption == 'alphabetically'){
                   $string86 = 'selected="selected"'; 
                  }   

                  $string87 = 'value="alphabetically">Alphabetically</option>
                  <option ';

                  $string88 = '';
                  if ($sortoption == 'year_read'){
                   $string88 = 'selected="selected"'; 
                  }

                  $string89 = 'value="year_read">Year Finished</option>
                  <option ';

                  $string90 = '';
                  if ($sortoption == 'pages_desc'){
                   $string90 = 'selected="selected"'; 
                  }   

                  $string91 = 'value="pages_desc">Pages (Descending)</option>
                  <option ';

                  $string92 = '';
                  if ($sortoption == 'pages_asc'){
                   $string92 = 'selected="selected"'; 
                  }   

                  $string93 = 'value="pages_asc">Pages (Ascending)</option>
                  <option '; 

                  $string94 = '';
                  if ($sortoption == 'signed'){
                    $string94 = 'selected="selected"'; 
                  }

                  $string95 = 'value="signed">Signed</option>
                  <option ';

                  $string96 = '';
                  if ($sortoption == 'first_edition'){
                   $string96 = 'selected="selected"'; 
                  }

                  $string97 = 'value="first_edition">First Edition</option>
                </select><br/>
              </td>
            </tr>';


            $string98 = '<tr>
                <td class="wpbooklist-display-bottom-4"><label>Set Books Per Page</label></td>
                <td class="wpbooklist-display-bottom-4"><input class="wpbooklist-dynamic-input" id="wpbooklist-book-control" type="text" name="books-per-page" value="'.esc_attr($booksonpage).'"></input></td>
            </tr></tbody></table>';

            $string99 = '<button id="wpbooklist-save-backend" name="save-backend" type="button">Save Changes</button></div>';


        echo $string1.$string2.$string3.$string4.$string5.$string6.$string7.$string8.$string9.$string10.$string11.$string12.$string13.$string14.$string15.$string16.$string17.$string18.$string19.$string20.$string21.$string22.$string23.$string24.$string25.$string26.$string27.$string28.$string29.$string30.$string31.$string32.$string33.$string34.$string35.$string36.$string37.$string38.$string39.$string40.$string41.$string42.$string43.$string44.$string45.$string46.$string47.$string48.$string49.$string50.$string51.$string52.$string53.$string54.$string55.$string56.$string57.$string58.$string59.$string60.$string61.$string62.$string63.$string64.$string65.$string66.$string67.$string100.$string101.$string102.$string103.$string68.$string69.$string70.$string71.$string72.$string73.$string74.$string75.$string76.$string77.$string78.$string79.$string80.$string83.$string84.$string85.$string86.$string87.$string88.$string89.$string90.$string91.$string92.$string93.$string94.$string95.$string96.$string97.$string98.$string99;
        
    }


}

endif;