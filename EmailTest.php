<?php
	require("header.php");
	if ($strReferer != $strPageURL and $PostVarCount > 0)
	{
			print "<p class=\"Error\">Invalid operation, Bad Reference!!!</p> ";
			exit;
	}
	$strFileName = "";
	$strAttach = "";
  if (isset($_POST['btnSubmit']))
  {
    $btnSubmit = $_POST['btnSubmit'];
  }
  else
  {
    $btnSubmit = "";
  }
  $TestWarn = $TextArray["TestWarn"];
	print "<p class='Header1'>Custom Email Composer</p>\n";
	print "<p class='LargeAttnCenter'>Do not expose this site to the internet</p>\n";
	print "<p class='BlueNote'>$TestWarn</p>\n";

	print "<center>\n";
	print "<p>Fill out this form and hit send and your message will be sent through your configured email service<p/>\n";
	if ($btnSubmit == 'Send')
	{
		$strFromName = CleanReg(substr(trim($_POST['txtFromName']),0,25));
		$strToName = CleanReg(substr(trim($_POST['txtToName']),0,25));
		$strFromEmail = CleanReg(substr(trim($_POST['txtFromEmail']),0,25));
		$strToEmail = CleanReg(substr(trim($_POST['txtToEmail']),0,25));
		$strSubject = CleanReg(substr(trim($_POST['txtSubject']),0,75));
		$FromEmail = "$strFromName|$strFromEmail";
		$toEmail = "$strToName|$strToEmail";

		$strAddHeader = CleanReg($_POST['txtHeader']);
		$strHTMLMsg = $_POST['txtBody'];

		$arrAddHeader = explode("\n",$strAddHeader);

		print "<h2>About to send the following email:</h2>\n";
		print "<b>From:</b> $FromEmail <br>\n";
		print "<b>To:</b> $toEmail <br>\n";
		print "<b>Subject:</b> $strSubject <br>\n";
		print "<b>Additional Email Headers:</b> $strAddHeader <br>\n";
		print "<h3>Body:</h3>\n$strHTMLMsg<br>\n";
		$response = SendHTMLAttach ($strHTMLMsg, $FromEmail, $toEmail, $strSubject, $strFileName, $strAttach,$arrAddHeader);
		print "<h3>$response</h3>\n";
    }
    else
    {
		print "<form method=\"POST\">\n";
		print "<span class=\"lbl\">From Name:</span>\n";
		print "<input type=\"text\" name=\"txtFromName\" size=\"25\">\n";
		print "<span class=\"lbl\">From email:</span>\n";
		print "<input type=\"text\" name=\"txtFromEmail\" size=\"25\"></br>\n";
		print "<span class=\"lbl\">To Name:</span>\n";
		print "<input type=\"text\" name=\"txtToName\" size=\"25\">\n";
		print "<span class=\"lbl\">To email:</span>\n";
		print "<input type=\"text\" name=\"txtToEmail\" size=\"25\"></br>\n";
		print "<span class=\"lbl\">Subject:</span>\n";
		print "<input type=\"text\" name=\"txtSubject\" size=\"75\">\n<br>\n";
		print "<span class=\"lbl\">Additional Custom Headers:<br><i>Put each header on their own line in the format of name:value</i></span><br>\n";
		print "<textarea name=\"txtHeader\" rows=\"5\" cols=\"75\"></textarea>\n<br>\n";
		print "<span class=\"lbl\"><br>Message body HTML:</br></span>\n";
		print "<textarea name=\"txtBody\" rows=\"20\" cols=\"120\"></textarea>\n<br>\n";
		print "<div align=\"center\"><input type=\"Submit\" value=\"Send\" name=\"btnSubmit\"></div>\n";
		print "</form>\n";
	}
	print "</center>";

	require("footer.php");
?>