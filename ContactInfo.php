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

	print("<p class=\"Header1\">Contact information</p>\n");

	if (($PostVarCount == 1) and ($btnSubmit == 'Go Back'))
	{
		header("Location: $strPageURL");
	}    
	
	if ($btnSubmit == 'Save')
	{
            $iSortNum = CleanSQLInput(substr(trim($_POST['iSortNum']),0,49));
            $strValue= CleanSQLInput(substr(trim($_POST['txtValue']),0,49));
            $strType = CleanSQLInput(substr(trim($_POST['cmbType']),0,49));
            $strLabel= CleanSQLInput(substr(trim($_POST['txtLabel']),0,49));
            $ContactID = CleanSQLInput(substr(trim($_POST['iContactID']),0,49));

            if ($iSortNum == '')
            {
                $iSortNum = 0;
            }
            if ($strValue== '')
            {
                print "<p>Contact Value is requried</p>\n";
            }
            else
            {
                $strQuery = "update tblContactInfo set vcType = '$strType', vcLabel = '$strLabel', vcValue = '$strValue', iSequence = $iSortNum where iContactID = $ContactID;";
                UpdateSQL ($strQuery,"update");
            }
	}
	
	if ($btnSubmit == 'Delete')
	{
		$ContactID = substr(trim($_POST['iContactID']),0,49);
		
		$strQuery = "delete from tblContactInfo where iContactID = $ContactID;";
		UpdateSQL ($strQuery,"delete");
	}

	if ($btnSubmit == 'Insert')
	{
            $iSortNum = CleanSQLInput(substr(trim($_POST['iSortNum']),0,49));
            $strValue= CleanSQLInput(substr(trim($_POST['txtValue']),0,49));
            $strType = CleanSQLInput(substr(trim($_POST['cmbType']),0,49));
            $strLabel= CleanSQLInput(substr(trim($_POST['txtLabel']),0,49));

            if ($iSortNum == '')
            {
                $iSortNum = 0;
            }

            if ($strValue== '')
            {
                print "<p>Please provide a contact value to insert</p>\n";
            }
            else
            {
                $strQuery = "insert tblContactInfo (vcValue, iSequence, vcLabel, vcType)"
                          . "values ('$strValue',$iSortNum, '$strLabel', '$strType');";
                UpdateSQL ($strQuery,"insert");
            }
	}
	
	//Print the normal form after update is complete.
	print "<table>\n";
        print "<tr>\n";
        print "<th class=lbl>Update existing Contacts</th>\n";
        print "<th width = 100></th>\n";
        print "<th class=lbl>Or Insert New one</th>\n";
        print "</tr>\n";
	print "<tr>\n";
        print "<td>\n";
        print "<table border = 0>\n";
        print "<tr>\n";
        print "<th></th>\n";
        print "<th class=lbl>Type</th>\n";
        print "<th class=lbl>Sort order</th>\n";
        print "<th class=lbl>Label</th>\n";
        print "<th class=lbl>Value</th>\n";
        print "</tr>\n";
	$strQuery = "SELECT vcValue, iSequence, iContactID, vcType, vcLabel FROM tblContactInfo order by vcType, iSequence;";
	if (!$Result = $dbh->query ($strQuery))
	{
            error_log ('Failed to fetch data. Error ('. $dbh->errno . ') ' . $dbh->error);
            error_log ($strQuery);
            exit(2);
	}
	while ($Row = $Result->fetch_assoc())
	{
            $strValue = $Row['vcValue'];
            $iSortNum = $Row['iSequence'];
            $ContactID = $Row['iContactID'];
            $strType  = $Row['vcType'];
            $strLabel = $Row['vcLabel'];
            if ($WritePriv <=  $Priv)
            {
                print "<form method=\"POST\">\n";
                print "<tr valign=\"top\">\n";
                print "<td><input type=\"hidden\" value=\"$ContactID\" name=\"iContactID\"> </td>\n";
                $strQuery = "SELECT vcTypes FROM tblContactTypes;";
                if (!$Result2 = $dbh->query ($strQuery))
                {
                    error_log ('Failed to fetch data. Error ('. $dbh->errno . ') ' . $dbh->error);
                    error_log ($strQuery);
                    print "<p class=\"Attn\" align=center>$ErrMsg</p>\n";
                    exit(2);
                }
                print "<td>\n";
                print("<select size=\"1\" name=\"cmbType\">\n");
                while ($Row2 = $Result2->fetch_assoc())
                {
                    $vcTypes = $Row2['vcTypes'];
                    if ($vcTypes == $strType)
                    {
                        print "<option selected value=\"$vcTypes\">$vcTypes</option>\n";
                    }
                    else
                    {
                        print "<option value=\"$vcTypes\">$vcTypes</option>\n";
                    }
                }
                print "</select>\n";
                print "</td>\n";
                print "<td><input type=\"text\" value=\"$iSortNum\" name=\"iSortNum\" size=\"5\" ></td>\n";
                print "<td><input type=\"text\" value=\"$strLabel\" name=\"txtLabel\" size=\"15\" ></td>\n";
                print "<td><input type=\"text\" value=\"$strValue\" name=\"txtValue\" size=\"25\" ></td>\n";
                print "<td><input type=\"Submit\" value=\"Save\" name=\"btnSubmit\"></td>";
                print "<td><input type=\"Submit\" value=\"Delete\" name=\"btnSubmit\"></td>";
                print "</tr>\n";
                print "</form>\n";
            }
	}
	print "</table>\n";	    
	print "</td>\n<td>\n</td>\n<td valign=\"top\">\n";
	print "<form method=\"POST\">\n";
	print "<table>\n";
	print "<tr>\n<td align = right class = lbl>Contact Type: </td>\n";
	print "<td>";
        $strQuery = "SELECT vcTypes FROM tblContactTypes;";
        if (!$Result = $dbh->query ($strQuery))
        {
            error_log ('Failed to fetch data. Error ('. $dbh->errno . ') ' . $dbh->error);
            error_log ($strQuery);
            print "<p class=\"Attn\" align=center>$ErrMsg</p>\n";
            exit(2);
        }
        print("<select size=\"1\" name=\"cmbType\">\n");

        while ($Row = $Result->fetch_assoc())
        {
            $vcTypes = $Row['vcTypes'];
            print "<option value=\"$vcTypes\">$vcTypes</option>\n";
        }
        print "</select>\n";        
        print "</td>\n</tr>\n";
	print "<tr>\n<td align = right class = lbl>Sort Order: </td>\n";
	print "<td><input type=\"text\" name=\"iSortNum\" size=\"13\" ></td></tr>\n";
	print "<tr>\n<td align = right class = lbl>Contact Label: </td>\n";
	print "<td><input type=\"text\" name=\"txtLabel\" size=\"30\" ></td>\n</tr>\n";
	print "<tr>\n<td align = right class = lbl>Contact Value: </td>\n";
	print "<td><input type=\"text\" name=\"txtValue\" size=\"30\" ></td>\n</tr>\n";
	print "<tr><td colspan=2 align=center><input type=\"Submit\" value=\"Insert\" name=\"btnSubmit\"></td></tr>\n";
	print "</table>\n";
	print "</form>\n</td>\n</tr>\n</table>";
	
	require("footer.php"); 
?>
