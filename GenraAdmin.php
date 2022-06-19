<?php
	require "header.php";

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

	print "<p class=\"Header1\">Dance Genra Administration</p>\n";

	if ($btnSubmit == 'Save')
	{
		$iGenraID = CleanSQLInput(substr(trim($_POST['iGenraID']),0,9));
		$strGenraName = CleanSQLInput(substr(trim($_POST['txtGenraName']),0,99));
		$strDescr = CleanSQLInput($_POST['txtDescr']);
		
		$strQuery = "update tblGenra set vcGenraName = '$strGenraName', tGenraDescr = '$strDescr' where iGenraID = $iGenraID;";
		UpdateSQL ($strQuery,"update");
	}
	
	if ($btnSubmit == 'Delete')
	{
		$iGenraID = CleanSQLInput(substr(trim($_POST['iGenraID']),0,9));
		
		$strQuery = "delete from tblGenra where iGenraID = $iGenraID;";
		UpdateSQL ($strQuery,"delete");
	}

	if ($btnSubmit == 'Insert')
	{
		$strGenraName = CleanSQLInput(substr(trim($_POST['txtGenraName']),0,99));
		$strDescr = CleanSQLInput($_POST['txtDescr']);
			
		if ($strGenraName == '')
		{
			print "<p>Please provide a Dance Genra name to insert</p>\n";
		}
		else
		{
			$strQuery = "insert tblGenra (vcGenraName, tGenraDescr) values ('$strGenraName','$strDescr');";
			UpdateSQL ($strQuery,"insert");
		}
	}
	
	//Print the normal form after update is complete.
	print "<table>\n";
        print "<tr>\n";
        print "<th>Update existing Genra Descriptions</th>\n";
        print "<th width = 100></th>\n";
        if ($btnSubmit != 'Edit')
        {
            print "<th class=lbl>Or Insert New one</th>";   
        }
	print "</tr>\n";
        print "<tr>\n";
        print "<td valign=\"top\">\n";
        print "<table border = 0>\n";
	$strQuery = "select iGenraID, vcGenraName, tGenraDescr from tblGenra;";
	if (!$Result = $dbh->query ($strQuery))
	{
            error_log ('Failed to fetch data. Error ('. $dbh->errno . ') ' . $dbh->error);
            error_log ($strQuery);
            print "<p class=\"Attn\" align=center>$ErrMsg</p>\n";
            exit(2);
	}
	while ($Row = $Result->fetch_assoc())
	{
            $vcGenraName = $Row['vcGenraName'];
            $iGenraID = $Row['iGenraID'];
            if ($WritePriv <=  $Priv)
            {
                print "<form method=\"POST\">\n";
                print "<tr valign=\"top\">\n";
                print "<td class=\"lbl\"><input type=\"hidden\" value=\"$iGenraID\" name=\"iGenraID\"></td>\n";
                print "<td>$vcGenraName</td>\n";
                print "<td><input type=\"Submit\" value=\"Edit\" name=\"btnSubmit\"></td>";
                print "<td><input type=\"Submit\" value=\"Delete\" name=\"btnSubmit\"></td>";
                print "</tr>\n";
                print "</form>\n";
            }
            else
            {
                print "<tr><td>$vcGenraName</td></tr>\n";
            }		
	}
	print "</table>\n";	    
        print "<form method=\"POST\">\n";
        print "<input type=\"Submit\" value=\"Go Back\" name=\"btnSubmit\">\n";
        print "</form>";
	print "</td>\n";
        print "<td>\n";
        print "</td>\n";
        print "<td valign=\"top\">\n";
        if (isset($_POST['iGenraID']) and $btnSubmit == 'Edit')
        {
            $iGenraID = $_POST['iGenraID'];
            $strQuery = "select iGenraID, vcGenraName, tGenraDescr from tblGenra where iGenraID = $iGenraID;";
            if (!$Result = $dbh->query ($strQuery))
            {
                error_log ('Failed to fetch data. Error ('. $dbh->errno . ') ' . $dbh->error);
                error_log ($strQuery);
                print "<p class=\"Attn\" align=center>$ErrMsg</p>\n";
                exit(2);
            }
            $Row = $Result->fetch_assoc();
            $strGenraName = $Row['vcGenraName'];
            $strClassDescr = $Row['tGenraDescr'];    
            $strBtnLabel = "Save";
        }
        else
        {
            $strGenraName = "";
            $strClassDescr = "";                         
            $iGenraID = "";
            $strBtnLabel = "Insert";
        }
	print "<form method=\"POST\">\n";
        print "<input type=\"hidden\" value=\"$iGenraID\" name=\"iGenraID\">";
	print "<span class=\"lbl\">Genra Name:</span>\n";
	print "<input type=\"text\" name=\"txtGenraName\" size=\"70\" value=\"$strGenraName\"><br>\n";
	print "<div class=\"lbl\">Description:</div>\n";
        print "<textarea name=\"txtDescr\" rows=\"20\" cols=\"80\">$strClassDescr</textarea>\n<br>\n";        
	print "<div align=\"center\"><input type=\"Submit\" value=\"$strBtnLabel\" name=\"btnSubmit\"></div>\n";
	print "</form>\n";
        if (isset($_POST['iGenraID']))
        {
            print "<form method=\"POST\">\n";
            print "<input type=\"Submit\" value=\"Go Back\" name=\"btnSubmit\">\n";
            print "</form>\n";   
        }
	print "</td>\n";
        print "</tr>\n";
        print "</table>\n";
        
	require "footer.php"; 
?>
