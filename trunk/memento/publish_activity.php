<?php

$activity = array(
	'message' => '{*actor*} just sponsored @:563683308!',
	'action_link' => array(
		'text' => 'Sponsor this cause',
		'href' => 'http://www.example.com/games?id=5878237'
	)
);

$facebook->api_client->dashboard_publishActivity($activity);
/*
$activity_ids = array('NEWS_ID1', 'NEWS_ID2');
$facebook->api_client->dashboard.removeActivity($activity_ids);
*/
?>