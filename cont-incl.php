<?php
print "<p class=\"MainText\">Feel free to contact us with any questions, concerns or feedback.</p>\n";

$strQuery = "SELECT * FROM tblContactInfo WHERE vcType = 'Address' ORDER BY iSequence";
if (!$Result = $dbh->query ($strQuery))
{
	error_log ('Failed to fetch data. Error ('. $dbh->errno . ') ' . $dbh->error);
	error_log ($strQuery);
        print "<p class=\"Attn\" align=center>$ErrMsg</p>\n";
	exit(2);
}
$NumAffected = $Result->num_rows;
if ($NumAffected > 0)
{
	print "<div class=\"Header3\">Address</div>";
}
print "<div class=\"MainText\">";
while ($Row = $Result->fetch_assoc())
{
	print $Row['vcValue'] . "<br>\n";
}
print "<p><a class=\"MapButton\" href=\"http://maps.google.com/?q=Studio+B+Dance+Renton\" target=\"_blank\">"
. "View Map</a></p>\n";
print "</div>\n";

$strQuery = "SELECT * FROM tblContactInfo WHERE vcType = 'Email' ORDER BY iSequence";
if (!$Result = $dbh->query ($strQuery))
{
	error_log ('Failed to fetch data. Error ('. $dbh->errno . ') ' . $dbh->error);
	error_log ($strQuery);
        print "<p class=\"Attn\" align=center>$ErrMsg</p>\n";
	exit(2);
}
$NumAffected = $Result->num_rows;
if ($NumAffected > 0)
{
	print "<div class=\"Header3\">Electronic mail</div>";
	print "<p class=\"MainText\">";
}

while ($Row = $Result->fetch_assoc())
{
	print $Row['vcLabel'] . ": <a href=\"mailto:" . $Row['vcValue'] . "\">" .
				$Row['vcValue'] . "</a><br>\n";
}
print "</p>\n";

$strQuery = "SELECT * FROM tblContactInfo WHERE vcType = 'Phone' ORDER BY iSequence";
if (!$Result = $dbh->query ($strQuery))
{
	error_log ('Failed to fetch data. Error ('. $dbh->errno . ') ' . $dbh->error);
	error_log ($strQuery);
        print "<p class=\"Attn\" align=center>$ErrMsg</p>\n";
	exit(2);
}
$NumAffected = $Result->num_rows;
if ($NumAffected > 0)
{
	print "<div class=\"Header3\">Phone Numbers</div>";
	print "<p class=\"MainText\">";
}

while ($Row = $Result->fetch_assoc())
{
	print $Row['vcLabel'] . ": " . $Row['vcValue'] . "<br>\n";
}
print "</p>\n";
//print "<div class=\"Header3\">Newsletter</div>";
print "<p class=\"MainText\">";
print "<a  class=\"NLSignup\" href=\"http://visitor.r20.constantcontact.com/d.jsp?llr=9cbtlfmab&p=oi&m=1112576459866&sit=nssxjouhb&f=d96756c3-ff64-4fe7-854f-58e67d4fdc0a\" target=\"_blank\">\n";
print "To sign up for a dance newsletter click here\n";
print "</a>\n";
print "</p>\n";
?>
