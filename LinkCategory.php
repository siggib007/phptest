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

	print "<p class=\"Header1\">Link Categories</p>\n";

	if (($PostVarCount == 1) and ($btnSubmit == 'Go Back'))
	{
		header("Location: $strPageURL");
	}

	if ($btnSubmit == 'Save')
	{
		$iSortNum = CleanSQLInput(substr(trim($_POST['iSortNum']),0,49));
		$strLinkCat = CleanSQLInput(substr(trim($_POST['txtLinkCat']),0,49));
		$iLinkCatID = CleanSQLInput(substr(trim($_POST['iLinkCatID']),0,49));

		if ($iSortNum == '')
		{
			$iSortNum = 0;
		}

		$strQuery = "update tbllinkcategory set vcCategory = '$strLinkCat', iSortNum = $iSortNum where iCatID = $iLinkCatID;";
		UpdateSQL ($strQuery,"update");
		//print "<form method=\"POST\">\n<input type=\"Submit\" value=\"Go Back\" name=\"btnSubmit\"></form>";
	}

	if ($btnSubmit == 'Delete')
	{
		$iLinkCatID = intval(substr(trim($_POST['iLinkCatID']),0,49));

		$strQuery = "delete from tbllinkcategory where iCatID = $iLinkCatID;";
		UpdateSQL ($strQuery,"delete");
	}

	if ($btnSubmit == 'Insert')
	{
		$iSortNum = CleanSQLInput(substr(trim($_POST['iSortNum']),0,49));
		$strLinkCat = CleanSQLInput(substr(trim($_POST['txtLinkCat']),0,49));

		if ($iSortNum == '')
		{
			$iSortNum = 0;
		}

		if ($strLinkCat == '')
		{
			print "<p>Please provide a document catergory name to insert</p>\n";
		}
		else
		{
			$strQuery = "insert tbllinkcategory (vcCategory, iSortNum) values ('$strLinkCat',$iSortNum);";
			UpdateSQL ($strQuery,"insert");
		}
	}

	//Print the normal form after update is complete.
	print "<table>\n<tr><th class=lbl>Update existing Category</th><th width = 100></th><th class=lbl>Or Insert New one</th></tr>\n";
	print "<tr>\n<td>\n<table border = 0>\n<tr><th></th><th class=lbl>Category Name</th><th class=lbl>Sort order</th></tr>\n";
	$strQuery = "SELECT vcCategory, iSortNum, iCatID FROM tbllinkcategory order by iSortNum;";
	if (!$Result = $dbh->query ($strQuery))
	{
		error_log ('Failed to fetch data. Error ('. $dbh->errno . ') ' . $dbh->error);
		error_log ($strQuery);
		exit(2);
	}
	while ($Row = $Result->fetch_assoc())
	{
		$vcLinkCat = $Row['vcCategory'];
		$iSortNum = $Row['iSortNum'];
		$iLinkCatID = $Row['iCatID'];
		if ($WritePriv <=  $Priv)
		{
			print "<form method=\"POST\">\n";
			print "<tr valign=\"top\">\n";
			print "<td class=\"lbl\"><input type=\"hidden\" value=\"$iLinkCatID\" name=\"iLinkCatID\"> </td>\n";
			print "<td><input type=\"text\" value=\"$vcLinkCat\" name=\"txtLinkCat\" size=\"30\" ></td>\n";
			print "<td><input type=\"text\" value=\"$iSortNum\" name=\"iSortNum\" size=\"13\" ></td>\n";
			print "<td><input type=\"Submit\" value=\"Save\" name=\"btnSubmit\"></td>";
			print "<td><input type=\"Submit\" value=\"Delete\" name=\"btnSubmit\"></td>";
			print "</tr>\n";
			print "</form>\n";
		}
		else
		{
			print "$vcLinkCat : $iSortNum<br>\n";
		}
	}
	print "</table>\n";
	print "</td>\n<td>\n</td>\n<td>\n";
	print "<form method=\"POST\">\n";
	print "<table>\n";
	print "<tr>\n<td align = right class = lbl>Category: </td>\n";
	print "<td><input type=\"text\" name=\"txtLinkCat\" size=\"30\" ></td>\n</tr>\n";
	print "<tr>\n<td align = right class = lbl>Sort Order: </td>\n";
	print "<td><input type=\"text\" name=\"iSortNum\" size=\"13\" ></td></tr>\n";
	print "<tr><td colspan=2 align=center><input type=\"Submit\" value=\"Insert\" name=\"btnSubmit\"></td></tr>\n";
	print "</table>\n";
	print "</form>\n</td>\n</tr>\n</table>";

	require("footer.php");
?>
