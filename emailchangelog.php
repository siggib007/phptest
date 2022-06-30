<?php
  require("header.php");
  print "<p class=\"Header1\">Email Update Log</p>\n";
  print "<table border=1>\n";
  print "<tr  class=lbl><th>ID</th><th>User Name</th><th>GUID</th><th>New Email</th><th>Source IP</th><th>Time Stamp</th></tr>\n";
  $strQuery = "SELECT iChangeID, vcName, vcGUID, vcNewEmail, vcReqIPAdd, dtTimeStamp FROM vwemailupdate order by dtTimeStamp desc;";
  if (!$Result = $dbh->query ($strQuery))
  {
    error_log ('Failed to fetch data. Error ('. $dbh->errno . ') ' . $dbh->error);
    error_log ($strQuery);
    exit(2);
  }
  while ($Row = $Result->fetch_assoc())
  {
    print "<tr><td>$Row[iChangeID]</td><td>$Row[vcName]</td><td>$Row[vcGUID]</td><td>$Row[vcNewEmail]</td><td>$Row[vcReqIPAdd]</td><td>$Row[dtTimeStamp]</td></tr>\n";
  }
  print "</table>\n";

  require("footer.php");
?>
