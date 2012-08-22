<?php

/*
	!!! NOT USED SINCE IMPLEMENTED AJAX FILE UPLOADER !!!
*/

// -> constants and includes <-

define('ROOT', '/' . join('/', array_splice(preg_split(';/;', __FILE__, -1, PREG_SPLIT_NO_EMPTY), 0, 2)) . '/public_html/facebook/');

require_once(ROOT.'_scripts/_constants.php');

// -> session <-

session_write_close();
session_id($_GET['sid']);
session_start();
$directory = $_SESSION['directory'];
$delimiter = "\n";

if ($_GET['type'] == 'image')
{
	$sub_dir = 'image';
}
else if ($_GET['type'] == 'media')
{
	$sub_dir = 'media';
}
else
{
	header('Content-type: text/javascript');
	header('pragma: no-cache');
	header('expires: 0');
	print('// ERROR');
	die();
}

// use your correct (relative!) path here
$img_dir    = "../".$directory."/assets/".$sub_dir;
$output     = "";
//$output    .= "// directory assigned to user:   ".$directory."\n";
//$output    .= "// directory to parse for files: ".$img_dir."\n";
//$output    .= "// session id:                   ".session_id()."\n";
//$output    .= "// recieved session id:          ".$_GET['sid']."\n";
$output    .= 'var tinyMCEImageList = new Array(';

// Since TinyMCE3.x you need absolute image paths in the list...
//$abspath = preg_replace('~^/?(.*)/[^/]+$~', '/$1', $_SERVER['SCRIPT_NAME']);

if (is_dir($img_dir))
{
	$direc = opendir($img_dir);

	while ($file = readdir($direc))
	{
		if (!preg_match('~^\.~', $file))
		{
			if (is_file("$img_dir/$file") && getimagesize("$img_dir/$file") != FALSE)
			{
				$output .= $delimiter
					. '["'
					. utf8_encode($file)
					. '", "'
					. utf8_encode("$abspath/$img_dir/$file")
					. '"],';
			}
		}
	}

	$output = substr($output, 0, -1);
	$output .= $delimiter;

	closedir($direc);
}

$output .= ');';

header('Content-type: text/javascript');
header('pragma: no-cache');
header('expires: 0');
echo $output;

?>