<?php
$UID = getenv("MYSQL_USER") ;
$DBServerName = getenv("MYSQL_HOST") ;
$PWD = getenv("MYSQL_PASSWORD") ;
$DefaultDB = getenv("MYSQL_DB") ;

$strRemoteIP = $_SERVER["REMOTE_ADDR"];
$dbh= new mysqli ($DBServerName, $UID, $PWD, $DefaultDB);
if ($dbh->connect_errno)
{
    error_log( "Failed to connect to MySQL. Error(" . $dbh->connect_errno . ") " . $dbh->connect_error);
    exit;
}
?>

