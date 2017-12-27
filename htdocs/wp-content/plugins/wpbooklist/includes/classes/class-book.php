<?php
/**
 * WPBookList Book Class
 * Handles functions for:
 * - Saving a Book to database
 * - Editing existing books
 * - Deleting Existing books
 * @author   Jake Evans
 * @category Root Product
 * @package  Includes/Classes
 * @version  1
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

if ( ! class_exists( 'WPBookList_Book', false ) ) :
/**
 * WPBookList_Book Class.
 */
class WPBookList_Book {

	public $add_result;
	public $edit_result;
	public $delete_result;
	public $title;
	public $amazon_array = array();
	public $library;
	public $retrieved_book;
	public $options_results;
	public $review_iframe;
	public $isbn;
	public $woocommerce;
	public $woofile;
	public $wooid;
	public $book_page;

	public function __construct($action = null, $book_array = null, $id = null) {
		
		if(($action == 'add' || $action == 'edit' || $action == 'search' || $action == 'bookfinder-colorbox') && $book_array != null){
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
			$this->copies = $book_array['copies'];
			$this->post_yes = $book_array['post_yes'];
			$this->page_id = $book_array['page_id'];
			$this->post_id = $book_array['post_id'];
			$this->lendable = $book_array['lendable'];
			$this->itunes_page = $book_array['itunes_page'];
			$this->google_preview = $book_array['google_preview'];
			$this->amazon_detail_page = $book_array['amazon_detail_page'];
			$this->review_iframe = $book_array['review_iframe'];
			$this->similar_products = $book_array['similar_products'];
			$this->woocommerce = $book_array['woocommerce'];
			$this->saleprice = $book_array['saleprice'];
			$this->regularprice = $book_array['regularprice'];
			$this->stock = $book_array['stock'];
			$this->length = $book_array['length'];
			$this->width = $book_array['width'];
			$this->height = $book_array['height'];
			$this->weight = $book_array['weight'];
			$this->sku = $book_array['sku'];
			$this->virtual = $book_array['virtual'];
			$this->download = $book_array['download'];
			$this->book_uid = $book_array['book_uid'];
			$this->woofile = $book_array['woofile'];
			$this->salebegin = $book_array['salebegin'];
			$this->saleend = $book_array['saleend'];
			$this->purchasenote = $book_array['purchasenote'];
			$this->productcategory = $book_array['productcategory'];
			$this->reviews = $book_array['reviews'];
			$this->upsells = $book_array['upsells'];
			$this->crosssells = $book_array['crosssells'];
			$this->id = $id;
		}

		if($action == 'add'){
			$this->add_book();
		}

		if($action == 'edit'){
			$this->id = $id;
			$this->edit_book();
		}

		if($action == 'delete'){
			$this->id = $id;
			$this->delete_book();
		}

		if($action == 'search'){
			$this->book_page = $book_array['book_page'];
			$this->amazon_auth_yes = $book_array['amazon_auth_yes'];
			if($this->amazon_auth_yes == 'true' && $this->use_amazon_yes == 'true'){
				$this->go_amazon === true;
				$this->gather_amazon_data();
				$this->gather_google_data();
				$this->gather_open_library_data();
				$this->gather_itunes_data();
				$this->create_buy_links();
			} else {
				// If $this->go_amazon is false, query the other apis and add the provided data to database
				$this->go_amazon === false;
				$this->gather_google_data();
				$this->gather_open_library_data();
				$this->gather_itunes_data();
				$this->create_buy_links();
			}
		}

		if($action == 'bookfinder-colorbox'){
			$this->gather_google_data();
			$this->gather_open_library_data();
			$this->gather_itunes_data();
			$this->create_buy_links();

			
		}
	}

	private function create_buy_links(){
		error_log('Entered create buy links function)');

		// Building Kobo Link
		$opts = array('http' =>
		    array(
		        'follow_location' => 1,
		        'timeout' => 10
		    )
		);

		$result = '';
		$responsecode = '';
		if(function_exists('file_get_contents')){
			error_log('entered the 1st kobo file get contents');
    		@file_get_contents('http://store.kobobooks.com/en-ca/Search?Query='.$this->isbn, false, $context);
    		list($version, $status, $text) = explode(' ', $http_response_header[0], 3);
    		if($status == 200){
    			error_log('entered the 2nd kobo file get contents');
    			$result = file_get_contents('http://store.kobobooks.com/en-ca/Search?Query='.$this->isbn, false, $context);
    		}

    	}

    	if($result == ''){
    		if (function_exists('curl_init')){ 
    			error_log('entered the kobo curl');
				$ch = curl_init();
				curl_setopt($ch, CURLOPT_HEADER, 0);
				curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
				curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
				curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10); 
				curl_setopt($ch, CURLOPT_TIMEOUT, 10); //timeout in seconds
				$url = 'http://store.kobobooks.com/en-ca/Search?Query='.$this->isbn;
				curl_setopt($ch, CURLOPT_URL, $url);
				$result = curl_exec($ch);
				$responsecode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
				curl_close($ch);
	    	} else {
	        	//#TODO: Log an error here in a log class like maintainme's saying both file_get_contents and cURL aren't available.
	    	}
    	}

    	if(strpos($result, 'did not return any results') !== false){
    		$this->kobo_link = null;
    	} else {
    		$this->kobo_link = 'http://store.kobobooks.com/en-ca/Search?Query='.$this->isbn;
    	}


    	// Creating Books-a-Million link
    	$opts = array('http' =>
		    array(
		        'follow_location' => 1,
		        'timeout' => 10
		    )
		);

		$result = '';
		$responsecode = '';
		if(function_exists('file_get_contents')){
    		@file_get_contents('http://www.booksamillion.com/p/'.$this->isbn, false, $context);
    		list($version, $status, $text) = explode(' ', $http_response_header[0], 3);
    		if($status == 200){
    			$result = file_get_contents('http://www.booksamillion.com/p/'.$this->isbn, false, $context);
    		}

    	}

    	if($result == ''){
    		if (function_exists('curl_init')){ 
				$ch = curl_init();
				curl_setopt($ch, CURLOPT_HEADER, 0);
				curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
				curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
				curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10); 
				curl_setopt($ch, CURLOPT_TIMEOUT, 10); //timeout in seconds
				$url = 'http://www.booksamillion.com/p/'.$this->isbn;
				curl_setopt($ch, CURLOPT_URL, $url);
				$result = curl_exec($ch);
				$responsecode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
				curl_close($ch);
	    	} else {
	        	//#TODO: Log an error here in a log class like maintainme's saying both file_get_contents and cURL aren't available.
	    	}
    	}

    	if(strpos($result, 'SORRY, WE COULD NOT FIND THE REQUESTED PRODUCT') !== false){
    		$this->bam_link = null;
    	} else {
    		$this->bam_link = 'http://www.booksamillion.com/p/'.$this->isbn;
    	}





	}

	private function add_book(){
		// First do Amazon Authorization check
		if($this->amazon_auth_yes == 'true' && $this->use_amazon_yes == 'true'){
			$this->go_amazon === true;
			$this->gather_amazon_data();
			$this->gather_google_data();
			$this->gather_open_library_data();
			$this->gather_itunes_data();
			$this->create_buy_links();
			$this->set_default_woocommerce_data();
			$this->create_wpbooklist_woocommerce_product();
			$this->add_to_db();
		} else {
			// If $this->go_amazon is false, query the other apis and add the provided data to database
			$this->go_amazon === false;
			$this->gather_google_data();
			$this->gather_open_library_data();
			$this->gather_itunes_data();
			$this->create_buy_links();
			$this->set_default_woocommerce_data();
			$this->create_wpbooklist_woocommerce_product();
			$this->add_to_db();
		}
	}

	private function gather_amazon_data(){
		global $wpdb;

		// Get associate tag for creating API call post data
		$table_name_options = $wpdb->prefix . 'wpbooklist_jre_user_options';
  		$this->options_results = $wpdb->get_row($wpdb->prepare("SELECT * FROM $table_name_options", $table_name_options));

		$params = array();

		# Build Query
		// Determine Amazon region
		$region = '';
		switch ($this->options_results->amazoncountryinfo) {
	        case "au":
	        	$region = 'com';
	            break;
	        case "ca":
	        	$region = 'ca';
	            break;
	        case "fr":
	        	$region = 'fr';
	            break;
	        case "de":
	        	$region = 'de';
	            break;
	        case "in":
	        	$region = 'in';
	            break;
	        case "it":
	        	$region = 'it';
	            break;
	        case "jp":
	        	$region = 'co.jp';
	            break;
	        case "mx":
	        	$region = 'com.mx';
	            break;
	        case "es":
	        	$region = 'es';
	            break;
	        case "uk":
	        	$region = 'co.uk';
	            break;
	        default:
	        	$region = 'com';
	            //$this->amazon_detail_page = $saved_book->amazon_detail_page;//filter_var($saved_book->amazon_detail_page, FILTER_SANITIZE_URL);
	    }

		// If user has saved their own Amazon API Keys
		if($this->options_results->amazonapisecret != null && $this->options_results->amazonapisecret != '' && $this->options_results->amazonapipublic != null && $this->options_results->amazonapipublic != ''){
			$postdata = http_build_query(
			  array(
			      'isbn' => $this->isbn,
			      'associate_tag' => $this->options_results->amazonaff,
			      'book_title' => $this->title,
			      'book_author' => $this->author,
			      'book_page' => $this->book_page,
			      'region' => $region,
			      'api_secret'=>$this->options_results->amazonapisecret,
			      'api_public'=>$this->options_results->amazonapipublic
			  )
			);
		} else {
			$postdata = http_build_query(
			  array(
			      'isbn' => $this->isbn,
			      'associate_tag' => $this->options_results->amazonaff,
			      'book_title' => $this->title,
			      'book_author' => $this->author,
			      'book_page' => $this->book_page,
			      'region' => $region,
			  )
			);
		}
		$opts = array('http' =>
		  array(
		      'method'  => 'POST',
		      'header'  => 'Content-type: application/x-www-form-urlencoded',
		      'content' => $postdata
		  )
		);

		$context = stream_context_create($opts);
		$result = null;


		$result = '';
    	if(function_exists('file_get_contents')){
    		@file_get_contents('https://jakerevans.com/awsapiconfig.php', false, $context);
    		list($version, $status, $text) = explode(' ', $http_response_header[0], 3);
    		if($status == 200){
    			$result = file_get_contents('https://jakerevans.com/awsapiconfig.php', false, $context);
    		}

    	}

    	if($result == ''){
    		if (function_exists('curl_init')){ 
				$ch = curl_init();
				curl_setopt($ch, CURLOPT_HEADER, 0);
				curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
				$url = 'https://jakerevans.com/awsapiconfig.php';
				curl_setopt($ch, CURLOPT_URL, $url);

				if($this->options_results->amazonapisecret != null && $this->options_results->amazonapisecret != '' && $this->options_results->amazonapipublic != null && $this->options_results->amazonapipublic != ''){
					$data = array('api_public'=>$this->options_results->amazonapipublic, 'api_secret'=>$this->options_results->amazonapisecret, 'book_page' => $this->book_page, 'book_title' => $this->title, 'book_author' => $this->author, 'associate_tag' => $this->options_results->amazonaff, 'isbn' => $this->isbn);
				} else {
					$data = array('book_title' => $this->title, 'associate_tag' => $this->options_results->amazonaff, 'isbn' => $this->isbn);
				}

				curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
				$result = curl_exec($ch);
				$responsecode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
				if($responsecode == 200){
				}
				curl_close($ch);
	    	} else {
	        	//#TODO: Log an error here in a log class like maintainme's saying both file_get_contents and cURL aren't available.
	    	}
    	}

    	// Convert result from API call to regular ol' array
		$xml = simplexml_load_string($result, 'SimpleXMLElement', LIBXML_NOCDATA);

		// if there was an error parsing the XML, try again by re-running the function
		if($xml === false){
			error_log('Calling Self');
			$this->gather_amazon_data();
		}
		$json = json_encode($xml);
		$this->amazon_array = json_decode($json,TRUE);
		# Begin assigning values from $this->amazon_array to properties

		// Get title
		if($this->title == null || $this->title == ''){
			$this->title = $this->amazon_array['Items']['Item'][0]['ItemAttributes']['Title'];
			if($this->title == null || $this->title == ''){
				$this->title = $this->amazon_array['Items']['Item']['ItemAttributes']['Title'];
			}
		}

		// Get cover image
		if($this->image == null || $this->image == ''){
			$this->image = $this->amazon_array['Items']['Item'][0]['LargeImage']['URL'];
			if($this->image == null || $this->image == ''){
				$this->image = $this->amazon_array['Items']['Item']['LargeImage']['URL'];
			}
		}

		// Get author
		$author_string = '';
		if($this->author == null || $this->author == ''){
			$this->author = $this->amazon_array['Items']['Item'][0]['ItemAttributes']['Author'];
			if(is_array($this->author)){
				foreach($this->author as $author){
					$author_string = $author_string.', '.$author;
				}
				$author_string = rtrim($author_string, ', ');
				$author_string = ltrim($author_string, ', ');
				$this->author = $author_string;
			}

			if($this->author == null || $this->author == ''){
				$this->author = $this->amazon_array['Items']['Item']['ItemAttributes']['Author'];
				if(is_array($this->author)){
					foreach($this->author as $author){
						$author_string = $author_string.', '.$author;
					}
					$author_string = rtrim($author_string, ', ');
					$author_string = ltrim($author_string, ', ');
					$this->author = $author_string;
				}
			}
		}

		// Getting pages
		if($this->pages == null || $this->pages == ''){
			$this->pages = $this->amazon_array['Items']['Item'][0]['ItemAttributes']['NumberOfPages'];
			if($this->pages == null || $this->pages == ''){
				$this->pages = $this->amazon_array['Items']['Item']['ItemAttributes']['NumberOfPages'];
			}
		}

		// Getting publication date
		if($this->pub_year == null || $this->pub_year == ''){
			$this->pub_year = $this->amazon_array['Items']['Item'][0]['ItemAttributes']['PublicationDate'];
			if($this->pub_year == null || $this->pub_year == ''){
				$this->pub_year = $this->amazon_array['Items']['Item']['ItemAttributes']['PublicationDate'];
			}
		}

		// Getting publisher
		if($this->publisher == null || $this->publisher == ''){
			$this->publisher = $this->amazon_array['Items']['Item'][0]['ItemAttributes']['Publisher'];
			if($this->publisher == null || $this->publisher == ''){
				$this->publisher = $this->amazon_array['Items']['Item']['ItemAttributes']['Publisher'];
			}
		}

		// Getting description
		if($this->description == null || $this->description == ''){
			$this->description = $this->amazon_array['Items']['Item'][0]['EditorialReviews']['EditorialReview']['Content'];
			if($this->description == null || $this->description == ''){
				$this->description = $this->amazon_array['Items']['Item']['EditorialReviews']['EditorialReview']['Content'];
				if($this->description == null || $this->description == ''){
					$this->description = $this->amazon_array['Items']['Item']['EditorialReviews']['EditorialReview'][0]['Content'];
				}
			}
		}

		// Getting amazon link
		if($this->amazon_detail_page == null || $this->amazon_detail_page == ''){
			$this->amazon_detail_page = $this->amazon_array['Items']['Item'][0]['DetailPageURL'];
			if($this->amazon_detail_page == null || $this->amazon_detail_page == ''){
				$this->amazon_detail_page = $this->amazon_array['Items']['Item']['DetailPageURL'];
			}
		}

		// Getting Amazon reviews iFrame
		if($this->review_iframe == null || $this->review_iframe == ''){
			$this->review_iframe = $this->amazon_array['Items']['Item'][0]['CustomerReviews']['IFrameURL'];
			if($this->review_iframe == null || $this->review_iframe == ''){
				$this->review_iframe = $this->amazon_array['Items']['Item']['CustomerReviews']['IFrameURL'];
			}
		}
		// Setting up iFrame to play with https
		if( isset($_SERVER['HTTPS'] ) ) {
            $pos = strpos($this->review_iframe, ':');
            $this->review_iframe = substr_replace($this->review_iframe, 'https', 0, $pos);
        }
        
        // Getting similar books
        $similarproductsstring = '';
		if($this->similar_products == null || $this->similar_products == ''){
			$this->similar_products = $this->amazon_array['Items']['Item'][0]['SimilarProducts']['SimilarProduct'];
			if(is_array($this->similar_products)){
				foreach($this->similar_products as $prod){
			      $similarproductsstring = $similarproductsstring.';bsp;'.$prod['ASIN'].'---'.$prod['Title'];

			    }
			    $this->similar_products = $similarproductsstring;
			}

			if($this->similar_products == null || $this->similar_products == ''){
				$this->similar_products = $this->amazon_array['Items']['Item']['SimilarProducts']['SimilarProduct'];
				if(is_array($this->similar_products)){
					foreach($this->similar_products as $prod){
				      $similarproductsstring = $similarproductsstring.';bsp;'.$prod['ASIN'].'---'.$prod['Title'];

				    }
				    $this->similar_products = $similarproductsstring;
				}
			}
		}
	}

	private function gather_google_data(){
		// If there's no ISBN # provided, there's no use in doing anything here
		if($this->isbn == null || $this->isbn == ''){
			return;
		}

		if($this->options_results->googleapi != null && $this->options_results->googleapi != ''){
			$google_api = $this->options_results->googleapi;
		} else {
			$google_api = 'AIzaSyBl6KEeKRddmhnK-jX65pGkjBW1Y6Q5_rM';
		}

		$result = '';
    	if(function_exists('file_get_contents')){
    		@file_get_contents('https://www.googleapis.com/books/v1/volumes?q=isbn:'.$this->isbn.'&key='.$google_api.'&country=US');
    		list($version, $status, $text) = explode(' ', $http_response_header[0], 3);
    		if($status == 200){
    			$result = file_get_contents('https://www.googleapis.com/books/v1/volumes?q=isbn:'.$this->isbn.'&key='.$google_api.'&country=US');
    		}

    	}

    	if($result == ''){
    		if (function_exists('curl_init')){ 
				$ch = curl_init();
				curl_setopt($ch, CURLOPT_HEADER, 0);
				curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
				$url = 'https://www.googleapis.com/books/v1/volumes?q=isbn:'.$this->isbn.'&key='.$google_api;
				curl_setopt($ch, CURLOPT_URL, $url);
				$result = curl_exec($ch);
				$responsecode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
				if($responsecode == 200){
				}
				curl_close($ch);
	    	} else {
	        	//#TODO: Log an error here in a log class like maintainme's saying both file_get_contents and cURL aren't available.
	    	}
    	}

    	// Convert result to array
    	$json_output_google = json_decode($result, true);

    	# Making sure we didn't miss any values from Amazon data grab
		if($this->author == null || $this->author == ''){
			$this->author  = $json_output_google['items'][0]['volumeInfo']['authors'][0];
		}

		if($this->image == null || $this->image == ''){
			$this->image = $json_output_google['items'][0]['volumeInfo']['imageLinks']['thumbnail'];
		}

		if($this->pages == null || $this->pages == ''){
			$this->pages = $json_output_google['items'][0]['volumeInfo']['pageCount'];
		}   

		if($this->pub_year == null || $this->pub_year == ''){
			$this->pub_year = $json_output_google['items'][0]['volumeInfo']['publishedDate'];
		}

		if($this->publisher == null || $this->publisher == ''){
			$this->publisher = $json_output_google['items'][0]['volumeInfo']['publisher'];
		}

		if($this->description == null || $this->description == ''){
			$this->description = $json_output_google['items'][0]['volumeInfo']['description'];
		}

		# Now getting new data
		$this->google_preview = $json_output_google['items'][0]['accessInfo']['webReaderLink'];

		if($this->category == null || $this->category == ''){
  			$this->category = $json_output_google['items'][0]['volumeInfo']['categories'][0];
  		}
	}

	private function gather_open_library_data(){

		// If there's no ISBN # provided, there's no use in doing anything here
		if($this->isbn == null || $this->isbn == ''){
			return;
		}


		$result = '';
    	if(function_exists('file_get_contents')){
    		@file_get_contents('https://openlibrary.org/api/books?bibkeys=ISBN:".$this->isbn."&jscmd=data&format=json');
    		list($version, $status, $text) = explode(' ', $http_response_header[0], 3);
    		if($status == 200){
    			$result = file_get_contents('https://openlibrary.org/api/books?bibkeys=ISBN:".$this->isbn."&jscmd=data&format=json');
    		}

    	}

    	if($result == ''){
    		if (function_exists('curl_init')){ 
				$ch = curl_init();
				curl_setopt($ch, CURLOPT_HEADER, 0);
				curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
				$url = "https://openlibrary.org/api/books?bibkeys=ISBN:".$this->isbn."&jscmd=data&format=json";
				curl_setopt($ch, CURLOPT_URL, $url);
				$responsecode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
				if($responsecode == 200){
				}
				curl_close($ch);
	    	} else {
	        	//#TODO: Log an error here in a log class like maintainme's saying both file_get_contents and cURL aren't available.
	    	}
    	}

    	$json_output_ol = json_decode($result, true);
    	$isbn_var = 'ISBN:'.$this->isbn; 

		if($this->author == null || $this->author == ''){
			$this->author = $json_output_ol['items'][0]['volumeInfo']['authors'][0];
		}

		if($this->image == null || $this->image == ''){
			$this->image = $json_output_ol[$isbn_var]['cover']['large'];
		}

		if($this->pages == null || $this->pages == ''){
			$this->pages = $json_output_ol[$isbn_var]['number_of_pages'];
		}   

		if($this->pub_year == null || $this->pub_year == ''){
			$this->pub_year = $json_output_ol[$isbn_var]['publish_date'];
		}

		if($this->publisher == null || $this->publisher == ''){
			$this->publisher = $json_output_ol[$isbn_var]['publishers'][0]['name'];
		}

		if($this->description == null || $this->description == ''){
			$this->description = $json_output_google['items'][0]['volumeInfo']['description'];
		}

		if($this->category == null || $this->category == ''){
			$this->category = $json_output_ol[$isbn_var]['subjects'][0]['name'];
		}
	}

	private function gather_itunes_data(){

		// If there's no ISBN # provided, there's no use in doing anything here
		if($this->isbn == null || $this->isbn == ''){
			return;
		}

		global $wpdb;

		// Get associate tag for creating API call post data
		$table_name_options = $wpdb->prefix . 'wpbooklist_jre_user_options';
  		$this->options_results = $wpdb->get_row($wpdb->prepare("SELECT * FROM $table_name_options", $table_name_options));


		$result = '';
    	if(function_exists('file_get_contents')){
    		@file_get_contents('https://itunes.apple.com/lookup?isbn='.$this->isbn.'&at='.$this->options_results->itunesaff);
    		list($version, $status, $text) = explode(' ', $http_response_header[0], 3);
    		if($status == 200){
    			$result = file_get_contents('https://itunes.apple.com/lookup?isbn='.$this->isbn.'&at='.$this->options_results->itunesaff);
    		}

    	}

    	if($result == ''){
    		if (function_exists('curl_init')){ 
				$ch = curl_init();
				curl_setopt($ch, CURLOPT_HEADER, 0);
				curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
				$url = 'https://itunes.apple.com/lookup?isbn='.$this->isbn.'&at='.$this->options_results->itunesaff;
				curl_setopt($ch, CURLOPT_URL, $url);
				$responsecode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
				if($responsecode == 200){
				}
				curl_close($ch);
	    	} else {
	        	//#TODO: Log an error here in a log class like maintainme's saying both file_get_contents and cURL aren't available.
	    	}
    	}

	  	$json_output_itunes = json_decode($result, true);
	  	$this->itunes_page = $json_output_itunes['results'][0]['trackViewUrl'];

		// If we didn't find the book via iBooks, let's search for the Audiobook via itunes
	  	if($this->itunes_page == null || $this->itunes_page == ''){
	  		$result = '';
	    	if(function_exists('file_get_contents')){
	    		$title = urlencode($this->title);
	    		@file_get_contents('https://itunes.apple.com/search?term='.$title.'&at='.$this->options_results->itunesaff);
	    		list($version, $status, $text) = explode(' ', $http_response_header[0], 3);
	    		if($status == 200){
	    			$result = file_get_contents('https://itunes.apple.com/search?term='.$title.'&at='.$this->options_results->itunesaff);
	    		}

	    	}

	  	}

	  	if($result == ''){
    		if (function_exists('curl_init')){ 
				$ch = curl_init();
				curl_setopt($ch, CURLOPT_HEADER, 0);
				curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
				$url = 'https://itunes.apple.com/search?term='.$title.'&at='.$this->options_results->itunesaff;
				curl_setopt($ch, CURLOPT_URL, $url);
				$responsecode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
				if($responsecode == 200){
				}
				curl_close($ch);
	    	} else {
	        	//#TODO: Log an error here in a log class like maintainme's saying both file_get_contents and cURL aren't available.
	    	}
    	}

	  	$json_output_itunes = json_decode($result, true);
	  	$this->itunes_page = $json_output_itunes['results'][0]['trackViewUrl'];
	}

	private function set_default_woocommerce_data(){
		global $wpdb;

		// Check to see if Storefront extension is active
		include_once( ABSPATH . 'wp-admin/includes/plugin.php' );
		if(is_plugin_active('wpbooklist-storefront/wpbooklist-storefront.php')){
			
			// Get saved settings
	    	$settings_table = $wpdb->prefix."wpbooklist_jre_storefront_options";
	    	$settings = $wpdb->get_row("SELECT * FROM $settings_table");

	    	if($this->saleprice == '' || $this->saleprice == null){
	    		$this->saleprice = $settings->defaultsaleprice;
	    	}

	    	if($this->regularprice == '' || $this->regularprice == null){
	    		$this->regularprice = $settings->defaultprice;
	    	}

	    	if($this->stock == '' || $this->stock == null){
	    		$this->stock = $settings->defaultstock;
	    	}

	    	if($this->length == '' || $this->length == null){
	    		$this->length = $settings->defaultlength;
	    	}

	    	if($this->width == '' || $this->width == null){
	    		$this->width = $settings->defaultwidth;
	    	}

	    	if($this->height == '' || $this->height == null){
	    		$this->height = $settings->defaultheight;
	    	}

	    	if($this->weight == '' || $this->weight == null){
	    		$this->weight = $settings->defaultweight;
	    	}

	    	if($this->sku == '' || $this->sku == null){
	    		$this->sku = $settings->defaultsku;
	    	}

	    	if($this->virtual == '' || $this->virtual == null){
	    		$this->virtual = $settings->defaultvirtual;
	    	}

	    	if($this->download == '' || $this->download == null){
	    		$this->download = $settings->defaultdownload;
	    	}

	    	if($this->salebegin == '-undefined-undefined' || $this->salebegin == null){
	    		$this->salebegin = $settings->defaultsalebegin;
	    	}

	    	if($this->saleend == '-undefined-undefined' || $this->saleend == null){
	    		$this->saleend = $settings->defaultsaleend;
	    	}

	    	if($this->purchasenote == '' || $this->purchasenote == null){
	    		$this->purchasenote = $settings->defaultnote;
	    	}

	    	if($this->productcategory == '' || $this->productcategory == null){
	    		$this->productcategory = $settings->defaultcategory;
	    	}

	    	if($this->upsells == '' || $this->upsells == null){
	    		$this->upsells = $settings->defaultupsell;
	    	}

	    	if($this->crosssells == '' || $this->crosssells == null){
	    		$this->crosssells = $settings->defaultcrosssell;
	    	}

		}

	}

	private function create_wpbooklist_woocommerce_product(){

		global $wpdb;

		if($this->woocommerce === 'true'){

			$price = '';
			if($this->price != null && $this->price != ''){
				if(!is_numeric($this->price[0])){
					$price = substr($this->price, 1);
				} else {
					$price = $this->price;
				}
			} else {
				if($this->regularprice != null && $this->regularprice != ''){
					if(!is_numeric($this->regularprice[0])){
						$price = substr($this->regularprice, 1);
					} else {
						$price = $this->regularprice;
					}
				} else {
					$price = '0.00';
				}
			}

			$woocommerce_existing_id = $wpdb->get_row($wpdb->prepare("SELECT * FROM $this->library WHERE ID = %d",$this->id ));
			
			include_once( STOREFRONT_CLASS_DIR . 'class-storefront-woocommerce.php');
  			$this->woocommerce = new WPBookList_StoreFront_WooCommerce($this->title, $this->description, $this->image, $price, $this->saleprice, $this->stock, $this->length, $this->width, $this->height, $this->weight, $this->sku, $this->virtual, $this->download, $this->woofile, $this->salebegin, $this->saleend, $this->purchasenote, $this->productcategory, $this->reviews, $woocommerce_existing_id->woocommerce, $this->upsells, $this->crosssells);

  			$this->wooid = $this->woocommerce->post_id;
  			error_log('Woocommerce post id:'.$this->woocommerce->post_id.' and '.$woocommerce_existing_id->woocommerce);

		}
	}

	private function add_to_db(){

		// Create a unique identifier for this book
		$this->book_uid = uniqid();

		if($this->page_yes || $this->post_yes){
			$page_post_array = array(
				'title' => $this->title, 
				'isbn' => $this->isbn,
				'author' => $this->author,
				'author_url' => $this->author_url,
				'price' => $this->price,
				'finished' => $this->finished,
				'date_finished' => $this->date_finished,
				'signed' => $this->signed,
				'first_edition' => $this->first_edition,
				'image' => $this->image,
				'pages' => $this->pages,
				'pub_year' => $this->pub_year,
				'publisher' => $this->publisher,
				'category' => $this->category,
				'description' => $this->description,
				'notes' => $this->notes,
				'rating' => $this->rating,
				'page_yes' => $this->page_yes,
				'post_yes' => $this->post_yes,
				'itunes_page' => $this->itunes_page,
				'google_preview' => $this->google_preview,
				'amazon_detail_page' => $this->amazon_detail_page,
				'review_iframe' => $this->review_iframe,
				'similar_products' => $this->similar_products,
				'book_uid' => $this->book_uid,
				'lendable' => $this->lendable,
				'copies' => $this->copies,
				'kobo_link' => $this->kobo_link,
				'bam_link' => $this->bam_link,
				'woocommerce' => $this->wooid
			);

			# Each of these class instantiations will return the ID of the page/post created for storage in DB
			$page = $this->page_yes;
			$post = $this->post_yes;

			if($this->post_yes == 'true'){
				require_once(CLASS_DIR.'class-post.php');
				$post = new WPBookList_Post($page_post_array);
				$post = $post->post_id;
			}

			
			if($this->page_yes == 'true'){
				require_once(CLASS_DIR.'class-page.php');
				$page = new WPBookList_Page($page_post_array);
				$page = $page->create_result;
			}

		}
error_log('Inside the add to db function');

		// Check to see if Storefront extension is active
		include_once( ABSPATH . 'wp-admin/includes/plugin.php' );
		if(is_plugin_active('wpbooklist-storefront/wpbooklist-storefront.php')){
			if($this->author_url == '' || $this->author_url == null){
				if($this->wooid != '' || $this->wooid != null){
					$this->author_url = get_permalink($this->wooid);

					if($this->price == null || $this->price == ''){
						$this->price = $this->regularprice;
					}
				}
			}
		}

		// Adding submitted values to the DB
		global $wpdb;
		$result = $wpdb->insert( $this->library, array(
          'title' => $this->title, 
          'isbn' => $this->isbn,
          'author' => $this->author,
          'author_url' => $this->author_url,
          'price' => $this->price,
          'finished' => $this->finished,
          'date_finished' => $this->date_finished,
          'signed' => $this->signed,
          'first_edition' => $this->first_edition,
          'image' => $this->image,
          'pages' => $this->pages,
          'pub_year' => $this->pub_year,
          'publisher' => $this->publisher,
          'category' => $this->category,
          'description' => $this->description,
          'notes' => $this->notes,
          'rating' => $this->rating,
          'page_yes' => $page,
          'post_yes' => $post,
          'itunes_page' => $this->itunes_page,
          'google_preview' => $this->google_preview,
          'amazon_detail_page' => $this->amazon_detail_page,
          'review_iframe' => $this->review_iframe,
          'similar_products' => $this->similar_products,
          'book_uid' => $this->book_uid,
          'lendable' => $this->lendable,
		  'copies' => $this->copies,
		  'kobo_link' => $this->kobo_link,
		  'bam_link' => $this->bam_link,
		  'woocommerce' => $this->wooid
          ),
        array(
              '%s',
              '%s',
              '%s',
              '%s',
              '%s',
              '%s',
              '%s',
              '%s',
              '%s',
              '%s',
              '%s',
              '%s',
              '%s',
              '%s',
              '%s',
              '%s',
              '%s',
              '%s',
              '%s',
              '%s',
              '%s',
              '%s',
              '%s',
              '%s',
              '%s',
              '%s',
              '%d',
              '%s',
              '%s',
              '%s'
          )   
  		);
error_log('Result of adding book is:'.$result);
		$this->add_result = $result;
		if($result == 1){
			$row = $wpdb->get_row($wpdb->prepare("SELECT * FROM $this->library WHERE book_uid = %s", $this->book_uid));
			$this->add_result = $this->add_result.','.$row->ID;
		}
		// TODO: Create a log class to record the result of adding the book - or maybe just record an error, if there is one. Make a link for the log file somehwere, on settings page perhaps, for user to download. 

		// Insert the Amazon Authorization into the DB if it's not already set to 'Yes'
		$table_name_options = $wpdb->prefix . 'wpbooklist_jre_user_options';
  		$this->options_results = $wpdb->get_row($wpdb->prepare("SELECT * FROM $table_name_options", $table_name_options));
  		if($this->options_results->amazonauth != 'true'){
			$data = array(
	        	'amazonauth' => $this->amazon_auth_yes
		    );
		    $format = array( '%s'); 
		    $where = array( 'ID' => 1 );
		    $where_format = array( '%d' );
		    $wpdb->update( $wpdb->prefix.'wpbooklist_jre_user_options', $data, $where, $format, $where_format );
		}

	}

	public static function display_edit_book_form(){

		// Perform check for previously-saved Amazon Authorization
		global $wpdb;
		$table_name = $wpdb->prefix . 'wpbooklist_jre_user_options';
		$opt_results = $wpdb->get_row("SELECT * FROM $table_name");

		$table_name = $wpdb->prefix . 'wpbooklist_jre_list_dynamic_db_names';
		$db_row = $wpdb->get_results("SELECT * FROM $table_name");

		// For grabbing an image from media library
		wp_enqueue_media();
	 	$string1 = '<div id="wpbooklist-editbook-container">
				<p><span ';

					if($opt_results->amazonauth == 'true'){ 
						$string2 = 'style="display:none;"';
					} else {
						$string2 = '';
					}

					$string3 = ' >You must check the box below to authorize <span class="wpbooklist-color-orange-italic">WPBookList</span> to gather data from Amazon, otherwise, the only data that will be added for your book is what you fill out on the form below. WPBookList uses it\'s own Amazon Product Advertising API keys to gather book data, but if you happen to have your own API keys, you can use those instead by adding them on the <a href="'.menu_page_url( 'WPBookList-Options-settings', false ).'&tab=amazon">Amazon Settings</a> page.</span></p>
          		<form id="wpbooklist-editbook-form" method="post" action="">
		          	<div id="wpbooklist-authorize-amazon-container">
		    			<table>';

		    			if($opt_results->amazonauth == 'true'){ 
							$string4 = '<tr style="display:none;"">
		    					<td><p id="auth-amazon-question-label">Authorize Amazon Usage?</p></td>
		    				</tr>
		    				<tr style="display:none;"">
		    					<td>
		    						<input checked type="checkbox" name="authorize-amazon-yes" />
		    						<label for="authorize-amazon-yes">Yes</label>
		    						<input type="checkbox" name="authorize-amazon-no" />
		    						<label for="authorize-amazon-no">No</label>
		    					</td>
		    				</tr>';
						} else {
							$string4 = '<tr>
		    					<td><p id="auth-amazon-question-label">Authorize Amazon Usage?</p></td>
		    				</tr>
		    				<tr>
		    					<td>
		    						<input type="checkbox" name="authorize-amazon-yes" />
		    						<label for="authorize-amazon-yes">Yes</label>
		    						<input type="checkbox" name="authorize-amazon-no" />
		    						<label for="authorize-amazon-no">No</label>
		    					</td>
		    				</tr>';
						}

		    			$string5 = '</table>
		    		</div>
	          		<div id="wpbooklist-use-amazon-container">
		    			<table>
		    				<tr>
		    					<td><p id="use-amazon-question-label">Automatically Gather Book Info From Amazon (ISBN/ASIN number required)?</p></td>
		    				</tr>
		    				<tr>
		    					<td style="text-align:center;">
		    						<input checked type="checkbox" name="use-amazon-yes" />
		    						<label for="use-amazon-yes">Yes</label>
		    						<input type="checkbox" name="use-amazon-no" />
		    						<label for="use-amazon-no">No</label>
		    					</td>
		    				</tr>
		    			</table>
		    		</div>
		          	<table>
		            	<tbody>
		            		<tr>
				              <td>
				                <label for="isbn">ISBN/ASIN: </label>
				              </td>
				              <td>
				                <label id="wpbooklist-editbook-label-booktitle" for="book-title">Book Title:</label>
				              </td>
				              <td>
				                <label for="book-author">Author: </label>
				              </td>
				              <td>
				                <label for="book-category">Category: </label><br>
				              </td>
		            		</tr>
		            		<tr>
								<td>
									<input type="text" id="wpbooklist-editbook-isbn" name="book-isbn">
								</td>
								<td>
									<input type="text" id="wpbooklist-editbook-title" name="book-title" size="30">
								</td>
								<td>
									<input type="text" id="wpbooklist-editbook-author" name="book-author" size="30">
								</td>
								<td>
									<input type="text" id="wpbooklist-editbook-category" name="book-category" size="30">
								</td>
		            		</tr>
		            		<tr>
								<td>
									<label for="book-pages">Pages: </label><br>
								</td>
								<td>
									<label for="book-pubdate">Publication Year: </label><br>
								</td>
								<td>
									<label for="book-publisher">Publisher: </label><br>
								</td>
		            		</tr>
		            		<tr>
								<td>
									<input type="number" id="wpbooklist-editbook-pages" name="book-pages" size="30">
								</td>
								<td>
									<input type="text" id="wpbooklist-editbook-pubdate" name="book-pubdate" size="30">
								</td>
								<td>
									<input type="text" id="wpbooklist-editbook-publisher" name="book-publisher" size="30">
								</td>
		            		</tr>
		            		<tr id="wpbooklist-addbook-page-post-create-label-row">
								<td colspan="2">
									<label class="wpbooklist-editbook-page-post-label" for="book-indiv-page">Create Individual Page?</label><br>
								</td>
								<td colspan="2">
									<label class="wpbooklist-editbook-page-post-label" for="book-indiv-post">Create Individual Post? </label><br>
								</td>
		            		</tr>
				            <tr id="wpbooklist-editbook-page-post-row">
				              <td colspan="2" class="wpbooklist-editbook-post-page-checkboxes">
				              	<input type="checkbox" id="wpbooklist-editbook-page-yes" name="book-indiv-page-yes" value="yes"/><label>Yes</label>
		                        <input type="checkbox" id="wpbooklist-editbook-page-no" name="book-indiv-page-no" value="no"/><label>No</label>
				              </td>
				              <td colspan="2" class="wpbooklist-editbook-post-page-checkboxes">
				              	<input type="checkbox" id="wpbooklist-editbook-post-yes" name="book-indiv-post-yes" value="yes"/><label>Yes</label>
		                        <input type="checkbox" id="wpbooklist-editbook-post-no" name="book-indiv-post-no" value="no"/><label>No</label>
				              </td>
				            </tr>
		            		<tr>
								<td colspan="2">
									<label for="book-description">Description (accepts html): </label><br>
								</td>
								<td colspan="2">
									<label for="book-notes">Notes (accepts html):</label><br>
								</td>
		            		</tr>
				            <tr>
				              <td colspan="2">
				                <textarea id="wpbooklist-editbook-description" name="book-description" rows="3" size="30"></textarea>
				              </td>
				              <td colspan="2">
				                <textarea id="wpbooklist-editbook-notes" name="book-notes" rows="3" size="30"></textarea>
				              </td>
				            </tr>
		            		<tr>
		              			<td colspan="2">
									<label for="book-rating">Rate This Title: </label><img id="wpbooklist-editbook-rating-img" src="'.ROOT_IMG_URL.'5star.png'.'" /><br>
								</td>
		              			<td colspan="2">
				                	<label id="wpbooklist-editbook-image-label" for="book-image">Cover Image:</label><input id="wpbooklist-editbook-upload_image_button" type="button" value="Choose Image"/><br>
				              	</td>
		            		</tr>
		            		<tr>
		              			<td colspan="2" style="vertical-align:top">
		                        	<select id="wpbooklist-editbook-rating">
		                        		<option selected>
		                        			Select a Rating...
		                        		</option>
		                    			<option value="5">
		                    				5 Stars
		                    			</option>
		                    			<option value="4">
		                    				4 Stars
		                    			</option>
		                    			<option value="3">
		                    				3 Stars
		                    			</option>
		                    			<option value="2">
		                    				2 Stars
		                    			</option>
		                    			<option value="1">
		                    				1 Star
		                    			</option>
		                  			</select>
		                        </td>
		              			<td colspan="2" style="position:relative">
		                			<input type="text" id="wpbooklist-editbook-image" name="book-image">
		                			<img id="wpbooklist-editbook-preview-img" src="'.ROOT_IMG_ICONS_URL.'book-placeholder.svg'.'" />
		                		</td>
		        			</tr>';

		        			// This filter allows the addition of one or more rows of items into the 'Add A Book' form. 
		        			$string8 = '';
		        			if(has_filter('wpbooklist_append_to_editbook_form')) {
            					$string8 = apply_filters('wpbooklist_append_to_editbook_form', $string8);
        					}

        					// This filter allows the addition of one or more rows of items into the 'Add A Book' form. 
		        			if(has_filter('wpbooklist_append_to_addbook_form_bookswapper')) {
            					$string8 = apply_filters('wpbooklist_append_to_addbook_form_bookswapper', $string8);
        					}

		        			$string9 = '
		          		</tbody>
		          	</table>
		            <div id="wpbooklist-editbook-signed-first-container">
						<table id="wpbooklist-editbook-signed-first-table">
			                <tbody>
			                	<tr>
				                    <td><label for="book-date-finished">Have You Finished This Book?</label></td>
				                    <td><label id="wpbooklist-editbook-signed-question" for="book-signed">Is This Book Signed?</label></td>
				                    <td><label id="wpbooklist-editbook-first-edition-question" for="book-first-edition">Is it a First Edition?</label></td>
			                	</tr>
		                        <tr>
		                            <td>
		                            	<input type="checkbox" id="wpbooklist-editbook-finished-yes" name="book-finished-yes" value="yes"/><label>Yes</label>
		                            	<input type="checkbox" id="wpbooklist-editbook-finished-no" name="book-finished-no" value="no"/><label>No</label>
		                            </td>
		                            <td id="wpbooklist-editbook-signed-td">
		                            	<input type="checkbox" id="wpbooklist-editbook-signed-yes" name="book-signed-yes" value="yes"/><label>Yes</label>
		                            	<input type="checkbox" id="wpbooklist-editbook-signed-no" name="book-signed-no" value="no"/><label>No</label>
		                            </td>
		                            <td id="wpbooklist-editbook-firstedition-td">
		                            	<input type="checkbox" id="wpbooklist-editbook-firstedition-yes" name="book-firstedition-yes" value="yes"/><label>Yes</label>
		                            	<input type="checkbox" id="wpbooklist-editbook-firstedition-no" name="book-firstedition-no" value="no"/><label>No</label>
		                            </td>
		                            <tr>
		                            	<td id="wpbooklist-editbook-date-finished-td" colspan="3">
		                            		<label for="book-date-finished-text"  id="book-date-finished-label">Date Finished: </label>
		                            		<input name="book-date-finished-text" type="date" id="wpbooklist-editbook-date-finished" />
		                            		<div id="wpbooklist-editbook-add-cancel-div">
		                            			<button type="button" id="wpbooklist-admin-editbook-button">Edit Book</button>
		                            			<button type="button" id="wpbooklist-admin-cancel-button">Cancel</button>
		                            		</div>
		                            		<div class="wpbooklist-spinner" id="wpbooklist-spinner-edit-indiv"></div>
		                            		<div id="wpbooklist-editbook-success-div" data-bookid="" data-booktable="">

		                            		</div>
		                            	</td>
		                            </tr>
		                        </tr>
		                    </tbody>
		                </table>
		            </div>
	        	</form>
	        	<div id="wpbooklist-add-book-error-check" data-add-book-form-error="true" style="display:none" data-></div>
    		</div>';

    		return $string1.$string2.$string3.$string4.$string5.$string6.$string7.$string8.$string9;
	}

	private function edit_book(){
		global $wpdb;
		// First do Amazon Authorization check
		if($this->amazon_auth_yes == 'true' && $this->use_amazon_yes == 'true'){
			$this->go_amazon === true;
			$this->gather_amazon_data();
			$this->gather_google_data();
			$this->gather_open_library_data();
			$this->gather_itunes_data();
			$this->create_wpbooklist_woocommerce_product();
		} else {
			// If $this->go_amazon is false, query the other apis and add the provided data to database
			$this->go_amazon === false;
			$this->gather_google_data();
			$this->gather_open_library_data();
			$this->gather_itunes_data();
			$this->create_wpbooklist_woocommerce_product();
		}

		if($this->page_yes || $this->post_yes){

			error_log('entered 1');
			$page_post_array = array(
				'title' => $this->title, 
				'isbn' => $this->isbn,
				'author' => $this->author,
				'author_url' => $this->author_url,
				'price' => $this->price,
				'finished' => $this->finished,
				'date_finished' => $this->date_finished,
				'signed' => $this->signed,
				'first_edition' => $this->first_edition,
				'image' => $this->image,
				'pages' => $this->pages,
				'pub_year' => $this->pub_year,
				'publisher' => $this->publisher,
				'category' => $this->category,
				'description' => $this->description,
				'notes' => $this->notes,
				'rating' => $this->rating,
				'page_yes' => $this->page_yes,
				'post_yes' => $this->post_yes,
				'itunes_page' => $this->itunes_page,
				'google_preview' => $this->google_preview,
				'amazon_detail_page' => $this->amazon_detail_page,
				'review_iframe' => $this->review_iframe,
				'similar_products' => $this->similar_products,
				'book_uid' => $this->book_uid,
				'lendable' => $this->lendable,
		  		'copies' => $this->copies

			);

			# Each of these class instantiations will return the ID of the page/post created for storage in DB
			$page = $this->page_id;
			$post = $this->post_id;
			if($this->page_yes == 'true' && ($this->page_id == 'false' || $this->page_id == 'true')){
				error_log('entered 2');
				require_once(CLASS_DIR.'class-page.php');
				$page = new WPBookList_Page($page_post_array);
				$page = $page->create_result;
			}

			if($this->post_yes == 'true' && ($this->post_id == 'false' || $this->post_id == 'true')){
				error_log('entered 3');
				require_once(CLASS_DIR.'class-post.php');
				$post = new WPBookList_Post($page_post_array);
				$post = $post->post_id;
			}
		}

		$data = array(
        	'title' => $this->title, 
			'isbn' => $this->isbn,
			'author' => $this->author,
			'author_url' => $this->author_url,
			'price' => $this->price,
			'finished' => $this->finished,
			'date_finished' => $this->date_finished,
			'signed' => $this->signed,
			'first_edition' => $this->first_edition,
			'image' => $this->image,
			'pages' => $this->pages,
			'pub_year' => $this->pub_year,
			'publisher' => $this->publisher,
			'category' => $this->category,
			'description' => $this->description,
			'notes' => $this->notes,
			'rating' => $this->rating,
			'page_yes' => $page,
			'post_yes' => $post,
			'itunes_page' => $this->itunes_page,
			'google_preview' => $this->google_preview,
			'amazon_detail_page' => $this->amazon_detail_page,
			'review_iframe' => $this->review_iframe,
			'similar_products' => $this->similar_products,
			'book_uid' => $this->book_uid,
			'lendable' => $this->lendable,
		  	'copies' => $this->copies,
		  	'woocommerce' => $this->wooid
	    );

	    $format = array( '%s','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s','%d','%s'); 
	    $where = array( 'ID' => $this->id );
	    $where_format = array( '%d' );
	    $result = $wpdb->update( $this->library, $data, $where, $format, $where_format );


		// Insert the Amazon Authorization into the DB if it's not already set to 'Yes'
		$table_name_options = $wpdb->prefix . 'wpbooklist_jre_user_options';
  		$this->options_results = $wpdb->get_row($wpdb->prepare("SELECT * FROM $table_name_options", $table_name_options));
  		if($this->options_results->amazonauth != 'true'){
			$data = array(
	        	'amazonauth' => $this->amazon_auth_yes
		    );
		    $format = array( '%s'); 
		    $where = array( 'ID' => 1 );
		    $where_format = array( '%d' );
		    $wpdb->update( $wpdb->prefix.'wpbooklist_jre_user_options', $data, $where, $format, $where_format );
		}

		$this->edit_result = $result;


	}

	public function empty_table($library){
		global $wpdb;
		$wpdb->query("TRUNCATE TABLE $library");

		// Drop table and re-create
		$row2 = $wpdb->get_results('SHOW CREATE TABLE '.$library);
		$wpdb->query("DROP TABLE $library");
		$wpdb->query($row2[0]->{'Create Table'});
		// Make sure auto_increment is set to 1
		$wpdb->query("ALTER TABLE $library AUTO_INCREMENT = 1");
		
	}

	public function empty_everything($library){
		global $wpdb;
		$results = $wpdb->get_results("SELECT * FROM $library");

		foreach($results as $result){
			wp_delete_post( $result->page_yes, true );
			wp_delete_post( $result->post_yes, true );
		}

		$wpdb->query("TRUNCATE TABLE $library");
	}

	public function delete_book($library, $book_id, $delete_string){
		global $wpdb;

		// Delete the associated post and page
		$post_delete = '';
		if($delete_string != ''){
			$delete_array = explode('-', $delete_string);
			foreach($delete_array as $delete){
				$delete_result = wp_delete_post( $delete, true );

				if($delete_result){
					$d_result = 1;
				}
				
				$post_delete = $post_delete.'-'.$d_result;
			}
		}

		// Deleting from saved_page_post_log
		$book_row = $wpdb->get_row($wpdb->prepare("SELECT * FROM $library WHERE ID = %d",$book_id ));
		$uid = $book_row->book_uid;
		$pp_table = $wpdb->prefix.'wpbooklist_jre_saved_page_post_log';
		$wpdb->delete( $pp_table, array( 'book_uid' => $uid ));


		// Deleting row
		$book_delete = $wpdb->delete( $library, array( 'ID' => $book_id ) );

		// Dropping primary key in database to alter the IDs and the AUTO_INCREMENT value
		$wpdb->query($wpdb->prepare( "ALTER TABLE $library MODIFY ID BIGINT(190) NOT NULL", $library));

		$wpdb->query($wpdb->prepare( "ALTER TABLE $library DROP PRIMARY KEY", $library));

		// Adjusting ID values of remaining entries in database
		$my_query = $wpdb->get_results($wpdb->prepare("SELECT * FROM $library", $library ));
		$title_count = $wpdb->num_rows;
		for ($x = $book_id; $x <= $title_count; $x++) {
			$data = array(
			    'ID' => $book_id
			);
			$format = array( '%s'); 
			$book_id++;  
			$where = array( 'ID' => ($book_id) );
			$where_format = array( '%d' );
			$wpdb->update( $library, $data, $where, $format, $where_format );
		}  

		// Adding primary key back to database 
		$wpdb->query($wpdb->prepare( "ALTER TABLE $library ADD PRIMARY KEY (`ID`)", $library));    

		$wpdb->query($wpdb->prepare( "ALTER TABLE $library MODIFY ID BIGINT(190) AUTO_INCREMENT", $library));

		// Setting the AUTO_INCREMENT value based on number of remaining entries
		$title_count++;
		$wpdb->query($wpdb->prepare( "ALTER TABLE $library AUTO_INCREMENT = %d", $title_count));

		return $book_delete.'-'.$post_delete;
	}

	public function refresh_amazon_review($id, $library){
		global $wpdb;

		// Build options table
		if(strpos($library, 'wpbooklist_jre_saved_book_log') !== false){
			$table_name_options = $wpdb->prefix . 'wpbooklist_jre_user_options';
		} else {
			$table = explode('wpbooklist_jre_', $library);
			$table_name_options = $wpdb->prefix . 'wpbooklist_jre_settings_'.$table[1];
		}

		// Get options for amazon affiliate id and hideamazonreview
		$this->options_results = $wpdb->get_row("SELECT * FROM $table_name_options");

		// Get book by id
		$this->get_book_by_id($id, $library);

		// Set isbn for gather Amazon data function
		$this->isbn = $this->retrieved_book->isbn;

		// Check and see if Amazon review URL is expired. If so, make a new api call, get URL, saved in DB.
		if($this->options_results->hideamazonreview == null || $this->options_results->hideamazonreview == 0){
			parse_str($this->retrieved_book->review_iframe, $output);
			if($output != null && $output != '' && isset($output['exp'])){
				$expire_date = substr($output['exp'], 0, 10);
				$today_date = date("Y-m-d");

				if($today_date == $expire_date || $today_date > $expire_date){

					$this->isbn = $this->retrieved_book->isbn;
					$this->title = $this->retrieved_book->title;

					// Gather Amazon data
					$this->gather_amazon_data();

					// Save new iframe url
					$data = array(
					  'review_iframe' => $this->review_iframe
					);
					$format = array( '%s'); 
					$where = array( 'ID' => $this->retrieved_book->ID );
					$where_format = array( '%d' );
					$wpdb->update( $library, $data, $where, $format, $where_format );
				}
			}
		}
	}

	private function get_book_by_id($id, $library){
		global $wpdb;
		$this->retrieved_book = $wpdb->get_row($wpdb->prepare("SELECT * FROM $library WHERE ID = %d", $id));
	}



}

endif;