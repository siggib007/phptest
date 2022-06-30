<?php
//Copyright © 2009,2015  Siggi Bjarnason.
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
  use RobThree\Auth\TwoFactorAuth;

  if (!isset($iUserID))
  {
    $iUserID = -15;
  }

  if (isset($_POST['btnSubmit']))
  {
    $btnSubmit = $_POST['btnSubmit'];
  }
  else
  {
    $btnSubmit = "";
  }
  $strQuery = "select * from tblUsers where iUserID = $iUserID;";
  if (!$Result = $dbh->query ($strQuery))
  {
    error_log ('Failed to fetch user data for TOTP MFA setup. Error ('. $dbh->errno . ') ' . $dbh->error);
    error_log ($strQuery);
    exit(2);
  }
  $rowcount=mysqli_num_rows($Result);
  if ($rowcount > 0)
  {
    $Row = $Result->fetch_assoc();
    $strUserEmail = $Row['vcEmail'];
    $strCurrSecret = $Row['vcMFASecret'];
    $strUserName = $Row['vcName'];
  }
  else
  {
    error_log("query of $strQuery returned no rows");
    $strUserEmail = "";
    $strCurrSecret = "";
    $strUserName = "";
  }

  print "<p class=\"Header1\">TOTP MFA Setup</p>\n";
  if ($strUserEmail != "")
  {
    spl_autoload_register(
      function ($className)
      {
        include_once str_replace(array('RobThree\\Auth', '\\'), array(__DIR__.'/RobThree2FA', '/'), $className) . '.php';
      }
    );
    print("<p class=\"Header2\">Setting up TOTP MFA (AKA Google Auth) for $strUserName</p>\n");
    $tfa = new TwoFactorAuth($ProdName);
    if (isset($_SESSION["2FASecret"]))
    {
      $MFASecret = $_SESSION["2FASecret"];
    }
    else
    {
      if ($strCurrSecret != "")
      {
        print("<p class=\"BlueNote\">If you complete this setup your existing TOTP MFA will be replaced. Hit cancel if you want to keep your existing setup!!</p>\n");
        print("<div class=\"MainTextCenter\">\n");
        print("<form method=\"POST\">\n");
        print("<p>To remove your TOTP MFA setup, click the delete button:\n");
        print("<input type=\"Submit\" value=\"Delete\" name=\"btnSubmit\">\n");
        print("</p>\n");
        print("</form>\n");
        print("</div>\n");
      }
      $MFASecret = $tfa->createSecret();
      $_SESSION["2FASecret"] = $MFASecret;
      $btnSubmit = "x";
      $strDispSecret = chunk_split($MFASecret,4," ");
      print("<div class=\"MainTextCenter\">\n");
      print("<p>Please enter the following code in your app:$strDispSecret</p>\n");
      $QRcode = $tfa->getQRCodeImageAsDataUri($strUserEmail, $MFASecret);
      print("<p>Or scan this QR code:<br>\n <img src=\"$QRcode\"></p>\n");
      print("<form method=\"POST\">\n");
      print("<p>Enter your code:\n");
      print("<input type=\"text\" name=\"txtCode\" size=\"10\">\n");
      print("<input type=\"Submit\" value=\"Submit\" name=\"btnSubmit\">\n");
      print("<input type=\"Submit\" value=\"Cancel\" name=\"btnSubmit\">\n");
      print("</p>\n");
      print("</form>\n");
      print("</div>\n");
    }
  }
  else
  {
    print("<p class=\"Error\">Seems your info couldn't be found in the database, this is not right. You need to tell $SupportEmail about this</p>");
    unset($_SESSION["2FASecret"]);
  }

  if (isset($_SESSION["2FASecret"]) and $btnSubmit == '')
  {
    print("<div class=\"MainTextCenter\">\n");
    print("<p>It seems your TOTP MFA setup is not validated. Please Enter the code from your Authenticator"
          . " app to validate and complete the setup, or cancel the setup</p>");
    print "<form method=\"POST\">\n";
    print "<p>Enter your code:\n";
    print "<input type=\"text\" name=\"txtCode\" size=\"10\">\n";
    print "<input type=\"Submit\" value=\"Submit\" name=\"btnSubmit\">\n";
    print "<input type=\"Submit\" value=\"Cancel\" name=\"btnSubmit\">\n";
    print "</p>\n";
    print "</form>\n";
    print("</div>\n");
  }

  if ($btnSubmit == 'Reset' or $btnSubmit == 'Cancel')
  {
    unset($_SESSION["2FASecret"]);
    print("<p class=\"MainTextCenter\">TOTP MFA Setup has been cancelled.</p>");
  }

  if ($btnSubmit == 'Delete')
  {
    $strQuery = "update tblUsers set vcMFASecret = '' where iUserID = $iUserID;";
    if(UpdateSQL ($strQuery, "update"))
    {
      print("<p class=\"BlueNote\">TOTP MFA Successfully removed</p>");
      unset($_SESSION["2FASecret"]);
    }
    else
    {
      print("<p class=\"Error\">Failed to update database</p>");
    }
  }

  if ($btnSubmit == 'Submit')
  {
    print("<p class=\"MainTextCenter\">Validating your TOTP MFA</p>");
    $iUserCode = intval($_POST['txtCode']);
    if ($tfa->verifyCode($MFASecret, strval($iUserCode)) === true)
    {
      print("<p class=\"BlueNote\">OK</p>");
      $strQuery = "update tblUsers set vcMFASecret = '$MFASecret' where iUserID = $iUserID;";
      if(UpdateSQL ($strQuery, "update"))
      {
        print("<p class=\"BlueNote\">Setup Completed Successfully</p>");
        unset($_SESSION["2FASecret"]);
      }
      else
      {
        print("<p class=\"Error\">Failed to update database</p>");
      }
    }
    else
    {
      print("<p class=\"Error\">FAIL</p>");
    }
  }

  require("footer.php");
?>
