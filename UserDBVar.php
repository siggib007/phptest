<?php
  $strQuery = "select * from tblUsers where iUserID = $strUserID;";
  $QueryData = QuerySQL($strQuery);

  if($QueryData[0] > 0)
  {
    foreach($QueryData[1] as $Row)
    {
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
    }
  }
  else
  {
    error_log("Rowcount: $QueryData[0] Msg:$QueryData[1]");
    $strName = "Error occured";
    $strAddr1 = "Failed to fetch the user data from the database";
    $strAddr2 = "";
    $strCity = "";
    $strState = "";
    $strZip = "";
    $strCountry = "";
    $iPrivLevel = "";
    $strEmail = "";
    $strCell = "";
    $strUID = "";
    $dtUpdated = "";
    $strTOTP = "";
  }
  $arrUserPrefs = array();
  $strQuery = "SELECT t.*,v.vcValue,v.iUserID ".
              "FROM tblUsrPrefTypes t LEFT JOIN tblUsrPrefValues v ON t.iID = v.iTypeID ".
              "WHERE v.iUserID = $iUserID OR v.iUserID IS NULL;";

  $QueryData = QuerySQL($strQuery);
  error_log("checking on prefs and populating. Rowcount = $QueryData[0] ");
  error_log($strQuery);

  if($QueryData[0] == 0)
  {
    error_log("Count was zero inserting #1");
    $strQuery = "INSERT INTO tblUsrPrefValues (iTypeID, iUserID) VALUES (1,$iUserID );";
    UpdateSQL ($strQuery, "insert");

    $strQuery = "SELECT t.*,v.vcValue,v.iUserID ".
    "FROM tblUsrPrefTypes t LEFT JOIN tblUsrPrefValues v ON t.iID = v.iTypeID ".
    "WHERE v.iUserID = $iUserID OR v.iUserID IS NULL;";
    $QueryData = QuerySQL($strQuery);
  }

  if($QueryData[0] > 0)
  {
    foreach($QueryData[1] as $Row)
    {
      if (is_null($Row["iUserID"]))
      {
        $strQuery = "INSERT INTO tblUsrPrefValues (iTypeID, iUserID) VALUES ($Row[iID],$iUserID );";
        UpdateSQL ($strQuery, "insert");
      }
      $arrUserPrefs[] = $Row;
    }
  }
?>
