<?php
    require_once("header.php");
    if ($strReferer != $strPageURL and $PostVarCount > 0)
    {
        print "<p class=\"Error\">Invalid operation, Bad Reference!!!</p> ";
        exit;
    }
    if (isset($_POST['cmbUser']))
    {
        $iUserArray = $_POST['cmbUser'];
    }
    else
    {
        $iUserArray = array();
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
       $iPrivLevel = CleanSQLInput(substr(trim($_POST['cmbPriv']),0,4));
    }
    else
    {
        $iPrivLevel = 100;
    }

    if ($btnSubmit == 'Update')
    {
       $iPrivUpdate = CleanSQLInput(substr(trim($_POST['cmbPrivUpdate']),0,4));
       $iPrivLevel = CleanSQLInput(substr(trim($_POST['iPrivLvl']),0,4));
       $strUserList = "";
       foreach ($iUserArray as $val)
       {
           $strUserList .= "$val, ";
       }
       $strUserList .= "-55";
       $strQuery = "update tblUsers set iPrivLevel = $iPrivUpdate where iUserID in ($strUserList);";
       UpdateSQL ($strQuery,"update");

    }


    print "<p class=\"Header1\">Priviledge Administration</p>\n";

    print "<form method=\"POST\">\n";
    print "<p align=\"center\">Change Priviledges for anyone who's priviledge is less than:\n";
    $strQuery = "SELECT iPrivLevel, vcPrivName FROM tblprivlevels ORDER BY iPrivLevel;";
    if (!$Result = $dbh->query ($strQuery))
    {
        error_log ('Failed to fetch data. Error ('. $dbh->errno . ') ' . $dbh->error);
        error_log ($strQuery);
        print "<p class=\"Attn\" align=center>$ErrMsg</p>\n";
        exit(2);
    }
    print("<select size=\"1\" name=\"cmbPriv\">\n");

    while ($Row = $Result->fetch_assoc())
    {
        $iPrivlvl = $Row['iPrivLevel'];
        $PrivName = $Row['vcPrivName'];

        if ($iPrivlvl == $iPrivLevel)
        {
            print "<option selected value=\"$iPrivlvl \">$PrivName</option>\n";
        }
        else
        {
            print "<option value=\"$iPrivlvl \">$PrivName </option>\n";
        }
    }
    print "</select>\n";
    print "<input type=\"Submit\" value=\"Change\" name=\"btnSubmit\">\n";
    print "</form>\n";

    print "<form method=\"POST\">\n";
    print "<input type=\"hidden\" name=\"iPrivLvl\" value=\"$iPrivLevel\">";
    $strQuery = "SELECT iUserID, vcName FROM tblUsers WHERE iPrivLevel < $iPrivLevel ORDER BY vcName;";
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
            print "<p align=\"center\">No one has less priviledges than that</p>";
            break;
        case "1":
            print "<p align=\"center\">Only one person has less priviledges than that</p>";
            break;
        default:
            print "<p align=\"center\">There are $NumAffected with less priviledges</p>";
            break;
    }
    print "<p align=\"center\">Choose users to change priviledges for:\n<br>\n";
    print("<select size=\"10\" multiple name=\"cmbUser[]\" style=\"min-width: 100px;\">\n");

    while ($Row = $Result->fetch_assoc())
    {
        $iUIDdb = $Row['iUserID'];
        $vcName = $Row['vcName'];
        print "<option value=\"$iUIDdb \">$vcName </option>\n";
    }
    print "</select>\n<br>";
    print "Change to:";
    $strQuery = "SELECT iPrivLevel, vcPrivName FROM tblprivlevels ORDER BY iPrivLevel;";
    if (!$Result = $dbh->query ($strQuery))
    {
        error_log ('Failed to fetch data. Error ('. $dbh->errno . ') ' . $dbh->error);
        error_log ($strQuery);
        print "<p class=\"Attn\" align=center>$ErrMsg</p>\n";
        exit(2);
    }
    print("<select size=\"1\" name=\"cmbPrivUpdate\">\n");

    while ($Row = $Result->fetch_assoc())
    {
        $iPrivlvl = $Row['iPrivLevel'];
        $PrivName = $Row['vcPrivName'];

        if ($iPrivlvl == $iPrivLevel)
        {
            print "<option selected value=\"$iPrivlvl \">$PrivName</option>\n";
        }
        else
        {
            print "<option value=\"$iPrivlvl \">$PrivName </option>\n";
        }
    }
    print "</select>\n";

    print "<input type=\"Submit\" value=\"Update\" name=\"btnSubmit\">\n";
    print "</form>\n <br>\n";

    require("footer.php");
?>
