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
  $strQuery = "SELECT iID, vcLabel FROM tblUsrPrefTypes";

  $QueryData = QuerySQL($strQuery);
  $arrTypes = $QueryData[1];

  $strQuery = "SELECT iTypeID, vcValue FROM tblUsrPrefValues WHERE iUserID = $iUserID;";
  $QueryData = QuerySQL($strQuery);
  $arrValues = $QueryData[1];

  foreach ($arrTypes as $Types)
  {
    $bFound = False;
    foreach ($arrValues as $Values)
    {
      if ($Types["iID"] == $Values["iTypeID"])
      {
        $bFound = True;
      }
    }
    if (!$bFound)
    {
      $strQuery = "INSERT INTO tblUsrPrefValues (iTypeID, iUserID) VALUES ($Types[iID],$iUserID );";
      UpdateSQL ($strQuery, "insert");
    }
  }
?>
