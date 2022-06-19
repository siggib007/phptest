<?php
   require("header.php");

   $NumAffected = -1;
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
        
    $iScheduleID = -10;
    if (isset($_POST['chkPaid']))
    {
        $bPaid = $_POST['chkPaid'];
    }
    else
    {
        $bPaid=array();
    }
    
    if (isset($_POST['btnSubmit']))
    {
            $btnSubmit = $_POST['btnSubmit'];
    }
    else
    {
            $btnSubmit = "";
    }

    if ($btnSubmit == 'Change')
    {
       $iSessionID = CleanSQLInput(substr(trim($_POST['cmbSession']),0,4));
    }
    else
    {
        $iSessionID = $iDefSessionID;
    }

    if ($btnSubmit == 'View')
    {
       $iScheduleID = CleanSQLInput(substr(trim($_POST['cmbSched']),0,4));
       $iSessionID = CleanSQLInput(substr(trim($_POST['iSession']),0,4));
    }

    if ($btnSubmit == 'Mark Paid')
    {
        $iScheduleID = CleanSQLInput(substr(trim($_POST['iSched']),0,4));
        $iSessionID = CleanSQLInput(substr(trim($_POST['iSession']),0,4));

        $strQuery = "SELECT iRegID, bPaid FROM tblClassReg where iSchedID = $iScheduleID;";
        if (!$Result = $dbh->query ($strQuery))
        {
            error_log ('Failed to fetch data. Error ('. $dbh->errno . ') ' . $dbh->error);
            error_log ($strQuery);
            print "<p class=\"Attn\" align=center>$ErrMsg</p>\n";
            exit(2);
        }
        while ($Row = $Result->fetch_assoc())
        {
            $bFound = FALSE;
            $iRegID = $Row['iRegID'];
            foreach ($bPaid as $value) 
            {
                if ($value == $iRegID )
                {
                    $strQuery = "update tblClassReg set bPaid = 1 WHERE iRegID = $value LIMIT 1;";
                    $bOK = CallSPNoOut($strQuery);
                    $bFound = TRUE;
                    break;
                }
            }
            if (!$bFound)
            {
                $strQuery = "update tblClassReg set bPaid = 0 WHERE iRegID = $iRegID LIMIT 1;";
                $bOK = CallSPNoOut($strQuery);
            }
        }
    }
    
    print "<p class=\"Header1\">Class Roster for Class Session $iSessionID</p>\n";

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
    print "</form>\n";

    print "<form method=\"POST\">\n";
    print "<input type=\"hidden\" name=\"iSession\" value=\"$iSessionID\">";
    print "<p align=\"center\">Select a class to view a roster:\n";
    $strQuery = " SELECT iSchedID, vcDayName, vcClassName "
          . " FROM vwScheduleDay where iSessionNum = $iSessionID ORDER BY iWeekday, dtClassTime;";
    if (!$Result = $dbh->query ($strQuery))
    {
        error_log ('Failed to fetch data. Error ('. $dbh->errno . ') ' . $dbh->error);
        error_log ($strQuery);
        print "<p class=\"Attn\" align=center>$ErrMsg</p>\n";
        exit(2);
    }
    print("<select size=\"1\" name=\"cmbSched\">\n");

    while ($Row = $Result->fetch_assoc())
    {
        $iSchedID = $Row['iSchedID'];
        $strDayName = $Row['vcDayName'];
        $strClassName = $Row['vcClassName'];

        if ($iSchedID == $iScheduleID)
        {
            print "<option selected value=\"$iSchedID \">$strClassName on $strDayName</option>\n";
        }
        else
        {
            print "<option value=\"$iSchedID \">$strClassName on $strDayName</option>\n";
        }
    }
    print "</select>\n";
    print "<input type=\"Submit\" value=\"View\" name=\"btnSubmit\">\n";
    print "</form>\n";

    if ($iScheduleID == -10)
    {
        print "<p align=\"center\">Please select session and class from above and click view</p>";
    }
    else
    {
        print "<p align=\"center\">There are:<br>\n";
        $strQuery = "SELECT vclf, count(*) as lfcount FROM tblClassReg WHERE iSchedID = '$iScheduleID' GROUP BY vclf";
        if (!$Result = $dbh->query ($strQuery))
        {
            error_log ('Failed to fetch data. Error ('. $dbh->errno . ') ' . $dbh->error);
            error_log ($strQuery);
            print "<p class=\"Attn\" align=center>$ErrMsg</p>\n";
            exit(2);
        }
        $NumAffected = $Result->num_rows;
        if ($NumAffected > 0)
        {
            while ($Row = $Result->fetch_assoc())
            {
                $vclf = $Row['vclf'];
                $lfcount = $Row['lfcount'];
                print $lfcount . " " . $vclf ."s<br>\n";
            }
        }
        $strQuery = "SELECT iRegID, vcName, bPaid, vcPhone, vcCell, vclf FROM vwClassRoster WHERE iSchedID = '$iScheduleID' ORDER BY vcName";
        if (!$Result = $dbh->query ($strQuery))
        {
            error_log ('Failed to fetch data. Error ('. $dbh->errno . ') ' . $dbh->error);
            error_log ($strQuery);
            print "<p class=\"Attn\" align=center>$ErrMsg</p>\n";
            exit(2);
        }
        $NumAffected = $Result->num_rows;
        switch ($NumAffected)
        {
            case "0":
                print "<p align=\"center\">There are no students registered</p>";
                break;
            case "1":
                print "Total of 1 student registered</p>";
                break;
            default:
                print "Total of $NumAffected students registered</p>";
                break;
        }
    }
    if ($NumAffected > 0)
    {
        print "<form method=\"POST\">\n";
        print "<input type=\"hidden\" name=\"iSession\" value=\"$iSessionID\">";
        print "<input type=\"hidden\" name=\"iSched\" value=\"$iScheduleID\">";
        print "<table class=\"ClassListing\">\n";
        print "<tr>\n";
        print "<th>Student Name</th>\n";
        print "<th>Role</th>\n";
        print "<th>Phone</th>\n";
        print "<th>Cell</th>\n";
        print "<th>Paid?</th>\n";
        print "</tr>\n";

        while ($Row = $Result->fetch_assoc())
        {
            $strStudent = $Row['vcName'];
            $strPhone = $Row['vcPhone'];
            $strCell = $Row['vcCell'];
            $iRegID =  $Row['iRegID'];
            $vclf =  $Row['vclf'];
            if ($Row['bPaid']==1)
            {
                $bPaid = "checked";
            }
            else
            {
                $bPaid="";
            }
            print "<tr>\n";
            print "<td>$strStudent</td>\n";
            print "<td>$vclf</td>\n";
            print "<td>$strPhone</td>\n";
            print "<td>$strCell</td>\n";
            print "<td><input type=\"checkbox\" name=\"chkPaid[]\" value=\"$iRegID\" $bPaid>\n</td>\n";
            print "</tr>\n";
        }
        print "<tr>\n";
        print "<td colspan=20 align=right>\n";
        print "<input type=\"Submit\" value=\"Mark Paid\" name=\"btnSubmit\">\n";
        print "</td>\n";
        print "</tr>\n";
        print "</table>\n";
        print "</form>\n";
    }
    require("footer.php");
?>
