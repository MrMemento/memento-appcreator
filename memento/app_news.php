<?php

$image = 'http://www.martialdevelopment.com/wordpress/wp-content/images/cheezburger-or-dim-mak.jpg';
$news = array(
	array(
		'message' => 'Your friend @:563683308 just sent you a present!',
		'action_link' => array(
			'text' => 'Get Your Gift',
			'href' => 'http://www.example.com/gifts?id=5878237'
		)
	)
);
$facebook->api_client->dashboard_addNews(UID, $news, $image);

$uids = array('9','12');
$image = 'http://www.martialdevelopment.com/wordpress/wp-content/images/cheezburger-or-dim-mak.jpg';
$news = array(
	array(
		'message' => 'Your friend @:563683308 just sent you a present!',
		'action_link' => array(
			'text' => 'Get Your Gift',
			'href' => 'http://www.example.com/gifts?id=5878237'
		)
	)
);

$facebook->api_client->dashboard_multiAddNews($uids, $news, $image);
/*
$ids = array(
	'563683308' => array('5494694961'),
	'12' => array('9956696531', '4656765984'),
	'9' => array()
);
$facebook->api_client->dashboard_multiClearNews($ids);
*/
?>