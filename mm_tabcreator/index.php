In order to use functionality of the application, please allow permissions!

<?php

define('ROOT', '/' . join('/', array_splice(preg_split(';/;', __FILE__, -1, PREG_SPLIT_NO_EMPTY), 0, 2)) . '/public_html/facebook/');
require_once(ROOT.'_libs/facebook/facebook.php');

// -> facebook <-

$appapikey = '2db2169405b0097330665de7690d0553';
$appsecret = '897f0898acf06c83e2482a99748ed648';
$facebook  = new Facebook($appapikey, $appsecret);

$user_id = $facebook->require_login();
print '<p>'.$user_id.'</p>';

?>