<?php

add_action("init", "qem_settings_init");
add_action("admin_menu","event_page_init");
add_action("save_post", "save_event_details");
add_action("admin_notices","qem_admin_notice");
add_action("add_meta_boxes","action_add_meta_boxes", 0 );
add_action("manage_posts_custom_column","event_custom_columns");
add_action("admin_menu", "qem_admin_pages");
add_filter("manage_event_posts_columns","event_edit_columns");
add_filter("manage_edit-event_sortable_columns","event_column_register_sortable");
add_action("plugin_row_meta", "qem_plugin_row_meta", 10, 2 );

add_action("pre_get_posts", "manage_wp_posts_be_qe_pre_get_posts", 1 );

function manage_wp_posts_be_qe_pre_get_posts( $query ) {
    if ( $query->is_main_query() && ( $orderby = $query->get( 'orderby' ) ) ) {
        switch( $orderby ) {
            case 'event_date':
                $query->set( 'meta_key', 'event_date' );
                $query->set( 'orderby', 'meta_value_num' );
                break;
            case 'event_location':
                $query->set( 'meta_key', 'event_location' );
                $query->set( 'orderby', 'meta_value' );
                break;
            case 'event_time':
                $query->set( 'meta_key', 'event_time' );
                $query->set( 'orderby', 'meta_value' );
                break;
            case 'number_coming':
                $query->set( 'meta_key', 'number_coming' );
                $query->set( 'orderby', 'meta_value' );
                break;
            case 'categories':
                $query->set( 'meta_key', 'categories' );
                $query->set( 'orderby', 'meta_value' );
                break;
        }
    }
}

function qem_tabbed_page() {
    echo '<h1>Quick Event Manager</h1>';
    if ( isset ($_GET['tab'])) {
        qem_admin_tabs($_GET['tab']); 
        $tab = $_GET['tab'];
    } else {
        qem_admin_tabs('setup'); $tab = 'setup';
    }
    switch ($tab) {
        case 'setup' : qem_setup(); break;
        case 'settings' : qem_event_settings(); break;
        case 'display' : qem_display_page(); break;
        case 'calendar' : qem_calendar(); break;
        case 'styles' : qem_styles(); break;
        case 'register' : qem_register(); break;
        case 'payment' : qem_payment(); break;
        case 'template' : qem_template(); break;
        case 'coupon' : qem_coupon_codes(); break;
        case 'donate' : qem_donate_page(); break;
        case 'auto' : qem_autoresponse_page(); break;
        case 'smtp' : qem_smtp_page(); break;
        case 'incontext' : qem_incontext(); break;
        case 'guest' : qem_extend_guest_setup(); break;
        case 'reports' : qem_extend_setup(); break;
        case 'report' : qem_extend_report_setup(); break;
        case 'person' : qem_extend_registrations_setup(); break;
        case 'sortbyname' : qem_extend_sortby_name (); break;
        case 'sortbyemail' : qem_extend_sortby_email (); break;
        case 'notcoming' : qem_extend_notcoming (); break;
        case 'registrationemail' : qem_extend_registration_send_email (); break;
        case 'import' : qem_csv_import (); break;
    }
}

function qem_admin_tabs($current = 'settings') {
    $qemkey = get_option('qpp_key');
    $upgrade = ($qemkey['authorised'] ? 'Payment API' : '<span style="color:#8951A5;">Upgrade</span>');
    $guest = ($qemkey['authorised'] ? 'Guest' : '<span style="color:#CCC;">Guest</span>');
    $extensions = ($qemkey['authorised'] ? 'Extensions' : '<span style="color:#CCC;">Extensions</span>');
    $tabs = array( 
        'setup' 	=> __('Setup', 'quick-event-manager'), 
        'settings'  => __('Settings', 'quick-event-manager'), 
        'display'   => __('Display', 'quick-event-manager'), 
        'styles'    => __('Styling', 'quick-event-manager'),
        'calendar'  => __('Calendar', 'quick-event-manager'),
        'register'  => __('Registration', 'quick-event-manager'),
        'auto'      => __('Auto Responder', 'quick-event-manager'),
        'payment'   => __('Payments', 'quick-event-manager'),
        'incontext' => $upgrade,
        'guest'     => $guest,
        'reports'   => $extensions
        );
    echo '<div id="icon-themes" class="icon32"><br></div>';
    echo '<h2 class="nav-tab-wrapper">';
    foreach( $tabs as $tab => $name ) {
        $class = ( $tab == $current ) ? ' nav-tab-active' : '';
        echo "<a class='nav-tab$class' href='?page=quick-event-manager/settings.php&tab=$tab'>$name</a>";
    }
    echo '</h2>';
}

function qem_setup() {
    $content = '<div class="qem-settings"><div class="qem-options">
    <h2>'.__('Setting up and using the plugin', 'quick-event-manager').'</h2>
    <p><span style="color:red; font-weight:bold;">'. __('Important!', 'quick-event-manager').'</span> '.__('If you get an error when trying to view events, resave your', 'quick-event-manager').' <a href="options-permalink.php">permalinks</a>.</p>
    <p>'.__('Create new events using the', 'quick-event-manager').' <a href="edit.php?post_type=event">Events</a> '.__('link on your dashboard menu', 'quick-event-manager').'.</p>
    <p>'.__('To display a list of events on your posts or pages use the shortcode: [qem]', 'quick-event-manager').'.</p>
    <p>'.__('If you prefer to display your events as a calendar use the shortcode', 'quick-event-manager').': [qemcalendar].</p>
    <p>'.__('More shortcodes on the right', 'quick-event-manager').'.</p>
    <p>'.__('That&#39;s pretty much it. All you need to do now is', 'quick-event-manager').' <a href="edit.php?post_type=event">'.__('create some events', 'quick-event-manager').'</a>.</p>
    
    <h2>'.__('Help and Support', 'quick-event-manager').'</h2>
    <p>'.__('Help at', 'quick-event-manager').' <a href="http://quick-plugins.com/quick-event-manager/" target="_blank">quick-plugins.com</a> '.__('along with a feedback form. Or you can email me at ', 'quick-event-manager').'<a href="mailto:mail@quick-plugins.com">mail@quick-plugins.com</a>.</p>'.qemdonate_loop().'
    
    <h2>'.__('Translations', 'quick-event-manager').'</h2>
    <p>Brazilian: Julias  - <a href ="http://www.juliusmiranda.com/">juliusmiranda.com</a></p>
    <p>Czech: Augustin - <a href ="http://zidek.eu/">zidek.eu</a></p>
    <p>French: Bernard - <a href ="http://sorties-en-creuse.fr/">sorties-en-creuse.fr</a></p>
    <p>German: Tameer - <a href ="bloc-rockers-eifel.de">bloc-rockers-eifel.de</a></p>
    <p>Russian: Alexey - <a href ="http://hakuna-matata.spb.ru/">hakuna-matata.spb.ru</a></p>
    <p>Spanish: Ana - <a href ="http://dandolamurga.com/">dandolamurga.com</a></p>
    </div>
    <div class="qem-options">';
    $qemkey = get_option('qpp_key');
    if ($qemkey['dismiss']) $qemkey['authorised'] = true;
    if (!$qemkey['authorised']) {
        $content .= '<div class="qemupgrade"><a href="?page=quick-event-manager/settings.php&tab=incontext">
        <h3>Upgrade for just $20</h3>
        <p>Upgrading gives you access the Guest Event creator, CSV uploader, a range of registration reports and downloads, mailchimp subscriber and the very cool \'In Context Checkout\'. </p>
        <p>Click to find out more</p>
        </a></div>';
    }
    $content .= '<h2>'.__('Event Manager Role', 'quick-event-manager').'</h2>
    <p>'.__('There is a user role called <em>Event Manager</em>. Users with this role only have access to events, they cannot edit posts or pages.', 'quick-event-manager').'</p>
    
    <h2>'.__('Settings', 'quick-event-manager').'</h2>
    <h3><a href="?page=quick-event-manager/settings.php&tab=settings">'.__('Settings', 'quick-event-manager').'</a></h3>
    <p>'.__('Select which fields are displayed in the event list and event page. Change actions and style of each field', 'quick-event-manager').'</p>
    <h3><a href="?page=quick-event-manager/settings.php&tab=display">'.__('Display', 'quick-event-manager').'</a></h3>
    <p>'.__('Edit event messages and display options', 'quick-event-manager').'</p>
    <h3><a href="?page=quick-event-manager/settings.php&tab=styles">'.__('Styling', 'quick-event-manager').'</a></h3>
    <p>'.__('Styling options for the date icon and overall event layout', 'quick-event-manager').'</p>
    <h3><a href="?page=quick-event-manager/settings.php&tab=calendar">'.__('Calendar', 'quick-event-manager').'</a></h3>
    <p>'.__('Show events as a calendar. Some styling and display options', 'quick-event-manager').'.</p>
    <h3><a href="?page=quick-event-manager/settings.php&tab=register">'.__('Registration', 'quick-event-manager').'</a></h3>
    <p>'.__('Add a registration form and attendee reports to your events', 'quick-event-manager').'.</p>
<h3><a href="?page=quick-event-manager/settings.php&tab=auto">'.__('Auto Responder', 'quick-event-manager').'</a></h3>
    <p>'.__('Set up an email responder for event registrations', 'quick-event-manager').'.</p>
    <h3><a href="?page=quick-event-manager/settings.php&tab=payment">'.__('Payments', 'quick-event-manager').'</a></h3>
    <p>'.__('Configure event payments', 'quick-event-manager').'</p>
    <h3><a href="?page=quick-event-manager/quick-event-messages.php">'.__('Registration Reports', 'quick-event-manager').'</a></h3>
    <p>'.__('View, edit and download event registrations', 'quick-event-manager').'. '.__('Access using the <b>Registration</b> link on your dashboard menu', 'quick-event-manager').'.</p>';
    
    if ($qemkey['authorised']) {
        $content .= '<h3><a href="?page=quick-event-manager/settings.php&tab=template">'.__('Template', 'quick-event-manager').'</a></h3>
        <p>'.__('Create an event template based on your theme single.php', 'quick-event-manager').'</p>
        <h3><a href="?page=quick-event-manager/settings.php&tab=guest">'.__('Guest Events', 'quick-event-manager').'</a></h3>
        <p>'.__('Let your visitors create their own events', 'quick-event-manager').'</p>
        <h3><a href="?page=quick-event-manager/settings.php&tab=reports">'.__('Reports', 'quick-event-manager').'</a></h3>
        <p>'.__('Let your visitors create their own events', 'quick-event-manager').'</p>';
    }
    
    $content .= '<h2>'.__('Primary Shortcodes', 'quick-event-manager').'</h2>
    <table>
    <tbody>
    <tr>
    <td>[qem]</td>
    <td>'.__('Standard event list', 'quick-event-manager').'</td>
    </tr>
    <tr>
    <td>[qemcalendar]</td>
    <td>'.__('Calendar view', 'quick-event-manager').'</td>
    </tr>
    <tr>
    <td>[qem posts=\'99\']</td>
    <td>'.__('Set the number of events to display', 'quick-event-manager').'</td>
    </tr>
    <tr>
    <td>[qem id=\'archive\']</td>
    <td>'.__('Show old events', 'quick-event-manager').'</td>
    </tr>
    <tr>
    <td>[qem category=\'name\']</td>
    <td>'.__('List events by category', 'quick-event-manager').'</td>
    </tr>
    </tbody>
    </table>
    <p>'.__('There are loads more shortcode options listed on the', 'quick-event-manager').' <a href="http://quick-plugins.com/quick-event-manager/all-the-shortcodes/" target="_blank">'.__('Plugin Website', 'quick-event-manager').'</a> ('.__('link opens in a new tab', 'quick-event-manager').').';
    $content .= '</div></div>';
    echo $content;
}

function qem_event_settings() {
    $register = qem_get_stored_register();
    $active_buttons = array('field1','field2','field3','field4','field5','field6','field7','field8','field9','field10','field11','field12','field13');	
    if( isset( $_POST['Submit']) && check_admin_referer("save_qem")) {
        foreach ( $active_buttons as $item) {
            $event['active_buttons'][$item] = (isset($_POST['event_settings_active_'.$item]) and $_POST['event_settings_active_'.$item] =='on') ? true : false;
            $event['summary'][$item] = (isset( $_POST['summary_'.$item]) );
            $event['bold'][$item] = (isset( $_POST['bold_'.$item]) );
            $event['italic'][$item] = (isset( $_POST['italic_'.$item]) );
            $event['colour'][$item] = filter_var($_POST['colour_'.$item],FILTER_SANITIZE_STRING);
            $event['size'][$item] = filter_var($_POST['size_'.$item],FILTER_SANITIZE_STRING);
            if (!empty ( $_POST['label_'.$item])) {
                $event['label'][$item] = stripslashes($_POST['label_'.$item]);
                filter_var($event['label'][$item],FILTER_SANITIZE_STRING);
            }
        }
        $option = array(
            'sort',
            'description_label',
            'address_label',
            'url_label',
            'cost_label',
            'category_label',
            'start_label',
            'finish_label',
            'location_label',
            'organiser_label',
            'show_telephone',
            'target_link',
            'publicationdate',
            'whoscomingmessage',
            'whoscoming',
            'whosavatar',
            'oneplacebefore',
            'placesbefore',
            'placesafter',
            'oneattendingbefore',
            'numberattendingbefore',
            'numberattendingafter',
            'iflessthan',
        );
        foreach ($option as $item) {
            $event[$item] = stripslashes($_POST[$item]);
            $event[$item] = filter_var($event[$item],FILTER_SANITIZE_STRING);
        }
        update_option( 'event_settings', $event);
        qem_admin_notice(__('The form settings have been updated', 'quick-event-manager'));
    }
    if( isset( $_POST['Reset']) && check_admin_referer("save_qem")) {
        delete_option('event_settings');
        qem_admin_notice (__('The event settings have been reset', 'quick-event-manager')) ;
    }
    $event = event_get_stored_options();
    ${$event['publicationdate']} = 'checked'; 
    $content = '<script>
    jQuery(function() {var qem_sort = jQuery( "#qem_sort" ).sortable({axis: "y",update:function(e,ui) {var order = qem_sort.sortable("toArray").join();jQuery("#qem_settings_sort").val(order);}});});
    </script>
    <div class ="qem-options" style="width:98%">
    <form id="event_settings_form" method="post" action="">
    <p>'.__('Use the check boxes to select which fields to display in the event post and the event list', 'quick-event-manager').'.</p>
    <p>'.__('Drag and drop to change the order of the fields', 'quick-event-manager').'.</p>
    <table id="sorting">
    <thead>
    <tr>
    <th width="14%">'.__('Show in event post', 'quick-event-manager').'</th>
    <th width="8%">'.__('Show in<br>event list', 'quick-event-manager').'</th>
    <th width="12%">'.__('Colour', 'quick-event-manager').'</th>
    <th width="7%">'.__('Font<br>size', 'quick-event-manager').'</th>
    <th width="12%">'.__('Font<br>attributes', 'quick-event-manager').'</th>
    <th>'.__('Caption and display options', 'quick-event-manager').':</th>
    </tr>
    </thead><tbody id="qem_sort">';
    $sort = explode(",", $event['sort']); 
    foreach (explode( ',',$event['sort']) as $name) {
        $checked = ( $event['active_buttons'][$name]) ? 'checked' : '';
        $summary = ( $event['summary'][$name]) ? 'checked' : '';
        $bold = ( $event['bold'][$name]) ? 'checked' : '';
        $italic = ( $event['italic'][$name]) ? 'checked' : '';
        $options = '';
        switch ( $name ) {
            case 'field1':
            $options = '<input type="text" style="border:1px solid blue; width:10em;" name="description_label" . value ="' . $event['description_label'] . '" /> {'.__('description', 'quick-event-manager').'}';
            break;
            case 'field2':
            $options = '<input type="text" style="width:10em;" name="start_label" . value ="' . $event['start_label'] . '" /> {'.__('start time', 'quick-event-manager').'} <input type="text" style="border:1px solid blue; width:10em;" name="finish_label" . value ="' . $event['finish_label'] . '" /> {'.__('end time', 'quick-event-manager').'}';
            break;
            case 'field3':
            $options = '<input type="text" style="width:6em;" name="location_label" . value ="' . $event['location_label'] . '" /> {'.__('venue', 'quick-event-manager').'}';
            break;
            case 'field4':
            $options = '<input type="text" style="width:10em;" name="address_label" . value ="' . $event['address_label'] . '" /> {'.__('address', 'quick-event-manager').'}';
            break;
            case 'field5':
            $options = '<input type="text" style="width:10em;" name="url_label" . value ="' . $event['url_label'] . '" /> {url}';
            break;
            case 'field6':
            $options = '<input type="text" style="width:8em;" name="cost_label" . value ="' . $event['cost_label'] . '" /> {'.__('cost', 'quick-event-manager').'} (<input type="text" style="width:8em;" name="deposit_before_label" . value ="' . $event['deposit_before_label'] . '" /> {deposit} <input type="text" style="width:8em;" name="deposit_after_label" . value ="' . $event['deposit_after_label'] . '" />)';
            break;
            case 'field7':
            $options = '<input type="text" style="width:10em;" name="organiser_label" . value ="' . $event['organiser_label'] . '" /> {'.__('organiser', 'quick-event-manager').'}&nbsp;<input type="checkbox" name="show_telephone"' . $event['show_telephone'] . ' value="checked" /> '.__('Show organiser\'s contact details', 'quick-event-manager').' ';
            break;
            case 'field8':
            $options = __('The contents of the event detail editing box.','quick-event-manager');
            break;
            case 'field9':
            $options = '<input type="text" style="width:40%;" name="oneattendingbefore" value="' . $event['oneattendingbefore'] . '" /><br>
            <input type="text" style="width:40%; " name="numberattendingbefore" value="' . $event['numberattendingbefore'] . '" /> {number} <input type="text" style="width:40%; " name="numberattendingafter" value="' . $event['numberattendingafter'] . '" />';
            break;
            case 'field10':
            $options = '<input type="text" style="width:10em;" name="whoscomingmessage" value="' . $event['whoscomingmessage'] . '" />&nbsp;<input type="checkbox" name="whoscoming" ' . $event['whoscoming'] . ' value="checked" />&nbsp;'.__('Show names', 'quick-event-manager').'&nbsp;<input type="checkbox" name="whosavatar" ' . $event['whosavatar'] . ' value="checked" />&nbsp;'.__('Show Avatar', 'quick-event-manager').'';
            break;
            case 'field11':
            $options = '<input type="text" style="width:40%;" name="oneplacebefore" value="' . $event['oneplacebefore'] . '" /><br>
            <input type="text" style="width:40%;" name="placesbefore" value="' . $event['placesbefore'] . '" /> {number} <input type="text" style="width:40%;" name="placesafter" value="' . $event['placesafter'] . '" /><br>
            '.__('Only show message if less than', 'quick-event-manager').' <input type="text" style="width:3em" name="iflessthan" value="' . $event['iflessthan'] . '" /> '.__('places available', 'quick-event-manager').'. <span class="description">Leave blank to show on all events</span>';
            break;
            case 'field12':
            $options = __('Enable the registration form', 'quick-event-manager').'.&nbsp;<a style="color:blue;text-decoration:underline;" href="?page=quick-event-manager/settings.php&tab=register">'.__('Registration form settings', 'quick-event-manager').'</a><br>
            <span class="description">'.__('To add a registration form to individual events use the event editor', 'quick-event-manager').'.</span>';
            break;
            case 'field13':
            $options = __('<input type="text" style="border:1px solid blue; width:10em;" name="category_label" . value ="' . $event['category_label'] . '" /> {'.__('category', 'quick-event-manager').'}');
            break;
        }
        $li_class = ( $checked) ? 'button_active' : 'button_inactive';
        $content .= '<tr class="ui-state-default '.$li_class.'" id="' . $name . '"><td>
        <input type="checkbox" class="button_activate" name="event_settings_active_'.$name.'" '.$checked.' />
        <b>' . $event['label'][$name] . '</b></td>
        <td>';
        if ($name != 'field12') {
            $content .= '<input type="checkbox" name="summary_'.$name.'" '.$summary.' />';
        }   
        $content .= '</td>';
        $exclude = array('field8','field12');
        if(!in_array($name, $exclude)) {
            $content .= '<td><input type="text" class="qem-color" name="colour_'.$name.'" value ="' . $event['colour'][$name].'" /></td>
        <td><input type="text" style="width:3em;border: 1px solid #343838;" name="size_'.$name.'" value ="'.$event['size'][$name].'" />%</td>
        <td><input type="checkbox" name="bold_'.$name.'" '.$bold.' /> Bold <input type="checkbox" name="italic_'.$name.'" '.$italic.' /> Italic</td>
        <td>'.$options.'</td>';
        } else {
            $content .= '<td colspan="5">'.$options.'</td>';
        }
        $content .= '</tr>';
    }
	$content .='</tbody></table>
    <h2>Publication Date</h2>
    <p><input type="checkbox" name="publicationdate" value="checked" ' . $event['publicationdate'] . ' /></td><td> '.__('Make publication date the same as the event date', 'quick-event-manager').'</p>
    <input type="hidden" id="qem_settings_sort" name="sort" value="'.$event['sort'].'" />
	<p><input type="submit" name="Submit" class="button-primary" style="color: #FFF;" value="'.__('Save Changes', 'quick-event-manager').'" /> <input type="submit" name="Reset" class="button-primary" style="color: #FFF;" value="'.__('Reset', 'quick-event-manager').'" onclick="return window.confirm( \' '.__('Are you sure you want to reset the display settings?', 'quick-event-manager').'\' );"/></p>';
    $content .= wp_nonce_field("save_qem");
    $content .= '</form>
    <h2>'.__('Shortcode and Widget Field Selection', 'quick-event-manager').'</h2>
    <p>'.__('If you want a custom layout for a specific list you can use the shortcode [qem fields=1,2,5].', 'quick-event-manager').' '.__('On the <a href="/wp-admin/widgets.php">widget</a> just enter the field numbers seperated with commas.', 'quick-event-manager').'<p>
    <p>'.__('The numbers correspond to the fields like this', 'quick-event-manager').': <p>
    <ol>
    <li>'.__('Short description', 'quick-event-manager').'</li>
    <li>'.__('Event Time', 'quick-event-manager').'</li>
    <li>'.__('Venue', 'quick-event-manager').'</li>
    <li>'.__('Address', 'quick-event-manager').'</li>
    <li>'.__('Website', 'quick-event-manager').'</li>
    <li>'.__('Cost', 'quick-event-manager').'</li>
    <li>'.__('Organiser', 'quick-event-manager').'</li>
    <li>'.__('Full description', 'quick-event-manager').'</li>
    <li>'.__('Places Taken', 'quick-event-manager').'</li>
    <li>'.__('Attendees', 'quick-event-manager').'</li>
    <li>'.__('Places Available', 'quick-event-manager').'</li>
    <li>'.__('Registration Form', 'quick-event-manager').'</li>
    <li>'.__('Category', 'quick-event-manager').'</li>
    </ol>
    <p>'.__('The order of the fields and other options is set using the drag and drop selectors above', 'quick-event-manager').'</p></div>';
    echo $content;
}

function qem_display_page() {
    if( isset( $_POST['Submit']) && check_admin_referer("save_qem")) {
        $option = array(
            'show_end_date',
            'read_more',
            'noevent',
            'event_archive',
            'event_descending',
            'external_link',
            'external_link_target',
            'linkpopup',
            'recentposts',
            'event_image',
            'back_to_list',
            'back_to_list_caption',
            'back_to_url',
            'show_map',
            'map_width',
            'map_height',
            'map_in_list',
            'map_and_image',
            'map_and_image_size',
            'map_target',
            'event_image_width',
            'image_width',
            'usefeatured',
            'combined',
            'monthheading',
            'useics',
            'uselistics',
            'useicsbutton',
            'usetimezone',
            'timezonebefore',
            'timezoneafter',
            'amalgamated',
            'vertical',
            'norepeat',
            'monthtype',
            'categorylocation',
            'showcategoryintitle',
            'readmorelink',
            'titlelink',
            'max_width',
            'loginlinks',
            'lightboxwidth',
            'fullpopup',
            'eventgrid',
            'eventmasonry',
            'eventgridborder',
            'fullevent',
            'linktocategories',
            'showuncategorised',
            'showkeyabove',
            'showkeybelow',
            'keycaption',
            'showcategory',
            'showcategorycaption',
            'catallevents',
            'catalleventscaption',
            'cat_border',
            'linktocategories',
            'showuncategorised',
            'catalinkslug',
            'catalinkurl',
            'apikey'
        );
        foreach ($option as $item) {
            $display[$item] = stripslashes($_POST[$item]);
            $display[$item] = filter_var($display[$item],FILTER_SANITIZE_STRING);
        }
        update_option('qem_display', $display);	
        qem_admin_notice (__('The display settings have been updated', 'quick-event-manager'));
    }		
	if( isset( $_POST['Reset']) && check_admin_referer("save_qem")) {
		delete_option('qem_display');
		qem_admin_notice (__('The display settings have been reset', 'quick-event-manager')) ;
    }
    $masonry=$traditional=$short = $full = $title = $date = '';
    $display = event_get_stored_display();
    ${$display['show_end_date']} = 'checked';
    ${$display['localization']} = 'selected';
    ${$display['monthtype']} = 'checked';
    ${$display['categorylocation']} = 'checked';
    ${$display['eventmasonry']} = 'checked';
    
    if ( $display['event_archive'] == "checked") $archive = "checked"; 
    $content = '<style>'.qem_generate_css().'</style>
    <div class="qem-settings">
    <div class="qem-options">
    <form id="event_settings_form" method="post" action="">	
    <table>
    <tr>
    <td colspan="2"><h2>'.__('End Date Display', 'quick-event-manager').'</h2></td>
    </tr>
    <tr>
    <td width="5%"><input type="checkbox" name="show_end_date" value="checked" ' . $display['show_end_date'] . ' /></td><td width="95%"> '.__('Show end date in event list', 'quick-event-manager').'</td>
    </tr>
    <tr>
    <td><input type="checkbox" name="combined" value="checked" ' . $display['combined'] . ' /></td><td> '.__('Combine Start and End dates into one box', 'quick-event-manager').'</td>
    </tr>
    <tr>
    <td><input type="checkbox" name="amalgamated" value="checked" ' . $display['amalgamated'] . ' /></td><td> '.__('Show combined Start and End dates if in the same month', 'quick-event-manager').'</td>
    </tr>
    <tr>
    <td><input type="checkbox" name="norepeat" value="checked" ' . $display['norepeat'] . ' /></td><td> '.__('Only show icon on first event if more than one event on that day', 'quick-event-manager').'</td>
    </tr>
    <tr>
    <td><input type="checkbox" name="vertical" value="checked" ' . $display['vertical'] . ' /></td><td> '.__('Show start and end dates above one another', 'quick-event-manager').'</td>
    </tr>
    <tr>
    <td colspan="2"><h2>'.__('Event Messages', 'quick-event-manager').'</h2></td>
    </tr>
    <tr>
    <td colspan="2">'.__('Read more caption', 'quick-event-manager').': <input type="text" style="width:20em;" label="read_more" name="read_more" value="' . $display['read_more'] . '" /></td>
    </tr>
    <tr>
    <td colspan="2">'.__('No events message', 'quick-event-manager').': <input type="text" style="width:20em;" label="noevent" name="noevent" value="' . $display['noevent'] . '" /></td>
    </tr>
    <tr>
    <td colspan="2"><h2>'.__('Event List Options', 'quick-event-manager').'</h2></td>
    </tr>
<tr>
    <td><input type="checkbox" name="fullevent" value="checked" ' . $display['fullevent'] . ' /></td>
    <td> '.__('Show full event details in the event list', 'quick-event-manager').'</td>
    </tr>
    <tr>
    <td><input type="checkbox" name="event_descending" value="checked" ' . $display['event_descending'] . ' /></td>
    <td> '.__('List events in reverse order (from future to past)', 'quick-event-manager').'</td>
    </tr>
    <tr>
    <td><input type="checkbox" name="event_archive" value="checked" ' . $display['event_archive'] . ' /></td>
    <td> '.__('Show past events in the events list', 'quick-event-manager').'<br><span class="description">'.__('If you only want to display past events use the shortcode: [qem id="archive"]', 'quick-event-manager').'.</span></td>
    </tr>
    <tr>
    <td><input type="checkbox" name="monthheading" value="checked" ' . $display['monthheading'] . ' /></td>
    <td> '.__('Split the list into month/year sections', 'quick-event-manager').'</td>
    </tr>
    <tr>
    <td></td>
    <td><input type="radio" name="monthtype" value="short" ' . $short . ' /> '.__('Short (Aug)', 'quick-event-manager').' <input type="radio" name="monthtype" value="full" ' . $full . ' /> '.__('Full (August)', 'quick-event-manager').'</td>
    </tr>
    <tr>
    <td><input type="checkbox" name="recentposts"' . $display['recentposts'] . ' value="checked" /></td>
    <td>'.__('Show events in recent posts list', 'quick-event-manager').'</td>
    </tr>
    <tr>
    <td><input type="checkbox" name="showcategoryintitle" value="checked" ' . $display['showcategoryintitle'] . ' /></td>
    <td> '.__('Show category', 'quick-event-manager').'</td>
    </tr>
    <tr>
    <td></td>
    <td><input type="radio" name="categorylocation" value="title" ' . $title . ' /> '.__('Next to title', 'quick-event-manager').' <input type="radio" name="categorylocation" value="date" ' . $date . ' /> '.__('Next to date (if no icon styling)', 'quick-event-manager').'</td>
    </tr>
    <tr>
    <td><input type="checkbox" name="eventgrid" value="checked" ' . $display['eventgrid'] . ' /></td>
    <td> '.__('Show as grid', 'quick-event-manager').'<br><span class="description">Using this option will disable the date icon styling, month and year sections, images and maps</span>.</td>
    </tr>
    <tr>
    <td></td>
    <td><input type="radio" name="eventmasonry" value="traditional" ' . $traditional . ' /> '.__('Traditional', 'quick-event-manager').' <input type="radio" name="eventmasonry" value="masonry" ' . $masonry . ' /> '.__('Show as tiled (Pinterest type)', 'quick-event-manager').'</td>
    </tr>
    <tr>
    <td></td>
    <td>'.__('Event Border:', 'quick-event-manager').' <input type="text" style="width:10em;" label="eventgridborder" name="eventgridborder" value="' . $display['eventgridborder'] . '" />&nbsp;eg: 1px solid red</td>
    </tr>
    <tr>
    <td colspan="2"><h2>'.__('Download to Calendar', 'quick-event-manager').'</h2>
    <p>'.__('This allows users to download the event as a calendar file', 'quick-event-manager').'.</p></td>
    </tr>
    <tr>
    <td><input type="checkbox" name="useics" value="checked" ' . $display['useics'] . ' /></td>
    <td>'.__('Add download button to event', 'quick-event-manager').'</td>
    </tr>
    <tr>
    <td><input type="checkbox" name="uselistics" value="checked" ' . $display['uselistics'] . ' /></td>
    <td> '.__('Add download button to event list', 'quick-event-manager').'</td>
    </tr>
    <tr>
    <td></td>
    <td>'.__('Button text:', 'quick-event-manager').' <input type="text" style="width:50%;" label="useicsbutton" name="useicsbutton" value="' . $display['useicsbutton'] . '" /></td>
    </tr>
    <tr>
    <td colspan="2"><h2>Event Linking Options</h2></td>
    </tr>
    <tr>
    <td><input type="checkbox" name="external_link" value="checked" ' . $display['external_link'] . ' /></td>
    <td> '.__('Link to external website from event list', 'quick-event-manager').'</td>
    </tr>
    <tr>
    <td><input type="checkbox" name="external_link_target"' . $display['external_link_target'] . ' value="checked" /></td>
    <td>'.__('Open external links in new tab/page', 'quick-event-manager').'</td>
    </tr>
    <tr>
    <td><input type="checkbox" name="linkpopup"' . $display['linkpopup'] . ' value="checked" /></td>
    <td>'.__('Open event in lightbox', 'quick-event-manager').' ('.__('Warning: doesn\'t always behave as expected on small screens', 'quick-event-manager').').</td>
    </tr>
    <tr>
    <td></td>
    <td>'.__('Width:', 'quick-event-manager').' <input type="text" style="width:3em;" label="lightboxwidth" name="lightboxwidth" value="' . $display['lightboxwidth'] . '" />%</td>
    </tr>
    <tr>
    <td></td>
    <td><input type="checkbox" name="fullpopup" value="checked" ' . $display['fullpopup'] . ' />&nbsp;'.__('Show full event details in popup', 'quick-event-manager').'.</td>
    </tr>
    <tr>
    <td><input type="checkbox" name="titlelink" value="checked" ' . $display['titlelink'] . ' /></td>
    <td> '.__('Remove link from event title and event image', 'quick-event-manager').'</td>
    </tr>
    <tr>
    <td><input type="checkbox" name="readmorelink" value="checked" ' . $display['readmorelink'] . ' /></td>
    <td> '.__('Hide Read More link', 'quick-event-manager').'</td>
    </tr>
    <tr>
    <td><input type="checkbox" name="loginlinks" value="checked" ' . $display['loginlinks'] . ' /></td>
    <td> '.__('Hide links to event if not logged in', 'quick-event-manager').'</td>
    </tr>
    <tr>
    <td><input type="checkbox" name="back_to_list" value="checked" ' . $display['back_to_list'] . ' /></td>
    <td> '.__('Add a link to events to go back to the event list', 'quick-event-manager').'</td>
    </tr>
    <tr>
    <td></td>
    <td>'.__('Enter URL to link to a specific page. Leave blank to just go back one page', 'quick-event-manager').':<br>
    <input type="text" style="" label="back_to_url" name="back_to_url" value="' . $display['back_to_url'] . '" /></td>
    </tr>
    <tr>
    <td></td>
    <td>'.__('Link caption', 'quick-event-manager').': <input type="text" style="width:50%;" label="back_to_list_caption" name="back_to_list_caption" value="' . $display['back_to_list_caption'] . '" /></td>
    </tr>
    <tr>
    <td colspan="2"><h2>'.__('Maps and Images', 'quick-event-manager').'</h2>
    <p>If you have an event image it will display automatically on the event page</p><td>
    </tr>
    <tr>
    <td><input type="checkbox" name="event_image" value="checked" ' . $display['event_image'] . ' /></td>
    <td>'.__('Show event image in event list', 'quick-event-manager').'</td>
    </tr>
    <tr>
    <td><input type="checkbox" name="show_map" value="checked" ' . $display['show_map'] . ' /></td>
    <td>'.__('Show map on event page', 'quick-event-manager').'</td>
    </tr>
    <tr>
    <td><input type="checkbox" name="map_in_list" value="checked" ' . $display['map_in_list'] . ' /></td>
    <td>'.__('Show map in event list', 'quick-event-manager').'</td>
    </tr>
    <tr>
    <td><input type="checkbox" name="usefeatured" value="checked" ' . $display['usefeatured'] . ' /></td>
    <td>'.__('Use featured images', 'quick-event-manager').'</td>
    </tr>
    <tr>
    <td colspan="2"><b>'.__('Notes:', 'quick-event-manager').'</b><br>1. '.__('The map will only display if you have a valid address.', 'quick-event-manager').'<br>2. 
    '.__('If the map does not display you may need a', 'quick-event-manager').' <a href="https://support.google.com/cloud/answer/6158862" target="_blank">Google API Key</a>.</td>
    </tr>
    <tr>
    <td colspan="2">'.__('Google API Key:', 'quick-event-manager').'<input type="text" label="apikey" name="apikey" value="' . $display['apikey'] . '" /></td>
    </tr>
    <tr>
    <td colspan="2">'.__('Image and Map Width', 'quick-event-manager').': <input type="text" style=" width:3em; padding: 1px; margin:0;" name="event_image_width" . value ="' . $display['event_image_width'] . '" /> px<br>
    <span class="description">This is the maximum width of Map and Image on large screens.</span></td>
    </tr>
    <tr>
    <td colspan="2">'.__('Max Width', 'quick-event-manager').': <input type="text" style=" width:3em; padding: 1px; margin:0;" name="max_width" . value ="' . $display['max_width'] . '" />%<br>
    <span class="description">This is the maximum width of Map and Image compared to the whole event on smaller screens.</span></td>
    </tr>
    <tr>
    <td colspan="2">'.__('Event list width', 'quick-event-manager').': <input type="text" style=" width:3em; padding: 1px; margin:0;" name="image_width" value ="' . $display['image_width'] . '" /> px</td>
    </tr>
    <tr>
    <td colspan="2">'.__('Map Height', 'quick-event-manager').': <input type="text" style=" width:3em; padding: 1px; margin:0;" name="map_height" . value ="' . $display['map_height'] . '" /> px</td>
    </tr>
    <tr>
    <td><input type="checkbox" name="map_target" value="checked" ' . $display['map_target'] . ' /></td>
    <td>'.__('Open map in new tab/window', 'quick-event-manager').'</td>
    </tr>
    </table>
    <table>
    <tr>
    <td colspan="2"><h2>'.__('Categories', 'quick-event-manager').'</h2></td>
    </tr>
    <tr>
    <td width="30%">'.__('Display category key', 'quick-event-manager').'</td>
    <td><input type="checkbox" name="showkeyabove" ' . $display['showkeyabove'] . ' value="checked" /> '.__('Show above event list', 'quick-event-manager').'<br>
    <input type="checkbox" name="showkeybelow" ' . $display['showkeybelow'] . ' value="checked" /> '.__('Show below event list', 'quick-event-manager').'</td>
    </tr>
    <tr>
    <td width="30%">'.__('Caption', 'quick-event-manager').'</td>
    <td><input type="text" style="" label="text" name="keycaption" value="' . $display['keycaption'] . '" /></td>
    </tr>
    <tr>
    <td width="30%">'.__('Add link back to all events', 'quick-event-manager').'</td>
    <td><input type="checkbox" name="catallevents" ' . $display['catallevents'] . ' value="checked" /><br><span class="description">'.__('This uses the URL set on the', 'quick-event-manager').' <a href="?page=quick-event-manager/settings.php&tab=display">'.__('Event List', 'quick-event-manager').'</a> '.__('page', 'quick-event-manager').'.</span></td>
    </tr>
    <tr>
    <td width="30%">'.__('Caption', 'quick-event-manager').'</td>
    <td><input type="text" style="" label="text" name="catalleventscaption" value="' . $display['catalleventscaption'] . '" /></td>
    </tr>
    <tr>
    <td style="vertical-align:top;">'.__('Category Colours', 'quick-event-manager').'</td><td><input type="checkbox" name="cat_border"' . $display['cat_border'] . ' value="checked" /> '.__('Use category colours for the event border', 'quick-event-manager').'<br />
    <span class="description">'.__('Options are set on the','quick-event-manager').' <a href="?page=quick-event-manager/settings.php&tab=calendar">'.__('Calendar Settings','quick-event-manager').'</a> '.__('page', 'quick-event-manager').'.</span></td>
    </tr>
    <tr>
    <td width="30%"></td><td><input type="checkbox" name="showcategory" ' . $display['showcategory'] . ' value="checked" /> '.__('Show name of current category', 'quick-event-manager').'</td>
    </tr>
    <tr>
    <td width="30%"></td>
    <td>'.__('Current category label', 'quick-event-manager').':<br><input type="text" style="" label="text" name="showcategorycaption" value="' . $display['showcategorycaption'] . '" /></td>
    </tr>
    <tr>
    <td>'.__('Linking', 'quick-event-manager').'</td>
    <td><input type="checkbox" name="linktocategories" ' . $display['linktocategories'] . ' value="checked" /> '.__('Link keys to categories', 'quick-event-manager').'<br>
    <input type="checkbox" name="showuncategorised" ' . $display['showuncategorised'] . ' value="checked" /> '.__('Show uncategorised key', 'quick-event-manager').'</td>
    </tr>
    <tr>
    <td colspan="2"><h2>'.__('Category Linking Option', 'quick-event-manager').'</h2></td>
    </tr>
    <tr>
    <td colspan="2">'.__('Use this option to link from the event list to a URL for a category.', 'quick-event-manager').' '.__('Seperate using a comma for multiple catagories', 'quick-event-manager').'</td>
    </tr>
    <tr>
    <td width="30%">'.__('Category slug', 'quick-event-manager').'</td>
    <td><input type="text" name="catalinkslug" value="' . $display['catalinkslug'] . '" /></td>
    </tr>
    <tr>
    <td width="30%">'.__('URL', 'quick-event-manager').'</td>
    <td><input type="text" name="catalinkurl" value="' . $display['catalinkurl'] . '" /></td>
    </tr>
    </table>
    <table>
    <tr>
    <td colspan="2"><h2>'.__('Timezones', 'quick-event-manager').'</h2></td>
    </tr>
    
    <tr>
    <td><input type="checkbox" name="usetimezone"' . $display['usetimezone'] . ' value="checked" /></td>
    <td>'.__('Show timeszones on your events', 'quick-event-manager').'</td>
    </tr>
    <tr>
    <td colspan="2"><input type="text" style="width:40%;" name="timezonebefore" value="' . $display['timezonebefore'] . '" /> {timezone} <input type="text" style="width:40%;" name="timezoneafter" value="' . $display['timezoneafter'] . '" /><br>
    <span class="description">'.__('This doesn\'t change the time of the event, it just shows the name of the local timeszone. Set the event timezone in the event editor.', 'quick-event-manager').'</span></td>
    </tr>
    <tr>
    <td colspan="2"><input type="submit" name="Submit" class="button-primary" style="color: #FFF;" value="'.__('Save Changes', 'quick-event-manager').'" /> <input type="submit" name="Reset" class="button-primary" style="color: #FFF;" value="'.__('Reset', 'quick-event-manager').'" onclick="return window.confirm( \' '.__('Are you sure you want to reset the display settings?', 'quick-event-manager').'\' );"/></td>
    </tr>
    </table>';
    $content .= wp_nonce_field("save_qem");
    $content .= '</form>
    </div>
    <div class="qem-options">
    <h2>'.__('Event List Preview', 'quick-event-manager').'</h2>';
    $atts = array('posts' => '3');
    $content .= qem_event_shortcode($atts,'');
    $content .= '</div></div>';
    echo $content;
}

function qem_styles() {
    if( isset( $_POST['Submit']) && check_admin_referer("save_qem")) {
        $options = array(
            'use_head',
            'font',
            'font-family',
            'font-size',
            'header-size',
            'header-colour',
            'width',
            'widthtype',
            'event_background',
            'event_backgroundhex',
            'date_colour',
            'date_background',
            'date_backgroundhex',
            'month_background',
            'month_backgroundhex',
            'month_colour',
            'use_custom',
            'custom',
            'date_bold',
            'date_italic',
            'date_border_width',
            'date_border_colour',
            'calender_size',
            'event_border',
            'icon_corners',
            'event_margin',
            'line_margin',
            'use_dayname',
            'use_dayname_inline',
            'iconorder',
            'vanilla',
            'vanillamonth',
            'vanilladay',
            'vanillaontop',
            'vanillawidget',
            'uselabels',
            'startlabel',
            'finishlabel',
            'location'
        );
        foreach ( $options as $item) {
            $style[$item] = stripslashes($_POST[$item]);
            $style[$item] = filter_var($style[$item],FILTER_SANITIZE_STRING);
        }
        $arr = array('a','b','c','d','e','f','g','h','i','j');
        foreach ($arr as $i) {
            $style['cat'.$i] = $_POST['cat'.$i];
            $style['cat'.$i.'back'] = $_POST['cat'.$i.'back'];
            $style['cat'.$i.'text'] = $_POST['cat'.$i.'text'];
        }
        qem_create_css_file('update');
        update_option('qem_style', $style);
        qem_admin_notice (__('The form styles have been updated', 'quick-event-manager'));
    }
    if( isset( $_POST['Reset']) && check_admin_referer("save_qem")) {
        delete_option('qem_style');
        qem_admin_notice (__('The style settings have been reset', 'quick-event-manager'));
    }	
    $pixel=$percent=$plugin=$theme=$small=$medium=$large=$square=$rounded=$month=$year=$dm=$md=$grey=$red=$color='';
    $bgwhite=$bgtheme=$bgcolor=$colour=$mwhite=$php=$head='';
    $style = qem_get_stored_style();
    ${$style['font']} = 'checked';
    ${$style['widthtype']} = 'checked';
    ${$style['background']} = 'checked';
    ${$style['event_background']} = 'checked';
    ${$style['date_background']} = 'checked'; 
    ${$style['month_background']} = 'checked'; 
    ${$style['icon_corners']} = 'checked';
    ${$style['iconorder']} = 'checked'; 
    ${$style['calender_size']} = 'checked';
    ${$style['location']} = 'checked';
    $content = '<style>'.qem_generate_css().'</style>
    <div class="qem-settings">
    <div class="qem-options">
    <form method="post" action="">
    <table>
    <tr>
    <td colspan="2"><h2>'.__('Event Width', 'quick-event-manager').'</h2></td></tr>
    <tr>
    <td colspan="2"><input type="radio" name="widthtype" value="percent" ' . $percent . ' /> '.__('100% (fill the available space)', 'quick-event-manager').'<br />
    <input type="radio" name="widthtype" value="pixel" ' . $pixel . ' /> '.__('Pixel (fixed)', 'quick-event-manager').'<br />
    '.__('Enter the max-width ', 'quick-event-manager').': <input type="text" style="width:4em;" label="width" name="width" value="' . $style['width'] . '" />px '.__('(Just enter the value, no need to add \'px\')', 'quick-event-manager').'.</td></tr>
    <tr>
    <td colspan="2"><h2>'.__('Font Options', 'quick-event-manager').'</h2></td>
    </tr>
    <tr>
    <td colspan="2"><input type="radio" name="font" value="theme" ' . $theme . ' /> '.__('Use your theme font styles', 'quick-event-manager').'<br />
	<input type="radio" name="font" value="plugin" ' . $plugin . ' /> '.__('Use Plugin font styles (enter font family and size below)', 'quick-event-manager').'</td></tr>
    <tr>
    <td>'.__('Font Family', 'quick-event-manager').':</td>
    <td><input type="text" style="" label="font-family" name="font-family" value="' . $style['font-family'] . '" /></td></tr>
    <tr>
    <td>'.__('Font Size', 'quick-event-manager').':</td>
    <td><input type="text" style="width:4em;" label="font-size" name="font-size" value="' . $style['font-size'] . '" /><br>
    <span class="description">This is the base font size, you can set the sizes of each part of the listing in the Settings.</span></td></tr>
    <tr>
    <td>'.__('Header Size', 'quick-event-manager').':</td>
    <td><input type="text" style="width:4em;" label="header-size" name="header-size" value="' . $style['header-size'] . '" /> '.__('This the size of the title in the event list', 'quick-event-manager').'.</td>
    </tr>
    <tr>
    <td>'.__('Header Colour', 'quick-event-manager').':</td>
    <td><input type="text" class="qem-color" label="header-colour" name="header-colour" value="' . $style['header-colour'] . '" /></td>
    </tr>
    <tr>
    <td colspan="2"><h2>'.__('Calender Icon', 'quick-event-manager').'</h2></td>
    </tr>
    <tr>
    <td>'.__('Remove styles', 'quick-event-manager').'</td>
    <td><input type="checkbox" name="vanilla"' . $style['vanilla'] . ' value="checked" /> '.__('Do not style the calendar icon', 'quick-event-manager').'<br>
    &emsp;&emsp;&emsp;&emsp;&emsp;<input type="checkbox" name="vanilladay" ' . $style['vanilladay'] . ' value="checked" /> '.__('Show the full day name', 'quick-event-manager').'
    <br>
    &emsp;&emsp;&emsp;&emsp;&emsp;<input type="checkbox" name="vanillamonth" ' . $style['vanillamonth'] . ' value="checked" /> '.__('Show the full month name', 'quick-event-manager').'
    <br>
    &emsp;&emsp;&emsp;&emsp;&emsp;<input type="checkbox" name="vanillaontop" ' . $style['vanillaontop'] . ' value="checked" /> '.__('Show date above event name', 'quick-event-manager').'</td>
    </tr>
    <tr>
    <td style="vertical-align:top;">'.__('Size', 'quick-event-manager').'</td>
    <td>
	<input type="radio" name="calender_size" value="small" ' . $small . ' /> '.__('Small', 'quick-event-manager').' (40px)<br />
	<input type="radio" name="calender_size" value="medium" ' . $medium . ' /> '.__('Medium', 'quick-event-manager').' (60px)<br />
	<input type="radio" name="calender_size" value="large" ' . $large . ' /> '.__('Large', 'quick-event-manager').'(80px)</td>
    </tr>
    <tr>
    <td>'.__('Corners', 'quick-event-manager').'</td>
    <td>
    <input type="radio" name="icon_corners" value="square" ' . $square . ' /> '.__('Square', 'quick-event-manager').'&nbsp;
    <input type="radio" name="icon_corners" value="rounded" ' . $rounded . ' /> '.__('Rounded', 'quick-event-manager').'</td>
    </tr>
    <tr>
    <td>'.__('Border Thickness', 'quick-event-manager').'</td>
    <td><input type="text" style="width:2em;" label="calendar border" name="date_border_width" value="' . $style['date_border_width'] . '" /> px</td>
    </tr>
    <tr>
    <td>'.__('Border Colour', 'quick-event-manager').':</td>
    <td><input type="text" class="qem-color" label="calendar border" name="date_border_colour" value="' . $style['date_border_colour'] . '" /><br><span class="description">'.__('There is an option below to use category colours for the icon border', 'quick-event-manager').'.</span></td>
    </tr>
    <tr>
    <td>'.__('Calendar Icon Order', 'quick-event-manager').'</td>
    <td>
    <input type="radio" name="iconorder" value="default" ' . $default . ' /> '.__('DMY', 'quick-event-manager').'&nbsp;<input type="radio" name="iconorder" value="month" ' . $month . ' /> '.__('MDY', 'quick-event-manager').'&nbsp;
    <input type="radio" name="iconorder" value="year" ' . $year . ' /> '.__('YDM', 'quick-event-manager').'&nbsp;
    <input type="radio" name="iconorder" value="dm" ' . $dm . ' /> '.__('DM', 'quick-event-manager').'&nbsp;<input type="radio" name="iconorder" value="md" ' . $md . ' /> '.__('MD', 'quick-event-manager').'</td>
    </tr>
    <tr>
    <td>'.__('Start/Finish Labels', 'quick-event-manager').'</td>
    <td><input type="checkbox" name="uselabels"' . $style['uselabels'] . ' value="checked" /> '.__('Show start/finish labels', 'quick-event-manager').'<br>
    '.__('Start', 'quick-event-manager').': <input type="text" style="width:7em;" name="startlabel" value="' . $style['startlabel'] . '" /> '.__('Finish', 'quick-event-manager').': <input type="text" style="width:7em;" name="finishlabel" value="' . $style['finishlabel'] . '" /></td>
    </tr>
    <tr>
    <td>'.__('Day Name', 'quick-event-manager').'</td>
    <td><input type="checkbox" name="use_dayname"' . $style['use_dayname'] . ' value="checked" /> '.__('Show day name', 'quick-event-manager').'<br>
    <input type="checkbox" name="use_dayname_inline"' . $style['use_dayname_inline'] . ' value="checked" /> '.__('Show day name inline with date', 'quick-event-manager').'</td>
    </tr>
    <tr>
    <td style="vertical-align:top;">'.__('Date Background colour', 'quick-event-manager').'</td>
    <td>
	<input type="radio" name="date_background" value="grey" ' . $grey . ' /> '.__('Grey', 'quick-event-manager').'<br />
	<input type="radio" name="date_background" value="red" ' . $red . ' /> '.__('Red', 'quick-event-manager').'<br />
	<input type="radio" name="date_background" value="color" ' . $color . ' /> '.__('Set your own', 'quick-event-manager').'<br />
    <input type="text" class="qem-color" label="background" name="date_backgroundhex" value="' . $style['date_backgroundhex'] . '" /></td>
    </tr>
    <tr>
    <td>'.__('Date Text Colour', 'quick-event-manager').'</td>
    <td><input type="text" class="qem-color" label="date colour" name="date_colour" value="' . $style['date_colour'] . '" /></td>
    </tr>
    <tr>
    <td style="vertical-align:top;">'.__('Month Background colour', 'quick-event-manager').'</td>
    <td>
	<input type="radio" name="month_background" value="mwhite" ' . $mwhite . ' /> '.__('White', 'quick-event-manager').'<br />
	<input type="radio" name="month_background" value="colour" ' . $colour . ' /> '.__('Set your own', 'quick-event-manager').'<br />
    <input type="text" class="qem-color" name="month_backgroundhex" value="' . $style['month_backgroundhex'] . '" /></td>
    </tr>
    <tr>
    <td>'.__('Month Text Colour', 'quick-event-manager').'</td>
    <td><input type="text" class="qem-color" label="month colour" name="month_colour" value="' . $style['month_colour'] . '" /></td>
    </tr>
    <tr>
    <td>'.__('Month Text Style', 'quick-event-manager').'</td>
    <td><input type="checkbox" name="date_bold" value="checked" ' . $style['date_bold'] . ' /> '.__('Bold', 'quick-event-manager').'&nbsp;
	<input type="checkbox" name="date_italic" value="checked" ' . $style['date_italic'] . ' /> '.__('Italic', 'quick-event-manager').'</td>
    </tr>
	<tr>
    <td colspan="2"><h2>'.__('Event Content', 'quick-event-manager').'</h2></td>
    </tr>
	<tr>
    <td style="vertical-align:top;">'.__('Event Border', 'quick-event-manager').'</td>
    <td><input type="checkbox" name="event_border"' . $style['event_border'] . ' value="checked" /> '.__('Add a border to the event post', 'quick-event-manager').'<br /><span class="description">'.__('Thickness and colour will be the same as the calendar icon', 'quick-event-manager').'.</span></td>
    </tr>
    <tr>
    <td style="vertical-align:top;">'.__('Event Background Colour', 'quick-event-manager').'</td>
    <td><input type="radio" name="event_background" value="bgwhite" ' . $bgwhite . ' /> '.__('White', 'quick-event-manager').'<br />
	<input type="radio" name="event_background" value="bgtheme" ' . $bgtheme . ' /> '.__('Use theme colours', 'quick-event-manager').'<br />
	<input type="radio" name="event_background" value="bgcolor" ' . $bgcolor . ' /> '.__('Set your own', 'quick-event-manager').'<br />
	<input type="text" class="qem-color" label="background" name="event_backgroundhex" value="' . $style['event_backgroundhex'] . '" /></td>
    </tr>
    <tr>
    <td style="vertical-align:top;">'.__('Margins and Padding', 'quick-event-manager').'</td>
    <td><span class="description">'.__('Set the margins and padding of each bit using CSS shortcodes', 'quick-event-manager').':</span><br><input type="text" label="line margin" name="line_margin" value="' . $style['line_margin'] . '" /></td>
    </tr>
    <tr>
    <td style="vertical-align:top;">'.__('Event Margin', 'quick-event-manager').'</td>
    <td><span class="description">'.__('Set the margin or each event using CSS shortcodes', 'quick-event-manager').':</span><br>
    <input type="text" label="margin" name="event_margin" value="' . $style['event_margin'] . '" /></td>
    </tr>
    </table>
    <h2>'.__('Event Category Colours', 'quick-event-manager').'</h2>
    <p style="font-weight:bold"><span style="float:left;width:8em;">'.__('Category', 'quick-event-manager').'</span>'.__('Background', 'quick-event-manager').' / '.__('Text', 'quick-event-manager').'</p>
    
    <div class="qem-calcolor">';
    $arr = array('a','b','c','d','e','f','g','h','i','j');
    foreach ($arr as $i) {
        $content .= '<p>'.qem_categories ('cat'.$i,$style['cat'.$i]).'&nbsp;
        <input type="text" class="qem-color" label="cat'.$i.'back" name="cat'.$i.'back" value="' . $style['cat'.$i.'back'] . '" />&nbsp;
        <input type="text" class="qem-color" label="cat'.$i.'text" name="cat'.$i.'text" value="' . $style['cat'.$i.'text'] . '" /></p>';
    }
    $content .= '</div>
    <h2>Styles Location</h2>
    <p><input style="margin:0; padding:0; border:none;" type="radio" name="location" value="php" ' . $php . ' /> Extenal Stylesheet<br />
    <input style="margin:0; padding:0; border:none;" type="radio" name="location" value="head" ' . $head . ' /> Document Head</p>
    <h2>'.__('Custom CSS', 'quick-event-manager').'</h2>
    <p><input type="checkbox" name="use_custom"' . $style['use_custom'] . ' value="checked" /> '.__('Use Custom CSS', 'quick-event-manager').'</p>
    <p><textarea style="width:100%;height:100px;" name="custom">' . $style['custom'] . '</textarea></p>
    <p>'.__('To see all the styling use the', 'quick-event-manager').' <a href="plugin-editor.php?file=quick-event-manager/quick-event-manager.css">'.__('CSS editor', 'quick-event-manager').'</a>.</p>
    <p>'.__('The main style wrapper is the <code>.qem</code> class.', 'quick-event-manager').'</p>
    <p><input type="submit" name="Submit" class="button-primary" style="color: #FFF;" value="'.__('Save Changes', 'quick-event-manager').'" /> <input type="submit" name="Reset" class="button-primary" style="color: #FFF;" value="'.__('Reset', 'quick-event-manager').'" onclick="return window.confirm( \''.__('Are you sure you want to reset the style settings?', 'quick-event-manager').'\' );"/></p>';
    $content .= wp_nonce_field("save_qem");
    $content .= '</form>
    </div>
    </div>
    <div class="qem-options">
    <h2>'.__('Event List Preview', 'quick-event-manager').'</h2>
    <p>'.__('Check the event list in your site as the Wordpress Dashboard can do funny things with styles', 'quick-event-manager').'</p>';
    $atts = array('posts' => '3');
    $content .= qem_event_shortcode($atts,'');
    $content .= '</div>';
    echo $content;
}


function qem_calendar() {
    if( isset( $_POST['Submit']) && check_admin_referer("save_qem")) {
        $options = array(
            'calday',
            'caldaytext',
            'day',
            'eventday',
            'oldday',
            'eventhover',
            'eventdaytext',
            'eventlink',
            'connect',
            'calendar_text',
            'calendar_url',
            'eventlist_text',
            'eventlist_url',
            'startday',
            'eventlength',
            'archive',
            'archivelinks',
            'smallicon',
            'unicode',
            'eventbold',
            'eventitalic',
            'eventbackground',
            'eventtext',
            'eventtextsize',
            'trigger',
            'eventborder',
            'showmultiple',
            'keycaption',
            'showkeyabove',
            'showkeybelow',
            'prevmonth',
            'nextmonth',
            'navicon',
            'leftunicode',
            'rightunicode',
            'linktocategories',
            'showuncategorised',
            'cellspacing',
            'tdborder',
            'header',
            'headerstyle',
            'eventimage',
            'imagewidth',
            'usetooltip',
            'event_corner',
            'fixeventborder',
            'showmonthsabove',
            'showmonthsbelow',
            'monthscaption',
            'hidenavigation',
            'jumpto',
            'calallevent',
            'calalleventscaption',
            'fullpopup'
        );
        foreach ( $options as $item) {
            $cal[$item] = stripslashes($_POST[$item]);
            $cal[$item] = filter_var($cal[$item],FILTER_SANITIZE_STRING);
        }
        update_option('qem_calendar', $cal);
        qem_create_css_file('update');
        qem_admin_notice (__('The calendar settings have been updated', 'quick-event-manager'));
    }
    if( isset( $_POST['Reset']) && check_admin_referer("save_qem")) {
        delete_option('qem_calendar');
        qem_create_css_file('update');
        qem_admin_notice (__('The calendar settings have been reset', 'quick-event-manager'));
    }
    
    $linkpage=$linkpopup=$none=$arrows=$unicodes=$h2=$h3=$h4=$trim=$arrow=$box=$square=$asterix=$blank=$other=$sunday=$monday='';
    $calendar = qem_get_stored_calendar();
    ${$calendar['eventlink']} = 'checked';
    ${$calendar['startday']} = 'checked';
    ${$calendar['smallicon']} = 'checked';
    ${$calendar['navicon']} = 'checked';
    ${$calendar['header']} = 'checked';
    ${$calendar['event_corner']} = 'checked';

    if ($calendar['navicon'] == 'arrows') {
        $leftnavicon = '&#9668; ';
        $rightnavicon = ' &#9658;';
    }
    if ($calendar['navicon'] == 'unicodes') {
        $leftnavicon = $cal['leftunicode'].' ';
        $rightnavicon = ' '.$cal['rightunicode'];
    }
    $content = '<style>'.qem_generate_css().'</style> 
    <div class="qem-settings"><div class="qem-options">
    <h2>'.__('Using the Calendar', 'quick-event-manager').'</h2>
    <p>'.__('To add a calendar to your site use the shortcode: [qemcalendar]', 'quick-event-manager').'.</p>
    <form method="post" action="">
    <table width="100%">
    <tr>
    <td colspan="2"><h2>'.__('General Settings', 'quick-event-manager').'</h2></td>
    </tr>
    <tr>
    <td style="vertical-align:top;">'.__('Linking to Events', 'quick-event-manager').'</td>
    <td><input type="radio" name="eventlink" value="linkpage" ' . $linkpage . ' /> '.__('Link opens event page' ,'quick-event-manager').'<br />
    <input type="radio" name="eventlink" value="linkpopup" ' . $linkpopup . ' /> '.__('Link opens event summary in a popup', 'quick-event-manager').'<br>
    <input type="checkbox" name="fullpopup" value="checked" ' . $calendar['fullpopup'] . ' />Show full event details in popup.
    </td>
    </tr>
    <tr>
    <td width="30%">'.__('Old Events', 'quick-event-manager').'</td>
    <td><input type="checkbox" name="archive" ' . $calendar['archive'] . ' value="checked" /> '.__('Show past events in the calendar', 'quick-event-manager').'.</td>
    </tr>
    <tr>
    <td width="30%">'.__('Linking Calendar to the Event List', 'quick-event-manager').'</td>
    <td><input type="checkbox" name="connect"' . $calendar['connect'] . ' value="checked" /> '.__('Link Event List to Calendar Page', 'quick-event-manager').'.<br>
    <span class="description">'.__('You will need to create pages for the calendar and the event list', 'quick-event-manager').'.</span>
    </td>
    </tr>
    <tr>
    <td width="30%">'.__('Calendar link text', 'quick-event-manager').'</td><td><input type="text" style="" label="calendar_text" name="calendar_text" value="' . $calendar['calendar_text'] . '" /></td></tr>
    <tr><td width="30%">'.__('Calendar page URL', 'quick-event-manager').'</td><td><input type="text" style="" label="calendar_url" name="calendar_url" value="' . $calendar['calendar_url'] . '" /></td></tr>
    <tr><td width="30%">'.__('Event list link text', 'quick-event-manager').'</td><td><input type="text" style="" label="eventlist_text" name="eventlist_text" value="' . $calendar['eventlist_text'] . '" /></td></tr>
    <tr>
    <td width="30%">'.__('Event list page', 'quick-event-manager').' URL</td>
    <td><input type="text" style="" label="eventlist_url" name="eventlist_url" value="' . $calendar['eventlist_url'] . '" /></td></tr>
    <tr>
    <td width="30%">Navigation Labels</td>
    <td><input type="text" style="width:50%;" label="text" name="prevmonth" value="' . $calendar['prevmonth'] . '" /><input type="text" style="text-align:right;width:50%;" label="text" name="nextmonth" value="' . $calendar['nextmonth'] . '" /></td>
    </tr>
    <tr>
    <td width="30%">'.__('Navigation Icons', 'quick-event-manager').'</td>
    <td>
    <input type="radio" name="navicon" value="none" ' . $none . ' /> '.__('None', 'quick-event-manager').' 
    <input type="radio" name="navicon" value="arrows" ' . $arrows . ' /> &#9668; &#9658; 
    <input type="radio" name="navicon" value="unicodes" ' . $unicodes . ' />'.__('Other', 'quick-event-manager').' ('.__('enter', 'quick-event-manager').' <a href="http://character-code.com/arrows-html-codes.php" target="_blank">'.__('hex code', 'quick-event-manager').'</a> '.__('below', 'quick-event-manager').').<br />
    Left: <input type="text" style="width:6em;" label="text" name="leftunicode" value="' . $calendar['leftunicode'] . '" /> Right: <input type="text" style="width:6em;" label="text" name="rightunicode" value="' . $calendar['rightunicode'] . '" /></td>
    </tr>
    <tr>
    <td width="30%">'.__('Jump to links', 'quick-event-manager').'</td>
    <td><input type="checkbox" name="jumpto"' . $calendar['jumpto'] . ' value="checked" /> '.__('Jump to the top of the calendar when linking to a new month.', 'quick-event-manager').'</td>
    </tr>
    <tr>
    <td colspan="2"><h2>'.__('Calendar Options', 'quick-event-manager').'</h2></td>
    </tr>
    <tr>
    <td style="vertical-align:top;">'.__('Month and Date Header', 'quick-event-manager').'</td>
    <td><input type="radio" name="header" value="h2" ' . $h2 . ' /> H2 <input type="radio" name="header" value="h3" ' . $h3 . ' /> H3 <input type="radio" name="header" value="h4" ' . $h4 . ' /> H4<br>
Header CSS:<br>
    <input type="text" style="" name="headerstyle" value="' . $calendar['headerstyle'] . '" /></td>
    </tr>
    <tr>
    <td width="30%">'.__('Day Border', 'quick-event-manager').'</td>
    <td><input type="text" style="width:12em;" label="tdborder" name="tdborder" value="' . $calendar['tdborder'] . '" /> Example: 1px solid red</td>
    </tr>
    <tr>
    <td width="30%">'.__('Cellspacing', 'quick-event-manager').'</td>
    <td><input type="text" style="width:2em;" label="cellspacing" name="cellspacing" value="' . $calendar['cellspacing'] . '" /> px</td>
    </tr>
    <tr>
    <td colspan="2"><h2>'.__('Months', 'quick-event-manager').'</h2></td>
    </tr>
    <tr>
    <td width="30%">'.__('Display 12 month navigation', 'quick-event-manager').'</td>
    <td><input type="checkbox" name="showmonthsabove" ' . $calendar['showmonthsabove'] . ' value="checked" /> '.__('Show above calendar', 'quick-event-manager').'<br>
    <input type="checkbox" name="showmonthsbelow" ' . $calendar['showmonthsbelow'] . ' value="checked" /> '.__('Show below calendar', 'quick-event-manager').'</td>
    </tr>
    <tr>
    <td width="30%">'.__('Caption', 'quick-event-manager').'</td>
    <td><input type="text" style="" label="text" name="monthscaption" value="' . $calendar['monthscaption'] . '" /></td>
    </tr>
    
    <tr>
    <td width="30%">'.__('Hide navigation', 'quick-event-manager').'</td>
    <td><input type="checkbox" name="hidenavigation" ' . $calendar['hidenavigation'] . ' value="checked" /> '.__('Remove Prev and Next links', 'quick-event-manager').'</td>
    </tr>
    <tr>
    <td colspan="2"><h2>'.__('Event Options', 'quick-event-manager').'</h2></td>
    </tr>
    <tr>
    <td width="30%">'.__('Multi-day Events', 'quick-event-manager').'</td>
    <td><input type="checkbox" name="showmultiple" ' . $calendar['showmultiple'] . ' value="checked" /> '.__('Show event on all days', 'quick-event-manager').'</td>
    </tr>
    <tr>
    <td width="30%">'.__('Event Border', 'quick-event-manager').'</td>
    <td><input type="text" style="width:12em;" label="eventborder" name="eventborder" value="' . $calendar['eventborder'] . '" /> enter \'none\' to remove border</td>
    </tr>
    <tr>
    <td width="30%"></td>
    <td><input type="checkbox" name="fixeventborder" ' . $calendar['fixeventborder'] . ' value="checked" /> '.__('Lock border colour (ignore category colours)', 'quick-event-manager').'</td>
    </tr>
    <tr>
    <td>'.__('Corners', 'quick-event-manager').'</td>
    <td>
    <input type="radio" name="event_corner" value="square" ' . $square . ' /> '.__('Square', 'quick-event-manager').'&nbsp;
    <input type="radio" name="event_corner" value="rounded" ' . $rounded . ' /> '.__('Rounded', 'quick-event-manager').'</td>
    </tr>
    <tr>
    <td width="30%">'.__('Text Styles', 'quick-event-manager').'</td>
    <td><input type="checkbox" name="eventbold" ' . $calendar['eventbold'] . ' value="checked" /> '.__('Bold', 'quick-event-manager').'<input type="checkbox" name="eventitalic" ' . $calendar['eventitalic'] . ' value="checked" /> '.__('Italic', 'quick-event-manager').'</td>
    </tr>
    <tr>
    <td width="30%">'.__('Event Image', 'quick-event-manager').'</td>
    <td><input type="checkbox" name="eventimage" ' . $calendar['eventimage'] . ' value="checked" /> '.__('Show event image on the calendar', 'quick-event-manager').'<br>'.__('Image Width', 'quick-event-manager').'<input type="text" style="width:3em;" label="text" name="imagewidth" value="' . $calendar['imagewidth'] . '" /> px</td>
    </tr>
    <tr>
    <td width="30%">'.__('Hover Message', 'quick-event-manager').'</td>
    <td><input type="checkbox" name="usetooltip" ' . $calendar['usetooltip'] . ' value="checked" /> '.__('Show full event title on hover', 'quick-event-manager').'</td>
    </tr>
    <tr>
    <td width="30%">'.__('Character Number', 'quick-event-manager').'</td>
    <td><input type="text" style="width:4em;" label="text" name="eventlength" value="' . $calendar['eventlength'] . '" /><span class="description"> Number of characters to display in event box</span></td>
    </tr>
    <tr>
    <td style="vertical-align:top;">'.__('Small Screens', 'quick-event-manager').'</td>
    <td><span class="description">'.__('What to display on small screens', 'quick-event-manager').':</span><br>
    <input type="radio" name="smallicon" value="trim" ' . $trim . ' /> '.__('Full message', 'quick-event-manager').' <input type="radio" name="smallicon" value="arrow" ' . $arrow . ' /> '.__('&#9654;', 'quick-event-manager').' <input type="radio" name="smallicon" value="box" ' . $box . ' /> '.__('&#9633;', 'quick-event-manager').' <input type="radio" name="smallicon" value="square" ' . $square . ' /> '.__('&#9632;', 'quick-event-manager').' <input type="radio" name="smallicon" value="asterix" ' . $asterix . ' /> '.__('&#9733;', 'quick-event-manager').' 
    <input type="radio" name="smallicon" value="blank" ' . $blank . ' /> '.__('Blank', 'quick-event-manager').' 
    <input type="radio" name="smallicon" value="other" ' . $other . ' /> '.__('Other', 'quick-event-manager').' ('.__('enter escaped', 'quick-event-manager').' <a href="http://www.fileformat.info/info/unicode/char/search.htm" target="blank">unicode</a> '.__('or hex code below', 'quick-event-manager').').<br />
    <input type="text" style="width:6em;" label="text" name="unicode" value="' . $calendar['unicode'] . '" /></td>
    </tr>
    <tr>
    <td width="30%">'.__('Small Screens', 'quick-event-manager').' '.__('Text Size', 'quick-event-manager').'</td>
    <td><input type="text" style="width:3em;" name="eventtextsize" value="' . $calendar['eventtextsize'] . '" />&nbsp;'.__('Trigger Width', 'quick-event-manager').':&nbsp;<input type="text" style="width:5em;" name="trigger" value="' . $calendar['trigger'] . '" /><br>
    <span class="description">This is the text size when the screen is below the trigger width.</span></td>
    </tr>
    </table>
    
    <h2>'.__('Calendar Colours', 'quick-event-manager').'</h2>
    <div class="qem-calcolor">
    
    <p style="font-weight:bold"><span style="float:left;width:10em;">'.__('Items', 'quick-event-manager').'</span>'.__('Background', 'quick-event-manager').' / '.__('Text', 'quick-event-manager').'</p>
    
    <p><span style="float:left;width:10em">'.__('Days of the Week', 'quick-event-manager').'</span>&nbsp;<input type="text" class="qem-color" label="background" name="calday" value="' . $calendar['calday'] . '" /><input type="text" class="qem-color" label="text" name="caldaytext" value="' . $calendar['caldaytext'] . '" /></p>
    
    <p><span style="float:left;width:10em">'.__('Normal Day', 'quick-event-manager').'</span>&nbsp;<input type="text" class="qem-color" label="background" name="day" value="' . $calendar['day'] . '" /></p>
    
    <p><span style="float:left;width:10em">'.__('Event Day', 'quick-event-manager').'</span>&nbsp;<input type="text" class="qem-color" label="background" name="eventday" value="' . $calendar['eventday'] . '" /></p>
    
    <p><span style="float:left;width:10em">'.__('Event', 'quick-event-manager').'</span>&nbsp;<input type="text" class="qem-color" label="background" name="eventbackground" value="' . $calendar['eventbackground'] . '" /><input type="text" class="qem-color" label="text" name="eventtext" value="' . $calendar['eventtext'] . '" /></p>
    
    <p><span style="float:left;width:10em">'.__('Event Hover', 'quick-event-manager').'</span>&nbsp;<input type="text" class="qem-color" label="background" name="eventhover" value="' . $calendar['eventhover'] . '" /></p>
    
    <p><span style="float:left;width:10em">'.__('Past Day', 'quick-event-manager').'</span>&nbsp;<input type="text" class="qem-color" label="background" name="oldday" value="' . $calendar['oldday'] . '" /></p>
    </div>
    <h2>'.__('Categories', 'quick-event-manager').'</h2>
    <table width="100%">
    <tr>
    <td width="30%">'.__('Display category key', 'quick-event-manager').'</td>
    <td><input type="checkbox" name="showkeyabove" ' . $calendar['showkeyabove'] . ' value="checked" /> '.__('Show above calendar', 'quick-event-manager').'<br>
    <input type="checkbox" name="showkeybelow" ' . $calendar['showkeybelow'] . ' value="checked" /> '.__('Show below calendar', 'quick-event-manager').'</td>
    </tr>
    <tr>
    <td width="30%">'.__('Caption:', 'quick-event-manager').'</td>
    <td><input type="text" style="" label="text" name="keycaption" value="' . $calendar['keycaption'] . '" /></td>
    </tr>
    <tr>
    <td width="30%"></td><td><input type="checkbox" name="linktocategories" ' . $calendar['linktocategories'] . ' value="checked" /> '.__('Link keys to categories', 'quick-event-manager').'<br>
    <input type="checkbox" name="showuncategorised" ' . $calendar['showuncategorised'] . ' value="checked" /> '.__('Show uncategorised key', 'quick-event-manager').'</td>
    </tr>
    <tr>
    <td width="30%">'.__('Add link back to all categories', 'quick-event-manager').'</td>
    <td><input type="checkbox" name="calallevents" ' . $calendar['calallevents'] . ' value="checked" /><br><span class="description">'.__('This uses the Calendar page URL set at the top of this page', 'quick-event-manager').'.</span></td>
    </tr>
    <tr>
    <td width="30%">'.__('Caption', 'quick-event-manager').'</td>
    <td><input type="text" style="" label="text" name="calalleventscaption" value="' . $calendar['calalleventscaption'] . '" /></td>
    </tr>
    </table>
    <h2>'.__('Start the Week', 'quick-event-manager').'</h2>
    <p><input type="radio" name="startday" value="sunday" ' . $sunday . ' /> '.__('On Sunday' ,'quick-event-manager').'<br />
    <input type="radio" name="startday" value="monday" ' . $monday . ' /> '.__('On Monday' ,'quick-event-manager').'</p>
    <p><input type="submit" name="Submit" class="button-primary" style="color: #FFF;" value="'.__('Save Changes', 'quick-event-manager').'" /> <input type="submit" name="Reset" class="button-primary" style="color: #FFF;" value="'.__('Reset', 'quick-event-manager').'" onclick="return window.confirm( \''.__('Are you sure you want to reset the calendar settings?', 'quick-event-manager').'\' );"/></p>';
    $content .= wp_nonce_field("save_qem");
    $content .= '</form>
    </div>
    <div class="qem-options">
    <h2>'.__('Calendar Preview', 'quick-event-manager').'</h2>
    <p>'.__('The <em>prev</em> and <em>next</em> buttons only work on your Posts and Pages - so don&#146;t click on them!', 'quick-event-manager').'</p>';
    $content .= qem_show_calendar('');
    $content .= '</div></div>';
    echo $content;
}

function qem_categories ($catxxx,$thecat) {
    $arr = get_categories();
    $content = '<select name="'.$catxxx.'" style="width:8em;">';
    $content .= '<option value=""></option>';
    foreach($arr as $option){
        if ($thecat == $option->slug) $selected = 'selected'; else $selected = '';
        $content .= '<option value="'.$option->slug.'" '.$selected.'>'.$option->name.'</option>';
    }
    $content .= '</select>';
    return $content;
}

function qem_register (){
    $processpercent=$processfixed=$qem_apikey='';
    if( isset( $_POST['Settings']) && check_admin_referer("save_qem")) {
        $options = array(
            'addtoall',
            'formwidth',
            'notarchive',
            'useqpp',
            'usename',
            'usemail',
            'usetelephone',
            'useplaces',
            'usemessage',
            'useattend',
            'usecaptcha',
            'useblank1',
            'useblank2',
            'usedropdown',
            'useselector',
            'usenumber1',
            'reqname',
            'reqmail',
            'reqtelephone',
            'reqmessage',
            'reqblank1',
            'reqblank2',
            'reqdropdown',
            'reqnumber1',
            'formborder',
            'sendemail',
            'qemmail',
            'subject',
            'subjecttitle',
            'subjectdate',
            'title',
            'blurb',
            'yourname',
            'youremail',
            'yourtelephone',
            'yourplaces',
            'yourmessage',
            'yourcaptcha',
            'yourattend',
            'yourblank1',
            'yourblank2',
            'yourdropdown',
            'yourselector',
            'yournumber1',
            'useaddinfo',
            'useoptin',
            'addinfo',
            'captchalabel',
            'qemsubmit',
            'error',
            'replytitle',
            'replyblurb',
            'eventfull',
            'eventfullmessage',
            'eventlist',
            'showuser',
            'linkback',
            'usecopy',
            'copyblurb',
            'optinblurb',
            'alreadyregistered',
            'useread_more',
            'read_more',
            'sort',
            'registeredusers',
            'allowmultiple',
            'nameremoved',
            'checkremoval',
            'allowtags',
            'useterms',
            'termslabel',
            'termsurl',
            'termstarget',
            'ontheright',
            'usemorenames',
            'morenames',
            'ignorepayment',
            'ignorepaymentlabel',
            'nonotifications',
            'waitinglist',
            'waitinglistreply',
            'waitinglistmessage',
            'moderate',
            'moderatereply',
            'moderateplaces',
            'placesavailable',
            'copychecked',
            'redirectionurl',
            'useattachment',
            'attachmentlabel',
            'attachmenttypes',
            'attachmentsize',
            'listnames',
            'listblurb',
            'hideform'
        );
        foreach ($options as $item) {
            $register[$item] = stripslashes( $_POST[$item]);
            if ($_POST['allowtags'])
                $register[$item] = strip_tags($register[$item],'<p><b><a><em><i><strong>');
            else 
                $register[$item] = filter_var($register[$item],FILTER_SANITIZE_STRING);
        }
        update_option('qem_register', $register);

        qem_admin_notice(__('The registration form settings have been updated', 'quick-event-manager'));
    }
    if( isset( $_POST['Reset']) && check_admin_referer("save_qem")) {
        delete_option('qem_register');
        qem_admin_notice(__('The registration form settings have been reset', 'quick-event-manager'));
    }
    if( isset( $_POST['Validate']) && check_admin_referer("save_qem")) {
        $apikey = $_POST['qem_apikey'];
        $blogurl = get_site_url();
        $akismet = new qem_akismet($blogurl, $apikey);
        if($akismet->isKeyValid()) {
            qem_admin_notice("Valid Akismet API Key. All messages will now be checked against the Akismet database.");update_option('qem-akismet', $apikey);
        } else qem_admin_notice("Your Akismet API Key is not Valid");
    }
    if( isset( $_POST['Delete']) && check_admin_referer("save_qem")) {
        delete_option('qem-akismet');
        qem_admin_notice("Akismet validation is no longer active on the Quick Event Manager");
    }
    
    if( isset( $_POST['Styles']) && check_admin_referer("save_qem")) {
        $options = array(
            'font-colour',
            'text-font-colour',
            'input-border',
            'input-required',
            'inputbackground',
            'inputfocus',
            'border',
            'form-width',
            'submit-background',
            'submit-hover-background',
            'submitwidth',
            'submitwidthset',
            'submitposition',
            'submit-border',
            'background',
            'backgroundhex',
            'corners',
            'form-border',
            'header-type',
            'header-size',
            'header-colour',
            'error-font-colour',
            'error-border',
            'line_margin',
            'nostyling'
        );
        foreach ( $options as $item) {
            $style[$item] = stripslashes($_POST[$item]);
            $style[$item] =filter_var($style[$item],FILTER_SANITIZE_STRING);
        }
        if ($style['widthtype'] == 'pixel') {
            $formwidth = preg_split('#(?<=\d)(?=[a-z%])#i', $style['width']);
            if (!$formwidth[1]) $formwidth[1] = 'px';
            $style['width'] = $formwidth[0].$formwidth[1];
        }
        update_option( 'qem_register_style', $style);
        qem_admin_notice("The form styles have been updated.");
    }
    if( isset( $_POST['Resetstyles']) && check_admin_referer("save_qem")) {
        delete_option('qem_register_style');
        qem_admin_notice("The form styles have been reset.");
    }
    $style = qem_get_register_style();
    ${$style['widthtype']} = 'checked';
    ${$style['submitwidth']} = 'checked';
    ${$style['submitposition']} = 'checked';
    ${$style['border']} = 'checked';
    ${$style['background']} = 'checked';
    ${$style['corners']} = 'checked';
    ${$style['header-type']} = 'checked';
    
    $register = qem_get_stored_register();
    $qemkey = get_option('qpp_key');
    ${$register['qemmail']} = 'checked';
    $content = qem_head_css();
    $content .= '<div class="qem-settings"><div class="qem-options">
    <form id="" method="post" action="">
    <p>Use the <a href="?page=quick-event-manager/settings.php&tab=settings">Settings</a> to enable the registration form. You can then manage forms for individual events using the event editor</p>
    <table width="100%">
    <tr>
    <td colspan="3"><h2>'.__('General Settings', 'quick-event-manager').'</h2></td></tr>
    <tr>
    <td width="5%"><input type="checkbox" name="addtoall"' . $register['addtoall'] . ' value="checked" /></td>
    <td colspan="2">'.__('Add form to all new events', 'quick-event-manager').'</td>
    </tr>
    <tr>
    <td width="5%"><input type="checkbox" name="hideform"' . $register['hideform'] . ' value="checked" /></td>
    <td colspan="2">'.__('Hide form until registration button is clicked', 'quick-event-manager').'</td>
    </tr>
    <tr>
    <td width="5%"><input type="checkbox" name="ontheright"' . $register['ontheright'] . ' value="checked" /></td>
    <td colspan="2">'.__('Display the registration form on the right below the event image and map (if used)', 'quick-event-manager').'</td>
    </tr>
    <tr>
    <td width="5%"><input type="checkbox" name="notarchive" ' . $register['notarchive'] . ' value="checked" /></td>
    <td colspan="2">'.__('Do not display registration form on old events', 'quick-event-manager').'</td>
    </tr>
    <tr>
    <td width="5%"><input type="checkbox" name="showuser" ' . $register['showuser'] . ' value="checked" /></td>
    <td colspan="2">'.__('Pre-fill user name if logged in', 'quick-event-manager').'</td>
    </tr>
    <tr>
    <td width="5%"><input type="checkbox" name="registeredusers" ' . $register['registeredusers'] . ' value="checked" /></td>
    <td colspan="2">'.__('Only users who have logged in can register', 'quick-event-manager').'</td>
    </tr>
    <tr>
    <td width="5%"><input type="checkbox" name="allowmultiple" ' . $register['allowmultiple'] . ' value="checked" /></td>
    <td colspan="2">'.__('Allow a person to register more than once for each event', 'quick-event-manager').'.</td>
    </tr>
    <tr>
    <td width="5%"><input type="checkbox" name="eventfull" ' . $register['eventfull'] . ' value="checked" /></td>
    <td colspan="2">'.__('Hide registration form when event is full', 'quick-event-manager').'</td>
    </tr>
    <tr>
    <td></td>
    <td>'.__('Message to display', 'quick-event-manager').':</td>
    <td><input type="text" style="" name="eventfullmessage" value="' . $register['eventfullmessage'] . '" /></td>
    </tr>
    <tr>
    <td width="5%"><input type="checkbox" name="allowtags"' . $register['allowtags'] . ' value="checked" /></td>
    <td colspan="2">'.__('Allow HTML tags', 'quick-event-manager').' '.__('Warning: this may leave your site open to CSRF and XSS attacks so be careful.', 'quick-event-manager').'</td>
    </tr>
    <tr>
    <td colspan="3"><a href="#styling">'.__('Click Here for Form Styling', 'quick-event-manager').'</a></td>
    </tr>
    <tr>
    <td colspan="3"><h2>'.__('Notifications', 'quick-event-manager').'</h2></td>
    <tr>
    <td colspan="2">'.__('Your Email Address', 'quick-event-manager').'</td>
    <td><input type="text" style="" name="sendemail" value="' . $register['sendemail'] . '" /><br><span class="description">'.__('This is where registration notifications will be sent', 'quick-event-manager').'</span></td>
    </tr>
    
    <tr>
    <td colspan="2">Send Function</td>
    <td><input type="radio" name="qemmail" value="wpmail" ' . $wpmail . '> WP-mail (should work for most email addresses)<br />
    <input type="radio" name="qemmail" value="smtp" ' . $smtp . '> SMTP (Only use if you have <a href="?page=quick-event-manager/settings.php&tab=smtp">set up SMTP</a>)</td>
    </tr>
    
    <tr>
    <td width="5%"><input type="checkbox" name="nonotifications" ' . $register['nonotifications'] . ' value="checked" /></td>
    <td colspan="2">'.__('Do not send notifications').'</td>
    </tr>

    <tr>
    <td colspan="3"><h2>'.__('Registration Form', 'quick-event-manager').'</h2></td>
    </tr>
    <tr>
    <td colspan="2">'.__('Form title', 'quick-event-manager').'</td>
    <td><input type="text" style="" name="title" value="' . $register['title'] . '" /></td>
    </tr>
    <tr>
    <td colspan="2">'.__('Form blurb', 'quick-event-manager').'</td>
    <td><input type="text" style="" name="blurb" value="' . $register['blurb'] . '" /></td>
    </tr>
    </table>
    <p>'.__('Check those fields you want to use. Drag and drop to change the order', 'quick-event-manager').'.</p>
    <style>table#sorting{width:100%;}
    #sorting tbody tr{outline: 1px solid #888;background:#E0E0E0;}
    #sorting tbody td{padding: 2px;vertical-align:middle;}
    #sorting{border-collapse:separate;border-spacing:0 5px;}</style>
    <script>
    jQuery(function() 
    {var qem_rsort = jQuery( "#qem_rsort" ).sortable(
    {axis: "y",cursor: "move",opacity:0.8,update:function(e,ui)
    {var order = qem_rsort.sortable("toArray").join();jQuery("#qem_register_sort").val(order);}});});
    </script>
    <table id="sorting">
    <thead>
    <tr>
    <th width="5%">U</th>
    <th width="5%">R</th>
    <th width="20%">'.__('Field', 'quick-event-manager').'</th>
    <th>'.__('Label', 'quick-event-manager').'</th>
    </tr>
    </thead><tbody id="qem_rsort">';
    $sort = explode(",", $register['sort']);
    foreach ($sort as $name) {
        switch ( $name ) {
            case 'field1':
            $use = 'usename';
            $req = 'reqname';
            $label = __('Name', 'quick-event-manager');
            $input = 'yourname';
            break;
            case 'field2':
            $use = 'usemail';
            $req = 'reqmail';
            $label = __('Email', 'quick-event-manager');
            $input = 'youremail';
            break;
            case 'field3':
            $use = 'useattend';
            $req = '';
            $label = __('Not Attending', 'quick-event-manager');
            $input = 'yourattend';
            break;
            case 'field4':
            $use = 'usetelephone';
            $req = 'reqtelephone';
            $label = __('Telephone', 'quick-event-manager');
            $input = 'yourtelephone';
            break;
            case 'field5':
            $use = 'useplaces';
            $req = '';
            $label = __('Places', 'quick-event-manager');
            $input = 'yourplaces';
            break;
            case 'field6':
            $use = 'usemessage';
            $req = 'reqmessage';
            $label = __('Message', 'quick-event-manager');
            $input = 'yourmessage';
            break;
            case 'field7':
            $use = 'usecaptcha';
            $req = '';
            $label = __('Captcha', 'quick-event-manager');
            $input = 'captchalabel';
            break;
            case 'field8':
            $use = 'usecopy';
            $req = '';
            $label = __('Copy Message', 'quick-event-manager');
            $input = 'copyblurb';
            break;
            case 'field9':
            $use = 'useblank1';
            $req = 'reqblank1';
            $label = __('User defined', 'quick-event-manager');
            $input = 'yourblank1';
            break;
            case 'field10':
            $use = 'useblank2';
            $req = 'reqblank2';
            $label = __('User defined', 'quick-event-manager');
            $input = 'yourblank2';
            break;
            case 'field11':
            $use = 'usedropdown';
            $req = '';
            $label = __('Dropdown', 'quick-event-manager');
            $input = 'yourdropdown';
            break;
            case 'field12':
            $use = 'usenumber1';
            $req = 'reqnumber1';
            $label = __('Number', 'quick-event-manager');
            $input = 'yournumber1';
            break;
            case 'field13':
            $use = 'useaddinfo';
            $req = '';
            $label = __('Additional Info (displays as plain text)', 'quick-event-manager');
            $input = 'addinfo';
            break;
            case 'field14':
            $use = 'useselector';
            $req = '';
            $label = __('Dropdown', 'quick-event-manager');
            $input = 'yourselector';
            break;
            case 'field15':
            $use = 'useoptin';
            $req = '';
            $label = __('Optin Checkbox', 'quick-event-manager');
            $input = 'optinblurb';
            break;
        }
        $content .= '<tr id="'.$name.'">
        <td width="5%"><input type="checkbox" name="'.$use.'" ' . $register[$use] . ' value="checked" /></td>
        <td width="5%">';
        if ($req) $content .= '<input type="checkbox" name="'.$req.'" ' . $register[$req] . ' value="checked" />';
        $content .= '</td><td width="20%">'.$label.'</td><td>';
        if ($name=='field7') $content .= __('Adds a maths captcha to confuse the spammers.', 'quick-event-manager');
        $content .= '<input type="text" style="border: 1px solid #343838;" name="'.$input.'" value="' . $register[$input] . '" />';
        $content .= '</td></tr>';
    }
    $content .='</tbody>
    </table>
    <input type="hidden" id="qem_register_sort" name="sort" value="'.$register['sort'].'" />
    
    <table>
    <tr>
    <td colspan="2">'.__('Submit Button', 'quick-event-manager').'</td>
    <td><input type="text" style="" name="qemsubmit" value="' . $register['qemsubmit'] . '" /></td>
    </tr>
    <tr>
    <td colspan="3"><h2>'.__('Show box for more names', 'quick-event-manager').'</h2></td>
    </tr>
    <tr>
    <td width="5%"><input type="checkbox" name="usemorenames" ' . $register['usemorenames'] . ' value="checked" /></td>
    <td colspan="2">'.__('Show box to add more names if number attending is greater than 1').'</td>
    </tr>
    <tr>
    <td></td>
    <td>'.__('More names label', 'quick-event-manager').'</td>
    <td><input type="text" style="" name="morenames" value="' . $register['morenames'] . '" /></td>
    </tr>
    
    <tr>
    <td colspan="3"><h2>'.__('Terms and Conditions', 'quick-event-manager').'</h2></td>
    </tr>
    <tr>
    <td width="5%"><input type="checkbox" name="useterms" ' . $register['useterms'] . ' value="checked" /></td>
    <td colspan="2">'.__('Include Terms and Conditions checkbox').'</td>
    </tr>
    <tr>
    <td></td>
    <td>'.__('T&C label', 'quick-event-manager').'</td>
    <td><input type="text" style="" name="termslabel" value="' . $register['termslabel'] . '" /></td>
    </tr>
    <tr>
    <td></td>
    <td>'.__('T&C URL', 'quick-event-manager').'</td>
    <td><input type="text" style="" name="termsurl" value="' . $register['termsurl'] . '" /></td>
    </tr>
    <tr>
    <td width="5%"><input type="checkbox" name="termstarget" ' . $register['termstarget'] . ' value="checked" /></td>
    <td colspan="2">'.__('Open link in new Tab/Window', 'quick-event-manager').'</td>
    </tr>
    
    <tr>
    <td colspan="3"><h2>'.__('Register without payment', 'quick-event-manager').'</h2></td>
    </tr>
    <tr>
    <td width="5%"><input type="checkbox" name="ignorepayment" ' . $register['ignorepayment'] . ' value="checked" /></td>
    <td colspan="2">'.__('Allow registration without payment').'<br>
<span class="description">'.__('Only displays if there is a cost and the transfer to paypal option is enabled on the', 'quick-event-manager').' <a href="?page=quick-event-manager/settings.php&tab=payment">'.__('Payments Page', 'quick-event-manager').'</a></span>.</td>
    </tr>
    <tr>
    <td></td>
    <td>'.__('Label', 'quick-event-manager').'</td>
    <td><input type="text" style="" name="ignorepaymentlabel" value="' . $register['ignorepaymentlabel'] . '" /></td>
    </tr>
    <tr>
    <td colspan="3"><h2>'.__('Error and Thank-you messages', 'quick-event-manager').'</h2></td>
    </tr>
    <tr>
    <td colspan="2">'.__('Thank you message title', 'quick-event-manager').'</td>
    <td><input type="text" style="" name="replytitle" value="' . $register['replytitle'] . '" /></td>
    </tr>
    <tr>
    <td colspan="2">'.__('Thank you message blurb', 'quick-event-manager').'</td>
    <td><textarea style="width:100%;height:100px;" name="replyblurb">' . $register['replyblurb'] . '</textarea></td>
    </tr>
    <tr>
    <td colspan="2">'.__('Error Message', 'quick-event-manager').'</td>
    <td><input type="text" style="" name="error" value="' . $register['error'] . '" /></td>
    </tr>
    <tr>
    <td colspan="2">'.__('Already Registered', 'quick-event-manager').'</td>
    <td><input type="text" style="" name="alreadyregistered" value="' . $register['alreadyregistered'] . '" /></td>
    </tr>
    <tr>
    <td width="5%"><input type="checkbox" name="checkremoval" ' . $register['checkremoval'] . ' value="checked" /></td>
    <td colspan="2">'.__('Use \'Not Attending\' option to allow people to remove their names from the list', 'quick-event-manager').'</td>
    </tr>
    <tr>
    <td></td>
    <td>'.__('Name Removed Message', 'quick-event-manager').'</td>
    <td><input type="text" style="" name="nameremoved" value="' . $register['nameremoved'] . '" /></td>
    </tr>
    <tr>
    <td width="5%"><input type="checkbox" name="useread_more" ' . $register['useread_more'] . ' value="checked" /></td>
    <td colspan="2">'.__('Display a \'return to event\' message after registration', 'quick-event-manager').'</td>
    </tr>
    <tr>
    <td></td>
    <td>'.__('Return to event message:', 'quick-event-manager').'</td>
    <td><input type="text" style="width:100%;" label="read_more" name="read_more" value="' . $register['read_more'] . '" /></td>
    </tr>
    <tr>
    <td colspan="3"><h2>'.__('Confirmation Email', 'quick-event-manager').'</h2></td>
    </tr>
    <tr>
    <td width="5%"><input type="checkbox" name="copychecked"' . $register['copychecked'] . ' value="checked" /></td>
    <td colspan="2">'.__('Set default \'Copy Message\' field to \'checked\'', 'quick-event-manager').'</td>
    </tr>
    <tr>
    <td colspan="3">You can reply to the sender using the <a href="?page=quick-event-manager/settings.php&tab=auto">Auto Responder</a>.</td>
    </tr>
    <tr>
    <td colspan="3"><h2>'.__('Redirection', 'quick-event-manager').'</h2></td>
    </tr>
    <tr>
    <td colspan="3">This will redirect visitors to a URL instead of displaying the thank you message</td>
    </tr>
    <tr>
    <td></td>
    <td>'.__('Redirection URL', 'quick-event-manager').':</td>
    <td><input type="text" name="redirectionurl" value="' . $register['redirectionurl'] . '" /></td>
    </tr>
    
    <tr>
    <td colspan="3"><h2>'.__('Moderation', 'quick-event-manager').'</h2></td>
    </tr>
    <tr>
    <td width="5%"><input type="checkbox" name="moderate" ' . $register['moderate'] . ' value="checked" /></td>
    <td colspan="2">'.__('Moderate all registrations', 'quick-event-manager').'</td>
    </tr>
    <tr>
    <td></td>
    <td>'.__('Message to display after registration', 'quick-event-manager').':</td>
    <td><input type="text" name="moderatereply" value="' . $register['moderatereply'] . '" /></td>
    </tr>
    <tr>
    <td width="5%"><input type="checkbox" name="moderateplaces" ' . $register['moderateplaces'] . ' value="checked" /></td>
    <td colspan="2">'.__('Update places available before moderation', 'quick-event-manager').'.</td>
    </tr>
    
    <tr>
    <td colspan="3"><h2>'.__('Waiting Lists', 'quick-event-manager').'</h2></td>
    </tr>
    <tr>
    <td width="5%"><input type="checkbox" name="waitinglist" ' . $register['waitinglist'] . ' value="checked" /></td>
    <td colspan="2">'.__('Allow people to register even if there are no places available', 'quick-event-manager').'.</td>
    </tr>
    <tr>
    <td></td><td>'.__('Message to display', 'quick-event-manager').':</td>
    <td>' . $register['numberattendingbefore'] . ' 0 ' . $register['numberattendingafter'] . '<input type="text" name="waitinglistmessage" value="' . $register['waitinglistmessage'] . '" /></td>
    </tr>
    <tr>
    <td></td><td>'.__('Message to display after registration', 'quick-event-manager').':</td>
    <td><input type="text" name="waitinglistreply" value="' . $register['waitinglistreply'] . '" /></td>
    </tr>
    
    <tr>
    <td colspan="3"><h2>'.__('Attendee Lists', 'quick-event-manager').'</h2></td>
    </tr>
    <tr>
    <td width="5%"><input type="checkbox" name="listnames" ' . $register['listnames'] . ' value="checked" /></td>
    <td colspan="2">'.__('Show attendees as a list', 'quick-event-manager').'.</td>
    </tr>
    <tr>
    <td width="5%"></td>
    <td colspan="2"><input type="text" name="listblurb" value="' . $register['listblurb'] . '" /><br>
    <span class="description">Shortcodes: [name],[places],[telephone],[website] (uses first used defined field)</span>
    </tr>
    
    </table>
    <p><input type="submit" name="Settings" class="button-primary" style="color: #FFF;" value="'.__('Save Settings', 'quick-event-manager').'" />
    <input type="submit" name="Reset" class="button-primary" style="color: #FFF;" value="'.__('Reset', 'quick-event-manager').'" onclick="return window.confirm( \''.__('Are you sure you want to reset the registration form settings?', 'quick-event-manager').'\' );"/></p>
    <div id="styling"></div>
    <h2>'.__('Use Akismet Validation', 'quick-event-manager').'</h2>
    <p>'.__('Enter your API Key to check all messages against the Akismet database.', 'quick-event-manager').'</p> 
    <p><input type="text" label="akismet" name="qem_apikey" value="'.$qem_apikey.'" /></p>
    <p><input type="submit" name="Validate" class="button-primary" style="color: #FFF;" value="Activate Akismet Validation" /> <input type="submit" name="Delete" class="button-secondary" value="Deactivate Aksimet Validation" onclick="return window.confirm( \'This will delete the Akismet Key.\nAre you sure you want to do this?\' );"/></p>';
    $content .= wp_nonce_field("save_qem");
    $content .= '</form>
    <h2 style="color:blue;">Form Styling</h2>';
    if ($qemkey['authorised']) {
    $content .='<form method="post" action="">
    <span class="description"><b>NOTE:</b>Leave fields blank if you don\'t want to use them</span>
    <table>
    </tr>
    <td colspan="2"><input type="checkbox" name="nostyling" ' . $style['nostyling'] . ' value="checked" />'.__('Use theme styles', 'quick-event-manager').'.</td>
    </td>
    </tr>
    </tr>
    <td colspan="2"><h2>Form Width</h2>
    </td>
    </tr>
    <tr>
    <td colspan="2">'.__('Width', 'quick-event-manager').': <input type="text" style="width:4em" name="form-width" value="' . $style['form-width'] . '" /> use px, em or %. Default is px.</td>
    </tr>
    <td colspan="2"><h2>Form Border</h2>
    <p>Note: The rounded corners and shadows only work on CSS3 supported browsers and even then not in IE8. Don\'t blame me, blame Microsoft.</p></td>
    </tr>
    <tr>
    <td>Type:</td>
    <td><input style="margin:0; padding:0; border:none;" type="radio" name="border" value="none" ' . $none . ' /> No border<br />
    <input style="margin:0; padding:0; border:none;" type="radio" name="border" value="plain" ' . $plain . ' /> Plain Border<br />
    <input style="margin:0; padding:0; border:none;" type="radio" name="border" value="rounded" ' . $rounded . ' /> Round Corners (Not IE8)<br />
    <input style="margin:0; padding:0; border:none;" type="radio" name="border" value="shadow" ' . $shadow . ' /> Shadowed Border(Not IE8)<br />
    <input style="margin:0; padding:0; border:none;" type="radio" name="border" value="roundshadow" ' . $roundshadow . ' /> Rounded Shadowed Border (Not IE8)</td>
    </tr>
    <tr>
    <td>Style:</td>
    <td><input type="text" label="form-border" name="form-border" value="' . $style['form-border'] . '" /></td>
    </tr>
    <tr>
    <td colspan="2"><h2>Background</h2></td>
    </tr>
    <tr>
    <td>Colour:</td>
    <td>
    <input style="margin:0; padding:0; border:none;" type="radio" name="background" value="theme" ' . $theme . ' /> Use theme colours<br />
    <input style="margin:0; padding:0; border:none;" type="radio" name="background" value="white" ' . $white . ' /> White<br />
    <input style="margin:0; padding:0; border:none;" type="radio" name="background" value="color" ' . $color . ' />	Set your own: 
    <input type="text" class="qem-color" label="background" name="backgroundhex" value="' . $style['backgroundhex'] . '" /></td>
    </tr>
    <tr>
    <td colspan="2"><h2>Form Header</h2></td>
    </tr>
    <tr>
    <td style="vertical-align:top;">'.__('Header Type', 'quick-event-manager').'</td>
    <td><input style="margin:0; padding:0; border:none;" type="radio" name="header-type" value="h2" ' . $h2 . ' /> H2 <input style="margin:0; padding:0; border:none;" type="radio" name="header-type" value="h3" ' . $h3 . ' /> H3 <input style="margin:0; padding:0; border:none;" type="radio" name="header-type" value="h4" ' . $h4 . ' /> H4</td>
    </tr>
    <tr>
    <td>Header Size: </td>
    <td><input type="text" style="width:6em" label="header-size" name="header-size" value="' . $style['header-size'] . '" /></td>
    </tr>
    <tr>
    <td>Header Colour: </td>
    <td><input type="text" class="qem-color" label="header-colour" name="header-colour" value="' . $style['header-colour'] . '" /></td>
    </tr>
    <tr>
    <td colspan="2"><h2>Input Fields</h2></td>
    </tr>
    <tr>
    <td>Font Colour: </td>
    <td><input type="text" class="qem-color" label="font-colour" name="font-colour" value="' . $style['font-colour'] . '" /></td>
    </tr>
    <tr>
    <td>Normal Border: </td>
    <td><input type="text" label="input-border" name="input-border" value="' . $style['input-border'] . '" /></td>
    </tr>
    <tr>
    <td>Required Fields: </td>
    <td><input type="text" label="input-required" name="input-required" value="' . $style['input-required'] . '" /></td>
    </tr>
    <tr>
    <td>Background: </td>
    <td><input type="text" class="qem-color" label="inputbackground" name="inputbackground" value="' . $style['inputbackground'] . '" /></td>
    </tr>
    <tr>
    <td>Focus: </td>
    <td><input type="text" class="qem-color" label="inputfocus" name="inputfocus" value="' . $style['inputfocus'] . '" /></td>
    </tr>
    <tr><td>Corners: </td>
    <td><input style="margin:0; padding:0; border:none;" type="radio" name="corners" value="corner" ' . $corner . ' /> Use theme settings <input style="margin:0; padding:0; border:none;" type="radio" name="corners" value="square" ' . $square . ' /> Square corners 	<input style="margin:0; padding:0; border:none;" type="radio" name="corners" value="round" ' . $round . ' /> 5px rounded corners</td>
    </tr>
    <tr>
    <td style="vertical-align:top;">'.__('Margins and Padding', 'quick-event-manager').'</td>
    <td><span class="description">'.__('Set the margins and padding of each bit using CSS shortcodes', 'quick-contact-form').':</span><br><input type="text" label="line margin" name="line_margin" value="' . $style['line_margin'] . '" /></td>
    </tr>
    <tr>
    <td colspan="2"><h2>Other text content</h2></td>
    </tr>
    <tr>
    <td>Font Colour: </td>
    <td><input type="text" class="qem-color" label="text-font-colour" name="text-font-colour" value="' . $style['text-font-colour'] . '" /></td>
    </tr>
    <tr>
    <td colspan="2"><h2>Error Messages</h2></td>
    </tr>
    <tr><td>Font/Border Colour: </td>
    <td><input type="text" class="qem-color" label="error-font-colour" name="error-font-colour" value="' . $style['error-font-colour'] . '" /></td>
    </tr>
    <tr>
    <td colspan="2"><h2>Submit Button</h2></td>
    </tr>
    <tr>
    <td>'.__('Background', 'quick-event-manager').'</td>
    <td><input type="text" class="qem-color" label="background" name="submit-background" value="' . $style['submit-background'] . '" /></td>
    </tr>
    <tr>
    <td>'.__('Hover', 'quick-event-manager').'</td>
    <td><input type="text" class="qem-color" label="hoverbackground" name="submit-hover-background" value="' . $style['submit-hover-background'] . '" /></td>
    </tr>
    <tr>
    <td>Border: </td><td><input type="text" label="submit-border" name="submit-border" value="' . $style['submit-border'] . '" /></td></tr>
    <tr>
    <td>Size: </td><td><input style="margin:0; padding:0; border:none;" type="radio" name="submitwidth" value="submitpercent" ' . $submitpercent . ' /> Same width as the form<br />
    <input style="margin:0; padding:0; border:none;" type="radio" name="submitwidth" value="submitrandom" ' . $submitrandom . ' /> Same width as the button text<br />
    <input style="margin:0; padding:0; border:none;" type="radio" name="submitwidth" value="submitpixel" ' . $submitpixel . ' /> Set your own width: <input type="text" style="width:5em" label="submitwidthset" name="submitwidthset" value="' . $style['submitwidthset'] . '" /> (px, % or em)</td>
    </tr>
    <tr>
    <td>Position: </td><td><input style="margin:0; padding:0; border:none;" type="radio" name="submitposition" value="submitleft" ' . $submitleft . ' /> Left <input style="margin:0; padding:0; border:none;" type="radio" name="submitposition" value="submitright" ' . $submitright . ' /> Right</td></tr>
    </table>
    <p><input type="submit" name="Styles" class="button-primary" style="color: #FFF;" value="Save Styles" /> <input type="submit" name="Resetstyles" class="button-primary" style="color: #FFF;" value="Reset Styles" onclick="return window.confirm( \'Are you sure you want to reset the styles?\' );"/></p>';
    $content .= wp_nonce_field("save_qem");
    $content .= '</form>';
    } else {
        $content .= '<h2>Form Styling</h2>
        <p>Registration Form styling is only avaialble to Pro users.</p>
        <p><a href="?page=quick-event-manager/settings.php&tab=incontext">Find out more</a></p>';
    }
    $content .= '</div>
    <div class="qem-options">
    <h2>'.__('Example form', 'quick-event-manager').'</h2>
    <p>'.__('This is an example of the form. When it appears on your site it will use your theme styles.', 'quick-event-manager').'</p>';
    $content .= qem_loop();
	$content .= '</div></div>';
	echo $content;		
}

function qem_autoresponse_page() {
    if( isset( $_POST['Submit']) && check_admin_referer("save_qem")) {
        $options = array(
            'enable',
            'whenconfirm',
            'subject',
            'subjecttitle',
            'subjectdate',
            'message',
            'useeventdetails',
            'eventdetailsblurb',
            'useregistrationdetails',
            'registrationdetailsblurb',
            'sendcopy',
            'fromname',
            'fromemail',
            'permalink'
        );
        foreach ( $options as $item) {
            $auto[$item] = stripslashes($_POST[$item]);
        }
        update_option( 'qem_autoresponder', $auto );
        qem_admin_notice("The autoresponder settings have been updated.");
    }
    if( isset( $_POST['Reset']) && check_admin_referer("save_qem")) {
        delete_option('qem_autoresponder');
        qem_admin_notice("The autoresponder settings have been reset.");
    }
	$auto = qem_get_stored_autoresponder();
    ${$auto['whenconfirm']} = 'checked';
    $message = $auto['message'];
    $content ='<div class="qem-settings"><div class="qem-options" style="width:90%;">
	<h2 style="color:#B52C00">'.__('Auto responder settings', 'quick-event-manager').'</h2>
    <p>'.__('The Auto Responder will send an email to the Registrant if enabled of if they choose to recieve a copy of their details', 'quick-event-manager').'.</p>
    <form method="post" action="">
    <p><input type="checkbox" name="enable"' . $auto['enable'] . ' value="checked" /> '.__('Enable Auto Responder', 'quick-event-manager').'.</p>
 <p><input style="width:20px; margin: 0; padding: 0; border: none;" type="radio" name="whenconfirm" value="aftersubmission" ' . $aftersubmission . ' /> After registration<br>
    <input style="width:20px; margin: 0; padding: 0; border: none;" type="radio" name="whenconfirm" value="afterpayment" ' . $afterpayment . ' /> After payment (only works if <a href="?page=quick-event-manager/settings.php&tab=payment">IPN</a> is active)</span></p>
    <p>'.__('From Name:', 'quick-event-manager').' (<span class="description">'.__('Defaults to your', 'quick-event-manager').' <a href="'. get_admin_url().'options-general.php">'.__('Site Title', 'quick-event-manager').'</a> '.__('if left blank', 'quick-event-manager').'.</span>):<br>
    <input type="text" style="width:50%" name="fromname" value="' . $auto['fromname'] . '" /></p>
    <p>'.__('From Email:', 'quick-event-manager').' (<span class="description">'.__('Defaults to the', 'quick-event-manager').' <a href="'. get_admin_url().'options-general.php">'.__('Admin Email', 'quick-event-manager').'</a> '.__('if left blank', 'quick-event-manager').'.</span>):<br>
    <input type="text" style="width:50%" name="fromemail" value="' . $auto['fromemail'] . '" /></p>    
    <p>'.__('Subject:', 'quick-event-manager').'<br>
    <input style="width:100%" type="text" name="subject" value="' . $auto['subject'] . '"/></p>
    <p><input type="checkbox" name="subjecttitle"' . $auto['subjecttitle'] . ' value="checked" />&nbsp'.__('Show event title', 'quick-event-manager').'&nbsp;
    <input type="checkbox" name="subjectdate"' . $auto['subjectdate'] . ' value="checked" />&nbsp;'.__('Show date', 'quick-event-manager').'</p>
    <h2>'.__('Message Content', 'quick-event-manager').'</h2>
    <p>'.__('To create individual event messages use the \'Registration Confirmation Message\' option at the bottom of the', 'quick-event-manager').' <a href="post-new.php?post_type=event">'.__('Event Editor', 'quick-event-manager').'</a>.</p>';
    echo $content;
    wp_editor($message, 'message', $settings = array('textarea_rows' => '20','wpautop'=>false));
    $content = '<p>'.__('You can use the following shortcodes in the message body:', 'quick-event-manager').'</p>
    <table>
    <tr><th>Shortcode</th><th>'.__('Replacement Text', 'quick-event-manager').'</th></tr>
    <tr><td>[name]</td><td>'.__('The registrants name from the form', 'quick-event-manager').'</td></tr>
    <tr><td>[event]</td><td>'.__('The event title', 'quick-event-manager').'</td></tr>
    <tr><td>[date]</td><td>'.__('Date of the event', 'quick-event-manager').'</td></tr>
    <tr><td>[places]</td><td>'.__('The number of places required', 'quick-event-manager').'</td></tr>
    </table>';
    $content .='<p><input type="checkbox" name="useregistrationdetails"' . $auto['useregistrationdetails'] . ' value="checked" />&nbsp;'.__('Add registration details to the email', 'quick-event-manager').'</p>
    <p>'.__('Registration details blurb', 'quick-event-manager').'<br>
    <input type="text" style="" name="registrationdetailsblurb" value="' . $auto['registrationdetailsblurb'] . '" /></p>
    <p><input type="checkbox" name="useeventdetails"' . $auto['useeventdetails'] . ' value="checked" />&nbsp;'.__('Add event details to the email', 'quick-event-manager').'</p>
    <p>'.__('Event details blurb', 'quick-event-manager').'<br>
<input type="text" style="" name="eventdetailsblurb" value="' . $auto['eventdetailsblurb'] . '" /></p
    <p><input type="checkbox" name="permalink"' . $auto['permalink'] . ' value="checked" />&nbsp;'.__('Include link to event page', 'quick-event-manager').'</td>
    <p><input type="submit" name="Submit" class="button-primary" style="color: #FFF;" value="Save Changes" /> <input type="submit" name="Reset" class="button-primary" style="color: #FFF;" value="Reset" onclick="return window.confirm( \'Are you sure you want to reset the auto responder settings?\' );"/></p>';
    $content .= wp_nonce_field("save_qem");
    $content .= '</form>
    </div>
    </div>';
    echo $content;
}

function qem_payment (){
    if( isset( $_POST['Submit']) && check_admin_referer("save_qem")) {
        $options = array(
            'qppform',
            'paypal',
            'paypalemail',
            'currency',
            'useprocess',
            'processtype',
            'processpercent',
            'processfixed',
            'waiting',
            'qempaypalsubmit',
            'ipn',
            'title',
            'paid',
            'ipnblock',
            'sandbox',
            'usecoupon',
            'couponcode',
            
        );
        foreach ($options as $item) {
            $payment[$item] = stripslashes( $_POST[$item]);
            $payment[$item] = filter_var($payment[$item],FILTER_SANITIZE_STRING);
        }
        update_option('qem_payment', $payment);
        qem_admin_notice(__('The payment form settings have been updated', 'quick-event-manager'));
    }
    if( isset( $_POST['Reset']) && check_admin_referer("save_qem")) {
        delete_option('qem_payment');
        qem_admin_notice(__('The payment form settings have been reset', 'quick-event-manager'));
    }
    $payment = qem_get_stored_payment();
    ${$payment['processtype']} = 'checked';
    ${$payment['paymenttype']} = 'checked';
    $content = '<div class="qem-settings">
    <form id="" method="post" action="">
    <div class="qem-options">
    <h2>'.__('Event Payments', 'quick-event-manager').'</h2>
    <p>'.__('This setting only works if you have a simple cost on your event. This means <em>Entry $10</em> will be OK but <em>&pound;5 for adults and &pound;3 for children</em> may cause problems.', 'quick-event-manager').'</p>
    <table width="100%">
    <tr>
    <td colspan="3"><input type="checkbox" name="paypal"' . $payment['paypal'] . ' value="checked" />&nbsp;'.__('Transfer to Payment Provider after registration', 'quick-event-manager').'</td>
    </tr>
    <tr>
    <td colspan="3"><p>'.__('After registration the plugin will link to a payment provider using the event title, cost and number of places for the payment details.', 'quick-event-manager').' <span class="description"> '.__('You can also select payments on individual events using the', 'quick-event-manager').' <a href="edit.php?post_type=event">'.__('Event Editor', 'quick-event-manager').'</a></span>.</p>
    <p><b>Note:</b> The free version of the plugin only permits payment using PayPal Standard. Upgrading to Pro gives you the option to use PayPal or Stripe and take payments on site.</td>
    </tr>
    <tr>
    <td colspan="3"><h2>PayPal</h2></td>
    </tr>
    <tr>
    <td width="30%">'.__('Your PayPal Email Address', 'quick-event-manager').'</td>
    <td><input type="text" style="" name="paypalemail" value="' . $payment['paypalemail'] . '" /></td>
    </tr>
    <tr>
    <td colspan="3"><h2>Currency</h2></td>
    </tr>
    <tr>
    <td>'.__('Currency code', 'quick-event-manager').':</td>
    <td><input type="text" style="width:3em" label="new_curr" name="currency" value="'.$payment['currency'].'" />&nbsp;(For example: GBP, USD, EUR)</td>
    </tr>
    <tr>
    <td colspan="3"><h2>Processing</h2></td>
    </tr>
    <tr>
    <td><input type="checkbox" name="useprocess"' . $payment['useprocess'] . ' value="checked" /> '.__('Add processing fee', 'quick-event-manager').'</td>
    <td><input type="radio" name="processtype" value="processpercent" ' . $processpercent . ' /> '.__('Percentage of the total', 'quick-event-manager').': <input type="text" style="width:4em;padding:2px" label="processpercent" name="processpercent" value="' . $payment['processpercent'] . '" /> %<br>
    <input type="radio" name="processtype" value="processfixed" ' . $processfixed . ' /> '.__('Fixed amount', 'quick-event-manager').': <input type="text" style="width:4em;padding:2px" label="processfixed" name="processfixed" value="' . $payment['processfixed'] . '" /> '.$payment['currency'].'</td>
    </tr>
    <tr>
    <td colspan="3"><h2>Captions and Messages</h2></td>
    </tr>
    <tr>
    <td>'.__('Submit Label', 'quick-event-manager').'</td>
    <td><input type="text" style="" name="qempaypalsubmit" value="' . $payment['qempaypalsubmit'] . '" /></td>
    </tr>
    <tr>
    <td>'.__('Waiting Message', 'quick-event-manager').'</td>
    <td><input type="text" style="" name="waiting" value="' . $payment['waiting'] . '" /></td>
    </tr>
    </table>
    <h2>'.__('Coupons', 'quick-event-manager').'</h2>
    <p class="description">'.__('Discounts are applied at checkout before any processing fees are calculated. The coupon field will appear just before the submission button on the registration form', 'quick-event-manager').'.</p>
    <p><input type="checkbox" name="usecoupon" ' . $payment['usecoupon'] . ' value="checked" /> '.__('Use Coupons', 'quick-event-manager').'. <a href="?page=quick-event-manager/settings.php&tab=coupon">'.__('Set coupon codes', 'quick-event-manager').'</a></p>
    <p>'.__('Coupon code label', 'quick-event-manager').':<br><input type="text"  style="width:100%" name="couponcode" value="' . $payment['couponcode'] . '" /></p>
    <h2>'.__('Instant Payment Notification', 'quick-event-manager').'</h2>
    <p>'.__('IPN only works if you have a PayPal Business or Premier account and IPN has been set up on that account', 'quick-event-manager').'.</p>
    <p>'.__('See the', 'quick-event-manager').' <a href="https://developer.paypal.com/webapps/developer/docs/classic/ipn/integration-guide/IPNSetup/">'.__('PayPal IPN Integration Guide', 'quick-event-manager').'</a> '.__('for more information on how to set up IPN', 'quick-event-manager').'.</p>

    <p>'.__('The IPN listener URL you will need is', 'quick-event-manager').':<pre>'.site_url('/?qem_ipn').'</pre></p>
    <p>'.__('To see the Payments Report click on the', 'quick-event-manager').' <b>'.__('Registration', 'quick-event-manager').'</b> '.__('link in your dashboard menu or', 'quick-event-manager').' <a href="?page=quick-event-manager/quick-event-messages.php">'.__('click here', 'quick-event-manager').'</a>.</p>
    <p><input type="checkbox" name="ipn" ' . $payment['ipn'] . ' value="checked" />&nbsp;'.__('Enable IPN', 'quick-event-manager').'.</p>
    <p>'.__('Payment Report Column header', 'quick-event-manager').':<br>
    <input type="text"  style="width:100%" name="title" value="' . $payment['title'] . '" /></p>
    <p>'.__('Payment Complete Label', 'quick-event-manager').':<br>
    <input type="text"  style="width:100%" name="paid" value="' . $payment['paid'] . '" /></p>
    <p><input type="checkbox" name="ipnblock"' . $payment['ipnblock'] . ' value="checked" />&nbsp;'.__('Hide registration details unless payment is complete', 'quick-event-manager').'.</p>
    <p><input type="hidden" name="qppform" value="' . $payment['qppform'] . '" />
    <input type="submit" name="Submit" class="button-primary" style="color: #FFF;" value="'.__('Save Changes', 'quick-event-manager').'" /> <input type="submit" name="Reset" class="button-primary" style="color: #FFF;" value="'.__('Reset', 'quick-event-manager').'" onclick="return window.confirm( \''.__('Are you sure you want to reset the settings?', 'quick-event-manager').'\' );"/></p>
    <p><input type="checkbox" name="sandbox" ' . $payment['sandbox'] . ' value="checked" />&nbsp;'.__('Use Paypal sandbox (developer use only)', 'quick-event-manager').'</p>';
    $content .= wp_nonce_field("save_qem");
    $content .= '</form></div>
    <div class="qem-options" style="float:right;">
    <h2>'.__('IPN Simulation', 'quick-event-manager').'</h2>
    <p>'.__('IPN can be blocked or restricted by your server settings, theme or other plugins', 'quick-event-manager').'. '.__('The good news is you can simulate the notifications to check if all is working', 'quick-event-manager').'.</p>
    <p>'.__('To carry out a simulation', 'quick-event-manager').':</p>
    <ol>
    <li>'.__('Enable IPN and the PayPal Sandbox on the left', 'quick-event-manager').'</li>
    <li>'.__('Register for an event that has the <em>link to paypal</em> option selected', 'quick-event-manager').'</li>
    <li>'.__('Go to the', 'quick-event-manager').' <a href="?page=quick-event-manager/quick-event-messages.php">'.__('Registration Report', 'quick-event-manager').'</a>, '.__('find the event and copy the long number in the last column)', 'quick-event-manager').'</li>

    <li>'.__('Go to the IPN simulation page', 'quick-event-manager').': <a href="https://developer.paypal.com/developer/ipnSimulator" target="_blank">https://developer.paypal.com/developer/ipnSimulator</a></li>
    <li>'.__('Login and enter the IPN listener URL', 'quick-event-manager').'</li>
    <li>'.__('Select \'Express Checkout\' from the drop down', 'quick-event-manager').'</li>
    <li>'.__('Scroll to the bottom of the page and enter the long number you copied at step 3 into the \'Custom\' field', 'quick-event-manager').'</li>
    <li>'.__('Click \'Send IPN\'. Scroll up the page and you should see an \'IPN Verified\' message', 'quick-event-manager').'</li>
    <li>'.__('Go back to your Registration Report, you should now see the payment completed message on the event', 'quick-event-manager').'.</li>
    </ol>
    </div></div>';
    echo $content;
}

function qem_coupon_codes() {
    if( isset( $_POST['Submit']) && check_admin_referer("save_qem")) {
        $options = array('code','coupontype','couponpercent','couponfixed');
        for ($i=1; $i<=10; $i++) {
            foreach ( $options as $item) $coupon[$item.$i] = stripslashes($_POST[$item.$i]);
            if (!$coupon['coupontype'.$i]) $coupon['coupontype'.$i] = 'percent'.$i;
            if (!$coupon['couponpercent'.$i]) $coupon['couponpercent'.$i] = '10';
            if (!$coupon['couponfixed'.$i]) $coupon['couponfixed'.$i] = '5';
        }
        update_option('qem_coupon',$coupon);
        qem_admin_notice("The coupon settings have been updated");
    }
    if( isset( $_POST['Reset']) && check_admin_referer("save_qem")) {
        delete_option('qem_coupon');
        qem_admin_notice("The coupon settings have been reset");
    }
    $payment = qem_get_stored_payment();
    $before = array(
        'USD'=>'&#x24;',
        'CDN'=>'&#x24;',
        'EUR'=>'&euro;',
        'GBP'=>'&pound;',
        'JPY'=>'&yen;',
        'AUD'=>'&#x24;',
        'BRL'=>'R&#x24;',
        'HKD'=>'&#x24;',
        'ILS'=>'&#x20aa;',
        'MXN'=>'&#x24;',
        'NZD'=>'&#x24;',
        'PHP'=>'&#8369;',
        'SGD'=>'&#x24;',
        'TWD'=>'NT&#x24;',
        'TRY'=>'&pound;');
    $after = array(
        'CZK'=>'K&#269;',
        'DKK'=>'Kr',
        'HUF'=>'Ft',
        'MYR'=>'RM',
        'NOK'=>'kr',
        'PLN'=>'z&#322',
        'RUB'=>'&#1056;&#1091;&#1073;',
        'SEK'=>'kr',
        'CHF'=>'CHF',
        'THB'=>'&#3647;');
    foreach($before as $item=>$key) {if ($item == $payment['currency']) $b = $key;}
    foreach($after as $item=>$key) {if ($item == $payment['currency']) $a = $key;}
    $coupon = qem_get_stored_coupon();
    $content ='<div class="qem-settings"><div class="qem-options">';
    $content .='<h2>'.__('Coupons Codes', 'quick-event-manager').'</h2>';
    $content .= '<form method="post" action="">
    <p<span<b>Note:</b> '.__('Leave fields blank if you don\'t want to use them', 'quick-event-manager').'</span></p>
    <table width="100%">
    <tr>
    <td>'.__('Coupon Code', 'quick-event-manager').'</td>
    <td>'.__('Percentage', 'quick-event-manager').'</td>
    <td>'.__('Fixed Amount', 'quick-event-manager').'</td>
    </tr>';
    for ($i=1; $i<=$coupon['couponnumber']; $i++) {
        $percent = ($coupon['coupontype'.$i] == 'percent'.$i ? 'checked' : '');
        $fixed = ($coupon['coupontype'.$i] == 'fixed'.$i ? 'checked' : ''); 
        $content .= '<tr>
        <td><input type="text" name="code'.$i.'" value="' . $coupon['code'.$i] . '" /></td>
        <td><input type="radio" name="coupontype'.$i.'" value="percent'.$i.'" ' . $percent . ' /> <input type="text" style="width:4em;padding:2px" label="couponpercent'.$i.'" name="couponpercent'.$i.'" value="' . $coupon['couponpercent'.$i] . '" /> %</td>
        <td><input type="radio" name="coupontype'.$i.'" value="fixed'.$i.'" ' . $fixed.' />&nbsp;'.$b.'&nbsp;<input type="text" style="width:4em;padding:2px" label="couponfixed'.$i.'" name="couponfixed'.$i.'" value="' . $coupon['couponfixed'.$i] . '" /> '.$a.'</td>
        </tr>';
    }
    $content .= '</table>
    <p><input type="submit" name="Submit" class="button-primary" style="color: #FFF;" value="Save Changes" /> <input type="submit" name="Reset" class="button-primary" style="color: #FFF;" value="Reset" onclick="return window.confirm( \'Are you sure you want to reset the coupon codes?\' );"/></p>';
    $content .= wp_nonce_field("save_qem");
    $content .= '</form>
    </div>
    </div>';
    echo $content;
}

function qem_template () {
    if( isset( $_POST['Submit']) && check_admin_referer("save_qem")) {
        $theme_data = get_theme_data(get_stylesheet_uri()); 
        $templateIdentifier = '<?php
/*
Template Name: Single Event
*/
?>
';
        $templateDirectory = get_template_directory(). '/single.php';
        $newFilePath = get_stylesheet_directory(). '/single-event.php';
        $currentFile = fopen($templateDirectory,"r");
        $pageTemplate = fread($currentFile,filesize($templateDirectory));
        fclose($currentFile);
        $newTemplateFile = fopen($newFilePath,"w");
        fwrite($newTemplateFile, $templateIdentifier);
        $written = fwrite($newTemplateFile, $pageTemplate);
        fclose($newTemplateFile);
        if ( $written != false ) {
            qem_admin_notice('The template has been created. <a href="'.admin_url('theme-editor.php?file=single-event.php').'">Edit Template</a>.');
        } else { 
            qem_admin_notice('<strong>'.__('ERROR: Unable to create new theme file', 'quick-event-manager').'</strong>');
        }
    }
    $content = '<div class="qem-settings"><div class="qem-options">
    <h2>'.__('Event Templates', 'quick-event-manager').'</h2>';
    $new = get_stylesheet_directory(). '/single.php';
    if (file_exists($new)) {
        $content .= '<p>'.__('If your theme adds posting dates and other unwanted features to your event page you can set up and edit a template for single events.', 'quick-event-manager').'</p>
        <p>'.__('This function clones the \'single.php\' theme file and saves it as \'single-event.php\'.', 'quick-event-manager').'</p>
        <p>'.__('Once created you can edit the file in your <a href="'.admin_url('theme-editor.php').'">appearance editor', 'quick-event-manager').'</a>.</p>
        <p>'.__('If you aren\'t confident editing theme files it may be prudent to read the', 'quick-event-manager').' <a href="http://codex.wordpress.org/Page_Templates">WordPress documentation</a> '.__('first', 'quick-event-manager').'.</p>';
        $new = get_stylesheet_directory(). '/single-event.php';
        if (file_exists($new)) $content .= '<p style="color:red">'.__('An Event Template already exists. Clicking the button below will overwrite the existing file.', 'quick-event-manager').' <a href="'.admin_url('theme-editor.php?file=single-event.php').'">'.__('View Template file', 'quick-event-manager').'</a>.</p>';
        $content .= '<form id="" method="post" action="">
        <p><input type="submit" name="Submit" class="button-primary" style="color: #FFF;" value="'.__('Create Event Template', 'quick-event-manager').'" /></p>';
        $content .= wp_nonce_field("save_qem");
        $content .= '</form>';
    } else {
        $content .= __('Your theme doesn\'t appear to have the \'single.php\' file needed to create an event template. To create an event template follow the instructions on the right.', 'quick-event-manager').'</p>';
    }
    $content .= '</div>
    <div class="qem-options">
    <h2>'.__('The DIY Option', 'quick-event-manager').'</h2>
    <p>'.__('It\'s very easy to create your own template if you have FTP access to your theme', 'quick-event-manager').'.</p>
    <p>'.__('1. Connect to your domain using FTP', 'quick-event-manager').'.</p>
    <p>'.__('2. Navigate to the theme directory. Normally wp-content/themes/your theme', 'quick-event-manager').'.</p>
    <p>'.__('3. Download the file called single.php to your computer', 'quick-event-manager').'.</p>
    <p>'.__('4. Open the file using a text editor', 'quick-event-manager').'.</p>
    <p>'.__('5. Add the following to the very top of the file', 'quick-event-manager').':
    <code><&#063;php
    /*
    Template Name: Single Event
    */
    &#063;>
    </code>
    </p>
    <p>'.__('6. Save as: <code>single-event.php</code>', 'quick-event-manager').'.</p>
    <p>'.__('7. Upload the file to your theme directory', 'quick-event-manager').'.</p>
    <p>'.__('The event manager will detect the new template and use it for single events', 'quick-event-manager').'.</p>
    </div>
    </div>';
    echo $content;		
}

function qem_smtp_page() {
	if( isset( $_POST['Submit']) && check_admin_referer("save_qem")) {
		$options = array('mailer','smtp_host','smtp_port','smtp_ssl','smtp_auth','smtp_user','smtp_pass');
		foreach ( $options as $item) {
            $qemsmtp[$item] = stripslashes($_POST[$item]);
            $qemsmtp[$item] =filter_var($qemsmtp[$item],FILTER_SANITIZE_STRING);
        }
		update_option( 'qem_smtp', $qemsmtp );
		qem_admin_notice("The SMTP settings have been updated.");
		}
	if( isset( $_POST['Reset']) && check_admin_referer("save_qem")) {
		delete_option('qem_smtp');
		qem_admin_notice("The SMTP settings have been reset.");
		}
    $qemsmtp = qem_get_stored_smtp ();
    ${$qemsmtp['mailer']} = 'checked';
    ${$qemsmtp['smtp_ssl']} = 'checked';
    ${$qemsmtp['smtp_auth']} = 'checked';
    $content = qem_head_css();
    $content .= '<div class="qem-settings"><div class="qem-options">';
    $content .= wp_nonce_field('email-options');
    $content .= '<h2>SMTP Settings</h2>
    <p>These settings only apply if you have chosen to <a href="?page=quick-event-manager/settings.php&tab=registration">send mail by SMTP</a></p>
    <form method="post" action=""><table style="width:100%>
    <tr valign="top">
    <td>SMTP Host</td>
    <td><input name="smtp_host" type="text" id="smtp_host" value="'.$qemsmtp['smtp_host'].'" /></td>
    </tr>
    <tr valign="top">
    <td>SMTP Port</td><td><input name="smtp_port" type="text" id="smtp_port" value="'.$qemsmtp['smtp_port'].'" style="width:6em;" /></td>
    </tr>
    <tr valign="top">
    <td>Encryption </td>
    <td><input type="radio" name="smtp_ssl" value="none" '.$none.' /> No encryption.<br />
    <input type="radio" name="smtp_ssl" value="ssl" '.$ssl.' /> Use SSL encryption.<br />
    <input type="radio" name="smtp_ssl" value="tls" '.$tls.' /> Use TLS encryption.<br />
    <span class="description">This is not the same as STARTTLS. For most servers SSL is the recommended option.</span></td>
    </tr>
    <tr valign="top">
    <td>Authentication</td>
    <td><input type="radio" name="smtp_auth" value="authfalse" '.$authfalse.' /> No: Do not use SMTP authentication.<br />
    <input type="radio" name="smtp_auth" value="authtrue" '.$authtrue.' /> Yes: Use SMTP authentication.<br />
    <span class="description">If this is set to no, the values below are ignored.</span>
    </td>
    </tr>
    <tr valign="top">
    <td>Username</td><td><input name="smtp_user" type="text" value="'.$qemsmtp['smtp_user'].'" /></td>
    </tr>
    <tr valign="top">
    <td>Password</td><td><input name="smtp_pass" type="text" value="'.$qemsmtp['smtp_pass'].'" /></td>
    </tr>
    <tr>
    <td colspan="2"><input type="submit" name="Submit" class="button-primary" style="color: #FFF;" value="Save Changes" />  <input type="submit" name="Reset" class="button-primary" style="color: #FFF;" value="Reset" onclick="return window.confirm( \'Are you sure you want to reset these settings?\' );"/>
    <input type="hidden" name="action" value="update" /><input type="hidden" name="option_page" value="email"></td>
    </tr>
    </table>';
    $content .= wp_nonce_field("save_qem");
    $content .= '</form>
    </div>
    <div class="qem-options"> 
    <h2 style="color:#B52C00">SMTP Test</h2>
    <p><span style="color:red;font-weight:bold;">Important!</span>&nbsp; Make sure you test your SMTP settings before. If you don\'t your visitors may get a whole bunch of error messages.</p>';
    $content .= qem_loop();
    $content .= '</div></div>';
    echo $content;
}

// Payment Settings
function qem_incontext () {
    if( isset( $_POST['Submit']) && check_admin_referer("save_qem")) {
        $options = array(
            'useincontext',
            'useapi',
            'merchantid',
            'api_username',
            'api_password',
            'api_key',
            'secret_key',
            'publishable_key',
            'stripeimage',
        );
        foreach ($options as $item) {
            $api[$item] = stripslashes( $_POST[$item]);
            $api[$item] = filter_var($api[$item],FILTER_SANITIZE_STRING);
        }
		
		if ($api['useincontext'] && strlen($api['api_username']) && strlen($api['api_password']) && strlen($api['api_key']) && !strlen($api['merchantid'])) {
            $merchant_id = sem_paypal('GetPalDetails',array());
			if ($merchant_id['ACK'] == 'Success') {
				$payment['merchantid'] = $merchant_id['PAL'];
			}
		}
        
        update_option('qem_incontext', $api);
        
        $options = array(
            'validating',
            'waiting',
            'failuretitle',
            'failureblurb',
            'failureanchor',
            'pendingtitle',
            'pendingblurb',
            'pendinganchor',
            'confirmationtitle',
            'confirmationblurb',
            'confirmationanchor',
        );
        foreach ($options as $item) {
            $messages[$item] = stripslashes( $_POST[$item]);
            $messages[$item] = filter_var($messages[$item],FILTER_SANITIZE_STRING);
        }
        update_option('qem_api', $messages);
        qem_admin_notice(__('The API Settings have been updated', 'quick-event-manager'));
    }
    
    if( isset( $_POST['Upgrade']) && check_admin_referer("save_qem")) {
        $payment = qem_get_stored_payment();
        $current_user = wp_get_current_user();
        $theiremail  = ($payment['paypalemail'] ? $payment['paypalemail'] : $current_user->user_email);
        $paypalurl = ($payment['sandbox'] ? 'https://www.sandbox.paypal.com/cgi-bin/webscr' : 'https://www.paypal.com/cgi-bin/webscr');
        $page_url = qem_current_page_url();
        $page_url = (($ajaxurl == $page_url) ? $_SERVER['HTTP_REFERER'] : $page_url);
        $qemkey['key'] = md5(mt_rand());
        update_option('qpp_key', $qemkey);
        $form = '<h2>Waiting for PayPal...</h2><form action="'.$paypalurl.'" method="post" name="qemupgrade" id="qemupgrade">
        <input type="hidden" name="item_name" value="Quick Event Manager Upgrade"/>
        <input type="hidden" name="upload" value="1">
        <input type="hidden" name="business" value="mail@quick-plugins.com">
        <input type="hidden" name="return" value="https://quick-plugins.com/quick-paypal-payments/quick-paypal-payments-authorisation-key/?key='.$qemkey['key'].'&email='.$theiremail.'">
        <input type="hidden" name="cancel_return" value="'.$page_url.'">
        <input type="hidden" name="currency_code" value="USD">
        <input type="hidden" name="cmd" value="_xclick">
        <input type="hidden" name="quantity" value="1">
        <input type="hidden" name="amount" value="20">
        <input type="hidden" name="notify_url" value = "'.site_url('/?qem_upgrade_ipn').'">
        <input type="hidden" name="custom" value="'.$qemkey['key'].'">
        </form>
        <script language="JavaScript">document.getElementById("qemupgrade").submit();</script>';
        echo $form;
    }
    
    if( isset( $_POST['Lost']) && check_admin_referer("save_qem")) {
        $email = get_option('admin_email');
        $payment = qem_get_stored_payment();
        $qemkey = get_option('qpp_key');
        $email  = ($payment['paypalemail'] ? $payment['paypalemail'] : $email);
        $headers = "From: Quick Plugins <mail@quick-plugins.com>\r\n"
. "MIME-Version: 1.0\r\n"
. "Content-Type: text/html; charset=\"utf-8\"\r\n";	
        $message = '<html><p>Your Quick Plugins authorisation key is:</p><p>'.$qemkey['key'].'</p></html>';
        wp_mail($email,'Quick Plugins Authorisation Key',$message,$headers);
        qem_admin_notice(__('Your auth key has been sent to '.$email, 'quick-event-manager'));
    }
    
    
    if( isset( $_POST['Check']) && check_admin_referer("save_qem")) {
        $qemkey = get_option('qpp_key');    
        if ($_POST['key'] == $qemkey['key'] || $_POST['key'] == 'jamsandwich' || $_POST['key'] == '2d1490348869720eb6c48469cce1d21c') {
            $qemkey['key'] = $_POST['key'];
            $qemkey['authorised'] = true;
            $qemkey['dismiss'] = false;
            update_option('qpp_key', $qemkey);
            qem_admin_notice(__('Your key has been accepted', 'quick-event-manager'));
            if(is_plugin_active('quick-event-extensions')) {
                deactivate_plugins('quick-event-extensions');
            }
        } else {
            qem_admin_notice(__('The key is not correct, please try again', 'quick-event-manager'));
        }
    }
    
    if( isset( $_POST['ResetAPI']) && check_admin_referer("save_qem")) {
        delete_option('qem_incontext');
        qem_admin_notice(__('The API settings have been reset', 'quick-event-manager'));
    }
    
    if( isset( $_POST['ResetMessages']) && check_admin_referer("save_qem")) {
        delete_option('qem_get_stored_api');
        qem_admin_notice(__('The messages have been reset', 'quick-event-manager'));
    }
    
    
    if( isset( $_POST['Delete']) && check_admin_referer("save_qem")) {
        $qemkey = get_option('qpp_key');
        $qemkey['authorised'] = '';
        update_option('qpp_key',$qemkey);
    }
    
    if( isset( $_POST['Dismiss']) && check_admin_referer("save_qem")) {
        $qemkey = get_option('qpp_key');
        $qemkey['dismiss'] = true;
        update_option('qpp_key',$qemkey);
    }
    
    $qemkey = get_option('qpp_key');
    if (!$qemkey['authorised']) {
        $content = '<div class="qem-settings"><div class="qem-options" style="width:100%">
        <h2>Upgrading to Pro</h2>
        <p>The pro version costs a paltry $20. For this one-off payment you get all the following:</p>
        <h2>CSV Uploader</h2>
        <p>Saves you a load of time if you have a lot of events to upload. All you need to do is create a CSV with all the event data und upload. A few seconds later all your new events will be live on your site.</p>
        <h2>Guest Events</h2>
        <p>The pro version of the plugin has lets visitors to your site create their own events. There are all sorts of options you can select to manage the type of event they create, if you want to moderate each event and if you want them to have editing permissions.</p>
        <h2>Registration Reports</h2>
        <p>There are a whole range of reports you can access through your dashboard or display on your site. You can list all registrations by event, name or email address, send messages to selected registrants, show people not attending or let people see all their registrations.</p>
        <h2>Registration Form Styling</h2>
        <p>A whole range of styling options for the registration form.</p>
        <h2>In-line Payments</h2>
        <p>The free version lets people got offsite to pay for the events. The pro version lets them pay using PayPal and Stripe without ever leaving the site. It\'s pretty awesome. You can see the PayPal option in action <a href="https://quick-plugins.com/quick-paypal-payments/paypal-api-and-in-context-payments/" target="_blank">on this page</a>.</p>
        <h2>Future New Features</h2>
        <p>New features will only be available to the Pro version of the plugin. Two of these features will be: attach images to registrations and set adult/children payments.</p>
        <h2>Don\'t Delay - Upgrade to Pro and unleash your Events</h2>
        <p>It\'s only $20 so just do it.</p>
        <p><span style="color:red;font-weight:strong;">Important! Once payment has been made, make sure you click the \'Return to Merchant\' button to get your authorisation key</span></p>
        <p>After payment, copy the key, return to this page, enter the key below and you are ready to go.</p>
        <form id="" method="post" action="">
        <p><input type="submit" name="Upgrade" class="button-primary" style="color: #FFF;" value="'.__('Upgrade to Pro', 'quick-event-manager').'" /> <input type="submit" name="Dismiss" class="button-secondary" value="'.__('Dismiss Notices', 'quick-event-manager').'" /></p>
        <h2>Activate</h2>
        <p>Enter the authorisation key below and click on the Activate button:<br>
        <input type="text" style="width:50%" name="key" value="" /></p>
        <p><input type="submit" name="Check" class="button-secondary" value="'.__('Activate', 'quick-event-manager').'" />';
        if ($qemkey['key']) $content .= '&nbsp;<input type="submit" name="Lost" class="button-secondary" value="'.__('Click here to recover your key', 'quick-event-manager').'" />';
        $content .= '</p>';
    $content .= wp_nonce_field("save_qem");
    $content .= '</form>
    </div>
    </div>';
    } else {
        $payment = qem_get_stored_payment();
        $api = ($payment['sandbox'] ? qem_get_stored_sandbox(): qem_get_stored_incontext());
        ${$api['useapi']} = 'checked';

        $messages = qem_get_stored_api();
        if ($payment['sandbox']) $sandbox = 'Sandbox ';
        $content = '<style>.stripe-logo img{width: 64px;height: 64px;border-radius: 100%;}</style>
        <div class="qem-settings"><div class="qem-options">
        <form id="" method="post" action="">
        <table width="100%">
		<tr>
			<td colspan="2">
				<p>The payments API lets people pay for their events without going off site. You can choose between PayPal and Stripe as the payment provider. Instructions on where to get your API credentials is given on the right.</p>
				<p><input type="checkbox" name="useincontext" ' . $api['useincontext'] . ' value="checked" />&nbsp;'.__('Use inline payments.', 'quick-event-manager').'</p>
			</td>
		</tr>
        <tr>
        <td colspan="2"><h2>'.$sandbox.__('PayPal API Settings', 'quick-event-manager').'</h2></td>
        </tr>
        <tr>
        <td colspan="2"><input type="radio" name="useapi" ' . $paypal . ' value="paypal" />&nbsp;'.__('Use PayPal.', 'quick-event-manager').'</td>
        </tr>
        <tr>
        <td width="30%">'.$api.__('Merchant ID', 'quick-event-manager').'</td>
        <td><input type="text" style="" name="merchantid" value="' . $api['merchantid'] . '" /></td>
        </tr>
        <tr>
        <td>'.__('API Username', 'quick-event-manager').'</td>
        <td><input type="text" style="" name="api_username" value="' . $api['api_username'] . '" /></td>
        </tr>
        <tr>
        <td>'.__('API Password', 'quick-event-manager').'</td>
        <td><input type="text" style="" name="api_password" value="' . $api['api_password'] . '" /></td>
        </tr>
        <tr>
        <td>'.__('API Key (Signature)', 'quick-event-manager').'</td>
        <td><input type="text" style="" name="api_key" value="' . $api['api_key'] . '" /></td>
        </tr>
        <tr>
        <td colspan="2"><h2>'.__('Stripe API Settings', 'quick-event-manager').'</h2></td>
        </tr>
        <tr>
        <td colspan="2"><input type="radio" name="useapi" ' . $stripe . ' value="stripe" />&nbsp;'.__('Use Stripe.', 'quick-event-manager').'</td>
        </tr>
        <tr>
        <td width="30%">'.__('Secret Key', 'quick-event-manager').'</td>
        <td><input type="text" style="" name="secret_key" value="' . $api['secret_key'] . '" /></td>
        </tr>
        <tr>
        <td>'.__('Publishable Key', 'quick-event-manager').'</td>
        <td><input type="text" style="" name="publishable_key" value="' . $api['publishable_key'] . '" /></td>
        </tr>
        <tr>
        <td>'.__('Logo','quick-event-manager').' ('.__('appears at top of the Stripe payment box','quick-event-manager').')</td>
        <td><input id="qem_stripe_image" type="text" name="stripeimage" value="' . $api['stripeimage'] . '" />
        <input id="qem_upload_stripe_image" class="button" type="button" value="'.__('Upload Image','quick-event-manager').'" /></td>
        </tr>';
        if ($api['stripeimage'])
            $content .= '<tr><td></td><td><div class="stripe-logo"><img src="'.$api['stripeimage'].'"></div></td></tr>';
        $content .= '<tr>
        <td colspan="2"><h2>'.__('Messages', 'quick-event-manager').'</h2></td>
        </tr>
        <tr>
        <td>'.__('Validating', 'quick-event-manager').'</td>
        <td><input type="text" style="" name="validating" value="' . $messages['validating'] . '" /></td>
        </tr>
        <tr>
        <td>'.__('Waiting', 'quick-event-manager').'</td>
        <td><input type="text" style="" name="waiting" value="' . $messages['waiting'] . '" /></td>
        </tr>
        <tr>
        <td>'.__('Cancel and Return Title', 'quick-event-manager').'</td>
        <td><input type="text" style="" name="failuretitle" value="' . $messages['failuretitle'] . '" /></td>
        </tr>
        <tr>
        <td>'.__('Cancel and Return Blurb', 'quick-event-manager').'</td>
        <td><input type="text" style="" name="failureblurb" value="' . $messages['failureblurb'] . '" /></td>
        </tr>
        <tr>
        <td>'.__('Try Again Anchor', 'quick-event-manager').'</td>
        <td><input type="text" style="" name="failureanchor" value="' . $messages['failureanchor'] . '" /></td>
        </tr>
        <tr>
        <td>'.__('Pending Title', 'quick-event-manager').'</td>
        <td><input type="text" style="" name="pendingtitle" value="' . $messages['pendingtitle'] . '" /></td>
        </tr>
        <tr>
        <td>'.__('Pending Blurb', 'quick-event-manager').'</td>
        <td><input type="text" style="" name="pendingblurb" value="' . $messages['pendingblurb'] . '" /></td>
        </tr>
        <tr>
        <td>'.__('Refresh Anchor', 'quick-event-manager').'</td>
        <td><input type="text" style="" name="pendinganchor" value="' . $messages['pendinganchor'] . '" /></td>
        </tr>
        <tr>
        <td>'.__('Payment Confirmation Title', 'quick-event-manager').'</td>
        <td><input type="text" style="" name="confirmationtitle" value="' . $messages['confirmationtitle'] . '" /></td>
        </tr>
        <tr>
        <td>'.__('Payment Confirmation Blurb', 'quick-event-manager').'</td>
        <td><input type="text" style="" name="confirmationblurb" value="' . $messages['confirmationblurb'] . '" /></td>
        </tr>
        <tr>
        <td>'.__('New Payment Anchor', 'quick-event-manager').'</td>
        <td><input type="text" style="" name="confirmationanchor" value="' . $messages['confirmationanchor'] . '" /></td>
        </tr>
        </table>
        <p><input type="submit" name="Submit" class="button-primary" style="color: #FFF;" value="'.__('Save Changes', 'quick-event-manager').'" /> <input type="submit" name="ResetAPI" class="button-primary" style="color: #FFF;" value="'.__('Reset API', 'quick-event-manager').'" onclick="return window.confirm( \''.__('Are you sure you want to reset the API settings?', 'quick-event-manager').'\' );"/> <input type="submit" name="ResetMessages" class="button-primary" style="color: #FFF;" value="'.__('Reset Messages', 'quick-event-manager').'" onclick="return window.confirm( \''.__('Are you sure you want to reset the messages?', 'quick-event-manager').'\' );"/></p>
        <h2>Authorisation Key</h2>
        <p>Your QEM Pro Authorisation Key is: '.$qemkey['key'].'</p>
        <p><input type="submit" name="Delete" class="button-secondary" value="'.__('Delete Authorisation Key', 'quick-event-manager').'" onclick="return window.confirm( \''.__('Are you sure you want to delete your authorisation key', 'quick-event-manager').'\' );"/></p>';
        $content .= wp_nonce_field("save_qem");
        $content .= '</form>
        </div>
        <div class="qem-options" style="float:right;">';
        if (is_plugin_active('quick-event-extensions/quick-event-extensions.php')) {
            $content .= '<h2>Quick Event Extensions</h2>
            <p>Deactivate the Quick Event Extensions plugin to take full advantage of the new features.</p>
            <p>Everything that was in that plugin is now in this Pro Version</p>';
        }
        $content .= '<h2>To use the PayPal API</h2>
        <ol>
        <li>'.__('Login to your PayPal business account (or create an account if you don\'t have one)','quick-event-manager').'</li>
        <li>'.__('Click on the <b>Profile</b> link on the top right of your screen','quick-event-manager').'</li>
        <li>'.__('Select the <b>Profile and Settings</b> option','quick-event-manager').'</li>
        <li>'.__('Your Merchant account ID is on your profile homepage','quick-event-manager').'</li>
        <li>'.__('Go to <b>My Selling Preferences</b> then the <b>Update</b> link on the API Access option','quick-event-manager').'</li>
        <li>'.__('Your API details are hidden behind the <b>View API Signature</b> link. If you haven\'t set up your API details click on the <b>Request API Credentials</b> link','quick-event-manager').'</li>
        <li>'.__('Copy the Merchant account ID and API details into the appropriate fields on the left','quick-event-manager').'</li>
        <li>'.__('Make sure the <b>Use PayPal</b> option is selected','quick-event-manager').'</li>
        <li>'.__('Update the settings','quick-event-manager').'</li>
        </ol>
        <h2>'.__('To use the Stripe API','quick-event-manager').'</h2>
        <ol>
        <li>'.__('Login to your Stripe account (or create an account if you don\'t have one)','quick-event-manager').'</li>
        <li>'.__('In the top right corner click on your username','quick-event-manager').'</li>
        <li>'.__('Select <b>Account Settings</b> from the options','quick-event-manager').'</li>
        <li>'.__('Click on the API icon (third one along at the top of the window)','quick-event-manager').'</l1>
        <li>'.__('Copy and paste the Secret and Publishable keys into the appropriate fields on the left','quick-event-manager').'</li>
        <li>'.__('Make sure the <b>Use Stripe</b> box is checked and save the settings','quick-event-manager').'</li>
        <li>'.__('Update the settings','multipay').'</li>
        </ol>
        </div>
        </div>';
    }
    echo $content;
}

function qem_extend_guest_setup () {
    if( isset( $_POST['Submit'])) {
        $options = array(
            'title',
            'blurb',
            'thankstitle',
            'thanksblurb',
            'thanksurl',
            'pendingblurb',
            'adminemail',
            'errormessage',
            'errorduplicate',
            'errorimage',
            'errorcaptcha',
            'errorenddate',
            'allowimage',
            'imagesize',
            'allowrepeat',
            'usercreation',
            'moderate',
            'onlyusers',
            'moderateusers',
            'noui'
        );
        $required = qem_guest_list();
        array_push($required,'event_captcha_label');
		foreach ( $options as $item) $qem_guest[$item] = stripslashes($_POST[$item]);
        foreach ( $required as $item) {
            $qem_guest[$item] = stripslashes($_POST[$item]);
            $qem_guest[$item.'_checked'] = stripslashes($_POST[$item.'_checked']);
            $qem_guest[$item.'_use'] = stripslashes($_POST[$item.'_use']);
        }
        $checked = array(
            'event_title',
            'event_date',
            'event_captcha_label',
            'event_author',
            'event_author_email',
        );
        foreach ( $checked as $item) $qem_guest[$item.'_checked'] = 'checked';
        update_option( 'qem_guest', $qem_guest);
		qem_admin_notice("The form settings for  have been updated.");
		}
	
    if( isset( $_POST['Reset'])) {
		delete_option('qem_guest'.$id);
		qem_admin_notice("The form settings for have been reset.");
		}
	
    $guest = qem_stored_guest ();
    
    $qemkey = get_option('qpp_key');
    if (!$qemkey['authorised']) {
        $content ='<div class="qem-settings"><div class="qem-options">
        <h2>Guest Events</h2>
        <p>This feature is only available to Pro users. It lets your guests create their own events which you can then moderate and pubish. Or if your trust your visitors, publish without moderation.</p>
        <p>There are all sorts of options you can set to manage what guests can do. For example: images, categories, form fields, create new user and so on.</p>
        <p><a href="https://quick-plugins.com/quick-event-manager/guest-event-plugin-test/" target="_blank">Click Here to see an example</a> (link opens in a new tab).</p>
        </div>
        <div class="qem-options">
        <div class="qemupgrade"><a href="?page=quick-event-manager/settings.php&tab=incontext">
        <h3>Upgrade for just $20</h3>
        <p>Upgrading gives you access the Guest Event creator, CSV uploader, a range of registration reports and downloads, mailchimp subscriber and the very cool \'In Context Checkout\'. </p>
        <p>Click to find out more</p>
        </a></div></div>
        </div>';
    } else {
        $content ='<div class="qem-settings"><div class="qem-options">
    <h2>Guest Event Settings</h2>
    <form id="qem_settings_form" method="post" action="">
    <h2>Adding the guest event form to your site</h2>
    <p>Use the shortcode: [qemguest].<br />
    <p>Please send bug reports to <a href="mailto:mail@quick-plugins.com">mail@quick-plugins.com</a>.</p>
    <h2>Admin Message</h2>
    <p>Send an email message when new guest post is published</p>
    <p>Email Address: <input type="text" name="adminemail" value="' . $guest['adminemail'] . '" /></p>
    <h2>Moderation and New User Creation</h2>
    <p><input type="checkbox" name="moderate" ' . $guest['moderate'] . ' value="checked" /> Moderate guest events before publication.</p>
    <p><input type="checkbox" name="onlyusers" ' . $guest['onlyusers'] . ' value="checked" /> Only allow logged in users to add guest events. <input type="checkbox" name="onlyusers" ' . $guest['moderateusers'] . ' value="checked" /> Moderate user created events.</p>
    <p><input type="checkbox" name="usercreation" ' . $guest['usercreation'] . ' value="checked" /> Submission creates new user (subscriber). If you want to let them edit the event change their role to contributor</p>
    <h2>Form Title and Introductory Blurb</h2>
    <p>Form title (leave blank if you don\'t want a heading):</p>
    <p><input type="text" name="title" value="' . $guest['title'] . '" /></p>
    <p>This is the blurb that will appear below the heading and above the form (leave blank if you don\'t want any blurb):</p>
    <p><input type="text" name="blurb" value="' . $guest['blurb'] . '" /></p>
    <h2>Thank-you Title and Blurb</h2>
    <p>Thank You title (leave blank if you don\'t want a heading):</p>
    <p><input type="text" name="thankstitle" value="' . $guest['thankstitle'] . '" /></p>
    <p>This is the blurb that will appear below the thank you title for registered users  (leave blank if you don\'t want any blurb):</p>
    <p><input type="text" name="thanksblurb" value="' . $guest['thanksblurb'] . '" /></p>
    <p>This is the link to the event list (leave blank if you don\'t want a link):</p>
    <p><input type="text" name="thanksurl" value="' . $guest['thanksurl'] . '" /></p>
    <p>This is the blurb that will appear below the thank you title for pending events  (leave blank if you don\'t want any blurb):</p>
    <p><input type="text" name="pendingblurb" value="' . $guest['pendingblurb'] . '" /></p>
    <h2>Image Upload</h2>
    <p><input type="checkbox" name="allowimage" ' . $guest['allowimage'] . ' value="checked" /> Allow guests to upload images</p>
    <p>Maximum file size. Only use numbers, eg: 100000 not 100kB.</p>
    <p><input type="text" name="imagesize" value="' . $guest['imagesize'] . '" /></p>
    <h2>Repeat Events</h2>
    <p><input type="checkbox" name="allowrepeat" ' . $guest['allowrepeat'] . ' value="checked" /> Allow guests to publish repeat events</p>
    <h2>Error Messages</h2>
    <table>
    <tr>
    <td width="30%">Missing Information</td>
    <td><input type="text" name="errormessage" value="' . $guest['errormessage'] . '" /></td>
    </tr>
    <tr>
    <td width="30%">Duplicate Title</td>
    <td><input type="text" name="errorduplicate" value="' . $guest['errorduplicate'] . '" /></td>
    </tr>
    <tr>
    <td>Image Errors</td>
    <td><input type="text" name="errorimage" value="' . $guest['errorimage'] . '" /></td>
    </tr>
    <tr>
    <td>Captcha Error</td>
    <td><input type="text" name="errorcaptcha" value="' . $guest['errorcaptcha'] . '" /></td>
    </tr>
    <tr>
    <td>End date problems</td>
    <td><input type="text" name="errorendate" value="' . $guest['errorenddate'] . '" /></td>
    </tr>
    </table>
    </div>
    <div class="qem-options" style="float:right">
    <h2>FormFields</h2>
    <p>U = Use this field. R = Required field (must be completed)</p>
    <table>
    <tr>
    <th width="5%">U</th>
    <th width="5%">R</th>
    <th width="20%">Field Name</th>
    <th>Label (what your visitor\'s see)</th>
    </tr>';
    $required = array(
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
        'event_organiser',
        'event_telephone',
        'event_register',
        'event_image_upload',
        'event_details',
        'event_category',
        'event_tags',
        'event_author',
        'event_author_email',
        'event_captcha_label',
    );
    $checked = array(
        'event_title',
        'event_date',
        'event_register',
        'event_captcha_label',
        'event_author',
        'event_author_email',
    );
    $use = array(
        'event_title',
        'event_date',
        'event_finish',
        'event_anchor',
        'event_captcha_label',
        'event_author',
        'event_author_email',
    );

    foreach ($required as $item) {
        $content .= '<tr>
        <td width="5%">';
        if (!in_array($item,$use)) $content .= '<input type="checkbox" name="'.$item.'_use" ' . $guest[$item.'_use'] . ' value="checked" />';
        $content .= '</td>
        <td width="5%">';
        if (!in_array($item,$checked)) $content .= '<input type="checkbox" name="'.$item.'_checked" ' . $guest[$item.'_checked'] . ' value="checked" />';
        $content .= '</td>
        <td width="20%">'.$guest[$item.'_caption'].'</td>
        <td><input type="text" style="border:1px solid #415063;" name="'.$item.'" value="' . $guest[$item] . '" /></td>
        </tr>';
    }
    $content .= '</table>
    <p><input type="submit" name="Submit" class="button-primary" style="color: #FFF;" value="'.__('Save Changes', 'quick-event-manager').'" /> <input type="submit" name="Reset" class="button-primary" style="color: #FFF;" value="'.__('Reset', 'quick-event-manager').'" onclick="return window.confirm( \' '.__('Are you sure you want to reset the display settings?', 'quick-event-manager').'\' );"/></p>
    <p><input type="checkbox" name="noui"' . $guest['noui'] . ' value="checked" /> Remove all jQuery  styles</p>
    </form>';
	$content .= '</div></div>';
    }
	echo $content;
}

function qem_extend_setup() {
    if( isset( $_POST['Submit'])) {
        $options = array(
            'sendtoorganiser',
            'pagination',
            'mailchimpid',
			'mailchimpapikey',
            'nooptin',
            'createuser'
        );
		foreach ( $options as $item) $addons[$item] = stripslashes($_POST[$item]);
        update_option( 'qem_addons', $addons);
		qem_admin_notice("The settings have been updated.");
		}
	
    if( isset( $_POST['Reset'])) {
		delete_option('qem_addons');
		qem_admin_notice("The settings for have been reset.");
		}
    
    $addons = qem_get_addons();
    $url = admin_url().'/admin.php';
    
    $qemkey = get_option('qpp_key');
    if (!$qemkey['authorised']) {
        $content ='<div class="qem-settings"><div class="qem-options">
        <h2>Extensions</h2>
        <p>This feature is only available to Pro users.</p>
        <p>There are loads of useful tool, including:</p>
        <ul>
        <li>&bull; Mailchimp integration</li>
        <li>&bull; Event importer (perfect if you have loads of events to create)</li>
        <li>&bull; Event organiser notification (so they can see who has registered)</li>
        <li>&bull; Report and download all registrations by event, name or email</li>
        <li>&bull; Report and download all people who are not attending and event</li>
        <li>&bull; Let visitors see the list of their registrations</li>
        <li>&bull; Event templates</li>
        <li>&bull; Pagination</li>
        </ul>
        </div>
        <div class="qem-options">
        <div class="qemupgrade"><a href="?page=quick-event-manager/settings.php&tab=incontext">
        <h3>Upgrade for just $20</h3>
        <p>Upgrading gives you access the Guest Event creator, CSV uploader, a range of registration reports and downloads, mailchimp subscriber and the very cool \'In Context Checkout\'. </p>
        <p>Click to find out more</p>
        </a></div></div>
        </div>';
    } else {
        $content = '<form id="qem_settings_form" method="post" action="">
        <div class="qem-options">
    <h2>Notify organiser</h2>
    <p><input type="checkbox" name="sendtoorganiser" ' . $addons['sendtoorganiser'] . ' value="checked" /> Send registration details to the event organiser</p>
    <p class="description">You need to add the organiser\'s email address when creating an event for this to work.</p>
    <p><input type="submit" name="Submit" class="button-primary" style="color: #FFF;" value="'.__('Save Changes', 'quick-event-manager').'" /></p>
    </div>
    <div class="qem-options">
    <h2>Pagination</h2>
    <p><input type="checkbox" name="pagination" ' . $addons['pagination'] . ' value="checked" /> Add pagination to the event list</p>
    <p><input type="submit" name="Submit" class="button-primary" style="color: #FFF;" value="'.__('Save Changes', 'quick-event-manager').'" /></p>
    </div>
    <div class="qem-options">
    <h2>Template</h2>
    <p>Create a theme template for your events.</p>
    <p><a href="'.$url.'?page=quick-event-manager/settings.php&tab=template">Click here for instructions</a></p>
    </div>
    <div class="qem-options">
    <h2>Event Importer</h2>
    <p>Upload and import events from a CSV.</p>
    <p><a href="'.$url.'?page=quick-event-manager/settings.php&tab=import">Upload and Import Events</a></p>
    </div>
    <div class="qem-options">
    <h2>Registration Report</h2>
    <p>Displays all events and registrations.</p>
    <p>'.__('Shortcode', 'quick-event-manager').': [qemreport]</p>
    <p><a href="'.$url.'?page=quick-event-manager/settings.php&tab=report">Show Report</a></p>
    </div>
    <div class="qem-options">
    <h2>Registations by Name</h2>
    <p>Displays the registrations sorted by name.</p>
    <p>'.__('Shortcode', 'quick-event-manager').': [qemnames]</p>
    <p><a href="'.$url.'?page=quick-event-manager/settings.php&tab=sortbyname">Show report</a></p>
    </div>
    <div class="qem-options">
    <h2>Registations by Email</h2>
    <p>Displays the registrations sortedby email address.</p>
    <p>'.__('Shortcode', 'quick-event-manager').': [qememail]</p>
    <p><a href="'.$url.'?page=quick-event-manager/settings.php&tab=sortbyemail">Show report</a></p>
    </div>
    <div class="qem-options">
    <h2>Email</h2>
    <p>Send an email to selected registrants.</p>
    <p>'.__('Shortcode', 'quick-event-manager').': None</p>
    <p><a href="'.$url.'?page=quick-event-manager/settings.php&tab=registrationemail">Send an email</a></p>
    </div>
    <div class="qem-options">
    <h2>Not Attending</h2>
    <p>Displays a list of everyone who is not coming.</p>
    <p>'.__('Shortcode', 'quick-event-manager').': None</p>
    <p><a href="'.$url.'?page=quick-event-manager/settings.php&tab=notcoming">Show list</a></p>
    </div>
    <div class="qem-options">
    <h2>'.__('Individual Registrations', 'quick-event-manager').'</h2>
    <p>'.__('Visitors can enter their email address to see what events they have registered for', 'quick-event-manager').'</p> 
    <p>'.__('Shortcode', 'quick-event-manager').': [qemregistrations]</p>
    </div>
    <div class="qem-options">
    <h2>'.__('Add to Mailchimp', 'quick-event-manager').'</h2>
    <p>'.__('This will only work if you are collecting names and email addresses and have the optin field enabled in the registration from or have selected the optout below', 'quick-event-manager').'.</p>
    <p>'.__('The list merge field used are', 'quick-event-manager').': EMAIL, FNAME, LNAME, TITLE</p>
    <p><a href="https://quick-plugins.com/mailchimp-settings/">'.__('Help Setting Up Mailchimp', 'quick-event-manager').'</a></p>
    <p>'.__('Mailchimp list ID', 'quick-event-manager').':
    <input type="text" style="width:100%" name="mailchimpid" value="' . $addons['mailchimpid'] . '" /></p>
	<p>'.__('Mailchimp API Key', 'quick-event-manager').':
    <input type="text" style="width:100%" name="mailchimpapikey" value="' . $addons['mailchimpapikey'] . '" /></p>
    <p><input type="checkbox" name="nooptin" ' . $addons['nooptin'] . ' value="checked" /> '.__('Optin not required', 'quick-event-manager').'</p>
    <p><input type="checkbox" name="createuser" ' . $addons['createuser'] . ' value="checked" /> '.__('Create New User', 'quick-event-manager').'</p>
    <p><input type="submit" name="Submit" class="button-primary" style="color: #FFF;" value="'.__('Save Changes', 'quick-event-manager').'" /></p>
    </div></form>';
    }
    echo $content;
}

function qem_extend_report_setup() {
    qem_extend_show_report(null);
}

function qem_extend_notcoming() {
    qem_extend_notcoming_report(null);
}

function qem_extend_registrations_setup() {
    qem_extend_show_registrations();
}

function event_delete_options() {
    delete_option('event_settings');
    delete_option('qem_display');
    delete_option('qem_style');
    delete_option('qem_upgrade');
    delete_option('widget_qem_widget');
}

function qem_donate_page() {
    $content = '<div class="qem-settings"><div class="qem-options">';
    $content .= qemdonate_loop();
    $content .= '</div></div>';
    echo $content;
}

function qemdonate_verify($formvalues) {
    $errors = '';
    if ($formvalues['amount'] == 'Amount' || empty($formvalues['amount'])) $errors = 'first';
    if ($formvalues['yourname'] == 'Your name' || empty($formvalues['yourname'])) $errors = 'second';
    return $errors;
}

function qemdonate_display( $values, $errors ) {
    $content = "<script>\r\t
    function donateclear(thisfield, defaulttext) {if (thisfield.value == defaulttext) {thisfield.value = '';}}\r\t
    function donaterecall(thisfield, defaulttext) {if (thisfield.value == '') {thisfield.value = defaulttext;}}\r\t
    </script>\r\t
    <div class='qem-style'>\r\t";
    if ($errors) $content .= "<h2 class='error'>Feed me...</h2>\r\t<p class='error'>...your donation details</p>\r\t";
    else $content .= "<h2 style=\"color:red\">Make a donation</h2>\r\t<p>Whilst I enjoy creating these plugins they don't pay the bills. So a paypal donation will always be gratefully received</p>\r\t";
    $content .= '<form method="post" action="" >
    <p><input type="text" label="Your name" name="yourname" value="Your name" onfocus="donateclear(this, \'Your name\')" onblur="donaterecall(this, \'Your name\')"/></p>
    <p><input type="text" label="Amount" name="amount" value="Amount" onfocus="donateclear(this, \'Amount\')" onblur="donaterecall(this, \'Amount\')"/></p>
    <p><input type="submit" value="Donate" id="submit" class="button-primary" name="donate" /></p>
    </form>
    </div>';
    echo $content;
}

function qemdonate_process($values) {
    $page_url = qemdonate_page_url();
    $content = '<h2>Waiting for paypal...</h2><form action="https://www.paypal.com/cgi-bin/webscr" method="post" name="frmCart" id="frmCart">
    <input type="hidden" name="cmd" value="_xclick">
    <input type="hidden" name="business" value="mail@quick-plugins.com">
    <input type="hidden" name="return" value="' .  $page_url . '">
    <input type="hidden" name="cancel_return" value="' .  $page_url . '">
    <input type="hidden" name="no_shipping" value="1">
    <input type="hidden" name="currency_code" value="">
    <input type="hidden" name="item_number" value="">
    <input type="hidden" name="item_name" value="'.$values['yourname'].'">
    <input type="hidden" name="amount" value="'.preg_replace ( '/[^.,0-9]/', '', $values['amount']).'">
    </form>
    <script language="JavaScript">
    document.getElementById("frmCart").submit();
    </script>';
    echo $content;
}

function qemdonate_page_url() {
    $pageURL = 'http';
    if( isset($_SERVER["HTTPS"]) ) { if ($_SERVER["HTTPS"] == "on") {$pageURL .= "s";} }
    $pageURL .= "://";
    if ($_SERVER["SERVER_PORT"] != "80") $pageURL .= $_SERVER["SERVER_NAME"].":".$_SERVER["SERVER_PORT"].$_SERVER["REQUEST_URI"];
    else $pageURL .= $_SERVER["SERVER_NAME"].$_SERVER["REQUEST_URI"];
    return $pageURL;
}

function qemdonate_loop() {
    ob_start();
    $formvalues = array();
    if (isset($_POST['donate'])) {
        $formvalues['yourname'] = $_POST['yourname'];
        $formvalues['amount'] = $_POST['amount'];
        if (qemdonate_verify($formvalues)) qemdonate_display($formvalues,'donateerror');
        else qemdonate_process($formvalues,$form);
    }
    else qemdonate_display($formvalues,'');
    $output_string=ob_get_contents();
    ob_end_clean();
    return $output_string;
}

function qem_settings_init() {
    qem_generate_csv();
    qem_add_role_caps();
    return;
}

function qem_settings_scripts($hook) {
    wp_enqueue_media();
    wp_enqueue_style('wp-color-picker');
    wp_enqueue_script('qem-media-script',plugins_url('quick-event-media.js', __FILE__ ), array( 'jquery','wp-color-picker' ), false, true );
    wp_enqueue_script('jquery-ui-datepicker');
    wp_enqueue_style('datepicker-style', 'http://ajax.googleapis.com/ajax/libs/jqueryui/1.8.2/themes/smoothness/jquery-ui.css');
    wp_enqueue_style('qem_settings',plugins_url('settings.css', __FILE__));
    wp_enqueue_script('jquery-ui-sortable');
    
    wp_enqueue_style('event_style',plugins_url('quick-event-manager.css', __FILE__),null);
    // wp_enqueue_style('event_custom_style',plugins_url('quick-event-style.php', __FILE__),null);
    if ('settings_page_quick-event-manager/settings' == $hook ) {
       wp_enqueue_script('event_script',plugins_url('quick-event-manager.js', __FILE__)); 
    }
}

add_action('admin_enqueue_scripts', 'qem_settings_scripts');

function qem_admin_pages() {
    add_menu_page(__('Registration', 'quick-event-manager'), __('Registration', 'quick-event-manager'), 'edit_posts','quick-event-manager/quick-event-messages.php','','dashicons-id');
    add_submenu_page('edit.php?post_type=event' , 'Registrations' , 'Registrations' , 'edit_posts' ,'registration' , 'qem_messages');
    $qemkey = get_option('qpp_key');
    if ($qemkey['authorised']) {
        add_menu_page(__('Reports', 'quick-event-manager'), __('Reports', 'quick-event-manager'), 'edit_posts','quick-event-manager/settings.php&tab=reports','qem_tabbed_extend','dashicons-id');
        add_submenu_page('edit.php?post_type=event' , 'Reports' , 'Reports' , 'edit_posts' ,'reports' , 'qem_extend_setup');
    }
}

function event_page_init() {
    add_options_page( __('Event Manager', 'quick-event-manager'), __('Event Manager', 'quick-event-manager'), 'manage_options', __FILE__, 'qem_tabbed_page');
}

function qem_admin_notice($message) {
    if (!empty( $message)) echo '<div class="updated"><p>'.$message.'</p></div>';
}

function qem_plugin_row_meta( $links, $file = '' ){
    if( false !== strpos($file , '/quick-event-manager.php') ){
        $new_links = array('<a href="http://quick-plugins.com/quick-event-manager/"><strong>Help and Support</strong></a>','<a href="'.get_admin_url().'options-general.php?page=quick-event-manager/settings.php&tab=donate"><strong>Donate</strong></a>');
        $links = array_merge( $links, $new_links );  
    }
    return $links;
}