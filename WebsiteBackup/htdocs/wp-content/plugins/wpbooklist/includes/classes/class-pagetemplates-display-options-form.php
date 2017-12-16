<?php
/**
 * WPBookList PageTemplates Display Options Form Tab Class
 *
 * @author   Jake Evans
 * @category Admin
 * @package  Includes/Classes
 * @version  1
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

if ( ! class_exists( 'WPBookList_PageTemplates_Display_Options_Form', false ) ) :
/**
 * WPBookList_Admin_Menu Class.
 **/
class WPBookList_PageTemplates_Display_Options_Form {

    public static function output_add_edit_form(){
        global $wpdb;

        $table_name = $wpdb->prefix . 'wpbooklist_jre_user_options';
        $default = $wpdb->get_row("SELECT * FROM $table_name");

        if($default->activepagetemplate == null || $default->activepagetemplate == 'Default'){
            $default->activepagetemplate = 'Default Page Template';
        }

        $default->activepagetemplate = str_replace('Page-', 'Page ', $default->activepagetemplate);
        $default->activepagetemplate = str_replace('Template-', 'Template ', $default->activepagetemplate);

        $string_table = '<div id="wpbooklist-stylepak-table-container">
                            <table>
                                <tr id="wpbooklist-stylepak-heading-row">
                                    <th>
                                        <img class="wpbooklist-stylepak-heading-img" src="'.ROOT_IMG_ICONS_URL.'librarystylepak.svg"><div class="wpbooklist-stylepak-table-heading">Active Page Template</div>
                                    </th>
                                </tr>
                                <tr>
                                    <td>
                                        <div class="wpbooklist-stylepak-table-stylepak">'.ucfirst($default->activepagetemplate).'</div>
                                    </td>
                                </tr>';


        $string_table = $string_table.'</table></div>';



        $string1 = '<p>What\'s a <span class="wpbooklist-color-orange-italic">Page Template</span> you ask? <span class="wpbooklist-color-orange-italic">Page Templates</span> are the best way to instantly change the look and feel of your <span class="wpbooklist-color-orange-italic">WPBookList</span> Pages!</p><p>Simply <a href="https://wpbooklist.com/index.php/templates-2/">Purchase a $2 Page Template Here</a>, upload it using the <span class="wpbooklist-color-orange-italic">\'Upload a New Page Template\'</span>&nbsp;button below, and assign your new Page Template to your WPBookList Pages - it\'s that simple!</p>

            <div id="wpbooklist-stylepak-demo-links">
                <a href="https://wpbooklist.com/index.php/downloads/template-pak-1/">Page Template 1</a>
                <a href="https://wpbooklist.com/index.php/downloads/template-pak-2/">Page Template 2</a>
                <a href="https://wpbooklist.com/index.php/downloads/template-pak-3/">Page Template 3</a>
                <a href="https://wpbooklist.com/index.php/downloads/template-pak-4/">Page Template 4</a>
                <a href="https://wpbooklist.com/index.php/downloads/template-pak-5/">Page Template 5</a>
            </div>

            <div id="wpbooklist-buy-library-stylepaks-div">
                <a id="wpbooklist-stylepak-buy-link" href="https://wpbooklist.com/index.php/templates-2/"><img src="'.ROOT_IMG_URL.'getpagetemplates.png" /></a>
            </div>

            <div id="wpbooklist-upload-stylepaks-div">
                <input id="wpbooklist-add-new-page-template" style="display:none;" type="file" name="files[]" multiple="">
                <button onclick="document.getElementById(\'wpbooklist-add-new-page-template\').click();" name="add-library-stylepak" type="button">Upload a New Page Template</button>
                    <div class="wpbooklist-spinner" id="wpbooklist-spinner-1"></div>
            </div>';

            $string2 = '<div id="wpbooklist-stylepak-select-stylepak-label">Select a Page Template To Apply to Your WPBookList Pages:</div>
                            <select id="wpbooklist-select-page-template">    
                                <option selected disabled>Select a Page Template</option>
                                <option value="Default Template">Default Page Template</option>';

            foreach(glob(PAGE_TEMPLATES_UPLOAD_DIR.'*.*') as $filename){
                $filename = basename($filename);
                if((strpos($filename, '.php') || strpos($filename, '.zip')) && strpos($filename, 'Page') !== false){
                    $display_name = str_replace('.php', '', $filename);
                    $display_name = str_replace('Template-', 'Template ', $display_name);
                    $display_name = str_replace('Page-', 'Page ', $display_name);
                    $string2 = $string2.'<option id="'.$filename.'" value="'.$filename.'">'.$display_name.'</option>';
                }
            }

            $string3 = '</select><button disabled id="wpbooklist-apply-page-template">Apply Page Template</button>
                        <div id="wpbooklist-addtemplate-success-div"></div>';


        echo $string1.$string_table.$string2.$string3;
    }


}

endif;