<?php
  //print "Login Successful<br>\n";
  $_SESSION["auth_username"] = $Row['vcName'];
  $_SESSION["auth_UID"] = $Row['vcUID'];
  $_SESSION["UID"] = $iUserID;
  $_SESSION["dtLogin"] = $dtNow;
  $_SESSION["iPrivLevel"] = $iPrivlvl;
  $_SESSION["LastActivity"] = time();
  $_SESSION["LoginTime"] = $dtNow;

  $strActivity = "Login";
  $arrTypes = array("SMS"=>"4","email"=>"5");

  NotifyActivity ($strActivity,$arrTypes);

  if($strLastUpdated=="")
  {
    $strReturn = 'myprofile.php';
  }

  $strQuery = "update tblUsers set dtLastLogin = '$dtNow' where iUserID='$iUserID'";
  if (!$dbh->query ($strQuery))
  {
    $strError = 'Database update during loginfailed. Error ('. $dbh->errno . ') ' . $dbh->error;
    $strError .= $strQuery;
    EmailText("$SupportEmail","Automatic Error Report",$strError,$FromEmail);
    error_log ($strError);
  }
  else
  {
    header("Location: " . $strReturn );
  }
?>
