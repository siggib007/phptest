<?php
  //print "Login Successful<br>\n";
  $_SESSION["auth_username"] = $Row['vcName'];
  $_SESSION["auth_UID"] = $Row['vcUID'];
  $_SESSION["UID"] = $iUserID;
  $_SESSION["dtLogin"] = $dtNow;
  $_SESSION["iPrivLevel"] = $iPrivlvl;
  $_SESSION["LastActivity"] = time();
  $_SESSION["LoginTime"] = $dtNow;
  if ($Row['dtUpdated']=="")
  {
    $strReturn = 'myprofile.php';
  }

  $strQuery = "update tblUsers set dtLastLogin = '$dtNow' where iUserID='$iUserID'";
  //print "<p>$strQuery</p>";
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
    //print "<p class=\"Header1\">Welcome $Row[vcName] !!</p>";
    //print "<p class=\"MainText\">You have level $iPrivlvl clerance. I need to take you back to $strReturn</p>\n";
  }
?>
