<?php

$news = array(
	array(
		'message' => 'There\'s a new level in the dungeon!',
		'action_link' => array(
			'text' => 'Play now.',
			'href' => 'http://www.example.com/gifts?id=5878237'
		)
	)
);
$image = 'http://www.martialdevelopment.com/wordpress/wp-content/images/cheezburger-or-dim-mak.jpg';
$facebook->api_client->dashboard_addGlobalNews($news, $image);
//$facebook->api_client->dashboard_clearGlobalNews($news_ids);

?>