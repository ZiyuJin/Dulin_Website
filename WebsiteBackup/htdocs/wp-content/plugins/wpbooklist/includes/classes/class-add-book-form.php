<?php
/**
 * WPBookList Add-Edit-Book-Form Tab Class
 *
 * @author   Jake Evans
 * @category Admin
 * @package  Includes/Classes
 * @version  1
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

if ( ! class_exists( 'WPBookList_Add_Book_Form', false ) ) :
/**
 * WPBookList_Admin_Menu Class.
 */
class WPBookList_Add_Book_Form {

	public static function output_add_book_form(){

		$trans1 = __('To add a book, simply select a library from the drop-down below, fill out the form, and click the','wpbooklist');
		$trans2 = __("'Add Book'",'wpbooklist');
		$trans3 = __("button. If you choose to gather book info from Amazon,",'wpbooklist');
		$trans4 = __("the only required field is the ISBN/ASIN number",'wpbooklist');
		$trans5 = __("You must check the box below to authorize",'wpbooklist');
		$trans6 = __("WPBookList",'wpbooklist');
		$trans7 = __("to gather data from Amazon, otherwise, the only data that will be added for your book is what you fill out on the form below. WPBookList uses it's own Amazon Product Advertising API keys to gather book data, but if you happen to have your own API keys, you can use those instead by adding them on the",'wpbooklist');
		$trans8 = __("Amazon Settings",'wpbooklist');
		$trans9 = __("page",'wpbooklist');


		


		// Perform check for previously-saved Amazon Authorization
		global $wpdb;
		$table_name = $wpdb->prefix . 'wpbooklist_jre_user_options';
		$opt_results = $wpdb->get_row("SELECT * FROM $table_name");

		$table_name = $wpdb->prefix . 'wpbooklist_jre_list_dynamic_db_names';
		$db_row = $wpdb->get_results("SELECT * FROM $table_name");

		// For grabbing an image from media library
		wp_enqueue_media();
	 	$string1 = "<div id='wpbooklist-addbook-container'>
				<p>".__('To add a book, simply select a library from the drop-down below, fill out the form, and click the','wpbooklist')."<span class='wpbooklist-color-orange-italic'> ".__("'Add Book'",'wpbooklist')." </span>".__("button. If you choose to gather book info from Amazon,",'wpbooklist')."  <span class='wpbooklist-color-orange-italic'>".__("the only required field is the ISBN/ASIN number",'wpbooklist')."</span>.<br/><br/><span ";

					if($opt_results->amazonauth == 'true'){ 
						$string2 = 'style="display:none;"';
					} else {
						$string2 = '';
					}

					$string3 = " >".__("You must check the box below to authorize",'wpbooklist')." <span class='wpbooklist-color-orange-italic'>".__("WPBookList",'wpbooklist')."</span> ".__("to gather data from Amazon, otherwise, the only data that will be added for your book is what you fill out on the form below. WPBookList uses it's own Amazon Product Advertising API keys to gather book data, but if you happen to have your own API keys, you can use those instead by adding them on the",'wpbooklist')." <a href='".menu_page_url( 'WPBookList-Options-settings', false )."&tab=api'>".__("Amazon Settings",'wpbooklist')."</a> ".__("page",'wpbooklist').".</span></p>
          		<form id='wpbooklist-addbook-form' method='post' action=''>
		          	<div id='wpbooklist-authorize-amazon-container'>
		    			<table>";

		    			if($opt_results->amazonauth == 'true'){ 
							$string4 = '<tr style="display:none;"">
		    					<td><p id="auth-amazon-question-label">'.__('Authorize Amazon Usage?','wpbooklist').'</p></td>
		    				</tr>
		    				<tr style="display:none;"">
		    					<td>
		    						<input checked type="checkbox" name="authorize-amazon-yes" />
		    						<label for="authorize-amazon-yes">'.__('Yes','wpbooklist').'</label>
		    						<input type="checkbox" name="authorize-amazon-no" />
		    						<label for="authorize-amazon-no">'.__('No','wpbooklist').'</label>
		    					</td>
		    				</tr>';
						} else {
							$string4 = '<tr>
		    					<td><p id="auth-amazon-question-label">'.__('Authorize Amazon Usage?','wpbooklist').'</p></td>
		    				</tr>
		    				<tr>
		    					<td>
		    						<input type="checkbox" name="authorize-amazon-yes" />
		    						<label for="authorize-amazon-yes">'.__('Yes','wpbooklist').'</label>
		    						<input type="checkbox" name="authorize-amazon-no" />
		    						<label for="authorize-amazon-no">'.__('No','wpbooklist').'</label>
		    					</td>
		    				</tr>';
						}

		    			$string5 = '</table>
		    		</div>
		    		<div id="wpbooklist-addbook-select-library-label" for="wpbooklist-addbook-select-library">'.__('Select a Library to Add This Book To:','wpbooklist').'</div>
		    		<select class="wpbooklist-addbook-select-default" id="wpbooklist-addbook-select-library">
		    			<option value="'.$wpdb->prefix.'wpbooklist_jre_saved_book_log">'.__('Default Library','wpbooklist').'</option> ';

		    		$string6 = '';
		    		foreach($db_row as $db){
						if(($db->user_table_name != "") || ($db->user_table_name != null)){
							$string6 = $string6.'<option value="'.$wpdb->prefix.'wpbooklist_jre_'.$db->user_table_name.'">'.ucfirst($db->user_table_name).'</option>';
						}
					}


	          		$string7 = '    
	          		</select>
	          		<div id="wpbooklist-use-amazon-container">
		    			<table>
		    				<tr>
		    					<td><p id="use-amazon-question-label">'.__('Automatically Gather Book Info From Amazon (ISBN/ASIN number required)?','wpbooklist').'</p></td>
		    				</tr>
		    				<tr>
		    					<td style="text-align:center;">
		    						<input checked type="checkbox" name="use-amazon-yes" />
		    						<label for="use-amazon-yes">'.__('Yes','wpbooklist').'</label>
		    						<input type="checkbox" name="use-amazon-no" />
		    						<label for="use-amazon-no">'.__('No','wpbooklist').'</label>
		    					</td>
		    				</tr>
		    			</table>
		    		</div>
		          	<table>
		            	<tbody>
		            		<tr>
				              <td>
				                <label for="isbn">'.__('ISBN/ASIN:','wpbooklist').' </label>
				              </td>
				              <td>
				                <label id="wpbooklist-addbook-label-booktitle" for="book-title">'.__('Book Title:','wpbooklist').'</label>
				              </td>
				              <td>
				                <label for="book-author">'.__('Author:','wpbooklist').' </label>
				              </td>
				              <td>
				                <label for="book-category">'.__('Category:','wpbooklist').' </label><br>
				              </td>
		            		</tr>
		            		<tr>
								<td>
									<input type="text" id="wpbooklist-addbook-isbn" name="book-isbn">
								</td>
								<td>
									<input type="text" id="wpbooklist-addbook-title" name="book-title" size="30">
								</td>
								<td>
									<input type="text" id="wpbooklist-addbook-author" name="book-author" size="30">
								</td>
								<td>
									<input type="text" id="wpbooklistaddbook-category" name="book-category" size="30">
								</td>
		            		</tr>
		            		<tr>
								<td>
									<label for="book-pages">'.__('Pages:','wpbooklist').' </label><br>
								</td>
								<td>
									<label for="book-pubdate">'.__('Publication Year:','wpbooklist').' </label><br>
								</td>
								<td>
									<label for="book-publisher">'.__('Publisher:','wpbooklist').' </label><br>
								</td>
		            		</tr>
		            		<tr>
								<td>
									<input type="number" id="wpbooklist-addbook-pages" name="book-pages" size="30">
								</td>
								<td>
									<input type="text" id="wpbooklist-addbook-pubdate" name="book-pubdate" size="30">
								</td>
								<td>
									<input type="text" id="wpbooklist-addbook-publisher" name="book-publisher" size="30">
								</td>
		            		</tr>
		            		<tr id="wpbooklist-addbook-page-post-create-label-row">
								<td colspan="2">
									<label class="wpbooklist-addbook-page-post-label" for="book-indiv-page">'.__('Create Individual Page?','wpbooklist').'</label><br>
								</td>
								<td colspan="2">
									<label class="wpbooklist-addbook-page-post-label" for="book-indiv-post">'.__('Create Individual Post?','wpbooklist').' </label><br>
								</td>
		            		</tr>
				            <tr id="wpbooklist-addbook-page-post-row">
				              <td colspan="2" class="wpbooklist-addbook-post-page-checkboxes">
				              	<input type="checkbox" id="wpbooklist-addbook-page-yes" name="book-indiv-page-yes" value="yes"/><label>'.__('Yes','wpbooklist').'</label>
		                        <input type="checkbox" id="wpbooklist-addbook-page-no" name="book-indiv-page-no" value="no"/><label>'.__('No','wpbooklist').'</label>
				              </td>
				              <td colspan="2" class="wpbooklist-addbook-post-page-checkboxes">
				              	<input type="checkbox" id="wpbooklist-addbook-post-yes" name="book-indiv-post-yes" value="yes"/><label>'.__('Yes','wpbooklist').'</label>
		                        <input type="checkbox" id="wpbooklist-addbook-post-no" name="book-indiv-post-no" value="no"/><label>'.__('No','wpbooklist').'</label>
				              </td>
				            </tr>
		            		<tr>
								<td colspan="2">
									<label for="book-description">'.__('Description (accepts html):','wpbooklist').' </label><br>
								</td>
								<td colspan="2">
									<label for="book-notes">'.__('Notes (accepts html):','wpbooklist').'</label><br>
								</td>
		            		</tr>
				            <tr>
				              <td colspan="2">
				                <textarea id="wpbooklist-addbook-description" name="book-description" rows="3" size="30"></textarea>
				              </td>
				              <td colspan="2">
				                <textarea id="wpbooklist-addbook-notes" name="book-notes" rows="3" size="30"></textarea>
				              </td>
				            </tr>
		            		<tr>
		              			<td colspan="2">
									<label for="book-rating">'.__('Rate This Title:','wpbooklist').' </label><img id="wpbooklist-addbook-rating-img" src="'.ROOT_IMG_URL.'5star.png'.'" /><br>
								</td>
		              			<td colspan="2">
				                	<label id="wpbooklist-addbook-image-label" for="book-image">'.__('Cover Image:','wpbooklist').'</label><input id="wpbooklist-addbook-upload_image_button" type="button" value="Choose Image"/><br>
				              	</td>
		            		</tr>
		            		<tr>
		              			<td colspan="2" style="vertical-align:top">
		                        	<select id="wpbooklist-addbook-rating">
		                        		<option selected>
		                        			'.__('Select a Rating...','wpbooklist').'
		                        		</option>
		                    			<option value="5">
		                    				'.__('5 Stars','wpbooklist').'
		                    			</option>
		                    			<option value="4">
		                    				'.__('4 Stars','wpbooklist').'
		                    			</option>
		                    			<option value="3">
		                    				'.__('3 Stars','wpbooklist').'
		                    			</option>
		                    			<option value="2">
		                    				'.__('2 Stars','wpbooklist').'
		                    			</option>
		                    			<option value="1">
		                    				'.__('1 Stars','wpbooklist').'
		                    			</option>
		                  			</select>
		                        </td>
		              			<td colspan="2" style="position:relative">
		                			<input type="text" id="wpbooklist-addbook-image" name="book-image">
		                			<img id="wpbooklist-addbook-preview-img" src="'.ROOT_IMG_ICONS_URL.'book-placeholder.svg'.'" />
		                		</td>
		        			</tr>';

		        			// This filter allows the addition of one or more rows of items into the 'Add A Book' form. 
		        			$string8 = '';
		        			if(has_filter('wpbooklist_append_to_addbook_form')) {
            					$string8 = apply_filters('wpbooklist_append_to_addbook_form', $string8);
        					}

        					// This filter allows the addition of one or more rows of items into the 'Add A Book' form. 
		        			if(has_filter('wpbooklist_append_to_addbook_form_bookswapper')) {
            					$string8 = apply_filters('wpbooklist_append_to_addbook_form_bookswapper', $string8);
        					}

		        			$string9 = '
		          		</tbody>
		          	</table>
		            <div id="wpbooklist-addbook-signed-first-container">
						<table id="wpbooklist-addbook-signed-first-table">
			                <tbody>
			                	<tr>
				                    <td><label for="book-date-finished">'.__('Have You Finished This Book?','wpbooklist').'</label></td>
				                    <td><label id="wpbooklist-addbook-signed-question" for="book-signed">'.__('Is This Book Signed?','wpbooklist').'</label></td>
				                    <td><label id="wpbooklist-addbook-first-edition-question" for="book-first-edition">'.__('Is it a First Edition?','wpbooklist').'</label></td>
			                	</tr>
		                        <tr>
		                            <td>
		                            	<input type="checkbox" id="wpbooklist-addbook-finished-yes" name="book-finished-yes" value="yes"/><label>'.__('Yes','wpbooklist').'</label>
		                            	<input type="checkbox" id="wpbooklist-addbook-finished-no" name="book-finished-no" value="no"/><label>'.__('No','wpbooklist').'</label>
		                            </td>
		                            <td id="wpbooklist-addbook-signed-td">
		                            	<input type="checkbox" id="wpbooklist-addbook-signed-yes" name="book-signed-yes" value="yes"/><label>'.__('Yes','wpbooklist').'</label>
		                            	<input type="checkbox" id="wpbooklist-addbook-signed-no" name="book-signed-no" value="no"/><label>'.__('No','wpbooklist').'</label>
		                            </td>
		                            <td id="wpbooklist-addbook-firstedition-td">
		                            	<input type="checkbox" id="wpbooklist-addbook-firstedition-yes" name="book-firstedition-yes" value="yes"/><label>'.__('Yes','wpbooklist').'</label>
		                            	<input type="checkbox" id="wpbooklist-addbook-firstedition-no" name="book-firstedition-no" value="no"/><label>'.__('No','wpbooklist').'</label>
		                            </td>
		                            <tr>
		                            	<td id="wpbooklist-addbook-date-finished-td" colspan="3">
		                            		<label for="book-date-finished-text"  id="book-date-finished-label">'.__('Date Finished:','wpbooklist').' </label>
		                            		<input name="book-date-finished-text" type="date" id="wpbooklist-addbook-date-finished" />
		                            		<button type="button" id="wpbooklist-admin-addbook-button">'.__('Add Book','wpbooklist').'</button>
		                            		<div class="wpbooklist-spinner" id="wpbooklist-spinner-1"></div>
		                            		<div id="wpbooklist-addbook-success-div" data-bookid="" data-booktable="">

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


}

endif;