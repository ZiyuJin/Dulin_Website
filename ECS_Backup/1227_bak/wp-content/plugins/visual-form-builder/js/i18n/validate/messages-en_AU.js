/*
 * Translated default messages for the jQuery validation plugin.
 * Locale: EN (English)
 * Region: AU (Austrailian)
 */
(function($) {
	$.extend($.validator.messages, {
		required: 'This field is required.',
		remote: 'Please fix this field.',
		email: 'Please enter a valid email address.',
		url: 'Please enter a valid URL.',
		date: 'Please enter a valid date.',
		dateISO: 'Please enter a valid date (ISO).',
		number: 'Please enter a valid number.',
		digits: 'Please enter only digits.',
		creditcard: 'Please enter a valid credit card number.',
		equalTo: 'Please enter the same value again.',
		maxlength: $.validator.format( 'Please enter no more than {0} characters.' ),
		minlength: $.validator.format( 'Please enter at least {0} characters.' ),
		rangelength: $.validator.format( 'Please enter a value between {0} and {1} characters long.' ),
		range: $.validator.format( 'Please enter a value between {0} and {1}.' ),
		max: $.validator.format( 'Please enter a value less than or equal to {0}.' ),
		min: $.validator.format( 'Please enter a value greater than or equal to {0}.' ),
		maxWords: $.validator.format( 'Please enter {0} words or less.' ),
		minWords: $.validator.format( 'Please enter at least {0} words.' ),
		rangeWords: $.validator.format( 'Please enter between {0} and {1} words.' ),
		alphanumeric: 'Letters, numbers, and underscores only please',
		lettersonly: 'Letters only please',
		nowhitespace: 'No white space please',
		phone: 'Please enter a valid phone number. Most US/Canada and International formats accepted.',
		ipv4: 'Please enter a valid IP v4 address.',
		ipv6: 'Please enter a valid IP v6 address.',
		ziprange: 'Your ZIP-code must be in the range 902xx-xxxx to 905-xx-xxxx',
		zipcodeUS: 'The specified US ZIP Code is invalid',
		integer: 'A positive or negative non-decimal number please',
		vfbUsername: 'This username is already registered. Please choose another one.'
	});
}(jQuery));