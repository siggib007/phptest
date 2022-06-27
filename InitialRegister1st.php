<?php
    require("header.php");
    date_default_timezone_set('America/Los_Angeles');

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

    $strQuery = "SELECT vcTextName, tPageTexts FROM tblPageTexts WHERE vcTextName IN ('SetupReg');";
    if (!$Result = $dbh->query ($strQuery))
    {
        error_log ('Failed to fetch data. Error ('. $dbh->errno . ') ' . $dbh->error);
        error_log ($strQuery);
        print "<p class=\"Attn\" align=center>$ErrMsg</p>\n";
        exit(2);
    }

    while ($Row = $Result->fetch_assoc())
    {
        switch ($Row['vcTextName'])
        {
            case "SetupReg":
              $RegHeader = $Row['tPageTexts'];
              break;
        }
    }

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


    print "<p class=\"BlueAttn\">$RegHeader</p>";

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
        require_once 'CleanReg.php';

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
                    require 'UserAdd.php';
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
      $strQuery = "DELETE FROM tblmenu WHERE vcLink='InitialRegister1st.php' LIMIT 1;";
      UpdateSQL ($strQuery,"delete");
      unlink("InitialRegister1st.php");
      print "<p class=\"BlueAttn\"><a href='index.php'>Setup completed. Click here to go home</a>\n";
    }
    else
    {
        print "<form method=\"POST\">\n";
        require 'UserRegForm.php';
        print "<tr>\n<td colspan=\"2\" align=\"center\"><input type=\"Submit\" value=\"Submit\" name=\"btnSubmit\"></td>\n</tr>\n";
        print "</table>\n</form>\n";
    }
    require ("footer.php");
?>