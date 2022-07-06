<?php
  //print "Login Successful<br>\n";
  $_SESSION["auth_username"] = $Row['vcName'];
  $_SESSION["auth_UID"] = $Row['vcUID'];
  $_SESSION["UID"] = $iUserID;
  $_SESSION["dtLogin"] = $dtNow;
  $_SESSION["iPrivLevel"] = $iPrivlvl;
  $_SESSION["LastActivity"] = time();
  $_SESSION["LoginTime"] = $dtNow;

  $strQuery = "SELECT v.iTypeID, v.vcValue, u.vcEmail " .
              "FROM tblUsrPrefValues v JOIN tblUsers u ON v.iUserID = u.iUserID " .
              "WHERE v.iUserID = $iUserID AND v.iTypeID IN (4,5);";
  $QueryData = QuerySQL($strQuery);
  $DataSet = array();
  if($QueryData[0] > 0)
  {
    foreach($QueryData[1] as $Row)
    {
      $iTypeID = $Row['iTypeID'];
      $strValue = $Row['vcValue'];
      $strEmail = $Row['vcEmail'];
      $DataSet[$iTypeID]=array("value"=>$strValue,"email");
    }
  }
  if(count($DataSet > 0))
  {
    if(array_key_exists("4",$DataSet)) # Receive email notification on each login is defined
    {
      if (strtolower($DataSet["4"]["value"]) == "true")
      {
        EmailText($DataSet["4"]["email"],"Successful Login Notification","Your account on $ProdName was successfully logged into",$fromEmail);
      }
    }
    if(array_key_exists("5",$DataSet)) # Receive SMS notification on each login is defined
    {
      if (strtolower($DataSet["5"]["value"]) == "true")
      {
        SendUserSMS("Your account on $ProdName was successfully logged into",$iUserID);
      }
    }
  }

  if($Row['dtUpdated']=="")
  {
    $strReturn = 'myprofile.php';
  }

  $strQuery = "update tblUsers set dtLastLogin = '$dtNow' where iUserID='$iUserID'";
  if (!$dbh->query ($strQuery))
  {
    $strError = 'Database update during loginfailed. Error ('. $dbh->errno . ') ' . $dbh->error;
    $strError .= $strQuery;
    EmailText("$SupportEmail","Automatic Error Report",$strError,$fromEmail);
    error_log ($strError);
  }
  else
  {
    header("Location: " . $strReturn );
  }
?>
