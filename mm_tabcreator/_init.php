<?php

// -> constants and includes <-

define('ROOT', '/' . join('/', array_splice(preg_split(';/;', __FILE__, -1, PREG_SPLIT_NO_EMPTY), 0, 2)) . '/public_html/facebook/');

define('DB_HOST', 'localhost');
define('DB_USER', 'memento_facebook');
define('DB_PASS', 'f4c3b00k_p4ss');
define('DB_NAME', 'memento_facebook');

require_once(ROOT.'_libs/database/MySQLLayer.php');
require_once(ROOT.'_libs/facebook/facebook.php');

// -> debug <-

function print_r_html()
{
	$HTML = "<div><p><b>DEBUG</b>";
	$args = func_get_args();
	foreach ($args as $key=>$arg)
	{
		$s = print_r($arg, true);
		$s = htmlentities($s, ENT_COMPAT, "utf-8");
		$s = str_replace("\r",	"",						$s);
		$s = str_replace("\n",	"<br>",					$s);
		$s = str_replace(" ",	"&nbsp;",				$s);
		$s = str_replace("\t",	"&nbsp;&nbsp;&nbsp;",	$s);

		$HTML .= "<hr />".$key.": ".$s;
	}

	$HTML .= "<hr /></p></div>";
	print $HTML;
}

function print_str_html()
{
	$HTML = "<div><p><b>DEBUG</b>\n";
	$args = func_get_args();
	foreach ($args as $key=>$arg)
	{
		$s = print_r($arg, true);
		$s = htmlentities($s, ENT_COMPAT, "utf-8");
		$s = str_replace("\r",	"",						$s);
		$s = str_replace("\n",	"<br>",					$s);
		$s = str_replace(" ",	"&nbsp;",				$s);
		$s = str_replace("\t",	"&nbsp;&nbsp;&nbsp;",	$s);

		$HTML .= "<hr />".$key.": ".$s;
	}

	$HTML .= "<hr /></p></div>";
	return $HTML;
}

function filog()
{
	$dump = 'Logged on: '.date(DATE_RFC822)."\r\n".print_r(func_get_args(), true)."\r\n";
	file_put_contents('/home/sexym/public_html/assets/zzz_tmp/zzz_memento.log', $dump, FILE_APPEND);
}

// -> database <-

$db = new MySQLLayer(DB_NAME, DB_HOST, DB_USER, DB_PASS);
$db->execute(
	"SET NAMES utf8"
);

// -> install <-

if ($db->numRows($db->query("SHOW TABLES LIKE 'users'")) == 0)
{
	$db->execute(
		"CREATE TABLE users(
			id int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
			fb_id bigint(63) UNSIGNED COLLATE utf8_unicode_ci NULL,
			name varchar(255) COLLATE utf8_unicode_ci NOT NULL,
			email varchar(255) COLLATE utf8_unicode_ci NOT NULL,
			permission int(6) UNSIGNED NOT NULL,
			applications varchar(255) COLLATE utf8_unicode_ci NOT NULL,
			password varchar(32) COLLATE utf8_unicode_ci NOT NULL,
			directory varchar(32) COLLATE utf8_unicode_ci NOT NULL,
			reg_time datetime NOT NULL,
			PRIMARY KEY (id)
		) ENGINE=InnoDB COLLATE=utf8_unicode_ci AUTO_INCREMENT=4"
	);

	$db->execute(
		"INSERT INTO users
			SET name = 'memento',
				email = 'theonelobo@gmail.com',
				password = 'facebookapp',
				permission = 10,
				applications = '*',
				directory = 'memento',
				reg_time = NOW()"
	);
}

if ($db->numRows($db->query("SHOW TABLES LIKE 'applications'")) == 0)
{
	$db->execute(
		"CREATE TABLE applications(
			id int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
			app_id bigint(63) UNSIGNED NOT NULL UNIQUE,
			app_key varchar(255) COLLATE utf8_unicode_ci NOT NULL,
			type tinyint(2) UNSIGNED NOT NULL,
			title varchar(255) COLLATE utf8_unicode_ci NOT NULL,
			author varchar(255) COLLATE utf8_unicode_ci NOT NULL,
			language varchar(255) COLLATE utf8_unicode_ci NOT NULL,
			contact_mail varchar(255) COLLATE utf8_unicode_ci NOT NULL,
			support_mail varchar(255) COLLATE utf8_unicode_ci NOT NULL,
			link varchar(255) COLLATE utf8_unicode_ci NOT NULL,
			icon_url varchar(255) COLLATE utf8_unicode_ci NOT NULL,
			avatar_url varchar(255) COLLATE utf8_unicode_ci NOT NULL,
			button_text varchar(255) COLLATE utf8_unicode_ci NOT NULL,
			post_text varchar(255) COLLATE utf8_unicode_ci NOT NULL,
			description text COLLATE utf8_unicode_ci NOT NULL,
			ga_code varchar(255) COLLATE utf8_unicode_ci NOT NULL,
			user_id bigint(63) UNSIGNED COLLATE utf8_unicode_ci NOT NULL,
			creation_time datetime NOT NULL,
			fb_url varchar(255) COLLATE utf8_unicode_ci NOT NULL,
			PRIMARY KEY (id)
		) ENGINE=InnoDB COLLATE=utf8_unicode_ci AUTO_INCREMENT=4"
	);
}

// -> session <-

session_write_close();
session_start();
$session_id   = session_id();
$member       = !isset($_SESSION['logged_in'])    ? false  : $_SESSION['logged_in'];
$directory    = !isset($_SESSION['directory'])    ? 'null' : $_SESSION['directory'];
$permission   = !isset($_SESSION['permission'])   ? 'null' : $_SESSION['permission'];
$applications = !isset($_SESSION['applications']) ? 'null' : $_SESSION['applications'];

// -> facebook <-

$appapikey = 'cdde23225f29b4ca83bbae3d6f373f0c';
$appsecret = '63469fe94f55cb86da7708e6eb4d3aca';
$facebook  = new Facebook($appapikey, $appsecret);

// req logged in user
$user_id = $_SESSION['fb_user_id'] = $facebook->require_login();

?>