<?php
  //Copyright © 2009,2015,2022  Siggi Bjarnason.
  //
  //This program is free software: you can redistribute it and/or modify
  //it under the terms of the GNU General Public License as published by
  //the Free Software Foundation, either version 3 of the License, or
  //(at your option) any later version.
  //
  //This program is distributed in the hope that it will be useful,
  //but WITHOUT ANY WARRANTY; without even the implied warranty of
  //MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
  //GNU General Public License for more details.
  //
  //You should have received a copy of the GNU General Public License
  //along with this program.  If not, see <http://www.gnu.org/licenses/>

	require_once("DBCon.php");
  use RobThree\Auth\TwoFactorAuth;
  spl_autoload_register(
    function ($className)
    {
      include_once str_replace(array('RobThree\\Auth', '\\'), array(__DIR__.'/RobThree2FA', '/'), $className) . '.php';
    }
  );

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
	if (isset($_SESSION["ReturnPage"]))
	{
    $strReturn = $_SESSION["ReturnPage"];
    unset($_SESSION["ReturnPage"]);
	}

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

  if (isset($_POST['txtCode']))
	{
    $strCode = CleanReg(trim($_POST['txtCode']));
	}
	else
	{
    $strCode ="";
	}
  if (isset($_POST['cmbMFA']))
	{
    $strMFAType = CleanReg(trim($_POST['cmbMFA']));
	}
	else
	{
    $strMFAType ="na";
	}
	$dtNow = date("Y-m-d H:i:s");
  $tfa = new TwoFactorAuth();
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
      $strTOTP = $Row['vcMFASecret'];
      $strRHash = $Row['vcRecovery'];
      $strLastUpdated = $Row['dtUpdated'];
      $strEmail = $Row['vcEmail'];
    }
    else
    {
      error_log("query of $strQuery returned no rows");
      $iUserID = 0;
      $iPrivlvl = 0;
      $strUPWD = "";
      $strTOTP = "";
      $strRHash = "";
      $strLastUpdated = "";
      $strEmail = "";
    }
    $arrMFAOptions = LoadMFAOptions($iUserID);

    if (password_verify($strPWD, $strUPWD))
    {
      if (!$_SESSION["bMFA_active"])
      {
        require("AuthIncl.php");
      }
      else
      {
        if ($strCode == "")
        {
          require_once("MFAOptions.php");
        }
        else
        {
          if ($strMFAType == "totp")
          {
            if ($tfa->verifyCode($strTOTP, $strCode) === true)
            {
              require("AuthIncl.php");
            }
            else
            {
              require_once("MFAOptions.php");
            }
          }
          if ($strMFAType == "recover")
          {
            $strActivity = "Recovery Code usage";
            $arrTypes = array("SMS"=>"6","email"=>"7");
            NotifyActivity ($strActivity,$arrTypes);

            $strCode = str_replace(' ','',$strCode);
            if (password_verify($strCode, $strRHash))
            {
              require("AuthIncl.php");
            }
            else
            {
              require_once("MFAOptions.php");
            }
          }
        }
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
    require_once("header.php");
    if (isset($_SESSION["Reason"]))
    {
        print "<p class=Header1>$_SESSION[Reason]</p>\n";
        unset($_SESSION["Reason"]);
    }
    require_once("LoginIncl.php");
    require_once("footer.php");
	}
?>