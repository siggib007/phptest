<?php
    require("header.php");

    if (isset($_POST['btnSubmit']))
    {
      $btnSubmit = $_POST['btnSubmit'];
    }
    else
    {
      $btnSubmit = "";
    }

    print "<p class=\"Header1\">2FA Setup</p>\n";
    spl_autoload_register(
      function ($className)
      {
        include_once str_replace(array('RobThree\\Auth', '\\'), array(__DIR__.'/RobThree2FA', '/'), $className) . '.php';
      }
    );

    use RobThree\Auth\TwoFactorAuth;
    $tfa = new TwoFactorAuth("Siggi's PHP Demo");
    if (isset($_SESSION["2FASecret"]))
    {
      $MFASecret = $_SESSION["2FASecret"];
      // print("<p>2FA setup in progress</p>");
    }
    else
    {
      print("<p>2FA setup starting</p>");
      $MFASecret = $tfa->createSecret();
      $_SESSION["2FASecret"] = $MFASecret;
      $strDispSecret = chunk_split($MFASecret,4," ");
      print("<p>Please enter the following code in your app:$strDispSecret</p>\n");
      $QRcode = $tfa->getQRCodeImageAsDataUri('Demo', $MFASecret);
      print("<p>Or scan this QR code:<br>\n <img src=\"$QRcode\"></p>\n");
    }
    print "<form method=\"POST\">\n";
    print "<p>Enter your code:\n";
    print "<input type=\"text\" name=\"txtCode\" size=\"10\">\n";
    print "<input type=\"Submit\" value=\"Submit\" name=\"btnSubmit\">\n";
    print "</p>\n";
    print "</form>\n";
    // $code = $tfa->getCode($MFASecret);
    // print("<p>Your code should be $code </p>\n");

    if ($btnSubmit == 'Submit')
    {
      $iUserCode = intval($_POST['txtCode']);
      if ($tfa->verifyCode($MFASecret, strval($iUserCode)) === true)
      {
        print("<p style=\"color:#0c0\">OK</p>");
      }
      else
      {
        print("<p style=\"color:#c00\">FAIL</p>");
      }
    }



    require("footer.php");
?>
