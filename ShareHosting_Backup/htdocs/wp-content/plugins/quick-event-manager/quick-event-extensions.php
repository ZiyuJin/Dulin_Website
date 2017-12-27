<?php

function qem_extend_registration_send_email() {
    $event=$title='';
    global $_GET;
    $event = (isset($_GET["event"]) ? $_GET["event"] : null);
    $title = (isset($_GET["title"]) ? $_GET["title"] : null);
    $unixtime = get_post_meta($event, 'event_date', true);
    $date = date_i18n("d M Y", $unixtime);
    $noregistration = '<p>No event selected</p>';
    $register = get_custom_registration_form ();
    $category = 'All Categories';
    if( isset( $_POST['qem_reset_message'])) {
        $event= $_POST['qem_download_form'];
        $title = get_the_title($event);
        delete_option('qem_messages_'.$event);
        delete_option($event);
        qem_admin_notice('Registrants for '.$title.' have been deleted.');
        $eventnumber = get_post_meta($event, 'event_number', true);
        update_option($event.'places',$eventnumber);
    }
    
    if( isset( $_POST['category']) ) {
        $category = $_POST["category"];
    }
    
    if( isset( $_POST['select_event'])  || isset( $_POST['eventid'])) {
        $event = $_POST["eventid"];
        if ($event) {
            $unixtime = get_post_meta($event, 'event_date', true);
            $date = date_i18n("d M Y", $unixtime);
            $title = get_the_title($event);
            $noregistration = '<h2>'.$title.' | '.$date.'</h2><p>Nobody has registered for '.$title.' yet</p>';
        } else {
            $noregistration = '<p>No event selected</p>';
        }
    }
    
    if( isset( $_POST['changeoptions'])) {
        $options = array( 'showevents','category');
        foreach ( $options as $item) $messageoptions[$item] = stripslashes($_POST[$item]);
        $category = $messageoptions['category'];
        update_option( 'qem_messageoptions', $messageoptions );
    }

    if( isset($_POST['qem_email_selected'])) {
        $event = $_POST["qem_download_form"];
        $message = get_option('qem_messages_'.$event);
        $subject = $_POST["subject"];
        $response = $_POST["response"];
        $response = str_replace('\&quot;', '', $response);
        $response = str_replace('\"', '"', $response);
        $fromname = $_POST["fromname"];
        $fromemail = $_POST["fromemail"];
        $auto = qem_get_stored_autoresponder();
        $headers = "From: ".$auto['fromname']." <{$auto['fromemail']}>\r\n"
. "MIME-Version: 1.0\r\n"
. "Content-Type: text/html; charset=\"utf-8\"\r\n";	
        for($i = 0; $i <= 100; $i++) {
            if ($_POST[$i] == 'checked') {
                $email = $message[$i]['youremail'];
                wp_mail($email, $subject, $response, $headers);
            }
        }
        if (is_admin()) qem_admin_notice('Message sent to selected.');
    }

    $content=$current=$all='';
    $messageoptions = qem_get_stored_msg();
    ${$messageoptions['showevents']} = "checked";
    $message = get_option('qem_messages_'.$event);
    
    $auto = qem_get_stored_autoresponder();

    $register['emailselected'] = true;
    
    $places = get_option($event.'places');
    if(!is_array($message)) $message = array();
    $dashboard = '<div class="wrap">
    <h1>Email Registrants</h1>
    <form method="post" action="">'.
        qem_message_categories($category).'
        &nbsp;&nbsp;'.
        qem_get_eventlist ($event,$register,$messageoptions,$category).'</form>
        <div id="qem-widget">
        <form method="post" id="qem_download_form" action="">';
    $content = qem_build_registration_table ($register,$message,'',$event);
    if ($content) {
        $dashboard .= '<h2>'.$title.' | '.$date.'</h2>';
        $dashboard .= '<p>Event ID: '.$event.'</p>';
        $dashboard .= $content;
        $dashboard .='<input type="hidden" name="qem_download_form" value = "'.$event.'" />';
    $dashboard .='<p>'.__('From Name:', 'quick-event-manager').' (<span class="description">'.__('Defaults to your', 'quick-event-manager').' <a href="'. get_admin_url().'options-general.php">'.__('Site Title', 'quick-event-manager').'</a> '.__('if left blank', 'quick-event-manager').'.</span>):<br>
    <input type="text" style="width:50%" name="fromname" value="' . $auto['fromname'] . '" /></p>
    <p>'.__('From Email:', 'quick-event-manager').' (<span class="description">'.__('Defaults to the', 'quick-event-manager').' <a href="'. get_admin_url().'options-general.php">'.__('Admin Email', 'quick-event-manager').'</a> '.__('if left blank', 'quick-event-manager').'.</span>):<br>
    <input type="text" style="width:50%" name="fromemail" value="' . $auto['fromemail'] . '" /></p>    
    <p>'.__('Subject:', 'quick-event-manager').'<br>
    <input style="width:100%" type="text" name="subject" value="' . $auto['subject'] . '"/></p>';
    echo $dashboard;
    wp_editor($response, 'response', $settings = array('textarea_rows' => '20','wpautop'=>false));    
    $dashboard = '<input type="submit" name="qem_email_selected" class="button-secondary" value="Send message to Selected"/>';
    $dashboard .= '</form>';
}
else $dashboard .= $noregistration;
$dashboard .= '</div></div>';		
echo $dashboard;
}

function qem_extend_sortby_name() {
    $register = qem_get_stored_register();
    $arr = qem_extend_collect_email ();
    $usernames = array();
    foreach ($arr as $user) $usernames[] = $user[1];
    array_multisort($usernames, SORT_ASC, $arr); 
    $output = qem_extend_build_the_list($register,$arr,'name');
    echo $output;
}
   
function qem_extend_sortby_email() {
    $register = qem_get_stored_register();
    $arr = qem_extend_collect_email ();
    $usernames = array();
    foreach ($arr as $user) $usernames[] = $user[0];
    array_multisort($usernames, SORT_ASC, $arr); 
    $output = qem_extend_build_the_list($register,$arr,'email');
    echo $output;
}

function qem_extend_build_the_list($register,$emails,$sortby) {
    if( isset($_POST['qem_emaillist'])) {
        $content = qem_extend_create_list_table ($register,$emails,$sortby);
        $content = '<html><style>th {padding: 5px;text-align: left;}
        </style>'.$content.'</html>';
        $qem_email = $_POST['youremail'];
        $headers = "From: {<{$qem_email}>\r\n"
. "MIME-Version: 1.0\r\n"
. "Content-Type: text/html; charset=\"utf-8\"\r\n";	
        wp_mail($qem_email, 'Registation Report', $content, $headers);
        if (is_admin()) qem_admin_notice('Registration list has been sent to '.$qem_email.'.');
    }
    global $current_user;
    get_currentuserinfo();

    if (!$qem_email) {
        $qem_email = $current_user->user_email;
    }

    $dashboard = '<div id="qem-widget">
    <form method="post" action="">
    <style>
    #qem-widget table {margin-bottom: 20px;}
    #qem-widget td {border-top: 1px solid #005F6B;padding: 5px 10px 5px 0;max-width: 400px;vertical-align:top;}
    #qem-widget td b {color: #005F6B;}
    #qem-widget th {padding: 5px 10px 5px 0;max-width: 400px;text-align: left;color: #005F6B;}
    </style>';
    if ($register['moderate']) $dashboard .= '<p>Registrations shown in <em>italics</em> are awaiting moderation.</p>';
    $title = ($sortby == 'name' ? 'Name' : 'Email');
    $dashboard .= '<h1>Registrations Listed by '.$title.'</h1>';
    $dashboard .= qem_extend_create_list_table ($register,$emails,$sortby);
    $dashboard .= '<p>Send to this email address: <input type="text" name="youremail" value="'.$qem_email.'">&nbsp;<input type="submit" name="qem_emaillist" class="button-primary" value="Email List" /></p>
    </form>
    </div>';
    return $dashboard;   
}

function qem_extend_create_list_table ($register,$emails,$sortby) {
    foreach ($emails as $item) {
        if ($item[0]) {
            $order = ($sortby == 'name' ? $item[1].' - '.$item[0] : $item[0].' - '.$item[1]);
            $table .= '<h2>'.$order.'</h2>
            <table cellspacing="0"><tr><th>Event</th><th>Date</th>';
            if ($register['useattend']) $table .= '<th>'.$register['yourattend'].'</th>';
            if ($register['usetelephone']) $table .= '<th>'.$register['yourtelephone'].'</th>';
            if ($register['useplaces']) $table .= '<th>'.$register['yourplaces'].'</th>';
            if ($register['usemorenames']) $table .= '<th>'.$register['morenames'].'</th>';
            if ($register['usemessage']) $table .= '<th>'.$register['yourmessage'].'</th>';
            if ($register['useblank1']) $table .= '<th>'.$register['yourblank1'].'</th>';
            if ($register['useblank2']) $table .= '<th>'.$register['yourblank2'].'</th>';
            if ($register['usedropdown']) {
                $arr = explode(",",$register['yourdropdown']);
                $table .= '<th>'.$arr[0].'</th>';
            }
            if ($register['useselector']) {
                $arr = explode(",",$register['yourselector']);
                $table .= '<th>'.$arr[0].'</th>';
            }
            if ($register['usenumber1']) $table .= '<th>'.$register['yournumber1'].'</th>';
            if ($register['ignorepayment']) $table .= '<th>'.$register['ignorepaymentlabel'].'</th>';
            $table .= '<th>Date Sent</th>';
            if ($register['moderate']) $table .= '<th>Approved</th>';
            if ($payment['ipn']) $table .= '<th>'.$payment['title'].'</th>';
            $table .= '</tr>';
            $table .= qem_extend_build_registrations($register,$item[0]);
            $table .= '</table>'; 
        }
    }
    return $table;
}

function qem_extend_in_array($needle, $haystack, $strict = false) {
    foreach ($haystack as $item) {
        if (($strict ? $item === $needle : $item == $needle) || (is_array($item) && qem_extend_in_array($needle, $item, $strict))) {
            return true;
        }
    }
    return false;
}
  
function qem_extend_collect_email () {
    global $post;
    $emails = array();
    $args = array('post_type'=> 'event','orderby'=>'title','order'=>'ASC','posts_per_page'=> -1);
    query_posts( $args );
    if ( have_posts()){
        while (have_posts()) {
            the_post();
            $id = get_the_id();
            $message = get_option('qem_messages_'.$id);
            if ($message) {
                foreach ($message as $item)
                    if (!qem_extend_in_array($item['youremail'], $emails,$strict))
                        array_push($emails, array($item['youremail'],$item['yourname']));
            }
        }
    }
    wp_reset_query();
    return $emails;
}

function qem_extend_show_registrations () {
    $register = qem_get_stored_register();
    
    if( isset($_POST['qem_emaillist'])) {
        $send_email = $_POST['youremail'];
        $content .= qem_extend_create_list_table ($register,$qem_email,'name');
        $headers = "From: {<{$send_email}>\r\n"
. "MIME-Version: 1.0\r\n"
. "Content-Type: text/html; charset=\"utf-8\"\r\n";	
        wp_mail($send_email, 'Registration List', $content, $headers);
    }
    
    if( isset($_POST['qem_get_email'])) {
        $qem_email = $_POST['youremail'];
    }
    
    global $current_user;
    get_currentuserinfo();
    $yourname = $current_user->user_login;
    
    if (!$qem_email) {
        $qem_email = $current_user->user_email;
    }
    
    $dashboard = '<div id="qem-widget">
    <form method="post" action="">';
    
    if (!$qem_email) {
        $dashboard .= '<p>Enter your email address</p>
    </p><input type="text" name="youremail" value="">&nbsp;<input type="submit" name="qem_get_email" class="button-primary" value="Enter" /></p>';
    
    } else {
        $emails = array(array($qem_email,$yourname));
        $dashboard .= '
        <style>
        #qem-widget table {margin-bottom: 20px;}
        #qem-widget td {border-top: 1px solid #005F6B;padding: 5px 10px 5px 0;max-width: 400px;vertical-align:top;}
        #qem-widget td b {color: #005F6B;}
        #qem-widget th {padding: 5px 10px 5px 0;max-width: 400px;text-align: left;color: #005F6B;}
        </style>
        <h2>Your Registrations</h2>';
        $dashboard .= qem_extend_create_list_table ($register,$emails,'name');
        $dashboard .= '<p>Send to this email address: <input type="text" name="youremail" value="'.$qem_email.'" >&nbsp;<input type="submit" name="qem_emaillist" class="button-primary" value="Send" /></p>';
    }
    
    $dashboard .= '</form></div>';
    
    echo $dashboard;
}

function qem_extend_build_registrations($register,$qem_email) {
    $args = array('post_type'=> 'event','orderby'=>'title','order'=>'ASC','posts_per_page'=> -1);
    
    query_posts( $args );
    
    if ( have_posts()){
        
        while (have_posts()) {
            the_post();
            $title = get_the_title();
            $id = get_the_id();
            $unixtime = get_post_meta($id, 'event_date', true);
            $date = date_i18n("d M Y", $unixtime);
            $message = get_option('qem_messages_'.$id);
            if ($message) {
                foreach ($message as $item) {
                    if (!$item['approved'] && $register['moderate']) $span = 'font-style:italic;';
                    if ($span) $span = ' style="'.$span.'" ';
                    $approved = ($item['approved'] ? 'Yes' : '');
                    if ($item['youremail'] == $qem_email) {
                        
                        
        $num = $num + $item['yourplaces'];
        $span='';
        if ($number && $num > $number) $span = 'color:#CCC;';
        if (!$item['approved'] && $register['moderate']) $span = $span.'font-style:italic;';
        if ($span) $span = ' style="'.$span.'" ';
        $content .= '<tr'.$span.'><td>'.$title.'</td><td>'.$date.'</td>';
        if ($register['useattend']) $content .= '<td>'.$item['notattend'].'</td>';
        if ($register['usetelephone']) $content .= '<td>'.$item['yourtelephone'].'</td>';
        if ($register['useplaces'] && empty($item['notattend'])) $content .= '<td>'.$item['yourplaces'].'</td>';
        elseif ($register['useplaces']) $content .= '<td></td>';
        if ($register['usemorenames']) $content .= '<td>'.$item['morenames'].'</td>';
        if ($register['usemessage']) $content .= '<td>'.$item['yourmessage'].'</td>';
        if ($register['useblank1']) $content .= '<td>'.$item['yourblank1'].'</td>';
        if ($register['useblank2']) $content .= '<td>'.$item['yourblank2'].'</td>';
        if ($register['usedropdown']) $content .= '<td>'.$item['yourdropdown'].'</td>';
        if ($register['useselector']) $content .= '<td>'.$item['yourselector'].'</td>';
        if ($register['usenumber1']) $content .= '<td>'.$item['yournumber1'].'</td>';
        if ($register['ignorepayment']) $content .= '<td>'.$item['ignore'].'</td>';
        if ($item['yourname']) $charles = 'messages';
        $content .= '<td>'.$item['sentdate'].'</td>';
        if ($payment['ipn']) {
            $ipn = ($payment['sandbox'] ? $item['ipn'] : '');
            $content .= ($item['ipn'] == "Paid" ? '<td>'.$payment['paid'].'</td>' : '<td>'.$ipn.'</td>');
        }
                        if ($register['moderate']) $content .= '<td>'.$approved.'</td></tr>';
                    }
                }
            }
        }
    }
    wp_reset_query();
    return $content;
}

function qem_extend_show_report ($atts) {
    $atts = shortcode_atts(array('event'=>'',),$atts,'qemreport');
    if ($atts['event']) $eventid = explode(',',$atts['event']);
    $register = qem_get_stored_register();
    if( isset($_POST['qem_emaillist'])) {
        $content = qem_extend_build_report($register,$eventid);
        $qem_email = $_POST['youremail'];
        $content = '<html><style>th {padding: 5px;text-align: left;}
        </style>'.$content.'</html>';
        $headers = "From: {<{$qem_email}>\r\n"
. "MIME-Version: 1.0\r\n"
. "Content-Type: text/html; charset=\"utf-8\"\r\n";	
        wp_mail($qem_email, 'Registration Report', $content, $headers);
        if (is_admin()) qem_admin_notice('Registration list has been sent to '.$qem_email.'.');
    }
    global $current_user;
    get_currentuserinfo();
    $qem_email = $current_user->user_email;
    $dashboard = '<h1>Registrations Listed by Event</h1>
    <div id="qem-widget">
    <form method="post" id="qem_download_form" action="">';
    $dashboard .= qem_extend_build_report($register,$eventid);
    $dashboard .='<p>Send report to this email address: <input type="text" name="youremail" value="'.$qem_email.'">&nbsp;<input type="submit" name="qem_emaillist" class="button-primary" value="Send Report" /></p>
    </form></div>';
    echo $dashboard;
}

function qem_extend_build_report($register,$eventid) {
    $args = array('post_type'=> 'event','orderby'=>'title','order'=>'ASC','posts_per_page'=> -1);
    $event = event_get_stored_options();
    $payment = qem_get_stored_payment();
    query_posts( $args );
    if ( have_posts()){
        while (have_posts()) {
            the_post();
            $title = get_the_title();
            $id = get_the_id();
            if (empty($eventid) || in_array($id,$eventid)) {
                $unixtime = get_post_meta($id, 'event_date', true);
                $date = date_i18n("d M Y", $unixtime);
                $message = get_option('qem_messages_'.$id);
            
                if ($message) {
                    $dashboard .= '<h2>'.$title.' | '.$date.'</h2>
                    <p>Event ID: '.$id.'</p>';
                    $dashboard .= qem_build_registration_table ($register,$message,'checked',$id);
                }
            }
        }
    }
    wp_reset_query();
    return $dashboard;
}


function qem_extend_notcoming_report () {
    $register = qem_get_stored_register();
    
    if( isset($_POST['qem_emaillist'])) {
        $content = qem_extend_build_notcoming_report($register);
        $qem_email = $_POST['youremail'];
        $content = '<html><style>th {padding: 5px;text-align: left;}
        </style>'.$content.'</html>';
        $headers = "From: {<{$qem_email}>\r\n"
. "MIME-Version: 1.0\r\n"
. "Content-Type: text/html; charset=\"utf-8\"\r\n";	
        wp_mail($qem_email, 'Not attending Report', $content, $headers);
        if (is_admin()) qem_admin_notice('Not attending Report has been sent to '.$qem_email.'.');
    }
    
    if( isset( $_POST['Reset'])) {
        delete_option('qem_removal');
        qem_admin_notice (__('Everyone has been deleted', 'quick-event-manager')) ;
    }
    
    global $current_user;
    get_currentuserinfo();
    $qem_email = $current_user->user_email;
    $dashboard = '<h1>Not attending Report</h1>
    <div id="qem-widget">
    <form method="post" id="qem_download_form" action="">';
    $dashboard .= qem_extend_build_notcoming_report($register,$payment);
    $dashboard .='<p><input type="submit" name="Reset" class="button-primary" style="color: #FFF;" value="'.__('Reset', 'quick-event-manager').'" onclick="return window.confirm( \' '.__('Are you sure you want to delete the list?', 'quick-event-manager').'\' );"/> Send report to this email address: <input type="text" name="youremail" value="'.$qem_email.'">&nbsp;<input type="submit" name="qem_emaillist" class="button-primary" value="Send Report" /></p>
    </form></div>';
    echo $dashboard;
}

function qem_extend_build_notcoming_report($register) {
    $dennis = '';
    $message = get_option('qem_removal');
    $table = '<table cellspacing="0"><tr><th>Event</th><th>Date</th>';
    if ($register['usename']) $table .= '<th>'.$register['yourname'].'</th>';
    if ($register['usemail']) $table .= '<th>'.$register['youremail'].'</th>';
    if ($register['usetelephone']) $table .= '<th>'.$register['yourtelephone'].'</th>';
    if ($register['usemessage']) $table .= '<th>'.$register['yourmessage'].'</th>';  
    $table .= '<th>Date Sent</th></tr>';  
    if ($message) $dennis = qem_extend_build_notcoming_table($register,$message);
    $table .= $dennis.'</table>';
    if (!$dennis) $table .= 'Everybody is attending';
    return $table;
}

function qem_extend_build_notcoming_table($register,$message) {
    foreach($message as $item) {
        $content .= '<tr><td>'.$item['title'].'</td><td>'.$item['date'].'</td>';
        if ($register['usename']) $content .= '<td>'.$item['yourname'].'</td>';
        if ($register['usemail']) $content .= '<td>'.$item['youremail'].'</td>';
        if ($register['usetelephone']) $content .= '<td>'.$item['yourtelephone'].'</td>';
        if ($register['usemessage']) $content .= '<td>'.$item['yourmessage'].'</td>';
        if ($item['yourname']) $charles = true;
        $content .= '<td>'.$item['sentdate'].'</td>';
    }
    if ($charles) return $content;
}

function qem_extend_admin_extension_pages() {
    add_submenu_page('edit.php?post_type=event' , 'Reports' , 'Reports' , 'edit_events' ,'reports' , 'qem_tabbed_extend');
}

function qem_readCSV($csvFile){
	$file_handle = fopen($csvFile, 'r');
	while (!feof($file_handle) ) $line_of_text[] = fgetcsv($file_handle, 1024);
	fclose($file_handle);
	return $line_of_text;
}

function qem_csv_import() {
        if( isset( $_POST['Import'])) {
		$filename = $_FILES['csv_file']['name'];
		if (!empty($filename)) {
			$ext = substr(strrchr($filename,'.'),1);
			if (strpos('csv',$ext) === false) {
                $error = 'error';
                guestlist_admin_notice("Unable to upload file. Check file type - CSV only");
            }
			if (empty($error)) {
				$uploads = wp_upload_dir();
				$csvFile = $uploads['basedir'].'/'.$filename;
				move_uploaded_file($_FILES['csv_file']['tmp_name'], $uploads['basedir'].'/'.$_FILES['csv_file']['name']);
				$csv = qem_readCSV($csvFile);
                $arr = array(
                    'event_title',
                    'event_date',
                    'event_end_date',
                    'event_desc',
                    'event_start',
                    'event_finish',
                    'event_location',
                    'event_address',
                    'event_link',
                    'event_anchor',
                    'event_cost',
                    'event_deposit',
                    'event_deposittype',
                    'event_organiser',
                    'event_telephone',
                    'event_registration',
                    'event_cutoff_date',
                    'event_number',
                    'event_paypal',
                    'event_image',
                    'event_redirect',
                    'event_details',
                    'event_category',
                    'event_tags',
                    );
				for ($i=1;$i<=count($csv);$i++) {
                    $j=0;
                    $event = array();
                    foreach ($arr as $item) {
				        $event[$item] = $csv[$i][$j];
                        $j++;
					}
                    $permalink = qem_process_csv($event,$arr);
                    $eventlist = $eventlist.$permalink;
                }
            }
            qem_admin_notice("The events have been imported.");
        }
    }

	$content .= '<div class="qem-settings"><div class="qem-options">
    <h2>Event Importer</h2>
    <p>If you want to import multiple events you can do so using a CSV.</p>
    <p>It/s fairly simple to set up but the field names and order must be the same as those in the sample CSV or the table below</p>
    <p><a href="'.plugin_dir_url( __FILE__ ).'images/events.csv">Click here to download a sample CSV</a>.</p>
    <p><span style="color:red; font-weight:bold;">'. __('Warning!', 'quick-event-manager').'</span> It is reccomended that you run an import test with a single event to make sure your CSV is properly formatted. Get it wrong and you may have to delete all your imported events!</p>
    <form method="post" enctype="multipart/form-data" action="">
    <p>Select your CSV: <input name="csv_file" type="file"/></p>
    <p><input type="submit" name="Import" class="button-primary" style="color: #FFF;" value="Import Events" /></p>
    </form>
    <h2>Field Values and format</h2>
    <p>The following field names are the same as those used in the <a href="edit.php?post_type=event">'.__('Event Editor', 'quick-event-manager').'</a></p>
    <table>
    <tr><td>event_title</td><td>Plain text only</td></tr>
    <tr><td>event_date</td><td>English DD Mmm YYYY only eg: 3 Oct 2017</td></tr>
    <tr><td>event_end_date</td><td>English DD Mmm YYYY only eg: 3 Oct 2017</td></tr>
    <tr><td>event_desc</td><td>Plain text only</td></tr>
    <tr><td>event_start</td><td>The time the event starts</td></tr>
    <tr><td>event_finish</td><td>The time the event finishes</td></tr>
    <tr><td>event_location</td><td>The name of the location</td></tr>
    <tr><td>event_address</td><td>Plain text only, use commas to seperate each part of the address: 1 Road, Smalltown, Omaha</td></tr>
    <tr><td>event_link</td><td>The full URL of the website: http://quick-plugins.com</td></tr>
    <tr><td>event_anchor</td><td>What displays on the page: Quick Plugins</td></tr>
    <tr><td>event_cost</td><td>A monetary value: £10</td></tr>
    <tr><td>event_deposit</td><td>A monetary value: £3</td></tr>
    <tr><td>event_deposittype</td><td>Use perperson or perevent</td></tr>
    <tr><td>event_organiser</td><td>Plain text only</td></tr>
    <tr><td>event_telephone</td><td>Telephone, email, skype etc</td></tr>
    <tr><td>event_registration</td><td>Use \'checked\' to add a registration form</td></tr>
    <tr><td>event_cutoff_date</td><td>English DD Mmm YYYY only eg: 3 Oct 2017</td></tr>
    <tr><td>event_number</td><td>The number of places available</td></tr>
    <tr><td>event_paypal</td><td>Use \'checked\' to link to PayPal after registration</td></tr>
    <tr><td>event_image</td><td>The URL of the event image</td></tr>
    <tr><td>event_redirect</td><td>Optional redirect URL after registration</td></tr>
    <tr><td>event_details</td><td>The full event description. You will have to use HTML if you want it formatted.</td></tr>
    <tr><td>event_category</td><td>The names of the categories, seperate using commas</td></tr>
    <tr><td>event_tags</td><td>Optional event tags, seperate using commas.</td></tr>
    </table>
    </div>';
    if ($eventlist) $content .= '<div class="qem-options"><h2>Imported Events</h2>'.$eventlist.'</div>';
    $content .= '</div>';
	echo $content;
}

function qem_process_csv($event,$arr) {
    if (!$event['event_title']) return;
    $startdate = strtotime($event["event_date"]);
    $starttime = qem_time($event["event_start"]);
    if (!$startdate) {
        $startdate=time();
    }
    $event["event_date"] = $startdate+$starttime;
    
    if($event["event_end_date"]) {
        $enddate = strtotime($event["event_end_date"]);
        $endtime = qem_time($event["event_finish"]);
        $event["event_end_date"] = ($enddate ? $enddate+$endtime : '');
    }
    
    if($event["event_cutoff_date"]) {
        $cutoffdate = strtotime($event["event_cutoff_date"]);
    }

    global $current_user;
    get_currentuserinfo();
    $yourname = $current_user->user_login;
    
    $cats  = explode(',',$event['event_category']);
    $catlist = array();
    
    foreach($cats as $item) {     
        $cat_ID = get_cat_ID($item);
        $catlist[] = $cat_ID;
    }

    $new_post = array(
        'post_title'    => $event['event_title'],
        'post_content'  => $event['event_details'],
        'post_category' => $catlist,
        'tags_input'    => $event['event_tags'],
        'post_status'   => 'publish',
        'post_type' => 'event',
        'post_author' => $yourname,
        'comment_status' => 'closed'
    );

    $pid = wp_insert_post($new_post);

    foreach ($event as $item => $value) {
        add_post_meta($pid, $item, $value, true);
    }
    $permalink = '<p><a href="'.get_permalink($pid).'">'.$event['event_title'].'</a></p>';
    return $permalink;
}
