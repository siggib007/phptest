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
    if (isset($_SESSION["auth_username"] ) )
    {
        print "<p class=\"Error\">You're already registered, what are you trying to do????</p> ";
        exit;
    }

    $RegFoot = $TextArray["RegFoot"];
    $RegHeader = $TextArray["RegHead"];

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
    if (isset($GLOBALS["ConfArray"]["minRegLevel"]) )
    {
        $iLevel = $GLOBALS["ConfArray"]["minRegLevel"];
    }
    else
    {
        $iLevel = 1;
    }
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
            print "<p class=\"Error\">Can't create new user without an email. Contact $SupportEmail if you have any questions.</p>";
        }
    }
    if (!$bSuccess)
    {
        print "<p>$RegHeader</p>\n";
        print "<form method=\"POST\">\n";
        require("UserRegForm.php");
        print "<tr>\n<td colspan=\"2\" align=\"center\">$RegFoot</td>\n</tr>\n";
        print "<tr>\n<td colspan=\"2\" align=\"center\"><input type=\"Submit\" value=\"Submit\" name=\"btnSubmit\"></td>\n</tr>\n";
        print "</table>\n</form>\n";
    }
    require("footer.php");
?>