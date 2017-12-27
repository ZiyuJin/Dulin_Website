<?php
/**
 * WPBookList Custom Libraries Form Tab Class
 *
 * @author   Jake Evans
 * @category Admin
 * @package  Includes/Classes
 * @version  1
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

if ( ! class_exists( 'WPBookList_Backup_Settings_Form', false ) ) :
/**
 * WPBookList_Admin_Menu Class.
 */
class WPBookList_Backup_Settings_Form {

    public static function output_backup_settings_form(){
        global $wpdb;

        $table_name = $wpdb->prefix . 'wpbooklist_jre_list_dynamic_db_names';
        $db_row = $wpdb->get_results("SELECT * FROM $table_name");

        $string1 = '<div id="wpbooklist-backup-settings-container">
                        <p>You\'ve worked hard to add all those books to your website - make sure they\'re backed up! Simply select a Library to backup, click the \'Backup Library\' button below, and <span class="wpbooklist-color-orange-italic">WPBookList</span> will create a Database backup of your library!</p>';

        $string2 = '<div id="wpbooklist-backup-select-library-label" for="wpbooklist-backup-select-library">Select a Library to Backup:</div>
                    <select class="wpbooklist-backup-select-default" id="wpbooklist-backup-select-library">
                        <option selected disabled value="Select a Library...">Select a Library...</option>
                        <option value="'.$wpdb->prefix.'wpbooklist_jre_saved_book_log">Default Library</option> ';

                    $string3 = '';
                    foreach($db_row as $db){
                        if(($db->user_table_name != "") || ($db->user_table_name != null)){
                            $string3 = $string3.'<option value="'.$wpdb->prefix.'wpbooklist_jre_'.$db->user_table_name.'">'.ucfirst($db->user_table_name).'</option>';
                        }
                    }
            $string4 = '</select>
                        <button disabled id="wpbooklist-apply-library-backup">Backup Library</button>
                        <div class="wpbooklist-spinner" id="wpbooklist-spinner-backup"></div>';

            $string5 = '<p>Here you can restore a library from previously-created <span class="wpbooklist-color-orange-italic">WPBookList</span> backups. Simply select a Library to restore, select a backup to restore from, and click the \'Restore Library\' button below. That\'s it!</p>
            <div id="wpbooklist-backup-select-library-label">Select a Library to Restore:</div>
                            <select id="wpbooklist-select-library-backup">    
                                <option selected disabled>Select a Library to Restore...</option>
                                <option value="'.$wpdb->prefix.'wpbooklist_jre_saved_book_log">Default Library</option>';

            $string6 = '';
                    foreach($db_row as $db){
                        if(($db->user_table_name != "") || ($db->user_table_name != null)){
                            $string6 = $string6.'<option value="'.$wpdb->prefix.'wpbooklist_jre_'.$db->user_table_name.'">'.ucfirst($db->user_table_name).'</option>';
                        }
                    }

            $string7 = '</select>';

            $string8 = '<div id="wpbooklist-backup-select-library-label">Select a Backup:</div>
                            <select disabled id="wpbooklist-select-actual-backup">    
                                <option selected disabled>Select a Backup...</option>';
 
            $string9 = '';
            foreach(glob(LIBRARY_DB_BACKUPS_UPLOAD_DIR.'*.*') as $filename){
                // Exclude the csv/txt files
                if(strpos($filename, 'isbn_asin') === false){
                    $filename = basename($filename);
                    $display_name = explode('_-_', $filename);
                    $string9 = $string9.'<option class="wpbooklist-backup-actual-option" data-table="'.$display_name[0].'" id="'.$filename.'" value="'.$filename.'">'.$display_name[1].' - '.date('h:i a', intval($display_name[2])).'</option>';
                }
            }

            $string10 = '</select>
                         <button disabled id="wpbooklist-apply-library-restore">Restore Library</button>
                         <div class="wpbooklist-spinner" id="wpbooklist-spinner-restore-backup"></div>';


            $string11 = '<div id="wpbooklist-backup-create-csv-div">
                            <p>Here you can download a .csv file of ISBN/ASIN numbers from a selected library, which can be very useful in conjunction with the <span class="wpbooklist-color-orange-italic"><a href="https://wpbooklist.com/index.php/downloads/bulk-upload-extension/">WPBookList Bulk-Upload Extension!</a></span> Simply select a Library, click the \'Create CSV File\' button, and <span class="wpbooklist-color-orange-italic">WPBookList</span> will create .csv file of ISBN/ASIN numbers.</p>
                            <select class="wpbooklist-backup-csv-select-default" id="wpbooklist-backup-csv-select-library">
                                <option selected disabled value="Select a Library...">Select a Library...</option>
                                <option value="'.$wpdb->prefix.'wpbooklist_jre_saved_book_log">Default Library</option>'.$string3.'</select>
                        <button disabled id="wpbooklist-apply-library-backup-csv">Create CSV File</button>
                        <div class="wpbooklist-spinner" id="wpbooklist-spinner-backup-csv"></div></div>';



        $string12 = '<div id="wpbooklist-addbackup-success-div"></div></div>';

        echo $string1.$string2.$string3.$string4.$string5.$string6.$string7.$string8.$string9.$string10.$string11.$string12;

    }


}

endif;