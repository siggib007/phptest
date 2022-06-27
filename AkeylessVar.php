<?php
$arrname = array();
$arrname[] = "EMAILPWD";
$arrname[] = "EMAILSERVER";
$arrname[] = "EMAILUSER";
$arrname[] = "EMAILPORT";
$arrname[] = "MYSQL_HOST";
$arrname[] = "MYSQL_PASSWORD";
$arrname[] = "MYSQL_USER";
$arrname[] = "USESSL";
$arrname[] = "USESTARTTLS";
$arrSecretValues = FetchKeylessStatic($arrname);
if (array_key_exists("error",$arrSecretValues))
{
  error_log("Failed to fetch secrets from AKEYLESS. ".$arrSecretValues["error"]);
  ShowErrHead();
}
else
{
  $DBServerName = $arrSecretValues["MYSQL_HOST"];
  $UID = $arrSecretValues["MYSQL_USER"];
  $PWD = $arrSecretValues["MYSQL_PASSWORD"];
  $MailUser = $arrSecretValues["EMAILUSER"];
  $MailPWD = $arrSecretValues["EMAILPWD"];
  $MailHost = $arrSecretValues["EMAILSERVER"];
  $MailHostPort = $arrSecretValues["EMAILPORT"];
  $UseSSL = $arrSecretValues["USESSL"];
  $UseStartTLS = $arrSecretValues["USESTARTTLS"];
}
?>