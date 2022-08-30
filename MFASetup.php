<?php
  /*
  Copyright © 2009,2015,2022  Siggi Bjarnason.
  Licensed under GNU GPL v3 and later. Check out LICENSE.TXT for details   
  or see <https://www.gnu.org/licenses/gpl-3.0-standalone.html>

  Page to Setup or manage your MFA options
  */

  require("header.php");
  use RobThree\Auth\TwoFactorAuth;
  spl_autoload_register(
    function ($className)
    {
      include_once str_replace(array('RobThree\\Auth', '\\'), array(__DIR__.'/RobThree2FA', '/'), $className) . '.php';
    }
  );
  $tfa = new TwoFactorAuth($HeadAdd . $ProdName);

  if(!isset($iUserID))
  {
    $iUserID = -15;
  }

  if(isset($_POST['btnSubmit']))
  {
    $btnSubmit = $_POST['btnSubmit'];
  }
  else
  {
    $btnSubmit = "";
  }

  if($btnSubmit == 'Reset' or $btnSubmit == 'Cancel')
  {
    unset($_SESSION["2FASecret"]);
    printPg("TOTP MFA Setup has been cancelled.","center");
  }

  if($btnSubmit == 'Delete')
  {
    $strQuery = "update tblUsers set vcMFASecret = '' where iUserID = $iUserID;";
    if(UpdateSQL($strQuery, "update"))
    {
      printPg("TOTP MFA Successfully removed","note");
      unset($_SESSION["2FASecret"]);
    }
    else
    {
      printPg("Failed to update database","error");
    }
  }

  if($btnSubmit == 'Submit')
  {
    $MFASecret = CleanReg(substr(trim($_POST['txtSecret']),0,20));
    $_SESSION["2FASecret"] = $MFASecret;
    printPg("Validating your TOTP MFA");
    $iUserCode = intval($_POST['txtCode']);
    if($tfa->verifyCode($MFASecret, strval($iUserCode)) === true)
    {
      printPg("OK","note");
      $strQuery = "update tblUsers set vcMFASecret = '$MFASecret' where iUserID = $iUserID;";
      if(UpdateSQL($strQuery, "update"))
      {
        printPg("Setup Completed Successfully","note");
        unset($_SESSION["2FASecret"]);
        GenerateRecovery($iUserID);
      }
      else
      {
        printPg("Failed to update database","error");
      }
    }
    else
    {
      printPg("FAIL","error");
    }
  }


  $strQuery = "select * from tblUsers where iUserID = $iUserID;";
  $QueryData = QuerySQL($strQuery);
  if($QueryData[0] > 0)
  {
    foreach($QueryData[1] as $Row)
    {
      $strUserEmail = $Row['vcEmail'];
      $strCurrSecret = $Row['vcMFASecret'];
      $strUserName = $Row['vcName'];
    }
  }
  else
  {
    if($QueryData[0] == 0)
    {
      error_log("query of $strQuery returned no rows");
      $strUserEmail = "";
      $strCurrSecret = "";
      $strUserName = "";
    }
    else
    {
      $strMsg = implode(";",$QueryData[1]);
      error_log("Query of $strQuery did not return data. Rowcount: $QueryData[0] Msg:$strMsg");
      printPg($ErrMsg,"error");
    }
  }

  if($btnSubmit == "")
  {
    printPg("TOTP MFA Setup<","h1");
    $AuthApp = $TextArray["AuthApp"];
    $RecovCode = $TextArray["RecovCode"];
    if($strUserEmail != "")
    {
      printPg("Setting up TOTP MFA (AKA Google Auth) for $strUserName","h2");
      printPg("$AuthApp","center");

      if(isset($_SESSION["2FASecret"]))
      {
        $MFASecret = $_SESSION["2FASecret"];
      }
      else
      {
        if($strCurrSecret != "")
        {
          printPg("If you complete this setup your existing TOTP MFA will be replaced. Hit cancel if you want to keep your existing setup!!","note");
          print "<div class=\"MainTextCenter\">\n";
          print "<form method=\"POST\">\n";
          print "<div>To remove your TOTP MFA setup, click the delete button:\n";
          print "<input type=\"Submit\" value=\"Delete\" name=\"btnSubmit\">\n";
          print "</div>\n";
          print "</form>\n";
          print "</div>\n";
        }
        $MFASecret = $tfa->createSecret();
        $btnSubmit = "x";
        $strDispSecret = chunk_split($MFASecret,4," ");
        print "<div class=\"MainTextCenter\">\n";
        printPg("Please enter the following code in your app:$strDispSecret","normal");
        $QRcode = $tfa->getQRCodeImageAsDataUri($strUserEmail, $MFASecret);
        printPg("Or scan this QR code:<br>\n <img src=\"$QRcode\">","normal");
        print "<form method=\"POST\">\n";
        print "<div>Enter your code:\n";
        print "<input type=\"text\" name=\"txtCode\" size=\"10\">\n";
        print "<input type=\"hidden\" value=\"$MFASecret\" name=\"txtSecret\">";
        print "<input type=\"Submit\" value=\"Submit\" name=\"btnSubmit\">\n";
        print "<input type=\"Submit\" value=\"Cancel\" name=\"btnSubmit\">\n";
        print "</div>\n";
        print "</form>\n";
        print "</div>\n";
      }
    }
    else
    {
      printPg("Seems your info couldn't be found in the database, this is not right. You need to tell $SupportEmail about this","tmh2");
      unset($_SESSION["2FASecret"]);
    }

    if(isset($_SESSION["2FASecret"]) and $btnSubmit == '')
    {
      print "<div class=\"MainTextCenter\">\n";
      printPg("It seems your TOTP MFA setup is not validated. Please Enter the code from your Authenticator"
            . " app to validate and complete the setup, or cancel the setup","normal");
      print "<form method=\"POST\">\n";
      print "<div>Enter your code:\n";
      print "<input type=\"text\" name=\"txtCode\" size=\"10\">\n";
      print "<input type=\"Submit\" value=\"Submit\" name=\"btnSubmit\">\n";
      print "<input type=\"Submit\" value=\"Cancel\" name=\"btnSubmit\">\n";
      print "</div>\n";
      print "</form>\n";
      print "</div>\n";
    }
  }

  require("footer.php");
?>
