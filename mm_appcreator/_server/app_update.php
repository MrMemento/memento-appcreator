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
		SET title='".addslashes($_SESSION['title'])."',
			author='".addslashes($_SESSION['author'])."',
			fb_url='".addslashes($_SESSION['name'])."',
			contact_mail='".addslashes($_SESSION['contact_mail'])."',
			support_mail='".addslashes($_SESSION['support_mail'])."',
			link='".addslashes($_SESSION['link'])."',
			icon_url='".addslashes($_SESSION['icon_url'])."',
			avatar_url='".addslashes($_SESSION['avatar_url'])."',
			button_text='".addslashes($_SESSION['button_text'])."',
			post_text='".addslashes($_SESSION['post_text'])."',
			description='".addslashes($_SESSION['description'])."',
			type='".$_SESSION['type']."',
			language='".$_SESSION['language']."',
			ga_code='".$_SESSION['ga_code']."'
		WHERE user_id=".$user_id."
			AND id=".$_SESSION['edited_id']
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
	print "Updating application properties...<br />";

	try
	{
		// update application
		$facebook->api_client->admin_setAppProperties(array(
			'application_name'		=> $_SESSION['title'],
			'contact_email'			=> $_SESSION['contact_mail'],
			'email'					=> $_SESSION['support_mail'],
			'description'			=> $_SESSION['description'],
			'icon_url'				=> $_SESSION['icon_url'],
			'logo_url'				=> $_SESSION['avatar_url'],
			'publish_action'		=> $_SESSION['post_text'],
			'publish_self_action'	=> $_SESSION['post_text'],
			// -------> CANVAS NAME COULD BE USED <-------
			'canvas_name'           => $_SESSION['name']
		));
	}
	catch (Exception $regular_error)
	{
		print "Updating local database due to error:<br />";				
		print "Fetching actual property (properties)...<br />";

		// update local databse with current values
		$error = true;
		$fb_app = $facebook->api_client->admin_getAppProperties(array(
			'canvas_name',
			'application_name',
			'contact_email',
			'description',
//			'icon_url',
//			'logo_url',
			'publish_action',
			'publish_self_action'
		));

		print "Saving to local database...<br />";

		$db->execute(
			"UPDATE applications
				SET title='".addslashes($fb_app['application_name'])."',
					fb_url='".addslashes($fb_app['canvas_name'])."',
					contact_mail='".addslashes($fb_app['contact_email'])."',
					post_text='".addslashes($fb_app['publish_action'])."',
					description='".addslashes($fb_app['description'])."'
				WHERE user_id=".$user_id."
					AND id=".$_SESSION['edited_id']
		);
	}
}

$facebook->api_client->end_permissions_mode();

print "Completed running.</p>\n";

/*
-----------------------> SCREEN: NAME
*/

if (!$fatal)
{
	$HTML  = "<form action=\"".FB_URL."edit_application.php?action=settings&id=".$_SESSION['edited_id']."\" method=\"post\" target=\"_top\" >";
	$HTML .= "\t<input type=\"submit\" class=\"fb_button\" value=\"Back to Settings\" />";
	$HTML .= "</form>";

	if (!$error)
	{
		print $HTML;
	}
	else
	{
		print "<p><b>ERROR</b>: Some error(s) occured during saving your application, these might be corrected through the settings page of the application. The details of the error cen be seen in the footer.</p>";
		print $HTML;
		$debug->print_r_html($regular_error);
	}
}
else
{
	print "<p><b>FATAL ERROR</b>: An unhandlable error occured during saving. The details of the error cen be seen in the footer.</p>\n";
	print $HTML;
	$debug->print_r_html($fatal_error);
}	

unset($_SESSION['title']);
unset($_SESSION['author']);
unset($_SESSION['type']);
unset($_SESSION['contact_mail']);
unset($_SESSION['support_mail']);
unset($_SESSION['link']);
unset($_SESSION['icon_url']);
unset($_SESSION['avatar_url']);
unset($_SESSION['button_text']);
unset($_SESSION['post_text']);
unset($_SESSION['description']);
unset($_SESSION['language']);
unset($_SESSION['ga_code']);
unset($_SESSION['name']);
unset($_SESSION['edited_id']);

?>

</body>