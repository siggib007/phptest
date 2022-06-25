<?php
    $bSpam = FALSE;
    $strUserID = trim($_POST['iUserID']);
    $strName = substr(trim($_POST['txtName']),0,49);
    $strAddr1 = substr(trim($_POST['txtAddr1']),0,49);
    $strAddr2 = substr(trim($_POST['txtAddr2']),0,49);
    $strCity = substr(trim($_POST['txtCity']),0,49);
    $strState = substr(trim($_POST['cmbState']),0,49);
    $strZip = substr(trim($_POST['txtZip']),0,9);
    $strCountry = substr(trim($_POST['cmbCountry']),0,99);
    $strEmail = substr(trim($_POST['txtEmail']),0,49);
    $strOEmail = substr(trim($_POST['txtOEmail']),0,49);
    $strCell = substr(trim($_POST['txtCell']),0,19);
    if (SpamDetect($strName))
    {
        print "<p class=\"Error\">URL detected in Name field</p>";
        $bSpam = TRUE;
    }
    if (SpamDetect($strAddr1))
    {
        print "<p class=\"Error\">URL detected in Address 1 field</p>";
        $bSpam = TRUE;
    }
    if (SpamDetect($strAddr2))
    {
        print "<p class=\"Error\">URL detected in Address 2 field</p>";
        $bSpam = TRUE;
    }
    if (SpamDetect($strCity))
    {
        print "<p class=\"Error\">URL detected in City field</p>";
        $bSpam = TRUE;
    }
    if (SpamDetect($strZip))
    {
        print "<p class=\"Error\">URL detected in Zip field</p>";
        $bSpam = TRUE;
    }
    if (SpamDetect($strEmail))
    {
        print "<p class=\"Error\">URL detected in email field</p>";
        $bSpam = TRUE;
    }
    if (SpamDetect($strCell))
    {
        print "<p class=\"Error\">URL detected in Cell phone field</p>";
        $bSpam = TRUE;
    }
    $strName = CleanReg($strName);
    $strAddr1 = CleanReg($strAddr1);
    $strAddr2 = CleanReg($strAddr2);
    $strCity = CleanReg($strCity);
    $strZip = CleanReg($strZip);
    $strEmail = CleanReg($strEmail);
    $strOEmail = CleanReg($strOEmail);
    $strCell = CleanReg($strCell);
    if($bSpam)
    {
        print "<p class=\"Error\">Your update has been flagged as spam as invalid or missing required data was detected in one or more input fields. ";
        print "Your changes were not saved. Your IP has been logged</p>";
    }
?>
