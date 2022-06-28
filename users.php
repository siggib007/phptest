<?php
    date_default_timezone_set('America/Los_Angeles');
    $PostVarCount = count($_POST);

    if (isset($_POST['btnSubmit']))
    {
        $btnSubmitValue = $_POST['btnSubmit'];
    }
    else
    {
        $btnSubmitValue = "";
    }
    if ($btnSubmitValue == 'Export user to CSV file')
    {
        require_once("DBCon.php");
        $filename = "Example_Users_".date("Y-m-d_Hi",time()).".csv";
        header("Content-type: application/vnd.ms-excel");
        header("Content-Disposition: attachment; filename=$filename");
        print "Name,Unit,Phone,Email,Addr1,Addr2,City,State,Zip,Country,AuthCode,UserType,UnitUse\n";
        $strQuery = "SELECT * FROM tblUsers;";
        if (!$Result = $dbh->query ($strQuery))
        {
            error_log ('Failed to fetch data. Error ('. $dbh->errno . ') ' . $dbh->error);
            error_log ($strQuery);
            print "<p class=\"Attn\" align=center>$ErrMsg</p>\n";
            exit(2);
        }
        while ($Row = $Result->fetch_assoc())
        {
            print "$Row[vcName],$Row[vcUnit],$Row[vcPhone],$Row[vcEmail],$Row[vcAddr1],$Row[vcAddr2],$Row[vcCity],$Row[vcState],$Row[vcZip],$Row[vcCountry]\n";
        }
        exit;
    }


    require("header.php");

    if ($strReferer != $strPageURL and $PostVarCount > 0)
    {
        print "<p class=\"Error\">Invalid operation, Bad Reference!!!</p> ";
        exit;
    }

    print "<p class=\"Header1\">User Maintenance</p>\n";

    if ($btnSubmitValue == 'Go Back')
    {
        // header("Location: $strPageURL");
        $PostVarCount = 0;
    }

    if ($PostVarCount == 0)
    {
        print "<form method=\"POST\">\n";
        print "<input type=\"Submit\" value=\"Add New User\" name=\"btnSubmit\">\n";
        print "</form>\n";
        print "<p class=\"Header3\">Lookup Existing user</p>\n";
        print "<form method=\"POST\">";
        print "<table>\n<tr>\n";
        print "<td class=\"lbl\">Search by name:</td>\n<td><input type=\"text\" name=\"txtName\" size=\"25\">";
        print "</td>\n</tr>";
        print "<tr>\n<td colspan=2 align=\"center\"><input type=\"Submit\" value=\"Search\" name=\"btnSubmit\">";
        print "</td>\n</tr>";
        print "</table>\n</form>";
        print "<form method=\"POST\">\n";
        print "<input type=\"Submit\" value=\"Export user to CSV file\" name=\"btnSubmit\">\n";
        print "</form>\n";
    }

    if ($btnSubmitValue == 'Search')
    {
        $strName = CleanReg(substr(trim($_POST['txtName']),0,49));
        $strQuery = "select iUserID, vcName from tblUsers ";
        $strQuery .= "where vcName like '%$strName%' order by vcName;";
        $crit = "name contains $strName";
        if (!$Result = $dbh->query ($strQuery))
        {
            error_log ('Failed to fetch data. Error ('. $dbh->errno . ') ' . $dbh->error);
            error_log ($strQuery);
            print "<p class=\"Attn\" align=center>$ErrMsg</p>\n";
            exit(2);
        }
        $NumAffected = $Result->num_rows;
        print "<table class=\"MainText\" border=\"0\" cellPadding=\"4\" cellSpacing=\"0\">\n";
        while ($Row = $Result->fetch_assoc())
        {
            print "<tr valign=\"top\">\n<td>$Row[vcName]</td>";
            print "<td><form method=\"POST\">\n<input type=\"Submit\" value=\"View\" name=\"btnSubmit\">";
            print "<input type=\"hidden\" value=\"$Row[iUserID]\" name=\"UserID\"></form>\n</td>\n";
            print "</tr>\n";
        }
        if ($NumAffected == 0)
        {
            print "<tr><td>No registration found where $crit</td></tr>";
        }
        print "</table>\n";
    }


    if ($btnSubmitValue == 'View')
    {
        $strUserID = intval(substr(trim($_POST['UserID']),0,9));
        $strQuery = "select * from tblUsers where iUserID = $strUserID;";
        if (!$Result = $dbh->query ($strQuery))
        {
            error_log ('Failed to data. Error ('. $dbh->errno . ') ' . $dbh->error);
            error_log ($strQuery);
            print "<p class=\"Attn\" align=center>$ErrMsg</p>\n";
            exit(2);
        }
        $Row = $Result->fetch_assoc();
        print "<p class=\"MainText\">\n";
        print "RegistrationID: $strUserID<br>\n";
        print "{$Row['vcName']}<br>\n";
        print "{$Row['vcAddr1']}<br>\n";
        print "{$Row['vcAddr2']}<br>\n";
        print "{$Row['vcCity']}, {$Row['vcState']} {$Row['vcZip']}<br>\n";
        print "{$Row['vcCountry']}<br>\n";
        print "{$Row['vcEmail']}<br>\n";
        print "{$Row['vcPhone']}<br>\n";

        $strQuery = "SELECT vcPrivName FROM tblprivlevels where iPrivLevel = {$Row['iPrivLevel']};";
        if (!$PrivResult = $dbh->query ($strQuery))
        {
            error_log ('Failed to data. Error ('. $dbh->errno . ') ' . $dbh->error);
            error_log ($strQuery);
            print "<p class=\"Attn\" align=center>$ErrMsg</p>\n";
            exit(2);
        }
        $PrivRow = $PrivResult->fetch_assoc();
        $PrivName = $PrivRow['vcPrivName'];
        if ($PrivName == '')
        {
            $PrivName = $Row['iPrivLevel'];
        }

        print "<p>Authorization level is set to $PrivName</p>\n";

        if ($Row['dtLastLogin'])
        {
            $LastLogin = 'on ' . date('l F jS Y \a\t G:i',strtotime($Row['dtLastLogin']));
        }
        else
        {
            $LastLogin = 'never';
        }
        print "<p>Last logged in $LastLogin</p>\n";
        if ($WritePriv <=  $Priv)
        {
            print "<form method=\"POST\">\n<input type=\"Submit\" value=\"Edit\" name=\"btnSubmit\">";
            print "<input type=\"Submit\" value=\"Delete Account\" name=\"btnSubmit\">";
            print "<input type=\"hidden\" name=\"BeenSubmitted\" value=\"false\">\n";
            print "<input type=\"hidden\" value=\"$strUserID\" name=\"UserID\"></form>\n";
        }
    }


    if ($btnSubmitValue == 'Edit')
    {
        $strUserID = intval(substr(trim($_POST['UserID']),0,9));
        require 'UserDBVar.php';
        print "<p>RegistrationID: $strUserID";
        print "<form method=\"POST\">\n";
        require 'UserRegForm.php';
        print "<tr>\n";
        print "<td width=\"280\" align=\"right\" class=\"lbl\">Priviledge Level:</td>\n";
        print "<td>\n";
        $strQuery = "select * from tblprivlevels where iPrivLevel <= $Priv;";
        print "<select size=\"1\" name=\"cmbPrivLevel\">\n";
        if (!$Result2 = $dbh->query ($strQuery))
        {
            error_log ('Failed to fetch data. Error ('. $dbh->errno . ') ' . $dbh->error);
            error_log ($strQuery);
            print "<p class=\"Attn\" align=center>$ErrMsg</p>\n";
            exit(2);
        }
        while ($Row2 = $Result2->fetch_assoc())
        {
            if ($Row2['iPrivLevel'] == $iPrivLevel)
            {
                print "<option selected value=\"{$Row2['iPrivLevel']}\">{$Row2['vcPrivName']}</option>\n";
            }
            else
            {
                print "<option value=\"{$Row2['iPrivLevel']}\">{$Row2['vcPrivName']}</option>\n";
            }
        }
        print "</select>\n";
        print "</td>\n";
        print "</tr>\n";
        print "<tr><td colspan=\"2\" align=\"center\"><input type=\"submit\" value=\"Submit\" name=\"btnSubmit\"></td></tr>";
        print "<tr><td colspan=\"2\" align=\"center\">";
        print "<input type=\"submit\" value=\"Delete Account\" name=\"btnSubmit\">";
        print "<input type=\"hidden\" name=\"BeenSubmitted\" value=\"false\">\n";
        print "<input type=\"hidden\" name=\"UserID\" size=\"5\" value=\"$strUserID\">\n";
        print "</td></tr>";
        print "</table></form>\n";
    }

    if ($btnSubmitValue == 'Add New User')
    {
        $strName = "";
        $strAddr1 = "";
        $strAddr2 = "";
        $strCity = "";
        $strState = "";
        $strZip = "";
        $strCountry = "";
        $strPhone = "";
        $iPrivLevel = "";
        $strEmail = "";
        $strUserID ="";
        $strCell = "";
        $strBdate = "";
        $strWedAnn = "";
        $strHealth = "";
        $strLocate = "";

        print "<form method=\"POST\">\n";
        require 'UserRegForm.php';
        print "<tr>\n";
        print "<td width=\"280\" align=\"right\" class=\"lbl\">Priviledge Level:</td>\n";
        print "<td>\n";
        $strQuery = "select * from tblprivlevels where iPrivLevel <= $Priv;";
        print "<select size=\"1\" name=\"cmbPrivLevel\">\n";
        if (!$Result2 = $dbh->query ($strQuery))
        {
            error_log ('Failed to fetch data. Error ('. $dbh->errno . ') ' . $dbh->error);
            error_log ($strQuery);
            print "<p class=\"Attn\" align=center>$ErrMsg</p>\n";
            exit(2);
        }
        while ($Row2 = $Result2->fetch_assoc())
        {
            print "<option value=\"{$Row2['iPrivLevel']}\">{$Row2['vcPrivName']}</option>\n";
        }
        print "</select>\n";
        print "</td>\n";
        print "</tr>\n";
        print "<tr><td colspan=\"2\" align=\"center\"><input type=\"submit\" value=\"Add User\" name=\"btnSubmit\"></td></tr>";
        print "</table></form>\n";
    }

    print "<form method=\"POST\">\n<input type=\"Submit\" value=\"Go Back\" name=\"btnSubmit\"></form>";

    if ($btnSubmitValue =="Delete Account")
    {
        $iRegNum = intval(trim($_POST['UserID']));
        $BeenSubmitted = trim($_POST['BeenSubmitted']);

        if($iRegNum)
        {
            if($BeenSubmitted == "True")
            {
                $strQuery = "Delete from tblUsers where iUserID='$iRegNum';";
                if ($dbh->query ($strQuery))
                {
                    print "Account Deleted successful<br>\n";
                }
                else
                {
                    $strError = "Database update failed. Error (". $dbh->errno . ") " . $dbh->error . "\n";
                    $strError .= "$strQuery\n";
                    error_log($strError);
                    if(EmailText("$SupportEmail","Automatic Error Report", $strError . "\n\n\n" . $strQuery ,"From:$SupportEmail"))
                    {
                        print "<p class=\"Error\">We seem to be experiencing technical difficulties. " .
                        "We have been notified. Please try again later. If you have any " .
                        "questions you can contact us at $SupportEmail.</p>";
                    }
                    else
                    {
                        print "<p class=\"Error\">We seem to be experiencing technical difficulties. " .
                                "Please send us a message at $SupportEmail with information about " .
                                "what you were doing.</p>";
                    }
                }
            }
            else
            {
                $strQuery = "select * from tblUsers where iUserID = $iRegNum;";
                if (!$Result = $dbh->query ($strQuery))
                {
                    error_log ('Failed to fetch data. Error ('. $dbh->errno . ') ' . $dbh->error);
                    error_log ($strQuery);
                    print "<p class=\"Attn\" align=center>$ErrMsg</p>\n";
                    exit(2);
                }
                $Row = $Result->fetch_assoc();
                $strUserName = $Row['vcName'];
                print "<center>\n<form method=\"post\">\n";
                print "<p class=\"Error\">Are you sure you want to delete the account for $strUserName? <br>\n";
                print "Just leave this page anyway you please if you do not want to delete it. ";
                print "Otherwise press \"Delete Account\" again.</p>\n";
                print "<input type=\"submit\" value=\"Delete Account\" name=\"btnSubmit\"><br>\n";
                print "<input type=\"hidden\" name=\"BeenSubmitted\" value=\"True\">\n";
                print "<input type=\"hidden\" name=\"UserID\" value=\"$iRegNum\">\n";
                print "</form>\n</center>\n";
            }
        }
        else
        {
            print("<p class=\"Error\">Registration number seems to have gotten lost in transport. Please try again" .
                        "<br>Feel free to contact us at $SupportEmail if you have questions.</p>\n");
        }
    }


    if ($btnSubmitValue == 'Add User')
    {
        require_once 'CleanReg.php';
        $iLevel = intval($_POST['cmbPrivLevel']);

        if ($strEmail)
        {
            $strNameParts = explode(' ',$strName);
            $HowMany = count($strNameParts);
            if ($HowMany==1)
            {
                print "Please provide both first and last name";
                print "<form method=\"POST\">\n";
                require_once 'UserRegForm.php';
                print "<tr>\n";
                print "<td width=\"280\" align=\"right\" class=\"lbl\">Priviledge Level:</td>\n";
                print "<td>\n";
                $strQuery = "select * from tblprivlevels where iPrivLevel <= $Priv;";
                print "<select size=\"1\" name=\"cmbPrivLevel\">\n";
                if (!$Result2 = $dbh->query ($strQuery))
                {
                    error_log ('Failed to fetch data. Error ('. $dbh->errno . ') ' . $dbh->error);
                    error_log ($strQuery);
                    print "<p class=\"Attn\" align=center>$ErrMsg</p>\n";
                    exit(2);
                }
                while ($Row2 = $Result2->fetch_assoc())
                {
                    print "<option value=\"{$Row2['iPrivLevel']}\">{$Row2['vcPrivName']}</option>\n";
                }
                print "</select>\n";
                print "</td>\n";
                print "</tr>\n";
                print "<tr><td colspan=\"2\" align=\"center\"><input type=\"submit\" value=\"Add User\" name=\"btnSubmit\"></td></tr>";
                print "</table></form>\n";
            }
            else
            {
                if (!$bSpam)
                {
                    require 'UserAdd.php';
                }
            }
        }
        else
        {
            print "Can't create new user without an email. Contact $SupportEmail if you have any questions.";
        }
        print "<form method=\"POST\">\n<input type=\"Submit\" value=\"Go Back\" name=\"btnSubmit\"></form>";
    }
    if ($btnSubmitValue == 'Submit')
    {
        require_once 'CleanReg.php';
        $iLevel = intval($_POST['cmbPrivLevel']);
        if (!$bSpam)
        {
            require 'UserUpdate.php';
        }
        print "<form method=\"POST\">\n<input type=\"Submit\" value=\"Go Back\" name=\"btnSubmit\"></form>";
    }
    require("footer.php");
?>