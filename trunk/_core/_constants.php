<?php

define('APP_APIKEY', 'cdde23225f29b4ca83bbae3d6f373f0c');
define('APP_SECRET', '63469fe94f55cb86da7708e6eb4d3aca');

define('MM_URL',     'http://facebook.mementomedia.net/');
define('MM_APP_URL', 'http://facebook.mementomedia.net/mm_appcreator/');
define('FB_URL',     'http://apps.facebook.com/mm_appcreator/');

define('DB_HOST', 'localhost');
define('DB_USER', 'memento_facebook');
define('DB_PASS', 'f4c3b00k_p4ss');
define('DB_NAME', 'memento_facebook');

$SKELETONS = array(
	array(
		'name' => 'Custom Tab',
		'dir'  => 'tab_app'
	),
	array(
		'name' => 'Custom Profile Box',
		'dir'  => 'box_app'
	),
	array(
		'name' => 'Publisher',
		'dir'  => 'publish_app'
	),
	array(
		'name' => 'Random Card',
		'dir'  => 'random_app'
	),
	array(
		'name' => 'Quiz',
		'dir'  => 'quiz_app'
	),
	array(
		'name' => 'Gift',
		'dir'  => 'gift_app'
	)
);

?>