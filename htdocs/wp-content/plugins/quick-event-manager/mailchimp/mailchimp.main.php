<?php

	namespace QEM;
	
	function getMailChimp($key) {
		
		return new MailChimp($key);
		
	}

	function subscribe($email, $name = '', $title = '') {
		
		$qemkey = get_option('qpp_key');
		if ($qemkey['authorised']) {
			$list = get_option('qem_addons');
		}

		
		$MailChimp = getMailChimp($list['mailchimpapikey']);
		
		$options = array();
		
		$options['email_address'] = $email;
		$options['status'] = 'subscribed';
		
		if (strlen($name)) {
			$names = explode(' ',$name);
			$merge = array(
				'FNAME' => $names[0],
				'TITLE' => $title // merge field name
			);
			if (isset($names[1])) {
				$merge['LNAME'] = $names[1];
			}
		}
		
		if (isset($merge)) $options['merge_fields'] = $merge;
		
		$result = $MailChimp->post("lists/{$list['mailchimpid']}/members", $options);

		// $result['status'] 
		//	-> 400 - Failure
		//	-> 'subscribed' - Success
		
	}
?>