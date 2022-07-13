<?php
    $dtNow = date("Y-m-d H:i:s");
    $uuid = uniqid(mt_rand(), true);
    $strURL = $strURL . "EmailUpdate.php?$uuid";
    // $strUserID = intval(trim($_POST['UserID']));
    if ($strUserID)
    {
        $strQuery = "update tblUsers set vcName = '$strName', vcAddr1 = '$strAddr1', " .
                         "vcAddr2 = '$strAddr2', vcCity = '$strCity', vcState = '$strState', vcZip = '$strZip', " .
                         "vcCountry = '$strCountry', iPrivLevel = $iLevel, dtUpdated = '$dtNow', vcCell='$strCell' "  .
                         " where iUserID='$strUserID'";
        if (UpdateSQL ($strQuery,"update"))
        {
          $dtUpdated = $dtNow;
        }

	if ($strEmail <> $strOEmail)
        {
            $EmailCount = 0;
            $strQuery = "select count(*) iEmailCount from tblUsers where vcEmail = '$strEmail'";
            if (!$Result2 = $dbh->query ($strQuery))
            {
                error_log ('Failed to fetch data. Error ('. $dbh->errno . ') ' . $dbh->error);
                error_log ($strQuery);
                print "<p class=\"Attn\" align=center>$ErrMsg</p>\n";
                exit(2);
            }
            $Row2 = $Result2->fetch_assoc();
            $EmailCount = $Row2['iEmailCount'];
            if ($EmailCount>0)
            {
                print "<p class=\"Attn\" align=center>The new email address specified is registered to a different user</p>\n";
            }
            else
            {
                $toEmail = "\"$strName\" <$strEmail>";
                $toOldEmail = "\"$strName\" <$strOEmail>";
                $strQuery = "INSERT INTO tblemailupdate (iClientID, vcGUID, vcNewEmail, vcReqIPAdd, dtTimeStamp)"
                                . " VALUES ($iUserID, '$uuid', '$strEmail', '$strRemoteIP', '$dtNow');";
                if (UpdateSQL($strQuery, "insert"))
                {
                    $strQuery = "SELECT iChangeID FROM tblemailupdate WHERE vcGUID= '$uuid'";
                    if (!$Result = $dbh->query ($strQuery))
                    {
                        error_log ('Failed to data. Error ('. $dbh->errno . ') ' . $dbh->error);
                        error_log ($strQuery);
                        exit(2);
                    }
                    $Row = $Result->fetch_assoc();
                    $iChangeNum = $Row['iChangeID'];
                    $strEmailText = "Someone (hopefully you) requested that the email address we have on file for $strName " .
                            "be changed to $strEmail. If you didn't make this request please notify us at $SupportEmail referncing changeID $iChangeNum.\n";
                    EmailText($toOldEmail,"Change of email address has been requested",$strEmailText,$FromEmail);
                    $strEmailText = "Before your request to update your email address can be processed you need " .
                            "confirm your request by going to $strURL\nIf you no longer wish to make the change " .
                            "or you didn't request this, just delete this message as no action will be taken without you visiting that page.";
                    EmailText($toEmail,"Change of email address has been requested",$strEmailText,$FromEmail);
                    print "<p>Before we can process your change of email address you need to confirm the new email. " .
                            "Please check your new email and follow the instructions in it.<br>\nIf you don't receive the mail " .
                            "please contact $SupportEmail with changeid $iChangeNum.<br>\nPlease note your IP has been recorded.</p>\n";
                }
            }
        }
    }
    else
    {
        printErr("Can't update without a user ID. Contact $SupportEmail if you have any questions.");
        EmailText("$SupportEmail","Automatic Error Report","Failed to update registration for $strName due to missing userid" ,
                            $FromEmail);
    }
?>
