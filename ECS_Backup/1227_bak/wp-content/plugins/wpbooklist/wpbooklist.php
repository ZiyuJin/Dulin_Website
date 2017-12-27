<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
/*
Plugin Name: WordPress Book List
Plugin URI: https://www.jakerevans.com
Description: For authors, publishers, librarians, and book-lovers alike - use it to sell your books, record and catalog your library, and more!
Version: 5.3.3
Author: Jake Evans
Text Domain: wpbooklist
Author URI: https://www.jakerevans.com
License: GPL2
*/ 


//TODO: Document all available filters
//TODO: When deleting a title, you must check for an associated page and post, and delete those too.
//TODO: When you've included the function for displaying a book, in the javascript portion that makes the callback to the function that actually displays the book, once colorbox opens, make a call to a element in the colorbox and insert the function for a google preview of the book. so do a .html or .after on something in the colorbox and insert this code: ('<script type="text/javascript"> GBS_insertEmbeddedViewer('ISBN:9780761169086',600,500);</script>'). https://developers.google.com/books/docs/preview-wizard
//TODO: Prefix every single global variable with 'wpbooklist', do a search-and-replace, and also wrap each 'define' below in a array_key_exists('v', $GLOBALS) to ensure there is no conflict with any other potential global variables throughout the entire wordpress website and my extensions. Update extension boilerplate with all availabe options as well.
global $wpdb;
require_once('includes/functions.php');
require_once('includes/ajaxfunctions.php');

// Parse the wpbooklistconfig file
$config_array = parse_ini_file("wpbooklistconfig.ini");

// Get the default admin message for inclusion into database 
define('ADMIN_MESSAGE', $config_array['initial_admin_message']);

// Grabbing database prefix
define('$wpdb->prefix', $wpdb->prefix);

// Root plugin folder directory
define('WPBOOKLIST_VERSION_NUM', '5.3.3');

// Root plugin folder directory
define('ROOT_DIR', ABSPATH.'wp-content/plugins/wpbooklist/');

// Root WordPress Plugin Directory
define('ROOT_WP_PLUGINS_DIR', str_replace('/wpbooklist', '', plugin_dir_path(__FILE__)));

// Root plugin folder URL
define('ROOT_URL', plugins_url().'/wpbooklist/');

// Quotes Directory
define('QUOTES_DIR', ROOT_DIR.'quotes/');

// Root JavaScript Directory
define('JAVASCRIPT_URL', ROOT_URL.'assets/js/');

// Root Classes Directory
define('CLASS_DIR', ROOT_DIR.'includes/classes/');

// Root Image URL
define('ROOT_IMG_URL', ROOT_URL.'assets/img/');

// Root Image Icons URL
define('ROOT_IMG_ICONS_URL', ROOT_URL.'assets/img/icons/');

// Root CSS URL
define('ROOT_CSS_URL', ROOT_URL.'assets/css/');

// Root UI directory
define('ROOT_INCLUDES_UI', ROOT_DIR.'includes/ui/');

// Root UI Admin directory
define('ROOT_INCLUDES_UI_ADMIN_DIR', ROOT_DIR.'includes/ui/admin/');

// Define the Uploads base directory
$uploads = wp_upload_dir();
$upload_path = $uploads['basedir'];
define('UPLOADS_BASE_DIR', $upload_path.'/');

$upload_url = $uploads['baseurl'];
define('UPLOADS_BASE_URL', $upload_url.'/');

// Define the Library Stylepaks base directory
define('LIBRARY_STYLEPAKS_UPLOAD_DIR', UPLOADS_BASE_DIR.'wpbooklist/stylepaks/library/');

// Define the Library Stylepaks base url
define('LIBRARY_STYLEPAKS_UPLOAD_URL', UPLOADS_BASE_URL.'wpbooklist/stylepaks/library/');

// Define the Posts Stylepaks base directory
define('POST_TEMPLATES_UPLOAD_DIR', UPLOADS_BASE_DIR.'wpbooklist/templates/posts/');

// Define the Posts Stylepaks base url
define('POST_TEMPLATES_UPLOAD_URL', UPLOADS_BASE_URL.'wpbooklist/templates/posts/');

// Define the Pages Stylepaks base directory
define('PAGE_TEMPLATES_UPLOAD_DIR', UPLOADS_BASE_DIR.'wpbooklist/templates/pages/');

// Define the Pages Stylepaks base url
define('PAGE_TEMPLATES_UPLOAD_URL', UPLOADS_BASE_URL.'wpbooklist/templates/pages/');

// Define the Library DB backups base directory
define('LIBRARY_DB_BACKUPS_UPLOAD_DIR', UPLOADS_BASE_DIR.'wpbooklist/backups/library/db/');

// Define the Library DB backups base directory
define('LIBRARY_DB_BACKUPS_UPLOAD_URL', UPLOADS_BASE_URL.'wpbooklist/backups/library/db/');

// Define the page templates base directory
define('PAGE_POST_TEMPLATES_DEFAULT_DIR', ROOT_DIR.'includes/templates/');

// Define the edit page offset
define('EDIT_PAGE_OFFSET', 100);

// Loading textdomain
load_plugin_textdomain( 'wpbooklist', false, ROOT_DIR.'languages' );

// For admin messages
add_action( 'admin_notices', 'wpbooklist_jre_for_reviews_and_wpbooklist_admin_notice__success' );

// Adding Ajax library
add_action( 'wp_head', 'wpbooklist_jre_prem_add_ajax_library' );

// Adding admin page
add_action( 'admin_menu', 'wpbooklist_jre_my_admin_menu' );

// Registers table names
add_action( 'init', 'wpbooklist_jre_register_table_name', 1 );

// Function to run any code that is needed to modify the plugin between different versions
add_action( 'plugins_loaded', 'wpbooklist_upgrade_function');

// Creates tables upon activation
register_activation_hook( __FILE__, 'wpbooklist_jre_create_tables' );

// Deletes tables upon plugin deletion
//register_uninstall_hook( __FILE__, 'wpbooklist_jre_delete_tables' );

// Adding the general admin css file
add_action('admin_enqueue_scripts', 'wpbooklist_jre_plugin_general_admin_style' );

// Adding the admin template css file
add_action('admin_enqueue_scripts', 'wpbooklist_jre_plugin_admin_template_style' );

// Adding the posts & pages css file
add_action('wp_enqueue_scripts', 'wpbooklist_jre_posts_pages_default_style' );

// Adding the front-end library ui css file
add_action('wp_enqueue_scripts', 'wpbooklist_jre_frontend_library_ui_default_style');

// Adding Colorbox css file
add_action('wp_enqueue_scripts', 'wpbooklist_jre_plugin_colorbox_style' );
add_action('admin_enqueue_scripts', 'wpbooklist_jre_plugin_colorbox_style' );

// Adding the form check js file
add_action('admin_enqueue_scripts', 'wpbooklist_form_checks_js' );

// Adding the html entities decode js file
//add_action('admin_enqueue_scripts', 'wpbooklist_he_js' );

// Adding the jquery masked js file
add_action('admin_enqueue_scripts', 'wpbooklist_jquery_masked_input_js' );

// Code for adding the jquery readmore file for text blocks like description and notes
add_action('wp_enqueue_scripts', 'wpbooklist_jquery_readmore_js' );

// Adding the front-end library shortcode
add_shortcode('wpbooklist_shortcode', 'wpbooklist_jre_plugin_dynamic_shortcode_function');

// Shortcode that allows a book image to be placed on a page
add_shortcode('showbookcover', 'wpbooklist_book_cover_shortcode');

// Adding colorbox JS file on both front-end and dashboard
add_action('admin_enqueue_scripts', 'wpbooklist_jre_plugin_colorbox_script' );
add_action('wp_enqueue_scripts', 'wpbooklist_jre_plugin_colorbox_script' );

// Adding AddThis sharing JS file
add_action('admin_enqueue_scripts', 'wpbooklist_jre_plugin_addthis_script' );
add_action('wp_enqueue_scripts', 'wpbooklist_jre_plugin_addthis_script' );

// For the REST API update for dashboard messages 
add_action( 'rest_api_init', function () {
  register_rest_route( 'wpbooklist/v1', '/notice/(?P<notice>[a-z0-9\-]+)', array(
    'methods' => 'GET',
    'callback' => 'wpbooklist_jre_rest_api_notice',
  ) );
} );

//For dismissing notice
add_action( 'admin_footer', 'wpbooklist_jre_dismiss_prem_notice_forever_action_javascript' ); // Write our JS below here
add_action( 'wp_ajax_wpbooklist_jre_dismiss_prem_notice_forever_action', 'wpbooklist_jre_dismiss_prem_notice_forever_action_callback' );
add_action( 'wp_ajax_nopriv_wpbooklist_jre_dismiss_prem_notice_forever_action', 'wpbooklist_jre_dismiss_prem_notice_forever_action_callback' );

// For adding a book from the admin dashboard
add_action( 'admin_footer', 'wpbooklist_dashboard_add_book_action_javascript' );
add_action( 'wp_ajax_wpbooklist_dashboard_add_book_action', 'wpbooklist_dashboard_add_book_action_callback' );
add_action( 'wp_ajax_nopriv_wpbooklist_dashboard_add_book_action', 'wpbooklist_dashboard_add_book_action_callback' );

// For editing a book from the admin dashboard
add_action( 'admin_footer', 'wpbooklist_edit_book_show_form_action_javascript' );
add_action( 'wp_ajax_wpbooklist_edit_book_show_form_action', 'wpbooklist_edit_book_show_form_action_callback' );
add_action( 'wp_ajax_nopriv_wpbooklist_edit_book_show_form_action', 'wpbooklist_edit_book_show_form_action_callback' );

// For displaying a book in colorbox
add_action( 'admin_footer', 'wpbooklist_show_book_in_colorbox_action_javascript' );
add_action( 'wp_footer', 'wpbooklist_show_book_in_colorbox_action_javascript' );
add_action( 'wp_ajax_wpbooklist_show_book_in_colorbox_action', 'wpbooklist_show_book_in_colorbox_action_callback' );
add_action( 'wp_ajax_nopriv_wpbooklist_show_book_in_colorbox_action', 'wpbooklist_show_book_in_colorbox_action_callback' );

// For creating/deleting custom libraries
add_action( 'admin_footer', 'wpbooklist_new_lib_shortcode_action_javascript' );
add_action( 'wp_ajax_wpbooklist_new_lib_shortcode_action', 'wpbooklist_new_lib_shortcode_action_callback' );
add_action( 'wp_ajax_nopriv_wpbooklist_new_lib_shortcode_action', 'wpbooklist_new_lib_shortcode_action_callback' );

// For saving library display options
add_action( 'admin_footer', 'wpbooklist_dashboard_save_library_display_options_action_javascript' );
add_action( 'wp_ajax_wpbooklist_dashboard_save_library_display_options_action', 'wpbooklist_dashboard_save_library_display_options_action_callback' );
add_action( 'wp_ajax_nopriv_wpbooklist_dashboard_save_library_display_options_action', 'wpbooklist_dashboard_save_library_display_options_action_callback' );

// For saving post display options
add_action( 'admin_footer', 'wpbooklist_dashboard_save_post_display_options_action_javascript' );
add_action( 'wp_ajax_wpbooklist_dashboard_save_post_display_options_action', 'wpbooklist_dashboard_save_post_display_options_action_callback' );
add_action( 'wp_ajax_nopriv_wpbooklist_dashboard_save_post_display_options_action', 'wpbooklist_dashboard_save_post_display_options_action_callback' );

// For saving page display options
add_action( 'admin_footer', 'wpbooklist_dashboard_save_page_display_options_action_javascript' );
add_action( 'wp_ajax_wpbooklist_dashboard_save_page_display_options_action', 'wpbooklist_dashboard_save_page_display_options_action_callback' );
add_action( 'wp_ajax_nopriv_wpbooklist_dashboard_save_page_display_options_action', 'wpbooklist_dashboard_save_page_display_options_action_callback' );

// To update the saved display option checkboxes when drop-down changes
add_action( 'admin_footer', 'wpbooklist_update_display_options_action_javascript' );
add_action( 'wp_ajax_wpbooklist_update_display_options_action', 'wpbooklist_update_display_options_action_callback' );
add_action( 'wp_ajax_nopriv_wpbooklist_update_display_options_action', 'wpbooklist_update_display_options_action_callback' );

// For handling the pagination of the 'Edit & Delete Books' tab
add_action( 'admin_footer', 'wpbooklist_edit_book_pagination_action_javascript' );
add_action( 'wp_ajax_wpbooklist_edit_book_pagination_action', 'wpbooklist_edit_book_pagination_action_callback' );
add_action( 'wp_ajax_nopriv_wpbooklist_edit_book_pagination_action', 'wpbooklist_edit_book_pagination_action_callback' );

// For switching libraries on the 'Edit & Delete Books' tab
add_action( 'admin_footer', 'wpbooklist_edit_book_switch_lib_action_javascript' );
add_action( 'wp_ajax_wpbooklist_edit_book_switch_lib_action', 'wpbooklist_edit_book_switch_lib_action_callback' );
add_action( 'wp_ajax_nopriv_wpbooklist_edit_book_switch_lib_action', 'wpbooklist_edit_book_switch_lib_action_callback' );

// For searching for a title to edit
add_action( 'admin_footer', 'wpbooklist_edit_book_search_action_javascript' );
add_action( 'wp_ajax_wpbooklist_edit_book_search_action', 'wpbooklist_edit_book_search_action_callback' );
add_action( 'wp_ajax_nopriv_wpbooklist_edit_book_search_action', 'wpbooklist_edit_book_search_action_callback' );

// For the saving of edits to existing books
add_action( 'admin_footer', 'wpbooklist_edit_book_actual_action_javascript' );
add_action( 'wp_ajax_wpbooklist_edit_book_actual_action', 'wpbooklist_edit_book_actual_action_callback' );
add_action( 'wp_ajax_nopriv_wpbooklist_edit_book_actual_action', 'wpbooklist_edit_book_actual_action_callback' );

// For deleting a book
add_action( 'admin_footer', 'wpbooklist_delete_book_action_javascript' );
add_action( 'wp_ajax_wpbooklist_delete_book_action', 'wpbooklist_delete_book_action_callback' );
add_action( 'wp_ajax_nopriv_wpbooklist_delete_book_action', 'wpbooklist_delete_book_action_callback' );

// For saving a user's own API keys
add_action( 'admin_footer', 'wpbooklist_user_apis_action_javascript' );
add_action( 'wp_ajax_wpbooklist_user_apis_action', 'wpbooklist_user_apis_action_callback' );
add_action( 'wp_ajax_nopriv_wpbooklist_user_apis_action', 'wpbooklist_user_apis_action_callback' );

// For handling the pagination of the library
add_action( 'wp_footer', 'wpbooklist_library_pagination_action_javascript' );
add_action( 'wp_ajax_wpbooklist_library_pagination_action', 'wpbooklist_library_pagination_action_callback' );
add_action( 'wp_ajax_nopriv_wpbooklist_library_pagination_action', 'wpbooklist_library_pagination_action_callback' );

// For handling the search of the library on the Frontend
add_action( 'wp_footer', 'wpbooklist_library_search_action_javascript' );
add_action( 'wp_ajax_wpbooklist_library_search_action', 'wpbooklist_library_search_action_callback' );
add_action( 'wp_ajax_nopriv_wpbooklist_library_search_action', 'wpbooklist_library_search_action_callback' );

// For sorting the books on the front-end library from the drop-down
add_action( 'wp_footer', 'wpbooklist_library_sort_select_action_javascript' );
add_action( 'wp_ajax_wpbooklist_library_sort_select_action', 'wpbooklist_library_sort_select_action_callback' );
add_action( 'wp_ajax_nopriv_wpbooklist_library_sort_select_action', 'wpbooklist_library_sort_select_action_callback' );

// For uploading a new StylePak after purchase
add_action( 'admin_footer', 'wpbooklist_upload_new_stylepak_action_javascript' );
add_action( 'wp_ajax_wpbooklist_upload_new_stylepak_action', 'wpbooklist_upload_new_stylepak_action_callback' );
add_action( 'wp_ajax_nopriv_wpbooklist_upload_new_stylepak_action', 'wpbooklist_upload_new_stylepak_action_callback' );

// For uploading a new post StylePak after purchase
add_action( 'admin_footer', 'wpbooklist_upload_new_post_template_action_javascript' );
add_action( 'wp_ajax_wpbooklist_upload_new_post_template_action', 'wpbooklist_upload_new_post_template_action_callback' );
add_action( 'wp_ajax_nopriv_wpbooklist_upload_new_post_template_action', 'wpbooklist_upload_new_post_template_action_callback' );

// For uploading a new page StylePak after purchase
add_action( 'admin_footer', 'wpbooklist_upload_new_page_template_action_javascript' );
add_action( 'wp_ajax_wpbooklist_upload_new_page_template_action', 'wpbooklist_upload_new_page_template_action_callback' );
add_action( 'wp_ajax_nopriv_wpbooklist_upload_new_page_template_action', 'wpbooklist_upload_new_page_template_action_callback' );

// For creating a backup of a Library
add_action( 'admin_footer', 'wpbooklist_create_db_library_backup_action_javascript' );
add_action( 'wp_ajax_wpbooklist_create_db_library_backup_action', 'wpbooklist_create_db_library_backup_action_callback' );
add_action( 'wp_ajax_nopriv_wpbooklist_create_db_library_backup_action', 'wpbooklist_create_db_library_backup_action_callback' );

// For restoring a backup of a Library
add_action( 'admin_footer', 'wpbooklist_restore_db_library_backup_action_javascript' );
add_action( 'wp_ajax_wpbooklist_restore_db_library_backup_action', 'wpbooklist_restore_db_library_backup_action_callback' );
add_action( 'wp_ajax_nopriv_wpbooklist_restore_db_library_backup_action', 'wpbooklist_restore_db_library_backup_action_callback' );

// For creating a .csv file of ISBN/ASIN numbers
add_action( 'admin_footer', 'wpbooklist_create_csv_action_javascript' );
add_action( 'wp_ajax_wpbooklist_create_csv_action', 'wpbooklist_create_csv_action_callback' );
add_action( 'wp_ajax_nopriv_wpbooklist_create_csv_action', 'wpbooklist_create_csv_action_callback' );

// For setting the Amazon Localization
add_action( 'admin_footer', 'wpbooklist_amazon_localization_action_javascript' );
add_action( 'wp_ajax_wpbooklist_amazon_localization_action', 'wpbooklist_amazon_localization_action_callback' );
add_action( 'wp_ajax_nopriv_wpbooklist_amazon_localization_action', 'wpbooklist_amazon_localization_action_callback' );

// For bulk-deleting books
add_action( 'admin_footer', 'wpbooklist_delete_book_bulk_action_javascript' );
add_action( 'wp_ajax_wpbooklist_delete_book_bulk_action', 'wpbooklist_delete_book_bulk_action_callback' );
add_action( 'wp_ajax_nopriv_wpbooklist_delete_book_bulk_action', 'wpbooklist_delete_book_bulk_action_callback' );

// For reordering books
add_action( 'admin_footer', 'wpbooklist_reorder_action_javascript' );
add_action( 'wp_ajax_wpbooklist_reorder_action', 'wpbooklist_reorder_action_callback' );
add_action( 'wp_ajax_nopriv_wpbooklist_reorder_action', 'wpbooklist_reorder_action_callback' );


// The function that determines which template to load for WPBookList Pages
add_filter( 'the_content', 'wpbooklist_set_page_post_template' );

// Handles various aestetic functions for the front end
add_action( 'wp_footer', 'wpbooklist_various_aestetic_bits_front_end' );

// Handles various aestetic functions for the back end
add_action( 'admin_footer', 'wpbooklist_various_aestetic_bits_back_end' );
?>