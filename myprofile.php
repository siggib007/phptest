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

  require("header.php");

  $strUserID = $_SESSION["UID"];

  print "<p class=\"Header1\">My Profile</p>\n";

  $strQuery = "select * from tblUsers where iUserID = $strUserID;";
  if (!$Result = $dbh->query ($strQuery))
  {
    error_log ('Failed to data. Error ('. $dbh->errno . ') ' . $dbh->error);
    error_log ($strQuery);
    print "<p class=\"Attn\" align=center>$ErrMsg</p>\n";
    exit(2);
}
  $Row = $Result->fetch_assoc();
  print "<p class=\"MainTextCenter\">\n";
  print "RegistrationID: $strUserID<br>\n";
  print "{$Row['vcName']}<br>\n";
  print "{$Row['vcAddr1']}<br>\n";
  print "{$Row['vcAddr2']}<br>\n";
  print "{$Row['vcCity']}, {$Row['vcState']} {$Row['vcZip']}<br>\n";
  print "{$Row['vcCountry']}<br>\n";
  print "{$Row['vcEmail']}<br>\n";
  print "{$Row['vcPhone']}<br>\n";

  $strQuery = "SELECT vcPrivName FROM tblprivlevels where iPrivLevel = {$Row['iPrivLevel']};";
  if (!$PrivResult = $dbh->query ($strQuery))
  {
    error_log ('Failed to data. Error ('. $dbh->errno . ') ' . $dbh->error);
    error_log ($strQuery);
    print "<p class=\"Attn\" align=center>$ErrMsg</p>\n";
    exit(2);
}
  $PrivRow = $PrivResult->fetch_assoc();
  $PrivName = $PrivRow['vcPrivName'];
  if ($PrivName == '')
  {
    $PrivName = $Row['iPrivLevel'];
  }

  print "<p class=\"MainTextCenter\">Authorization level is set to $PrivName</p>\n";

  if ($Row['dtLastLogin'])
  {
    $LastLogin = 'on ' . date('l F jS Y \a\t G:i',strtotime($Row['dtLastLogin']));
  }
  else
  {
    $LastLogin = 'never';
  }
  print "<p class=\"MainTextCenter\">Last logged in $LastLogin</p>\n";

  require "footer.php";
?>
