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

	print("<p class=\"Header1\">Dance Interests</p>\n");

	if (($PostVarCount == 1) and ($btnSubmit == 'Go Back'))
	{
		header("Location: $strPageURL");
	}    
	
	if ($btnSubmit == 'Save')
	{
		$iSortNum = CleanSQLInput(substr(trim($_POST['iSortNum']),0,49));
		$strInterests = CleanSQLInput(substr(trim($_POST['txtInterests']),0,49));
		$InterestID = CleanSQLInput(substr(trim($_POST['iInterestID']),0,49));
		
		if ($iSortNum == '')
		{
			$iSortNum = 0;
		}
		
		$strQuery = "update tblInterests set vcInterest = '$strInterests', iSortNum = $iSortNum where iInterestId = $InterestID;";
		UpdateSQL ($strQuery,"update");
		//print "<form method=\"POST\">\n<input type=\"Submit\" value=\"Go Back\" name=\"btnSubmit\"></form>";
	}
	
	if ($btnSubmit == 'Delete')
	{
		$InterestID = substr(trim($_POST['iInterestID']),0,49);
		
		$strQuery = "delete from tblInterests where iInterestId = $InterestID;";
		UpdateSQL ($strQuery,"delete");
	}

	if ($btnSubmit == 'Insert')
	{
		$iSortNum = CleanSQLInput(substr(trim($_POST['iSortNum']),0,49));
		$strInterests = CleanSQLInput(substr(trim($_POST['txtInterests']),0,49));
		
		if ($iSortNum == '')
		{
			$iSortNum = 0;
		}
		
		if ($strInterests == '')
		{
			print "<p>Please provide an dance interest to insert</p>\n";
		}
		else
		{
			$strQuery = "insert tblInterests (vcInterest, iSortNum) values ('$strInterests',$iSortNum);";
			UpdateSQL ($strQuery,"insert");
		}
	}
	
	//Print the normal form after update is complete.
	print "<table>\n<tr><th class=lbl>Update existing Interest</th><th width = 100></th><th class=lbl>Or Insert New one</th></tr>\n";
	print "<tr>\n<td>\n<table border = 0>\n<tr><th></th><th class=lbl>Interest Name</th><th class=lbl>Sort order</th></tr>\n";
	$strQuery = "SELECT vcInterest, iSortNum, iInterestId FROM tblInterests order by iSortNum;";
	if (!$Result = $dbh->query ($strQuery))
	{
		error_log ('Failed to fetch data. Error ('. $dbh->errno . ') ' . $dbh->error);
		error_log ($strQuery);
		exit(2);
	}
	while ($Row = $Result->fetch_assoc())
	{
		$strIntName = $Row['vcInterest'];
		$iSortNum = $Row['iSortNum'];
		$InterestID = $Row['iInterestId'];
		if ($WritePriv <=  $Priv)
		{
			print "<form method=\"POST\">\n";
			print "<tr valign=\"top\">\n";
			print "<td class=\"lbl\"><input type=\"hidden\" value=\"$InterestID\" name=\"iInterestID\"> </td>\n";
			print "<td><input type=\"text\" value=\"$strIntName\" name=\"txtInterests\" size=\"30\" ></td>\n";
			print "<td><input type=\"text\" value=\"$iSortNum\" name=\"iSortNum\" size=\"13\" ></td>\n";
			print "<td><input type=\"Submit\" value=\"Save\" name=\"btnSubmit\"></td>";
			print "<td><input type=\"Submit\" value=\"Delete\" name=\"btnSubmit\"></td>";
			print "</tr>\n";
			print "</form>\n";
		}
		else
		{
			print "$strIntName : $iSortNum<br>\n";
		}		
	}
	print "</table>\n";	    
	print "</td>\n<td>\n</td>\n<td valign=\"top\">\n";
	print "<form method=\"POST\">\n";
	print "<table>\n";
	print "<tr>\n<td align = right class = lbl>Interest: </td>\n";
	print "<td><input type=\"text\" name=\"txtInterests\" size=\"30\" ></td>\n</tr>\n";
	print "<tr>\n<td align = right class = lbl>Sort Order: </td>\n";
	print "<td><input type=\"text\" name=\"iSortNum\" size=\"13\" ></td></tr>\n";
	print "<tr><td colspan=2 align=center><input type=\"Submit\" value=\"Insert\" name=\"btnSubmit\"></td></tr>\n";
	print "</table>\n";
	print "</form>\n</td>\n</tr>\n</table>";
	
	require("footer.php"); 
?>
