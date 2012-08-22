<?php

	// -> constants and includes <-

	define('ROOT', '/' . join('/', array_splice(preg_split(';/;', __FILE__, -1, PREG_SPLIT_NO_EMPTY), 0, 2)) . '/public_html/facebook/');

	require_once(ROOT.'_core/_constants.php');

	// -> session <-

	session_write_close();
	session_id($_GET['sid']);
	session_start();

	if (!$_SESSION['logged_in'])
		die('<p>You must be logged in to use this function!</p>');
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title>Memento Application Creator - Editor</title>
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
<script type="text/javascript" src="<?= MM_URL; ?>_assets/javascript/tiny_mce/tiny_mce.js"></script>
<script type="text/javascript">
//<![CDATA[
<?php
	print "\tsid = '".$_GET['sid']."';\n";
	print "\tbase = '".MM_URL."';\n";
	print "\tvar ajaxfilemanagerurl = \"".MM_URL."_assets/javascript/tiny_mce/plugins/ajaxfilemanager/ajaxfilemanager.php?sid=\" + sid;\n";
?>

	function docLoad() {

		tinyMCE.init({
			// General options
			mode : "textareas",
			theme : "advanced",
			elements : "ajaxfilemanager",
			file_browser_callback : "ajaxfilemanager",
			plugins : "pagebreak,style,layer,table,advhr,advimage,advlink,emotions,inlinepopups,insertdatetime,preview,media,searchreplace,paste,noneditable,visualchars,nonbreaking,xhtmlxtras,template,wordcount,advlist",
			// Theme options
			theme_advanced_buttons1 : "bold,italic,underline,strikethrough,|,justifyleft,justifycenter,justifyright,justifyfull,|,bullist,numlist,|,outdent,indent,blockquote,|,hr,|,link,unlink,anchor,|,search,replace,|,undo,redo",
			theme_advanced_buttons2 : "cut,copy,paste,pastetext,pasteword,|,tablecontrols,|,insertlayer,moveforward,movebackward,absolute",
			theme_advanced_buttons3 : "formatselect,fontselect,fontsizeselect,|,visualchars,nonbreaking,pagebreak,|,sub,sup,|,charmap,emotions,image,media,|,forecolor,backcolor,styleprops",
			theme_advanced_toolbar_location : "top",
			theme_advanced_toolbar_align : "left",
			theme_advanced_statusbar_location : "bottom",
			theme_advanced_resizing : false,
			theme_advanced_resize_horizontal : false,
			media_use_script : true,
			paste_use_dialog : false,
			apply_source_formatting : true,
			force_br_newlines : true,
			force_p_newlines : false,
			cleanup_on_startup : true,
			cleanup: true,
			document_base_url : base
		});

// THESE SHOULD BE UPDATED WHEN CLOSING CUSTOM IMAGE BROWSER PLUGIN
//			external_image_list_url : "_editor_list.php?type=image&sid=" + sid,
//			media_external_list_url : "_editor_list.php?type=media&sid=" + sid
//		});
	}

	function ajaxfilemanager(field_name, url, type, win) {

		switch (type) {
			case "image":
				break;
			case "media":
				break;
			case "flash": 
				break;
			case "file":
				break;
			default:
				return false;
		}

		tinyMCE.activeEditor.windowManager.open({
			url: ajaxfilemanagerurl + '&type=' + type,
			width: 700,
			height: 440,
			inline : "yes",
			close_previous : "no"
		},{
			window : win,
			input : field_name
		});
	}
//]]>
</script>
</head>
<body onload="docLoad();">
	<div>
		<form method="post" action="<?= FB_URL; ?>edit_application.php?action=save_page&id=<?= $_SESSION['page']['owner_id']; ?>" target="_top">
			<textarea id="editor" name="page" rows="15" cols="80" style="width:100%; height:600px;">
<?php
	if (isset($_SESSION['page']['id']))
	{
		$page_path = ROOT.$directory.'/assets/page/'.str_pad($_SESSION['page']['id'], 16, '0', STR_PAD_LEFT).'.html';
		print file_get_contents($page_path);
	}
?>
			</textarea>
			<br/>
			<input type="submit" class="fb_button" value="Save" />
		</form>
	</div>
</body>
</html>