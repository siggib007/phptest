<?php
	require("header.php");

	if ($strReferer != $strPageURL and $PostVarCount > 0)
	{
		print "<p class=\"Error\">Invalid operation, Bad Reference!!!</p> ";
		exit;
	}

	print("<p class=\"Header1\">Link Administration</p>\n");

	if (($PostVarCount == 1) and ($btnSubmit == 'Go Back'))
	{
		header("Location: $strPageURL");
	}

	if (isset($_POST['btnSubmit']))
	{
		$btnSubmit = $_POST['btnSubmit'];
	}
	else
	{
		$btnSubmit = "";
	}

	if ($btnSubmit == 'Save')
	{
		$iLinkID = substr(trim($_POST['iLinkID']),0,49);
		$vcLink = CleanSQLInput(substr(trim($_POST['txtLink']),0,499));
		$vcLinkName = CleanSQLInput(substr(trim($_POST['txtName']),0,49));
		$vcComment = CleanSQLInput(substr(trim($_POST['txtComment']),0,499));
		$iLinkCat = substr(trim($_POST['cmbCategory']),0,49);

		$strQuery = "update tbllinks set iCategory ='$iLinkCat', vcLink='$vcLink', vcName='$vcLinkName', vcComment='$vcComment' where iLinkID=$iLinkID;";
		UpdateSQL ($strQuery,"update");
	}

	if ($btnSubmit == 'Delete')
	{
		$iLinkID = inval(substr(trim($_POST['iLinkID']),0,49));

		$strQuery = "delete from tbllinks where iLinkID = $iLinkID;";
		UpdateSQL ($strQuery,"delete");
	}

	if ($btnSubmit == 'Insert')
	{
		$vcLink = CleanSQLInput(substr(trim($_POST['txtLink']),0,499));
		$vcLinkName = CleanSQLInput(substr(trim($_POST['txtName']),0,49));
		$vcComment = CleanSQLInput(substr(trim($_POST['txtComment']),0,49));
		$iLinkCat = intval(substr(trim($_POST['cmbCategory']),0,49));

		if ($vcLink == '' or $vcLinkName == '')
		{
			print "<p>Please provide the link and a link name to insert</p>\n";
		}
		else
		{
			$strQuery = "insert tbllinks (iCategory, vcLink, vcName, vcComment) values ($iLinkCat, '$vcLink', '$vcLinkName', '$vcComment');";
			UpdateSQL ($strQuery,"insert");
		}
		//print "<form method=\"POST\">\n<input type=\"Submit\" value=\"Go Back\" name=\"btnSubmit\"></form>";
	}

	//Print the normal form after update is complete.
	print "<form method=\"POST\">\n";
	print "<table>\n";
	print "<tr><td colspan=2 align=center class=lbl>Insert New Link</td></tr>\n";
	$strQuery = "select iCatID, vcCategory from tbllinkcategory order by vcCategory;";
	//print "$strQuery<br>\n";
	print "<tr>\n<td align = right class = lbl>Link Category: </td>\n";
	print("<td>\n<select size=\"1\" name=\"cmbCategory\">\n");
	if (!$Result2 = $dbh->query ($strQuery))
	{
		error_log ('Failed to fetch data. Error ('. $dbh->errno . ') ' . $dbh->error);
		error_log ($strQuery);
		exit(2);
	}

	while ($Row2 = $Result2->fetch_assoc())
	{
		if ($Row2['iCatID'] == $Row['iCategory'])
		{
			print("<option selected value=\"{$Row2['iCatID']}\">{$Row2['vcCategory']}</option>\n");
		}
		else
		{
			print("<option value=\"{$Row2['iCatID']}\">{$Row2['vcCategory']}</option>\n");
		}
	}
	print("</select>\n</td>");
	print "<tr>\n<td align = right class = lbl>Link: </td>\n";
	print "<td><input type=\"text\" name=\"txtLink\" size=\"60\" ></td>\n</tr>\n";
	print "<tr>\n<td align = right class = lbl>Name: </td>\n";
	print "<td><input type=\"text\" name=\"txtName\" size=\"60\" ></td></tr>\n";
	print "<tr>\n<td align = right class = lbl>Comment: </td>\n";
	print "<td><input type=\"text\" name=\"txtComment\" size=\"60\" ></td></tr>\n";
	print "<tr><td colspan=2 align=center><input type=\"Submit\" value=\"Insert\" name=\"btnSubmit\"></td></tr>\n";
	print "</table>\n";
	print "</form>\n";

	print "<div class=lbl>Or Update existing Links</th><th width = 100></div>\n";
	print "<table border = 0>\n";
	print "<tr><th></th><th class=lbl>Category</th><th class=lbl>Link</th><th class=lbl>Name</th><th class=lbl>Comment</th></tr>\n";
	$strQuery = "SELECT iLinkID, iCategory, vcLink, vcName, vcComment FROM tbllinks;";
	if (!$Result = $dbh->query ($strQuery))
	{
		error_log ('Failed to fetch data. Error ('. $dbh->errno . ') ' . $dbh->error);
		error_log ($strQuery);
		exit(2);
	}
	while ($Row = $Result->fetch_assoc())
	{
		if ($WritePriv <=  $Priv)
		{
			print "<form method=\"POST\">\n";
			print "<tr valign=\"top\">\n";
			print "<td class=\"lbl\"><input type=\"hidden\" value=\"$Row[iLinkID]\" name=\"iLinkID\"> </td>\n";
			$strQuery = "select iCatID, vcCategory from tbllinkcategory order by vcCategory;";
			//print "$strQuery<br>\n";
			print("<td>\n<select size=\"1\" name=\"cmbCategory\">\n");
			if (!$Result2 = $dbh->query ($strQuery))
			{
				error_log ('Failed to fetch data. Error ('. $dbh->errno . ') ' . $dbh->error);
				error_log ($strQuery);
				exit(2);
			}
			while ($Row2 = $Result2->fetch_assoc())
			{
				if ($Row2['iCatID'] == $Row['iCategory'])
				{
					print("<option selected value=\"{$Row2['iCatID']}\">{$Row2['vcCategory']}</option>\n");
				}
				else
				{
					print("<option value=\"{$Row2['iCatID']}\">{$Row2['vcCategory']}</option>\n");
				}
			}
			print("</select>\n</td>");
			print "<td><input type=\"text\" value=\"$Row[vcLink]\" name=\"txtLink\" size=\"50\" ></td>\n";
			print "<td><input type=\"text\" value=\"$Row[vcName]\" name=\"txtName\" size=\"30\" ></td>\n";
			print "<td><input type=\"text\" value=\"$Row[vcComment]\" name=\"txtComment\" size=\"50\" ></td>\n";
			print "<td><input type=\"Submit\" value=\"Save\" name=\"btnSubmit\"></td>";
			print "<td><input type=\"Submit\" value=\"Delete\" name=\"btnSubmit\"></td>";
			print "</tr>\n";
			print "</form>\n";
		}
		else
		{
			print "<tr><td>$Row[vcLink]</td><td>$Row[vcName]</td><td>$Row[vcComment]</td></tr>\n";
		}
	}
	print "</table>\n";

	require("footer.php");
?>
