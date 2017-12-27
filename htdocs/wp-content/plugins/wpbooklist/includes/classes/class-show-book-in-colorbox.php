<?php
/**
 * WPBookList Show Book In Colorbox Class
 *
 * @author   Jake Evans
 * @category Admin
 * @package  Includes/Classes
 * @version  1
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

if ( ! class_exists( 'WPBookList_Show_Book_In_Colorbox', false ) ) :
/**
 * WPBookList_Admin_Menu Class.
 */
class WPBookList_Show_Book_In_Colorbox {

	// The final html output for the colorbox
	public $output;

	# All saved book properties
	public $amazon_auth_yes;
	public $library;
	public $settings_library;
	public $use_amazon_yes;
	public $isbn;
	public $title;
	public $author;
	public $author_url;
	public $category;
	public $price;
	public $pages;
	public $pub_year;
	public $publisher;
	public $description;
	public $notes;
	public $rating;
	public $image;
	public $finished;
	public $date_finished;
	public $signed;
	public $first_edition;
	public $page_yes;
	public $post_yes;
	public $itunes_page;
	public $google_preview;
	public $amazon_detail_page;
	public $review_iframe;
	public $similar_products;
	public $kobo_link;
	public $bam_link;

	# All settings properties
	public $enablepurchase;
	public $hidefacebook;
	public $hidetwitter;
	public $hidegoogleplus;
	public $hidemessenger;
	public $hidepinterest;
	public $hideemail;
	public $hidegoodreadswidget;
	public $hideamazonreview;
	public $hidedescription;
	public $hidesimilar;
	public $hidebookimage;
	public $hidefinished;
	public $hidebooktitle;
	public $hidelibrarytitle;
	public $hideauthor;
	public $hidecategory;
	public $hidepages;
	public $hidebookpage;
	public $hidebookpost;
	public $hidepublisher;
	public $hidepubdate;
	public $hidesigned;
	public $hidefirstedition;
	public $hidefeaturedtitles;
	public $hidenotes;
	public $hidebottompurchase;
	public $hidequotebook;
	public $hideratingbackend;
	public $amazoncountryinfo;
	public $amazonaff;
	public $itunesaff;
	public $hidegooglepurchase;
	public $hideamazonpurchase;
	public $hidebnpurchase;
	public $hidekobopurchase;
	public $hidebampurchase;
	public $hideitunespurchase;
	public $hidefrontendbuyimg;
    public $hidefrontendbuyprice;
    public $hidecolorboxbuyimg;
    public $hidecolorboxbuyprice;
    public $hidekindleprev;

	# All color data
	public $addbookcolor;
	public $backupcolor;
	public $searchcolor;
	public $statscolor;
	public $quotecolor;
	public $titlecolor;
	public $editcolor;
	public $deletecolor;
	public $pricecolor;
	public $purchasecolor;
	public $pagenumcolor;
	public $pagebackcolor;
	public $purchasebookcolor;
	public $titlebookcolor;
	public $quotebookcolor;
	public $storefront_active;

	// Array for the BookFinder Extension
	public $book_array = array();


	public function __construct($book_id = null, $book_table = null, $book_array = array()) {


		$plugins = get_option('active_plugins');
		$this->storefront_Active = array_search('wpbooklist-storefront/wpbooklist-storefront.php', $plugins);

		global $wpdb;
		// Construct the settings table name
		if(strpos($book_table, 'wpbooklist_jre_saved_book_log') !== false || $book_table == null){
			$this->settings_library = $wpdb->prefix . 'wpbooklist_jre_user_options';
		} else {
			$temp_lib = explode('_', $book_table);
			$this->settings_library = $wpdb->prefix.'wpbooklist_jre_settings_'. array_pop($temp_lib);
		}

		// Checking to see if the StoreFront extension is active
		$active = wp_get_active_and_valid_plugins();
		foreach ($active as $key => $plugin) {
			if(strpos($plugin, 'wpbooklist-storefront.php') !== false){
				$this->storefront_active = true;
			}
		}

		// If class is being called from the BookFinder extension, otherwise...
		if($book_id == null && $book_table == null){
			$this->book_array = $book_array;
			$this->gather_user_options();
			$this->gather_bookfinder_data();
			$this->set_amazon_localization();
			$this->modify_author_url();
			$this->create_similar_products();
			$this->dynamic_amazon_aff();
			$this->output_saved_book();
			//$this->output_plain_html();
		} else {
			$this->library = $book_table;
			$this->book_id = $book_id;

			$this->gather_user_options();
			$this->gather_book_info();
			$this->set_amazon_localization();
			$this->modify_author_url();
			//$this->gather_color_options();
			$this->create_similar_products();
			$this->dynamic_amazon_aff();
			$this->output_saved_book();
		}

		
	}

	private function gather_book_info(){

		global $wpdb;
  		$saved_book = $wpdb->get_row($wpdb->prepare("SELECT * FROM $this->library WHERE ID = %d", $this->book_id));
		$this->isbn = $saved_book->isbn;
		$this->id = $saved_book->ID;
		$this->title = $saved_book->title;
		$this->author = $saved_book->author;
		$this->author_url = $saved_book->author_url;
		$this->category = $saved_book->category;
		$this->price = $saved_book->price;
		$this->pages = $saved_book->pages;
		$this->pub_year = $saved_book->pub_year;
		$this->publisher = $saved_book->publisher;
		$this->description = $saved_book->description;
		$this->notes = $saved_book->notes;
		$this->rating = $saved_book->rating;
		$this->image = $saved_book->image;
		$this->finished = $saved_book->finished;
		$this->date_finished = $saved_book->date_finished;
		$this->signed = $saved_book->signed;
		$this->first_edition = $saved_book->first_edition;
		$this->page_yes = $saved_book->page_yes;
		$this->post_yes = $saved_book->post_yes;
		$this->itunes_page = $saved_book->itunes_page;
		$this->google_preview = $saved_book->google_preview;
		$this->amazon_detail_page = $saved_book->amazon_detail_page;
		$this->review_iframe = $saved_book->review_iframe;
		$this->similar_products = $saved_book->similar_products;
		$this->page_id = $saved_book->page_yes;
		$this->post_id = $saved_book->post_yes;
		$this->similar_products_array = array();
		$this->featured_results  = array();
		$this->kobo_link = $saved_book->kobo_link;
		$this->bam_link = $saved_book->bam_link;

		if($this->review_iframe == 'https'){
			$this->review_iframe = null;
		}

	}

	private function gather_user_options(){
		global $wpdb;
		$options_results = $wpdb->get_row($wpdb->prepare("SELECT * FROM $this->settings_library", $this->settings_library));
		$default_opt_table = $wpdb->prefix.'wpbooklist_jre_user_options';
		$default_options_results = $wpdb->get_row($wpdb->prepare("SELECT * FROM $default_opt_table", $default_opt_table));
		$this->enablepurchase = $options_results->enablepurchase;
		$this->hidefacebook = $options_results->hidefacebook;
		$this->hidetwitter = $options_results->hidetwitter;
		$this->hidegoogleplus = $options_results->hidegoogleplus;
		$this->hidemessenger = $options_results->hidemessenger;
		$this->hidepinterest = $options_results->hidepinterest;
		$this->hideemail = $options_results->hideemail;
		$this->hidegoodreadswidget = $options_results->hidegoodreadswidget;
		$this->hideamazonreview = $options_results->hideamazonreview;
		$this->hidedescription = $options_results->hidedescription;
		$this->hidesimilar = $options_results->hidesimilar;
		$this->hidebookimage = $options_results->hidebookimage;
		$this->hidefinished = $options_results->hidefinished;
		$this->hidebooktitle = $options_results->hidebooktitle;
		$this->hidelibrarytitle = $options_results->hidelibrarytitle;
		$this->hideauthor = $options_results->hideauthor;
		$this->hidecategory = $options_results->hidecategory;
		$this->hidepages = $options_results->hidepages;
		$this->hidebookpage = $options_results->hidebookpage;
		$this->hidebookpost = $options_results->hidebookpost;
		$this->hidepages = $options_results->hidepages;
		$this->hidepublisher = $options_results->hidepublisher;
		$this->hidepubdate = $options_results->hidepubdate;
		$this->hidesigned = $options_results->hidesigned;
		$this->hidefirstedition = $options_results->hidefirstedition;
		$this->hidefeaturedtitles = $options_results->hidefeaturedtitles;
		$this->hidenotes = $options_results->hidenotes;
		$this->hidebottompurchase = $options_results->hidebottompurchase;
		$this->hidequotebook = $options_results->hidequotebook;
		$this->hideratingbook = $options_results->hideratingbook;
		$this->amazoncountryinfo = $default_options_results->amazoncountryinfo;
		$this->amazonaff = $options_results->amazonaff;
		$this->itunesaff = $options_results->itunesaff;
		$this->hidegooglepurchase = $options_results->hidegooglepurchase;
		$this->hideamazonpurchase = $options_results->hideamazonpurchase;
		$this->hidebnpurchase = $options_results->hidebnpurchase;
		$this->hidekobopurchase = $options_results->hidekobopurchase;
		$this->hidebampurchase = $options_results->hidebampurchase;
		$this->hideitunespurchase = $options_results->hideitunespurchase;
		$this->hidefrontendbuyimg = $options_results->hidefrontendbuyimg;
        $this->hidefrontendbuyprice = $options_results->hidefrontendbuyprice;
        $this->hidecolorboxbuyimg = $options_results->hidecolorboxbuyimg;
        $this->hidecolorboxbuyprice = $options_results->hidecolorboxbuyprice;
        $this->hidekindleprev = $options_results->hidekindleprev;
	}

	private function gather_color_options(){
		global $wpdb;
		$color_table_name = $wpdb->prefix . 'wpbooklist_jre_color_options';
  		$colors_row = $wpdb->get_row($wpdb->prepare("SELECT * FROM $color_table_name WHERE ID = %d", 1));

  		$this->addbookcolor = $color_row->addbookcolor;
		$this->backupcolor = $color_row->backupcolor;
		$this->searchcolor = $color_row->searchcolor;
		$this->statscolor = $color_row->statscolor;
		$this->quotecolor = $color_row->quotecolor;
		$this->titlecolor = $color_row->titlecolor;
		$this->editcolor = $color_row->editcolor;
		$this->deletecolor = $color_row->deletecolor;
		$this->pricecolor = $color_row->pricecolor;
		$this->purchasecolor = $color_row->purchasecolor;
		$this->pagenumcolor = $color_row->pagenumcolor;
		$this->pagebackcolor = $color_row->pagebackcolor;
		$this->purchasebookcolor = $color_row->purchasebookcolor;
		$this->titlebookcolor = $color_row->titlebookcolor;
		$this->quotebookcolor = $color_row->quotebookcolor;

	}

	private function set_amazon_localization(){
		switch ($this->amazoncountryinfo ) {
	        case "au":
	            $this->amazon_detail_page = str_replace(".com",".com.au", $this->amazon_detail_page);
	            $this->review_iframe = str_replace(".com",".com.au", $this->review_iframe);
	            break;
	        case "br":
	            $this->amazon_detail_page = str_replace(".com",".com.br", $this->amazon_detail_page);
	            $this->review_iframe = str_replace(".com",".com.br", $this->review_iframe);
	            break;
	        case "ca":
	            $this->amazon_detail_page = str_replace(".com",".ca", $this->amazon_detail_page);
	            $this->review_iframe = str_replace(".com",".ca", $this->review_iframe);
	            break;
	        case "cn":
	            $this->amazon_detail_page = str_replace(".com",".cn", $this->amazon_detail_page);
	            $this->review_iframe = str_replace(".com",".cn", $this->review_iframe);
	            break;
	        case "fr":
	            $this->amazon_detail_page = str_replace(".com",".fr", $this->amazon_detail_page);
	            $this->review_iframe = str_replace(".com",".fr", $this->review_iframe);
	            break;
	        case "de":
	            $this->amazon_detail_page = str_replace(".com",".de", $this->amazon_detail_page);
	            $this->review_iframe = str_replace(".com",".de", $this->review_iframe);
	            break;
	        case "in":
	            $this->amazon_detail_page = str_replace(".com",".in", $this->amazon_detail_page);
	            $this->review_iframe = str_replace(".com",".in", $this->review_iframe);
	            break;
	        case "it":
	            $this->amazon_detail_page = str_replace(".com",".it", $this->amazon_detail_page);
	            $this->review_iframe = str_replace(".com",".it", $this->review_iframe);
	            break;
	        case "jp":
	            $this->amazon_detail_page = str_replace(".com",".co.jp", $this->amazon_detail_page);
	            $this->review_iframe = str_replace(".com",".co.jp", $this->review_iframe);
	            break;
	        case "mx":
	            $this->amazon_detail_page = str_replace(".com",".com.mx", $this->amazon_detail_page);
	            $this->review_iframe = str_replace(".com",".com.mx", $this->review_iframe);
	            break;
	        case "nl":
	            $this->amazon_detail_page = str_replace(".com",".nl", $this->amazon_detail_page);
	            $this->review_iframe = str_replace(".com",".nl", $this->review_iframe);
	            break;
	        case "es":
	            $this->amazon_detail_page = str_replace(".com",".es", $this->amazon_detail_page);
	            $this->review_iframe = str_replace(".com",".es", $this->review_iframe);
	            break;
	        case "uk":
	            $this->amazon_detail_page = str_replace(".com",".co.uk", $this->amazon_detail_page);
	            $this->review_iframe = str_replace(".com",".co.uk", $this->review_iframe);
	            break;
	        default:
	            //$this->amazon_detail_page = $saved_book->amazon_detail_page;//filter_var($saved_book->amazon_detail_page, FILTER_SANITIZE_URL);
	    }
	}

	private function modify_author_url(){
		if($this->author_url != null){
	        if(strpos($this->author_url, 'http://') === false && strpos($this->author_url, 'https://') === false){
	            $this->author_url = 'http://'.$this->author_url;
	        } else {
	            $this->author_url = $this->author_url;
	        }
    	}
	}

	private function create_similar_products(){
		// If no similar products were found, set array to null and return
		if($this->similar_products == ';bsp;1---1;bsp;G---G'){
			$this->similar_products_array  = null;
			return;
		}

		$similarproductsarray = explode(';bsp;',$this->similar_products);
        $similarproductsarray = array_unique($similarproductsarray);
        $this->similar_products_array = array_values($similarproductsarray);
	}

	private function dynamic_amazon_aff(){
		$this->amazon_detail_page = str_replace('wpbooklistid-20', $this->amazonaff, $this->amazon_detail_page);
	}

	private function gather_featured_titles(){
		global $wpdb;
		$table_name_featured = $wpdb->prefix . 'wpbooklist_jre_saved_books_for_featured';
		$this->featured_results = $wpdb->get_results("SELECT * FROM $table_name_featured");
	}

	private function output_saved_book(){
		$string1 = '<div id="wpbooklist_top_top_div">
    			<div id="wpbooklist_top_display_container">
			    	<table>
			            <tbody>
			                <tr>
			                    <td id="wpbooklist_image_saved_border">
			                        <div id="wpbooklist_display_image_container">';

			                        // Determine which image to use for the title
			                       if($this->hidebookimage == null || $this->hidebookimage == 0){
			                            if($this->image == null){
											$string2 = '<img id="wpbooklist_cover_image_popup" src="'.ROOT_IMG_URL.'image_unavaliable.png"/>';
			                            } else {
			                            	$string2 = '<img id="wpbooklist_cover_image_popup" src="'.$this->image.'"/>';
			                            }
		                        	}
		      
		                            $string3 = '<input type="submit" id="wpbooklist_desc_button" value="Description, Notes & Reviews"></input>';

									if(($this->hiderating == null || $this->hiderating == 0) && ($this->rating != 0)){ 
							            $string4 = '<p class="wpbooklist-share-text">My Rating</p>
							            <div class="wpbooklist-line-7"></div>';

										if($this->rating == 5){
										    $string5 = '<img style="width: 50px;" src="'.ROOT_IMG_URL.'5star.png'.'" />';
										}    

										if($this->rating == 4){
										    $string5 = '<img style="width: 50px;" src="'.ROOT_IMG_URL.'4star.png'.'" />';
										}    

										if($this->rating == 3){
										    $string5 = '<img style="width: 50px;" src="'.ROOT_IMG_URL.'3star.png'.'" />';
										}    

										if($this->rating == 2){
										    $string5 = '<img style="width: 50px;" src="'.ROOT_IMG_URL.'2star.png'.'" />';
										}    

										if($this->rating == 1){
										    $string5 = '<img style="width: 50px;" src="'.ROOT_IMG_URL.'1star.png'.'" />';
										}    
									} else {
										$string4 = '';
										$string5 = ''; 
									}

									if(($this->hidefacebook == null || $this->hidefacebook == 0) || ($this->hidetwitter == null || $this->hidetwitter == 0) || ($this->hidegoogleplus == null || $this->hidegoogleplus == 0) || ($this->hidemessenger == null || $this->hidemessenger == 0) || ($this->hidepinterest == null || $this->hidepinterest == 0) || ($this->hideemail == null || $this->hideemail == 0)){ 

						                $string6 = '<p class="wpbooklist-share-text">Share This Book</p>
						                <div class="wpbooklist-line-4"></div>';


						                if($this->hidefacebook == null || $this->hidefacebook == 0){
						                	$string7 = '<div class="addthis_sharing_toolbox addthis_default_style" style="cursor:pointer"><a style="cursor:pointer;" href="" addthis:title="'.$this->title.'" addthis:description="'.htmlspecialchars(addslashes($this->description)).'"addthis:url="'.$this->amazon_detail_page.'" class="addthis_button_facebook"></a></div>';
						            	} else {
						            		$string7 = '';
						            	}

						            	if($this->hidetwitter == null || $this->hidetwitter == 0){
						                	$string8 = '<div class="addthis_sharing_toolbox addthis_default_style" style="cursor:pointer"><a style="cursor:pointer;" href="" addthis:title="'.$this->title.'" addthis:description="'.htmlspecialchars(addslashes($this->description)).'"addthis:url="'.$this->amazon_detail_page.'" class="addthis_button_twitter"></a></div>';
						            	} else {
						            		$string8 = '';
						            	}

						            	if($this->hidegoogleplus == null || $this->hidegoogleplus == 0){
						                	$string9 = '<div class="addthis_sharing_toolbox addthis_default_style" style="cursor:pointer"><a style="cursor:pointer;" href="" addthis:title="'.$this->title.'" addthis:description="'.htmlspecialchars(addslashes($this->description)).'"addthis:url="'.$this->amazon_detail_page.'" class="addthis_button_google_plusone_share"></a></div>';
						            	} else {
						            		$string9 = '';
						            	}

						            	if($this->hidepinterest == null || $this->hidepinterest == 0){
						                	$string10 = '<div class="addthis_sharing_toolbox addthis_default_style" style="cursor:pointer"><a style="cursor:pointer;" href="" addthis:title="'.$this->title.'" addthis:description="'.htmlspecialchars(addslashes($this->description)).'"addthis:url="'.$this->amazon_detail_page.'" class="addthis_button_pinterest_share"></a></div>';
						            	} else {
						            		$string10 = '';
						            	}

						            	if($this->hidemessenger == null || $this->hidemessenger == 0){
						                	$string11 = '<div class="addthis_sharing_toolbox addthis_default_style" style="cursor:pointer"><a style="cursor:pointer;" href="" addthis:title="'.$this->title.'" addthis:description="'.htmlspecialchars(addslashes($this->description)).'"addthis:url="'.$this->amazon_detail_page.'" class="addthis_button_messenger"></a></div>';
						            	} else {
						            		$string11 = '';
						            	}

						            	if($this->hideemail == null || $this->hideemail == 0){ 
						                	$string12 = '<div class="addthis_sharing_toolbox addthis_default_style" style="cursor:pointer"><a style="cursor:pointer;" href="" addthis:title="'.$this->title.'" addthis:description="'.htmlspecialchars(addslashes($this->description)).'"addthis:url="'.$this->amazon_detail_page.'" class="addthis_button_gmail"></a></div>';
						            	} else {
						            		$string12 = '';
						            	}
						            } else {
						            	$string6 ='';
						            	$string7 ='';
						            	$string8 ='';
						            	$string9 ='';
						            	$string10 ='';
						            	$string11 ='';
						            	$string12 ='';
						            }

						            $string13 = '</div></div></td></table></div></td></tr></tbody><a name="desc_scroll"></a></table>';

						            $string14 = '<div id="wpbooklist_display_table">
                            						<table id="wpbooklist_display_table_2">';

                            						$string15 = '';
                            						$string16 = '';
                            						$string17 = '';
                            						if($this->hidelibrarytitle != 1){
						                            	$string15 = '<tr>
						                                    <td id="wpbooklist_title"><div';
							                                    if($this->titlebookcolor != null){ 
							                            			$string16 = 'data-modifycolor=false style="color:#'.$this->titlebookcolor.'"';
							                                 	} else {
							                                 		$string16 = '';
							                                 	}

						                                    $string17 = ' id="wpbooklist_title_div">'.htmlspecialchars_decode(stripslashes($this->title)).'</div>
						                                    </td>
						                                </tr>';
						                            }

						                            $string18 = '';
						                            if($this->hideauthor != 1){
						                            	$string18 = '<tr>
						                                    <td>
						                                        <span class="wpbooklist-bold-stats-class" id="wpbooklist_bold">Author: </span><span class="wpbooklist-bold-stats-value">'.$this->author.'</span>
						                                    </td>   
						                                </tr>
						                                ';
						                            }

						                            $string19 = '';
													if(($this->enablepurchase != null && $this->enablepurchase != 0) && $this->price != null && $this->hidecolorboxbuyprice != 1){
														// TODO: Add filter:  $string19 = '<tr><td><span class="wpbooklist-bold-stats-class" id="wpbooklist_bold">Price:</span> '.$this->price.'</td>   </tr>';
														if(has_filter('wpbooklist_append_to_colorbox_price')) {
            												$string19 = apply_filters('wpbooklist_append_to_colorbox_price', $this->price);
        												}
													} 

						                            $string20 = '';
						                            $string21 = '';
						                            $string22 = '';
						                            if($this->hidecategory != 1){
							                            $string20 = '<tr>
							                                    <td>';

														if($this->category == null){
							                            	$string21 = '<span class="wpbooklist-bold-stats-class" id="wpbooklist_bold">Category: </span><span class="wpbooklist-bold-stats-value">Not Available</span>';
							                            } else {
							                            	$string21 = '<span class="wpbooklist-bold-stats-class" id="wpbooklist_bold">Category: </span><span class="wpbooklist-bold-stats-value">'.$this->category.'</span>';
							                            }

							                            $string22 = '</td>
							                                </tr>';
						                            }

						                            $string23 = '';
						                            $string24 = '';
						                            $string25 = '';
						                            if($this->hidepages != 1){
							                            $string23 = '<tr>
							                                    <td>';

							                            if($this->pages == null){
							                            	$string24 = '<span class="wpbooklist-bold-stats-class" id="wpbooklist_bold">Pages: </span><span class="wpbooklist-bold-stats-value">Not Available</span>';
							                            } else {
							                            	$string24 = '<span class="wpbooklist-bold-stats-class" id="wpbooklist_bold">Pages: </span><span class="wpbooklist-bold-stats-value">'.$this->pages.'</span>';
							                            }
													
														$string25 = '</td>
						                                	</tr>';
						                            }

						                            $string26 = '';
						                            $string27 = '';
						                            $string28 = '';
						                            if($this->hidepublisher != 1){
							                            $string26 = '<tr>
							                                    <td>';

							                            if($this->publisher == null){
							                            	$string27 = '<span class="wpbooklist-bold-stats-class" id="wpbooklist_bold">Publisher: </span><span class="wpbooklist-bold-stats-value">Not Available</span>';
							                            } else {
							                            	$string27 = '<span class="wpbooklist-bold-stats-class" id="wpbooklist_bold">Publisher: </span><span class="wpbooklist-bold-stats-value">'.stripslashes(stripslashes($this->publisher)).'</span>';
							                            }

							                            $string28 = '</td>
							                                </tr>';
							                        }

							                        $string29 = '';
						                            $string30 = '';
						                            $string31 = '';
							                        if($this->hidepubdate != 1){
							                        	$string29 = '<tr>
							                                    <td>';
							                            if($this->pub_year == null){
							                            	$string30 = '<span class="wpbooklist-bold-stats-class" id="wpbooklist_bold">Publication: </span><span class="wpbooklist-bold-stats-value">Not Available</span>';
							                            } else {
							                            	$string30 = '<span class="wpbooklist-bold-stats-class" id="wpbooklist_bold">Publication: </span><span class="wpbooklist-bold-stats-value">'.$this->pub_year.'</span>';
							                            }

							                            $string31 = '</td>
							                                </tr>';
							                        }

							                        $string32 = '';
						                            $string33 = '';
						                            $string34 = '';
						                            $string35 = '';
							                        if($this->hidefinished != 1){
							                        	$string32 = '<tr>
							                                    <td>';

						                            	if($this->finished == 'true'){
								                            if($this->date_finished == 0){
								                            	$string33 = '<span class="wpbooklist-bold-stats-class" id="wpbooklist_bold">Finished? </span><span class="wpbooklist-bold-stats-value">Yes</span>';
								                            } else {
								                            	$string33 = '<span class="wpbooklist-bold-stats-class" id="wpbooklist_bold">Finished? </span><span class="wpbooklist-bold-stats-value">Yes, on '.$this->date_finished.'</span>';
								                            }
							                        	} else {
							                        		$string34 = '<span class="wpbooklist-bold-stats-class" id="wpbooklist_bold">Finished? </span><span class="wpbooklist-bold-stats-value">Not Yet</span>';
							                        	}

							                        	$string35 = '</td>
							                                </tr>';
							                        }

							                        $string36 = '';
						                            $string37 = '';
						                            $string38 = '';
													if($this->hidesigned != 1){			
														$string36 = '<tr>
							                                    <td>';		                        	
							                        	
						                            	if($this->signed == 'true'){
								                            $string37 = '<span class="wpbooklist-bold-stats-class" id="wpbooklist_bold">Signed? </span><span class="wpbooklist-bold-stats-value">Yes</span>';
							                        	} else {
							                        		$string37 = '<span class="wpbooklist-bold-stats-class" id="wpbooklist_bold">Signed? </span><span class="wpbooklist-bold-stats-value">No</span>';
							                        	}
							                        	$string38 = '</td>
							                                </tr>';
							                        }

							                        $string39 = '';
						                            $string40 = '';
						                            $string41 = '';
							                        if($this->hidefirstedition != 1){
						                        		$string39 = '<tr>
						                                    <td>';	

						                            	if($this->first_edition == 'true'){
								                            $string40 = '<span class="wpbooklist-bold-stats-class" id="wpbooklist_bold">First Edition? </span>Yes';
							                        	} else {
							                        		$string40 = '<span class="wpbooklist-bold-stats-class" id="wpbooklist_bold">First Edition? </span><span class="wpbooklist-bold-stats-value">No</span>';
							                        	}

							                        	$string41 = '</td>
							                                </tr>';
							                        }

							                        $string42 = '';
							                        if($this->hidebookpage != 1 && $this->page_id != null && $this->page_id != 'false'){
							                        	$string42 = '<tr>
						                                    <td>
						                                    	<span class="wpbooklist-bold-stats-class" id="wpbooklist_bold"><a id="wpbooklist-purchase-book-view" href="'.get_permalink( $this->page_id ).'"><span class="wpbooklist-bold-stats-page">Book Page</span></a></span>
																</td>
                            							</tr>';
							                        }

							                        $string43;
							                        if($this->hidebookpost != 1 && $this->post_id != null && $this->post_id != 'false'){
							                        	$string43 = '<tr>
						                                    <td>
						                                    	<span class="wpbooklist-bold-stats-class" id="wpbooklist_bold"><a id="wpbooklist-purchase-book-view" href="'.get_permalink( $this->post_id ).'"><span class="wpbooklist-bold-stats-page">Book Post</span></a></span>
																</td>
                            							</tr>';

							                        }

							                        $string44 = '';
						                            $string45 = '';
						                            $string46 = '';
						                        	if(($this->enablepurchase != null && $this->enablepurchase != 0) && $this->price != null && $this->author_url != ''){
						                        		$string44 = '
						                        		<tr>
                                							<td>
                                								<span class="wpbooklist-bold-stats-class" id="wpbooklist_bold"><a';

															if($this->purchasebookcolor != null){
																$string45 = 'data-modifycolor=false style="color:#'.$this->purchasebookcolor.'"';

															}

															// TODO: Add filter $string46 = ' id="wpbooklist-purchase-book-view" href="'.$this->author_url.'">Purchase Now!</a></td></tr>';
															if(($this->enablepurchase != null && $this->enablepurchase != 0) && $this->author_url != null && $this->author_url != '' && $this->hidecolorboxbuyprice != 1){
																$string46 = '';
																if(has_filter('wpbooklist_append_to_colorbox_purchase_text_link')) {
	        														$string46 = apply_filters('wpbooklist_append_to_colorbox_purchase_text_link', $this->author_url);
	    														}
    														}
						                        	}

						                        	$string46 = $string46.'</a>';

						                        	if(($this->hidekobopurchase == null || $this->hidekobopurchase == 0 && ($this->kobo_link != null && $this->kobo_link != 'http://store.kobobooks.com/en-ca/Search?Query=')) || ($this->hidebampurchase == null || $this->hidebampurchase == 0 && ($this->bam_link != null && $this->bam_link != 'http://www.booksamillion.com/p/')) || ($this->hideamazonpurchase == null || $this->hideamazonpurchase == 0 && ($this->amazon_detail_page != null)) || ($this->hidebnpurchase == null || $this->hidebnpurchase == 0 && ($this->isbn != null)) || ($this->hidegooglepurchase == null || $this->hidegooglepurchase == 0 && ($this->google_preview != null)) || ($this->hideitunespurchase == null || $this->hideitunespurchase == 0 && ($this->itunes_page != null)) || (($this->storefront_active == true) &&  ($this->hidecolorboxbuyimg == null || $this->hidecolorboxbuyimg == 0) && ($this->author_url != null && $this->author_url != '') )){

						                        		$string47 = '</td></tr><tr>
                        									<td><div class="wpbooklist-line-2"></div></td>
						                                </tr>
						                                <tr>
						                                    <td class="wpbooklist-purchase-title" colspan="2">Purchase This Book At:</td>
						                                </tr>
						                                <tr>
						                                    <td><div class="wpbooklist-line"></div></td>
						                                </tr>
						                                <tr>
						                                	<td>
						                                		<a';
						                                } else {
						                                	$string47 = '<a';
						                                }

						                                $string48 = '';
														if (($this->amazon_detail_page == null) || ($this->hideamazonpurchase != null && $this->hideamazonpurchase != 0 )){
															$string48 = ' style="display:none;"';
														} 
														
														$string49 = ' class="wpbooklist-purchase-img" href="'.$this->amazon_detail_page.'" target="_blank">
														<img src="'.ROOT_IMG_URL.'amazon.png" /></a>
														<a ';


														if(preg_match("/[a-z]/i", $this->isbn)){
															$string49 = ' class="wpbooklist-purchase-img" href="'.$this->amazon_detail_page.'" target="_blank">
																<img src="'.ROOT_IMG_URL.'kindle.png" /></a>
																<a ';
														} else {
															$string49 = ' class="wpbooklist-purchase-img" href="'.$this->amazon_detail_page.'" target="_blank">
																<img src="'.ROOT_IMG_URL.'amazon.png" /></a>
																<a ';
														}

														$string50 = '';
														if (($this->isbn == null) || ($this->hidebnpurchase != null && $this->hidebnpurchase != 0 )){
															$string50 = ' style="display:none;"';
														} 
															
														$string51 = ' class="wpbooklist-purchase-img" href="http://www.barnesandnoble.com/s/'.$this->isbn.'" target="_blank">
														<img src="'.ROOT_IMG_URL.'bn.png" /></a>
														<a ';

														$string52 = '';
														if (($this->google_preview == null) || ($this->hidegooglepurchase != null && $this->hidegooglepurchase != 0 )){
															$string52 = ' style="display:none;"';
														}

														$string53 = ' class="wpbooklist-purchase-img" href="'.$this->google_preview.'" target="_blank">
														<img src="'.ROOT_IMG_URL.'googlebooks.png" /></a><a ';

														$string54 = '';
														if (($this->itunes_page == null) || ($this->hideitunespurchase != null && $this->hideitunespurchase != 0 )){
															$string54 = ' style="display:none;"';
														}

														$string55 = ' class="wpbooklist-purchase-img" href="'.$this->itunes_page.'" target="_blank">
																<img src="'.ROOT_IMG_URL.'ibooks.png" id="wpbooklist-itunes-img" /></a><a ';

														$string56 = '';
														if (($this->author_url == null) || ($this->hidecolorboxbuyimg != null && $this->hidecolorboxbuyimg != 0 )){
															$string56 = ' style="display:none;"';
														}
															
														//TODO: Add filter $string57 = ' class="wpbooklist-purchase-img" href="'.$this->author_url.'" target="_blank"><img src="'.ROOT_IMG_URL.'author-icon.png" /></a>';
														$string57 = '></a>';


														$string84 = '<a ';
														if ($this->hidekobopurchase != null && $this->hidekobopurchase != 0 || $this->kobo_link == null || ($this->kobo_link == 'http://store.kobobooks.com/en-ca/Search?Query=')){
															$string84 = $string84.' style="display:none;"';
														}
														$string85 = ' class="wpbooklist-purchase-img" href="'.$this->kobo_link.'" target="_blank">
																<img src="'.ROOT_IMG_URL.'kobo-icon.png" /></a>';

														$string86 = '<a ';
														if ($this->hidebampurchase != null && $this->hidebampurchase != 0 || $this->bam_link == null || ($this->bam_link == 'http://www.booksamillion.com/p/')){
															$string86 = $string86.' style="display:none;"';
														}
														$string87 = ' class="wpbooklist-purchase-img" href="'.$this->bam_link.'" target="_blank">
																<img src="'.ROOT_IMG_URL.'bam-icon.jpg" /></a>';



														if(($this->enablepurchase != null && $this->enablepurchase != 0) && $this->author_url != null && $this->author_url != '' && $this->hidecolorboxbuyimg != 1){
															if(has_filter('wpbooklist_append_to_colorbox_purchase_image_link')) {
	            												$string57 = $string57.apply_filters('wpbooklist_append_to_colorbox_purchase_image_link', $this->author_url);
	        												}
        												}

														$string58 = '</td>   
                        										</tr>
								                                <tr>';

								                        $string59 = '';
														if(($this->hidekobopurchase == null || $this->hidekobopurchase == 0 && ($this->kobo_link != null && $this->kobo_link != 'http://store.kobobooks.com/en-ca/Search?Query=')) || ($this->hidebampurchase == null || $this->hidebampurchase == 0 && ($this->bam_link != null && $this->bam_link != 'http://www.booksamillion.com/p/')) || ($this->hideamazonpurchase == null || $this->hideamazonpurchase == 0 && ($this->amazon_detail_page != null)) || ($this->hidebnpurchase == null || $this->hidebnpurchase == 0 && ($this->isbn != null)) || ($this->hidegooglepurchase == null || $this->hidegooglepurchase == 0 && ($this->google_preview != null)) || ($this->hideitunespurchase == null || $this->hideitunespurchase == 0 && ($this->itunes_page != null)) || (($this->storefront_active == true) &&  ($this->hidecolorboxbuyimg == null || $this->hidecolorboxbuyimg == 0) && ($this->author_url != null && $this->author_url != '') )){
															
															$string59 = '</td>   
                    										</tr>
							                                <tr>
							                                    <td><div class="wpbooklist-line-3"></div></td>
							                                </tr>
							                                <tr>';
							                        	}

							                        	$string60 = '';
														if ($this->hidegoodreadswidget == null || $this->hidegoodreadswidget == 0 && ($this->isbn != '' || $this->isbn != null)){
                            								$string60 = '<td> 
                            									<div id="gr_add_to_books">
																<div class="gr_custom_each_container_">
														          <a target="_blank" style="border:none" href="https://www.goodreads.com/book/isbn/'.$this->isbn.'"><img alt="goodreads-image-of-book" src="https://www.goodreads.com/images/atmb_add_book-70x25.png" /></a>
														        </div>
														      </div>
														      <script src="https://www.goodreads.com/book/add_to_books_widget_frame/'.$this->isbn.'?atmb_widget%5Bbutton%5D=atmb_widget_1.png"></script></td>'; 
														}

														$string61 = '</tr>
															    </table>
															    </div>
															         </div>         
															        <div id="wpbooklist_desc_id">';

														$string62 = '';
														$string63 = '';
														$string64 = '';
														if(($this->hidesimilar == null || $this->hidesimilar == 0) && $this->similar_products_array != null){
													        if($this->similar_products == null){

													    	} else {
													            $string62 = '<div class="wpbooklist-similar-featured-div">
													                <p id="wpbooklist-similar-titles-id" class="wpbooklist_description_p">Similar Titles:</p> 
																		<table class="wpbooklist-similar-titles-table"> <tr>';

																$string63 = '';
													            foreach($this->similar_products_array as $key=>$prod){
											                        $arr = explode("---", $prod, 2);
											                        $asin = $arr[0];

											                        $image = 'http://images.amazon.com/images/P/'.$asin.'.01.LZZZZZZZ.jpg?rand='.uniqid();
											                        $url = 'https://www.amazon.com/dp/'.$asin.'?tag='.$this->amazonaff;
											                        if($asin != null && $asin != ''){
												                        if(strlen($image) > 51 ){
												                            if($key == 6){
												                                $string63 = $string63.'</tr><tr><td><a class="wpbooklist-similar-link" target="_blank" href="'.$url.'"><img class="wpbooklist-similar-image" src="'.$image.'" /></a></td>';
												                            } else {
												                               $string63 = $string63.'<td><a class="wpbooklist-similar-link" target="_blank" href="'.$url.'"><img class="wpbooklist-similar-image" src="'.$image.'" /></a></td>';
												                            }
												                        }
											                    	}
													            }
													                    
													            $string64 = '</tr>
																	    </table>
																	</div>';
															} 
														}

														$string65 = '';
														$string66 = '';
														$string67 = '';
														if($this->hidefeaturedtitles == null || $this->hidefeaturedtitles == 0){
													        if($this->featured_results == null){
													            
													        } else {
													            $string65 = '<div class="wpbooklist-similar-featured-div" style="margin-left:5px">
													                <p id="wpbooklist-similar-titles-id" class="wpbooklist_description_p">Featured Titles:</p> 
													                <table class="wpbooklist-similar-titles-table"> <tr>';
													                $string66 = '';
												                    foreach($this->featured_results as $key=>$featured){
												                        $image = $featured->coverimage;
												                        $url = $featured->amazondetailpage;
												                        if(strlen($image) > 51 ){
												                            if($key == 5){
												                                $string66 = $string64.'</tr><tr><td><a class="wpbooklist-similar-link" target="_blank" href="'.$url.'"><img class="wpbooklist-similar-image" src="'.$image.'" /></a></td>';
												                            } else {
												                               $string66 = $string64.'<td><a class="wpbooklist-similar-link" target="_blank" href="'.$url.'"><img class="wpbooklist-similar-image" src="'.$image.'" /></a></td>';
												                            }
												                        }
												                    }
													                 $string67 = '</tr>
													                </table>
													            </div>';
													        }
													    }

													    $string68 = '';
													    $lend_array = array($this->id, $this->library);
														if(has_filter('wpbooklist_append_to_colorbox_lending_info')) {
												
																//array_push($saved_book, $this->library);
            													$string68 = apply_filters('wpbooklist_append_to_colorbox_lending_info', $lend_array);
        												}




													    $kindle_array = array($this->isbn, $this->amazonaff);
													    $isbn_test = preg_match("/[a-z]/i", $this->isbn);
														if(($this->hidekindleprev == null || $this->hidekindleprev == 0) && $isbn_test){
															if(has_filter('wpbooklist_add_to_colorbox_kindle')) {
            													$string68 = $string68.apply_filters('wpbooklist_add_to_colorbox_kindle', $kindle_array);
        													}
        												}
									
														$string69 = '';
														if($this->hidedescription == null || $this->hidedescription == 0){
														     $string68 = $string68.'<p class="wpbooklist_description_p" id="wpbooklist-desc-title-id">Description:</p>'; 

														    if($this->description == null){
													        	$string69 = '<p class="wpbooklist_desc_p_class">Not Available</p>';
													        } else {
													        	$string69 = '<div class="wpbooklist_desc_p_class">'.stripslashes(html_entity_decode($this->description)).'</div>';
													        } 
														}

														if(($this->hideamazonreview == null || $this->hideamazonreview == 0) && ($this->review_iframe != null)){
													            $string70 = '<p class="wpbooklist_description_p" id="wpbooklist-amazon-review-title-id">Amazon Reviews:</p> 
													            <p class="wpbooklist_desc_p_class"><iframe id="wpbooklist-review-iframe" src="'.$this->review_iframe.'"></iframe></p>';
													    }

													    $string71 = '';
													    $string72 = '';
													    if($this->hidenotes == null || $this->hidenotes == 0){
													         $string71 = '<p class="wpbooklist_description_p" id="wpbooklist-notes-title-id">Notes:</p>';

												            if($this->notes == null){
												                $string72 = '<p class="wpbooklist_desc_p_class">None Provided</p>';
												            } else {
												                $string72 = '<p class="wpbooklist_desc_p_class">'.stripslashes(html_entity_decode($this->notes)).'</p>';
												            } 
													    }

													    $string73 = '';
														if(($this->hidekobopurchase == null || $this->hidekobopurchase == 0 && ($this->kobo_link != null && $this->kobo_link != 'http://store.kobobooks.com/en-ca/Search?Query=')) || ($this->hidebampurchase == null || $this->hidebampurchase == 0 && ($this->bam_link != null && $this->bam_link != 'http://www.booksamillion.com/p/')) || ($this->hideamazonpurchase == null || $this->hideamazonpurchase == 0 && ($this->amazon_detail_page != null)) || ($this->hidebnpurchase == null || $this->hidebnpurchase == 0 && ($this->isbn != null)) || ($this->hidegooglepurchase == null || $this->hidegooglepurchase == 0 && ($this->google_preview != null)) || ($this->hideitunespurchase == null || $this->hideitunespurchase == 0 && ($this->itunes_page != null)) || (($this->storefront_active == true) &&  ($this->hidecolorboxbuyimg == null || $this->hidecolorboxbuyimg == 0) && ($this->author_url != null && $this->author_url != ''))){

														} else {
															$string73 = '<div style="display:none;" >';
														}



           
														$string74 = '<div class="wpbooklist-line-5"></div>
											            <p id="wpbooklist-purchase-title-id-bottom" class="wpbooklist-purchase-title">
											                Purchase This Book At:
											            </p>
											            <div class="wpbooklist-line-6"></div>
											            <a';
			
														$string75 = '';
														if (($this->amazon_detail_page == null) || ($this->hideamazonpurchase != null && $this->hideamazonpurchase != 0 )){
															$string75 = ' style="display:none;"';
														}

														if(preg_match("/[a-z]/i", $this->isbn)){
															$string76 = ' class="wpbooklist-purchase-img" href="'.$this->amazon_detail_page.'" target="_blank"><img src="'.ROOT_IMG_URL.'kindle.png" /></a><a';
														} else {
															$string76 = ' class="wpbooklist-purchase-img" href="'.$this->amazon_detail_page.'" target="_blank"><img src="'.ROOT_IMG_URL.'amazon.png" /></a><a';
														}

													

														$string77 = '';
														if (($this->isbn == null)|| ($this->hidebnpurchase != null && $this->hidebnpurchase != 0 )){
															$string77 = ' style="display:none;"';
														}

														$string78 = ' class="wpbooklist-purchase-img" href="http://www.barnesandnoble.com/s/'.$this->isbn.'" target="_blank">
														<img src="'.ROOT_IMG_URL.'bn.png" /></a><a ';

														$string79 = '';
														if (($this->google_preview == null) || ($this->hidegooglepurchase != null && $this->hidegooglepurchase != 0 )){
															$string79 = ' style="display:none;"';
														}
																
														$string80 = ' class="wpbooklist-purchase-img" href="'.$this->google_preview.'" target="_blank">
																<img src="'.ROOT_IMG_URL.'googlebooks.png" /></a><a ';

														$string81 = '';
														if (($this->itunes_page == null) || ($this->hideitunespurchase != null && $this->hideitunespurchase != 0 )){
															$string81 = ' style="display:none;"';
														}
															
														$string82 = ' class="wpbooklist-purchase-img" href="'.$this->itunes_page.'" target="_blank">
																<img id="wpbooklist-itunes-img" src="'.ROOT_IMG_URL.'ibooks.jpg" /></a>';



														$string88 = '<a ';
														if ($this->hidekobopurchase != null && $this->hidekobopurchase != 0 || $this->kobo_link == null || ($this->kobo_link == 'http://store.kobobooks.com/en-ca/Search?Query=')){
															$string88 = $string88.' style="display:none;"';
														}
														$string89 = ' class="wpbooklist-purchase-img" href="'.$this->kobo_link.'" target="_blank">
																<img src="'.ROOT_IMG_URL.'kobo-icon.png" /></a>';

														$string90 = '<a ';
														if ($this->hidebampurchase != null && $this->hidebampurchase != 0 || $this->bam_link == null || ($this->bam_link == 'http://www.booksamillion.com/p/')){
															$string90 = $string90.' style="display:none;"';
														}
														$string91 = ' class="wpbooklist-purchase-img" href="'.$this->bam_link.'" target="_blank">
																<img src="'.ROOT_IMG_URL.'bam-icon.jpg" /></a>';


														if(($this->enablepurchase != null && $this->enablepurchase != 0) && $this->author_url != null && $this->author_url != '' && $this->hidecolorboxbuyimg != 1){
															if(has_filter('wpbooklist_append_to_colorbox_purchase_image_link')) {
	            												$string91 = $string91.apply_filters('wpbooklist_append_to_colorbox_purchase_image_link', $this->author_url);
	        												}
        												}


														if(($this->hidekobopurchase == null || $this->hidekobopurchase == 0 && ($this->kobo_link != null && $this->kobo_link != 'http://store.kobobooks.com/en-ca/Search?Query=')) || ($this->hidebampurchase == null || $this->hidebampurchase == 0 && ($this->bam_link != null && $this->bam_link != 'http://www.booksamillion.com/p/')) || ($this->hideamazonpurchase == null || $this->hideamazonpurchase == 0 && ($this->amazon_detail_page != null)) || ($this->hidebnpurchase == null || $this->hidebnpurchase == 0 && ($this->isbn != null)) || ($this->hidegooglepurchase == null || $this->hidegooglepurchase == 0 && ($this->google_preview != null)) || ($this->hideitunespurchase == null || $this->hideitunespurchase == 0 && ($this->itunes_page != null)) || (($this->storefront_active == true) &&  ($this->hidecolorboxbuyimg == null || $this->hidecolorboxbuyimg == 0) && ($this->author_url != null && $this->author_url != ''))){

														} else {
															$string83 = '</div>';
														}


		$this->output = $string1.$string2.$string3.$string4.$string5.$string6.$string7.$string8.$string9.$string10.$string11.$string12.$string13.$string14.$string15.$string16.$string17.$string18.$string19.$string20.$string21.$string22.$string23.$string24.$string25.$string26.$string27.$string28.$string29.$string30.$string31.$string32.$string33.$string34.$string35.$string36.$string37.$string38.$string39.$string40.$string41.$string42.$string43.$string44.$string45.$string46.$string47.$string48.$string49.$string50.$string51.$string52.$string53.$string54.$string55.$string56.$string57.$string84.$string85.$string86.$string87.$string58.$string59.$string60.$string61.$string62.$string63.$string64.$string65.$string66.$string67.$string68.$string69.$string70.$string71.$string72.$string73.$string74.$string75.$string76.$string77.$string78.$string79.$string80.$string81.$string82.$string83.$string88.$string89.$string90.$string91;
   
	}

	private function gather_bookfinder_data(){
		$this->title = $this->book_array['title'];
		$this->author = $this->book_array['author'];
		$this->category = $this->book_array['category'];
		$this->itunes_page = $this->book_array['itunes_page'];
		$this->pages = $this->book_array['pages'];
		$this->pub_year = $this->book_array['pub_year'];
		$this->publisher = $this->book_array['publisher'];
		$this->description = $this->book_array['description'];
		$this->image = $this->book_array['image'];
		$this->similar_products_array = array();
		$this->review_iframe = $this->book_array['reviews'];
		$this->isbn = $this->book_array['isbn'];
		$this->amazon_detail_page = $this->book_array['details'];
		$this->similar_products = $this->book_array['similar_products'];
		$this->kobo_link = $this->book_array['kobo_link'];
		$this->bam_link = $this->book_array['bam_link'];
	}



}


endif;