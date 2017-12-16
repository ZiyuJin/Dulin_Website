<?php
/**
 * WPBookList Front-End Library UI Class
 *
 * @author   Jake Evans
 * @category Front-End UI
 * @package  Includes/UI
 * @version  1
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

if ( ! class_exists( 'WPBookList_Front_End_Library_UI', false ) ) :
/**
 * WPBookList_Front_End_Library_UI Class.
 */
class WPBookList_Front_End_Library_UI {

	public $table;
	public $books_read;
	public $books_signed;
	public $books_first_edition;
	public $display_options_table;
	public $display_options_actual = array();
	public $quotes_table;
	public $category_count = 0;
	public $books_actual = array();
	public $quotes_actual = array();
	public $total_book_count = 0;
	public $total_pages_read_count = 0;
	public $total_category_count = 0;
	public $total_quotes_count = 0;
	public $total_book_read_count = 0;
	public $total_book_signed_count = 0;
	public $total_book_first_edition_count = 0;
	public $library_actual_string = '';
	public $library_pagination_string = '';
	public $final_category_array = array();
	public $action;

	public function __construct($which_table, $searchType = null, $searchTerm = null, $sort = null, $action = null) {
		# Set up variables and such we'll need throughout
		global $wpdb;
		$this->table = strtolower($which_table);

		// Setting action to take when image is clicked
		$this->action = $action;

		// Building display options table
		if($this->table == $wpdb->prefix.'wpbooklist_jre_saved_book_log'){
			$this->display_options_table = $wpdb->prefix.'wpbooklist_jre_user_options';
		} else {
			$temp = explode('_', $this->table);
			$temp = array_pop($temp);
			$this->display_options_table = $wpdb->prefix.'wpbooklist_jre_settings_'.strtolower($temp);
		}


		// Getting all display options
		$this->display_options_actual = $wpdb->get_row($wpdb->prepare("SELECT * FROM $this->display_options_table WHERE ID = %d", 1));


		// Getting quotes table name
		$this->quotes_table = $wpdb->prefix.'wpbooklist_jre_book_quotes';


		# Getting all books from table depending on search and sort settings
		// If no search term is set, build default query for entire library
		if(($searchTerm == null || $searchTerm == 'Search...') && $searchType == ''){
			$query = "SELECT * FROM $this->table";
		} else {
			$searchType = explode('-', $searchType);
			// If only one search type was specified
			if(sizeof($searchType) == 2){
				if($searchType[1] == 'title'){
					$query = "SELECT * FROM $this->table where title LIKE %s";
				}
				if($searchType[1] == 'author'){
					$query = "SELECT * FROM $this->table where author LIKE %s";
				}
				if($searchType[1] == 'category'){
					$query = "SELECT * FROM $this->table where category LIKE %s";
				}
			}

			// If two search types were specified
			if(sizeof($searchType) == 3){
				if(in_array('title', $searchType) && in_array('author', $searchType)){
					$query = "SELECT * FROM $this->table where title LIKE %s OR author LIKE %s";
				}

				if(in_array('title', $searchType) && in_array('category', $searchType)){
					$query = "SELECT * FROM $this->table where title LIKE %s OR category LIKE %s";
				}

				if(in_array('author', $searchType) && in_array('category', $searchType)){
					$query = "SELECT * FROM $this->table where author LIKE %s OR category LIKE %s";
				}
			}

			// If all three search terms were specified
			if(sizeof($searchType) > 3 || sizeof($searchType) == 1){
				$query  = "SELECT * FROM $this->table where author LIKE %s OR category LIKE %s OR title LIKE %s";
			}
		}

		# Build second part of query if a sort option is set
		if($sort != null && $sort != 'default'){
			// Handles the sorting if one of the dynamic categories is selected
			if($sort != 'first-edition' && $sort != 'alphabetically' && $sort != 'year-read' && $sort != 'signed' && $sort != 'pages-desc' && $sort != 'pages-asc'){
				$sortTerm = 'cat';
			} else {
				$sortTerm = $sort;
			}

			// If a search term was not set, then append to first part of query without any 'AND's
			if(($searchTerm == null || $searchTerm == 'Search...') && $searchType == ''){
				switch ($sortTerm) {
	                case "first-edition":
	                    $query = $query." WHERE first_edition = 'true' ORDER BY title ASC";
	                    break;
	                case "alphabetically":
	                    $query = $query." ORDER BY title ASC";
	                    break;
	                case "year-read":
	                    $query = $query." WHERE finished = 'true' ORDER BY date_finished ASC";
	                    break;
	                case "signed":
	                    $query = $query." WHERE signed = 'yes' ORDER BY title ASC";
	                    break;
	                case "pages-desc":
	                    $query = $query." ORDER BY pages DESC";
	                    break;
	                case "pages-asc":
	                    $query = $query." ORDER BY pages ASC";
	                    break;
	                case "cat":
	                    $query = $query." WHERE category = %s";
	                    break;		       
            	}
			} else {
				// If a search term was set, then append to first part of query with 'AND's
				switch ($sort) {
	                case "first_edition":
	                    $query = $query." AND first_edition = 'true' ORDER BY title ASC";
	                    break;
	                case "alphabetically":
	                    $query = $query." ORDER BY title ASC";
	                    break;
	                case "year_read":
	                    $query = $query." AND finished = 'true' ORDER BY date_finished ASC";
	                    break;
	                case "signed":
	                    $query = $query." AND signed = 'yes' ORDER BY title ASC";
	                    break;
	                case "pages_desc":
	                    $query = $query." ORDER BY pages DESC";
	                    break;
	                case "pages_asc":
	                    $query = $query." ORDER BY pages ASC";
	                    break;
	                default:
	                	$query = $query." AND category = %s";					       
	            }
			}
		}

		// Determine how many '%s' palceholders are in query string for wpdb->prepare
		$placeholder = array();
		$count = substr_count($query, '%s');
		if($count >= 1){
			// If at least one placeholder was found...
			$searchTerm = '%'.$wpdb->esc_like($searchTerm).'%';
			if(($searchTerm == null || $searchTerm == 'Search...') && $searchType == ''){
				// If search term was not set, and one placeholder was found, then query must pertain to sort
				$this->books_actual = $wpdb->get_results($wpdb->prepare($query, $sort));
			} else {
				// If one placeholder was found, and query does or does not contains 'WHERE Category'...
				if($count == 1){ 
					if(strpos($query, 'WHERE category = %s') !== false){
						$this->books_actual = $wpdb->get_results($wpdb->prepare($query, $sort));
					} else {
						$this->books_actual = $wpdb->get_results($wpdb->prepare($query, $searchTerm));
					}
				}

				// 2 placeholders
				if($count == 2){
					$this->books_actual = $wpdb->get_results($wpdb->prepare($query, $searchTerm, $searchTerm));
				}

				// 3 placeholders
				if($count == 3){
					$this->books_actual = $wpdb->get_results($wpdb->prepare($query, $searchTerm, $searchTerm, $searchTerm));
				}
			}
		} else {
			// If no placeholders are found in query, execute the query
			$this->books_actual = $wpdb->get_results($query);
		}

		// Getting total book count
		$this->total_book_count = $wpdb->num_rows;


		// Getting total # of books read/finished
		foreach($this->books_actual as $book){
			if($book->finished == 'true'){
				$this->total_book_read_count++;
			}
		}


		// Getting total # of pages read/finished
		foreach($this->books_actual as $book){
			if($book->finished == 'true'){
				$this->total_pages_read_count = $this->total_pages_read_count+$book->pages;
			}
		}


		// Getting total # of books signed
		foreach($this->books_actual as $book){
			if($book->signed == 'true'){
				$this->total_book_signed_count++;
			}
		}


		// Getting total # of books first edition
		foreach($this->books_actual as $book){
			if($book->first_edition == 'true'){
				$this->total_book_first_edition_count++;
			}
		}


		// Creating a unique list of categories
		$temp_category_array = array();
		foreach($this->books_actual as $key=>$book){
			array_push($temp_category_array, $book->category);
		}
		$temp_category_array = array_unique($temp_category_array);
		foreach($temp_category_array as $cat){
			if($cat != ''){
				array_push($this->final_category_array, $cat);
			}
		}
		sort($this->final_category_array);
		$this->total_category_count = sizeof($this->final_category_array);


		// Getting quotes
		$this->quotes_table = $wpdb->prefix.'wpbooklist_jre_book_quotes';
		$this->quotes_actual = $wpdb->get_results("SELECT * FROM $this->quotes_table");


		// Getting number of quotes
		$this->total_quotes_count = $wpdb->num_rows;


		// Output default non-variable first bit of html
		$this->output_beginning_html();


		// Output sort by drop-down if not hidden
		if($this->display_options_actual->hidesortby != 1){
			$this->output_sort_by();
		}


		// Output search if not hidden
		if($this->display_options_actual->hidesearch != 1){
			$this->output_search();
		}


		// Output the closure of the sort and search elements
		$this->output_close_sort_search();


		// Output Stats if not hidden
		if($this->display_options_actual->hidestats != 1){
			$this->output_stats();
		}


		// Output Quote if not hidden
		if($this->display_options_actual->hidequote != 1){
			$this->output_quote();
		}

		// Build Library actual output string
		//$this->build_library_actual();

		// Build library pagination
		//$this->build_library_pagination();

		// Output Library and pagination strings
		//$this->output_library_actual();
	}

	private function output_beginning_html(){
		echo '<div class="wpbooklist-top-container">
    			<div class="wpbooklist-table-for-app">'.$this->table.'</div>
    			<p id="specialcaseforappid"></p>
				<a id="hidden-link-for-styling" style="display:none"></a>
				<div id="wpbooklist-sort-search-div">';
	}

	private function output_sort_by(){
		$string1 = '<div id="wpbooklist-select-sort-div">
                        <select id="wpbooklist-sort-select-box">    
                            <option selected disabled>'.__('Sort By...', 'wpbooklist').'</option>
                            <option value="default">'.__('Default', 'wpbooklist').'</option>
                            <option value="alphabetically">'.__('Alphabetically', 'wpbooklist').'</option>
                            <option value="year-read">'.__('Year Finished', 'wpbooklist').'</option>
                            <option value="signed">'.__('Signed', 'wpbooklist').'</option>
                            <option value="first-edition">'.__('First Edition', 'wpbooklist').'</option>
                            <option value="pages-desc">'.__('Pages (Descending)', 'wpbooklist').'</option>
                            <option value="pages-asc">'.__('Pages (Ascending)', 'wpbooklist').'</option>
                            <optgroup label="Categories">';

                            $string2 = '';
                            foreach($this->final_category_array as $cat){
                            	$string2 = $string2.'<option value="'.$cat.'">'.$cat.'</option>';
                            }

                            $string3 = '</optgroup>
                        </select>
                    </div>';

        echo $string1.$string2.$string3;
	}

	private function output_search(){
		$string1 = '<div id="wpbooklist-search-div">
                    	<div id="wpbooklist-search-checkboxes">
	                        <p>'.__('Search By','wpbooklist').':
	                            <input id="wpbooklist-book-title-search" type="checkbox" name="book-title-search" value="book-title-search">'.__('Title', 'wpbooklist').'</input>
	                            <input id="wpbooklist-author-search" type="checkbox" name="author-search" value="author-search">'.__('Author','wpbooklist').'</input>
	                            <input id="wpbooklist-cat-search" type="checkbox" name="cat-search" value="author-search">'.__('Category','wpbooklist').'</input>
	                        </p>
                    	</div>
                    	<div>
                    		<input id="wpbooklist-search-text" type="text" name="search-query" value="'.__('Search...','wpbooklist').'">
                    	</div>
	                    <div id="wpbooklist-search-submit">
	                        <input disabled data-table="'.$this->table.'" id="wpbooklist-search-sub-button" type="button" name="search-button" value="'.__('Search','wpbooklist').'"></input>
	                    </div>
                	</div>';

        echo $string1;
	}

	private function output_close_sort_search(){
		echo '</div>';
	}

	private function output_stats(){
		$string1 = '
		<div class="wpbooklist_stats_tdiv">
         	<p class="wpbooklist_control_panel_stat">'.__('Total Books:','wpbooklist').' '.number_format($this->total_book_count).'</p>
            <p class="wpbooklist_control_panel_stat">'.__('Finished:','wpbooklist').' '.number_format($this->total_book_read_count).'</p>
            <p class="wpbooklist_control_panel_stat">'.__('Signed:','wpbooklist').' '.number_format($this->total_book_signed_count).'</p>
            <p class="wpbooklist_control_panel_stat">'.__('First Editions:','wpbooklist').' '.number_format($this->total_book_first_edition_count).'</p>
            <p class="wpbooklist_control_panel_stat">'.__('Total Pages Read:','wpbooklist').' '.number_format($this->total_pages_read_count).'</p>
            <p class="wpbooklist_control_panel_stat">'.__('Categories:','wpbooklist').' '.number_format($this->total_category_count).'</p>
            <p class="wpbooklist_control_panel_stat">'.__('Library Completion:','wpbooklist').' ';

        if(($this->total_book_read_count == 0) || ($this->total_book_count == 0)){
        	$string2 = "0%";
        } else {
        	$string2 = number_format((($this->total_book_read_count/$this->total_book_count)*100), 2)."%";
        }

        $string3 = '</p>
        </div>';

        echo $string1.$string2.$string3;
	}

	private function output_quote(){
		$quote_num = rand(0,$this->total_quotes_count);
		if($quote_num != null){
		    $quote_actual = $this->quotes_actual[$quote_num]->quote;
		    $pos = strpos($quote_actual,'" - ');
		    $attribution = substr($quote_actual, $pos);
		    $quote = substr($quote_actual, 0, $pos);
		    echo '<div class="wpbooklist-ui-quote-area-div">
	    		<p class="wpbooklist-ui-quote-area-p">
	    			<span id="wpbooklist-quote-actual">'.stripslashes($quote).'</span>
	    			<span id="wpbooklist-attribution-actual">'.stripslashes($attribution).'</span>
	    		</p>
	    	  </div>';
	    }
	}

	public function build_library_actual($offset){

		//TODO: introduce functionality to account for pagination, default sorting, and frontend sorting

		//$this->display_options_actual->booksonpage;

		// Default sorting - sorts by IDs from low to high
		function compare_ids($a, $b){
			return $a->ID - $b->ID;
		}
		usort($this->books_actual, "compare_ids");

		switch ($this->display_options_actual->sortoption) {
			case 'alphabetically':
				function compare1($a, $b){
    				return strcmp($a->title, $b->title);
				}
				usort($this->books_actual, "compare1");
			break;
			case 'pages_desc':
				function compare2($b, $a){
    				return $a->pages - $b->pages;
				}
				usort($this->books_actual, "compare2");
			break;
			case 'pages_asc':
				function compare3($a, $b){
    				return $a->pages - $b->pages;
				}
				usort($this->books_actual, "compare3");
			break;
			case 'year_read':
				// First get all books that have been finished, then sort those, then display those first, and then the unfinished ones
				$finished_array = array();
				foreach($this->books_actual as $key=>$book){
					if($book->finished == 'true'){
						array_push($finished_array, $book);
						unset($this->books_actual[$key]);
					}
				}
				function compare4($a, $b){
    				return strcmp($a->date_finished, $b->date_finished);
				}
				usort($finished_array, "compare4");
				$this->books_actual = array_merge($finished_array, $this->books_actual);
			break;
			case 'signed':
				// First get all books that have been finished, then sort those, then display those first, and then the unfinished ones
				$signed_array = array();
				foreach($this->books_actual as $key=>$book){
					if($book->signed == 'true'){
						array_push($signed_array, $book);
						unset($this->books_actual[$key]);
					}
				}
				function compare5($a, $b){
    				return strcmp($a->signed, $b->signed);
				}
				usort($signed_array, "compare5");
				$this->books_actual = array_merge($signed_array, $this->books_actual);
			break;
			case 'first_edition':
				// First get all books that have been finished, then sort those, then display those first, and then the unfinished ones
				$edition_array = array();
				foreach($this->books_actual as $key=>$book){
					if($book->first_edition == 'true'){
						array_push($edition_array, $book);
						unset($this->books_actual[$key]);
					}
				}
				function compare6($a, $b){
    				return strcmp($a->first_edition, $b->first_edition);
				}
				usort($edition_array, "compare6");
				$this->books_actual = array_merge($edition_array, $this->books_actual);
			break;
			
			default:
				# code...
				break;
		}

		$this->books_actual;

		$string1 = '<div id="wpbooklist_main_display_div">';

		$onpage_key = 1;
		$string2 = '';
		foreach($this->books_actual as $key=>$book){
			if($key >= $offset){ 
				if($onpage_key <= $this->display_options_actual->booksonpage){

					// Displaying books based on provided action
					if($this->action == 'amazon'){
						$string2 = $string2.'<div class="wpbooklist_entry_div">
		                <p style="display:none;" id="wpbooklist-hidden-isbn1">'.$book->isbn.'</p>
		                <div class="wpbooklist_inner_main_display_div">
		                    <a href="'.$book->amazon_detail_page.'"><img class="wpbooklist_cover_image_class" data-bookid="'.$book->ID.'" data-booktable="'.$this->table.'" id="wpbooklist_cover_image" src="'.$book->image.'" style="opacity: 1;"></a>
		                    <span class="hidden_id_title">'.$book->ID.'</span>
		                    <a href="'.$book->amazon_detail_page.'"><p class="wpbooklist_saved_title_link" data-bookid="'.$book->ID.'" data-booktable="'.$this->table.'" id="wpbooklist_saved_title_link">'.stripslashes($book->title).'<span class="hidden_id_title">1</span>
		                    </p></a>';
		            } else if($this->action == 'googlebooks'){
		            	$string2 = $string2.'<div class="wpbooklist_entry_div">
		                <p style="display:none;" id="wpbooklist-hidden-isbn1">'.$book->isbn.'</p>
		                <div class="wpbooklist_inner_main_display_div">
		                    <a href="'.$book->google_preview.'"><img class="wpbooklist_cover_image_class" data-bookid="'.$book->ID.'" data-booktable="'.$this->table.'" id="wpbooklist_cover_image" src="'.$book->image.'" style="opacity: 1;"></a>
		                    <span class="hidden_id_title">'.$book->ID.'</span>
		                    <a href="'.$book->google_preview.'"><p class="wpbooklist_saved_title_link" data-bookid="'.$book->ID.'" data-booktable="'.$this->table.'" id="wpbooklist_saved_title_link">'.stripslashes($book->title).'<span class="hidden_id_title">1</span>
		                    </p></a>';

		            } else if($this->action == 'ibooks'){
		            	$string2 = $string2.'<div class="wpbooklist_entry_div">
		                <p style="display:none;" id="wpbooklist-hidden-isbn1">'.$book->isbn.'</p>
		                <div class="wpbooklist_inner_main_display_div">
		                    <a href="'.$book->itunes_page.'"><img class="wpbooklist_cover_image_class" data-bookid="'.$book->ID.'" data-booktable="'.$this->table.'" id="wpbooklist_cover_image" src="'.$book->image.'" style="opacity: 1;"></a>
		                    <span class="hidden_id_title">'.$book->ID.'</span>
		                    <a href="'.$book->itunes_page.'"><p class="wpbooklist_saved_title_link" data-bookid="'.$book->ID.'" data-booktable="'.$this->table.'" id="wpbooklist_saved_title_link">'.stripslashes($book->title).'<span class="hidden_id_title">1</span>
		                    </p></a>';

		            } else if($this->action == 'booksamillion'){
		            	$string2 = $string2.'<div class="wpbooklist_entry_div">
		                <p style="display:none;" id="wpbooklist-hidden-isbn1">'.$book->isbn.'</p>
		                <div class="wpbooklist_inner_main_display_div">
		                    <a href="http://www.anrdoezrs.net/links/8090484/type/dlg/'.$book->bam_link.'?id=7059442747215"><img class="wpbooklist_cover_image_class" data-bookid="'.$book->ID.'" data-booktable="'.$this->table.'" id="wpbooklist_cover_image" src="'.$book->image.'" style="opacity: 1;"></a>
		                    <span class="hidden_id_title">'.$book->ID.'</span>
		                    <a href="http://www.anrdoezrs.net/links/8090484/type/dlg/'.$book->bam_link.'?id=7059442747215"><p class="wpbooklist_saved_title_link" data-bookid="'.$book->ID.'" data-booktable="'.$this->table.'" id="wpbooklist_saved_title_link">'.stripslashes($book->title).'<span class="hidden_id_title">1</span>
		                    </p></a>';

		            } else if($this->action == 'kobo'){
		            	$string2 = $string2.'<div class="wpbooklist_entry_div">
		                <p style="display:none;" id="wpbooklist-hidden-isbn1">'.$book->isbn.'</p>
		                <div class="wpbooklist_inner_main_display_div">
		                    <a href="'.$book->kobo_link.'"><img class="wpbooklist_cover_image_class" data-bookid="'.$book->ID.'" data-booktable="'.$this->table.'" id="wpbooklist_cover_image" src="'.$book->image.'" style="opacity: 1;"></a>
		                    <span class="hidden_id_title">'.$book->ID.'</span>
		                    <a href="'.$book->kobo_link.'"><p class="wpbooklist_saved_title_link" data-bookid="'.$book->ID.'" data-booktable="'.$this->table.'" id="wpbooklist_saved_title_link">'.stripslashes($book->title).'<span class="hidden_id_title">1</span>
		                    </p></a>';
		            } else {
		            	$string2 = $string2.'<div class="wpbooklist_entry_div">
		                <p style="display:none;" id="wpbooklist-hidden-isbn1">'.$book->isbn.'</p>
		                <div class="wpbooklist_inner_main_display_div">
		                    <img class="wpbooklist_cover_image_class wpbooklist-show-book-colorbox" data-bookid="'.$book->ID.'" data-booktable="'.$this->table.'" id="wpbooklist_cover_image" src="'.$book->image.'" style="opacity: 1;">
		                    <span class="hidden_id_title">'.$book->ID.'</span>
		                    <p class="wpbooklist_saved_title_link wpbooklist-show-book-colorbox" data-bookid="'.$book->ID.'" data-booktable="'.$this->table.'" id="wpbooklist_saved_title_link">'.stripslashes($book->title).'<span class="hidden_id_title">1</span>
		                    </p>';
		            }

		                    if($this->display_options_actual->hiderating != 1 && $book->rating != 0 && $book->rating != null){

		                    	if($book->rating == 1){
		                    		$string2 = $string2.'<img style="opacity: 1;" class="wpbooklist-rating-image" src="'.ROOT_IMG_URL.'1star.png">';
		                    	}

		                    	if($book->rating == 2){
		                    		$string2 = $string2.'<img style="opacity: 1;" class="wpbooklist-rating-image" src="'.ROOT_IMG_URL.'2star.png">';
		                    	}

		                    	if($book->rating == 3){
		                    		$string2 = $string2.'<img style="opacity: 1;" class="wpbooklist-rating-image" src="'.ROOT_IMG_URL.'3star.png">';
		                    	}

		                    	if($book->rating == 4){
		                    		$string2 = $string2.'<img style="opacity: 1;" class="wpbooklist-rating-image" src="'.ROOT_IMG_URL.'4star.png">';
		                    	}

		                    	if($book->rating == 5){
		                    		$string2 = $string2.'<img style="opacity: 1;" class="wpbooklist-rating-image" src="'.ROOT_IMG_URL.'5star.png">';
		                    	}

		                    }

		                    $string2 = $string2.'<div class="wpbooklist-library-frontend-purchase-div">';

		                    $sales_array = array($book->author_url,$book->price);
		                    if($this->display_options_actual->enablepurchase == 1 && ($book->price != null && $book->price != '') && $this->display_options_actual->hidefrontendbuyprice != 1){
			                    if(has_filter('wpbooklist_append_to_frontend_library_price_purchase')) {
									$string2 = $string2.apply_filters('wpbooklist_append_to_frontend_library_price_purchase', $sales_array);
								}
							}

							if($this->display_options_actual->enablepurchase == 1 && $book->author_url != '' && $this->display_options_actual->hidefrontendbuyimg != 1){
			                    if(has_filter('wpbooklist_append_to_frontend_library_image_purchase')) {
									$string2 = $string2.apply_filters('wpbooklist_append_to_frontend_library_image_purchase', $sales_array);
								}
							}

		                    $string2 = $string2.'</div></div></div>';

		            $onpage_key++;
		        }
	    	}
		}

		$string3 = '</div>';

		$this->library_actual_string = $string1.$string2;

		$this->build_library_pagination();
		$this->output_library_actual();

	}

	private function build_library_pagination(){

		$string1 = '<div id="wpbooklist-pagination-div">';

		if($this->total_book_count > 0 && $this->display_options_actual->booksonpage > 0){
			$whole_pages = floor($this->total_book_count/$this->display_options_actual->booksonpage);
			$remainder_pages = $this->total_book_count%$this->display_options_actual->booksonpage;

			// If there's only one page, don't show pagination
			if($whole_pages >= 1){
				for($i = 0; $i <= $whole_pages; $i++){

					$remainder = $this->total_book_count-($i*$this->display_options_actual->booksonpage);

					if( ((($i+1)*$this->display_options_actual->booksonpage)-($this->display_options_actual->booksonpage-1)) >   $this->total_book_count){
						$string1 = $string1.'</div>';
						$this->library_pagination_string = $string1;
						return;
					}

					if($remainder > $this->display_options_actual->booksonpage){
						if(((($i+1)*$this->display_options_actual->booksonpage)-($this->display_options_actual->booksonpage-1)) == (($i+1)*$this->display_options_actual->booksonpage)){
								$string1 = $string1.'<div data-library="'.$this->table.'" data-page="'.$i.'" data-per-page="'.$this->display_options_actual->booksonpage.'" class="wpbooklist-pagination-page-div" id="wpbooklist-pagination-page-'.$i.'"> '.((($i+1)*$this->display_options_actual->booksonpage)-($this->display_options_actual->booksonpage-1)).' </div>';
						} else {
							$string1 = $string1.'<div data-library="'.$this->table.'" data-page="'.$i.'" data-per-page="'.$this->display_options_actual->booksonpage.'" class="wpbooklist-pagination-page-div" id="wpbooklist-pagination-page-'.$i.'"> '.((($i+1)*$this->display_options_actual->booksonpage)-($this->display_options_actual->booksonpage-1)).'-'.(($i+1)*$this->display_options_actual->booksonpage).' </div>';
						}

					} else {
						// This if displays just the one book number if there's only one book on the next page
						if(((($i+1)*$this->display_options_actual->booksonpage)-($this->display_options_actual->booksonpage-1)) == $this->total_book_count){
							$string1 = $string1.'<div data-library="'.$this->table.'" data-page="'.$i.'" data-per-page="'.$this->display_options_actual->booksonpage.'" class="wpbooklist-pagination-page-div" id="wpbooklist-pagination-page-'.$i.'"> '.((($i+1)*$this->display_options_actual->booksonpage)-($this->display_options_actual->booksonpage-1)).' </div>';
						} else {
							$string1 = $string1.'<div data-library="'.$this->table.'" data-page="'.$i.'" data-per-page="'.$this->display_options_actual->booksonpage.'" class="wpbooklist-pagination-page-div" id="wpbooklist-pagination-page-'.$i.'"> '.((($i+1)*$this->display_options_actual->booksonpage)-($this->display_options_actual->booksonpage-1)).'-'.$this->total_book_count.' </div>';
						}
					}
				}
			}
		}

		$string1 = $string1.'</div>';

		$this->library_pagination_string = $string1;
	}

	private function output_library_actual(){
		echo $this->library_actual_string.$this->library_pagination_string;
	}


}


endif;

?>