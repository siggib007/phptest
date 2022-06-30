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

require_once("header.php");
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
require_once("footer.php");
?>
