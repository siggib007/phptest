<?php
    require "header.php";
    
    $iDayOfMonth = 1;
    $strMonthFormat = "F Y";
    $strISODateFormat = "Y-m-d";
    

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
    
    if (isset($_POST['txtMonth']))
    {
        $strCurMonth = $_POST['txtMonth'];
    }
    else
    {
        $strCurMonth = date($strMonthFormat);
    }
    
    if ($btnSubmit == 'Previous')
    {
        $dtDate = new DateTime($strCurMonth);
        $dtDate->modify("-1 month");
        $strCurMonth = $dtDate->format($strMonthFormat);
    }
    
    if ($btnSubmit == 'Next')
    {
        $dtDate = new DateTime($strCurMonth);
        $dtDate->modify("+1 month");
        $strCurMonth = $dtDate->format($strMonthFormat);
    }
    print "<p class=\"Header1\">Calendar</p>\n";
    
    $iFirstDay = intval(date("w",  strtotime("$iDayOfMonth $strCurMonth")));
    $iLastDay = intval(date("t", strtotime($strCurMonth)));
    
    print "<p></p>\n";
    print "<p>\n<div align=center>\n";
    print "<form method=\"POST\">\n";
    print "<input type=\"hidden\" name=\"txtMonth\" value=\"$strCurMonth\">\n";
    
    if ($strCurMonth == date($strMonthFormat))
    {
        print "<input type=\"Submit\" value=\"Previous\" name=\"btnSubmit\" disabled>\n";   
    }
    else
    {
        print "<input type=\"Submit\" value=\"Previous\" name=\"btnSubmit\">\n";
    }
    
    print "<span class=\"Header2\">$strCurMonth</span>\n";
    print "<input type=\"Submit\" value=\"Next\" name=\"btnSubmit\">\n";
    print "</form>\n";
    print "</div>\n</p>\n";
    
    print "<table  class=\"Calendar\">\n";
    print "<tr>\n";
    print "<th class=\"Calendar\">Sunday</th>\n";
    print "<th class=\"Calendar\">Monday</th>\n";
    print "<th class=\"Calendar\">Tuesday</th>\n";
    print "<th class=\"Calendar\">Wednesday</th>\n";
    print "<th class=\"Calendar\">Thursday</th>\n";
    print "<th class=\"Calendar\">Friday</th>\n";
    print "<th class=\"Calendar\">Saturday</th>\n";
    print "</tr>\n";
    print "<tr>\n";
    for ($i=0;$i<$iFirstDay;$i++)
    {
        print "<td class=\"Calendar\"></td>\n";
    }
    while ($iDayOfMonth<=$iLastDay)
    {
        print "<td class=\"Calendar\">\n";
        print "<span class=\"CalDay\">$iDayOfMonth</span>\n";
        $strCurDate = date($strISODateFormat,  strtotime("$iDayOfMonth $strCurMonth"));
        $strQuery ="SELECT vcComment, vcPicturePath, bClosed FROM tblCalItems WHERE dtDate = '$strCurDate';";
        if (!$Result = $dbh->query ($strQuery))
        {
            error_log ('Failed to fetch data. Error ('. $dbh->errno . ') ' . $dbh->error);
            error_log ($strQuery);
            print "<p class=\"Attn\" align=center>$ErrMsg</p>\n";
            exit(2);
        }
        $Row = $Result->fetch_assoc();
        $strComment = $Row['vcComment'];
        $strPict = $Row['vcPicturePath'];
        $iClosed = $Row['bClosed'];
        if ($iClosed == 1)
        {
            print "<span class=\"CalClosed\"> CLOSED</span>\n";
        }
        if ($strComment)
        {
            print "<div>$strComment</div>\n";
            print "<img src=\"$strPict\" height=\"35\">\n";
        }
        $strQuery = "SELECT dtStartTime, dtStopTime FROM tblLDTSchedule " . 
                    "WHERE dtDate = '$strCurDate' ";
        if (!$Result = $dbh->query ($strQuery))
        {
            error_log ('Failed to fetch data. Error ('. $dbh->errno . ') ' . $dbh->error);
            error_log ($strQuery);
            print "<p class=\"Attn\" align=center>$ErrMsg</p>\n";
            exit(2);
        }
        while ($Row = $Result->fetch_assoc())
        {
            $strLDTStart = date($strTimeFormat,strtotime($Row['dtStartTime']));
            $strLDTEnd = date($strTimeFormat,strtotime($Row['dtStopTime']));
            $strEventName = $ConfArray['LDCalName'];
            $strPict = $ConfArray['LDCalImg'];
            print "<img src=\"$strPict\" height=\"35\">\n";
            print "<div>\n";
            print "$strLDTStart $strEventName";
            print "</div>\n";
        }
        if ($iClosed == 0)
        {
            $strQuery = "SELECT dtClassTime, vcClassName, iSchedID, iSessionNum FROM vwSchedTime " .
                        "WHERE dtStarts <= '$strCurDate' AND dtEnds >= '$strCurDate' ". 
                        "AND iWeekday = '$i' order by dtclasstime;";
            if (!$Result = $dbh->query ($strQuery))
            {
                error_log ('Failed to fetch data. Error ('. $dbh->errno . ') ' . $dbh->error);
                error_log ($strQuery);
                print "<p class=\"Attn\" align=center>$ErrMsg</p>\n";
                exit(2);
            }
            while ($Row = $Result->fetch_assoc())
            {
                $strClassTime = date($strTimeFormat,strtotime($Row['dtClassTime']));
                $strClassName = $Row['vcClassName'];
                $strSchedID = $Row['iSchedID'];
                $strSessNum = $Row['iSessionNum'];
                print "<div>\n";
                print "<a href=\"Classes.php?sess=$strSessNum#$strSchedID\">";
                print "$strClassTime $strClassName";
                print "</a>\n";
                print "</div>\n";
            }
        }
        $strQuery = "SELECT iEventID, vcEventName, TIME(dtEventDate) as dtEvenTime FROM tblEvents " . 
                    "WHERE DATE(dtEventDate) = '$strCurDate' ORDER BY dtEventDate ";
        if (!$Result = $dbh->query ($strQuery))
        {
            error_log ('Failed to fetch data. Error ('. $dbh->errno . ') ' . $dbh->error);
            error_log ($strQuery);
            print "<p class=\"Attn\" align=center>$ErrMsg</p>\n";
            exit(2);
        }
        while ($Row = $Result->fetch_assoc())
        {
            $strEventTime = date($strTimeFormat,strtotime($Row['dtEvenTime']));
            $strEventName = $Row['vcEventName'];
            $strEventID = $Row['iEventID'];
            print "<div>\n";
            print "<a href=\"Events.php#$strEventID\">";
            print "$strEventTime $strEventName";
            print "</a>\n";
            print "</div>\n";
        }
        print "</td>";
        $iDayOfMonth ++;
        if ($i==6)
        {
            $i = 0;
            print "</tr>\n";
            print "<tr>\n";
        }
        else
        {
            $i++;
        }
    }
    while ($i<=6)
    {
        print "<td class=\"Calendar\"></td>\n";
        $i++;
    }
        
    print "</table>\n";
    
    require "footer.php";
?>
