<?php

require_once('init.php');
require_once('member.php');
require_once('fb_header.php');

print '<p>permissions: '.$permission.'</p>';

if ($permission == '10' || $permission == '*')
	print '<p>YOU are the MAN! :]</p>';

require_once('fb_footer.php');

?>