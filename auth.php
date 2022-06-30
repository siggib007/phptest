<?php
	require_once("DBCon.php");
	if (isset($_SESSION["auth_username"] ) )
	{
    header("Location: index.php");
    exit;
	}

	if (isset($_POST['txtDest']))
	{
    $strDest = trim($_POST['txtDest']);
	}
	else
	{
    $strDest ="";
}
	if (isset($_SERVER['HTTP_REFERER']))
	{
    $strReferer = $_SERVER['HTTP_REFERER'];
	}
	else
	{
    $strReferer = "";
	}
	$strURI = $_SERVER["REQUEST_URI"];
	$strPageNameParts = explode('/',$strURI);
	$HowMany = count($strPageNameParts);
	$LastIndex = $HowMany - 1;
	$strPageName = strtolower($strPageNameParts[$LastIndex]);
	if ($strPageName == 'auth.php')
	{
    header("Location: index.php");
    exit;
	}
	$strPageNameParts = explode('/',$strReferer);
	$HowMany = count($strPageNameParts);
	$LastIndex = $HowMany - 1;
	$strReferPage = $strPageNameParts[$LastIndex];
	if ($strReferPage=='registerconf.php')
	{
    $strReferPage = 'index.php';
	}
	if ($strReferPage=='register.php')
	{
    $strReferPage = 'index.php';
	}
	if ($strReferPage=='delete.php')
	{
    $strReferPage = 'index.php';
	}
	if ($strReferPage=='update.php')
  {
    $strReferPage = 'index.php';
	}
	if ($strReferPage=='recover.php')
	{
    $strReferPage = 'index.php';
	}
	if ($strReferPage=='')
	{
    $strReferPage = 'index.php';
	}
	if ($strDest != '')
	{
    $strReturn = $strDest;
	}
	else if ($strPageName != 'login.php')
	{
    $strReturn = $strPageName;
	}
	else if ($strReferPage != 'login.php')
	{
    $strReturn = $strReferPage;
	}
	else
	{
    $strReturn = 'index.php';
	}
	//error_log ("returned page determined to be $strReturn");
	if (isset($_SESSION["ReturnPage"]))
	{
    $strReturn = $_SESSION["ReturnPage"];
    unset($_SESSION["ReturnPage"]);
    //error_log("Got return page from session as: $strReturn");
	}

	//print "strReferer: $strReferPage<br>\nstrScriptName: $strPageName<br>\nstrReturn: $strReturn<br>\n";
  //Log_Session("starting login analysis in auth.php");
	if (isset($_POST['txtLogin']))
	{
    $strLogin = CleanReg(trim($_POST['txtLogin']));
	}
	else
	{
    $strLogin ="";
	}
	if (isset($_POST['txtPwd']))
	{
    $strPWD = CleanReg(trim($_POST['txtPwd']));
	}
	else
	{
    $strPWD ="";
	}

	$dtNow = date("Y-m-d H:i:s");
	$salt = substr($strLogin,0,4);
	if ($strLogin and $strPWD)
	{
    $strQuery = "select * from tblUsers where vcUID = '$strLogin'";
    if (!$Result = $dbh->query ($strQuery))
    {
      error_log ('Failed to fetch initial user data. Error ('. $dbh->errno . ') ' . $dbh->error);
      error_log ($strQuery);
      exit(2);
    }
    $rowcount=mysqli_num_rows($Result);
    if ($rowcount > 0)
    {
      $Row = $Result->fetch_assoc();
      $iUserID = $Row['iUserID'];
      $iPrivlvl = $Row['iPrivLevel'];
      $strUPWD = $Row['vcPWD'];
    }
    else
    {
      error_log("query of $strQuery returned no rows");
      $iUserID = 0;
      $iPrivlvl = 0;
      $strUPWD = "";
    }
    $strEPWD = crypt($strPWD,$salt);
//		print "Correct password:$strUPWD<br>\nYou entered:$strPWD which encrypts to $strEPWD using a salt of $salt<br>\n";
    if ($strUPWD==$strEPWD)
    {
      //print "Login Successful<br>\n";
      $_SESSION["auth_username"] = $Row['vcName'];
      $_SESSION["auth_UID"] = $Row['vcUID'];
      $_SESSION["UID"] = $iUserID;
      $_SESSION["dtLogin"] = $dtNow;
      $_SESSION["iPrivLevel"] = $iPrivlvl;
      $_SESSION["LastActivity"] = time();
      $_SESSION["LoginTime"] = $dtNow;
      if ($Row['dtUpdated']=="")
      {
        $strReturn = 'myprofile.php';
      }

      $strQuery = "update tblUsers set dtLastLogin = '$dtNow' where iUserID='$iUserID'";
      //print "<p>$strQuery</p>";
      if (!$dbh->query ($strQuery))
      {
        $strError = 'Database update during loginfailed. Error ('. $dbh->errno . ') ' . $dbh->error;
        $strError .= $strQuery;
        EmailText("$SupportEmail","Automatic Error Report",$strError,$fromEmail);
        error_log ($strError);
      }
      else
      {
        header("Location: " . $strReturn );
        //print "<p class=\"Header1\">Welcome $Row[vcName] !!</p>";
        //print "<p class=\"MainText\">You have level $iPrivlvl clerance. I need to take you back to $strReturn</p>\n";
      }
    }
    else
    {
      require_once("header.php");
      print "<p class=\"Attn\">Invalid username or password</p>";
      require("LoginIncl.php");
      require_once("footer.php");
    }
	}
	else
	{
    if ($strLogin and !$strPWD)
    {
        print "<p class=\"Attn\">You provided your username but not your password. Please provide both</p>";
    }
    else if (!$strLogin and $strPWD)
    {
        print "<p class=\"Attn\">You provided your password but not your username. Please provide both</p>";
    }
    //Log_Session("including header.php in auth.php");
    require_once("header.php");
    if (isset($_SESSION["Reason"]))
    {
        print "<p class=Header1>$_SESSION[Reason]</p>\n";
        unset($_SESSION["Reason"]);
    }
    require_once("LoginIncl.php");
    require_once("footer.php");
    //print "strReferer: $strReferer<br>\nstrScriptName: $strScriptName<br>\n";
    //Log_BackTrace (debug_backtrace(),"at end of auth.php");
    //Log_Session("at end of auth.php");
	}
?>