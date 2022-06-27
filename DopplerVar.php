<?php
$arrSecretValues = FetchDopplerStatic("phpdev","dev");
if (array_key_exists("secrets",$arrSecretValues))
{
  $DBServerName = $arrSecretValues["secrets"]["MYSQL_HOST"]["computed"];
  $UID = $arrSecretValues["secrets"]["MYSQL_USER"]["computed"];
  $PWD = $arrSecretValues["secrets"]["MYSQL_PASSWORD"]["computed"];
  $MailUser = $arrSecretValues["secrets"]["EMAILUSER"]["computed"];
  $MailPWD = $arrSecretValues["secrets"]["EMAILPWD"]["computed"];
  $MailHost = $arrSecretValues["secrets"]["EMAILSERVER"]["computed"];
  $MailHostPort = $arrSecretValues["secrets"]["EMAILPORT"]["computed"];
  $UseSSL = $arrSecretValues["secrets"]["USESSL"]["computed"];
  $UseStartTLS = $arrSecretValues["secrets"]["USESTARTTLS"]["computed"];
}
else
{
  if (array_key_exists("messages",$arrSecretValues))
  {
    $strMsg = "There was an issue fetching the secrets: ";
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