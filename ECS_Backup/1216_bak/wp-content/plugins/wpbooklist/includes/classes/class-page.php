<?php
/**
 * WPBookList Page Class
 * Handles functions for:
 * - Creating an individual page for an added book
 * 
 * @author   Jake Evans
 * @category Root Product
 * @package  Includes/Classes
 * @version  1
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

if ( ! class_exists( 'WPBookList_Page', false ) ) :
/**
 * WPBookList_Book Class.
 * Use a default custom page template for each book
 */
class WPBookList_Page {

	public function __construct($book_array) {

		$this->amazon_auth_yes = $book_array['amazon_auth_yes'];
		$this->library = $book_array['library'];
		$this->use_amazon_yes = $book_array['use_amazon_yes'];
		$this->isbn = $book_array['isbn'];
		$this->title = $book_array['title'];
		$this->author = $book_array['author'];
		$this->author_url = $book_array['author_url'];
		$this->category = $book_array['category'];
		$this->price = $book_array['price'];
		$this->pages = $book_array['pages'];
		$this->pub_year = $book_array['pub_year'];
		$this->publisher = $book_array['publisher'];
		$this->description = $book_array['description'];
		$this->notes = $book_array['notes'];
		$this->rating = $book_array['rating'];
		$this->image = $book_array['image'];
		$this->finished = $book_array['finished'];
		$this->date_finished = $book_array['date_finished'];
		$this->signed = $book_array['signed'];
		$this->first_edition = $book_array['first_edition'];
		$this->page_yes = $book_array['page_yes'];
		$this->post_yes = $book_array['post_yes'];
		$this->itunes_page = $book_array['itunes_page'];
		$this->google_preview = $book_array['google_preview'];
		$this->amazon_detail_page = $book_array['amazon_detail_page'];
		$this->review_iframe = $book_array['review_iframe'];
		$this->similar_products = $book_array['similar_products'];
		$this->book_uid = $book_array['book_uid'];

		$this->page_type = 'page';
		$this->page_name = $this->title;
		$this->page_template = NULL;
		$this->page_author_id = get_current_user_id();
		$this->page_status = 'publish';

		// Create the WPBookList Post Category
		$cat_id = $this->create_page_category();
		$this->create_the_page();

	}

	private function create_the_page(){

		$excerpt = $this->description;

		if($excerpt == '' || $excerpt == null){
			$excerpt = $this->title;
		}

		if($excerpt == '' || $excerpt == null){
			$excerpt = 'No excerpt available';
		}


		$post = get_page_by_title( $this->page_nameE, 'OBJECT', $this->page_type );

		$post_data = array(
			'post_title'    => wp_strip_all_tags( $this->page_name ),
			'post_name'		=> $this->page_name.' (page)',
			'post_status'   => $this->page_status,
			'post_type'     => $this->page_type,
			'post_author'   => $this->page_author_id,
			'post_excerpt'      =>  $excerpt
		);
		
		$this->create_result = wp_insert_post( $post_data, $error_obj );

		if ( ! isset( $post ) ) {
			add_action( 'admin_init', 'hbt_create_post' );
			if($error_obj){
				// TODO:s If there was an error, record it in log file here
			} else {
				$db_result = $this->add_to_db();
				// TODO: add $db_result into log file

				$this->create_page_image($this->image, $this->create_result);

				if($db_result == 1){
					return $this->create_result;
				}
			}
		} 
	}

	private function add_to_db(){
		global $wpdb;

		$table_name = $wpdb->prefix.'wpbooklist_jre_saved_page_post_log';

		$insert_array = array(
			'book_uid' => $this->book_uid, 
			'book_title' => $this->title,
			'post_id' => $this->create_result,
			'type'=> $this->page_type,
			'post_url' => get_permalink($this->create_result),
			'author' => $this->page_author_id,
			'active_template' => 'default'
		);
		// TODO: log database save
		return $wpdb->insert( $table_name, $insert_array);
	}


	private function create_page_image( $image_url, $post_id  ){
	    $upload_dir = wp_upload_dir();
	    $image_data = file_get_contents($image_url);

	    $image_url = str_replace('%', '', $image_url);

	    $filename = basename($image_url);
	    if(wp_mkdir_p($upload_dir['path']))     $file = $upload_dir['path'] . '/' . $filename;
	    else                                    $file = $upload_dir['basedir'] . '/' . $filename;
	    file_put_contents($file, $image_data);

	    $wp_filetype = wp_check_filetype($filename, null );
	    $attachment = array(
	        'post_mime_type' => $wp_filetype['type'],
	        'post_title' => sanitize_file_name($filename),
	        'post_content' => '<div class="wpbooklist-page-content">DO NOT DELETE</div>',
	        'post_status' => 'inherit'
	    );
	    $attach_id = wp_insert_attachment( $attachment, $file, $post_id );
	    require_once(ABSPATH . 'wp-admin/includes/image.php');
	    $attach_data = wp_generate_attachment_metadata( $attach_id, $file );
	    $res1= wp_update_attachment_metadata( $attach_id, $attach_data );
	    $res2= set_post_thumbnail( $post_id, $attach_id );
	}

	private function create_page_category(){
		// Create default WPBookList Book Page Category if it doesn't already exist
		$create_cat = true;
		$cat_id = 0;
		foreach((get_categories()) as $category) {
			if($category->cat_name == 'WPBookList Book Page'){
				$cat_id = get_cat_ID('WPBookList Book Page');
				$create_cat = false;
			}
		}

		if($create_cat == false){
			return $cat_id;
			
		} else {
			$result = wp_insert_term(
				'WPBookList Book Page',
				'category',
				array(
				  'description'	=> 'This is a category created by WPBookList to display a book in it\'s very own individual page',
				  'slug' 		=> 'wpbooklist-book-page-cat'
				)
			);

			if(is_object($result)){
				//TODO: Log messages here. This part here will fire if the category already exists, and apparently $result will be a wp error object
				$this->cat_create_result = $result;
			} else {
				$this->cat_create_result = $result['term_id'];
			}
		}
	}
	



}

endif;