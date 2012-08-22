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
<html xmlns="http://www.w3.org/1999/xhtml" style="margin:0px; padding:0px; overflow:hidden;">
<head>
<title>Memento Application Creator - Application Form</title>
<link href="<?= MM_URL; ?>_assets/style/facebook-form.css" title="form_css" rel="stylesheet" type="text/css">
<script type="text/javascript">
//<![CDATA[
<?php
	print "\tvar sid = '".$_GET['sid']."';\n";
	print "\tvar ajaxfilemanagerurl = \"".MM_URL."_assets/javascript/tiny_mce/plugins/ajaxfilemanager/ajaxfilemanager.php?editor=form&type=image&sid=\" + sid;\n";
?>
	function browseFile(passed_element)
	{
		var win = window.open(ajaxfilemanagerurl + '&elementId=' + passed_element, 'ajaxFileImageManager', 'status=no,toolbar=no,menubar=no,location=no,width=782,height=500');
		return false;
	}

	function fileCallback(url, elementId)
	{
		if (document.getElementById(elementId))
			document.getElementById(elementId).value = url;
	}

	// prevent enter from poping up browser
	function stopRKey(evt)
	{
		var evt = (evt) ? evt : ((event) ? event : null);
		var node = (evt.target) ? evt.target : ((evt.srcElement) ? evt.srcElement : null);
		if ((evt.keyCode == 13) && (node.type=="text"))
			return false;
	}
	document.onkeypress = stopRKey;
//]]>
</script>
</head>
<body>
<div class="box">
	<h3>Application Settings</h3>
	<form action="<?= FB_URL; ?>create_application.php?action=create" method="post" target="_top">
		<label><span>Title</span>
			<input type="text" name="title" class="input-text" value="Ask the Lama" />
		</label>
		<label><span>Author</span>
			<input type="text" name="author" class="input-text" value="Mr.Memento"/>	
		</label>
		<label><span>Type</span>
			<select name="type">
<?php

	$allowed_apps = explode(',', $applications);
	$selected     = false;
	foreach ($SKELETONS as $index => $skeleton)
	{
		if ($applications = '*' || in_array($index, $allowed_apps))
		{
			if ($selected)
			{
				print "\t\t\t\t<option value=\"".$index."\">".$skeleton['name']."</option>\n";
			}
			else
			{
				print "\t\t\t\t<option value=\"".$index."\" selected>".$skeleton['name']."</option>\n";
				$selected = true;
			}
		}
	}
?>
			</select>
		</label>
		<label><span>Language</span>
			<select name="language">
				<option value="EN" selected>English</option>
				<option value="HU">Hungarian</option>
			</select>
		</label>
		<label><span>Contact Email</span>
			<input type="text" name="contact_mail" class="input-text" value="contact@askthelama.com"/>
		</label>
		<label><span>Support Email</span>
			<input type="text" name="support_mail" class="input-text" value="support@askthelama.com"/>
		</label>
		<label><span>Link URL</span>
			<input type="text" name="link" class="input-text" value="http://www.retek.hu/"/>
		</label>
		<label><span>Icon URL</span>
			<input type="text" id="icon_url" name="icon_url" class="input-text" value="http://www.retek.hu/retek.jpg"/>
		</label>
		<div align="right" class="btn_right">	
			<button class="form_btn" onclick="return browseFile('icon_url');">Browse</button>
		</div>
		<label><span>Avatar URL</span>
			<input type="text" id="avatar_url" name="avatar_url" class="input-text" value="http://www.retek.hu/retek.jpg"/>
		</label>
		<div align="right" class="btn_right">
			<button class="form_btn" onclick="return browseFile('avatar_url');">Browse</button>
		</div>
		<label><span>Button Text</span>
		 	<input type="text" name="button_text" class="input-text" value="Ask the Lama"/>
		</label>
		<label><span>Publish Text</span>
			<input type="text" name="post_text" class="input-text" value="Post Lama's answer on your wall"/>
		</label>
		<label><span>Description</span>
			<textarea rows="3" cols="20" type="text" class="input-text" name="description">Sometimes everybody needs guidance... Why not Ask the Lama? ;)</textarea>
		</label>
		<label><span>Google Analytics</span>
			<input type="text" name="ga_code" class="input-text" value=""/>
		</label>
		<br/><br/>
		<div align="right" class="btn_right">
			<input type="submit" class="form_btn" value="Create"/>
		</div>
	</form>
</div>
</body>
</html>