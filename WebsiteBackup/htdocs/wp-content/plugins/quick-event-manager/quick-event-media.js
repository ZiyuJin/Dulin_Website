jQuery(document).ready(function () {
	
	datePickerOptions = {
		closeText: "Done",
		prevText: "Prev",
		nextText: "Next",
		currentText: "Today",
		monthNames: [ "January","February","March","April","May","June",
		"July","August","September","October","November","December" ],
		monthNamesShort: [ "Jan", "Feb", "Mar", "Apr", "May", "Jun",
		"Jul", "Aug", "Sep", "Oct", "Nov", "Dec" ],
		dayNames: [ "Sunday", "Monday", "Tuesday", "Wednesday", "Thursday", "Friday", "Saturday" ],
		dayNamesShort: [ "Sun", "Mon", "Tue", "Wed", "Thu", "Fri", "Sat" ],
		dayNamesMin: [ "Su","Mo","Tu","We","Th","Fr","Sa" ],
		weekHeader: "Wk",
		firstDay: 1,
		isRTL: false,
		showMonthAfterYear: false,
		yearSuffix: "",
		dateFormat: 'dd M yy'
	};
	
	if (jQuery('#qemdate').size()) jQuery('#qemdate').datepicker(datePickerOptions);
    if (jQuery('#qemenddate').size()) jQuery('#qemenddate').datepicker(datePickerOptions);
	if (jQuery('#qemcutoffdate').size()) jQuery('#qemcutoffdate').datepicker(datePickerOptions);
	
    var custom_uploader, img;
    jQuery('.qem-color').wpColorPicker();
    jQuery('#qem_upload_stripe_image').click(function(e) {
        e.preventDefault();
        if (custom_uploader) {custom_uploader.open();return;}
        custom_uploader = wp.media.frames.file_frame = wp.media({
            title: 'Stripe Image',button: {text: 'Insert Image'},multiple: false});
        custom_uploader.on('select', function() {
            attachment = custom_uploader.state().get('selection').first().toJSON();
            jQuery('#qem_stripe_image').val(attachment.url);
        });
        custom_uploader.open();
    });
    jQuery('#upload_media_button').click(function (e) {
        e.preventDefault();
        if (custom_uploader) {custom_uploader.open(); return; }
        custom_uploader = wp.media.frames.file_frame = wp.media({
            title: 'Select Background Image', button: {text: 'Insert Image'}, multiple: false});
        custom_uploader.on('select', function () {
            attachment = custom_uploader.state().get('selection').first().toJSON();
            jQuery('#upload_image').val(attachment.url);
        });
        custom_uploader.open();
    });
    jQuery('#upload_submit_button').click(function (e) {
        e.preventDefault();
        if (custom_uploader) {custom_uploader.open(); return; }
        custom_uploader = wp.media.frames.file_frame = wp.media({
            title: 'Select Submit Button Image', button: {text: 'Insert Image'}, multiple: false });
        custom_uploader.on('select', function () {
            attachment = custom_uploader.state().get('selection').first().toJSON();
            jQuery('#submit_image').val(attachment.url);
        });
        custom_uploader.open();
    });
    jQuery('#upload_event_image').click(function (e) {
        e.preventDefault();
        if (custom_uploader) {custom_uploader.open(); return; }
        custom_uploader = wp.media.frames.file_frame = wp.media({
            title: 'Select Event Image', button: {text: 'Insert Image'}, multiple: false});
        custom_uploader.on('select', function () {
            attachment = custom_uploader.state().get('selection').first().toJSON();
            jQuery('#event_image').val(attachment.url);
			jQuery('#event_image').change();
        });
        custom_uploader.open();
    });
    jQuery("#yourplaces").keyup(function () {
        var model= document.getElementById('yourplaces');
        var number = jQuery('#yourplaces').val()
        if (number == 1)
                jQuery("#morenames").hide();
            else {
                jQuery("#morenames").show();
            }
    });
	jQuery('#remove_event_image').click(function() {
		jQuery('#event_image').val('');
		jQuery('#event_image').change();
	});
	
	jQuery('#event_image').change(function() {
		
		/*
			Preload Image Before Displaying
		*/
		img = jQuery('<img />');
		
		/*
			If load is complete, display the image
		*/
		img.load(function() {
			if (this.src && this.complete) {
				jQuery('.qem-image').attr('src',this.src);
			} 
		});
		
		/*
			If error occurs (usually due to invalid url) show failure image
		*/
		img.error(function() {
			jQuery('.qem-image').attr('src',jQuery('.qem-image').attr('alt'));
		});
		
		/*
			Start the download of the image
		*/
		if (jQuery('#event_image').val() != '') {
			img.attr('src', jQuery('#event_image').val());
		} else {
			jQuery('.qem-image').attr('src',jQuery('.qem-image').attr('rel'));
		}
		
	});
});