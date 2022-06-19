<?php 
    require("header.php"); 
    $uuid = $_SERVER["QUERY_STRING"];
    #print "UUID: $uuid<br>/n";
    $strQuery = "SELECT * FROM tblemailupdate WHERE vcGUID= '$uuid' and dtConfirmed is null";
    if (!$Result = $dbh->query ($strQuery))
    {
        error_log ('Failed to fetch data. Error ('. $dbh->errno . ') ' . $dbh->error);
        error_log ($strQuery);
        exit(2);
    }
    $NumAffected = $Result->num_rows;
    if ($NumAffected<>1)
    {
        print "Invalid Confirmation string provided. Please check your email to ensure that you are using the entire URL " .
                "If you need assistance please contact $SupportEmail.<br> \n";
    }
    else
    {
        $Row = $Result->fetch_assoc();
        $strEmail = $Row['vcNewEmail'];
        $iUserID = $Row['iClientID'];
        print "Updating email in record $iUserID to $strEmail<br>\n";
        $strQuery = "update tblUsers set vcEmail = '$strEmail' where `iUserID`= $iUserID";
        if(UpdateSQL ($strQuery, "update"))
        {
            $strQuery = "update tblemailupdate set dtConfirmed = now() where vcGUID= '$uuid';";
            UpdateSQL($strQuery, "update");
        }
    }
    require("footer.php"); 
?>