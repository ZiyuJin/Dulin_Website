<?php
/**
 * WPBookList API Settings Tab
 *
 * @author   Jake Evans
 * @category Admin
 * @package  Includes/Classes
 * @version  1
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

if ( ! class_exists( 'WPBookList_Backup_Settings', false ) ) :
/**
 * WPBookList_Backup_Settings Class.
 */
class WPBookList_Backup_Settings {

    public function __construct() {
        require_once(CLASS_DIR.'class-admin-ui-template.php');
        require_once(CLASS_DIR.'class-backup-settings-form.php');
        // Instantiate the class
        $this->template = new WPBookList_Admin_UI_Template;
        $this->form = new WPBookList_Backup_Settings_Form;
        $this->output_open_admin_container();
        $this->output_tab_content();
        $this->output_close_admin_container();
        $this->output_admin_template_advert();
    }

    private function output_open_admin_container(){
        $title = __('Backups','wpbooklist');
        $icon_url = ROOT_IMG_ICONS_URL.'backup.svg';
        echo $this->template->output_open_admin_container($title, $icon_url);
    }

    private function output_tab_content(){
        echo $this->form->output_backup_settings_form();
    }

    private function output_close_admin_container(){
        echo $this->template->output_close_admin_container();
    }

    private function output_admin_template_advert(){
        echo $this->template->output_template_advert();
    }


}
endif;

// Instantiate the class
$cm = new WPBookList_Backup_Settings;