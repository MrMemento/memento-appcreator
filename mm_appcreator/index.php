<?

require_once('init.php');

/*
	---------------------------> ACTION: LOGIN
*/
if ($_GET['action'] == 'login')
{
	$name = $_POST['name'];
	$pass = $_POST['pass'];

	$res = $db->queryAssoc(
		"SELECT id, fb_id, directory, permission
			FROM users
			WHERE name = '$name'
				AND password = '$pass'"
	);
	if ($db->numRows() > 0)
	{
		$id = $res[0]['id'];
		if ($res[0]['fb_id'] == null)
			$db->execute(
				"UPDATE users
					SET fb_id=$user_id
					WHERE id=$id"
			);

//		$debug->print_r_html($res[0]);

		$_SESSION['logged_in']    = $member       = true;
		$_SESSION['directory']    = $directory    = $res[0]['directory'];
		$_SESSION['permission']   = $permission   = $res[0]['permission'];
		$_SESSION['applications'] = $applications = $res[0]['applications'];
	}
	else
	{
		$_SESSION['logged_in']    = $member       = false;
		$_SESSION['directory']    = $directory    = 'null';
		$_SESSION['permission']   = $permission   = 'null';
		$_SESSION['applications'] = $applications = 'null';
		session_destroy();
	}
}

/*
	---------------------------> ACTION: LOGOUT
*/
if ($_GET['action'] == 'logout')
{
	$_SESSION['logged_in']    = $member       = false;
	$_SESSION['directory']    = $directory    = 'null';
	$_SESSION['permission']   = $permission   = 'null';
	$_SESSION['applications'] = $applications = 'null';
}

require_once('fb_header.php');

function loginForm()
{
	print <<<HTML

<p>Please log in, to access features of the application. If you have not recieved your account information, please contact Memento-Media!</p>

<fb:editor action="?action=login" labelwidth="100">
	<fb:editor-text label="Username" name="name" value=""/>
	<fb:editor-custom label="Password"> 
		<input type="password" name="pass" size="25">
	</fb:editor-custom> 
	<fb:editor-buttonset>
		<fb:editor-button value="Login"/>
	</fb:editor-buttonset>
</fb:editor>

HTML;
}

function dashboard($db, $user_id)
{
	$res = $db->queryAssoc(
		"SELECT id, title, type, author, app_id, fb_url
			FROM applications
			WHERE user_id = $user_id"
	);

	print <<<HTML

<style type="text/css">
.apps table {
	width:650px;
}
.apps table tr {
	background-color:#FFFFFF;
}
.apps table tr.odd {
	background-color:#F0F0FF;
}
.apps table td {
	padding:3px 3px 3px 9px;
}
.apps table .subheader {
	color:gray;
	font-size:9px;
}
.apps table h4 {
	font-weight:bold;
	margin:0;
	font-size:11px;
}
.apps table td.btn {
	width:60px;
}
</style>
<br/>
<p>You are having the following applications in our database:</p>
<br/>
<div class="apps" align="center">
	<table cellspacing="0">
HTML;

	$j = count($res);
	if ($j == 0)
	{
		print 'No applications found. Start creating your applications by clicking the "Create new Application" button in the top right corner!';
	}
	else
	{		
		global $debug;
		global $SKELETONS;

		for ($i=0; $i<$j; $i++)
		{
			$title    = $res[$i]['title'];
			$type     = $SKELETONS[$res[$i]['type']]['name'];
			$edit_url = '_skeletons/'.$SKELETONS[$res[$i]['type']]['dir'].'/edit.php';
			$author   = $res[$i]['author'];
			$id       = $res[$i]['id'];
			$app_id   = $res[$i]['app_id'];
			$fb_url   = $res[$i]['fb_url'];

			print "\t\t<tr";
			if ($i%2==0) print ' class="odd"';
			print '>';
			print <<<HTML

			<td>
				<h4>$title</h4>
				<div class="subheader">type: $type<br/>author: $author</div>
			</td>
			<td class="btn">
HTML;
			print "\t\t\t<a href=\"".FB_URL."edit_application.php?action=synchronize&id=$id\">Synchronize</a>\n";
			print <<<HTML
			</td>
			<td class="btn">
HTML;
			print "\t\t\t<a href=\"".FB_URL."edit_application.php?action=settings&id=$id\">Settings</a>\n";
			print <<<HTML
			</td>
			<td class="btn">
HTML;
			print "\t\t\t<a href=\"".FB_URL."edit_application.php?action=delete&id=$id\">Delete</a>\n";
			print <<<HTML
			</td>
			<td class="btn">
HTML;
			print "\t\t\t<a href=\"".FB_URL."edit_application.php?action=page&id=$id\">Open Page</a>\n";
			print <<<HTML
			</td>
			<td class="btn">
HTML;
			print "\t\t\t<a href=\"".FB_URL.$edit_url."?id=$id\">Contents</a>\n";
			print <<<HTML
			</td>
			<td class="btn">			
HTML;
			print "\t\t\t<a href=\"http://www.facebook.com/apps/application.php?id=$app_id\">Profile</a>\n";
			print <<<HTML
			</td>
			<td class="btn">
HTML;
			print "\t\t\t<a href=\"http://apps.facebook.com/$fb_url\">Go</a>\n";
			print <<<HTML
			</td>
		</tr>
HTML;
		}
	}

	print <<<HTML

	</table>
</div>

HTML;
}

if ($member == true)
	dashboard($db, $user_id);
else
	loginForm();

require_once('fb_footer.php');

?>