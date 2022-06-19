<?php
    require("header.php");

    $strQuery = "SELECT max(iSessionNum) iSessionNum FROM vwActiveSessions WHERE (dtSessionStart - interval 14 day) < now();";
    if (!$Result = $dbh->query ($strQuery))
    {
        error_log ('Failed to fetch data. Error ('. $dbh->errno . ') ' . $dbh->error);
        error_log ($strQuery);print "<table width=\"1200\">\n<tr valign=\"top\">\n<td width=\"40%\">\n";
        print "<p class=\"Attn\" align=center>$ErrMsg</p>\n";
        exit(2);
    }
    $Row = $Result->fetch_assoc();
    $iDefSessionID = $Row['iSessionNum'];

    if (!$iDefSessionID)
    {
        $strQuery = "SELECT max(iSessionNum) iSessionNum FROM tblClassSession;";
        if (!$Result = $dbh->query ($strQuery))
        {
            error_log ('Failed to fetch data. Error ('. $dbh->errno . ') ' . $dbh->error);
            error_log ($strQuery);
            print "<p class=\"Attn\" align=center>$ErrMsg</p>\n";
            exit(2);
        }
        $Row = $Result->fetch_assoc();
        $iDefSessionID = $Row['iSessionNum'];

    }
    
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

    if (($PostVarCount == 1) and ($btnSubmit == 'Go Back'))
    {
            header("Location: $strPageURL");
    }

    if ($btnSubmit == 'Change')
    {
       $iSessionID = CleanSQLInput(substr(trim($_POST['cmbSession']),0,4));
    }
    else
    {
        $iSessionID = $iDefSessionID;
    }
    
    if ($btnSubmit == 'Save')
    {
        $iDayNum = CleanSQLInput(substr(trim($_POST['cmbDay']),0,49));
        $strNote= CleanSQLInput(substr(trim($_POST['txtNote']),0,49));
        $iSessionID = CleanSQLInput(substr(trim($_POST['iSessionID']),0,4));
        $iNoteID = CleanSQLInput(substr(trim($_POST['iNoteID']),0,49));

        if ($iDayNum == '')
        {
            $iDayNum = 1;
        }
        if ($strNote== '')
        {
            print "<p>Blank note doesn't make sense, use delete if no longer wanted</p>\n";
        }
        else
        {
            $strQuery = "update tblSessionNotes set iDayNum = '$iDayNum', vcNote = '$strNote' where iNoteID = $iNoteID;";
            UpdateSQL ($strQuery,"update");
        }
    }

    if ($btnSubmit == 'Delete')
    {
            $iNoteID = substr(trim($_POST['iNoteID']),0,49);

            $strQuery = "delete from tblSessionNotes where iNoteID = $iNoteID;";
            UpdateSQL ($strQuery,"delete");
    }

    if ($btnSubmit == 'Insert')
    {
        $iDayNum = CleanSQLInput(substr(trim($_POST['cmbDay']),0,49));
        $strNote= CleanSQLInput(substr(trim($_POST['txtNote']),0,49));
        $iSessionID = CleanSQLInput(substr(trim($_POST['iSessionID']),0,4));
        
        if ($iDayNum == '')
        {
            $iDayNum = 1;
        }

        if ($strNote== '')
        {
            print "<p>Please provide a note to insert</p>\n";
        }
        else
        {
            $strQuery = "SELECT count(*) a FROM tblSessionNotes WHERE iSessionID=$iSessionID and iDayNum=$iDayNum;";
            if (!$Result = $dbh->query ($strQuery))
            {
                error_log ('Failed to fetch data. Error ('. $dbh->errno . ') ' . $dbh->error);
                error_log ($strQuery);print "<table width=\"1200\">\n<tr valign=\"top\">\n<td width=\"40%\">\n";
                print "<p class=\"Attn\" align=center>$ErrMsg</p>\n";
                exit(2);
            }
            $Row = $Result->fetch_assoc();
            $iRowCount = $Row['a'];
            if ($iRowCount>0)
            {
                print "<p class=\"Error\">There is already a note for that day, please update that instead</p>\n";
            }
            else
            {
               $strQuery = "insert tblSessionNotes (iSessionID, iDayNum, vcNote)"
                          . "values ('$iSessionID', $iDayNum, '$strNote');";
                UpdateSQL ($strQuery,"insert");                
            }
        }
    }
        
    //Print the normal form after update is complete.
    print "<p class=\"Header1\">Class Notes Administration for session $iSessionID</p>\n";

    print "<form method=\"POST\">\n";
    print "<p align=\"center\">Change Session to:\n";
    $strQuery = "SELECT iSessionNum, dtSessionStart FROM tblClassSession ORDER BY iSessionNum;";
    if (!$Result = $dbh->query ($strQuery))
    {
        error_log ('Failed to fetch data. Error ('. $dbh->errno . ') ' . $dbh->error);
        error_log ($strQuery);
        print "<p class=\"Attn\" align=center>$ErrMsg</p>\n";
        exit(2);
    }
    print("<select size=\"1\" name=\"cmbSession\">\n");

    while ($Row = $Result->fetch_assoc())
    {
        $iSessID = $Row['iSessionNum'];
        $iSessStart = date("m/d/Y",strtotime($Row['dtSessionStart']));

        if ($iSessID == $iSessionID)
        {
            print "<option selected value=\"$iSessID \">Session #$iSessID starts $iSessStart </option>\n";
        }
        else
        {
            print "<option value=\"$iSessID \">Session #$iSessID starts $iSessStart </option>\n";
        }
    }
    print "</select>\n";
    print "<input type=\"Submit\" value=\"Change\" name=\"btnSubmit\">\n";
    print "<a href=\"SessionAdmin.php\">Class Session Administration</a>\n</p>\n";
    print "</form>\n";
    
    print "<table>\n";
    print "<tr>\n";
    print "<th class=lbl>Update existing notes</th>\n";
    print "<th width = 100></th>\n";
    print "<th class=lbl>Or Insert New one</th>\n";
    print "</tr>\n";
    print "<tr>\n";
    print "<td>\n";
    print "<table border = 0>\n";
    print "<tr>\n";
    print "<th></th>\n";
    print "<th class=lbl>Day of week</th>\n";
    print "<th class=lbl>Note</th>\n";
    print "</tr>\n";
    $strQuery = "SELECT iNoteID, iDayNum, vcNote FROM tblSessionNotes WHERE iSessionID=$iSessionID ORDER BY iDayNum;";
    if (!$Result = $dbh->query ($strQuery))
    {
        error_log ('Failed to fetch data. Error ('. $dbh->errno . ') ' . $dbh->error);
        error_log ($strQuery);
        exit(2);
    }
    while ($Row = $Result->fetch_assoc())
    {
        $strNote = $Row['vcNote'];
        $iDayNum = $Row['iDayNum'];
        $iNoteID = $Row['iNoteID'];
        if ($WritePriv <=  $Priv)
        {
            print "<form method=\"POST\">\n";
            print "<input type=\"hidden\" value=\"$iSessionID\" name=\"iSessionID\">\n";
            print "<tr valign=\"top\">\n";
            print "<td><input type=\"hidden\" value=\"$iNoteID\" name=\"iNoteID\"> </td>\n";
            $strQuery = "SELECT iDayNum,vcDayName FROM tblWeekdays ORDER BY iDayNum;";
            if (!$Result2 = $dbh->query ($strQuery))
            {
                error_log ('Failed to fetch data. Error ('. $dbh->errno . ') ' . $dbh->error);
                error_log ($strQuery);
                print "<p class=\"Attn\" align=center>$ErrMsg</p>\n";
                exit(2);
            }
            print "<td>\n";
            print "<select size=\"1\" name=\"cmbDay\">\n";
            while ($Row2 = $Result2->fetch_assoc())
            {
                if ($Row2['iDayNum'] == $Row['iDayNum'])
                {
                    print "<option selected value=\"{$Row2['iDayNum']}\">{$Row2['vcDayName']}</option>\n";
                }
                else
                {
                    print "<option value=\"{$Row2['iDayNum']}\">{$Row2['vcDayName']}</option>\n";
                }
            }
            print "</select>\n";
            print "</td>\n";
            print "<td><input type=\"text\" value=\"$strNote\" name=\"txtNote\" size=\"35\" ></td>\n";
            print "<td><input type=\"Submit\" value=\"Save\" name=\"btnSubmit\"></td>";
            print "<td><input type=\"Submit\" value=\"Delete\" name=\"btnSubmit\"></td>";
            print "</tr>\n";
            print "</form>\n";
        }
    }
    print "</table>\n";	    
    print "</td>\n<td>\n</td>\n<td valign=\"top\">\n";
    print "<form method=\"POST\">\n";
    print "<input type=\"hidden\" value=\"$iSessionID\" name=\"iSessionID\">\n";
    print "<table>\n";
    print "<tr>\n<td align = right class = lbl>Contact Type: </td>\n";
    print "<td>";
    $strQuery = "SELECT iDayNum,vcDayName FROM tblWeekdays ORDER BY iDayNum;";
    if (!$Result = $dbh->query ($strQuery))
    {
        error_log ('Failed to fetch data. Error ('. $dbh->errno . ') ' . $dbh->error);
        error_log ($strQuery);
        print "<p class=\"Attn\" align=center>$ErrMsg</p>\n";
        exit(2);
    }
    print "<select size=\"1\" name=\"cmbDay\">\n";

    while ($Row = $Result->fetch_assoc())
    {
        print "<option value=\"{$Row['iDayNum']}\">{$Row['vcDayName']}</option>\n";
    }
    print "</select>\n";
    print "</td>\n</tr>\n";
    print "<tr>\n<td align = right class = lbl>Note: </td>\n";
    print "<td><input type=\"text\" name=\"txtNote\" size=\"45\"></td>\n</tr>\n";
    print "<tr><td colspan=2 align=center><input type=\"Submit\" value=\"Insert\" name=\"btnSubmit\"></td></tr>\n";
    print "</table>\n";
    print "</form>\n</td>\n</tr>\n</table>";

    require("footer.php");
?>
