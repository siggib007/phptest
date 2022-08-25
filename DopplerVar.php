<?php
$arrSecretValues = FetchDopplerStatic($DopplerProj,$DopplerConf);
if (array_key_exists("secrets",$arrSecretValues))
{
  $DBServerName = $arrSecretValues["secrets"]["MYSQL_HOST"]["computed"];
  $DefaultDB = $arrSecretValues["secrets"]["MYSQL_DB"]["computed"];
  $UID = $arrSecretValues["secrets"]["MYSQL_USER"]["computed"];
  $PWD = $arrSecretValues["secrets"]["MYSQL_PASSWORD"]["computed"];
  $MailUser = $arrSecretValues["secrets"]["EMAILUSER"]["computed"];
  $MailPWD = $arrSecretValues["secrets"]["EMAILPWD"]["computed"];
  $MailHost = $arrSecretValues["secrets"]["EMAILSERVER"]["computed"];
  $MailHostPort = $arrSecretValues["secrets"]["EMAILPORT"]["computed"];
  $UseSSL = $arrSecretValues["secrets"]["USESSL"]["computed"];
  $UseStartTLS = $arrSecretValues["secrets"]["USESTARTTLS"]["computed"];
  $TwilioToken = $arrSecretValues["secrets"]["TWILIO_KEY"]["computed"];
  $FromNumber = $arrSecretValues["secrets"]["TWILIO_NUM"]["computed"];
  $TwilioSID = $arrSecretValues["secrets"]["TWILIO_SID"]["computed"];
  $DevEnvironment = $arrSecretValues["secrets"]["DEVENV"]["computed"];
}
else
{
  if (array_key_exists("messages",$arrSecretValues))
  {
    $AccessKey = getenv("DOPPLERKEY");
    $strMsg = "There was an issue fetching the secrets from $DopplerProj - $DopplerConf. Key starts with '" . substr($AccessKey,0,12) ."'";
    foreach ($arrSecretValues["messages"] as $msg)
    {
      $strMsg .= "$msg. ";
    }
    error_log($strMsg);
    ShowErrHead();
  }
  else
  {
    Log_Array($arrSecretValues,"Unexpected reponse from FetchDopplerStatic");
    ShowErrHead();
  }
}
?>