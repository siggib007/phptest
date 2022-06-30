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
    $iPrivLevel = $Row['iPrivLevel'];
    $strEmail = $Row['vcEmail'];
    $strCell = $Row['vcCell'];
    $strUID = $Row['vcUID'];
    $dtUpdated = $Row['dtUpdated'];
    $strTOTP = $Row['vcMFASecret'];
?>
