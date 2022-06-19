<?php
    require("header.php");

    if ($strReferer != $strPageURL and $PostVarCount > 0)
    {
            print "<p class=\"Error\">Invalid operation, Bad Reference!!!</p> ";
            exit;
    }

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
        $iNumWeeks = CleanSQLInput(substr(trim($_POST['txtNumWeeks']),0,4));
        $strStartDate = CleanSQLInput(substr(trim($_POST['txtStartDay']),0,14));

        $strISOStartDate = date("Y-m-d",strtotime($strStartDate));

        $strQuery = "update tblClassSession set iSessionLen='$iNumWeeks', dtSessionStart='$strISOStartDate' "
                  . " where iSessionNum=$iSessionID;";

        UpdateSQL ($strQuery,"update");
    }

    if ($btnSubmit == 'New')
    {
        $iSessionID = CleanSQLInput(substr(trim($_POST['iSessionID']),0,4));
        CreateSession($iSessionID);
    }

    if ($btnSubmit == 'Copy')
    {
        $iSessionID = CleanSQLInput(substr(trim($_POST['iSessionID']),0,4));
        $FromiSessionID = $iSessionID - 1;
        CreateSession($iSessionID);
        $strQuery = "insert into tblClassSched (iSessionNum, iClassID, iWeekday, dtClassTime, dCost, "
                      . "iLenght, iNumWeeks, dtStarts)"
                      . "SELECT $iSessionID, iClassID, iWeekday, dtClassTime, dCost, iLenght, iNumWeeks, "
                      .  "date_add(dtSTarts, interval iNumWeeks week) "
                      .  "FROM tblClassSched where iSessionNum = $FromiSessionID";
        UpdateSQL ($strQuery,"insert");
    }

    if ($btnSubmit == 'upload')
    {
        $iSessionID = CleanSQLInput(substr(trim($_POST['iSessionID']),0,4));
        $strPDFName = $strPDFBaseName . $iSessionID . ".pdf";
        $DocRoot = "PDFSchedules/";
        $newPath = $DocRoot . $strPDFName;
        if (isset($_FILES['Docfile']))
        {
            $FilesVarCount = count($_FILES['Docfile']['name']);
            if ($FilesVarCount == 1 and $_FILES['Docfile']['name']=="")
            {
                print "<p class=\"Error\">No File Chosen</p>";
            }
            else
            {
                $tmpFile = $_FILES['Docfile']['tmp_name'];
                $Error = $_FILES['Docfile']['error'];
                $DocFileName = $_FILES['Docfile']['name'];
                $DocBaseName = basename($DocFileName);
                if ($Error == UPLOAD_ERR_OK)
                {
                    if (move_uploaded_file($tmpFile, $newPath))
                    {
                        $strQuery = "update tblClassSession set vcPDFSchedule='$newPath'"
                                  . " where iSessionNum=$iSessionID;";
                        UpdateSQL ($strQuery,"update");
                        print "<div class=\"MainText\">File $DocBaseName uploaded and move "
                             ."to $newPath successfully</div>";
                    }
                    else
                    {
                        print "<p class=\"Error\">Couldn't move file to $newPath</p>";
                    }
                }
                else
                {
                    $ErrMsg = codeToMessage($Error);
                    print "<p class=\"Error\">Error \"$ErrMsg\" while uploading $DocFileName</p>\n";
                }

            }
        }
    }

    $strQuery = "SELECT dtSessionStart, iSessionLen, vcPDFSchedule FROM tblClassSession WHERE iSessionNum = $iSessionID;";
    if (!$Result = $dbh->query ($strQuery))
    {
        error_log ('Failed to fetch data. Error ('. $dbh->errno . ') ' . $dbh->error);
        error_log ($strQuery);
        print "<p class=\"Attn\" align=center>$ErrMsg</p>\n";
        exit(2);
    }
    $Row = $Result->fetch_assoc();
    $iSessionStart = date("Y-m-d",strtotime($Row['dtSessionStart']));
    $iSessionLen = $Row['iSessionLen'];
    $strPDFName = $Row['vcPDFSchedule'];

    $strQuery = "SELECT max(iSessionNum) iSessionNum FROM tblClassSession;";
    if (!$Result = $dbh->query ($strQuery))
    {
        error_log ('Failed to fetch data. Error ('. $dbh->errno . ') ' . $dbh->error);
        error_log ($strQuery);print "<table width=\"1200\">\n<tr valign=\"top\">\n<td width=\"40%\">\n";
        print "<p class=\"Attn\" align=center>$ErrMsg</p>\n";
        exit(2);
    }
    $Row = $Result->fetch_assoc();
    $iNextSess = $Row['iSessionNum'] + 1;
    $iCopyFromSession = $iNextSess - 1;

    //Print the normal form after update is complete.
    print "<p class=\"Header1\">Class Session Administration</p>\n";

    print "<table width=\"90%\">\n";
    print "<tr valign=\"top\">\n";
    print "<td width=\"50%\">\n";
    print "<form method=\"POST\">\n";
    print "<p class=\"lbl\">Change Session to:\n";
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
    print "<input type=\"Submit\" value=\"Change\" name=\"btnSubmit\">\n</p>\n";
    print "</form>\n";

    print "<form method=\"POST\">\n";
    print "<input type=\"hidden\" value=\"$iSessionID\" name=\"iSessionID\">\n";
    print "<table width=\"550\" border=0>\n";
    print "<tr><td colspan=2 align=center class=Header2>Manage Session #$iSessionID</td></tr>\n";
    print "<tr>\n<td class=\"lblright\" width=\"275\">PDF Schedule file uploaded:</td>\n";
    print "<td>$strPDFName</td>\n</tr>\n";
    print "<tr>\n<td class=\"lblright\" width=\"50%\">Number of Weeks:</td>\n";
    print "<td><input type=\"text\" name=\"txtNumWeeks\" size=\"7\" value=\"$iSessionLen\"></td>\n</tr>\n";
    print "<tr>\n<td class=\"lblright\">First day of Class:</td>\n";
    print "<td><input type=\"date\" name=\"txtStartDay\" size=\"15\" value=\"$iSessionStart\"></td>\n</tr>\n";
    print "<tr><td colspan=2 align=center><input type=\"Submit\" value=\"Update\" name=\"btnSubmit\"></td></tr>\n";
    print "</table>\n";
    print "</form>\n";

    print "<br>\n";
    print "<form enctype=\"multipart/form-data\" method=\"POST\">\n";
    print "<input type=\"hidden\" value=\"$iSessionID\" name=\"iSessionID\">\n";
    print "<table width=\"550\" border=0>\n";
    print "<tr><td colspan=2 align=center class=Header2>Upload a downloadable PDF version of the schedule</td></tr>\n";
    print "<tr>\n<td  class=\"lblright\" width=\"50%\">File name: </td>\n";
    print "<td><input type=\"file\" name=\"Docfile\" size=\"50\"></td>\n</tr>\n";
    print "<tr><td colspan=2 align=center><input type=\"Submit\" value=\"upload\" name=\"btnSubmit\"></td></tr>\n";
    print "</table>\n";
    print "</form>\n";
    print "</td>\n";

    print "<td align=center>\n";
    print "<form method=\"POST\">\n";
    print "<input type=\"hidden\" value=\"$iNextSess\" name=\"iSessionID\">\n";
    print "<p>Create Session $iNextSess and copy all scheduled classes "
            . "from session $iCopyFromSession\n";

    print "<input type=\"Submit\" value=\"Copy\" name=\"btnSubmit\">\n</p>\n";
    print "</form>\n";

    print "<form method=\"POST\">\n";
    print "<input type=\"hidden\" value=\"$iNextSess\" name=\"iSessionID\">\n";
    print "Create blank session $iNextSess <input type=\"Submit\" value=\"New\" name=\"btnSubmit\">";
    print "</form>\n";
    print "</td>\n";
    print "</tr>\n";
    print "</table>\n";

    require("footer.php");

    function CreateSession($iSessionID)
    {
        $dbh = $GLOBALS['dbh'];
        $ErrMsg = $GLOBALS['ErrMsg'];
        $DefNumWeeks = $GLOBALS['DefNumWeeks'];

        $iLastSessionID = $iSessionID - 1;

        $strQuery = "SELECT dtSessionStart, iSessionLen FROM tblClassSession WHERE iSessionNum = $iLastSessionID;";
        if (!$Result = $dbh->query ($strQuery))
        {
            error_log ('Failed to fetch data. Error ('. $dbh->errno . ') ' . $dbh->error);
            error_log ($strQuery);
            print "<p class=\"Attn\" align=center>$ErrMsg</p>\n";
            exit(2);
        }
        $Row = $Result->fetch_assoc();
        $strStartDate = date("Y-m-d",strtotime($Row['dtSessionStart']));
        $iNumWeeks = $Row['iSessionLen'];

        $iNumDays = ($iNumWeeks) * 7;
        $strMod = "+$iNumDays day";

        $EndDate = new DateTime($strStartDate);
        $EndDate->modify($strMod);
        $strEndDate = $EndDate->format("Y-m-d");
        $strQuery = "INSERT INTO tblClassSession (iSessionNum, dtSessionStart, iSessionLen)"
                                       . "VALUES ('$iSessionID', '$strEndDate', '$DefNumWeeks')";
        UpdateSQL ($strQuery,"insert");
    }
?>
