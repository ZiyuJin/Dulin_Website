<?php

/*
Quick Event Manager Guest Events
*/

function qem_guest_loop($atts) {
	ob_start();
    extract ( shortcode_atts (array('cat' => '1','author' => '1',), $atts ) );
	$event = qem_stored_guest ();
	if (isset($_POST['qemguestsubmit'])) {
		$formvalues  = $_POST;
        $required = qem_guest_list();

        //handle category
        $formvalues['event_category'] = $_POST['cat'];
        foreach ($required as $item) {
            if ($event[$item.'_checked'] == 'checked' && ($formvalues[$item] == $event[$item] || empty($formvalues[$item])))
                $error .= $event[$item]. '. ';
        }

        //handle required category
        if ($event['event_category_checked'] == 'checked' && ($formvalues['event_category'] == '-1')){
            $error .= $event['event_category']. '. ';
        }

        if ($_FILES["event_image_upload"]["tmp_name"]) {

            $imageData = @getimagesize($_FILES["event_image_upload"]["tmp_name"]);

            //added max width for image
            if($imageData === FALSE || !($imageData[2] == IMAGETYPE_GIF || $imageData[2] == IMAGETYPE_JPEG || $imageData[2] == IMAGETYPE_PNG) || $_FILES["event_image_upload"]["size"] >= $event['imagesize'])
                $error .= $error.$event['errorimage']. '. ';
            }

        if ( ( $formvalues['event_end_date'] <> $event['event_end_date'] || empty($formvalues['event_end_date']) ) && strtotime( $formvalues['event_date']) > strtotime($formvalues['event_end_date'] ) )
            $error .= $error.$event['errorenddate'];

        if (!is_user_logged_in() && $formvalues['answer'] <> $formvalues['event_captcha'] ) $error .= $event['errorcaptcha']. '. ';

        if ($error) qem_guest_display_form($formvalues,$event,$error);

		else qem_guest_process_form($formvalues,$event);
		}
	else {
		$values = $event;
        $digit1 = mt_rand(1,10);
		$digit2 = mt_rand(1,10);
		if( $digit2 >= $digit1 ) {
		$values['thesum'] = "$digit1 + $digit2";
		$values['answer'] = $digit1 + $digit2;
		} else {
		$values['thesum'] = "$digit1 - $digit2";
		$values['answer'] = $digit1 - $digit2;
		}
        if ( is_user_logged_in() ) {
            $current_user = wp_get_current_user();
            $values['event_author'] = $current_user->user_login;
            $values['event_author_email'] = $current_user->user_email;
        }
        qem_guest_display_form( $values,$event,null);
	}
	$output_string=ob_get_contents();
	ob_end_clean();
	return $output_string;
	}

function qem_guest_display_form( $values,$event,$error) {
    $content = '<div class="qem-guestpost">';
    $required = qem_guest_list();
    array_push($required ,"event_captcha");
    $thedays = array('Week','Month','Sunday','Monday','Tuesday','Wednesday','Thursday','Friday','Saturday');
    $thenumbers = array('','First','Second','Third','Fourth');
    
    foreach ($required as $item) {
        if ($event[$item.'_checked'] == 'checked') {
            $event[$item.'_checked'] = 'class="required"';
            //added '-1' comparison to handle category
            if ($error && ($values[$item] == $event[$item] || empty($values[$item])|| $values[$item] == '-1' )) $event[$item.'_checked'] = 'class="error"';
        }
    }
    if ($error)
		$content .= "<h3 style='color:red'>". $event['errormessage'] . ": " . $error."</h3>\r\t";
	else
		$content .= '<h3>'.$event['title'].'</h3><p>'.$event['blurb'].'</p>';
    if ($event['onlyuser'] && !is_user_logged_in()) {
        $content = '<p>You have to be logged in to create a guest event.</p>';
    } else {
    $content .= '<form method="post" action="" enctype="multipart/form-data">
    <input type="text" '.$event['event_title_checked'].' name="event_title" value="'.$values['event_title'].'" onblur="if (this.value == \'\') {this.value = \''.$event['event_title'].'\';}" onfocus="if (this.value == \''.$event['event_title'].'\') {this.value = \'\';}"/>
    <input type="text" style="width:40%;" '.$event['event_date_checked'].' id="qemdate" name="event_date" value="'.$values['event_date'].'" onblur="if (this.value == \'\') {this.value = \''.$event['event_date'].'\';}" onfocus="if (this.value == \''.$event['event_date'].'\') {this.value = \'\';}"/> <em>'.__('Errors will reset to today&#146;s date.','quick-event-manager').'</em>
    <script type="text/javascript">jQuery(document).ready(function($) {});</script>';

    if ($event['event_end_date_use']) $content .='<input type="text" style="width:40%" '.$event['event_end_date_checked'].' id="qemenddate" name="event_end_date" value="'.$values['event_end_date'].'" onblur="if (this.value == \'\') {this.value = \''.$event['event_end_date'].'\';}" onfocus="if (this.value == \''.$event['event_end_date'].'\') {this.value = \'\';}"/> <em>'.__('Leave blank for one day events.', 'quick-event-manager').'</em>
    <script type="text/javascript">jQuery(document).ready(function($) {});</script>';

    if ($event['event_desc_use']) $content .='<input type="text" '.$event['event_desc_checked'].' name="event_desc" value="'.$values['event_desc'].'" onblur="if (this.value == \'\') {this.value = \''.$event['event_desc'].'\';}" onfocus="if (this.value == \''.$event['event_desc'].'\') {this.value = \'\';}"/>';
    
    if ($event['event_start_use']) $content .='<div style="float:left;width:49%"><input type="text" '.$event['event_start_checked'].' name="event_start" value="'.$values['event_start'].'" onblur="if (this.value == \'\') {this.value = \''.$event['event_start'].'\';}" onfocus="if (this.value == \''.$event['event_start'].'\') {this.value = \'\';}"/></div><div style="float:right;width:49%;"><input type="text"  '.$event['event_finish_checked'].' name="event_finish" value="'.$values['event_finish'].'" onblur="if (this.value == \'\') {this.value = \''.$event['event_finish'].'\';}" onfocus="if (this.value == \''.$event['event_finish'].'\') {this.value = \'\';}"/></div>
   <div style="clear:both"></div>';
    
    if ($event['event_location_use']) $content .='<input type="text" '.$event['event_location_checked'].' name="event_location" value="'.$values['event_location'].'" onblur="if (this.value == \'\') {this.value = \''.$event['event_location'].'\';}" onfocus="if (this.value == \''.$event['event_location'].'\') {this.value = \'\';}"/>';
    
    if ($event['event_address_use']) $content .='<input type="text" '.$event['event_address_checked'].' name="event_address" value="'.$values['event_address'].'" onblur="if (this.value == \'\') {this.value = \''.$event['event_address'].'\';}" onfocus="if (this.value == \''.$event['event_address'].'\') {this.value = \'\';}"/>';
    
    if ($event['event_link_use']) $content .='<div style="float:left;width:49%"><input type="text" '.$event['event_link_checked'].' name="event_link" value="'.$values['event_link'].'" onblur="if (this.value == \'\') {this.value = \''.$event['event_link'].'\';}" onfocus="if (this.value == \''.$event['event_link'].'\') {this.value = \'\';}"/></div><div style="float:right;width:49%;">
<input type="text"  '.$event['event_anchor_checked'].' name="event_anchor" value="'.$values['event_anchor'].'" onblur="if (this.value == \'\') {this.value = \''.$event['event_anchor'].'\';}" onfocus="if (this.value == \''.$event['event_anchor'].'\') {this.value = \'\';}"/></div>
    <div style="clear:both"></div>';
    
    if ($event['event_organiser_use']) $content .='<input type="text" '.$event['event_organiser_checked'].' name="event_organiser" value="'.$values['event_organiser'].'" onblur="if (this.value == \'\') {this.value = \''.$event['event_organiser'].'\';}" onfocus="if (this.value == \''.$event['event_organiser'].'\') {this.value = \'\';}"/>';
    
    if ($event['event_telephone_use']) $content .='<input type="text" '.$event['event_telephone_checked'].' name="event_telephone" value="'.$values['event_telephone'].'" onblur="if (this.value == \'\') {this.value = \''.$event['event_telephone'].'\';}" onfocus="if (this.value == \''.$event['event_telephone'].'\') {this.value = \'\';}"/>';
    
    if ($event['event_cost_use']) $content .='<input type="text" '.$event['event_cost_checked'].' name="event_cost" value="'.$values['event_cost'].'" onblur="if (this.value == \'\') {this.value = \''.$event['event_cost'].'\';}" onfocus="if (this.value == \''.$event['event_cost'].'\') {this.value = \'\';}"/>';

    if ($event['event_register_use']) $content .='<p><input type="checkbox" name="event_register" value="checked"> '.$event['event_register'].'<p>
    <p>Number of places available: <input type="text" style="width:3em" '.$event['event_number'].' name="event_number" value="" /> (Leave blank for unlimited places)</p>';

    if ($event['allowimage']) {
        $content .= '<p>'.$event['event_image_upload'].': <input type="file" name="event_image_upload"/></p>';
    }

    if ($event['event_details_use']) 
        $content .='<textarea rows="8" '.$event['event_details_checked'].' name="event_details" onblur="if (this.value == \'\') {this.value = \''.$event['event_details'].'\';}" onfocus="if (this.value == \''.$event['event_details'].'\') {this.value = \'\';}"/>'.$values['event_details'].'</textarea>';

    if ($event['allowrepeat']) {
        $content .= '<p><input type="checkbox" name="event_repeat" value="checked"> Repeat Event </p>
        <p>Repeat every <select style="width:5em;" name="thenumber">';
        for ($i = 0; $i <= 4; ++$i) {
            $content .= '<option value="'.$i.'">'.$thenumbers[$i].'</option>';
        }
        $content .= '</select>&nbsp;';
        $content .= '<select style="width:8em;" name="theday">';
        for ($i = 0; $i <= 10; ++$i) {
            $content .= '<option value="'.$i.'">'.$thedays[$i].'</option>';
        }
        $content .= '</select>';
        $content .= ' for <input type="text" style="width:3em;" name="therepetitions" value="12"  onblur="if (this.value == \'\') {this.value = \'12\';}" onfocus="if (this.value == \'12\') {this.value = \'\';}">&nbsp;
        <select name="thewmy" style="width:5em;">
        <option value="weeks">Weeks</option>
        <option value="months">Months</option>
        <option value="years">Years</option>
        </select></p>';
    }  
    if ($event['event_category_use']){

        //handle require/error class
        if ($event['event_category_checked']=='class="required"') {
            $class = '&class=required';
        } elseif ($event['event_category_checked']=='class="error"') {
            $class = '&class=error';
        }

        //handle selected
        if ($values['event_category']) $selected = '&selected='.$values['event_category'][0];

        //exclude uncategorised
        $exclude = '&exclude=1';

        $content .=wp_dropdown_categories('show_option_none=Select a category...&tab_index=4&orderby=name&taxonomy=category&hide_empty=0'.$exclude.$selected.$class.'&echo=0');
    }

    if ($event['event_tags_use']) $content .= '<input type="text" '.$event['event_tags_checked'].' name="event_tags" value="'.$values['event_tags'].'" onblur="if (this.value == \'\') {this.value = \''.$event['event_tags'].'\';}" onfocus="if (this.value == \''.$event['event_tags'].'\') {this.value = \'\';}"/>';

    if ( is_user_logged_in() ) {
        $content .= '<input type="hidden" name="event_author" value="'.$values['event_author'].'">
        <input type="hidden" name="event_author_email" value="'.$values['event_author_email'].'">';
    } else {
        $content .= '<input type="text" '.$event['event_author_checked'].' name="event_author" value="'.$values['event_author'].'" onblur="if (this.value == \'\') {this.value = \''.$event['event_author'].'\';}" onfocus="if (this.value == \''.$event['event_author'].'\') {this.value = \'\';}"/>
    <input type="text" '.$event['event_author_email_checked'].' name="event_author_email" value="'.$values['event_author_email'].'" onblur="if (this.value == \'\') {this.value = \''.$event['event_author_email'].'\';}" onfocus="if (this.value == \''.$event['event_author_email'].'\') {this.value = \'\';}"/>'.
        $event['event_captcha_label'].': ' . $values['thesum'] . ' = <input type="text" style="width:3em;font-size:inherit;" label="Sum" '.$event['event_captcha_checked'].' name="event_captcha" value="'.$values['event_captcha'].'">
        <input type="hidden" name="answer" value="'.$values['answer'].'" />
        <input type="hidden" name="thesum" value="'.$values['thesum'].'" />';
    }
    $content .= '<input type="submit" value="Submit" id="submit" name="qemguestsubmit" />
    </form>';
    }
    $content .= '</div>';
    echo $content;
}

function qem_guest_process_form($values,$guest) {

    $event_items = qem_guest_list();
    $event = array();

    foreach ($event_items as $item) {
        $event[$item] = $_POST[$item];
        if ($event[$item] == $guest[$item]) $event[$item] = '';
        $event[$item]  = filter_var($event[$item] , FILTER_SANITIZE_STRING);
    }
    
    if (!$event['event_title']) $event['event_title'] = 'No Title';
    if (!$event['event_date']) $event['event_date'] = time();
    else $event['event_date'] = strtotime($event['event_date']);
    $event['event_end_date'] = strtotime($event['event_end_date']);

    if ( !$guest['moderate'] || current_user_can('editor') || current_user_can('administrator')) $publish = 'publish';
    else $publish = 'pending';
    if (is_user_logged_in() && $guest['moderateusers']) $publish = 'pending';
    if ($guest['usercreation'] ) {
        register_new_user( $event['event_author'], $event['event_author_email'] );
        $user = get_userdatabylogin($event['event_author']);
        $userid = $user->ID;
    }

    //handle page refresh - no duplicates based on title

        //no comments by default
        
        $new_post = array(
            'post_title'    => $event['event_title'],
            'post_content'  => $event['event_details'],
            'post_category' => array($_POST['cat']),
            'tags_input'    => $event['event_tags'],
            'post_status'   => $publish,
            'post_type' => 'event',
            'post_author' => $userid,
            'comment_status' => 'closed'
        );

        if (!function_exists( 'wp_handle_upload' ) ) require_once( ABSPATH . 'wp-admin/includes/file.php' );
        $filename = $_FILES['event_image_upload']['name'];
        if ($filename) {
            $uploadedfile = $_FILES['event_image_upload'];
            $upload_overrides = array( 'test_form' => false );
            $movefile = wp_handle_upload( $uploadedfile, $upload_overrides );

            if ( $movefile ) {
                // $event['event_image'] = get_bloginfo('siteurl') . '/wp-content/uploads/'.$filename;
                $event['event_image'] = $movefile['url'];
            } else {
                echo "File not uploaded\n";
            }
        }

        $pid = wp_insert_post($new_post);

        //stop submitted data being stored as meta_key
        foreach ($event as $item => $value) {
            add_post_meta($pid, $item, $value, true);
        }

        add_post_meta($pid, 'event_image', $event['event_image'], true);

        if ($event['event_repeat']) qem_duplicate_new_post($event,$pid,$publish);
        
        $url =  "//{$_SERVER['HTTP_HOST']}{$_SERVER['REQUEST_URI']}";
        $escaped_url = htmlspecialchars( $url, ENT_QUOTES, 'UTF-8' );

        if ( is_user_logged_in() || !$guest['moderate']) {
            $content = '<div class="qem-guestpost">
            <h3>'.$guest['thankstitle'].'</h3>
            <p><a href="'.$guest['thanksurl'].'">'.$guest['thanksblurb'].'</a></p>
            <p><a href="' . $escaped_url . '">Add Another Event</a></p>
            </div>';

            echo $content;

            $event['event_date'] = date_i18n("d M Y", $event['event_date']);
            $permalink = get_permalink($pid);
            $guest['event_author'] = "Author";
            $guest['event_author_email'] = "Author Email";
            $qem_email = $guest['adminemail'];
            $headers = "From: {$qem_email} \r\n"
. "MIME-Version: 1.0\r\n"
. "Content-Type: text/html; charset=\"utf-8\"\r\n";
            $to = $qem_email;
            $subject = 'Guest Event Posted';
            $message = "<html><p>A new event has been published</p>";
            foreach ($event_items as $item) {
                if ($event[$item]) 
                    $message .= "<p><b>".$guest[$item].":</b>&nbsp;".$event[$item]."<p>";
            }
            $message .= '<p>Event Image: <a href="'.$movefile['url'].'">'.$movefile['url'].'</a></p>';
            $message .= '<p>View Event: <a href="'.$permalink.'">'.$permalink.'</a></p></html>';

            wp_mail($to, $subject, $message, $headers);

        } else {

            $content = '<div class="qem-guestpost">
            <h3>'.$guest['thankstitle'].'</h3>
            <p>'.$guest['pendingblurb'].'</p>
            <p><a href="' . $escaped_url . '">Add Another Event</a></p>
            </div>';
            echo $content;

            $url = admin_url('edit.php?post_type=event');
            $date = date_i18n("d M Y", $event['event_date']);
            //$permalink = get_permalink($pid);

            $qem_email = $guest['adminemail'];
            $headers = "From: {$qem_email} \r\n";
            $to = $qem_email;
            $subject = 'Guest Event Posted';
            $message = "New event awaiting approval";
            foreach ($event_items as $item) {
                if ($event[$item]) 
                    $message .= "<p><b>".$guest[$item].":</b>&nbsp;".$event[$item]."<p>";
            } 
            $message .= '<p>Event Image: <a href="'.$movefile['url'].'">'.$movefile['url'].'</a></p>';
            $message .= 'Login to WordPress to see all posts here: '.$url;

            wp_mail($to, $subject, $message, $headers);
        }
}