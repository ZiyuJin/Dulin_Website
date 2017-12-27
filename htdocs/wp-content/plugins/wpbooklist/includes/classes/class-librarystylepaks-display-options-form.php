<?php
/**
 * WPBookList LibraryStylePaks Display Options Form Tab Class
 *
 * @author   Jake Evans
 * @category Admin
 * @package  Includes/Classes
 * @version  1
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

if ( ! class_exists( 'WPBookList_LibraryStylePaks_Display_Options_Form', false ) ) :
/**
 * WPBookList_Admin_Menu Class.
 **/
class WPBookList_LibraryStylePaks_Display_Options_Form {

    public static function output_add_edit_form(){
        global $wpdb;

        $table_name = $wpdb->prefix . 'wpbooklist_jre_list_dynamic_db_names';
        $db_row = $wpdb->get_results("SELECT * FROM $table_name");

        $table_name = $wpdb->prefix . 'wpbooklist_jre_user_options';
        $default = $wpdb->get_row("SELECT * FROM $table_name");

        if($default->stylepak == null || $default->stylepak == 'Default StylePak'){
            $default->stylepak = 'Default StylePak';
        }

        $string_table = '<div id="wpbooklist-stylepak-table-container">
                            <table>
                                <tr id="wpbooklist-stylepak-heading-row">
                                    <th>
                                        <img class="wpbooklist-stylepak-heading-img" src="'.ROOT_IMG_ICONS_URL.'library-options.svg"><div id="wpbooklist-stylepak-heading-left" class="wpbooklist-stylepak-table-heading">'.__('Library Name','wpbooklist').'</div>
                                    </th>
                                    <th>
                                        <img class="wpbooklist-stylepak-heading-img" src="'.ROOT_IMG_ICONS_URL.'librarystylepak.svg"><div class="wpbooklist-stylepak-table-heading">'.__('Active Library StylePak','wpbooklist').'</div>
                                    </th>
                                </tr>
                                <tr>
                                    <td class="wpbooklist-stylepaks-col1">
                                        <div class="wpbooklist-stylepak-table-lib"><span class="wpbooklist-stylepak-table-num">#1:</span>'.__('Default Library','wpbooklist').'</div>
                                    </td>
                                    <td>
                                        <div class="wpbooklist-stylepak-table-stylepak">'.ucfirst($default->stylepak).'</div>
                                    </td>
                                </tr>';

        foreach($db_row as $key=>$db){

            if($db->stylepak == null){
                $db->stylepak = ''.__('Default Library StylePak','wpbooklist').'';
            }

            $string_table = $string_table.'<tr>
                                            <td class="wpbooklist-stylepaks-col1">
                                                <div class="wpbooklist-stylepak-table-lib"><span class="wpbooklist-stylepak-table-num">#'.($key+2).':</SPAN> '.ucfirst($db->user_table_name).' '.__('Library','wpbooklist').'</div>
                                            </td>
                                            <td>
                                                <div class="wpbooklist-stylepak-table-stylepak">'.ucfirst($db->stylepak).'</div>
                                            </td>
                                        </tr>';
        }

        $string_table = $string_table.'</table></div>';



        $string1 = '<p>What\'s a <span class="wpbooklist-color-orange-italic">'.__('Library StylePak','wpbooklist').'</span> you ask? <span class="wpbooklist-color-orange-italic">'.__('Library StylePaks','wpbooklist').'</span> '.__('are the best way to instantly change the look and feel of your','wpbooklist').' <span class="wpbooklist-color-orange-italic">WPBookList</span> '.__('Libraries!','wpbooklist').'</p><p>'.__('Simply','wpbooklist').' <a href="http://wpbooklist.com/index.php/stylepaks-2/">'.__('Purchase a $2 Library StylePak here','wpbooklist').'</a>, '.__('upload it using the','wpbooklist').' <span class="wpbooklist-color-orange-italic">\''.__('Upload a New Library StylePak','wpbooklist').'\'</span> '.__('button below, and assign your new Library StylePak to a Library - it\'s that simple!','wpbooklist').'</p>

            <div id="wpbooklist-stylepak-demo-links">
                <a href="http://wpbooklist.com/index.php/downloads/library-stylepak-1/">'.__('Library StylePak1 Demo','wpbooklist').'</a>
                <a href="http://wpbooklist.com/index.php/downloads/library-stylepak-2/">'.__('Library StylePak2 Demo','wpbooklist').'</a>
                <a href="http://wpbooklist.com/index.php/downloads/library-stylepak-3/">'.__('Library StylePak3 Demo','wpbooklist').'</a>
                <a href="http://wpbooklist.com/index.php/downloads/library-stylepak-4/">'.__('Library StylePak4 Demo','wpbooklist').'</a>
                <a href="http://wpbooklist.com/index.php/downloads/library-stylepak-5/">'.__('Library StylePak5 Demo','wpbooklist').'</a>
            </div>

            <div id="wpbooklist-buy-library-stylepaks-div">
                <a id="wpbooklist-stylepak-buy-link" href="http://wpbooklist.com/index.php/stylepaks-2/"><img src="'.ROOT_IMG_URL.'getstylepaks.png" /></a>
            </div>

            <div id="wpbooklist-upload-stylepaks-div">
                <input id="wpbooklist-add-new-library-stylepak" style="display:none;" type="file" name="files[]" multiple="">
                <button id="wpbooklist-add-new-library-stylepak-button" onclick="document.getElementById(\'wpbooklist-add-new-library-stylepak\').click();" name="add-library-stylepak" type="button">'.__('Upload a New Library StylePak','wpbooklist').'</button>
                    <div class="wpbooklist-spinner" id="wpbooklist-spinner-1"></div>
            </div>';

            $string2 = '<div id="wpbooklist-stylepak-select-stylepak-label">'.__('Select a Library StylePak:','wpbooklist').'</div>
                            <select id="wpbooklist-select-library-stylepak">    
                                <option selected disabled>'.__('Select a Library StylePak','wpbooklist').'</option>
                                <option value="Default StylePak">'.__('Default StylePak','wpbooklist').'</option>';

            foreach(glob(LIBRARY_STYLEPAKS_UPLOAD_DIR.'*.*') as $filename){
                $filename = basename($filename);
                $display_name = str_replace('.css', '', $filename);
                $display_name = str_replace('.zip', '', $display_name);
                if(strpos($filename, '.css') || strpos($filename, '.zip')){
                    $filename = str_replace('.zip', '', $filename);
                    $string2 = $string2.'<option id="'.$filename.'" value="'.$filename.'">'.$display_name.'</option>';
                }
            }

            $string2 = $string2.'</select>';

            $string3 = '<div id="wpbooklist-stylepak-select-library-label" for="wpbooklist-stylepak-select-library">Select a Library to Apply This StylePak to:</div>
                    <select class="wpbooklist-stylepak-select-default" id="wpbooklist-stylepak-select-library">
                        <option value="'.$wpdb->prefix.'wpbooklist_jre_saved_book_log">'.__('Default Library','wpbooklist').'</option> ';

                    $string4 = '';
                    foreach($db_row as $db){
                        if(($db->user_table_name != "") || ($db->user_table_name != null)){
                            $string4 = $string4.'<option value="'.$wpdb->prefix.'wpbooklist_jre_'.$db->user_table_name.'">'.ucfirst($db->user_table_name).'</option>';
                        }
                    }
            $string5 = '</select>
                        <button disabled id="wpbooklist-apply-library-stylepak">'.__('Apply Library StylePak','wpbooklist').'</button>
                        <div id="wpbooklist-addstylepak-success-div"></div>';


        echo $string1.$string_table.$string2.$string3.$string4.$string5;
    }


}

endif;