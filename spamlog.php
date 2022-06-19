<?php
	require("header.php");
	$LimitCount = 300;
	print "<p class=\"Header1\">Spam Log</p>\n";
	print "<p class=\"Header3\">Limited to the most recent $LimitCount records</p>\n";
	print "<table border=1>\n";
	print "<tr  class=lbl><th>ID</th><th>Time Stamp</th><th>Source IP</th><th>Content</th></tr>\n";
	$strQuery = "SELECT iLogID, dtLogDateTime, vcIPAddress, vcContent FROM tblSpamLog order by dtLogDateTime desc limit $LimitCount;";
	if (!$Result = $dbh->query ($strQuery))
	{
		error_log ('Failed to fetch data. Error ('. $dbh->errno . ') ' . $dbh->error);
		error_log ($strQuery);
		exit(2);
	}
	while ($Row = $Result->fetch_assoc())
	{
			print "<tr><td>$Row[iLogID]</td><td>$Row[dtLogDateTime]</td><td>$Row[vcIPAddress]</td><td>$Row[vcContent]</td></tr>\n";
	}
	print "</table>\n";

	require("footer.php");
?>
