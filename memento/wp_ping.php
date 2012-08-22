<?php

function filog()
{
	$args = func_get_args();
	if (count($args) == 1 && $args[0])
		$args = $args[0];

	file_put_contents(
		'/home/memento/public_html/facebook/ping/zzz_memento.log',
		'Logged on: '.date(DATE_RFC822)."\r\nArguments: ".print_r($args, true)."\r\n",
		FILE_APPEND
	);
}

$ping = simplexml_load_string(preg_replace('/<\?xml(.*)?\?'.'>/', '', $HTTP_RAW_POST_DATA));

//if ($ping['methodName'] == 'weblogUpdates.extendedPing')
filog(compact('ping'));

//

print xmlrpc_encode_request(null, array('success' => 1)); 

?>