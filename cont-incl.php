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
?>
