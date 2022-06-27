<?php
    require "header.php";
    // print "<script src=\"ckeditor/ckeditor.js\"></script>\n";

    $TextTemplatefile = "TemplatePage.php";
    $TableTemplateFile = "TablePage.php";

    if (isset($_POST['btnSubmit']))
    {
        $btnSubmit = $_POST['btnSubmit'];
    }
    else
    {
        $btnSubmit = "";
    }
    if (isset($_POST['PageID']))
    {
        $iPageID = intval(substr(trim($_POST['PageID']),0,49));
    }
    else
    {
        $iPageID = -10;
    }

    if (isset($_POST['cmbType']))
    {
        $PageType = intval($_POST['cmbType']);
    }
    else
    {
        $strQuery = "SELECT bCont FROM tblmenu WHERE iMenuID = '$iPageID';";
        if (!$Result = $dbh->query ($strQuery))
        {
            error_log ('Failed to fetch data. Error ('. $dbh->errno . ') ' . $dbh->error);
            error_log ($strQuery);
            exit(2);
        }
        $NumAffected = $Result->num_rows;
        if ($NumAffected > 0)
        {
            $Row = $Result->fetch_assoc();
            $PageType=$Row['bCont'];
        }
        else
        {
            $PageType=0;
        }
    }

    if ($strReferer != $strPageURL and $PostVarCount > 0)
    {
        print "<p class=\"Error\">Invalid operation, Bad Reference!!!</p> ";
        exit;
    }

    switch ($PageType)
    {
        case 1:
            $Templatefile = $TextTemplatefile;
            $AdminPage = 0;
            $PrivLevel = 500;
            break;
        case 2:
            $Templatefile = $TableTemplateFile;
            $AdminPage = 1;
            $PrivLevel = 300;
            break;
    }

    print("<p class=\"Header1\">Page Maintenace</p>\n");


    if (($btnSubmit == 'Create New') and ($PageType == 2) or (($btnSubmit == 'Edit') and ($PageType == 2)))
    {
        print "<form method=\"POST\">\n<input type=\"Submit\" value=\"Go Back\" name=\"btnSubmit\"></form>";
        print "<form method=\"POST\">\n";
        if ($iPageID > 0)
        {
            $strQuery = "SELECT * FROM tblPageTable WHERE iMenuID = '$iPageID';";
            if (!$Result = $dbh->query ($strQuery))
            {
                error_log ('Failed to fetch data. Error ('. $dbh->errno . ') ' . $dbh->error);
                error_log ($strQuery);
                exit(2);
            }
            $Row = $Result->fetch_assoc();
            $PageHeader = $Row['vcPageHeader'];
            $strFields  = $Row['vcColumnList'];
            $strTable   = $Row['vcTableName'];
            $strFilter  = $Row['vcFilterStr'];
            $iLimit     = $Row['iLimit'];
            $RecID      = $Row['iTableID'];
        }
        else
        {
            $PageHeader = "";
            $strFields  = "*";
            $strTable   = "";
            $strFilter  = "";
            $iLimit     = "3000";
            $RecID      = "";
            print "<span class=\"lbl\">Page Name:</span>\n";
            print "<input type=\"text\" name=\"txtName\" size=\"25\">\n";
            print "<span class=\"lbl\">Page Title:</span>\n";
            print "<input type=\"text\" name=\"txtTitle\" size=\"25\">\n";
            print "<span class=\"lbl\">File Name:</span>\n";
            print "<input type=\"text\" name=\"txtFile\" size=\"25\">\n<br>\n";
        }
        print "<div class=\"lbl\">Page Main header: </div>\n";
        print "<input type=\"text\" name=\"txtHeader\" size=\"90\" value=\"$PageHeader\"><br>\n";
        print "<div class=\"lbl\">List of columns, comma seperate:</div>\n";
        print "<textarea name=\"txtFields\" rows=\"3\" cols=\"90\">$strFields</textarea><br>\n";
        print "<div class=\"lbl\">Table name:</div>\n";
        print "<input type=\"text\" name=\"txtFrom\" size=\"90\" value=\"$strTable\"><br>\n";
        print "<div class=\"lbl\">[Optional] Filter criteria:</div>\n";
        print "<textarea name=\"txtWhere\" rows=\"3\" cols=\"90\">$strFilter</textarea><br>\n";
        print "<span class=\"lbl\">Limit results to: </span>\n";
        print "<input type=\"text\" name=\"txtLimit\" size=\"5\" value=\"$iLimit\"><br>\n";
        print "<input type=\"hidden\" name=\"PageID\" size=\"5\" value=\"$iPageID\">";
        print "<input type=\"hidden\" name=\"RecID\" size=\"5\" value=\"$RecID\">";
        print "<input type=\"hidden\" name=\"cmbType\" size=\"5\" value=\"2\">";
        print "<input type=\"Submit\" value=\"Save\" name=\"btnSubmit\"><br>\n";
        print "</form>\n";
    }


    if (
           ($btnSubmit == 'Edit' and $PageType == 1)
        or ($btnSubmit == 'Create New' and $PageType == 1)
        or ($btnSubmit == 'Change')
       )
    {
        print "<form method=\"POST\">\n<input type=\"Submit\" value=\"Go Back\" name=\"btnSubmit\"></form>";
        $PageHeader="";
        $PageText = "";

        print "<p class=\"BlueNote\">\n";
        print "It is advised that the Page body is composed off-line and pasted here to " .
              "avoid data loss in case of network error, etc.\n";
        print "</p>\n";
        if ($iPageID > 0)
        {
            if (isset($_POST['cmbRevTime']))
            {
                $RevTime = CleanReg(substr(trim($_POST['cmbRevTime']),0,49));
                $QueryAdd = "'$RevTime'";
            }
            else
            {
                $QueryAdd = "(select max(dtTimeStamp) from tblContent where iMenuID = '$iPageID')";
                $RevTime = "";
            }
            $strQuery   = "SELECT dtTimeStamp FROM tblContent WHERE iMenuID = '$iPageID' order by dtTimeStamp desc";
            if (!$Result = $dbh->query ($strQuery))
            {
                error_log ('Failed to fetch data. Error ('. $dbh->errno . ') ' . $dbh->error);
                error_log ($strQuery);
                exit(2);
            }
            print "<table class=\"MainText\" border=\"0\" cellPadding=\"4\" cellSpacing=\"0\">\n";
            print "<tr valign=\"top\">\n<td>\n";
            print "<form method=\"POST\">\n";
            print "<input type=\"hidden\" name=\"PageID\" size=\"5\" value=\"$iPageID\">";
            print "<span class=\"lbl\">Change to revision from:</span>\n";
            print "\n<select size=\"1\" name=\"cmbRevTime\">\n";
            while ($Row = $Result->fetch_assoc())
            {
                $TimeStamp = date('F jS Y \a\t G:i',strtotime($Row['dtTimeStamp']));
                if ($Row['dtTimeStamp'] == $RevTime)
                {
                    print "<option selected value=\"{$Row['dtTimeStamp']}\">$TimeStamp</option>\n";
                }
                else
                {
                    print "<option value=\"{$Row['dtTimeStamp']}\">$TimeStamp</option>\n";
                }
            }
            print "</select>\n";
            print "<input type=\"Submit\" value=\"Change\" name=\"btnSubmit\" >";
            print "</form>\n";
            print "</td>\n</tr>\n";
            print "</table>\n";
            $strQuery = "SELECT * FROM tblContent WHERE iMenuID = '$iPageID' and dtTimeStamp = $QueryAdd;";
            if (!$Result = $dbh->query ($strQuery))
            {
                    error_log ('Failed to fetch data. Error ('. $dbh->errno . ') ' . $dbh->error);
                    error_log ($strQuery);
                    exit(2);
            }
            $Row = $Result->fetch_assoc();
            $PageHeader=$Row['vcPageHeader'];
            $PageText = $Row['tPageText'];
            $bCRLF = $Row['bLineBreak'];

            $strQuery = "SELECT iSubOfMenu FROM tblmenutype WHERE iMenuID = '$iPageID';";
            if (!$Result = $dbh->query ($strQuery))
            {
                    error_log ('Failed to fetch data. Error ('. $dbh->errno . ') ' . $dbh->error);
                    error_log ($strQuery);
                    exit(2);
            }
            $Row = $Result->fetch_assoc();
            $iSubPageID=$Row['iSubOfMenu'];

            print "<form method=\"POST\">\n";
            print "<input type=\"hidden\" name=\"PageID\" size=\"5\" value=\"$iPageID\">";
        }
        else
        {
            print "<form method=\"POST\">\n";
            print "<input type=\"hidden\" name=\"cmbType\" size=\"5\" value=\"$PageType\">";
            print "<span class=\"lbl\">Page Name:</span>\n";
            print "<input type=\"text\" name=\"txtName\" size=\"25\">\n";
            print "<span class=\"lbl\">Page Title:</span>\n";
            print "<input type=\"text\" name=\"txtTitle\" size=\"25\">\n";
            print "<span class=\"lbl\">File Name:</span>\n";
            print "<input type=\"text\" name=\"txtFile\" size=\"25\">\n<br>\n";
        }
        print "<span class=\"lbl\">Page Header:</span>\n";
        print "<input type=\"text\" name=\"txtHeader\" size=\"50\" value=\"$PageHeader\">\n";
        print "<span class=\"lbl\">Sub Page of:</span>\n";
        $strQuery   = "SELECT iMenuID, vcTitle FROM tblmenu WHERE bCont = '1' AND bdel = '1' AND iMenuID <> $iPageID;";
        if (!$Result = $dbh->query ($strQuery))
        {
            error_log ('Failed to fetch data. Error ('. $dbh->errno . ') ' . $dbh->error);
            error_log ($strQuery);
            exit(2);
        }
        print "<select size=\"1\" name=\"cmbSubPage\">\n";
        print "<option value=\"0\">Not a sub page</option>\n";
        while ($Row = $Result->fetch_assoc())
        {
            if ($Row['iMenuID'] == $iSubPageID)
            {
                print "<option selected value=\"{$Row['iMenuID']}\">{$Row['vcTitle']}</option>\n";
            }
            else
            {
                print "<option value=\"{$Row['iMenuID']}\">{$Row['vcTitle']}</option>\n";
            }
        }
        print "</select>\n";
        print "<span class=\"lbl\">Maintain Linebreaks:</span>\n";
        if ($bCRLF==0)
        {
            print "<input type=\"checkbox\" name=\"chkCR\">\n";
        }
        else
        {
            print "<input type=\"checkbox\" name=\"chkCR\" checked>\n";
        }
        print "<div class=\"lbl\">Page Body:</div>\n";
        print "<textarea name=\"txtBody\" rows=\"20\" cols=\"120\">$PageText </textarea>\n<br>\n";
        print "<script>CKEDITOR.replace( 'txtBody' );</script>\n";
        print "<input type=\"Submit\" value=\"Save\" name=\"btnSubmit\">";
        print "</form>\n";
        print "<form method=\"POST\">\n<input type=\"Submit\" value=\"Go Back\" name=\"btnSubmit\"></form>";
    }

    if ($btnSubmit == 'Save')
    {
        $iSubPage = intval(substr(trim($_POST['cmbSubPage']),0,49));
        if (isset($_POST['txtFile']))
        {
            $FileName = CleanReg(substr(trim($_POST['txtFile']),0,49));
            $PageName = CleanReg(substr(trim($_POST['txtName']),0,49));
            $MenuTitle = CleanReg(substr(trim($_POST['txtTitle']),0,49));

            if (substr($FileName,-4)!=".php")
            {
                $FileName .= ".php";
                print "<p class=\"Error\">\n";
                print "The filename you specified was missing the php extension so I added it. \n";
                print "New filename: $FileName. \n";
                print "</p>\n";
            }
            if (file_exists($FileName))
            {
                print "<p class=\"Error\">\n";
                print "The filename you specified already exists, please choose another\n";
                print "</p>\n";
            }
            else
            {
                if (copy($Templatefile, $FileName))
                {
                    $strQuery = "INSERT INTO tblmenu (vcTitle, vcLink, iReadPriv, iWritePriv, vcHeader, bAdmin, bCont, bdel)"
                             . "VALUES ('$MenuTitle', '$FileName', '$PrivLevel', '$PrivLevel', '$PageName', '$AdminPage', '$PageType', '1');";
                    if (CallSP($strQuery))
                    {
                        $strQuery = "SELECT iMenuID FROM tblmenu WHERE vcLink = '$FileName' LIMIT 1;";
                        if (!$Result = $dbh->query ($strQuery))
                        {
                            error_log ('Failed to fetch data. Error ('. $dbh->errno . ') ' . $dbh->error);
                            error_log ($strQuery);
                            exit(2);
                        }
                        $Row = $Result->fetch_assoc();
                        $iPageID = $Row['iMenuID'];
                        if ($AdminPage == 0)
                        {
                            $strQuery = "SELECT MAX(iMenuOrder) as MaxHead FROM tblmenutype WHERE vcMenuType = 'head';";
                            if (!$Result = $dbh->query ($strQuery))
                            {
                                error_log ('Failed to fetch data. Error ('. $dbh->errno . ') ' . $dbh->error);
                                error_log ($strQuery);
                                exit(2);
                            }
                            $Row = $Result->fetch_assoc();
                            $iMaxHead = $Row['MaxHead'] + 1;

                            if ($iPageID > 0)
                            {
                                $strQuery = "INSERT INTO tblmenutype (iMenuID, vcMenuType, iMenuOrder, iSubOfMenu)" .
                                            " VALUES ('$iPageID', 'head', '$iMaxHead', '$iSubPage');";
                                CallSP($strQuery);
                            }
                        }
                    }
                }
                else
                {
                    print "<p class=\"Error\">\n";
                    print "Failed to create the file $FileName from template file $Templatefile\n";
                    print "</p>\n";
                }
            }
        }
        if ($iPageID > 0)
        {
            if (isset($_POST['txtHeader']))
            {
                $strHeader = CleanSQLInput(substr(trim($_POST['txtHeader']),0,79));
            }
            else
            {
                $strHeader = "";
            }
            if (isset($_POST['chkCR']))
            {
                $bCRLF = 1;
            }
            else
            {
                $bCRLF = 0;
            }

            switch ($PageType)
            {
                case 1:
                    $strQuery="UPDATE tblmenutype SET iSubOfMenu = '$iSubPage' WHERE iMenuID = '$iPageID';";
                    UpdateSQL($strQuery, "update");
                    if (isset($_POST['txtBody']))
                    {
                        $strBody = CleanSQLInput(trim($_POST['txtBody']));
                    }
                    else
                    {
                        $strBody = "";
                    }
                    $strQuery = "insert into tblContent (iMenuID, vcPageHeader, tPageText, bLineBreak) " .
                                "values ('$iPageID','$strHeader','$strBody','$bCRLF');";
                    break;
                case 2:
                    if (isset($_POST['txtFields']))
                    {
                        $strFields = CleanSQLInput(substr(trim($_POST['txtFields']),0,799));
                    }
                    else
                    {
                        $strFields = "";
                    }
                    if (isset($_POST['txtFrom']))
                    {
                        $strTableName = CleanSQLInput(substr(trim($_POST['txtFrom']),0,34));
                    }
                    else
                    {
                        $strTableName = "";
                    }
                    if (isset($_POST['txtWhere']))
                    {
                        $strCrit = CleanSQLInput(substr(trim($_POST['txtWhere']),0,34));
                    }
                    else
                    {
                        $strCrit = "";
                    }
                    if (isset($_POST['RecID']))
                    {
                        $iRecID = CleanSQLInput(substr(trim($_POST['RecID']),0,5));
                    }
                    else
                    {
                        $iRecID = "";
                    }
                    if (isset($_POST['txtLimit']))
                    {
                        $iLimit = CleanSQLInput(substr(trim($_POST['txtLimit']),0,5));
                    }
                    else
                    {
                        $iLimit = "";
                    }
                    if (!is_numeric($iLimit) or $iLimit > 5000)
                    {
                        $iLimit = 5000;
                    }
                    if ($iRecID == "")
                    {
                        $strQuery = "insert into tblPageTable (iMenuID, vcPageHeader, vcColumnList, vcTableName, vcFilterStr, iLimit) " .
                                        "values ('$iPageID','$strHeader','$strFields', '$strTableName', '$strCrit', '$iLimit');";
                    }
                    else
                    {
                        $strQuery = "UPDATE tblPageTable SET iMenuID = '$iPageID', vcPageHeader = '$strHeader', " .
                                        "vcColumnList = '$strFields', vcTableName = '$strTableName', " .
                                        "vcFilterStr = '$strCrit', iLimit = '$iLimit' WHERE iTableID = '$iRecID'";
                    }
                    break;
            }
            CallSP($strQuery);
        }
        else
        {
            print "<p class=\"Error\">\n";
            print "Unable to save due to missing PageID\n";
            print "</p>\n";
        }
        print "<form method=\"POST\">\n<input type=\"Submit\" value=\"Go Back\" name=\"btnSubmit\"></form>";
    }

    if ($btnSubmit == 'Delete')
    {
        $strQuery = "SELECT * FROM tblmenu WHERE iMenuID = '$iPageID';";
        if (!$Result = $dbh->query ($strQuery))
        {
            error_log ('Failed to fetch data. Error ('. $dbh->errno . ') ' . $dbh->error);
            error_log ($strQuery);
            exit(2);
        }

        $Row = $Result->fetch_assoc();
        $FileName = $Row['vcLink'];
        $PageTitle = $Row['vcTitle'];
        $PageHeader = $Row['vcHeader'];
        print "<p class=\"BlueNote\">Are you sure you want to delete $PageTitle $PageHeader "
            . "and the associated file $FileName. This action is irreversible.</p>\n";
        print "<center>\n<form method=\"POST\">\n";
        print "<input type=\"Submit\" value=\"Yes I am very sure\" name=\"btnSubmit\">";
        print "<input type=\"hidden\" value=\"$iPageID\" name=\"PageID\">\n";
        print "<input type=\"hidden\" value=\"$FileName\" name=\"FileName\">\n";
        print "</form>\n";

        print "<form method=\"POST\">\n<input type=\"Submit\" value=\"Go Back\" name=\"btnSubmit\">\n"
                        . "</form>\n</center>\n";
    }

    if ($btnSubmit == 'Yes I am very sure')
    {
        $FileName = CleanReg(substr(trim($_POST['FileName']),0,49));

        if (unlink($FileName))
        {
            print "Successfully deleted $FileName<br>\n";
            $strQuery = "DELETE FROM tblmenu WHERE iMenuID = '$iPageID';";
            UpdateSQL ($strQuery,"Delete");
            $strQuery = "DELETE FROM tblmenutype WHERE iMenuID = '$iPageID';";
            UpdateSQL ($strQuery,"Delete");
            $strQuery = "DELETE FROM tblContent WHERE iMenuID = '$iPageID';";
            UpdateSQL ($strQuery,"Delete");        }
        else
        {
            print "<p class=\"Error\">\n";
            print "Failed to deleted $FileName\n";
            print "</p>\n";
        }
        print "<form method=\"POST\">\n<input type=\"Submit\" value=\"Go Back\" name=\"btnSubmit\"></form>";
    }

    if (($PostVarCount == 0) or ($btnSubmit == 'Go Back'))
    {
            print "<center><form method=\"POST\">\n";
            print "<input type=\"Submit\" value=\"Create New\" name=\"btnSubmit\">\n";
            print "<select size=\"1\" name=\"cmbType\">\n";
            $strQuery = "select * from tblPageTypes;";
            if (!$Result2 = $dbh->query ($strQuery))
            {
                error_log ('Failed to fetch data. Error ('. $dbh->errno . ') ' . $dbh->error);
                error_log ($strQuery);
                exit(2);
            }
            while ($Row2 = $Result2->fetch_assoc())
            {
                if ($Row2['iTypeID'] == $PageType)
                {
                    print "<option selected value=\"{$Row2['iTypeID']}\">{$Row2['vcPageType']}</option>\n";
                }
                else
                {
                    print "<option value=\"{$Row2['iTypeID']}\">{$Row2['vcPageType']}</option>\n";
                }
            }
            print "</select>\n";
            print "</form>\n";
            print "<table class=\"MainText\" border=\"0\" cellPadding=\"4\" cellSpacing=\"0\">\n";
            print "<tr align=\"center\"><th>Menu Title</th><th>Page Title</th></tr>\n";
            $strQuery = "SELECT * FROM tblmenu where bCont>0 order by vcTitle";
            if (!$Result = $dbh->query ($strQuery))
            {
                error_log ('Failed to fetch data. Error ('. $dbh->errno . ') ' . $dbh->error);
                error_log ($strQuery);
                exit(2);
            }

            $NumAffected = $Result->num_rows;

            while ($Row = $Result->fetch_assoc())
            {
                print "<tr valign=\"top\">\n";
                print "<td>$Row[vcTitle]</td>";
                print "<td>$Row[vcHeader]</td>";
                print "<form method=\"POST\">\n";
                print "<td>\n<input type=\"Submit\" value=\"Edit\" name=\"btnSubmit\">\n";
                print "<input type=\"hidden\" value=\"$Row[iMenuID]\" name=\"PageID\"></td>\n";
                print "</form>\n";
                if ($Row['bdel']==1)
                {
                    print "<form method=\"POST\">\n";
                    print "<td><input type=\"Submit\" value=\"Delete\" name=\"btnSubmit\">";
                    print "<input type=\"hidden\" value=\"$Row[iMenuID]\" name=\"PageID\"></td>\n";
                    print "</form>\n";
                }
                print "</tr>\n";
            }
            print "</table></center>";
    }
    require("footer.php");
?>
