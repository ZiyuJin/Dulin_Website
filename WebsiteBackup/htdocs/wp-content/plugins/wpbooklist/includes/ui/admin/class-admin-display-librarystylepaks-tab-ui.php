<?php
/**
 * WPBookList LibraryStylePaks Display Options Tab Class
 *
 * @author   Jake Evans
 * @category Admin
 * @package  Includes/UI/Admin
 * @version  1
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

if ( ! class_exists( 'WPBookList_LibraryStylePaks_Display_Options_Tab', false ) ) :
/**
 * WPBookList_Admin_Menu Class.
 */
class WPBookList_LibraryStylePaks_Display_Options_Tab {

    public function __construct() {
    	require_once(CLASS_DIR.'class-admin-ui-template.php');
    	require_once(CLASS_DIR.'class-librarystylepaks-display-options-form.php');
    	// Instantiate the class
		$this->template = new WPBookList_Admin_UI_Template;
		$this->form = new WPBookList_LibraryStylePaks_Display_Options_Form ;
		$this->output_open_admin_container();
		$this->output_tab_content();
		$this->output_close_admin_container();
		$this->output_admin_template_advert();
    }

    private function output_open_admin_container(){
        $title = __('Library StylePaks', 'wpbooklist');
        $icon_url = ROOT_IMG_ICONS_URL.'librarystylepak.svg';
    	echo $this->template->output_open_admin_container($title, $icon_url);
    }

    private function output_tab_content(){
    	echo $this->form->output_add_edit_form();
    }

    #TODO: Replace that 'Book Added Succesfully!' line above with a link to open the title in colorbox, once that functionality is complete

    private function output_close_admin_container(){
    	echo $this->template->output_close_admin_container();
    }

    private function output_admin_template_advert(){
    	echo $this->template->output_template_advert();
    }

}

endif;


// Instantiate the class
$am = new WPBookList_LibraryStylePaks_Display_Options_Tab;