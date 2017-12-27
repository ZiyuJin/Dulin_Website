<?php
/**
 * WPBookList Edit-Book-Form Tab Class
 *
 * @author   Jake Evans
 * @category Admin
 * @package  Includes/Classes
 * @version  1
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

if ( ! class_exists( 'WPBookList_Edit_Book_Form', false ) ) :
/**
 * WPBookList_Edit_Book Class.
 */
class WPBookList_Edit_Book_Form {

	public $table;
	public $limit;

	public function output_edit_book_form($table, $offset, $search_mode = null, $search_term = null){
		global $wpdb;
		wp_enqueue_media();

		$this->table = $table;
		if($this->table === 'default'){
			$this->table = $wpdb->prefix.'wpbooklist_jre_saved_book_log';
		}

		global $wpdb;
		if($search_mode != null && $search_term != null){
			if($search_mode == 'author'){
				$this->books_actual = $wpdb->get_results($wpdb->prepare("SELECT * FROM $this->table WHERE author LIKE '%s'", '%'.$search_term.'%'));
			}

			if($search_mode == 'title'){
				$this->books_actual = $wpdb->get_results($wpdb->prepare("SELECT * FROM $this->table WHERE title LIKE '%s'", '%'.$search_term.'%'));
			}

			if($search_mode == 'both'){
				$this->books_actual = $wpdb->get_results($wpdb->prepare("SELECT * FROM $this->table WHERE title LIKE '%s' OR author LIKE '%s'", '%'.$search_term.'%', '%'.$search_term.'%'));
			}
		} else {
			$this->books_actual = $wpdb->get_results("SELECT * FROM $this->table");
		}

		// Getting number of results
		$this->limit = $wpdb->num_rows;

		// Default sorting - sorts by IDs from low to high
		function compare_ids($a, $b){
			return $a->ID - $b->ID;
		}
		usort($this->books_actual, "compare_ids");

		// Set up library drop-down
		$table_name = $wpdb->prefix . 'wpbooklist_jre_list_dynamic_db_names';
		$db_row = $wpdb->get_results("SELECT * FROM $table_name");


	    $string1 = '<div id="wpbooklist-edit-books-lib-search-div">
	    	<div id="wpbooklist-edit-books-lib-div">
	    		<p id="wpbooklist-edit-books-lib-p">Select a Library to Edit Books From</p>
	    		<select class="wpbooklist-editbook-select-default" id="wpbooklist-editbook-select-library">
		    			<option value="'.$wpdb->prefix.'wpbooklist_jre_saved_book_log">Default Library</option>';
		$string2 = '';
		foreach($db_row as $db){
			if(($db->user_table_name != "") || ($db->user_table_name != null)){
				$string2 = $string2.'<option value="'.$wpdb->prefix.'wpbooklist_jre_'.$db->user_table_name.'">'.ucfirst($db->user_table_name).'</option>';
			}
		}

		$string3 = '</select>
				</div>
				<div class="wpbooklist-spinner" id="wpbooklist-spinner-edit-change-lib"></div>
				<div id="wpbooklist-edit-books-search-div">
					<p id="wpbooklist-edit-books-lib-p">Search for a Book to Edit</p>
					<label>Search by Title</label><input id="wpbooklist-search-title-checkbox" type="checkbox"/><label>Search by Author</label><input id="wpbooklist-search-author-checkbox" type="checkbox"/>
					<input id="wpbooklist-edit-book-search-input" type="text" />
					<button id="wpbooklist-edit-book-search-button" type="button">Search</button>
				</div>
			</div>
			<div id="wpbooklist-bulk-edit-div">
				<button id="wpbooklist-bulk-edit-mode-on-button"';

				$string4 = '';
				if(count($this->books_actual) == 0){
					$string4 = 'disabled';
				}

				$string5 = ' type="button">Bulk Delete Mode</button>
				<div id="wpbooklist-bulk-edit-mode-on-div">
					<button disabled id="wpbooklist-bulk-edit-mode-delete-checked" type="button">Delete Checked Books</button>
					<button id="wpbooklist-bulk-edit-mode-delete-all-in-lib" type="button">Delete All Books in This Library</button>
					<button id="wpbooklist-bulk-edit-mode-delete-all-plus-pp-in-lib" type="button">Delete All Books & Pages & Posts in This Library</button>
					<button id="wpbooklist-bulk-edit-mode-delete-all-in-lib-cancel" type="button">Cancel</button>
				</div>
				<button id="wpbooklist-reorder-button" type="button">Reorder Books</button>
				<button id="wpbooklist-cancel-reorder-button" type="button">Cancel</button>
			</div>';
		$string6 = '';
		// If there are no results from the query
		if($this->books_actual == null){
			$string6 = '<div class="wpbooklist-search-indiv-container"><div id="wpbooklist-search-results-info"></div>';
		}

		$divclose = '';
		if($this->books_actual < 1 || $this->books_actual == null){
			$divclose = '</div>';
		} else {

			// The loop that will construct each line
			foreach($this->books_actual as $key=>$book){
				if(($key >= ($offset)) && ($key <= ($offset+EDIT_PAGE_OFFSET))){

				if($book->title == '' || $book->title == null){
					$book->title = 'Book Title Unavailable!';
				}

				if($book->author == '' || $book->author == null){
					$book->author = 'Book Author Unavailable!';
				}

				if($book->image == '' || $book->image == null){
					$book->image = ROOT_IMG_URL.'image_unavaliable.png';
				}

				$string6 = $string6.'<div class="wpbooklist-search-indiv-container"><div id="wpbooklist-search-results-info">

					</div>
					<div class="wpbooklist-edit-book-indiv-div-class" id="wpbooklist-edit-book-indiv-div-id-'.$key.'"">
						<div class="wpbooklist-edit-title-div">
							<div class="wpbooklist-bulk-delete-checkbox-div">
								<input data-key="'.$key.'" data-table="'.$this->table.'" data-book-id="'.$book->ID.'" class="wpbooklist-bulk-delete-checkbox" type="checkbox" /><label>Delete Title</label>
							</div>
							<div class="wpbooklist-edit-img-author-div">
								<img data-bookid="'.$book->ID.'" data-bookuid="'.$book->book_uid.'" data-booktable="'.$this->table.'" class="wpbooklist-edit-book-cover-img wpbooklist-show-book-colorbox" src="'.$book->image.'"/>
								<p class="wpbooklist-edit-book-title wpbooklist-show-book-colorbox" data-booktable="'.$this->table.'" data-bookid="'.$book->ID.'">'.stripslashes($book->title).'</p><br/>
								<img class="wpbooklist-edit-book-icon wpbooklist-book-icon-author " src="'.ROOT_IMG_ICONS_URL.'author.svg"/><p class="wpbooklist-edit-book-author">'.$book->author.'</p>
							</div>
						</div>
						<div class="wpbooklist-edit-actions-div">
							<div class="wpbooklist-edit-actions-edit-button" data-key="'.$key.'" data-table="'.$this->table.'" data-book-id="'.$book->ID.'"><img class="wpbooklist-edit-book-icon wpbooklist-edit-book-icon-button" src="'.ROOT_IMG_ICONS_URL.'pencil.svg"/> Edit</div>
							<div class="wpbooklist-edit-actions-delete-button" data-key="'.$key.'" data-table="'.$this->table.'" data-book-id="'.$book->ID.'"><img class="wpbooklist-edit-book-icon wpbooklist-edit-book-icon-button" src="'.ROOT_IMG_ICONS_URL.'garbage-bin.svg"/> Delete</div>
							<div class="wpbooklist-edit-book-delete-page-post-div">';
								
								if($book->page_yes != null && $book->page_yes != 'false'){
									$string6 = $string6.'<input data-id="'.$book->page_yes.'" id="wpbooklist-delete-page-input" type="checkbox"/><label for="wpbooklist-edit-delete-page">Delete Page</label><br/>';
								}

								if($book->post_yes != null && $book->post_yes != 'false'){
									$string6 = $string6.'<input data-id="'.$book->post_yes.'" id="wpbooklist-delete-post-input" type="checkbox"/><label for="wpbooklist-edit-delete-post">Delete Post</label>';
								}

							$string6 = $string6.'</div>
						</div>
						<div class="wpbooklist-spinner" id="wpbooklist-spinner-'.$key.'"></div>
						<div class="wpbooklist-delete-result" id="wpbooklist-delete-result-'.$key.'"></div>
						<div class="wpbooklist-edit-form-div" id="wpbooklist-edit-form-div-'.$key.'">
							
						</div>
					</div></div>';
				}
			}
		}

		$string7 = '<div id="wpbooklist-edit_books-pagination-div">
						<div ';

						$string8 = '';
						if(count($this->books_actual) == 0){
							$string8 = 'style="opacity:0.3; pointer-events:none;"';
						}

						$string9 = ' data-limit="'.$this->limit.'" id="wpbooklist-edit-next-100">Next '.EDIT_PAGE_OFFSET.' Results<img class="wpbooklist-edit-book-icon-next" src="'.ROOT_IMG_ICONS_URL.'next-page.svg"/></div>
						<div data-limit="'.$this->limit.'" id="wpbooklist-edit-previous-100"><img class="wpbooklist-edit-book-icon-back" src="'.ROOT_IMG_ICONS_URL.'next-page.svg"/>Previous '.EDIT_PAGE_OFFSET.' Results</div>
					</div>
					<div class="wpbooklist-spinner" id="wpbooklist-spinner-pagination"></div>'.$divclose;

		return $string1.$string2.$string3.$string4.$string5.$string6.$string7.$string8.$string9;
	}


}

endif;