<?php
require_once("functions.php");

$ROOTPATH = "/";
$HeadImg ="img/PHPDemoBanner.png";
$CSSName = "SiteStyle.css";
$ErrMsg = "We seem to be experiencing some technical difficulties, " .
          "hopefully we'll have it resolved shortly.<br>" .
          "If you have any questions please contact us at support@example.com.";


// Decide How you want to keep and access environment variables and secrets

// uncomment this line if you want to store it all in a special secrets file
// make sure you gitignore this file
require("secrets.php");

// If you rather store everything in evironment variables uncomment this block
/* $DBServerName = getenv("MYSQL_HOST");
$UID = getenv("MYSQL_USER");
$PWD = getenv("MYSQL_PASSWORD");
$MailUser = getenv("EMAILUSER");
$MailPWD = getenv("EMAILPWD");
$MailHost = getenv("EMAILSERVER");
$MailHostPort = getenv("EMAILPORT");
$UseSSL = getenv("USESSL");
$UseStartTLS = getenv("USESTARTTLS"); */


// The recommended approach is to store everything in Doppler
// See https://infosechelp.net/secrets-management/ for how to get started with Doppler
// If you don't want to use Doppler comment this block out and uncomment one of the above ones.
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
# end Fetching Doppler secrets

date_default_timezone_set('UTC');
$DefaultDB = "PHPDemo" ;
$strRemoteIP = $_SERVER["REMOTE_ADDR"];
$Priv = 0; // Default Privledge level is public or 0
$strHost = $_SERVER["SERVER_NAME"];
$strScriptName = $_SERVER["SCRIPT_NAME"];
$gFileName = __FILE__;
$strURI = $_SERVER["REQUEST_URI"];
$HeadAdd = "";
$strSiteLabel = "";
$DBError = "false";
$strHostNameParts = explode('.',$strHost);
$HostnamePartCount = count($strHostNameParts);
$OSEnv = "not used";

if ($HostnamePartCount == 1)
{
	$SiteType = "a";
}
else
{
	$SiteType = $strHostNameParts[0];
}

//switch ($SiteType)
//{
//    case "192":
//    case "a":
//    case "b":
//        $DefaultDB = "siggib_studiob";
//        $ROOTPATH = "/studiob/";
//        $DBServerName = "localhost";
//        break;
//    case "beta":
//        $DefaultDB = "studiob_beta";
//        $ROOTPATH = "/";
//        break;
//    default:
//        //$DefaultDB = "studiob";
//        $ROOTPATH = "/";
//        break;
//}
//$strURL = "http://" . $strHost . $ROOTPATH;
$strURL = "Localhost/";


try
{
  $dbh= new mysqli ($DBServerName, $UID, $PWD, $DefaultDB);
}
catch (Exception $e)
{
  error_log("Error while attempting to create a new mysqli client:" . $e->getMessage());
  error_log($e->getFile() . " line " . $e->getLine());
  Log_BackTrace($e->getTrace(), "Here is the backtrace");
  ShowErrHead();
}
if ($dbh->connect_errno)
{
    error_log( "Failed to connect to MySQL. Error(" . $dbh->connect_errno . ") " . $dbh->connect_error);
    $DBError="true";
}
else
{
    $strQuery = "SELECT * FROM tblconf";
    if (!$Result = $dbh->query ($strQuery))
    {
            error_log ('Failed to fetch Configuration data. Error ('. $dbh->errno . ') ' . $dbh->error);
            error_log ($strQuery);
            $DBError="true";
    }
    else
    {
        while ($Row = $Result->fetch_assoc())
        {
            $ConfArray[$Row['vcValueName']] = $Row['vcValue'];
            switch ($Row['vcValueName'])
            {
                case "SupportEmail":
                        $SupportEmail = $Row['vcValue'];
                        break;
                case "HeadKeyLen":
                        $HKeyLen = $Row['vcValue'];
                        break;
                case "FootKeyLen":
                        $FKeyLen = $Row['vcValue'];
                        break;
                case "ImgHeight":
                        $ImgHeight = $Row['vcValue'];
                        break;
                case "Owner":
                        $Owner = $Row['vcValue'];
                        break;
                case "Address1":
                        $Address1 = $Row['vcValue'];
                        break;
                case "Address2":
                        $Address2 = $Row['vcValue'];
                        break;
                case "Phone":
                        $Phone = $Row['vcValue'];
                        break;
                case "ProfileNotify":
                        $ProfileNotify = $Row['vcValue'];
                        break;
                case "Copyright":
                        $Copyright = $Row['vcValue'];
                        break;
                case "Maintenance":
                        $Maintenance = $Row['vcValue'];
                        break;
                case "EmailFromAddr":
                        $eFromAddr = $Row['vcValue'];
                        break;
                case "EmailFromName":
                        $eFromName = $Row['vcValue'];
                        break;
                case "ShowLinkURL":
                        $ShowLinkURL = $Row['vcValue'];
                        break;
                case "SiteMessage":
                        $strSiteLabel = $Row['vcValue'];
                        break;
                case "HeadAdd":
                        $HeadAdd = $Row['vcValue'];
                        break;
                case "DefNumWeeks":
                        $DefNumWeeks = $Row['vcValue'];
                        break;
                case "DefClassLen":
                        $DefClassLen = $Row['vcValue'];
                        break;
                case "defClassPrice":
                        $DefClassPrice = $Row['vcValue'];
                        break;
               case "ShowLast":
                        $ShowLastClass = $Row['vcValue'];
                        break;
               case "ClassDur":
                        $ClassDuration = $Row['vcValue'];
                        break;
               case "CTUnit":
                        $CTUnit = $Row['vcValue'];
                        break;
               case "TimeFormat":
                        $strTimeFormat = $Row['vcValue'];
                        break;
               case "DateFormat":
                        $strDateFormat = $Row['vcValue'];
                        break;
               case "PDFBase":
                        $strPDFBaseName = $Row['vcValue'];
                        break;
               case "DefMaxStudent":
                        $DefMaxStudent = $Row['vcValue'];
                        break;
               case "minRegLevel":
                        $minRegLevel = $Row['vcValue'];
                        break;
               case "SecureOpt":
                        $strSecOpt = $Row['vcValue'];
                        break;
               case "NumAdminCol":
                        $iNumCol = $Row['vcValue'];
                        break;
               case "ShowAdminSub":
                        $ShowAdminSub = $Row['vcValue'];
                        break;
               case "UserTimeout":
                        $Timeout = $Row['vcValue'] * 60;
                        break;
               case "NewPWDLen":
                        $PWDLength = $Row['vcValue'];
                        break;
            }
        }
    }
//     Log_Array($ConfArray,"Dumping ConfArray");
    $FromEmail = "From:$eFromName <$eFromAddr>";
    if (!isset($_SESSION))
    {
        session_start();
    }

    if (isset($_SERVER['HTTP_REFERER']))
    {
        $strReferer = $_SERVER['HTTP_REFERER'];
    }
    else
    {
        $strReferer = "";
    }
    if (isset($_SERVER['HTTPS']))
    {
        $strProto = "https://";
    }
    else
    {
        $strProto = "http://";
    }
    $strPageURL = $strProto . $strHost . $strURI;
    $PostVarCount = count($_POST);
    $dtNow = date("Y-m-d H:i:s");
}
?>