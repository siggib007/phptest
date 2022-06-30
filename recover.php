<?php
    $PostVarCount = count($_POST);
    if ($PostVarCount == 0 )
    {
        header("Location: index.php" );
    }
    require("header.php");
    $strEmail = CleanSQLInput(trim($_POST['txtRecEmail']));
    $strEmail = str_replace("'","",$strEmail);
    print "Recovering the password for $strEmail<br>\n";
    if ($strEmail)
    {
        $strQuery = "select * from tblUsers where vcEmail = '$strEmail'";
        if (!$Result = $dbh->query ($strQuery))
        {
            error_log ('Failed to fetch data. Error ('. $dbh->errno . ') ' . $dbh->error);
            error_log ($strQuery);
            exit(2);
        }
        $Row = $Result->fetch_assoc();
        if ($Row['vcEmail']!=$strEmail)
        {
            print "Sorry can't find that email address. Please contact us at $SupportEmail for further help";
        }
        else
        {
//          print "creating new password<br>\n";
            $iUserID = $Row['iUserID'];
            $strUID =  $Row['vcUID'];
            $strName =  $Row['vcName'];
            $String = "This is a very long string that will be used | as the basis in the password generation routine.";
            $EString = md5($String);
            $StringLen = strlen($EString);
            srand((double) microtime() * 1000000);
            $Begin = rand(0,($StringLen - $PWDLength -1));
            $Password = substr($EString, $Begin, $PWDLength);
            $salt = substr($strUID , 0, 4) ;
            $PWD = crypt($Password , $salt);
            $strQuery = "update tblUsers set vcPWD = '$PWD' where iUserID='$iUserID'";
            $bUpdate = UpdateSQL ($strQuery,"update");;
            if ($bUpdate)
            {
                $StrMsg = "Per your request login for our site is {$Row['vcUID']} and the new password is $Password";
//				print "Email body: $StrMsg<br>\n";
                if ($OSEnv == "win")
                {
                    $toEmail = "$strEmail";
                    $fromEmail = "From:$eFromAddr";
                }
                else
                {
                    $toEmail = "\"$strName\" <$strEmail>";
                    $fromEmail = "From:$eFromName <$eFromAddr>";
                }

                if(EmailText($toEmail,"Your Password request",$StrMsg,$fromEmail))
                {
                    print "<p class=\"MainText\">We have reset your password and emailed the new one to you.</p>\n";
                }
            }
            else
            {
                print "<p class=\"Error\">There was an unknown error when attempting to email your password. " .
                                    "Please let us know at $SupportEmail</p>\n";
            }
        }
    }
    else
    {
        print "email is required to look up your password.";
    }

    require("footer.php");
?>
