<?php
    $strQuery = "select * from tblUsers where iUserID = $strUserID;";
    if (!$Result = $dbh->query ($strQuery))
    {
        error_log ('Failed to data. Error ('. $dbh->errno . ') ' . $dbh->error);
        error_log ($strQuery);
        print "<p class=\"Attn\" align=center>$ErrMsg</p>\n";
        exit(2);
    }
    $Row = $Result->fetch_assoc();
    $strName = $Row['vcName'];
    $strAddr1 = $Row['vcAddr1'];
    $strAddr2 = $Row['vcAddr2'];
    $strCity = $Row['vcCity'];
    $strState = $Row['vcState'];
    $strZip = $Row['vcZip'];
    $strCountry = $Row['vcCountry'];
    $strPhone = $Row['vcPhone'];
    $iPrivLevel = $Row['iPrivLevel'];
    $strEmail = $Row['vcEmail'];
    $strCell = $Row['vcCell'];
    $strBdate = $Row['vcBirthdate'];
    $strWedAnn = $Row['vcWedAnn'];
    $strHealth = $Row['vcHealthIssues'];
    $strLocate = $Row['vcLocate'];
    $strUID = $Row['vcUID'];
    $dtUpdated = $Row['dtUpdated'];
    $strGender = $Row['vcGender'];
    $iDelegate = $Row['iDelegateUserID'];
?>
