<?php
  require_once("header.php");
  if ($strTOTP == "" or $strMFAType == "smsemail")
  {
    # Generate and send code
    $strQuery = "SELECT iTypeID, vcValue FROM tblUsrPrefValues WHERE iUserID = $iUserID AND iTypeID IN (2,3);";
    $QueryData = QuerySQL($strQuery);
    $ConfCode = bin2hex(random_bytes(4));
    $_SESSION["ConfCode"]=$ConfCode;
    $strMsg = "Your confirmation code is: $ConfCode";

    if($QueryData[0] > 0)
    {
      foreach($QueryData[1] as $Row)
      {
        if (strtolower($Row['vcValue']) == "true" && strtolower($Row['iTypeID']) == "2")
        {
          SendUserSMS($strMsg,$iUserID);
        }
        if (strtolower($Row['vcValue']) == "true" && strtolower($Row['iTypeID']) == "3")
        {
          EmailText($strEmail,"Login Verification Code",$strMsg,$FromEmail);
        }
      }
    }

  }
  elseif ($_SESSION["bSMSemail"])
  {
    print "<p class=BlueAttn>To use SMS or email MFA, change the dropdown make sure the value field is empty and hit submit</p>";
  }
  print "<form method=\"POST\">";
  print "Select your MFA Type: ";
  print "<select size=\"1\" name=\"cmbMFA\">\n";
  foreach($arrMFAOptions as $key => $value)
  {
    if ($key == $strMFAType)
    {
      print "<option value=$key selected>$value</option>\n";
    }
    else
    {
      print "<option value=$key>$value</option>\n";
    }
  }
  print "</select>";
  print "<INPUT TYPE=\"HIDDEN\" NAME=\"txtLogin\" VALUE=\"$strLogin\">";
  print "<INPUT TYPE=\"HIDDEN\" NAME=\"txtPwd\" VALUE=\"$strPWD\">";
  print "&nbsp;&nbsp;Please provide your code: ";
  print "<input type=\"text\" name=\"txtCode\" size=\"30\">";
  print "&nbsp;&nbsp;<input type=\"submit\" value=\"Submit\" name=\"btnLogin\">";
  print "</form>";
  require_once("footer.php");
?>