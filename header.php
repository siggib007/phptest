<?php
require_once("DBCon.php");
if (isset($_SERVER['HTTPS'])and $strSecOpt =="prevent")
{
    $strUnSecure = "http://$strHost$strScriptName";
    header("Location: $strUnSecure");
}

// if (isset($_SERVER['HTTPS'])and $strHost !="www.studio-b-dance.com" and $SiteType != "a")
// {
//     switch ($strHost) {
//         case "seattlestudiobdance.com":
//         case "www.seattlestudiobdance.com":
//         case "studiobdanceseattle.com":
//         case "www.studiobdanceseattle.com":
//         case "studio-b-dance.com":
//         case "beta.studio-b-dance.com":
//             $strRedir = "https://www.studio-b-dance.com$strScriptName";
//             break;
//         default:
//             $strRedir = "http://$strHost$strScriptName";
//             break;
//     }
//     header("Location: $strRedir");
// }
$iMenuID=0;
$iLastSlash = strrpos($strURI, "/");
$strPagePath = substr($strURI, 0,$iLastSlash+1);
$strPageNameParts = explode('/',$strURI);
$FirstPart = "/$strPageNameParts[1]/";
$iSubOfID = 0;
if ($FirstPart != $ROOTPATH)
{
    $ROOTPATH = "/";
}
$strURL = "http://" . $strHost . $ROOTPATH;
$CSSName = $ROOTPATH . $CSSName;

$HowMany = count($strPageNameParts);

$LastIndex = $HowMany - 1;
$strPageArgs = explode('?',$strPageNameParts[$LastIndex]);
$strPageName = $strPageArgs[0];
if ($strPageName =="" and $strURI == $ROOTPATH)
{
    $strPageName = 'index.php';
}
if (($strPageName =="" or $strPageName =="index.php") and $strPagePath != $ROOTPATH)
{
    $LastIndex = $HowMany - 2;
    $strPageArgs = explode('?',$strPageNameParts[$LastIndex]);
    $strPageName = $strPageArgs[0];
}
if ($DBError == "true")
{
    ShowErrHead();
}

$strQuery = "SELECT * FROM tblmenu WHERE vcLink = '$strPageName' LIMIT 1;";
if (!$Result = $dbh->query ($strQuery))
{
    error_log ('Failed to fetch Link Detail data. Error ('. $dbh->errno . ') ' . $dbh->error);
    error_log ($strQuery);
    ShowErrHead();
}
$Row = $Result->fetch_assoc();
$PrivReq = $Row['iReadPriv'];
$iMenuID = $Row['iMenuID'];
$iAdminCat = $Row['bAdmin'];

if (!isset($_SERVER['HTTPS'])and $strSecOpt =="force" and $Row['bSecure'] == 1)
{
    switch ($strHost)
    {
        case "seattlestudiobdance.com":
        case "studiobdanceseattle.com":
            $strSecure = "https://www.studio-b-dance.com$strScriptName";
            break;
        default :
            $strSecure = "https://$strHost$strScriptName";
            break;
    }
    header("Location: $strSecure");
}

if ($PrivReq == '')
{
    $PrivReq = 0;
}
$WritePriv = $Row['iWritePriv'];
if ($WritePriv == '')
{
    $WritePriv = 0;
}
$strHeader = "$HeadAdd " . $Row['vcHeader'];

if ($iMenuID)
{
    $strQuery = "SELECT iSubOfMenu FROM tblmenutype WHERE iMenuID = '$iMenuID' LIMIT 1;";
    if (!$Result = $dbh->query ($strQuery))
    {
        error_log ('Failed to fetch Link Detail data. Error ('. $dbh->errno . ') ' . $dbh->error);
        error_log ($strQuery);
        ShowErrHead();
    }
    $Row = $Result->fetch_assoc();
    $iSubOfID = $Row['iSubOfMenu'];
}

if ($iSubOfID > 0)
{
    $strQuery = "SELECT * FROM vwmenuitem WHERE iMenuID = '$iSubOfID' LIMIT 1;";
    if (!$Result = $dbh->query ($strQuery))
    {
        error_log ('Failed to fetch Link Detail data. Error ('. $dbh->errno . ') ' . $dbh->error);
        error_log ($strQuery);
        ShowErrHead();
    }
    $Row = $Result->fetch_assoc();
    $strSubOfLink = $Row['vcLink'];
}
else
{
    $strSubOfLink = "";
}

$LogoutReason ="";

if (isset($_SESSION["LastActivity"] ) )
{
    if (($_SESSION["LastActivity"] + $Timeout) > time())
    {
            $_SESSION["LastActivity"] = time();
    }
    else
    {
            require("KillSession.php");
            $LogoutReason = "Logged out due to inactivity.  ";
    }
}
if (isset($_SESSION["UID"] ) )
{
    $iUserID = $_SESSION["UID"];
    $strQuery = "SELECT dtLastLogin, iPrivLevel FROM tblUsers WHERE iUserID=$iUserID LIMIT 1;";
    if (!$Result = $dbh->query ($strQuery))
    {
        error_log ('Failed to fetch user data. Error ('. $dbh->errno . ') ' . $dbh->error);
        error_log ($strQuery);
        ShowErrHead();
    }
    $Row = $Result->fetch_assoc();

    $dtLastLogin = $Row['dtLastLogin'];
    $dbiPrivLevel = $Row['iPrivLevel'];
    if (($_SESSION["iPrivLevel"] != $dbiPrivLevel) or ($_SESSION["LoginTime"] != $dtLastLogin))
    {
        require("KillSession.php");
        $LogoutReason = "Logged out due to invalid session.  ";
    }
}

if ((! isset($_SESSION["UID"] ) ) and $LogoutReason != "")
{
    $_SESSION["LogoutReason"] = $LogoutReason;
    $_SESSION["LogoutReasonCount"] = 1;
}

if (isset($_SESSION["LogoutReason"]) and $LogoutReason == "")
{
    $LogoutReason = $_SESSION["LogoutReason"];
    $_SESSION["LogoutReasonCount"] += 1;
}

if (isset($_SESSION["LogoutReasonCount"]))
{
    if ($_SESSION["LogoutReasonCount"] > 4)
    {
        unset ($_SESSION["LogoutReason"]);
        unset ($_SESSION["LogoutReasonCount"]);
    }
}

if ( ! isset($_SESSION["auth_username"] ) )
{
    $Priv = 0;
}
else
{
    $Priv = $_SESSION["iPrivLevel"];
    $UsersName = $_SESSION["auth_username"];
    $vcUID = $_SESSION["auth_UID"];
}

if ($PrivReq > 0)
{
    if ( ! isset($_SESSION["auth_username"] ))
    {
        $_SESSION["ReturnPage"] = $strPageName;
        $_SESSION["Reason"] = "This is a secure page please login";
        $LoginPage = $ROOTPATH . "Login.php";
        header("Location: $LoginPage" );
    }
}
print "<!DOCTYPE HTML PUBLIC \"-//W3C//DTD HTML 4.01 Transitional//EN\"\n\"http://www.w3.org/TR/html4/loose.dtd\">\n";
print "<html>\n";
print "<head>\n";
print "<title>$strHeader</title>\n";
$strQuery = "SELECT * FROM tblPageMeta WHERE iMenuID is null or iMenuID = '$iMenuID';";
if (!$MetaResult = $dbh->query ($strQuery))
{
    error_log ('Failed to fetch Meta data. Error ('. $dbh->errno . ') ' . $dbh->error);
    error_log ($strQuery);
    ShowErrHead();
}

while ($MetaRow = $MetaResult->fetch_assoc())
{
    $AttrName = $MetaRow['vcAttrName'];
    $MetaName = $MetaRow['vcMetaName'];
    $MetaValue = $MetaRow['vcMetaValue'];
    print "<meta $AttrName=\"$MetaName\" content=\"$MetaValue\" />\n";
}
print "<link rel=\"shortcut icon\" type=\"image/x-icon\" href=\"/favicon.ico\">\n";
print "<link href=\"$CSSName\" rel=\"stylesheet\" type=\"text/css\">\n";
print "</head>\n";
print "<body>\n";
include_once("analyticstracking.php"); // Include Google Anaytics in each page
print "<!-- Form a border for the page -->\n";
print "<div id=\"left\"></div>\n";
print "<div id=\"right\"></div>\n";
print "<div id=\"top\"></div>\n";
print "<div id=\"bottom\"></div>\n";
print "<!-- End border start div for the header -->\n";
print "<div class=\"BlacktblHead\">\n";
if ($strSiteLabel <> "")
{
    print "<center><span class=SiteLabel>$strSiteLabel</span></center>\n";
}
print "<table width=\"100%\">\n<tr>\n";
if ( ! isset($_SESSION["auth_username"] ) )
{
    print "<td width=\"80%\"><span class=Attn>$LogoutReason</span></td>\n";
    print "<td class=\"login\"><a class=\"login\" href=\"" . $ROOTPATH . "register.php\">New Account</a></td>\n";
    print "<td  class=\"login\"><a class=\"login\" href=\"" . $ROOTPATH . "Login.php\">Login</a></td>\n";
    $Priv = 0;
}
else
{
    $Priv = $_SESSION["iPrivLevel"];
    print "<td class=\"Welcome\" width=\"85%\">Welcome $_SESSION[auth_username]</td>\n";
    print "<td class=\"Profile\"><a class=\"Profile\" href=\"" . $ROOTPATH . "myprofile.php\">My Profile</a></td>\n";
    print "<td class=\"Profile\"><a class=\"Profile\" href=\"" . $ROOTPATH . "logout.php\">Logout</a></td>\n";
}
//print "</tr>\n<tr>\n<td class=\"Profile\" colspan=\"3\">\n";
print "<a class=\"NLSignupHead\" href=\"http://visitor.r20.constantcontact.com/d.jsp?llr=9cbtlfmab&p=oi&m=1112576459866&sit=nssxjouhb&f=d96756c3-ff64-4fe7-854f-58e67d4fdc0a\" target=\"_blank\">\n";
print "Sign Up For Our <BR> Dance Newsletter\n";
print "</a>\n";
//print "</td>\n</tr>\n";
print "</tr>\n</table>\n";
if($strSubOfLink=="")
{
    $strSubOfLink=$strPageName;
}
$strQuery = "SELECT * FROM vwmenuitem where vcMenuType = 'head' and iReadPriv <= $Priv and iSubOfMenu = 0 order by iMenuOrder";
if (!$Result = $dbh->query ($strQuery))
{
    error_log ('Failed to fetch menu data. Error ('. $dbh->errno . ') ' . $dbh->error);
    error_log ($strQuery);
    ShowErrHead();
}
$MenuNum = $Result->num_rows;

print"<div class=\"Header\" align=\"center\">\n";

$imgname = $ROOTPATH . $HeadImg;
print "<table border=\"0\" cellPadding=\"4\" cellSpacing=\"0\">\n";
print "<tr>\n";
print "<td colspan=$MenuNum align=\"center\" vAlign=\"middle\">\n";
print "<img border=\"0\" src=\"$imgname\" align=\"center\" height=\"$ImgHeight\">\n";
print "</td>\n";
print "</tr>\n";
if ($Maintenance == "true")
{
    print "<tr class=\"header1\">\n";
    print "<td colspan=$MenuNum align=\"center\" vAlign=\"middle\">\n";
    print "</td>\n</tr>\n</table>\n</div>\n</div>\n";
    print "<p class=\"Header1\">Under maintenance</p>\n";
    $ImgFileName = array(
                    "CautionSign.png" => "Under Construction",
                    "Construction2.png" => "Under Construction",
                    "ConstructionHat.png" => "Under Construction",
                    "ConstructionSign.png" => "Under Construction",
                    "ConstuctionBarricade.png" => "Under Construction",
                    "HammerWrenchSign.png" => "Under Construction",
                    "WMSmileyConstruction.gif" => "Under Construction"
                    );
    print "<center>";
    $ImgFolder = $ROOTPATH . "img/";
    foreach($ImgFileName as $value => $tag)
      {
            $Fullimgname = $ImgFolder . $value;
            print "<img src=\"$Fullimgname\" height=\"$ImgHeight\" alt=\"$tag\"/>";
      }
    print "</center>";
    print "<p class=\"Attn\" align=center>We are currently updating the website, " .
            "please try again in 10 minutes. We appologize for inconvenience</p>\n";
    print "</TR>\n";
    exit;
}
print "</table>\n";
print "<ul class=\"nav\">\n";
while ($Row = $Result->fetch_assoc())
{
    $key = str_replace(" ", "&nbsp;", $Row['vcTitle']);
    $value = $Row['vcLink'];
    $FileName = $ROOTPATH . $value;
    if ($Row['bNewWindow'] == 1)
    {
        $target = "_blank";
    }
    else
    {
        $target = "_self";
    }
    if ($strURI == $ROOTPATH and $key == "Home")
    {
        print "<li class=\"HL\"><a href=\"$FileName\" target=\"$target\">Home</a></li>\n";
    }
    elseif ($strSubOfLink == $value)
    {
        print "<li class=\"HL\"><a href=\"$FileName\" target=\"$target\">$key</a></li>\n";
    }
    elseif ($value == "admin.php" and $iAdminCat > 0)
    {
        print "<li class=\"HL\"><a href=\"$FileName\" target=\"$target\">$key</a></li>\n";
    }
    else
    {
        print "<li class=\"Norm\"><a href=\"$FileName\" target=\"$target\">$key</a></li>\n";
    }
}
print "</ul>\n";
print "</div>\n</div>\n";

if ($iMenuID)
{
    if ($iSubOfID == 0)
    {
        $iSubOfID = $iMenuID;
    }
    $strQuery = "SELECT * FROM vwmenuitem where vcMenuType = 'head' and iReadPriv <= $Priv and iSubOfMenu=$iSubOfID order by iMenuOrder";
    if (!$Result = $dbh->query ($strQuery))
    {
        error_log ('Failed to fetch menu data. Error ('. $dbh->errno . ') ' . $dbh->error);
        error_log ($strQuery);
        print "<p class=\"Attn\" align=center>$ErrMsg</p>\n";
        exit(2);
    }
    $MenuNum = $Result->num_rows;
    if ($MenuNum>0)
    {
        print "<div class=\"SubHead\">";
        print "<ul class=\"nav\">\n";
        while ($Row = $Result->fetch_assoc())
        {
            $key = str_replace(" ", "&nbsp;", $Row['vcTitle']);
            $value = $Row['vcLink'];
            $FileName = $ROOTPATH . $value;
            if ($Row['bNewWindow'] == 1)
            {
                $target = "_blank";
            }
            else
            {
                $target = "_self";
            }
            if ($strPageName == $value)
            {
                print "<li class=\"HL\"><a href=\"$FileName\" target=\"$target\">$key</a></li>\n";
            }
            else
            {
                print "<li class=\"Norm\"><a href=\"$FileName\" target=\"$target\">$key</a></li>\n";
            }
        }
        print "</ul>\n";
        print "</div>\n";
    }
}

if ($ShowAdminSub == "True")
{
    if ($strPageName == "admin.php" or $iAdminCat > 0)
    {
        if (isset($_GET['cat']))
        {
            $strCatID = $_GET['cat'];
        }
        else
        {
            $strCatID=0;
        }
        If ($iAdminCat>0)
        {
            $strCatID=$iAdminCat;
        }
        else
        {
            $iAdminCat=$strCatID;
        }
        $strQuery = "SELECT * FROM tblAdminCategories WHERE iCatID > 0 order by vcCatName";
        if (!$Result = $dbh->query ($strQuery))
        {
            error_log ('Failed to fetch menu data. Error ('. $dbh->errno . ') ' . $dbh->error);
            error_log ($strQuery);
            print "<p class=\"Attn\" align=center>$ErrMsg</p>\n";
            exit(2);
        }
        $MenuNum = $Result->num_rows;
        print "<div class=\"SubHead\">";
        print "<ul class=\"nav\">\n";
        while ($Row = $Result->fetch_assoc())
        {
            $key = str_replace(" ", "&nbsp;", $Row['vcCatName']);
            $value = $Row['iCatID'];
            $FileName = "admin.php?cat=$value";
            if ($strCatID == $value)
            {
                print "<li class=\"HL\"><a href=\"$FileName\">$key</a></li>\n";
            }
            else
            {
                print "<li class=\"Norm\"><a href=\"$FileName\">$key</a></li>\n";
            }
        }
        print "</ul>\n";
        print "</div>\n";
    }
    if ($iAdminCat > 0)
    {
        $strQuery = "SELECT * FROM tblmenu where bAdmin = '$iAdminCat' and iReadPriv <= $Priv order by vcTitle";
        if (!$Result = $dbh->query ($strQuery))
        {
            error_log ('Failed to fetch menu data. Error ('. $dbh->errno . ') ' . $dbh->error);
            error_log ($strQuery);
            print "<p class=\"Attn\" align=center>$ErrMsg</p>\n";
            exit(2);
        }
        $MenuNum = $Result->num_rows;
        print "<div class=\"SubHead2\">";
        print "<ul class=\"nav\">\n";
        while ($Row = $Result->fetch_assoc())
        {
            $key = str_replace(" ", "&nbsp;", $Row['vcTitle']);
            $value = $Row['vcLink'];
            $FileName = $ROOTPATH . $value;
            if ($Row['bNewWindow'] == 1)
            {
                $target = "_blank";
            }
            else
            {
                $target = "_self";
            }
            if ($strPageName == $value)
            {
                print "<li class=\"HL\"><a href=\"$FileName\" target=\"$target\">$key</a></li>\n";
            }
            else
            {
                print "<li class=\"Norm\"><a href=\"$FileName\" target=\"$target\">$key</a></li>\n";
            }
        }
        print "</ul>\n";
        print "</div>\n";
    }
}
if ($PrivReq >  $Priv)
{
    print "<p class=\"Error\">You do not have sufficient priviledge to access this page</p>";
    exit;
}
?>