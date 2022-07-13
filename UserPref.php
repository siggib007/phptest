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

  require("header.php");
  if ($strReferer != $strPageURL and $PostVarCount > 0)
  {
    print "<p class=\"Error\">Invalid operation, very Bad Reference!!!</p> ";
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
    if (stripos($strLabel,"mfa")!== false)
    {
      printNote("Make sure you have a recovery code for MFA failures.<br>\nIf you need a new recovery code, ".
                "head over to the <a href=UserProfileOther.php>Other tab</a> to reset your recovery code and get a new one.");
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

  $arrMFAOptions = LoadMFAOptions($iUserID);
  print "<p class=\"Header2\"><a id=\"mfa\">Account Preferences</a></p>\n";
  print "<div class=\"MainTextCenter\">\n";
  $btnValue = "";
  $strQuery = "SELECT t.*,v.vcValue,v.iUserID ".
  "FROM tblUsrPrefTypes t JOIN tblUsrPrefValues v ON t.iID = v.iTypeID ".
  "WHERE v.iUserID = $iUserID OR v.iUserID IS NULL ORDER BY iSortOrder;";

  if (!$Result = $dbh->query ($strQuery))
  {
    error_log ('Failed to fetch data. Error ('. $dbh->errno . ') ' . $dbh->error);
    error_log ($strQuery);
    print "<p class=\"Attn\" align=center>$ErrMsg</p>\n";
    exit(2);
  }
  while ($Row = $Result->fetch_assoc())
  {
    $Key = $Row['iID'];
    $Value = $Row['vcValue'];
    $ValueDescr = $Row['vcLabel'];
    $ValueType = $Row['vcType'];
    print "<form method=\"POST\">\n";
    print "<input type=\"hidden\" value=\"$Key\" name=\"txtKey\">";
    print "<input type=\"hidden\" value=\"$ValueDescr\" name=\"txtLabel\">";
    print "<p>$ValueDescr: ";
    switch ($ValueType)
    {
      case "Boolean":
        if (strtolower($Value)=="true")
        {
          $btnValue = "Enabled";
        }
        else
        {
          $btnValue = "Disabled";
        }
        break;
      case "int":
      case "text":
        print "<input type=\"text\" value=\"$Value\" name=\"txtValue\" size=\"30\" >";
        $btnValue = "Save";
        break;
      }
      print "<input type=\"Submit\" value=\"$btnValue\" name=\"btnSubmit\"></p>\n";
      print "</form>\n";
  }
  print "</div>\n";
  require("footer.php");
?>
