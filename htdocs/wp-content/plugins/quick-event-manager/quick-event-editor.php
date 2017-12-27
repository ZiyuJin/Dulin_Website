<?php

function event_custom_columns($column) {
    global $post;
    $event=get_the_ID();
    $custom = get_post_custom();
    switch ($column) {
        case "event_date":$date = $custom["event_date"][0];echo date_i18n("d M Y", $date);
        if ($custom["event_end_date"][0]) {
            $enddate = $custom["event_end_date"][0]; echo ' - '. date_i18n("d M Y", $enddate);
        }
        break;
        case "event_start" : echo $custom["event_start"][0];
        if ($custom["event_finish"][0]) echo ' - ' . $custom["event_finish"][0];break;
        case "event_location" : echo $custom["event_location"][0];break;
        case "event_website" : echo $custom['event_link'][0];	break;
        case "event_cost" : echo $custom["event_cost"][0];break;
        case "number_coming": echo qem_attending($event);;break;
        case 'categories' :$category = get_the_term_list( get_the_ID(), 'category', '', ', ', '' );echo __( $category );break;
        case 'author' : echo get_the_author();break;
        case 'date' : echo get_the_date();break;
    }
}

function qem_attending($event) {
    global $post;
    $number = get_post_meta($post->ID, 'event_number', true);
    $on=$off=$str='';
    $whoscoming = get_option('qem_messages_'.$event);
    if ($whoscoming) {
        foreach($whoscoming as $item)
            $str = $str + $item['yourplaces'];
    }
    if ($number == $str) {$on='<span style="color:red">';$off='</span>';}
    if (!$number && ! $str) return;
    $places = ($number ? '/'.$number : '');
    $attending = ($str ? $str : '0');
    return $on.$attending.$places.$off;
}

function event_column_register_sortable( $columns ) {
    $columns['event_date'] = 'event_date';
    $columns['event_start'] = 'event_start';
    $columns['event_location'] = 'event_location';
    $columns['number_coming'] = 'number_coming';
    $columns['categories'] = 'category_name';
    $columns['author'] = 'author_name';
    $columns['date'] = 'date';
    return $columns;
}

function event_edit_columns($columns) {
    $columns = array(
        "cb" => "<input type=\"checkbox\" />",
        "title" => __('Event', 'quick-event-manager'),
        "event_date" => __('Event Date', 'quick-event-manager'),
        "event_start" => __('Event Time', 'quick-event-manager'),
        "event_location" => __('Venue', 'quick-event-manager'),
        "number_coming" => __('Attending<br>/ Places', 'quick-event-manager'),
        "categories" => __( 'Categories' ),
        "author" => __( 'Author' ),
        "date" => __( 'Date' )
    );
    return $columns;
}

function event_details_meta() {
    global $post;
    $perevent=$perperson=$localcutoffdate = $enddate = $output = '';
    $event = event_get_stored_options();
    $register = qem_get_stored_register();
    $payment = qem_get_stored_payment();
    $display = event_get_stored_display();
    $eventdate = get_event_field('event_date');
	if (empty($eventdate)) $eventdate = time();
	$date = date("d M Y", $eventdate);
    $localdate = date_i18n("d M Y", $eventdate);
    $thedays = array('Week','Month','Sunday','Monday','Tuesday','Wednesday','Thursday','Friday','Saturday');
    $thenumbers = array('','First','Second','Third','Fourth');
    
	$eventenddate = get_event_field('event_end_date');
	if ($eventenddate) {
        $enddate = date("d M Y", $eventenddate);
        $localenddate = date_i18n("d M Y", $eventenddate);
    }
    
    $cutoffdate = get_event_field('event_cutoff_date');
    if ($cutoffdate) {
        $localcutoffdate = date_i18n("d M Y", $cutoffdate);
        $cutoffdate = date("d M Y", $cutoffdate);
    }
    
    $event_show_cutoff_blurb = get_event_field('event_show_cutoff_blurb');

    if ($register['addtoall'] && !get_event_field('event_date')) $useform = 'checked';
    else $useform = get_event_field("event_register");
    $usepaypal ='';

    $deposittype = (get_event_field('event_deposittype') ? get_event_field('event_deposittype') : 'perperson');
    ${$deposittype} = 'checked';
    ${$perevent} = 'checked';
    
    
    if ($register['paypal'] && !get_event_field('event_date') || get_event_field('event_paypal')=='checked') $usepaypal = 'checked';
	$output .= '<p><em>'.__('Empty fields are not displayed', 'quick-event-manager').' '.__('See the plugin', 'quick-event-manager').' <a href="options-general.php?page=quick-event-manager/settings.php">'.__('settings', 'quick-event-manager').'</a> '.__('page for options', 'quick-event-manager').'.</em></p>
    <p>Event ID: '.$post->ID.'</p>
    <table>
    <tr>
    <td width="20%"><label>'.__('Date', 'quick-event-manager').': </label></td>
    <td width="80%"><input type="text" class="qem_input" style="width:30%;border:1px solid #415063;" id="qemdate" name="event_date" value="' . $date . '" /> <em>'.__('Local date', 'quick-event-manager').' ('.__('as it appears on your website', 'quick-event-manager').'): '.$localdate.'</em>.</td>
    <script type="text/javascript">jQuery(document).ready(function($) {});</script>
    </tr>
    <tr>
    <td width="20%"><label>'.__('End Date', 'quick-event-manager').': </label></td>
    <td width="80%"><input type="text" class="qem_input" style="width:30%;border:1px solid #415063;"  id="qemenddate" name="event_end_date" value="' . $enddate . '" /> <em>'.__('Leave blank for one day events', 'quick-event-manager').'.</em>';
    if ($eventenddate) $output .= ' <em>'.__('Current end date', 'quick-event-manager').' ('.__('as it appears on your website', 'quick-event-manager').'): '.$localenddate.'</em>';
    $output .= '</td>
    <script type="text/javascript">jQuery(document).ready(function($) {});</script>
    </tr>
    <tr>
    <td width="20%"><label>'.__('Short Description', 'quick-event-manager').': </label></td>
    <td width="80%"><input type="text" class="qem_input" style="border:1px solid #415063;" name="event_desc" value="' . get_event_field("event_desc") . '" />
    </td>
    </tr>
    <tr>
    <td width="20%"><label>'.__('Time', 'quick-event-manager').'</label></td>
    <td width="80%">' . $event['start_label'] . ' <input type="text" class="qem_input" style="width:40%;border:1px solid #415063;"  name="event_start" value="' . get_event_field("event_start") . '" /> ' . $event['finish_label'] . '&nbsp;<input type="text" style="overflow:hidden;border:1px solid #415063;"   name="event_finish" value="' . get_event_field("event_finish") . '" /><br>
    <span class="description">'.__('Start times in the format 8.23 am/pm, 8.23, 8:23 and 08:23 will be used to order events by time and date. All other formats will display but won\'t contribute to the event ordering', 'quick-event-manager').'.</span> 
    </td>
    </tr>';
    if ($display['usetimezone']) {
        $tz = get_event_field("selected_timezone");
        ${$tz} = 'selected';        
        $output .='<tr>
		<td width="20%"><label>'.__('Timezone', 'quick-event-manager').': </label></td>
		<td width="80%">';
        if(get_event_field("event_timezone") ) $output .= '<b>Current timezone:</b> ' . get_event_field("event_timezone") .'.&nbsp;&nbsp;';
        $output .='Select a new timezone or enter your own:<br>
        <select style="border:1px solid #415063;" name="event_timezone" id="event_timezone">
        <option value="">None</option>
        <option '.$Eni.' value="Eniwetok, Kwajalein">(GMT -12:00) Eniwetok, Kwajalein</option>       
        <option '.$Mid.' value="Midway Island, Samoa">(GMT -11:00) Midway Island, Samoa</option>       
        <option '.$Hwa.' value="Hawaii">(GMT -10:00) Hawaii</option>       
        <option '.$Ala.' value="Alaska">(GMT -9:00) Alaska</option>       
        <option '.$Pac.' value="Pacific Time (US &amp; Canada)">(GMT -8:00) Pacific Time (US &amp; Canada)</option>       
        <option '.$Mou.' value="Mountain Time (US &amp; Canada)">(GMT -7:00) Mountain Time (US &amp; Canada)</option>       
        <option '.$Cen.' value="Central Time (US &amp; Canada), Mexico City">(GMT -6:00) Central Time (US &amp; Canada), Mexico City</option>       
        <option '.$Eas.' value="Eastern Time (US &amp; Canada), Bogota, Lima">(GMT -5:00) Eastern Time (US &amp; Canada), Bogota, Lima</option>       
        <option '.$Atl.' value="Atlantic Time (Canada), Caracas, La Paz">(GMT -4:00) Atlantic Time (Canada), Caracas, La Paz</option>       
        <option '.$New.' value="Newfoundland">(GMT -3:30) Newfoundland</option>       
        <option '.$Bra.' value="Brazil, Buenos Aires, Georgetown">(GMT -3:00) Brazil, Buenos Aires, Georgetown</option>       
        <option '.$Mia.' value="Mid-Atlantic">(GMT -2:00) Mid-Atlantic</option>       
        <option '.$Azo.' value="Azores, Cape Verde Islands">(GMT -1:00 hour) Azores, Cape Verde Islands</option>       
        <option '.$Wes.' value="Western Europe Time, London, Lisbon, Casablanca">(GMT) Western Europe Time, London, Lisbon, Casablanca</option>       
        <option '.$Bru.' value="Brussels, Copenhagen, Madrid, Paris">(GMT +1:00 hour) Brussels, Copenhagen, Madrid, Paris</option>       
        <option '.$Kal.' value="Kaliningrad, South Africa">(GMT +2:00) Kaliningrad, South Africa</option>       
        <option '.$Bag.' value="Baghdad, Riyadh, Moscow, St. Petersburg">(GMT +3:00) Baghdad, Riyadh, Moscow, St. Petersburg</option>       
        <option '.$Teh.' value="Tehran">(GMT +3:30) Tehran</option>       
        <option '.$Abu.' value="Abu Dhabi, Muscat, Baku, Tbilisi">(GMT +4:00) Abu Dhabi, Muscat, Baku, Tbilisi</option>       
        <option '.$Kab.' value="Kabul">(GMT +4:30) Kabul</option>       
        <option '.$Eka.' value="Ekaterinburg, Islamabad, Karachi, Tashkent">(GMT +5:00) Ekaterinburg, Islamabad, Karachi, Tashkent</option>       
        <option '.$Bom.' value="Bombay, Calcutta, Madras, New Delhi">(GMT +5:30) Bombay, Calcutta, Madras, New Delhi</option>       
        <option '.$Kat.' value="Kathmandu">(GMT +5:45) Kathmandu</option>       
        <option '.$Alm.' value="Almaty, Dhaka, Colombo">(GMT +6:00) Almaty, Dhaka, Colombo</option>       
        <option '.$Ban.' value="Bangkok, Hanoi, Jakarta">(GMT +7:00) Bangkok, Hanoi, Jakarta</option>       
        <option '.$Bei.' value="Beijing, Perth, Singapore, Hong Kong">(GMT +8:00) Beijing, Perth, Singapore, Hong Kong</option>       
        <option '.$Tok.' value="Tokyo, Seoul, Osaka, Sapporo, Yakutsk">(GMT +9:00) Tokyo, Seoul, Osaka, Sapporo, Yakutsk</option>       
        <option '.$Ade.' value="Adelaide, Darwin">(GMT +9:30) Adelaide, Darwin</option>       
        <option '.$Aus.' value="Eastern Australia, Guam, Vladivostok">(GMT +10:00) Eastern Australia, Guam, Vladivostok</option>       
        <option '.$Mag.' value="Magadan, Solomon Islands, New Caledonia">(GMT +11:00) Magadan, Solomon Islands, New Caledonia</option>       
        <option '.$Auk.' value="Auckland, Wellington, Fiji, Kamchatka">(GMT +12:00) Auckland, Wellington, Fiji, Kamchatka</option> 
        </select>
        <br><span class="description">The option to display timezones is set on the <a href="options-general.php?page=quick-event-manager/settings.php&tab=display">Event Display</a> page.</span>
    </td>
    </tr>';}
    $output .='
    <tr>
    <td width="20%"><label>'.__('Venue', 'quick-event-manager').': </label></td>
    <td width="80%"><input type="text" class="qem_input" style="border:1px solid #415063;"  name="event_location" value="' . get_event_field("event_location") . '" /></td>
    </tr>
    <tr>
    <td width="20%"><label>'.__('Address', 'quick-event-manager').': </label></td>
    <td width="80%"><input type="text" class="qem_input" style="border:1px solid #415063;"  name="event_address" value="' . get_event_field("event_address") . '" /></td>
    </tr>
    <tr>
    <td width="20%"><label>'.__('Website', 'quick-event-manager').': </label></td>
    <td width="80%"><input type="text" class="qem_input" style="width:40%;border:1px solid #415063;"  name="event_link" value="' . get_event_field("event_link") . '" /><label> '.__('Display As', 'quick-event-manager').':</label>&nbsp;<input type="text" style="width:40%;overflow:hidden;border:1px solid #415063;"  name="event_anchor" value="' . get_event_field("event_anchor") . '" /></td>
    </tr>
    <tr>
    <td width="20%"><label>'.__('Cost', 'quick-event-manager').': </label></td>
    <td width="80%"><input type="text" class="qem_input" style="border:1px solid #415063;" name="event_cost" value="' . get_event_field("event_cost") . '" /></td>
    </tr>
    <tr>
    <td width="20%"><label>'.__('Deposit', 'quick-event-manager').': </label></td>
    <td width="80%"><input type="text" class="qem_input" style="width:10em;border:1px solid #415063;" name="event_deposit" value="' . get_event_field("event_deposit") . '" />&nbsp;<input type="radio" name="event_deposittype" value="perperson" ' . $perperson . ' /> '.__('Per person', 'quick-event-manager').' <input type="radio" name="event_deposittype" value="perevent" ' . $perevent . ' /> '.__('Per Event', 'quick-event-manager').'<br><span class="description">'.__('If you add a deposit this amount will be used for payments', 'quick-event-manager').'</span></td>
    </tr>
    <tr>
    <td width="20%"><label>'.__('Organiser', 'quick-event-manager').': </label></td>
    <td width="80%"><input type="text" class="qem_input" style="border:1px solid #415063;" name="event_organiser" value="' . get_event_field("event_organiser") . '" /></td>
    </tr>
    <tr>
    <td width="20%"><label>'.__('Organiser Contact Details', 'quick-event-manager').': </label></td>
    <td width="80%"><input type="text" class="qem_input" style="border:1px solid #415063;" name="event_telephone" value="' . get_event_field("event_telephone") . '" /></td>
    </tr>
    <tr>
    <td width="20%"><label>'.__('Registration Form', 'quick-event-manager').': </label></td>
    <td width="80%"><input type="checkbox" style="" name="event_register" value="checked" ' . $useform  . '> Add registration form to this event. <a href="options-general.php?page=quick-event-manager/settings.php&tab=register">'.__('Registration form settings', 'quick-event-manager').'</a></td></tr>
    <tr>
    <td></td>
    <td width="80%">'.__('Notes', 'quick-event-manager').':<br>
    1. '.__('You can create a custom registration form for this event using the options at the bottom of this page', 'quick-event-manager').'.<br>
    2. If you are using the <a href="options-general.php?page=quick-event-manager/settings.php&tab=auto">autoresponder</a> you can create a reply message for this event. See the \'Registration Confirmation Message\' at the bottom of this page.</td>
    </tr>
    <tr>
    <td width="20%"><label>'.__('Redirect to a URL after registration', 'quick-event-manager').': </label></td>
    <td width="80%"><input type="text" class="qem_input" style="border:1px solid #415063;" name="event_redirect" value="' . get_event_field("event_redirect") . '" /><br>
    <input type="checkbox" style="" name="event_redirect_id" value="checked" ' . get_event_field("event_redirect_id") . ' /> Add event ID to redirect URL</td>
    </tr>
    <tr>
    <td width="20%"><label>'.__('Registration Cutoff Date', 'quick-event-manager').': </label></td>
    <td width="80%"><input type="text" class="qem_input" style="width:40%;border:1px solid #415063;" id="qemcutoffdate" name="event_cutoff_date" value="' . $cutoffdate . '" /> <em>'.__('Local date', 'quick-event-manager').': '.$localcutoffdate.'</em>.</td>
    </tr>
    <tr>
    <td></td>
    <td><input type="checkbox" style="" name="event_show_cutoff" value="checked" ' . get_event_field("event_show_cutoff") . ' /> Show cutoff date on event page. Message to display:<br>
    <input type="text" class="qem_input" style="border:1px solid #415063;" name="event_show_cutoff_blurb" value="' . $event_show_cutoff_blurb . '" / />
    </td>
    </tr>
    
    <tr>
    <td width="20%"><label>'.__('Places Available', 'quick-event-manager').': </label></td>
    <td><input type="text" class="qem_input" style="width:3em;border:1px solid #415063;" name="event_number" value="' . get_event_field("event_number") . '" /></td>
    </tr>
    <tr>
    <td width="20%"><label>'.__('Maximum number of places per registration', 'quick-event-manager').': </label></td>
    <td><input type="text" class="qem_input" style="width:3em;border:1px solid #415063;" name="event_maxplaces" value="' . get_event_field("event_maxplaces") . '" /> <input type="checkbox" style="" name="event_requiredplaces" value="checked" ' . get_event_field("event_requiredplaces") . '> '.__('Make this required number', 'quick-event-manager').' <input type="checkbox" style="" name="event_getemails" value="checked" ' . get_event_field("event_getemails") . '> '.__('Collect emails', 'quick-event-manager').'</td>
    </tr>
    
    <tr>
    <td width="20%"><label>'.__('Payment', 'quick-event-manager').': </label></td>
    <td><input type="checkbox" name="event_paypal" value="checked" ' . $usepaypal . ' /> '.__('Link to paypal after registration', 'quick-event-manager').'. <a href="options-general.php?page=quick-event-manager/settings.php&tab=payment">'.__('Payment settings', 'quick-event-manager').'</a>.</td>
    </tr>
    
    <tr>
    <td width="20%"><label>'.__('Hide Event', 'quick-event-manager').': </label></td>
    <td width="80%"><input type="checkbox" style="" name="hide_event" value="checked" ' . get_event_field("hide_event") . '> '.__('Hide this event in the event list (only display on the calendar)', 'quick-event-manager').'.</td>
    </tr>
    <tr>
    <td style="vertical-align:top;"><label>'.__('Event Image', 'quick-event-manager').': </label></td>';
    if (get_event_field("event_image")) { 
		$output .= '
			<td><img class="qem-image qem-no-image" rel="'.plugin_dir_url( __FILE__ ).'images/no_image.png" alt="'.plugin_dir_url( __FILE__ ).'images/image_error.png" src=' . get_event_field("event_image") . '></td>';
	} else {
		$output .= '
			<td><img class="qem-image qem-no-image" rel="'.plugin_dir_url( __FILE__ ).'images/no_image.png" alt="'.plugin_dir_url( __FILE__ ).'images/image_error.png" src="'.plugin_dir_url( __FILE__ ).'images/no_image.png"></td>';
	}
    $output .= '</tr>
    <tr>
    <td></td>
    <td><input id="event_image" type="hidden" name="event_image" value="' . get_event_field("event_image") . '" /><input id="upload_event_image" class="button" type="button" value="Upload Image" /> &nbsp <input id="remove_event_image" class="button" type="button" value="Remove Image" /></td>
    </tr>
    <tr>
    <td style="vertical-align:top"><label>'.__('Repeat Event', 'quick-event-manager').': </label></td>
    <td><span style="color:red;font-weight:bold;">'.__('Warning', 'quick-event-manager').':</span> '.__('Only use once or you will get lots of duplicated events', 'quick-event-manager').'<br />
    <p><input type="checkbox" name="event_repeat" value="checked"> Repeat Event </p>';
        $output .= '<form method="post" action="">';
        $output .= __('Repeat every', 'quick-event-manager').' <select style="width:7em;" name="thenumber">';
        for ($i = 0; $i <= 4; ++$i) {
            $output .= '<option value="'.$i.'">'.$thenumbers[$i].'</option>';
        }
        $output .= '</select>&nbsp;';
        $output .= '<select style="width:8em;" name="theday">';
        for ($i = 0; $i <= 8; ++$i) {
            $output .= '<option value="'.$i.'">'.$thedays[$i].'</option>';
        }
        $output .= '</select>';
        $output .= ' '.__('for', 'quick-event-manager').' <input type="text" style="width:3em;" name="therepetitions" value="12"  onblur="if (this.value == \'\') {this.value = \'12\';}" onfocus="if (this.value == \'12\') {this.value = \'\';}">&nbsp;
        <select name="thewmy" style="width:7em;">
        <option value="weeks">'.__('Weeks', 'quick-event-manager').'</option>
        <option value="months">'.__('Months', 'quick-event-manager').'</option>
        <option value="years">'.__('Years', 'quick-event-manager').'</option>
        </select>
    </tr>';
    $event = get_the_ID();
    $title = get_the_title();
    $whoscoming = get_option('qem_messages_'.$event);
    if ($whoscoming) {
        foreach($whoscoming as $item) $event_names .= $item['yourname'].', ';
        $event_names = substr($event_names, 0, -2); 
        $output .= '<tr>
        <td>'.__('Attendees', 'quick-event-manager').' ('.__('names and emails collected from the', 'quick-event-manager').' <a href="options-general.php?page=quick-event-manager/settings.php&tab=register">'.__('registration form', 'quick-event-manager').'</a>)</td>
        <td><input type="text" class="qem_input" style="border:1px solid #415063;" name="event_names" value="' . $event_names.'" /></td>
        </tr>
        <tr>
        <td></td>
        <td><a href="admin.php?page=quick-event-manager/quick-event-messages.php&event='.$event.'&title='.$title.'">'.__('View Full Registration Details', 'quick-event-manager').'</a></td>
        <tr>';}
    $output .='</table>';
    $output .= wp_nonce_field('qem_nonce','save_qem');
    $qemkey = get_option('qpp_key');
    if (!$qemkey['authorised']) {
        $output .= '<div class="qemupgrade"><a href="admin.php?page=quick-event-manager/settings.php&tab=incontext">
        <h3>Upgrade to Pro for just $10</h3>
        <p>Upgrading gives you access the Guest Event creator, a range of registration reports and the very cool \'In Context Checkout\'. </p>
        <p>Click to find out more</p>
        </a></div>';
    }
	echo $output;
	}

function event_details_reg() {
    global $post;
    $event = event_get_stored_options();
    $register = qem_get_stored_register();
    $payment = qem_get_stored_payment();
    $display = event_get_stored_display();
    $output = '<p><input type="checkbox" style="" name="usecustomform" value="checked" ' . get_event_field("usecustomform"). '> '.__('Use custom form settings', 'quick-event-manager').'</p>
    <p><em>'.__('Check the fields you want to display on the form.', 'quick-event-manager').' '.__('See the plugin', 'quick-event-manager').' <a href="options-general.php?page=quick-event-manager/settings.php&tab=register">'.__('Registration', 'quick-event-manager').'</a> '.__('page for options and to change the order of the fields.', 'quick-event-manager').'.</em></p>
    <p>
    <input type="checkbox" name="usename" value="checked" ' . get_event_field("usename"). '> '.__('Name', 'quick-event-manager').'<br>
    <input type="checkbox" name="usemail" value="checked" ' . get_event_field("usemail"). '> '.__('Email', 'quick-event-manager').'<br>
    <input type="checkbox" name="usetelephone" value="checked" ' . get_event_field("usetelephone"). '> '.__('Telephone', 'quick-event-manager').'<br>
    <input type="checkbox" name="useplaces" value="checked" ' . get_event_field("useplaces"). '> '.__('Places', 'quick-event-manager').'<br>
    <input type="checkbox" name="usemessage" value="checked" ' . get_event_field("usemessage"). '> '.__('Message', 'quick-event-manager').'<br>
    <input type="checkbox" name="useattend" value="checked" ' . get_event_field("useattend"). '> '.__('Not Attending', 'quick-event-manager').'<br>
    <input type="checkbox" name="useblank1" value="checked" ' . get_event_field("useblank1"). '> '.__('User defined', 'quick-event-manager').' 1<br>
    <input type="checkbox" name="useblank2" value="checked" ' . get_event_field("useblank2"). '> '.__('User defined', 'quick-event-manager').' 2<br>
    <input type="checkbox" name="usedropdown" value="checked" ' . get_event_field("usedropdown"). '> '.__('Dropdown', 'quick-event-manager').'<br>
    <input type="checkbox" name="useselector" value="checked" ' . get_event_field("useselector"). '> '.__('Dropdown', 'quick-event-manager').'<br>
    <input type="checkbox" name="usenumber1" value="checked" ' . get_event_field("usenumber1"). '> '.__('Number', 'quick-event-manager').'<br>
    <input type="checkbox" name="useaddinfo" value="checked" ' . get_event_field("useaddinfo"). '> '.__('Additional Info (displays as plain text)', 'quick-event-manager').'<br>
    <input type="text" name= "addinfo" class="qem_input" value="' . get_event_field("addinfo"). '"></br>
    <input type="checkbox" name="usemorenames" value="checked" ' . get_event_field("usemorenames"). '> '.__('Show box to add more names if number attending is greater than 1', 'quick-event-manager').'<br>
    <input type="checkbox" name="moreemails" value="checked" ' . get_event_field("moreemails"). '> '.__('Collect email addresses for all attendees', 'quick-event-manager').'<br>
    <input type="checkbox" name="usecopy" value="checked" ' . get_event_field("usecopy"). '>'.__('Copy Message', 'quick-event-manager').'<br>
    <input type="checkbox" name="useterms" value="checked" ' . get_event_field("useterms"). '> '.__('Include Terms and Conditions checkbox', 'quick-event-manager').'<br>
    <input type="checkbox" name="usecaptcha" value="checked" ' . get_event_field("usecaptcha"). '>'.__('Captcha', 'quick-event-manager').'<br>';
    $output .= wp_nonce_field('qem_nonce','save_qem');
	echo $output;
}

function get_event_field($event_field) {
	global $post;
	$custom = get_post_custom($post->ID);
	if (isset($custom[$event_field])) return $custom[$event_field][0];
	}

function save_event_details() {
    global $post;
    $eventdetails = event_get_stored_options();
    $event = get_the_ID();
	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) return;
    
    if (! isset( $_POST['save_qem'] ) || ! wp_verify_nonce( $_POST['save_qem'], 'qem_nonce' ) ) {
        return;
    }
	
    if(isset($_POST["event_date"])) {
        $startdate = strtotime($_POST["event_date"]);
        $starttime = qem_time($_POST["event_start"]);
        if (!$startdate) {
            $startdate=time();
        }
        $newdate = $startdate+$starttime;
        update_post_meta($post->ID, "event_date", $newdate);
    }
    
    if(isset($_POST["event_end_date"])) {
        $enddate = strtotime($_POST["event_end_date"]);
        $endtime = qem_time($_POST["event_finish"]);
        $newenddate = ($enddate ? $enddate+$endtime : '');
        update_post_meta($post->ID, "event_end_date", $newenddate);
    }
    
    
    if(isset($_POST["event_cutoff_date"])) {
        $cutoffdate = strtotime($_POST["event_cutoff_date"]);
        update_post_meta($post->ID, "event_cutoff_date", $cutoffdate);
    }
    
    save_event_field("event_desc");
    save_event_field("event_start");
    save_event_field("event_finish");
    save_event_field("event_timezone");
    if ($_POST["event_timezone"] == "Eastern Australia, Guam, Vladivostok") $sel = "Aus";
    elseif ($_POST["event_timezone"] == "Mid-Atlantic") $sel = "Mia";
    else $sel = substr($_POST["event_timezone"],0,3);
    update_post_meta($post->ID, "selected_timezone", $sel);
    save_event_field("event_custom_timezone");
    save_event_field("event_location");
    save_event_field("event_address");
    save_event_field("event_link");
    save_event_field("event_anchor");
    save_event_field("event_cost");
    save_event_field("event_deposit");
    save_event_field("event_deposittype");
    save_event_field("event_organiser");
    save_event_field("event_telephone");
    save_event_field("event_image");
    save_event_field("event_redirect");
    save_event_field("event_registration_message");
    save_event_field("event_show_cutoff_blurb");
    save_event_field("event_maxplaces");
    
    if ($eventdetails['publicationdate'] && $newdate) {
        remove_action('save_post', 'save_event_details');
        $updatestart = date_i18n('Y-m-d H:i:s',$newdate);
        wp_update_post(array('ID' => $event, 'post_date' => $updatestart));
        add_action('save_post', 'save_event_details');
    }
    
    $arr = array(
        'hide_event',
        'event_number',
        'event_register',
        'event_counter',
        'event_paypal',
        'event_redirect_id',
        'usecustomform',
        'usename',
        'usemail',
        'usetelephone',
        'useplaces',
        'usemessage',
        'useattend',
        'useblank1',
        'useblank2',
        'usedropdown',
        'useselector',
        'usenumber1',
        'useaddinfo',
        'addinfo',
        'usemorenames',
        'moreemails',
        'usecopy',
        'useterms',
        'usecaptcha',
        'event_requiredplaces',
        'event_getemails',
        'event_show_cutoff'
    );
    foreach ($arr as $item) {
        $old = get_event_field($item);
        $new = $_POST[$item];
        if ($new && $new != $old) update_post_meta($post->ID, $item, $new);
        elseif ('' == $new && $old) delete_post_meta($post->ID, $item, $old);
    }
    if ($_POST["event_repeat"]) {$_POST["event_repeat"] = '';$posts = $_POST; qem_duplicate_new_post($posts,$event,'publish');}
}

function save_event_field($event_field) {
    global $post;
    if(isset($_POST[$event_field])) update_post_meta($post->ID, $event_field, $_POST[$event_field]);
}

function action_add_meta_boxes() {
    
    add_meta_box('event_details',__('Event Details', 'quick-event-manager'),'event_details_meta','event', 'normal', 'high');
    add_meta_box('event_registration',__('Event Registration Form', 'quick-event-manager'),'event_details_reg','event');
    add_meta_box( 'registration_confirmation', 'Registration Confirmation Message', 'rcm_meta_box','event');
    global $_wp_post_type_features;
    if (isset($_wp_post_type_features['event']['editor']) && $_wp_post_type_features['event']['editor']) {
        unset($_wp_post_type_features['event']['editor']);
        add_meta_box('description_section', __('Event Description', 'quick-event-manager'),'inner_custom_box','event', 'normal', 'high');
    }
}

function inner_custom_box( $post ) {
    $settings = array('wpautop'=>false);
    wp_editor($post->post_content, 'post_content', $settings);
}

function rcm_meta_box( $post ) {
    $settings = array('wpautop'=>false);
    $field_value = get_post_meta( $post->ID, 'event_registration_message', false );
    if (!$field_value[0]) {
        $auto = qem_get_stored_autoresponder();
        $field_value[0] = $auto['message'];
    }
    wp_editor( $field_value[0], 'event_registration_message', $settings);
}

function qem_duplicate() {
    qem_duplicate_post(null);
}

function qem_duplicate_post($period) {
    global $wpdb;
    if (!(isset( $_GET['post']) || isset($_POST['post'])  || (isset($_REQUEST['action']) && 'qem_duplicate_post' == $_REQUEST['action']))) {
        wp_die('No post to duplicate has been supplied!');
    }
	$post_id = (isset($_GET['post']) ? $_GET['post'] : $_POST['post']);
	$post = get_post( $post_id );
    $new_post_id = qem_create_duplicate_post($period,$post_id,$post,'draft',null);
    wp_redirect( admin_url( 'post.php?post='.$new_post_id.'&action=edit' ) );
    exit;
}

add_action( 'admin_action_qem_duplicate', 'qem_duplicate' );
 
function duplicate_post_month( $actions, $post ) {
    if (current_user_can('edit_posts') && 'event' == get_post_type() ) {
        $actions['duplicate'] = '<a href="admin.php?action=qem_duplicate_month&amp;post=' . $post->ID . '" title="Duplicate this item" rel="permalink">Monthly</a>';
    }
    return $actions;
}

function duplicate_post_week( $actions, $post ) {
    if (current_user_can('edit_posts') && 'event' == get_post_type() ) {
        $actions['duplicate2'] = '<a href="admin.php?action=qem_duplicate_week&amp;post=' . $post->ID . '" title="Duplicate this item" rel="permalink">Weekly</a>';
    }
    return $actions;
}

function duplicate_post( $actions, $post ) {
    if (current_user_can('edit_posts') && 'event' == get_post_type() ) {
        $actions['duplicateevent'] = '<a href="admin.php?action=qem_duplicate&amp;post=' . $post->ID . '" title="Duplicate this event" rel="permalink">Duplicate</a>';
    }
    return $actions;
}

function qem_attendees( $actions, $post ) {
    if (current_user_can('edit_posts') && 'event' == get_post_type() ) {
        global $post;
        $title = get_the_title();
        $actions['attendees'] = '<a href="admin.php?page=quick-event-manager/quick-event-messages.php&event='.$post->ID.'&title='.$title.'">Registrations</a>';
    }
    return $actions;
}

add_filter( 'post_row_actions', 'duplicate_post', 10, 2 );
add_filter( 'post_row_actions', 'qem_attendees', 10, 2 );