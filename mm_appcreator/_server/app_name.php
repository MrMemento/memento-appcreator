<?php

// -> session <-

session_write_close();
session_id($_GET['sid']);
session_start();

$session_id   = $_GET['sid'];
$member       = !isset($_SESSION['logged_in'])    ? false  : $_SESSION['logged_in'];
$directory    = !isset($_SESSION['directory'])    ? 'null' : $_SESSION['directory'];
$permission   = !isset($_SESSION['permission'])   ? 'null' : $_SESSION['permission'];
$applications = !isset($_SESSION['applications']) ? 'null' : $_SESSION['applications'];

// -> constants and includes <-

define('ROOT', '/' . join('/', array_splice(preg_split(';/;', __FILE__, -1, PREG_SPLIT_NO_EMPTY), 0, 2)) . '/public_html/facebook/');

require_once('../member.php');
require_once(ROOT.'_core/_constants.php');
require_once(ROOT.'_core/database/MySQLLayer.php');
require_once(ROOT.'_core/facebook/facebook.php');
require_once(ROOT.'_core/debug/Debug.php');

// -> database <-

$db = new MySQLLayer(DB_NAME, DB_HOST, DB_USER, DB_PASS);
$db->execute(
	"SET NAMES utf8"
);

// -> debug <-

$debug = Debug::getInstance(ROOT.'zzz_logs/debug.log');

// -> facebook <-

$facebook  = new Facebook(APP_APIKEY, APP_SECRET);

// req logged in user
$user_id = $_SESSION['fb_user_id'] = $facebook->require_login();

set_time_limit(30);

?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" style="margin:0px; padding:0px; overflow:hidden;">
<head>
<title>Memento Application Creator - Application Saver</title>
<style type="text/css">
p {
	font-family:"lucida grande",tahoma,verdana,arial,sans-serif;
	font-size:11px;
	text-align:left;
}
.fb_button {
	background-color: #3b5998;
	border-color: #d8dfea rgb(14, 31, 91) rgb(14, 31, 91) rgb(216, 223, 234);
	border-style: solid;
	border-width: 1px;
	color: #fff;
	font-family: "lucida grande", tahoma, verdana, arial, sans-serif;
	font-size: 11px;
	margin: 0 2px;
	padding: 2px 18px;
}
</style>
</head>
<body>

<?php

print "<p style=\"font-family:monospace; width:100%; background:#000; color:#FFF;\">";
print "Saving to local database...<br />";

$db->execute(
	"UPDATE applications
		SET fb_url='".addslashes($_SESSION['name'])."'
		WHERE user_id=".$user_id."
			AND id=".$_SESSION['created_id']
);

print "Connecting to application...<br />";

try
{
	// mimic application
	$facebook->api_client->permissions_grantApiAccess($_SESSION['app_key'], array('admin'));
	$facebook->api_client->begin_permissions_mode($_SESSION['app_key']);
}
catch (Exception $fatal_error)
{
	// fatal error, can't connect to application
	// maybe deleted?
	$fatal = true;

	print "FATAL ERROR<br />";
}

if (!$fatal)
{
	print "Setting application properties...<br />";

	try
	{
		// update application
		$facebook->api_client->admin_setAppProperties(array(
			// -------> CANVAS NAME COULD BE USED <-------
			'canvas_name' => $_SESSION['name']
		));
	}
	catch (Exception $regular_error)
	{
		print "Updating local database due to error:<br />";				
		print "Fetching actual property (properties)...<br />";

		// update local databse with current value
		$error = true;
		$fb_app = $facebook->api_client->admin_getAppProperties(array(
			'canvas_name'
		));

		print "Saving to local database...<br />";

		$db->execute(
			"UPDATE applications
				SET fb_url='".addslashes($fb_app['canvas_name'])."'
				WHERE user_id=".$user_id."
					AND id=".$_SESSION['created_id']
		);
	}	
}

// close mimic
$facebook->api_client->end_permissions_mode();

print "Completed running.</p>\n";

/*
-----------------------> SCREEN: NAME
*/

if (!$fatal)
{
	$HTML  = "<p>Creation of your application is complete!</p>\n";
	$HTML .= "<form action=\"".FB_URL."\" method=\"post\" target=\"_top\" >";
	$HTML .= "\t<input type=\"submit\" class=\"fb_button\" value=\"Back to List\" />";
	$HTML .= "</form>";

	$ERR_HTML  = "<form action=\"".FB_URL."create_application.php?action=add_name\" method=\"post\" target=\"_top\" >";
	$ERR_HTML .= "\t<input type=\"submit\" class=\"fb_button\" value=\"Try another name\" />";
	$ERR_HTML .= "</form>";

	if (!$error)
	{
		print $HTML;
	}
	else
	{
		print "<p><b>ERROR</b>: Some error(s) occured during saving your application, these might be corrected through the settings page of the application. The details of the error cen be seen in the footer.</p>";
		print $ERR_HTML;
		$debug->print_r_html($regular_error);
	}
}
else
{
	print "<p><b>FATAL ERROR</b>: An unhandlable error occured during saving. The details of the error cen be seen in the footer.</p>\n";
	print $ERR_HTML;
	$debug->print_r_html($fatal_error);
}	

unset($_SESSION['name']);

?>

</body>