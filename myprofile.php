<?php
  //Copyright © 2009,2015,2022  Siggi Bjarnason.
  //
  //This program is free software: you can redistribute it and/or modify
  //it under the terms of the GNU General Public License as published by
  //the Free Software Foundation, either version 3 of the License, or
  //(at your option) any later version.
  //
  //This program is distributed in the hope that it will be useful,
  //but WITHOUT ANY WARRANTY; without even the implied warranty of
  //MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
  //GNU General Public License for more details.
  //
  //You should have received a copy of the GNU General Public License
  //along with this program.  If not, see <http://www.gnu.org/licenses/>
  require_once("header.php");

  $strUserID = $_SESSION["UID"];
  require("UserDBVar.php");

  if ($strReferer != $strPageURL and $PostVarCount > 0)
  {
    print "<p class=\"Error\">Invalid operation, very Bad Reference!!!</p> ";
    exit;
  }

  print "<p class=\"Header1\">My Profile</p>\n";
  if (isset($_POST['btnSubmit']))
  {
    $btnSubmit = $_POST['btnSubmit'];
  }
  else
  {
    $btnSubmit = "";
  }

  if ($btnSubmit =="Reset Recovery Code")
  {
    GenerateRecovery($iUserID);
  }

  if ($btnSubmit =="Delete Account")
  {
    $iRegNum = trim($_POST['iUserID']);
    $BeenSubmitted = trim($_POST['BeenSubmitted']);

    if($iRegNum)
    {
      if($BeenSubmitted == "True")
      {
        if(UpdateSQL($strQuery, "delete"))
        {
          $strQuery = "Delete from tblUsers where iUserID='$iRegNum';";
          if ($dbh->query ($strQuery))
          {
            print "Account Deleted successful, please close your browser.<br>\n";
            require_once("KillSession.php");
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
      }
      else
      {
        print "<center>\n<form method=\"post\">\n";
        print "<p class=\"Error\">Are you sure you want to delete your account? <br>\n";
        print "Just leave this page anyway you please if you do not want to delete it. ";
        print "Otherwise press \"Delete Account\" again.</p>\n";
        print "<input type=\"submit\" value=\"Delete Account\" name=\"btnSubmit\"><br>\n";
        print "<input type=\"hidden\" name=\"BeenSubmitted\" value=\"True\">\n";
        print "<input type=\"hidden\" name=\"iUserID\" value=\"$iRegNum\">\n";
        print "</form>\n</center>\n";
      }
    }
    else
    {
      print "<p class=\"Error\">Registration number seems to have gotten lost in transport. Please try again" .
            "<br>Feel free to contact us at $SupportEmail if you have questions.</p>\n";
    }
  }

  if ($btnSubmit =="Save")
  {
    if (isset($_POST['txtKey']))
    {
      $strKey =  CleanReg($_POST['txtKey']);
    }
    else
    {
      $strKey = "";
    }
    if (isset($_POST['txtLabel']))
    {
      $strLabel = CleanReg($_POST['txtLabel']);
    }
    else
    {
      $strLabel = "";
    }
    if (isset($_POST['txtValue']))
    {
      $strValue = CleanReg($_POST['txtValue']);
    }
    else
    {
      $strValue = "";
    }
    $strQuery = "update tblUsrPrefValues set vcValue = '$strValue' where iUserID = $iUserID and iTypeID = $strKey ;";
    if(UpdateSQL ($strQuery, "update"))
    {
      print "<p class=\"BlueAttn\">Update for $strLabel successful</p>";
    }
    else
    {
      print "<p class=\"BlueAttn\">failed to update $strLabel</p>";
    }
    $btnSubmit = "";
  }

  if ($btnSubmit =="Validate")
  {
    if (isset($_POST['txtCode']))
    {
      $strCode =  CleanReg($_POST['txtCode']);
    }
    else
    {
      $strCode = "";
    }
    if (isset($_SESSION["ConfCode"]))
    {
      $ConfCode = $_SESSION["ConfCode"];
    }
    else
    {
      $ConfCode = "";
    }

    if ($strCode == $ConfCode)
    {
      $strQuery = "update tblUsrPrefValues set vcValue = 'True' where iUserID = $iUserID and iTypeID = 1 ;";
      if(UpdateSQL ($strQuery, "update"))
      {
        print "<p class=\"BlueAttn\">Update for Enable Sending SMS Messages successful</p>";
      }
      else
      {
        print "<p class=\"BlueAttn\">failed to update Enable Sending SMS Messages</p>";
      }
    }
    else
    {
      print "<p class=\"Error\">Invalid code, please try again</p>\n";
    }
    unset($_SESSION["ConfCode"]);
    $btnSubmit = "";
  }

  if ($btnSubmit =="Disabled")
  {
    if (isset($_POST['txtKey']))
    {
      $strKey =  CleanReg($_POST['txtKey']);
    }
    else
    {
      $strKey = "";
    }
    if (isset($_POST['txtLabel']))
    {
      $strLabel = CleanReg($_POST['txtLabel']);
    }
    else
    {
      $strLabel = "";
    }
    $bSMSOK = True;

    if ($strKey == 1 and $strCell != "")
    {
      $ConfCode = bin2hex(random_bytes(4));
      $response = SendTwilioSMS("Your confirmation code is: $ConfCode",$strCell);
      if ($response[0])
      {
        $_SESSION["ConfCode"]=$ConfCode;
        print "<form method=\"POST\">\n";
        print "<p>Please enter the confirmation code we just sent you:\n";
        print "<input type=\"text\" name=\"txtCode\" size=\"10\">\n";
        print "<input type=\"Submit\" value=\"Validate\" name=\"btnSubmit\">\n";
        print "<input type=\"Submit\" value=\"Cancel\" name=\"btnSubmit\">\n";
        print "</p>\n";
        print "</form>\n";
        $bSMSOK = False;
      }
      else
      {
        print "<p class=\"Error\">A failure occured:</p>\n";
        $arrResponse = json_decode($response[1], TRUE);
        $errmsg = "";
        if (array_key_exists("message",$arrResponse))
        {
          $errmsg .= $arrResponse["message"];
        }
        print "<p class=\"Error\">$errmsg</p><br>\n";
        $bSMSOK = False;
        $btnSubmit = "";
      }
    }

    if ($strKey == 1 and $strCell == "")
    {
      print "<p class=\"Error\">You have to provide your cell number before you can enable SMS Notifications</p>\n";
      $btnSubmit = "";
      $bSMSOK = False;
    }

    if (stripos($strLabel,"sms")!== false and $strKey != 1)
    {
      $strQuery = "SELECT vcValue FROM tblUsrPrefValues WHERE iUserID = $iUserID AND iTypeID = 1;";
      if (!$Result = $dbh->query ($strQuery))
      {
        error_log ('Failed to fetch user data from tblUsrPrefValues. Error ('. $dbh->errno . ') ' . $dbh->error);
        error_log ($strQuery);
        exit(2);
      }
      $rowcount=mysqli_num_rows($Result);
      if ($rowcount > 0)
      {
        $Row = $Result->fetch_assoc();
        $strValue = $Row['vcValue'];
        if (strtolower($strValue) == "true")
        {
          $bSMSOK = True;
        }
        else
        {
          $bSMSOK = False;
        }
      }
      else
      {
        $bSMSOK = False;
      }
    }

    if ($bSMSOK)
    {
      $strQuery = "update tblUsrPrefValues set vcValue = 'True' where iUserID = $iUserID and iTypeID = $strKey ;";
      if(UpdateSQL ($strQuery, "update"))
      {
        print "<p class=\"BlueAttn\">Update for $strLabel successful</p>";
      }
      else
      {
        print "<p class=\"BlueAttn\">failed to update $strLabel</p>";
      }
      $btnSubmit = "";
    }
    else
    {
      if ($strKey != 1)
      {
        print "<p class=\"Error\">Please enable sending SMS Messages before enabling any SMS functions</p>\n";
        $btnSubmit = "";
      }
    }
  }

  if ($btnSubmit =="Enabled")
  {
    if (isset($_POST['txtKey']))
    {
      $strKey =  CleanReg($_POST['txtKey']);
    }
    else
    {
      $strKey = "";
    }
    if (isset($_POST['txtLabel']))
    {
      $strLabel = CleanReg($_POST['txtLabel']);
    }
    else
    {
      $strLabel = "";
    }

    $arrTypeIDs = array();
    if ($strKey == 1)
    {
      $strQuery = "SELECT iID FROM tblUsrPrefTypes WHERE vcCode LIKE '%sms%';";
      if (!$Result = $dbh->query ($strQuery))
      {
        error_log ('Failed to fetch user data from tblUsrPrefValues. Error ('. $dbh->errno . ') ' . $dbh->error);
        error_log ($strQuery);
        exit(2);
      }
      while ($Row = $Result->fetch_assoc())
      {
        $arrTypeIDs[] = $Row['iID'];
      }
    }
    else
    {
      $arrTypeIDs[] = $strKey;
    }
    $strTypeList = implode(",",$arrTypeIDs);
    $strQuery = "update tblUsrPrefValues set vcValue = 'False' where iUserID = $iUserID and iTypeID IN ($strTypeList) ;";
    if(UpdateSQL ($strQuery, "update"))
    {
      print "<p class=\"BlueAttn\">Update for $strLabel successful</p>";
    }
    else
    {
      print "<p class=\"BlueAttn\">failed to update $strLabel</p>";
    }
    $btnSubmit = "";
  }


  if ($btnSubmit =="Submit")
  {
    //Saving updates to my profile to database
    require_once("CleanReg.php");
    $iLevel = $Priv;
    if (!$bSpam)
    {
      require("UserUpdate.php");
      if ($iUserID)
      {
        if ($strCell == "")
        {
          $arrTypeIDs = array();
          $strQuery = "SELECT iID FROM tblUsrPrefTypes WHERE vcCode LIKE '%sms%';";
          if (!$Result = $dbh->query ($strQuery))
          {
            error_log ('Failed to fetch user data from tblUsrPrefValues. Error ('. $dbh->errno . ') ' . $dbh->error);
            error_log ($strQuery);
            exit(2);
          }
          while ($Row = $Result->fetch_assoc())
          {
            $arrTypeIDs[] = $Row['iID'];
          }
          $strTypeList = implode(",",$arrTypeIDs);
          $strQuery = "update tblUsrPrefValues set vcValue = 'False' where iUserID = $iUserID and iTypeID IN ($strTypeList) ;";
          if(UpdateSQL ($strQuery, "update"))
          {
            print "<p class=\"BlueAttn\">Disabling all SMS functions successful</p>";
          }
          else
          {
            print "<p class=\"BlueAttn\">Disabling all SMS functions failed</p>";
          }
        }
        $strUID = substr(trim($_POST['txtUID']),0,19);
        $strOUID = substr(trim($_POST['txtOUID']),0,19);
        $Password = trim($_POST['txtPWD']);
        $PWDConf = trim($_POST['txtPWDConf']);
        $strUID = str_replace("'","",$strUID);
        CleanReg($strUID);
        CleanReg($strOUID);
        CleanReg($Password);
        Cleanreg($PWDConf);
        if ($Password !='' or $strOUID != $strUID)
        {
          $strQuery = "select count(*) iRowCount from tblUsers where vcUID = '$strUID' and iUserID <> $iUserID";
          if (!$Result = $dbh->query ($strQuery))
          {
            error_log ('Failed to fetch data. Error ('. $dbh->errno . ') ' . $dbh->error);
            error_log ($strQuery);
            exit(2);
          }
          $Row = $Result->fetch_assoc();
          $RowCount = $Row['iRowCount'];
          $i = 1;
          $strUID2 = $strUID;
          while ($RowCount>0)
          {
            $strUID2 = $strUID.$i;
            $strQuery = "select count(*) iRowCount from tblUsers where vcUID = '$strUID2' and iUserID <> $iUserID";
            if (!$Result2 = $dbh->query ($strQuery))
            {
              error_log ('Failed to data. Error ('. $dbh->errno . ') ' . $dbh->error);
              error_log ($strQuery);
              exit(2);
            }
            $Row2 = $Result2->fetch_assoc();
            $RowCount = $Row2['iRowCount'];
            $i += 1;
          }

          if (strlen($Password) < $MinPWDLen)
          {
            print "<p>Passwords is too short, please supply a password that is at least $MinPWDLen char long.</p>\n";
          }
          else
          {
            $PWD = password_hash($Password, PASSWORD_DEFAULT);
            $strQuery="";
            if ($Password == $PWDConf and $strUID == $strUID2 and $Password != '')
            {
              $strQuery = "UPDATE tblUsers SET vcUID = '$strUID', vcPWD = '$PWD', bChangePWD = 0 WHERE iUserID = '$iUserID'";
            }
            if ($Password =='' and $strOUID != $strUID and $strUID == $strUID2)
            {
              print "<p>Please provide password to change your user name.</p>";
            }
            if ($Password != $PWDConf and $strUID == $strUID2)
            {
              print "<p>Passwords do not match so password and username was not changed.</p>\n";
            }
            if ($Password == $PWDConf and $strUID != $strUID2)
            {
              print "<p>Requested username is already in use could not be changed, however the password will been changed. " .
                    "To change the username use a different username that is not in use. For example $strUID2 is available.</p>\n";
              $strQuery = "UPDATE tblUsers SET vcPWD = '$PWD', bChangePWD = 0 WHERE iUserID = '$iUserID'";
            }
            if ($Password != $PWDConf and $strUID != $strUID2)
            {
              print "<p>Passwords do not match so password was not changed. Requested username is already in use could not be changed. " .
                    "To change the username Try again and use a different username that is not in use. " .
                    "For example $strUID2 is available. When changing the password make sure you type the same one twice.</p>\n";
            }
            if ($strQuery)
            {
              if(UpdateSQL($strQuery, "update"))
              {
                print "<p>Update successful!!</p>";
                $bChangePWD = 0;
              }
            }
          }
        }
      }
    }
    $btnSubmit = "";
  }

  $arrMFAOptions = LoadMFAOptions($iUserID);
  if ($btnSubmit == "")
  {
    print "<p class=\"Header2\">General Info</p>\n\n";
    $strQuery = "SELECT vcPrivName FROM tblprivlevels where iPrivLevel = $Priv;";
    if (!$Result = $dbh->query ($strQuery))
    {
      error_log ('Failed to fetch data. Error ('. $dbh->errno . ') ' . $dbh->error);
      error_log ($strQuery);
      print "<p class=\"Attn\" align=center>$ErrMsg</p>\n";
      exit(2);
    }
    $Row = $Result->fetch_assoc();
    $PrivName = $Row['vcPrivName'];
    if ($PrivName == '')
    {
      $PrivName = $Priv;
    }

    print "<div class=\"MainTextCenter\">\n";
    if ($bChangePWD == 1)
    {
      print "<p class=\"Error\">You are required to change your password. Please provide new password and hit submit.</p>\n";
    }

    if ($dtUpdated=="")
    {
      print "<p class=\"Error\">This account has not been verified. Please verify the information, " .
            "make any needed changes, then submit to verify your information.</p>\n";
    }
    if ($strTOTP == "")
    {
      print "<p class=\"BlueNote\">You do not have MFA setup, to increase the security of your account " .
            "please setup a MFA option at the bottom of this page <a href=#mfa>here</a></p>\n";
    }
    print "<p>RegistrationID: $iUserID" ;
    $strQuery = "SELECT vcPrivName FROM tblprivlevels where iPrivLevel = $iPrivLevel;";
    if (!$PrivResult = $dbh->query ($strQuery))
    {
      error_log ('Failed to fetch data. Error ('. $dbh->errno . ') ' . $dbh->error);
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
    print "</div>\n";
    print "<form method=\"POST\">\n";
    require("UserRegForm.php");
    print "<tr>";
    print "<td colspan=2 align=\"center\" >";
    print "<p class=\"Header2\">Username and password</p>\n\n";
    print "You can change your username and password here,<br>\n";
    print "Just make sure you provide password and confirm it when changing your username.<br>";
    print "Also the following characters are stripped out of the password = \ \" '";
    print "</td>";
    print "</tr>";
    print "<tr><td align=\"right\" class=\"lbl\">UserName:</td>\n";
    print "<input type=\"hidden\" name=\"txtOUID\" value=\"$strUID\">";
    print "<td><input type=\"text\" name=\"txtUID\" size=\"50\" value=\"$strUID\"><span class=\"Attn\">Required</span></td></tr>\n";
    print "<tr><td align=\"right\" class=\"lbl\">Password:</td>\n";
    print "<td><input type=\"password\" name=\"txtPWD\" size=\"50\"><span class=\"Attn\">Required</span></td></tr>\n";
    print "<tr><td align=\"right\" class=\"lbl\">Confirm Password:</td>\n";
    print "<td><input type=\"password\" name=\"txtPWDConf\" size=\"50\"><span class=\"Attn\">Required</span></td></tr>\n";
    print "<tr><td colspan=\"2\" align=\"center\"><input type=\"submit\" value=\"Submit\" name=\"btnSubmit\"></td></tr>";
    print "</table></form>\n";

    print "<div class=\"MainTextCenter\">\n";

    require("UserPref.php");

    print "<p>&nbsp;</p>\n<p class=\"Header2\"><a id=\"mfa\">TOTP MultiFactor Authentication (MFA) Setup</a></p>\n";
    print "<p class=\"Header3\"> AKA Google Authenticator</p>\n";
    if ($strTOTP == "")
    {
      print "To Setup TOTP MFA click <a href=MFASetup.php>here</a><br>\n";
    }
    else
    {
      print "Thank you for securing your account with TOTP MFA. <br>\n";
      print "To change your TOTP MFA setup click <a href=MFASetup.php>here</a><br>\n";
    }
    if ($_SESSION["bMFA_active"])
    {
      print "<p>&nbsp;</p>\n<p class=\"Header2\">Reset Recovery Code</p>\n";
      print "To reset your recovery code, click this button.<br>\n";
      print "<form method=\"post\">\n";
      print "<input type=\"submit\" value=\"Reset Recovery Code\" name=\"btnSubmit\">\n";
      print "</form>\n";
      print "</div>\n";
    }
    print "<p>&nbsp;</p>\n<p>&nbsp;</p>\n<p class=\"Header2\">Account deletion</p>\n";
    print "<div class=\"MainTextCenter\">\n";
    print "<p>If you wish to completely delete your account you can do that here.</p>\n";
    print "<form method=\"post\">\n";
    print "<input type=\"submit\" value=\"Delete Account\" name=\"btnSubmit\">\n";
    print "<input type=\"hidden\" name=\"BeenSubmitted\" value=\"false\">\n";
    print "<input type=\"hidden\" name=\"iUserID\" size=\"5\" value=\"$iUserID\">\n";
    print "</form>\n";
    print "</div>\n";
  }
  require_once("footer.php");
  ?>
