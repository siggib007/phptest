<?php
  $PostVarCount = count($_POST);
  if ($PostVarCount == 0 )
  {
    header("Location: index.php" );
  }

  require("header.php");

  $strEmail = CleanReg(trim($_POST['txtRecEmail']));

  $RecoverAck = $TextArray["RecoverAck"];

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
      print $RecoverAck;
    }
    else
    {
      $iUserID = $Row['iUserID'];
      $strUID =  $Row['vcUID'];
      $strName =  $Row['vcName'];
      if ($PWDLength%2>0)
      {
        $PWDLength = $PWDLength + 1;
      }

      $Password = bin2hex(random_bytes($PWDLength/2));
      $PWD = password_hash($Password, PASSWORD_DEFAULT);
      $strQuery = "update tblUsers set vcPWD = '$PWD', bChangePWD=1  where iUserID='$iUserID'";
      $bUpdate = UpdateSQL ($strQuery,"update");
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
          print "<p class=\"MainText\">$RecoverAck</p>\n";
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
