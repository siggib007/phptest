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
            print "<p class=\"BlueAttn\">Since you haven't provided a cell phone, all SMS functionality has been disabled</p>";
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
        $strCleanUID = preg_replace('/[^a-z0-9]*/i', '', $strUID);
        if ($strCleanUID != $strUID)
        {
          print "<p class=\"Error\">New Username contains illegal characters, only a-z and 0-9 are allowed in usernames. ".
                "Nothing was changed, please specify a valid username if you want to change it</p>\n";
        }
        else 
        {
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
              print "<p class=\"Error\">Passwords is too short, please supply a password that is at least $MinPWDLen char long.</p>\n";
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
                print "<p class=\"Error\">Please provide password to change your user name.</p>";
              }
              if ($Password != $PWDConf and $strUID == $strUID2)
              {
                print "<p class=\"Error\">Passwords do not match so password and username was not changed.</p>\n";
              }
              if ($Password == $PWDConf and $strUID != $strUID2)
              {
                print "<p class=\"Error\">Requested username is already in use could not be changed, however the password will been changed. " .
                      "To change the username use a different username that is not in use. For example $strUID2 is available.</p>\n";
                $strQuery = "UPDATE tblUsers SET vcPWD = '$PWD', bChangePWD = 0 WHERE iUserID = '$iUserID'";
              }
              if ($Password != $PWDConf and $strUID != $strUID2)
              {
                print "<p class=\"Error\">Passwords do not match so password was not changed. Requested username is already in use could not be changed. " .
                      "To change the username Try again and use a different username that is not in use. " .
                      "For example $strUID2 is available. When changing the password make sure you type the same one twice.</p>\n";
              }
              if ($strQuery)
              {
                if(UpdateSQL($strQuery, "update"))
                {
                  print "<p class=\"BlueAttn\">Update successful!!</p>";
                  $bChangePWD = 0;
                }
              }
            }
          }
        }
      }
    }
    $btnSubmit = "";
  }

  if ($btnSubmit == "")
  {
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
      "please setup a MFA on the <a href=MFASetup.php>MFA Setup</a> page</p>\n";
    }
    print "<p class=\"Header2\">General Info</p>\n\n";
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
    print "Only letters a-z and numbers 0-9 are allowed in usernames!<br>\n";
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
  }
  require_once("footer.php");
  ?>
