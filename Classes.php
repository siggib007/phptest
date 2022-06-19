<?php
    require("header.php");


    if (isset($_GET['sess']))
    {
        $iSessionID = $_GET['sess'];
        unset($_GET['sess']);
    }
    else
    {
        $iSessionID = "";
    }
    if (isset($_GET['a']))
    {
        $strType = $_GET['a'];
    }
    else
    {
        $strType="Sched";
    }

    if ($strType=="Sched")
    {
        ClassSched ($iSessionID);
    }
    else
    {
        ClassDescr();
    }

    function ClassDescr()
    {
        $strPageName = $GLOBALS['strPageName'];
        print "<center>\n";
        print "<a class=\"NavButton\" href=\"$strPageName?a=Sched\">Schedule</a>\n";
        print "</center>\n";
        $dbh = $GLOBALS['dbh'];
        $ErrMsg = $GLOBALS['ErrMsg'];
        print "<p class=\"Header1\">Class Descriptions</p>\n";
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
            $strClassDescr = $Row['tClassDescr'];
            $iClassid = $Row['iClassid'];
            print "<div class=\"ClassName\"><a id=$iClassid>$vcClassName</a></div>\n";
            print "<div class=\"ClassDescr\">$strClassDescr</div>\n";
        }
    }

    function ClassSched ($iDefSessionID)
    {
        $strPageName = $GLOBALS['strPageName'];
        print "<center>\n";
        print "<a class=\"NavButton\" href=\"$strPageName?a=Descr\">Descriptions</a>\n";
        print "</center>\n";
        $dbh = $GLOBALS['dbh'];
        $ErrMsg = $GLOBALS['ErrMsg'];
        $ShowLastClass = $GLOBALS['ShowLastClass'];
        $ClassDuration = $GLOBALS['ClassDuration'];
        $CTUnit = $GLOBALS['CTUnit'];
        $strTimeFormat = $GLOBALS['strTimeFormat'];
        $strDateFormat = $GLOBALS['strDateFormat'];
        $Priv = $GLOBALS['Priv'];
        $WritePriv = $GLOBALS['WritePriv'];
        $iStudentLevel = $GLOBALS['minRegLevel'];
        $FromEmail = $GLOBALS['FromEmail'];
        $ProfileNotify = $GLOBALS['ProfileNotify'];
        $strContactEmail = $GLOBALS['eFromAddr'];
        $SiteType = $GLOBALS['SiteType'];


        if (isset($GLOBALS['iUserID']))
        {
            $iUserID = $GLOBALS['iUserID'];
        }
        else
        {
            $iUserID = 0;
        }

        $strDN = "";

        if (! $iDefSessionID)
        {
            $strQuery = "SELECT max(iSessionNum) iSessionNum FROM vwActiveSessions WHERE (dtSessionStart - interval 14 day) < now();";
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

        if ($btnSubmit == 'Impersonate')
        {
           $strUserID = CleanSQLInput(substr(trim($_POST['cmbAsUser']),0,4));
           $iSessionID = CleanSQLInput(substr(trim($_POST['txtSessionID']),0,4));
        }
        else
        {
            if (isset($GLOBALS['iUserID']))
            {
                $strUserID = $GLOBALS['iUserID'];
            }
            else
            {
                $strUserID = 0;
            }

        }

        if (isset($_POST['txtUID']))
        {
            $strUserID = CleanSQLInput(substr(trim($_POST['txtUID']),0,4));
        }
        $strQuery = "SELECT vcName, vcGender, vcEmail FROM tblUsers WHERE iUserID = $strUserID;";
        if (!$Result = $dbh->query ($strQuery))
        {
            error_log ('Failed to fetch data. Error ('. $dbh->errno . ') ' . $dbh->error);
            error_log ($strQuery);
            print "<p class=\"Attn\" align=center>$ErrMsg</p>\n";
            exit(2);
        }
        $Row = $Result->fetch_assoc();
        $vcName = $Row['vcName'];
        $vcGender = trim($Row['vcGender']);
        $vcEmail = $Row['vcEmail'];

        if ($btnSubmit == 'Update')
        {
            $iSessionID = CleanSQLInput(substr(trim($_POST['txtSessionID']),0,4));
            if (isset($_POST['chkEnroll']))
            {
                $iClassIDs= $_POST['chkEnroll'];
            }
            else
            {
                $iClassIDs=array();
            }
            if (isset($_POST['cmbLF']))
            {
                $vcLFArray= $_POST['cmbLF'];
            }
            else
            {
                $vcLFArray=array();
            }
            $bOK = true;
            $strQuery = "SELECT iSchedID FROM vwSessReg where iUserID = $strUserID and iSessionNum = $iSessionID;";
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
                $iSchedID = $Row['iSchedID'];
                foreach ($iClassIDs as $value)
                {
                    if ($value == $iSchedID)
                    {
                        $bFound = TRUE;
                        break;
                    }
                }
                if (!$bFound)
                {
                    $strQuery = "delete from tblClassReg where iUserID = $strUserID and iSchedID = $iSchedID ;";
                    if (!CallSPNoOut($strQuery))
                    {
                        $bOK = false;
                    }
                }
            }

            foreach ($iClassIDs as $value)
            {
                $strQuery = "SELECT COUNT(*) b FROM tblClassReg WHERE iSchedID = $value AND iUserID = $strUserID LIMIT 1;";
                if (!$Result2 = $dbh->query ($strQuery))
                {
                    error_log ('Failed to fetch data. Error ('. $dbh->errno . ') ' . $dbh->error);
                    error_log ($strQuery);
                    print "<p class=\"Attn\" align=center>$ErrMsg</p>\n";
                    exit(2);
                }
                $Row2 = $Result2->fetch_assoc();
                $iRowCount = $Row2['b'];
                $vcLF = $vcLFArray[$value];
                if ($iRowCount == 0)
                {
                    $strQuery = "insert into tblClassReg (iSchedID,iUserID,vcLF) " .
                         "values ($value,$strUserID,'$vcLF');";
                    if (!CallSPNoOut($strQuery))
                    {
                        $bOK = false;
                    }
                }
                else
                {
                    $strQuery ="update tblClassReg set vcLF = '$vcLF' WHERE iSchedID = $value AND iUserID = $strUserID;";
                    if (!CallSPNoOut($strQuery))
                    {
                        $bOK = false;
                    }
                }
            }
            if ($bOK)
            {
                print "<p>Update successful, sending confirmation email.</p>\n";
                $strQuery = "SELECT vcClassName, vcDayName, dtClassTime, dtStarts FROM vwUserReg where iUserID = $strUserID and iSessionNum = $iSessionID order by dtStarts, dtClassTime;";
                if (!$Result = $dbh->query ($strQuery))
                {
                    error_log ('Failed to fetch data. Error ('. $dbh->errno . ') ' . $dbh->error);
                    error_log ($strQuery);
                    print "<p class=\"Attn\" align=center>$ErrMsg</p>\n";
                    exit(2);
                }

                switch ($SiteType)
                {
                    case "a":
                        $strMsgOut  = "*** DEV SITE *** \n Dear $vcName,\n";
                        break;
                    case "beta":
                        $strMsgOut  = "*** BETA SITE *** \n Dear $vcName,\n";
                        break;
                    default:
                        $strMsgOut  = "Dear $vcName,\n";
                        break;
                }
                $NumAffected = $Result->num_rows;
                switch ($NumAffected)
                {
                    case "0":
                        $strMsgOut .= "Thank you for your interest in classes with Studio B Dance. ";
                        $strMsgOut .= "At your request you have been unregistered from all classes in session $iSessionID.\n";
                        $strMsgOut .= "We hope to see you again soon.\n";
                        break;
                    case "1":
                        $strMsgOut .= "Thank you for registering for classes with Studio B Dance on. ";
                        $strMsgOut .= "Here is the class you are currently registered for in session $iSessionID.\n";
                        break;
                    default:
                        $strMsgOut .= "Thank you for registering for classes with Studio B Dance. ";
                        $strMsgOut .= "Here are the classes you are registered for in session $iSessionID.\n";
                        break;
                }
                while ($Row = $Result->fetch_assoc())
                {
                    $dtStart = $Row['dtStarts'];
                    $dtClassTime = $Row['dtClassTime'];
                    $strDayName = $Row['vcDayName'];
                    $strClassName=$Row['vcClassName'];
                    $strStartDate = date($strDateFormat, strtotime($dtStart));
                    $strTime = date($strTimeFormat, strtotime($dtClassTime ));
                    $strMsgOut .= "$strClassName $strDayName $strTime first class on $strStartDate \n";
                }
                $strMsgOut .= "\nSincerely,\nStudio B Dance\n";
                $strQuery = "SELECT concat(if(ifnull(vcLabel,'')<>'',concat(vcLabel,': '),''),vcValue) vcValue FROM tblContactInfo ORDER BY vcType, iSequence;";
                if (!$Result = $dbh->query ($strQuery))
                {
                    error_log ('Failed to fetch data. Error ('. $dbh->errno . ') ' . $dbh->error);
                    error_log ($strQuery);
                    print "<p class=\"Attn\" align=center>$ErrMsg</p>\n";
                    exit(2);
                }
                while ($Row = $Result->fetch_assoc())
                {
                    $vcValue = $Row['vcValue'];
                    $strMsgOut .= "$vcValue \n";
                }

                EmailText($ProfileNotify,"Copy of Class registration confirmation",$strMsgOut,$FromEmail);
                if(EmailText("$vcName|$vcEmail","Class registration confirmation",$strMsgOut,$FromEmail))
                {
                    print "<p>Confirmation email sent successfully</p>";
                }
                else
                {
                    print "<p>Confirmation email could not be sent successfully</p>";
                }
            }
            else
            {
                print "<p>There was an issue with the update</p>\n";
            }
        }

        if ($WritePriv <=  $Priv)
        {
            $strQuery = "SELECT iUserID, vcName FROM tblUsers ORDER BY vcName;";
        }
        else
        {
            $strQuery = "SELECT iUserID, vcName FROM tblUsers where iUserID = $iUserID or iDelegateUserID = $iUserID ORDER BY vcName ;";
        }

        if ($Priv >= $iStudentLevel)
        {
            if (!$Result = $dbh->query ($strQuery))
            {
                error_log ('Failed to fetch data. Error ('. $dbh->errno . ') ' . $dbh->error);
                error_log ($strQuery);
                print "<p class=\"Attn\" align=center>$ErrMsg</p>\n";
                exit(2);
            }
            print "<form method=\"POST\">\n";
            print "<input type=\"hidden\" name=\"txtSessionID\" value=\"$iSessionID\">";
            print "<p align=\"center\">Impersonate:\n";
            print("<select size=\"1\" name=\"cmbAsUser\">\n");

            while ($Row = $Result->fetch_assoc())
            {
                $iAsUID = $Row['iUserID'];
                $asName = $Row['vcName'];
                if ($iAsUID == $strUserID)
                {
                    print "<option selected value=\"$iAsUID \">$asName</option>\n";
                }
                else
                {
                    print "<option value=\"$iAsUID \">$asName</option>\n";
                }
            }
            print "</select>\n";
            print "<input type=\"Submit\" value=\"Impersonate\" name=\"btnSubmit\">\n";
            print "</form>\n";

            print "<p class=\"Header2\">You are operating as user $vcName</p>";
        }
        print "<p class=\"Header1\">Class Schedule for Class Session $iSessionID</p>\n";
        $strQuery = "SELECT dtSessionStart, iSessionLen FROM tblClassSession WHERE iSessionNum = $iSessionID;";
        if (!$Result = $dbh->query ($strQuery))
        {
            error_log ('Failed to fetch data. Error ('. $dbh->errno . ') ' . $dbh->error);
            error_log ($strQuery);
            print "<p class=\"Attn\" align=center>$ErrMsg</p>\n";
            exit(2);
        }
        $Row = $Result->fetch_assoc();
        $strStartDate = date("m/d/Y",strtotime($Row['dtSessionStart']));
        $iNumWeeks = $Row['iSessionLen'];
        $iNumDays = ($iNumWeeks * 7) - 1;
        $strMod = "+$iNumDays day";

        $EndDate = new DateTime($strStartDate);
        $EndDate->modify($strMod);
        $strEndDate = $EndDate->format("m/d/Y");

        print "<div align=\"center\">Runs from $strStartDate until $strEndDate</div>";

        $strQuery = "SELECT iSessionNum, dtSessionStart FROM vwActiveSessions ORDER BY iSessionNum;";
        if (!$Result = $dbh->query ($strQuery))
        {
            error_log ('Failed to fetch data. Error ('. $dbh->errno . ') ' . $dbh->error);
            error_log ($strQuery);
            print "<p class=\"Attn\" align=center>$ErrMsg</p>\n";
            exit(2);
        }
        $NumAffected = $Result->num_rows;
        if ($NumAffected == 0)
        {
            print "<p align=\"center\">No future session schedule available. ";
            print "Please contact $strContactEmail with your questions</p> ";
        }
        else
        {
            print "<form method=\"POST\">\n";
            print "<p align=\"center\">Change Session to:\n";
            print "<input type=\"hidden\" name=\"txtUID\" value=\"$strUserID\">";
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
        }

        $strQuery = "SELECT vcPDFSchedule FROM tblClassSession WHERE iSessionNum = '$iSessionID';;";
        if (!$Result = $dbh->query ($strQuery))
        {
            error_log ('Failed to fetch data. Error ('. $dbh->errno . ') ' . $dbh->error);
            error_log ($strQuery);print "<table width=\"1200\">\n<tr valign=\"top\">\n<td width=\"40%\">\n";
            print "<p class=\"Attn\" align=center>$ErrMsg</p>\n";
            exit(2);
        }
        $Row = $Result->fetch_assoc();
        $strPDFSchedule = $Row['vcPDFSchedule'];


        if ($strPDFSchedule)
        {
            print "<center><a href=\"$strPDFSchedule\" target=\"_blank\">";
            print "Click here to download printable schedule</a><br></center>";
        }
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
        print "<p class=\"Attn\" align=center>$ScheduleRequirements</p>\n";
        $strQuery = " SELECT iSchedID, vcDayName, vcClassName, dtClassTime, iLenght, iNumWeeks, "
                  . " dCost, iClassID, dtStarts, bCancelled, vcNote, iMaxStudent, SessNote "
                  . " FROM vwScheduleDay where iSessionNum = $iSessionID ORDER BY iWeekday, dtClassTime;";
        if (!$Result = $dbh->query ($strQuery))
        {
            error_log ('Failed to fetch data. Error ('. $dbh->errno . ') ' . $dbh->error);
            error_log ($strQuery);
            print "<p class=\"Attn\" align=center>$ErrMsg</p>\n";
            exit(2);
        }

        print "<form method=\"POST\">\n";
        print "<input type=\"hidden\" name=\"txtUID\" value=\"$strUserID\">";
        print "<input type=\"hidden\" name=\"txtSessionID\" value=\"$iSessionID\">";
        print "<table border=0 class=\"ClassListing\">";
        print "<tr><th>Class Time</th><th>Class Name</th>";
        if ($ClassDuration =="Duration")
        {
            print "<th>Class length</th>";
        }

        print "<th>Class<br>Duration</th><th>Cost of<br>series</th><th>Class Starts</th>";
        if ($ShowLastClass == "True")
        {
            print "<th>Last class</th>";
        }

        if ($Priv >= $iStudentLevel)
        {
            print "<th>Lead/Follow</th>";
            print "<th>Enroll</th>";
            print "<th width=\"10\">Open Spaces</th>";
        }
        print "</tr>";
        while ($Row = $Result->fetch_assoc())
        {
            $dtStart = $Row['dtStarts'];
            $dtClassTime = $Row['dtClassTime'];
            $strDayName = $Row['vcDayName'];
            $iClassID=$Row['iClassID'];
            $iSchedID=$Row['iSchedID'];
            $strClassName=$Row['vcClassName'];
            $iLength = $Row['iLenght'];
            $iNumWeeks = $Row['iNumWeeks'];
            $strClassNote = $Row['vcNote'];
            $strSessionNote = $Row['SessNote'];
            $iMaxStudent = $Row['iMaxStudent'];
            $bCancelled = $Row['bCancelled'];
            $iNumDays = ($iNumWeeks - 1) * 7;
            $iCost = $Row['dCost'];
            $strStartDate = date($strDateFormat, strtotime($dtStart));
            $strMod = "+$iNumDays day";

            $EndDate = new DateTime($dtStart);
            $EndDate->modify($strMod);
            $strEndDate = $EndDate->format($strDateFormat);
            if (intval($iLength)!=$iLength)
            {
                if ($CTUnit=="hour")
                {
                    $TUnit = "minute";
                    $iLength = intval(60 * $iLength);
                }
                else
                {
                    $TUnit = "minute";
                    $iLength = intval($iLength);
                }
            }
            else
            {
                $iLength = intval($iLength);
                $TUnit="hour";
            }

            $strTimeMod = "+$iLength $TUnit";
            $dtEnd = new DateTime($dtClassTime);
            $dtEnd->modify($strTimeMod);
            $dtEnd = $dtEnd->format($strTimeFormat);

            $strTime = date($strTimeFormat, strtotime($dtClassTime ));

            $strQuery = "SELECT COUNT(iSchedID) b FROM tblClassReg WHERE iSchedID = $iSchedID";
            if (!$Result2 = $dbh->query ($strQuery))
            {
                error_log ('Failed to fetch data. Error ('. $dbh->errno . ') ' . $dbh->error);
                error_log ($strQuery);
                print "<p class=\"Attn\" align=center>$ErrMsg</p>\n";
                exit(2);
            }
            $Row2 = $Result2->fetch_assoc();
            $iRegCount = $iMaxStudent - $Row2['b'];

            $strQuery = "SELECT iUserID, vcLF FROM tblClassReg WHERE iSchedID = $iSchedID AND iUserID = $strUserID LIMIT 1;";
            if (!$Result2 = $dbh->query ($strQuery))
            {
                error_log ('Failed to fetch data. Error ('. $dbh->errno . ') ' . $dbh->error);
                error_log ($strQuery);
                print "<p class=\"Attn\" align=center>$ErrMsg</p>\n";
                exit(2);
            }
            $Row2 = $Result2->fetch_assoc();
            $vcLF = $Row2['vcLF'];
            if ($vcLF)
            {
                $strChecked = "checked";
            }
            else
            {
                $strChecked = "";
                if ($vcGender=="male")
                {
                    $vcLF = "Lead";
                }
                else
                {
                    $vcLF = "Follow";
                }
            }

            if($iRegCount < 1 and !$strChecked)
            {
                $bDisable = "disabled";
            }
            else
            {
                $bDisable = "";
            }

            $iStartDate = strtotime($strStartDate);
            $iNow = time();
            if ($strChecked and $iStartDate  <= $iNow)
            {
                $bHide = "hidden";
            }
            else
            {
                $bHide = "";
            }

            if ($WritePriv <=  $Priv)
            {
                $bDisable = "";
                $bHide = "";
            }

            if ($iLength>1)
            {
                $strLenUnit = $TUnit . "s";
            }
            else
            {
                $strLenUnit = $TUnit;
            }
            if ($ClassDuration =="startstop")
            {
                $strTime .= " - $dtEnd";
            }
            if ($strDayName != $strDN)
            {
                print "<tr>\n";
                print "<td class=\"DOW\">$strDayName</td>\n";
                print "<td colspan=\"9\" class=\"DayClassNote\">$strSessionNote</td>\n";
                print "</tr>\n";
                $strDN = $strDayName;
            }
            if ($bCancelled==1)
            {
                print "<tr class=\"CancelledClass\">";
                if (!$strClassNote)
                {
                    $strClassNote = "This class has been Cancelled";
                }
            }
            else
            {
                print "<tr>";
            }
            print "<td><a id=\"$iSchedID\">$strTime</a><td>";
            print "<a href=\"Classes.php?a=Descr#$iClassID\">$strClassName</a></td>";
            if ($ClassDuration =="Duration")
            {
                print "<td>$iLength $strLenUnit</td>";
            }

            print "<td>$iNumWeeks weeks</td>";
            print "<td>\$$iCost</td>";
            print "<td>$strStartDate</td>";
            if ($ShowLastClass == "True")
            {
                print "<td>$strEndDate</td>";
            }

            if ($bCancelled==0 and $Priv >= $iStudentLevel)
            {
                print "<td align=\"center\">\n";
                print "<select name=\"cmbLF[$iSchedID]\">\n";
                if ($vcLF=="Lead")
                {
                    print "<option selected value=\"Lead\">Lead</option>\n";
                }
                else
                {
                    print "<option value=\"Lead\">Lead</option>\n";
                }
                if ($vcLF=="Follow")
                {
                    print "<option selected value=\"Follow\">Follow</option>\n";
                }
                else
                {
                    print "<option value=\"Follow\">Follow</option>\n";
                }
                if ($vcLF=="Either")
                {
                    print "<option selected value=\"Either\">Either</option>\n";
                }
                else
                {
                    print "<option value=\"Either\">Either</option>\n";
                }
                print "</select>\n";
                print "</td>\n";
                print "<td align=\"center\">\n";
                print "<input type=\"checkbox\" name=\"chkEnroll[]\" value=\"$iSchedID\" $strChecked $bDisable $bHide>\n";
                if ($bHide)
                {
                    print "no drop";
                }
                print "</td>\n";
                print "<td align=\"center\">$iRegCount</td>\n";
            }

            print "</tr>\n";
            if ($strClassNote)
            {
                print "<tr><td colspan=\"9\" class=\"ClassNote\">$strClassNote</td></tr>";
            }
        }
        if ($Priv >= $iStudentLevel)
        {
            print "<tr>\n<td colspan=20 align=right>\n<input type=\"Submit\" value=\"Update\" name=\"btnSubmit\">\n</td>\n</tr>\n";
        }
        print "</table>\n";
        print "</form>";
    }

    require("footer.php");
?>
