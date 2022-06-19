<?php 
$strReferer = $_SERVER['HTTP_REFERER'];
$strPageNameParts = explode('/',$strReferer);
$HowMany = count($strPageNameParts);
$LastIndex = $HowMany - 1;
$strReferPage = $strPageNameParts[$LastIndex];	

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
if ($strReferPage=='myprofile.php')
{
	$strReferPage = 'index.php';
}
require_once("DBCon.php");
require_once("KillSession.php");
$strReferPage = $ROOTPATH . $strReferPage;
header("Location: $strReferPage");
print "<p class=\"Header1\">You have been successfully logged out.</p>";
require("footer.php");
?>