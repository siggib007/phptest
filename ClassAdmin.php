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

	print "<p class=\"Header1\">Class Administration</p>\n";

	if (($PostVarCount == 1) and ($btnSubmit == 'Go Back'))
	{
//		header("Location: $strPageURL");
	}    
	
	if ($btnSubmit == 'Save')
	{
		$iClassID = CleanSQLInput(substr(trim($_POST['iClassid']),0,49));
		$strClassName = CleanSQLInput(substr(trim($_POST['txtClassName']),0,49));
		$strDescr = CleanSQLInput($_POST['txtDescr']);
		
		$strQuery = "update tblClasses set vcClassName = '$strClassName', tClassDescr = '$strDescr' where iClassid = $iClassID;";
		UpdateSQL ($strQuery,"update");
//		print "<form method=\"POST\">\n<input type=\"Submit\" value=\"Go Back\" name=\"btnSubmit\"></form>";
	}
	
	if ($btnSubmit == 'Delete')
	{
		$iClassID = CleanSQLInput(substr(trim($_POST['iClassid']),0,49));
		
		$strQuery = "delete from tblClasses where iClassid = $iClassID;";
		UpdateSQL ($strQuery,"delete");
	}

	if ($btnSubmit == 'Insert')
	{
		$strClassName = CleanSQLInput(substr(trim($_POST['txtClassName']),0,49));
		$strDescr = CleanSQLInput($_POST['txtDescr']);
		
		if ($iSortNum == '')
		{
			$iSortNum = 0;
		}
		
		if ($strClassName == '')
		{
			print "<p>Please provide a class name to insert</p>\n";
		}
		else
		{
			$strQuery = "insert tblClasses (vcClassName, tClassDescr) values ('$strClassName','$strDescr');";
			UpdateSQL ($strQuery,"insert");
		}
	}
	
	//Print the normal form after update is complete.
	print "<table>\n<tr><th class=lbl>Update existing Class Descriptions</th>";
        print "<th width = 100></th>";
        if (!isset($_POST['iClassid']))
        {
            print "<th class=lbl>Or Insert New one</th>";   
        }
	print "</tr>\n<tr>\n<td valign=\"top\">\n<table border = 0>\n";
	$strQuery = "select iClassid, vcClassName, tClassDescr from tblClasses;";
	if (!$Result = $dbh->query ($strQuery))
	{
		error_log ('Failed to fetch data. Error ('. $dbh->errno . ') ' . $dbh->error);
		error_log ($strQuery);
                print "<p class=\"Attn\" align=center>$ErrMsg</p>\n";
		exit(2);
	}
	while ($Row = $Result->fetch_assoc())
	{
		$vcClassName = $Row['vcClassName'];
		$iClassid = $Row['iClassid'];
		if ($WritePriv <=  $Priv)
		{
			print "<form method=\"POST\">\n";
			print "<tr valign=\"top\">\n";
			print "<td class=\"lbl\"><input type=\"hidden\" value=\"$iClassid\" name=\"iClassid\"> </td>\n";
			print "<td>$vcClassName</td>\n";
			print "<td><input type=\"Submit\" value=\"Edit\" name=\"btnSubmit\"></td>";
                        print "<td><input type=\"Submit\" value=\"Delete\" name=\"btnSubmit\"></td>";
			print "</tr>\n";
			print "</form>\n";
		}
		else
		{
			print "<tr><td>$vcClassName</td></tr>\n";
		}		
	}
	print "</table>\n";	    
        print "<form method=\"POST\">\n<input type=\"Submit\" value=\"Go Back\" name=\"btnSubmit\"></form>";
	print "</td>\n<td>\n</td>\n<td valign=\"top\">\n";
        if (isset($_POST['iClassid']) and $_POST['btnSubmit'] == 'Edit')
        {
            $iClassid = $_POST['iClassid'];
            $strQuery = "select iClassid, vcClassName, tClassDescr from tblClasses where iClassid = $iClassid;";
            if (!$Result = $dbh->query ($strQuery))
            {
                error_log ('Failed to fetch data. Error ('. $dbh->errno . ') ' . $dbh->error);
                error_log ($strQuery);
                print "<p class=\"Attn\" align=center>$ErrMsg</p>\n";
                exit(2);
            }
            $Row = $Result->fetch_assoc();
            $strClassName = $Row['vcClassName'];
            $strClassDescr = $Row['tClassDescr'];    
            $strBtnLabel = "Save";
        }
         else
        {
            $strClassName = "";
            $strClassDescr = "";                         
            $iClassid = "";
            $strBtnLabel = "Insert";
        }
	print "<form method=\"POST\">\n";
        print "<input type=\"hidden\" value=\"$iClassid\" name=\"iClassid\">";
	print "<span class=\"lbl\">Class Name:</span>\n";
	print "<input type=\"text\" name=\"txtClassName\" size=\"70\" value=\"$strClassName\"><br>\n";
	print "<div class=\"lbl\">Description:</div>\n";
        print "<textarea name=\"txtDescr\" rows=\"20\" cols=\"80\">$strClassDescr</textarea>\n<br>\n";        
	print "<div align=\"center\"><input type=\"Submit\" value=\"$strBtnLabel\" name=\"btnSubmit\"></div>\n";
	print "</form>\n";
        if (isset($_POST['iClassid']))
        {
            print "<form method=\"POST\">\n<input type=\"Submit\" value=\"Go Back\" name=\"btnSubmit\"></form>";   
        }
	print "</td>\n";
        print "</tr>\n";
        print "</table>";
        
	require("footer.php"); 
?>
