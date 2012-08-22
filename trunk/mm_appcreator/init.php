<?php

// -> constants and includes <-

define('ROOT', '/' . join('/', array_splice(preg_split(';/;', __FILE__, -1, PREG_SPLIT_NO_EMPTY), 0, 2)) . '/public_html/facebook/');

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
		) ENGINE=InnoDB COLLATE=utf8_unicode_ci AUTO_INCREMENT=3"
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
		) ENGINE=InnoDB COLLATE=utf8_unicode_ci AUTO_INCREMENT=3"
	);
}

if ($db->numRows($db->query("SHOW TABLES LIKE 'pages'")) == 0)
{
	$db->execute(
		"CREATE TABLE pages(
			id int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
			owner_id int(11) UNSIGNED NOT NULL,
			role varchar(255) COLLATE utf8_unicode_ci NOT NULL,
			extra varchar(255) COLLATE utf8_unicode_ci NOT NULL,
			PRIMARY KEY (id)
		) ENGINE=InnoDB COLLATE=utf8_unicode_ci AUTO_INCREMENT=3"
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

$facebook  = new Facebook(APP_APIKEY, APP_SECRET);

// req logged in user
$user_id = $_SESSION['fb_user_id'] = $facebook->require_login();

?>