
function selectFile(url)
{
//	window.opener.document.getElementById(elementId).value = url;
	window.close();
	window.opener.fileCallback(url, elementId);
}

function cancelSelectFile()
{
  // close popup window
  window.close();
  return false;
}