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

    print("<p class=\"Header1\">Line Dance Team Schedule Administration</p>\n");

    if ($btnSubmit == 'Save')
    {
        $strDate = date("Y-m-d",  strtotime($_POST['txtDate']));
        $dtStartTime = date("H:i:s",  strtotime($_POST['StartTime']));
        $dtStopTime = date("H:i:s",  strtotime($_POST['StopTime']));
        $iSchedID = CleanSQLInput(substr(trim($_POST['iSchedID']),0,49));

        $strQuery = "update tblLDTSchedule set dtDate = '$strDate', dtStartTime = '$dtStartTime', dtStopTime = '$dtStopTime' where iID = $iSchedID;";
        UpdateSQL ($strQuery,"update");
    }

    if ($btnSubmit == 'Delete')
    {
        $iSchedID = substr(trim($_POST['iSchedID']),0,49);

        $strQuery = "delete from tblLDTSchedule where iID = $iSchedID;";
        UpdateSQL ($strQuery,"delete");
    }

    if ($btnSubmit == 'Insert')
    {
        $strDate = date("Y-m-d",  strtotime($_POST['txtDate']));
        $dtStartTime = date("H:i:s",  strtotime($_POST['StartTime']));
        $dtStopTime = date("H:i:s",  strtotime($_POST['StopTime']));

        $strQuery = "insert tblLDTSchedule (dtDate, dtStartTime, dtStopTime) values ('$strDate','$dtStartTime','$dtStopTime');";
        UpdateSQL ($strQuery,"insert");
    }

    //Print the normal form after update is complete.
    print "<table>\n";
    print "<tr>\n";
    print "<th class=lbl>Update existing schedule</th>\n";
    print "<th width = 100></th>\n<th class=lbl>Or Insert New one</th>\n";
    print "</tr>\n";
    print "<tr>\n";
    print "<td>\n";
    print "<table border = 0>\n";
    print "<tr>\n";
    print "<th></th>\n<th>Date</th>\n<th>Start Time</th>\n<th>Stop Time</th>\n";
    print "</tr>\n";
    $strQuery = "SELECT dtDate, dtStartTime, dtStopTime, iID FROM tblLDTSchedule order by dtDate;";
    if (!$Result = $dbh->query ($strQuery))
    {
        error_log ('Failed to fetch data. Error ('. $dbh->errno . ') ' . $dbh->error);
        error_log ($strQuery);
        exit(2);
    }
    while ($Row = $Result->fetch_assoc())
    {
        $strDate = $Row['dtDate'];
        $dtStartTime = $Row['dtStartTime'];
        $dtStopTime = $Row['dtStopTime'];
        $iSchedID = $Row['iID'];
        if ($WritePriv <=  $Priv)
        {
            print "<form method=\"POST\">\n";
            print "<tr valign=\"top\">\n";
            print "<td class=\"lbl\"><input type=\"hidden\" value=\"$iSchedID\" name=\"iSchedID\"> </td>\n";
            print "<td><input type=\"date\" value=\"$strDate\" name=\"txtDate\" ></td>\n";
            print "<td><input type=\"text\" value=\"$dtStartTime\" name=\"StartTime\" size=\"8\" ></td>\n";
            print "<td><input type=\"text\" value=\"$dtStopTime\" name=\"StopTime\" size=\"8\" ></td>\n";
            print "<td><input type=\"Submit\" value=\"Save\" name=\"btnSubmit\"></td>";
            print "<td><input type=\"Submit\" value=\"Delete\" name=\"btnSubmit\"></td>";
            print "</tr>\n";
            print "</form>\n";
        }
        else
        {
                print "$strDate : $dtStartTime<br>\n";
        }
    }
    print "</table>\n";
    print "</td>\n";
    print "<td>\n</td>\n";
    print "<td valign=\"top\">\n";
    print "<form method=\"POST\">\n";
    print "<table>\n";
    print "<tr>\n";
    print "<td align = right class = lbl>Date: </td>\n";
    print "<td><input type=\"date\" name=\"txtDate\"></td>\n";
    print "</tr>\n";
    print "<tr>\n";
    print "<td align = right class = lbl>Start Time: </td>\n";
    print "<td><input type=\"text\" name=\"StartTime\" size=\"8\" ></td>";
    print "</tr>\n";
    print "<tr>\n";
    print "<td align = right class = lbl>Stop Time: </td>\n";
    print "<td><input type=\"text\" name=\"StopTime\" size=\"8\" ></td>";
    print "</tr>\n";
    print "<tr>\n";
    print "<td colspan=2 align=center>";
    print "<input type=\"Submit\" value=\"Insert\" name=\"btnSubmit\">";
    print "</td>\n";
    print "</tr>\n";
    print "</table>\n";
    print "</form>\n";
    print "</td>\n";
    print "</tr>\n";
    print "</table>";

    require("footer.php");
?>
