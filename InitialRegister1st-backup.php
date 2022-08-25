<?php
    require("header.php");
    if (isset($_POST['btnSubmit']))
    {
      $btnSubmitValue = $_POST['btnSubmit'];
    }
    else
    {
      $btnSubmitValue = "";
    }
    if ($strReferer != $strPageURL and $PostVarCount > 0)
    {
      print "<p class=\"Error\">Invalid operation, Bad Reference!!!</p> ";
      exit;
    }

    if (!isset($GLOBALS["ConfArray"]["InitSetup"]) )
    {
      print "<p class=\"Error\">Setup Mode isn't enable so you can't use this page</p> ";
      exit;
    }
    require("FileInv.php");
    $strQuery = "UPDATE tblmenu SET iReadPriv='300' WHERE vcLink = 'FileInv.php';";
    UpdateSQL ($strQuery,"update");

    $RegHeader = $TextArray["RegForm"];

    $strQuery = "SELECT iPrivLevel FROM tblprivlevels WHERE vcPrivName LIKE '%admin%';";
    if (!$Result = $dbh->query ($strQuery))
    {
      error_log ('Failed to fetch data. Error ('. $dbh->errno . ') ' . $dbh->error);
      error_log ($strQuery);
      print "<p class=\"Attn\" align=center>$ErrMsg</p>\n";
      exit(2);
    }
    $rowcount=mysqli_num_rows($Result);
    if ($rowcount > 0)
    {
      $Row = $Result->fetch_assoc();
      $iLevel = $Row['iPrivLevel'];
    }
    else
    {
      $iLevel = 300;
    }

    $strName = "";
    $strAddr1 = "";
    $strAddr2 = "";
    $strCity = "";
    $strState = "";
    $strZip = "";
    $strCountry = "";
    $iPrivLevel = "";
    $strEmail = "";
    $strUserID = "";
    $strCell = "";
    $bSuccess = FALSE;
    $bRegOK = FALSE;

    if ($btnSubmitValue == 'Submit')
    {
      require_once("CleanReg.php");

      if ($strEmail)
      {
        $strNameParts = explode(' ',$strName);
        $HowMany = count($strNameParts);
        if ($HowMany==1)
        {
          print "<p class=\"Error\">Please provide both first and last name</p>";
        }
        else
        {
          if (!$bSpam)
          {
            require("UserAdd.php");
          }
        }
      }
      else
      {
        print "<p class=\"Error\">Can't create new admin account without an email..</p>";
      }
    }
    $bSuccess = $bRegOK;
    if ($bSuccess)
    {
      $strQuery = "DELETE FROM tblconf WHERE vcValueName='InitSetup' LIMIT 1;";
      UpdateSQL ($strQuery,"delete");
      $strQuery = "DELETE FROM tblPageTexts WHERE vcTextName='SetupReg' LIMIT 1;";
      UpdateSQL ($strQuery,"delete");
      $strQuery = "DELETE FROM tblmenu WHERE vcLink='$strPageName' LIMIT 1;";
      UpdateSQL ($strQuery,"delete");
      if (strtolower($DevEnvironment) != "true")
      {
        unlink($strPageName);
      }
      print "<p class=\"BlueAttn\">\n<a href='index.php'>Setup completed. Click here to go home</a>\n</p>\n";
    }
    else
    {
      print "<p class=\"BlueAttn\">$RegHeader</p>";
      print "<form method=\"POST\">\n";
      require("UserRegForm.php");
      print "<tr>\n<td colspan=\"2\" align=\"center\"><input type=\"Submit\" value=\"Submit\" name=\"btnSubmit\"></td>\n</tr>\n";
      print "</table>\n</form>\n";
    }
    require("footer.php");
?>