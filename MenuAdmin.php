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
    print "<p class=\"Header1\">Menu Maintenace</p>\n";
    if ($btnSubmit == 'Edit')
    {
        print "<form method=\"POST\">\n<input type=\"Submit\" value=\"Go Back\" name=\"btnSubmit\"></form>";
        $iMenuID = substr(trim($_POST['MenuID']),0,49);
        $strQuery = "SELECT * FROM tblmenu where iMenuID = $iMenuID;";
        if (!$Result = $dbh->query ($strQuery))
        {
            error_log ('Failed to fetch Menu data. Error ('. $dbh->errno . ') ' . $dbh->error);
            error_log ($strQuery);
            print "<p class=\"Attn\" align=center>$ErrMsg</p>\n";
            exit(2);
        }
        $Row = $Result->fetch_assoc();
        if ($Row['bSecure'] == 0)
        {
            $strChecked = "";
        }
        else
        {
            $strChecked = "checked";
        }
        if ($Row['bNewWindow'] == 0)
        {
            $bWindow = "";
        }
        else
        {
            $bWindow = "checked";
        }
        print "<form method=\"POST\">\n";
        print "<table border=\"0\" width=\"850\">\n";
        print "<input type=\"hidden\" name=\"MenuID\" size=\"5\" value=\"$iMenuID\"></p>";
        print "<tr><td width=\"280\" align=\"right\" class=\"lbl\">Menu Title: </td>\n";
        print "<td width=\"520\"><input type=\"text\" name=\"txtTitle\" size=\"50\" value=\"{$Row['vcTitle']}\"></td></tr>\n";
        print "<tr><td width=\"280\" align=\"right\" class=\"lbl\">Page Header:</td>\n";
        print "<td width=\"520\"><input type=\"text\" name=\"txtHeader\" size=\"50\" value=\"{$Row['vcHeader']}\"></td></tr>";
        print "<tr><td width=\"280\" align=\"right\" class=\"lbl\">Contains sensitive data:</td>\n";
        print "<td width=\"520\"><input type=\"checkbox\" name=\"chkSensitive\" $strChecked></td></tr>";
        print "<tr><td width=\"280\" align=\"right\" class=\"lbl\">Show in new window:</td>\n";
        print "<td width=\"520\"><input type=\"checkbox\" name=\"chkNewWindow\" $bWindow></td></tr>";
        print "<tr><td width=\"280\" align=\"right\" class=\"lbl\">Read Priviledge Required:</td>";
        print "<td><select size=\"1\" name=\"cmbReadPrivLevel\">\n";
        $strQuery = "select * from tblprivlevels;";
        if (!$Result2 = $dbh->query ($strQuery))
        {
            error_log ('Failed to fetch privlevels data. Error ('. $dbh->errno . ') ' . $dbh->error);
            error_log ($strQuery);
            print "<p class=\"Attn\" align=center>$ErrMsg</p>\n";
            exit(2);
        }
        while ($Row2 = $Result2->fetch_assoc())
        {
            if ($Row2['iPrivLevel'] == $Row['iReadPriv'])
            {
                print "<option selected value=\"{$Row2['iPrivLevel']}\">{$Row2['vcPrivName']}</option>\n";
            }
            else
            {
                print "<option value=\"{$Row2['iPrivLevel']}\">{$Row2['vcPrivName']}</option>\n";
            }
        }
        print "</select>\n</td>\n</tr>";
        print "<tr><td width=\"280\" align=\"right\" class=\"lbl\">Write Priviledge Required:</td>";
        print "<td><select size=\"1\" name=\"cmbWritePrivLevel\">\n";
        $strQuery = "select * from tblprivlevels;";
        if (!$Result2 = $dbh->query ($strQuery))
        {
            error_log ('Failed to fetch privlevels data. Error ('. $dbh->errno . ') ' . $dbh->error);
            error_log ($strQuery);
            print "<p class=\"Attn\" align=center>$ErrMsg</p>\n";
            exit(2);
        }
        while ($Row2 = $Result2->fetch_assoc())
        {
            if ($Row2['iPrivLevel'] == $Row['iWritePriv'])
            {
                print "<option selected value=\"{$Row2['iPrivLevel']}\">{$Row2['vcPrivName']}</option>\n";
            }
            else
            {
                print "<option value=\"{$Row2['iPrivLevel']}\">{$Row2['vcPrivName']}</option>\n";
            }
        }
        print "</select>\n</td>\n</tr>";
        print "<tr><td width=\"280\" align=\"right\" class=\"lbl\">Administrative category:</td>";
        print "<td><select size=\"1\" name=\"cmbAdminCat\">\n";
        $strQuery = "select * from tblAdminCategories;";
        if (!$Result2 = $dbh->query ($strQuery))
        {
            error_log ('Failed to fetch Admin Category data. Error ('. $dbh->errno . ') ' . $dbh->error);
            error_log ($strQuery);
            print "<p class=\"Attn\" align=center>$ErrMsg</p>\n";
            exit(2);
        }
        while ($Row2 = $Result2->fetch_assoc())
        {
            if ($Row2['iCatID'] == $Row['bAdmin'])
            {
                print "<option selected value=\"{$Row2['iCatID']}\">{$Row2['vcCatName']}</option>\n";
            }
            else
            {
                print "<option value=\"{$Row2['iCatID']}\">{$Row2['vcCatName']}</option>\n";
            }
        }
        print "</select>\n</td>\n</tr>";
        print "<tr><td colspan=\"2\" align=\"center\"><input type=\"Submit\" value=\"Save\" name=\"btnSubmit\"></td></tr>";
        print "</table></form>\n";
        print "<form method=\"POST\">\n<input type=\"Submit\" value=\"Go Back\" name=\"btnSubmit\"></form>";
    }

    if ($btnSubmit == 'Save')
    {
        $strTitle = substr(trim($_POST['txtTitle']),0,49);
        $strHeader = substr(trim($_POST['txtHeader']),0,49);
        $iReadPriv = substr(trim($_POST['cmbReadPrivLevel']),0,6);
        $iWritePriv = substr(trim($_POST['cmbWritePrivLevel']),0,6);
        $iAdminCatID = substr(trim($_POST['cmbAdminCat']),0,6);
        $iMenuID = substr(trim($_POST['MenuID']),0,4);
        if (isset($_POST['chkSensitive']))
        {
           $bSensitive = 1;
        }
        else
        {
            $bSensitive = 0;
        }
        if (isset($_POST['chkNewWindow']))
        {
           $bWindow = 1;
        }
        else
        {
            $bWindow = 0;
        }
        $strQuery = "update tblmenu set vcTitle = '$strTitle', vcHeader = '$strHeader', bAdmin = '$iAdminCatID', " .
                    " iReadPriv = $iReadPriv, iWritePriv = $iWritePriv, bSecure = $bSensitive, bNewWindow = $bWindow " .
                    " where iMenuID=$iMenuID";
        UpdateSQL ($strQuery,"update");
    }

    if ($btnSubmit == 'Update Position')
    {
        $NewHeadPos = substr(trim($_POST['NewHeadPos']),0,4);
        $OldHeadPos = substr(trim($_POST['OldHeadPos']),0,4);
        $iMenuID = substr(trim($_POST['MenuID']),0,4);

        if ($NewHeadPos > 0)
        {
            $strQuery = "CALL spMovePos ('$iMenuID', '$NewHeadPos', 'head') ";
            CallSP($strQuery);
        }
    }

    if ($btnSubmit == 'Add to menu')
    {
        $iMenuID = substr(trim($_POST['MenuID']),0,4);
        $strQuery = "SELECT max(iMenuOrder)+1 AS NextID FROM tblmenutype";
        if (!$Result = $dbh->query ($strQuery))
        {
            error_log ('Failed to fetch menu data. Error ('. $dbh->errno . ') ' . $dbh->error);
            error_log ($strQuery);
            print "<p class=\"Attn\" align=center>$ErrMsg</p>\n";
            exit(2);
        }
        $rowcount=mysqli_num_rows($Result);
        if ($rowcount > 0)
        {
            $Row = $Result->fetch_assoc();
            $nextPos = $Row['NextID'];
        }
        $strQuery = "INSERT INTO tblmenutype (iMenuID, vcMenuType, iMenuOrder) VALUES ($iMenuID, 'head', $nextPos);";
        // print $strQuery;
        UpdateSQL ($strQuery,"insert");
    }

    if ($btnSubmit == 'Remove from Menu')
    {
        $iMenuID = substr(trim($_POST['MenuID']),0,4);
        $strQuery = "DELETE FROM tblmenutype WHERE iMenuID=$iMenuID;";
        // print $strQuery;
        UpdateSQL ($strQuery,"delete");
    }

    print "<p class=\"Header2\">Visible in menu</p>\n";
    $strQuery = "SELECT * FROM vwmenupriv where bAdmin=0  AND iMenuOrder IS NOT NULL order by iMenuOrder";
    print "<table class=\"MainText\" cellPadding=\"4\" cellSpacing=\"0\">\n";
    if (!$Result = $dbh->query ($strQuery))
    {
        error_log ('Failed to fetch menu data. Error ('. $dbh->errno . ') ' . $dbh->error);
        error_log ($strQuery);
        print "<p class=\"Attn\" align=center>$ErrMsg</p>\n";
        exit(2);
    }
    if ($WritePriv <=  $Priv)
    {
        print "<tr align=\"left\"><th>Menu Title</th><th>Page Title</th><th>Read Priv</th><th>Write Priv</th><th>Sensitive</th>";
        print "<th>New Window</th><th></th><th width=\"20\">Header Position</th></tr>\n";
    }
    else
    {
        print "<tr align=\"left\"><th>Menu Title</th><th>Page Title</th><th>Read Priv</th><th>Write Priv</th>th>Sensitive</th><th>New Window</th></tr>\n";
    }
    while ($Row = $Result->fetch_assoc())
    {
        print "<tr valign=\"top\">\n";
        print "<td>$Row[vcTitle]</td>";
        print "<td>$Row[vcHeader]</td>";
        print "<td>$Row[ReadPriv]</td>\n";
        print "<td>$Row[WritePriv]</td>\n";
        if ($Row['bSecure'] == 0)
        {
            print "<td align=center><input type=\"checkbox\" disabled></td>\n";
        }
        else
        {
            print "<td align=center><input type=\"checkbox\" checked disabled></td>\n";
        }
        if ($Row['bNewWindow'] == 0)
        {
            print "<td align=center><input type=\"checkbox\" disabled></td>\n";
        }
        else
        {
            print "<td align=center><input type=\"checkbox\" checked disabled></td>\n";
        }
        if ($WritePriv <=  $Priv)
        {
            $HeaderPos = $Row['iMenuOrder'];
            print "<form method=\"POST\">\n";
            print "<td><input type=\"Submit\" value=\"Edit\" name=\"btnSubmit\">";
            print "<input type=\"hidden\" value=\"$Row[iMenuID]\" name=\"MenuID\"></td>\n";
            print "</form>\n";
            if ($HeaderPos > 0)
            {
                print "<td>\n";
                print "<input type=\"text\" value=\"$HeaderPos\" name=\"NewHeadPos\" size=\"3\">\n";
                print "<input type=\"hidden\" value=\"$HeaderPos\" name=\"OldHeadPos\">\n";
                print "</td>\n";
                print "<td>\n";
                print "<input type=\"Submit\" value=\"Update Position\" name=\"btnSubmit\">\n";
                print "<input type=\"hidden\" value=\"$Row[iMenuID]\" name=\"MenuID\">";
                print "</td>\n";
                print "</form>\n";
            }
            print "<form method=\"POST\">\n";
            print "<td><input type=\"Submit\" value=\"Remove from Menu\" name=\"btnSubmit\">";
            print "<input type=\"hidden\" value=\"$Row[iMenuID]\" name=\"MenuID\"></td>\n";
            print "</form>\n";
        }
        print "</tr>\n";
    }
    print "</table>";

    print "<p class=\"Header2\">Administrative Items</p>\n";
    $strQuery = "SELECT * FROM vwmenupriv where bAdmin>0 order by vcTitle";
    print "<table class=\"MainText\" border=\"0\" cellPadding=\"4\" cellSpacing=\"0\">\n";
    if (!$Result = $dbh->query ($strQuery))
    {
        error_log ('Failed to fetch menu data. Error ('. $dbh->errno . ') ' . $dbh->error);
        error_log ($strQuery);
        print "<p class=\"Attn\" align=center>$ErrMsg</p>\n";
        exit(2);
    }
    if ($WritePriv <=  $Priv)
    {
        print "<tr align=\"left\"><th>Menu Title</th><th>Page Title</th><th>Read Priv</th><th>Write Priv</th><th>Sensitive</th>";
        print "<th>New Window</th><th></th></tr>\n";
    }
    else
    {
        print "<tr align=\"left\"><th>Menu Title</th><th>Page Title</th><th>Read Priv</th><th>Write Priv</th>th>Sensitive</th><th>New Window</th></tr>\n";
    }
    while ($Row = $Result->fetch_assoc())
    {
        print "<tr valign=\"top\">\n";
        print "<td>$Row[vcTitle]</td>";
        print "<td>$Row[vcHeader]</td>";
        print "<td>$Row[ReadPriv]</td>\n";
        print "<td>$Row[WritePriv]</td>\n";
        if ($Row['bSecure'] == 0)
        {
            print "<td align=center><input type=\"checkbox\" disabled></td>\n";
        }
        else
        {
            print "<td align=center><input type=\"checkbox\" checked disabled></td>\n";
        }
        if ($Row['bNewWindow'] == 0)
        {
            print "<td align=center><input type=\"checkbox\" disabled></td>\n";
        }
        else
        {
            print "<td align=center><input type=\"checkbox\" checked disabled></td>\n";
        }
        if ($WritePriv <=  $Priv)
        {
            print "<form method=\"POST\">\n";
            print "<td><input type=\"Submit\" value=\"Edit\" name=\"btnSubmit\">";
            print "<input type=\"hidden\" value=\"$Row[iMenuID]\" name=\"MenuID\"></td>\n";
            print "</form>\n";

            print "<form method=\"POST\">\n";
            print "<td><input type=\"Submit\" value=\"Add to menu\" name=\"btnSubmit\">";
            print "<input type=\"hidden\" value=\"$Row[iMenuID]\" name=\"MenuID\"></td>\n";
            print "</form>\n";
        }
        print "</tr>\n";
    }
    print "</table>";

    print "<p class=\"Header2\">Other Items</p>\n";
    $strQuery = "SELECT * FROM vwmenupriv where bAdmin=0 AND iMenuOrder IS NULL order by vcTitle";
    print "<table class=\"MainText\" border=\"0\" cellPadding=\"4\" cellSpacing=\"0\">\n";
    if (!$Result = $dbh->query ($strQuery))
    {
        error_log ('Failed to fetch menu data. Error ('. $dbh->errno . ') ' . $dbh->error);
        error_log ($strQuery);
        print "<p class=\"Attn\" align=center>$ErrMsg</p>\n";
        exit(2);
    }
    if ($WritePriv <=  $Priv)
    {
        print "<tr align=\"left\"><th>Menu Title</th><th>Page Title</th><th>Read Priv</th><th>Write Priv</th><th>Sensitive</th>";
        print "<th>New Window</th><th></th></tr>\n";
    }
    else
    {
        print "<tr align=\"left\"><th>Menu Title</th><th>Page Title</th><th>Read Priv</th><th>Write Priv</th>th>Sensitive</th><th>New Window</th></tr>\n";
    }
    while ($Row = $Result->fetch_assoc())
    {
        print "<tr valign=\"top\">\n";
        print "<td>$Row[vcTitle]</td>";
        print "<td>$Row[vcHeader]</td>";
        print "<td>$Row[ReadPriv]</td>\n";
        print "<td>$Row[WritePriv]</td>\n";
        if ($Row['bSecure'] == 0)
        {
            print "<td align=center><input type=\"checkbox\" disabled></td>\n";
        }
        else
        {
            print "<td align=center><input type=\"checkbox\" checked disabled></td>\n";
        }
        if ($Row['bNewWindow'] == 0)
        {
            print "<td align=center><input type=\"checkbox\" disabled></td>\n";
        }
        else
        {
            print "<td align=center><input type=\"checkbox\" checked disabled></td>\n";
        }
        if ($WritePriv <=  $Priv)
        {
            print "<form method=\"POST\">\n";
            print "<td><input type=\"Submit\" value=\"Edit\" name=\"btnSubmit\">";
            print "<input type=\"hidden\" value=\"$Row[iMenuID]\" name=\"MenuID\"></td>\n";
            print "</form>\n";

            print "<form method=\"POST\">\n";
            print "<td><input type=\"Submit\" value=\"Add to menu\" name=\"btnSubmit\">";
            print "<input type=\"hidden\" value=\"$Row[iMenuID]\" name=\"MenuID\"></td>\n";
            print "</form>\n";
        }

        print "</tr>\n";
    }
    print "</table>";

    require("footer.php");
?>
