<?php
$UID = "MainPage";
$PWD = "Xe24bjacb9VvPQBw";
$DefaultDB = "mainpage";
$DBServerName = "localhost";
$strRemoteIP = $_SERVER["REMOTE_ADDR"];
$dbh= new mysqli ($DBServerName, $UID, $PWD, $DefaultDB);
if ($dbh->connect_errno)
{
    error_log( "Failed to connect to MySQL. Error(" . $dbh->connect_errno . ") " . $dbh->connect_error);
    exit;
}
?>

