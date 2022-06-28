<?php
require_once("functions.php");

// default_charset = "utf-8";
ini_set( 'default_charset', 'UTF-8' );
set_time_limit(30);
$ROOTPATH = "/";
$HeadImg ="img/PHPDemoBanner.png";
$CSSName = "SiteStyle.css";
$ErrMsg = "We seem to be experiencing some technical difficulties, " .
          "hopefully we'll have it resolved shortly.<br>" .
          "If you have any questions please contact us at support@example.com.";

# All Environment and secret vars are specified in ExtVars.php
# Follow instruction there on how to adjust.

require("ExtVars.php");

if ($DBServerName == "" or $UID == "" or $PWD == "" or $MailUser == ""
    or $MailPWD == "" or $MailHost == "" or $MailHostPort == ""
    or $UseSSL =="" or $UseStartTLS =="")
    {
      error_log("One or more of the required email and DB creds variable are blank" );
      error_log("Make sure database connections and email server conf in DBCon.php are correct.");
      ShowErrHead();
    }

date_default_timezone_set('UTC');
$DefaultDB = "PHPDemo" ;
$strRemoteIP = $_SERVER["REMOTE_ADDR"];
$Priv = 0; // Default Privledge level is public or 0
$strHost = $_SERVER["SERVER_NAME"];
if ($_SERVER['SERVER_PORT'] != 80 and $_SERVER['SERVER_PORT'] != 443)
{
  $strHost .= ":".$_SERVER['SERVER_PORT'];
}
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

$strURL = "Localhost/";

// mysqli_set_charset("utf8");
try
{
  $dbh= new mysqli ($DBServerName, $UID, $PWD, $DefaultDB);
}
catch (Exception $e)
{
  error_log("Error while attempting to create a new mysqli client:" . $e->getMessage());
  error_log("Make sure database connections in DBCon.php are correct.");
  ShowErrHead();
}
if ($dbh->connect_errno)
{
    error_log( "Failed to connect to MySQL. Error(" . $dbh->connect_errno . ") " . $dbh->connect_error);
    error_log("Make sure database connections in DBCon.php are correct.");
    $DBError="true";
}
else
{
    // $dbh->set_charset("utf-8");
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