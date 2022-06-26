<?php
    $String = "This is a very long string that will be used | as the basis in the password generation routine.";
    $String = $String . "To make it even longer lets add this string as well.";
    $EString = md5($String);
    $StringLen = strlen($EString);
    srand((double) microtime() * 1000000);
    $Begin = rand(0,($StringLen - $PWDLength -1));
    $Password = substr($EString, $Begin, $PWDLength);
    $LastIndex = $HowMany - 1;
    $FName = $strNameParts[0];
    $LName = $strNameParts[$LastIndex];
    $strUID = strtolower(substr($FName,0,1).substr($LName,0,9));
    $strUID = str_replace("'","",$strUID);
    $strUID = str_replace("\\","",$strUID);
    $strUID = str_replace("'","\'",$strUID);
    $Password = str_replace("\\","",$Password);
    $Password = str_replace("'","\'",$Password);
    $strUID = str_replace("\\","",$strUID);
    $Password = str_replace("\\","",$Password);

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
        print "<p class=\"Attn\" align=center>The email address specified is already registered</p>\n";
    }
    else
    {
        $strQuery = "select count(*) iRowCount from tblUsers where vcUID = '$strUID'";
        if (!$Result2 = $dbh->query ($strQuery))
        {
            error_log ('Failed to fetch data. Error ('. $dbh->errno . ') ' . $dbh->error);
            error_log ($strQuery);
            print "<p class=\"Attn\" align=center>$ErrMsg</p>\n";
            exit(2);
        }
        $Row2 = $Result2->fetch_assoc();
        $RowCount = $Row2['iRowCount'];
        $i = 1;
        $strUID2 = $strUID;
        while ($RowCount>0)
        {
            //print "userid: $strUID2 already in use trying";
            $strUID2 = $strUID.$i;
            //print " $strUID2.<br>\n";
            $strQuery = "select count(*) iRowCount from tblUsers where vcUID = '$strUID2'";
            if (!$Result2 = $dbh->query ($strQuery))
            {
                error_log ('Failed to fetch data. Error ('. $dbh->errno . ') ' . $dbh->error);
                error_log ($strQuery);
                print "<p class=\"Attn\" align=center>$ErrMsg</p>\n";
                exit(2);
            }
            $Row2 = $Result2->fetch_assoc();
            $RowCount = $Row2['iRowCount'];
            //print ("$strQuery<br>\niRowCount=$RowCount\n<br>");
            $i += 1;
        }
        $strUID   = $strUID2;
        $strURL   = "http://" . $strHost . $ROOTPATH . "Login.php";
        $StrMsg   = "You have been given an account on $strHost.\n\n";
        $StrMsg2  = "Your username is $strUID and your Password is: $Password\n\n";
        $StrMsg2 .= "Please login into your account at $strURL ";
        $StrMsg2 .= "and confirm your account to activate it.\n";
        $strNotification  = "$strName has registered for a new account on $strHost.\n";
        $strNotification .= "They provided $strCell as their cell.\n";
        $strNotification .= "The email they provided is $strEmail and they listed their address as:\n";
        $strNotification .= " $strAddr1\n $strAddr2\n $strCity, $strState $strZip";
        //print $StrMsg;
        $salt = substr($strUID , 0, 4) ;
        $PWD = crypt($Password , $salt);
        $strQuery = "INSERT INTO tblUsers (vcName, vcEmail, vcAddr1, vcAddr2, vcCity, vcState, vcZip, " .
                                "vcCountry, vcUID, vcPWD, dMailSent, tMailSent, iPrivLevel,vcCell) " .
                                "VALUES ('$strName', '$strEmail', '$strAddr1', '$strAddr2', '$strCity', '$strState', " .
                                "'$strZip','$strCountry', '$strUID', '$PWD', CURDATE(), CURTIME(), '$iLevel', '$strCell')";
        if ($dbh->query ($strQuery))
        {
            $NumAffected = $dbh->affected_rows;
            print "Database insert of $NumAffected record successful<br>\n";
            EmailText($ProfileNotify, "New user registration notification",$strNotification, $FromEmail);
            $toEmail = "\"$strName\" <$strEmail>";
            $StrMsg = str_replace("\\'","'",$StrMsg);
            $StrMsg = str_replace("&quot;",'"',$StrMsg);
            if(EmailText($toEmail,"Your new account at $strHost",$StrMsg . $StrMsg2,$FromEmail))
            {
                print "<p class=\"MainText\">The account was created successful and an confirmation email was sent to $strEmail.<br>\n";
            }
            else
            {
                print "<p class=\"Error\">Signup was successful but Failed to send the confirmation email</p>";
                print "<p class=\"Error\">Please notify us at $SupportEmail, " .
                    "including the email address you used to sign up with.</p>\n";
            }
            $strQuery = "select iUserID from tblUsers where vcUID = '$strUID';";
            if (!$Result2 = $dbh->query ($strQuery))
            {
                error_log ('Failed to fetch data. Error ('. $dbh->errno . ') ' . $dbh->error);
                error_log ($strQuery);
                print "<p class=\"Attn\" align=center>$ErrMsg</p>\n";
                exit(2);
            }
            $Row2 = $Result2->fetch_assoc();
            $strUserID = $Row2['iUserID'];
            $bOK = TRUE;
            if (!$bOK)
            {
                print "<p class=\"Attn\" align=center>$ErrMsg Some interests may not have been saved.</p>\n";
            }
            $bSuccess = TRUE;
        }
        else
        {
            $strError = "Database insert failed. Error (". $dbh->errno . ") " . $dbh->error . "\n";
            $strError .= "$strQuery\n";
            print "\nDatabase insert failed: \n";
            error_log($strError);
            if(EmailText("$SupportEmail","Automatic Error Report",$strError,$FromEmail))
            {
                    print "We seem to be experiencing technical difficulties. We have been notified. " .
                    "Please try again later. Thank you.<br>";
            }
            else
            {
                    print "We seem to be experiencing technical difficulties. " .
                            "Please send us a message at $SupportEmail with information about " .
                            "what you were doing.</p>";
            }
            $bSuccess = FALSE;
        }
    }
?>
