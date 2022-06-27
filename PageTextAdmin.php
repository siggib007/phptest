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

	print "<p class=\"Header1\">Miscelaneous text administration</p>\n";

	if (($PostVarCount == 1) and ($btnSubmit == 'Go Back'))
	{
//		header("Location: $strPageURL");
	}

	if ($btnSubmit == 'Save')
	{
            $strTextName = CleanSQLInput(substr(trim($_POST['TextName']),0,49));
            $strContent = CleanSQLInput($_POST['txtDescr']);

            $strQuery = "update tblPageTexts set tPageTexts = '$strContent' WHERE vcTextName = '$strTextName';";
            UpdateSQL ($strQuery,"update");
	}


	//Print the normal form after update is complete.
	print "<table>\n<tr><th class=lbl>Update existing texts</th>";
        print "<th width = 100></th>";

	print "</tr>\n<tr>\n<td valign=\"top\">\n<table border = 0>\n";
	$strQuery = "SELECT vcTextName, vcTextDescr, tPageTexts FROM tblPageTexts;";
	if (!$Result = $dbh->query ($strQuery))
	{
		error_log ('Failed to fetch data. Error ('. $dbh->errno . ') ' . $dbh->error);
		error_log ($strQuery);
                print "<p class=\"Attn\" align=center>$ErrMsg</p>\n";
		exit(2);
	}
	while ($Row = $Result->fetch_assoc())
	{
            $strTextDescr = $Row['vcTextDescr'];
            $strTextName = $Row['vcTextName'];
            if ($WritePriv <=  $Priv)
            {
                print "<form method=\"POST\">\n";
                print "<tr valign=\"top\">\n";
                print "<td class=\"lbl\"><input type=\"hidden\" value=\"$strTextName\" name=\"TextName\"> </td>\n";
                print "<td>$strTextDescr</td>\n";
                print "<td><input type=\"Submit\" value=\"Edit\" name=\"btnSubmit\"></td>";
                print "</tr>\n";
                print "</form>\n";
            }
            else
            {
                    print "<tr><td>$strTextDescr</td></tr>\n";
            }
	}
	print "</table>\n";
        print "<form method=\"POST\">\n<input type=\"Submit\" value=\"Go Back\" name=\"btnSubmit\"></form>";
	print "</td>\n<td>\n</td>\n<td valign=\"top\">\n";
        if (isset($_POST['TextName']) and $_POST['btnSubmit'] == 'Edit')
        {
            $TextName = CleanReg($_POST['TextName']);
            $strQuery = "SELECT tPageTexts, vcTextDescr FROM tblPageTexts WHERE vcTextName = '$TextName';";
            if (!$Result = $dbh->query ($strQuery))
            {
                error_log ('Failed to fetch data. Error ('. $dbh->errno . ') ' . $dbh->error);
                error_log ($strQuery);
                print "<p class=\"Attn\" align=center>$ErrMsg</p>\n";
                exit(2);
            }
            $Row = $Result->fetch_assoc();
            $strPageText  = $Row['tPageTexts'];
            $strPageDescr = $Row['vcTextDescr'];
            print "<form method=\"POST\">\n";
            print "<input type=\"hidden\" value=\"$TextName\" name=\"TextName\">";
            print "<div class=\"lbl\">$strPageDescr</div>\n";
            print "<textarea name=\"txtDescr\" rows=\"20\" cols=\"90\">$strPageText</textarea>\n<br>\n";
            print "<div align=\"center\"><input type=\"Submit\" value=\"Save\" name=\"btnSubmit\"></div>\n";
            print "</form>\n";
            if (isset($_POST['iClassid']))
            {
                print "<form method=\"POST\">\n<input type=\"Submit\" value=\"Go Back\" name=\"btnSubmit\"></form>";
            }
        }
	print "</td>\n";
        print "</tr>\n";
        print "</table>";

	require("footer.php");
?>
