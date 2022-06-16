<?php
  $UID = getenv("MYSQL_USER");
  $DBServerName = getenv("MYSQL_HOST");
  $PWD = getenv("MYSQL_PASSWORD");  
  $DefaultDB = "VMdb" ;
  $MailHost = getenv("EMAILSERVER");
  $MailHostPort = getenv("EMAILPORT");
  $MailUser = getenv("EMAILUSER");  
  $MailPWD = getenv("EMAILPWD");
  $UseSSL = getenv("USESSL");
  $UseStartTLS = getenv("USESTARTTLS");
  $strRemoteIP = $_SERVER["REMOTE_ADDR"];
  $dbh= new mysqli ($DBServerName, $UID, $PWD, $DefaultDB);
  if ($dbh->connect_errno)
  {
      error_log( "Failed to connect to MySQL. Error(" . $dbh->connect_errno . ") " . $dbh->connect_error);
      exit;
  }
  if (isset($_POST['btnSubmit']))
  {
    $btnSubmit = $_POST['btnSubmit'];
  }
  else
  {
    $btnSubmit = "";
  }
?>

