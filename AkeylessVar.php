<?php
$arrname = array();
$arrname[] = "EMAILPWD";
$arrname[] = "EMAILSERVER";
$arrname[] = "EMAILUSER";
$arrname[] = "EMAILPORT";
$arrname[] = "USESSL";
$arrname[] = "USESTARTTLS";
$arrname[] = "MYSQL_HOST";
$arrname[] = "MYSQL_DB";
$arrname[] = "MYSQL_USER";
$arrname[] = "MYSQL_PASSWORD";
$arrname[] = "TWILIO_KEY";
$arrname[] = "TWILIO_NUM";
$arrname[] = "TWILIO_SID";
$arrname[] = "DEVENV";

$arrSecretValues = FetchKeylessStatic($arrname);
if (array_key_exists("error",$arrSecretValues))
{
  error_log("Failed to fetch secrets from AKEYLESS. ".$arrSecretValues["error"]);
  ShowErrHead();
}
else
{
  $DBServerName = $arrSecretValues["MYSQL_HOST"];
  $DefaultDB = $arrSecretValues["MYSQL_DB"];
  $UID = $arrSecretValues["MYSQL_USER"];
  $PWD = $arrSecretValues["MYSQL_PASSWORD"];
  $MailUser = $arrSecretValues["EMAILUSER"];
  $MailPWD = $arrSecretValues["EMAILPWD"];
  $MailHost = $arrSecretValues["EMAILSERVER"];
  $MailHostPort = $arrSecretValues["EMAILPORT"];
  $UseSSL = $arrSecretValues["USESSL"];
  $UseStartTLS = $arrSecretValues["USESTARTTLS"];
  $TwilioSID = $arrSecretValues["TWILIO_SID"];
  $FromNumber = $arrSecretValues["TWILIO_NUM"];
  $TwilioToken = $arrSecretValues["TWILIO_KEY"];
  $DevEnvironment = $arrSecretValues["DEVENV"];
}
?>
