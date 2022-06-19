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

    if ($btnSubmit == 'Update')
    {
        $iSessionID = CleanSQLInput(substr(trim($_POST['iSessionID']),0,4));
        $strDescr = CleanSQLInput(trim($_POST['txtDescr']));
        $iSchedID = CleanSQLInput(substr(trim($_POST['iSchedID']),0,4));
        $strQuery = "UPDATE tblPageTexts SET tPageTexts = '$strDescr' WHERE vcTextName = 'SchedIntro'; ";
        UpdateSQL ($strQuery,"update");
    }

    if ($btnSubmit == 'Save')
    {
        $iSessionID = CleanSQLInput(substr(trim($_POST['iSessionID']),0,4));
        $iSchedID = CleanSQLInput(substr(trim($_POST['iSchedID']),0,4));
        $iClassID = CleanSQLInput(substr(trim($_POST['cmbClass']),0,4));
        $iWeekday = CleanSQLInput(substr(trim($_POST['cmbDay']),0,14));
        $iHour = CleanSQLInput(substr(trim($_POST['cmbHour']),0,4));
        $iMin = CleanSQLInput(substr(trim($_POST['cmbMin']),0,4));
        $strAMPM = CleanSQLInput(substr(trim($_POST['cmbAMPM']),0,4));
        $iMaxStudents = CleanSQLInput(substr(trim($_POST['txtMaxStudent']),0,9));
        $iCost = CleanSQLInput(substr(trim($_POST['txtCost']),0,9));
        $iClassLen = CleanSQLInput(substr(trim($_POST['txtLen']),0,4));
        $iNumWeeks = CleanSQLInput(substr(trim($_POST['txtNumWeeks']),0,4));
        $iNumClasses = CleanSQLInput(substr(trim($_POST['txtNumClasses']),0,4));
        $strStartDate = CleanSQLInput(substr(trim($_POST['txtStartDay']),0,14));
        $strClassNote = CleanSQLInput(substr(trim($_POST['txtNote']),0,99));

        if (isset($_POST['bCancel']))
        {
            $bCancel = 1;
        }
        else
        {
            $bCancel=0;
        }

        $strISOStartDate = date("Y-m-d",strtotime($strStartDate));

        if ($strAMPM =="PM" and $iHour < 12)
        {
            $iHour += 12;
        }

        if ($strAMPM =="AM" and $iHour == 12)
        {
            $iHour = 0;
        }

        $strTime = "$iHour:$iMin";


        $strQuery = "update tblClassSched set iClassID ='$iClassID', iWeekday='$iWeekday', "
                  . "dtClassTime='$strTime', dCost='$iCost', iLenght='$iClassLen', "
                  . "iNumWeeks='$iNumWeeks', iNumClasses='$iNumClasses', dtStarts='$strISOStartDate', "
                  . "vcNote='$strClassNote', bCancelled=$bCancel, iMaxStudent= $iMaxStudents "
                  . " where iSchedID=$iSchedID;";

        UpdateSQL ($strQuery,"update");
    }

    if ($btnSubmit == 'Delete')
    {
        $iSessionID = CleanSQLInput(substr(trim($_POST['iSessionID']),0,4));
        $iSchedID = CleanSQLInput(substr(trim($_POST['iSchedID']),0,4));

        $strQuery = "delete from tblClassSched where iSchedID=$iSchedID;";
        UpdateSQL ($strQuery,"delete");
    }

    if ($btnSubmit == 'Insert')
    {
        $iSessionID = CleanSQLInput(substr(trim($_POST['iSessionID']),0,4));
        $iClassID = CleanSQLInput(substr(trim($_POST['cmbClass']),0,4));
        $iWeekday = CleanSQLInput(substr(trim($_POST['cmbDay']),0,14));
        $iHour = CleanSQLInput(substr(trim($_POST['cmbHour']),0,4));
        $iMin = CleanSQLInput(substr(trim($_POST['cmbMin']),0,4));
        $strAMPM = CleanSQLInput(substr(trim($_POST['cmbAMPM']),0,4));
        $iMaxStudents = CleanSQLInput(substr(trim($_POST['txtMaxStudent']),0,9));
        $iCost = CleanSQLInput(substr(trim($_POST['txtCost']),0,9));
        $iClassLen = CleanSQLInput(substr(trim($_POST['txtLen']),0,4));
        $iNumWeeks = CleanSQLInput(substr(trim($_POST['txtNumWeeks']),0,4));
        $iNumClasses = CleanSQLInput(substr(trim($_POST['txtNumClasses']),0,4));
        $strStartDate = CleanSQLInput(substr(trim($_POST['txtStartDay']),0,14));
        $strClassNote = CleanSQLInput(substr(trim($_POST['txtNote']),0,99));
        $strISOStartDate = date("Y-m-d",strtotime($strStartDate));

        if ($strAMPM =="PM")
        {
            $iHour += 12;
        }

        $strTime = "$iHour:$iMin";

        $strQuery = "insert tblClassSched (iSessionNum, iClassID, iWeekday, dtClassTime, iMaxStudent, dCost, iLenght, iNumWeeks, iNumClasses, dtStarts, bCancelled, vcNote)"
              ." values ('$iSessionID','$iClassID', '$iWeekday', '$strTime', '$iMaxStudents', '$iCost', '$iClassLen', '$iNumWeeks', '$iNumClasses', '$strISOStartDate', '0', '$strClassNote');";
        UpdateSQL ($strQuery,"insert");
    }

    //Print the normal form after update is complete.
    print "<p class=\"Header1\">Class Administration for session $iSessionID</p>\n";

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

    $strLenUnit = $CTUnit . "s";
    print "<table width=\"1200\">\n<tr valign=\"top\">\n<td width=\"40%\">\n";
    print "<form method=\"POST\">\n";
    print "<input type=\"hidden\" value=\"$iSessionID\" name=\"iSessionID\">\n";
    print "<table>\n";
    print "<tr><td colspan=2 align=center class=Header2>Create New Schedule</td></tr>\n";
    print "<tr>\n<td  class=\"lblright\">Class Name:</td>\n";
    $strQuery = "SELECT iClassid, vcClassName FROM tblClasses ORDER BY vcClassName;";
    if (!$Result = $dbh->query ($strQuery))
    {
        error_log ('Failed to fetch data. Error ('. $dbh->errno . ') ' . $dbh->error);
        error_log ($strQuery);
        print "<p class=\"Attn\" align=center>$ErrMsg</p>\n";
        exit(2);
    }
    print("<td>\n<select size=\"1\" name=\"cmbClass\">\n");

    while ($Row = $Result->fetch_assoc())
    {
        print "<option value=\"{$Row['iClassid']}\">{$Row['vcClassName']}</option>\n";
    }
    print "</select>\n</td>\n</tr>\n";
    print "<tr>\n<td  class=\"lblright\">Weekday:</td>\n";
    $strQuery = "SELECT iDayNum,vcDayName FROM tblWeekdays ORDER BY iDayNum;";
    if (!$Result = $dbh->query ($strQuery))
    {
        error_log ('Failed to fetch data. Error ('. $dbh->errno . ') ' . $dbh->error);
        error_log ($strQuery);
        print "<p class=\"Attn\" align=center>$ErrMsg</p>\n";
        exit(2);
    }
    print "<td>\n<select size=\"1\" name=\"cmbDay\">\n";

    while ($Row = $Result->fetch_assoc())
    {
        print "<option value=\"{$Row['iDayNum']}\">{$Row['vcDayName']}</option>\n";
    }
    print "</select>\n</td>\n</tr>\n";
    print "<tr>\n<td  class=\"lblright\">Time:</td>\n";
    print "<td>\n<select size=\"1\" name=\"cmbHour\">\n";
    $i = 1;
    while ($i<=12)
    {
        print "<option value=\"$i\">$i</option>\n";
        $i++;
    }
    print "</select>\n";
    $strQuery = "SELECT iMinutes FROM tblMinutes ORDER BY iMinutes;";
    if (!$Result = $dbh->query ($strQuery))
    {
        error_log ('Failed to fetch data. Error ('. $dbh->errno . ') ' . $dbh->error);
        error_log ($strQuery);
        print "<p class=\"Attn\" align=center>$ErrMsg</p>\n";
        exit(2);
    }

    print "<select size=\"1\" name=\"cmbMin\">\n";
    while ($Row = $Result->fetch_assoc())
    {
        print "<option value=\"{$Row['iMinutes']}\">{$Row['iMinutes']}</option>\n";
    }
    print "</select>\n";
    print "<select size=\"1\" name=\"cmbAMPM\">\n";
    print "<option value=\"AM\">AM</option>\n";
    print "<option selected value=\"PM\">PM</option>\n";
    print "</select>\n</td>\n</tr>\n";
    print "<tr>\n<td  class=\"lblright\">Max Students:</td>\n";
    print "<td><input type=\"text\" name=\"txtMaxStudent\" size=\"7\" value=\"$DefMaxStudent\"></td>\n</tr>\n";
    print "<tr>\n<td  class=\"lblright\">Cost:</td>\n";
    print "<td>\$<input type=\"text\" name=\"txtCost\" size=\"6\" value=\"$DefClassPrice\"></td>\n</tr>\n";
    print "<tr>\n<td  class=\"lblright\">Class Length:</td>\n";
    print "<td><input type=\"text\" name=\"txtLen\" size=\"7\" value=\"$DefClassLen\">$strLenUnit</td>\n</tr>\n";
    print "<tr>\n<td  class=\"lblright\">Number of Weeks:</td>\n";
    print "<td><input type=\"text\" name=\"txtNumWeeks\" size=\"7\" value=\"$DefNumWeeks\"></td>\n</tr>\n";
    print "<tr>\n<td  class=\"lblright\">Number of Classes:</td>\n";
    print "<td><input type=\"text\" name=\"txtNumClasses\" size=\"7\" value=\"$DefNumWeeks\"></td>\n</tr>\n";
    print "<tr>\n<td  class=\"lblright\">First day of Class:</td>\n";
    print "<td><input type=\"date\" name=\"txtStartDay\" size=\"15\"></td>\n</tr>\n";
    print "<tr>\n<td  class=\"lblright\">Class Note:</td>\n";
    print "<td><input type=\"text\" name=\"txtNote\" size=\"35\"></td>\n</tr>\n";
    print "<tr><td colspan=2 align=center><input type=\"Submit\" value=\"Insert\" name=\"btnSubmit\"></td></tr>\n";
    print "</table>\n";
    print "</form>\n";

    print "</td>\n<td>\n";
    print "<form method=\"POST\">\n";
    print "<input type=\"hidden\" value=\"$iSessionID\" name=\"iSessionID\">\n";
    print "<table>\n";
    print "<tr><td colspan=2 align=center class=Header2>Update Overall Schedule Statement</td></tr>\n";
    print "<tr>\n";
    print "<td>";
    $strQuery = "SELECT tPageTexts FROM tblPageTexts WHERE vcTextName = 'SchedIntro';";
    if (!$Result = $dbh->query ($strQuery))
    {
        error_log ('Failed to fetch data. Error ('. $dbh->errno . ') ' . $dbh->error);
        error_log ($strQuery);
        print "<p class=\"Attn\" align=center>$ErrMsg</p>\n";
        exit(2);
    }
    $Row = $Result->fetch_assoc();
    $ScheduleRequirements = $Row['tPageTexts'];
    print "<input type=\"hidden\" value=\"1\" name=\"iSchedID\">\n";
    print "<textarea name=\"txtDescr\" rows=\"10\" cols=\"70\">$ScheduleRequirements</textarea>\n</td>\n</tr>\n";
    print "<tr><td colspan=2 align=center><input type=\"Submit\" value=\"Update\" name=\"btnSubmit\"></td></tr>\n";
    print "</table>\n";
    print "</form>\n";
    print "</td>\n</tr>\n</table>\n";

    $strQuery = "SELECT iSchedID, iClassID, iWeekday, dtClassTime, iMaxStudent, dCost, iLenght, iNumWeeks, iNumClasses, dtStarts, vcNote, bCancelled "
              . " FROM tblClassSched where iSessionNum = $iSessionID ORDER BY iWeekday, dtClassTime;";
    if (!$Result = $dbh->query ($strQuery))
    {
        error_log ('Failed to fetch data. Error ('. $dbh->errno . ') ' . $dbh->error);
        error_log ($strQuery);
        print "<p class=\"Attn\" align=center>$ErrMsg</p>\n";
        exit(2);
    }
    print "<table border=0 width=\"1400\">\n";
    print "<caption class=Header2>Or Update existing ones</caption>";
    print "<tr>";
    print "<th width=\"35\">ID</th>";
    print "<th width=\"280\">Class</th>";
    print "<th width=\"100\">Day of Week</th>";
    print "<th width=\"160\">Class Start Time</th>";
    print "<th width=\"60\">Max Students</th>";
    print "<th width=\"60\">Cost \$</th>";
    print "<th width=\"60\">Length in $strLenUnit</th>";
    print "<th width=\"60\">Number of weeks</th>";
    print "<th width=\"60\">Number of classes</th>";
    print "<th width=\"140\">Start</th>";
    print "<th width=\"60\">Note</th>";
    print "<th width=\"60\">Cancelled</th>";
    print "<tr>";
    while ($Row = $Result->fetch_assoc())
    {
        $strTime = date("h i A", strtotime($Row['dtClassTime']));
        $strTimeParts=  explode(" ", $strTime);
//        $strStartDate = date("m/d/Y",strtotime($Row['dtStarts']));
        $strStartDate = date("Y-m-d",strtotime($Row['dtStarts']));
        $bCancelled = "";
        if ($Row['bCancelled']==1)
        {
            $bCancelled = "Checked";
        }
        $strQuery = "SELECT iClassid, vcClassName FROM tblClasses ORDER BY vcClassName;";
        if (!$Result2 = $dbh->query ($strQuery))
        {
            error_log ('Failed to fetch data. Error ('. $dbh->errno . ') ' . $dbh->error);
            error_log ($strQuery);
            print "<p class=\"Attn\" align=center>$ErrMsg</p>\n";
            exit(2);
        }
        print "<form method=\"POST\">\n<tr>";
        print "<input type=\"hidden\" value=\"$iSessionID\" name=\"iSessionID\">\n";
        print "<td><input type=\"hidden\" value=\"{$Row['iSchedID']}\" name=\"iSchedID\">{$Row['iSchedID']}</td>\n";
        print "<td>\n<select size=\"1\" name=\"cmbClass\">\n";

        while ($Row2 = $Result2->fetch_assoc())
        {
            if ($Row2['iClassid'] == $Row['iClassID'])
            {
                    print "<option selected value=\"{$Row2['iClassid']}\">{$Row2['vcClassName']}</option>\n";
            }
            else
            {
                    print "<option value=\"{$Row2['iClassid']}\">{$Row2['vcClassName']}</option>\n";
            }
        }
        print "</select>\n</td>\n";
        $strQuery = "SELECT iDayNum,vcDayName FROM tblWeekdays ORDER BY iDayNum;";
        if (!$Result2 = $dbh->query ($strQuery))
        {
            error_log ('Failed to fetch data. Error ('. $dbh->errno . ') ' . $dbh->error);
            error_log ($strQuery);
            print "<p class=\"Attn\" align=center>$ErrMsg</p>\n";
            exit(2);
        }
        print "<td>\n<select size=\"1\" name=\"cmbDay\">\n";

        while ($Row2 = $Result2->fetch_assoc())
        {
            if ($Row2['iDayNum'] == $Row['iWeekday'])
            {
                print "<option selected value=\"{$Row2['iDayNum']}\">{$Row2['vcDayName']}</option>\n";
            }
            else
            {
                print "<option value=\"{$Row2['iDayNum']}\">{$Row2['vcDayName']}</option>\n";
            }
        }
        print "</select>\n</td>\n";
        print "<td>\n<select size=\"1\" name=\"cmbHour\">\n";
        $i = 1;
        while ($i<=12)
        {
            if ($i == $strTimeParts[0])
            {
                print "<option selected value=\"$i\">$i</option>\n";
            }
            else
            {
                print "<option value=\"$i\">$i</option>\n";
            }
            $i++;
        }
        print "</select>\n";
        $strQuery = "SELECT iMinutes FROM tblMinutes ORDER BY iMinutes;";
        if (!$Result2 = $dbh->query ($strQuery))
        {
            error_log ('Failed to fetch data. Error ('. $dbh->errno . ') ' . $dbh->error);
            error_log ($strQuery);
            print "<p class=\"Attn\" align=center>$ErrMsg</p>\n";
            exit(2);
        }

        print "<select size=\"1\" name=\"cmbMin\">\n";
        while ($Row2 = $Result2->fetch_assoc())
        {
            if ($Row2['iMinutes'] == $strTimeParts[1])
            {
                print "<option selected value=\"{$Row2['iMinutes']}\">{$Row2['iMinutes']}</option>\n";
            }
            else
            {
                print "<option value=\"{$Row2['iMinutes']}\">{$Row2['iMinutes']}</option>\n";
            }
        }
        print "</select>\n";
        print "<select size=\"1\" name=\"cmbAMPM\">\n";
        if ($strTimeParts[2]=="AM")
        {
            print "<option selected value=\"AM\">AM</option>\n";
        }
        else
        {
            print "<option value=\"AM\">AM</option>\n";
        }
        if ($strTimeParts[2]=="PM")
        {
            print "<option selected value=\"PM\">PM</option>\n";
        }
        else
        {
            print "<option value=\"PM\">PM</option>\n";
        }
        print "</select>\n</td>\n";
        print "<td><input type=\"text\" name=\"txtMaxStudent\" size=\"6\" value=\"{$Row['iMaxStudent']}\"></td>\n";
        print "<td><input type=\"text\" name=\"txtCost\" size=\"6\" value=\"{$Row['dCost']}\"></td>\n";
        print "<td><input type=\"text\" name=\"txtLen\" size=\"3\" value=\"{$Row['iLenght']}\"></td>\n";
        print "<td><input type=\"text\" name=\"txtNumWeeks\" size=\"2\" value=\"{$Row['iNumWeeks']}\"></td>\n";
        print "<td><input type=\"text\" name=\"txtNumClasses\" size=\"2\" value=\"{$Row['iNumClasses']}\"></td>\n";
        print "<td><input type=\"date\" name=\"txtStartDay\" size=\"10\" value=\"$strStartDate\"></td>\n";
        print "<td><input type=\"text\" name=\"txtNote\" size=\"15\" value=\"{$Row['vcNote']}\"</td>\n";
        print "<td align=\"center\"><input type=\"checkbox\" name=\"bCancel\" $bCancelled></td>\n";
        print "<td><input type=\"Submit\" value=\"Save\" name=\"btnSubmit\"></td>\n";
        print "<td><input type=\"Submit\" value=\"Delete\" name=\"btnSubmit\"></td></tr>\n";
        print "</form>\n";
    }
    print "</table>\n";
    require("footer.php");
?>
