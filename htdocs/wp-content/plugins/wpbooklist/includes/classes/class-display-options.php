<?php
/**
 * WPBookList Display Options Class
 * Handles functions for:
 * - Saving display options for Library
 * - Saving display options for Posts
 * - Saving display options for Pages
 * @author   Jake Evans
 * @category Root Product
 * @package  Includes/Classes
 * @version  1
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

if ( ! class_exists( 'WPBookList_Display_Options', false ) ) :
/**
 * WPBookList_Display_Options.
 */
class WPBookList_Display_Options {

	public function save_library_settings($table, $settings_array = array()){

		global $wpdb;
		$final_table = '';
		if(strpos($table, 'wpbooklist_jre_saved_book_log') !== false){
			$final_table = $wpdb->prefix.'wpbooklist_jre_user_options';
		} else {
			$temp = array_pop(explode('_', $table));
			$final_table = $wpdb->prefix.'wpbooklist_jre_settings_'.$temp;
		}

		foreach($settings_array as $key=>$array){
			if($array == 'false'){
				$settings_array[$key] = 0;
			}

			if($array == 'true'){
				$settings_array[$key] = 1;
			}


		}


        $where = array( 'ID' => 1 );
        $result = $wpdb->update( $final_table, $settings_array, $where );

	}

	public function save_post_settings($settings_array = array()){
		global $wpdb;
		$table = $wpdb->prefix.'wpbooklist_jre_post_options';

		foreach($settings_array as $key=>$array){
			if($array == 'false'){
				$settings_array[$key] = 0;
			}

			if($array == 'true'){
				$settings_array[$key] = 1;
			}
		}

        $where = array( 'ID' => 1 );
        $result = $wpdb->update( $table, $settings_array, $where);

	}

	public function save_page_settings($settings_array = array()){

		global $wpdb;
		$table = $wpdb->prefix.'wpbooklist_jre_page_options';

		foreach($settings_array as $key=>$array){
			if($array == 'false'){
				$settings_array[$key] = 0;
			}

			if($array == 'true'){
				$settings_array[$key] = 1;
			}
		}

        $where = array( 'ID' => 1 );
        $result = $wpdb->update( $table, $settings_array, $where);


	}

	public function get_page_display_settings(){

	}

	public function get_post_display_settings(){

	}

	public function get_library_display_settings($table){

	}

	



}

endif;