<?php
	require("header.php");

	if ($strReferer != $strPageURL and $PostVarCount > 0)
	{
		print "<p class=\"Error\">Invalid operation, Bad Reference!!!</p> ";
		exit;
	}
	if (isset($_POST['btnSubmit']))
	{
		$btnSubmit = $_POST['btnSubmit'];
	}
	else
	{
		$btnSubmit = "";
	}

	print "<p class=\"Header1\">FAQ Administration</p>\n";

	if (($PostVarCount == 1) and ($btnSubmit == 'Go Back'))
	{
//		header("Location: $strPageURL");
	}

	if ($btnSubmit == 'Save')
	{
		$iFAQID = CleanSQLInput(substr(trim($_POST['iFAQid']),0,49));
		$strQuestion = CleanSQLInput(substr(trim($_POST['txtQuestion']),0,49));
		$strAnswer = CleanSQLInput($_POST['txtAnswer']);

		$strQuery = "update tblFAQ set vcQuestion = '$strQuestion', tAnswer = '$strAnswer' where iFAQid = $iFAQID;";
		UpdateSQL ($strQuery,"update");
//		print "<form method=\"POST\">\n<input type=\"Submit\" value=\"Go Back\" name=\"btnSubmit\"></form>";
	}

	if ($btnSubmit == 'Delete')
	{
		$iFAQID = CleanSQLInput(substr(trim($_POST['iFAQid']),0,49));

		$strQuery = "delete from tblFAQ where iFAQid = $iFAQID;";
		UpdateSQL ($strQuery,"delete");
	}

	if ($btnSubmit == 'Insert')
	{
		$strQuestion = CleanSQLInput(substr(trim($_POST['txtQuestion']),0,49));
		$strAnswer = CleanSQLInput($_POST['txtAnswer']);

		if ($iSortNum == '')
		{
			$iSortNum = 0;
		}

		if ($strQuestion == '')
		{
			print "<p>Please provide a Question to insert</p>\n";
		}
		else
		{
			$strQuery = "insert tblFAQ (vcQuestion, tAnswer) values ('$strQuestion','$strAnswer');";
			UpdateSQL ($strQuery,"insert");
		}
	}

	//Print the normal form after update is complete.
	print "<table>\n<tr><th class=lbl>Update existing FAQ</th>";
        print "<th width = 100></th>";
        if (!isset($_POST['iFAQid']))
        {
            print "<th class=lbl>Or Insert New one</th>";
        }
	print "</tr>\n<tr>\n<td valign=\"top\">\n<table border = 0>\n";
	$strQuery = "select iFAQid, vcQuestion, tAnswer from tblFAQ;";
	if (!$Result = $dbh->query ($strQuery))
	{
		error_log ('Failed to fetch data. Error ('. $dbh->errno . ') ' . $dbh->error);
		error_log ($strQuery);
                print "<p class=\"Attn\" align=center>$ErrMsg</p>\n";
		exit(2);
	}
	while ($Row = $Result->fetch_assoc())
	{
		$vcQuestion = $Row['vcQuestion'];
		$iFAQid = $Row['iFAQid'];
		if ($WritePriv <=  $Priv)
		{
			print "<form method=\"POST\">\n";
			print "<tr valign=\"top\">\n";
			print "<td class=\"lbl\"><input type=\"hidden\" value=\"$iFAQid\" name=\"iFAQid\"> </td>\n";
			print "<td>$vcQuestion</td>\n";
			print "<td><input type=\"Submit\" value=\"Edit\" name=\"btnSubmit\"></td>";
                        print "<td><input type=\"Submit\" value=\"Delete\" name=\"btnSubmit\"></td>";
			print "</tr>\n";
			print "</form>\n";
		}
		else
		{
			print "<tr><td>$vcQuestion</td></tr>\n";
		}
	}
	print "</table>\n";
        print "<form method=\"POST\">\n<input type=\"Submit\" value=\"Go Back\" name=\"btnSubmit\"></form>";
	print "</td>\n<td>\n</td>\n<td valign=\"top\">\n";
        if (isset($_POST['iFAQid']) and $_POST['btnSubmit'] == 'Edit')
        {
            $iFAQid = intval($_POST['iFAQid']);
            $strQuery = "select iFAQid, vcQuestion, tAnswer from tblFAQ where iFAQid = $iFAQid;";
            if (!$Result = $dbh->query ($strQuery))
            {
                error_log ('Failed to fetch data. Error ('. $dbh->errno . ') ' . $dbh->error);
                error_log ($strQuery);
                print "<p class=\"Attn\" align=center>$ErrMsg</p>\n";
                exit(2);
            }
            $Row = $Result->fetch_assoc();
            $strQuestion = $Row['vcQuestion'];
            $strClassDescr = $Row['tAnswer'];
            $strBtnLabel = "Save";
        }
         else
        {
            $strQuestion = "";
            $strClassDescr = "";
            $iFAQid = "";
            $strBtnLabel = "Insert";
        }
	print "<form method=\"POST\">\n";
        print "<input type=\"hidden\" value=\"$iFAQid\" name=\"iFAQid\">";
	print "<span class=\"lbl\">Class Name:</span>\n";
	print "<input type=\"text\" name=\"txtQuestion\" size=\"70\" value=\"$strQuestion\"><br>\n";
	print "<div class=\"lbl\">Description:</div>\n";
        print "<textarea name=\"txtAnswer\" rows=\"20\" cols=\"80\">$strClassDescr</textarea>\n<br>\n";
	print "<div align=\"center\"><input type=\"Submit\" value=\"$strBtnLabel\" name=\"btnSubmit\"></div>\n";
	print "</form>\n";
        if (isset($_POST['iFAQid']))
        {
            print "<form method=\"POST\">\n<input type=\"Submit\" value=\"Go Back\" name=\"btnSubmit\"></form>";
        }
	print "</td>\n";
        print "</tr>\n";
        print "</table>";

	require("footer.php");
?>
