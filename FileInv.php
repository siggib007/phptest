<?php
require("header.php");
$arrFiles = array();
$handle = opendir('.');

if ($handle) {
  while (($entry = readdir($handle)) !== FALSE) {
    $arrFiles[] = $entry;
  }
}

closedir($handle);
//print "<p>".var_dump($arrFiles)."</P>";
$strQuery = "SELECT vcLink FROM tblmenu";
if (!$Result = $dbh->query ($strQuery))
{
    error_log ('Failed to fetch menu data. Error ('. $dbh->errno . ') ' . $dbh->error);
    error_log ($strQuery);
    print "<p class=\"Attn\" align=center>$ErrMsg</p>\n";
    exit(2);
}
$KnownFiles = array();
while ($Row = $Result->fetch_assoc())
{
  $KnownFiles[] = $Row['vcLink'];
}
// $KnownFiles = array("DBCon.php","functions.php","index.php","secrets.php");

print "<p class=\"Header1\">Here are the files that are missing are being inserted</p>";
foreach($arrFiles as $file)
{
  if (substr($file,-3)=="php" and ! in_array($file,$KnownFiles))
  {
    print "<p class=\"BlueNote\">$file</P>";
    $strQuery = "INSERT INTO tblmenu (vcTitle, vcLink, iReadPriv, vcHeader, bAdmin, bSecure) VALUES ('$file', '$file', '500', '$file', '0', '0');";
    // print $strQuery;
    UpdateSQL ($strQuery,"insert");
  }
}
require("footer.php");
?>
