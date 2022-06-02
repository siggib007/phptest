<?php
	require("header.php");
    if ($strReferer != $strPageURL and $PostVarCount > 0)
    {
        print "<p class=\"Error\">Invalid operation, Bad Reference!!!</p> ";
        exit;
    }
	$strFileName = "";
	$strAttach = "";

	print "<center>\n";
	print "<h1>Custom Email Composer</h2>\n";

    if ($btnSubmit == 'Send')
    {
        $strFromName = CleanSQLInput(substr(trim($_POST['txtFromName']),0,25));
        $strToName = CleanSQLInput(substr(trim($_POST['txtToName']),0,25));
        $strFromEmail = CleanSQLInput(substr(trim($_POST['txtFromEmail']),0,25));
        $strToEmail = CleanSQLInput(substr(trim($_POST['txtToEmail']),0,25));
        $strSubject = CleanSQLInput(substr(trim($_POST['txtSubject']),0,75));
        $FromEmail = "$strFromName|$strFromEmail";
        $toEmail = "$strToName|$strToEmail";

        $strAddHeader = CleanSQLInput($_POST['txtHeader']);
        $strHTMLMsg = CleanSQLInput($_POST['txtBody']);

        print "<h2>About to send the following email:</h2>\n";
        print "<b>From:</b> $FromEmail <br>\n";
        print "<b>To:</b> $toEmail <br>\n";
        print "<b>Subject:</b> $strSubject <br>\n";
        print "<b>Additional Email Headers:</b> $strAddHeader <br>\n";
        print "<h3>Body:</h3>\n$strHTMLMsg<br>\n";

		$count = SendHTMLAttach ($strHTMLMsg, $FromEmail, $toEmail, $strSubject, $strFileName, $strAttach,$strAddHeader);
		print "<h3>Successfully sent $count recepients</h3>\n";
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
		print "<span class=\"lbl\">Header:</span>\n";
		print "<input type=\"text\" name=\"txtHeader\" size=\"75\">\n<br>\n";
		print "<span class=\"lbl\"><br>Message body HTML:</br></span>\n";
		print "<textarea name=\"txtBody\" rows=\"20\" cols=\"120\">$strPageText</textarea>\n<br>\n";
		print "<div align=\"center\"><input type=\"Submit\" value=\"Send\" name=\"btnSubmit\"></div>\n";
		print "</form>\n";
	}
	print "</center>";

	// $strHTMLMsg = "This is a test of the swift mail system with speacial headers, remote image and all.<br><img src=\"http://www.studio-b-dance.com/img/StudioB320.jpg\" height=\"100\"/>";
	// $FromEmail = "Geek Web Master|siggi@supergeek.us";
	// $toEmail = "Siggi Bjarnason|siggi@bjarnason.us";
	// $strSubject = "Geeky Sendmail function test with special headers";
	// $strAddHeader = "X-Testing:This is my test header";

	require("footer.php");
?>