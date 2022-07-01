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
        $strUID = substr(trim($_POST['txtUID']),0,19);
        $strOUID = substr(trim($_POST['txtOUID']),0,19);
        $Password = trim($_POST['txtPWD']);
        $PWDConf = trim($_POST['txtPWDConf']);
        $strUID = str_replace("'","",$strUID);
        CleanReg($strUID);
        CleanReg($strOUID);
        CleanReg($Password);
        cleanreg($PWDConf);
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

          if ($strUID2 == $strUID)
          {
            $salt = substr($strUID , 0, 4) ;
            $PWD = crypt($Password , $salt);
          }
          else
          {
            $salt = substr($strOUID , 0, 4) ;
            $PWD = crypt($Password , $salt);
          }
          $strQuery="";
          if ($Password == $PWDConf and $strUID == $strUID2 and $Password != '')
          {
            $strQuery = "UPDATE tblUsers SET vcUID = '$strUID', vcPWD = '$PWD' WHERE iUserID = '$iUserID'";
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
            $strQuery = "UPDATE tblUsers SET vcPWD = '$PWD' WHERE iUserID = '$iUserID'";
          }
          if ($Password != $PWDConf and $strUID != $strUID2)
          {
            print "<p>Passwords do not match so password was not changed. Requested username is already in use could not be changed. " .
                  "To change the username Try again and use a different username that is not in use. " .
                  "For example $strUID2 is available. When changing the password make sure you type the same one twice.</p>\n";
          }
          if ($strQuery)
          {
            UpdateSQL($strQuery, "update");
          }
        }
      }
    }
  }

  if ($btnSubmit == "" or $btnSubmit == "Submit")
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

    require("UserDBVar.php");
    print "<div class=\"MainTextCenter\">\n";
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
    print "<p>&nbsp;</p>\n<p class=\"Header2\"><a id=\"mfa\">MultiFactor Setup</a></p>\n";
    print "<p class=\"Header3\">TOTP MFA (AKA Google Authenticator)</p>\n";
    if ($strTOTP == "")
    {
      print "To Setup TOTP MFA click <a href=MFASetup.php>here</a><br>\n";
    }
    else
    {
      print "Thank you for securing your account with TOTP MFA. <br>\n";
      print "To change your TOTP MFA setup click <a href=MFASetup.php>here</a><br>\n";
    }
    print "<p class=\"Header3\">Reset Recovery Code</p>\n";
    print "To reset your recovery code, click this button.<br>\n";
    print "<form method=\"post\">\n";
    print "<input type=\"submit\" value=\"Reset Recovery Code\" name=\"btnSubmit\">\n";
    print "</form>\n";
    print "</div>\n";

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
