<?php
/**
 * WPBookList Custom Libraries Form Tab Class
 *
 * @author   Jake Evans
 * @category Admin
 * @package  Includes/Classes
 * @version  1
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

if ( ! class_exists( 'WPBookList_Custom_Libraries_Form', false ) ) :
/**
 * WPBookList_Admin_Menu Class.
 */
class WPBookList_Custom_Libraries_Form {

	public static function output_custom_libraries_form(){
		global $wpdb;
		$table_name = $wpdb->prefix . 'wpbooklist_jre_list_dynamic_db_names';
		$db_row = $wpdb->get_results("SELECT * FROM $table_name");

		$string1 = '<div id="wpbooklist-custom-libraries-container">
				<p id="wpbooklist-use-shortcodes-p">'.__('Use these Shortcodes below to display your different libraries, or create a new Custom Library below', 'wpbooklist').'</p>';

		$string2 = '<table>
            			<tbody>
							<tr colspan="2"><td colspan="2"><p><span class="wpbooklist-jre-cover-shortcode-class">[wpbooklist_shortcode]</span> - ('.__('default shortcode for default library','wpbooklist').'</p></td>
							</tr>
							<tr>
								<td>	
									<p style="margin-left:5px; margin-top:0px;">'.__('By specifying the \'Action\' argument in the default shortcode above, you can control what happens when a user clicks on a book cover or title. For example, if you\'d rather have the visitor directed to a book\'s Amazon page, simply add the \'Action\' argument like so:','wpbooklist').' <br/><br/><span style="text-align:left;" class="wpbooklist-jre-cover-shortcode-class-orange">[wpbooklist_shortcode action="amazon"]</span>
									</p>
									<ul style="list-style: disc; margin-left: 20px;">
										<li><span class="wpbooklist-jre-cover-shortcode-class">'.__('All Available Action Values:','wpbooklist').'</span>
											<ul class="wpbooklist-jre-cover-shortcode-class-sub-ul">
												<li>action="bookview"</li>
												<li>action="amazon"</li>
												<li>action="googlebooks"</li>
												<li>action="ibooks"</li>
												<li>action="booksamillion"</li>
												<li>action="kobo"</li>
											</ul>
										</li>
									</ul>
								</td>
							</tr>
							<tr colspan="2">
								<td colspan="2" style="width: 100%;">
									<p><span class="wpbooklist-jre-cover-shortcode-class">[showbookcover]</span> - ('.__('shortcode for displaying Individual Books','wpbooklist').')</p>
									<ul style="list-style: disc; margin-left: 20px;">
										<li><span class="wpbooklist-jre-cover-shortcode-class">'.__('Specify a book:', 'wpbooklist').'</span> isbn="xxxxxxxxxxxxx"</li>
										<li><span class="wpbooklist-jre-cover-shortcode-class">'.__('Set Alignment:', 'wpbooklist').'</span> align="left"  <span style="font-style:italic;">or </span>align="right"</li>
										<li><span class="wpbooklist-jre-cover-shortcode-class">'.__('Specify Library:', 'wpbooklist').'</span> table="nameoflibrary" ('.__('leave out to use default library', 'wpbooklist').')</li>
										<li><span class="wpbooklist-jre-cover-shortcode-class">'.__('Set the Display:', 'wpbooklist').'</span> display="justimage"  <span style="font-style:italic;">or </span>display="excerpt"</li>
										<li><span class="wpbooklist-jre-cover-shortcode-class">'.__('Set the Size:', 'wpbooklist').'</span> width="100"</li>
										<li><span class="wpbooklist-jre-cover-shortcode-class">'.__('Specify the Action:', 'wpbooklist').'</span>
											<ul class="wpbooklist-jre-cover-shortcode-class-sub-ul">
												<li>action="bookview"</li>
												<li>action="amazon"</li>
												<li>action="googlebooks"</li>
												<li>action="ibooks"</li>
												<li>action="booksamillion"</li>
												<li>action="kobo"</li>
											</ul>
										</li>
									</ul>
								</td>
							</tr>
              				<tr colspan="2">
              					<td colspan="2"><p>'.__('So, for example, to display just a book\'s cover from your default library on the left side of a page or post, with a size of 100, this shortcode would do the trick:','wpbooklist').'<br/><span class="wpbooklist-jre-cover-shortcode-class-orange">[showbookcover display="justimage" isbn="123456789912" align="left" width="100"]</p></span>
              					</td>
              				</tr>
              				<tr>
              					<td colspan="2"><p>'.__('To display a book and it\'s excerpt from a custom library, with a size of 200, this shortcode would do the trick:', 'wpbooklist').'</br><span class="wpbooklist-jre-cover-shortcode-class-orange">[showbookcover display="excerpt" table="fiction" isbn="123456789912" align="left" width="200"]</p></span>
              					</td>
              				</tr>
              				<tr>
              					<td colspan="2"><p>'.__('To display a book\'s cover, that links to it\'s Amazon page, from your default library, with a size of 150, this shortcode would do the trick:','wpbooklist').'</br><span class="wpbooklist-jre-cover-shortcode-class-orange">[showbookcover action="amazon" isbn="123456789912" width="150"]</p></span>
              					</td>
              				</tr>
              				<tr>
              					<td colspan="2"><p style="text-align:center;"><a href="https://wpbooklist.com/index.php/2017/09/22/wpbooklist-shortcode-guide/">'.__('Be sure to read this detailed post on all of the different ways that the WPBookList Shortcode can be used!','wpbooklist').'</a></p></span>
              					</td>
              				</tr>
						</tbody>
              			<tbody>
              				<tr colspan="2">
              					<td colspan="2">
              						<p id="wpbooklist-use-shortcodes"></p>
              					</td>
      						</tr>';

      						$counter = 0;
              
              				$string3 = '';
							foreach($db_row as $db){
								$counter++;
								if(($db->user_table_name != "") || ($db->user_table_name != null)){
									$string3 = $string3.'<tr><td><p class="wpbooklist-jre-cover-shortcode-class">[wpbooklist_shortcode table="'.$db->user_table_name.'"]</p></td><td><button id="'.$db->user_table_name.'_'.$counter.'" class="wpbooklist_delete_custom_lib" type="button" >'.__('Delete Library','wpbooklist').'</button></td></tr>'; 
								}
							}

							$string4 = '<tr>
											<td>
												<input type="text" value="'.__('Create a new Library here...','wpbooklist').'" class= "wpbooklist-dynamic-input" id="wpbooklist-dynamic-input-library" name="wpbooklist-dynamic-input"></input>
											</td>
											<td>
												<button id="wpbooklist-dynamic-shortcode-button" type="button" disabled="true">'.__('Create New Library','wpbooklist').'</button>
											</td>
										</tr>
						            </tbody>
						        </table>
        					</div>';

		echo $string1.$string2.$string3.$string4;

	}


}

endif;