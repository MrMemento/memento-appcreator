<?php

require_once('init.php');
require_once('member.php');
require_once('fb_header.php');

$error = false;
$fatal = false;

switch ($_GET['action'])
{
	case 'name':

//	-----------------------> ACTION: SAVE NAME

		$_SESSION['name'] = $_POST['name'];

		print "<fb:iframe name=\"server_name\" frameborder=\"0\" style=\"width:760px;height:700px;\" src=\"".MM_APP_URL."_server/app_name.php?sid=".$session_id."\" />\n";

		break;

	case 'save':	

//-----------------------> ACTION: SAVE APPLICATION

		$_SESSION['app_id']  = $_GET['app_id'];
		$_SESSION['app_key'] = $_GET['app_key'];

		print "<fb:iframe name=\"server_save\" frameborder=\"0\" style=\"width:760px;height:700px;\" src=\"".MM_APP_URL."_server/app_save.php?sid=".$session_id."\" />\n";

		break;

	case 'add_name':

		$FBML  = "<p>Please select a name to be used within Facebook application URL:</p>\n";
		$FBML .= "<fb:editor action=\"?action=name\" labelwidth=\"100\">\n";
		$FBML .= "\t<fb:editor-text label=\"Name in Faceook URL\" name=\"name\" value=\"this_will_be_the_name\"/>\n";
		$FBML .= "\t<fb:editor-custom>\n";
		$FBML .= "\t</fb:editor-custom>\n";
		$FBML .= "\t<fb:editor-buttonset>\n";
		$FBML .= "\t\t<fb:editor-button value=\"Save\"/>\n";
		$FBML .= "\t</fb:editor-buttonset>\n";
		$FBML .= "</fb:editor>";

		print $FBML;

		break;

//-----------------------> ALLOW POPUP SCREEN

	case 'create':

		$allowed_apps = explode(',', $applications);
		if ($applications != '*' && !in_array($_POST['type'], $allowed_apps))
		{
			print 'You are not allowed to create application of this type!';
			break;
		}

//		$debug->print_r_html($_POST);

		$_SESSION['title']        = $_POST['title'];
		$_SESSION['author']       = $_POST['author'];
		$_SESSION['type']         = $_POST['type'];
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

//		$debug->print_r_html($_SESSION);

?>
	<div id="resp">
		<p>Now you must accept the creation<br/>of the application in the popup.</p>
	</div>
	<div id="alert">
	</div>
	<script>
	//<![CDATA[

		var new_app_id;
		var new_api_key;
<?php
		print "\t\tvar title = '".$_POST['title']."';\n";
?>
		try {
			Facebook.createApplication(title, onAppCreate);
		}
		catch(e) {
			document.setLocation('<?= FB_URL; ?>create_application.php?action=error');
		}

		function onAppCreate(response)
		{
			new_app_id = response['new_app_id'];
			new_api_key = response['new_api_key'];

			var my_div = document.getElementById('resp');
			my_div.setTextValue('New application info: new_app_id = '+new_app_id+', new_api_key = '+new_api_key);

			my_div = document.getElementById('alert');
			my_div.setTextValue('Please wait, while we synchronize the application!');

			document.setLocation('<?= FB_URL; ?>create_application.php?action=save&app_id=' + new_app_id + '&app_key=' + new_api_key);
/*
			// unable to set timeut as well

			var ajax = new Ajax();
			ajax.responseType = Ajax.FBML;
			ajax.ondone = function(data) {
				document.getElementById('resp').setInnerFBML(data);
			}
			ajax.onerror = function() {
				document.getElementById('resp').setTextValue('An error occured. Please check dashboard!');
				document.getElementById('alert').setTextValue('');
			}
			ajax.post('<?= MM_APP_URL; ?>_ajax/app_save.php?app_id=' + new_app_id + '&app_key=' + new_api_key);
*/
		}

	//]]>
	</script>
<?php

		break;
/*
-----------------------> ERROR SCREEN
*/
	case 'error':

		print "<p>!!! There were some errors creating the application. !!!</p>";
/*
-----------------------> START SCREEN
*/
	default:

		print "<fb:iframe name=\"settings\" frameborder=\"0\" style=\"width:760px;height:700px;\" src=\"".MM_APP_URL."_forms/app_create.php?sid=".$session_id."\" />\n";

		break;
}

require_once('fb_footer.php');

?>