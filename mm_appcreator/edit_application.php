<?php

require_once('init.php');
require_once('member.php');
require_once('fb_header.php');

$error = false;
$fatal = false;

$res = $db->queryAssoc(
	"SELECT *
		FROM applications
		WHERE id=".$_GET['id']."
			AND user_id=$user_id"
);

if ($db->numRows() == 0)
	die('<p>You are not allowed to modify this application!</p>');
$app = $res[0];

switch ($_GET['action'])
{
	case 'synchronize':
/*
	-----------------------> ACTION: SYNCHRONIZE
*/
		$_SESSION['edited_id']    = $_GET['id'];
		$_SESSION['title']        = $app['title'];
		$_SESSION['author']       = $app['author'];
		$_SESSION['name']         = $app['fb_url'];
		$_SESSION['language']     = $app['language'];
		$_SESSION['contact_mail'] = $app['contact_mail'];
		$_SESSION['support_mail'] = $app['support_mail'];
		$_SESSION['link']         = $app['link'];
		$_SESSION['icon_url']     = $app['icon_url'];
		$_SESSION['avatar_url']   = $app['avatar_url'];
		$_SESSION['button_text']  = $app['button_text'];
		$_SESSION['post_text']    = $app['post_text'];
		$_SESSION['description']  = $app['description'];
		$_SESSION['ga_code']      = $app['ga_code'];
		$_SESSION['app_id']       = $app['app_id'];
		$_SESSION['app_key']      = $app['app_key'];

		print "<fb:iframe name=\"server_sync\" frameborder=\"0\" style=\"width:760px;height:700px;\" src=\"".MM_APP_URL."_server/app_synchronize.php?sid=".$session_id."\" />\n";

		break;

	case 'update':
/*
	-----------------------> ACTION: UPDATE
*/
		$_SESSION['edited_id']    = $_GET['id'];
		$_SESSION['title']        = $_POST['title'];
		$_SESSION['author']       = $_POST['author'];
		$_SESSION['name']         = $_POST['name'];
		$_SESSION['language']     = $_POST['language'];
		$_SESSION['contact_mail'] = $_POST['contact_mail'];
		$_SESSION['support_mail'] = $_POST['support_mail'];
		$_SESSION['link']         = $_POST['link'];
		$_SESSION['icon_url']     = $_POST['icon_url'];
		$_SESSION['avatar_url']   = $_POST['avatar_url'];
		$_SESSION['button_text']  = $_POST['button_text'];
		$_SESSION['post_text']    = $_POST['post_text'];
		$_SESSION['description']  = $_POST['description'];
		$_SESSION['ga_code']      = $_POST['ga_code'];
		$_SESSION['app_id']       = $app['app_id'];
		$_SESSION['app_key']      = $app['app_key'];

		print "<fb:iframe name=\"server_update\" frameborder=\"0\" style=\"width:760px;height:700px;\" src=\"".MM_APP_URL."_server/app_update.php?sid=".$session_id."\" />\n";

		break;

	case 'settings':
/*
	-----------------------> SCREEN: SETTINGS
*/
		$_SESSION['edited_app'] = $app;
		print "<fb:iframe name=\"settings\" frameborder=\"0\" style=\"width:760px;height:700px;\" src=\"".MM_APP_URL."_forms/app_edit.php?sid=".$session_id."\" />\n";

		break;

	case 'page':
/*
	-----------------------> SCREEN: PAGE
*/
		switch ($app['type'])
		{
			case 0:
				$role  = 'open';
				$extra = 0;
				break;

			case 1:
				$role  = 'open';
				$extra = 0;
				break;
		}

		$res = $db->queryAssoc(
			"SELECT id
				FROM pages
				WHERE owner_id=".$app['id']."
					AND role='".$role."'
					AND extra='".$extra."'"
		);

		if (isset($res[0]['id']))
		{
			$_SESSION['page'] = array(
				'id'       => $res[0]['id'],
				'owner_id' => $app['id'],
				'role'     => $role,
				'extra'    => $extra
			);
		}
		else
		{
			$_SESSION['page'] = array(
				'owner_id' => $app['id'],
				'role'     => $role,
				'extra'    => $extra
			);
		}

		print("<p>Page of application <b>".$app['title']."</b>:</p>\n");
		print "<fb:iframe frameborder=\"0\" style=\"width:760px;height:700px;\" src=\"".MM_APP_URL."_forms/content_editor.php?sid=".$session_id."\" />\n";

		break;

	case 'save_page':

		$page_cont = $_POST['page'];
		if (strip_tags($page_cont) != '')
		{
			if (isset($_SESSION['page']['id']))
			{
				$page_id = $_SESSION['page']['id'];
			}
			else
			{
				$db->execute(
					"INSERT INTO pages
						SET owner_id=".$_SESSION['page']['owner_id'].",
							role='".$_SESSION['page']['role']."',
							extra='".$_SESSION['page']['extra']."'"
				);

				$page_id = $db->lastInsertedId();
			}

			print "<p>Succeeded in saving page. You may now return to Application List.</p>\n";
			print "<fb:editor action=\"".FB_URL."\" labelwidth=\"100\">\n";
			print "\t<fb:editor-buttonset>\n";
			print "\t\t<fb:editor-button value=\"Go to List\"/>\n";
			print "\t</fb:editor-buttonset>\n";
			print "</fb:editor>\n";
		}

		$page_path = ROOT.$directory.'/assets/page/'.str_pad($page_id, 16, '0', STR_PAD_LEFT).'.html';
		file_put_contents($page_path, $page_cont);

		unset($_SESSION['page']);

		break;

	case 'delete':
/*
	-----------------------> SCREEN: CONFIRM DELETE
*/
		if ($_POST['confirmed'] == 'true' || $_POST['confirmed'] == true)
		{
			$db->execute(
				"DELETE FROM applications
					WHERE user_id=".$user_id."
						AND id=".$_GET['id']."
						LIMIT 1"
			);

			print "<p>Application <b>".$app['title']."</b> deleted.<br />Visit <a href=\"http://www.facebook.com/developers/apps.php\"><b>Developer Application</b></a>, to delete the app from your page aswell!</p>\n";
			print "<fb:editor action=\"http://www.facebook.com/developers/apps.php\" labelwidth=\"100\">\n";
			print "\t<fb:editor-buttonset>\n";
			print "\t\t<fb:editor-button value=\"Go there now\"/>\n";
			print "\t</fb:editor-buttonset>\n";
			print "</fb:editor>\n";
		}
		else
		{
			print("<p>Are you sure you want to delete the application <b>".$app['title']."?</b><br />This will only delete application from local database, not from your Facebook Account.<br />To do so, visit <a href=\"http://www.facebook.com/developers/apps.php\"><b>Developer Application</b></a>!</p>\n");
			print "<fb:editor action=\"?action=delete&id=".$_GET['id']."\" labelwidth=\"100\">\n";
			print "\t<fb:editor-custom>\n";
			print "\t\t<input type=\"hidden\" name=\"confirmed\" value=\"true\" />\n";
			print "\t</fb:editor-custom>\n";
			print "\t<fb:editor-buttonset>\n";
			print "\t\t<fb:editor-button value=\"DELETE\"/>\n";
			print "\t</fb:editor-buttonset>\n";
			print "</fb:editor>\n";
		}

		break;
}