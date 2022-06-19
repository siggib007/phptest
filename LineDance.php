<?php 
require("header.php");
$strQuery = "SELECT tPageTexts FROM tblPageTexts WHERE vcTextName = 'LineDance' ;";		
if (!$Result = $dbh->query ($strQuery))
{
    error_log ('Failed to fetch Content data. Error ('. $dbh->errno . ') ' . $dbh->error);
    error_log ($strQuery);
    print "<p class=\"Attn\" align=center>$ErrMsg</p>\n";
    exit(2);
}
$Row = $Result->fetch_assoc();
$PageText = $Row['tPageTexts'];
print "<div align=\"center\"><img src=\"img/LinceDanceHead.jpg\"  height=\"100\" alt=\"Line Dance Performance Team\"align=\"center\"></div>\n";
$PageText = str_replace("\r\n","\n",$PageText);
$PageText = str_replace("\r","\n",$PageText);
//$PageText = str_replace("\n\n","\n</p>\n<p class=MainText>\n",$PageText);
$PageText = str_replace("\n","<br>\n",$PageText);
print "<p class=\"LDT_Text\">\n$PageText</p>\n";
$strQuery = "SELECT tPageTexts FROM tblPageTexts WHERE vcTextName = 'NewFloor' ;";		
if (!$Result = $dbh->query ($strQuery))
{
    error_log ('Failed to fetch Content data. Error ('. $dbh->errno . ') ' . $dbh->error);
    error_log ($strQuery);
    print "<p class=\"Attn\" align=center>$ErrMsg</p>\n";
    exit(2);
}
$Row = $Result->fetch_assoc();
$PageText = $Row['tPageTexts'];

print "<p class=\"LDT_NewFloor\">\n$PageText</p>\n";
print "<div align=\"center\">\n";
print "<img src=\"img/DanceCats.jpg\" height=\"100\" align=\"center\">";

$strQuery = "SELECT dtDate, dtStartTime, dtStopTime FROM tblLDTSchedule WHERE date_add(dtDate,INTERVAL 1 DAY) >= now() order by dtDate LIMIT {$ConfArray['maxPracticeDates']};";
if (!$Result = $dbh->query ($strQuery))
{
    error_log ('Failed to fetch data. Error ('. $dbh->errno . ') ' . $dbh->error);
    error_log ($strQuery);
    print "<p class=\"Attn\" align=center>$ErrMsg</p>\n";
    exit(2);
}

print "<table class=\"LDT_Dates\">\n";
print "<tr>\n";
print "<td colspan=2>";
print "<div class=\"LDT_DateTimeHead\">Upcoming Practice Dates & Times<br>\n";
print "(Practice Fee = \${$ConfArray['PracticeFee']})</div>";
print "</td>\n";
print "</tr>\n";
while ($Row = $Result->fetch_assoc())
{
    $strDate = date($strDateFormat,strtotime($Row['dtDate']));
    $strStartTime = date($strTimeFormat,strtotime($Row['dtStartTime']));
    $strStopTime = date($strTimeFormat,strtotime($Row['dtStopTime']));
    print "<tr>\n";
    print "<td class=\"LDT_Dates\">$strDate</td>\n";
    print "<td class=\"LDT_Dates\">$strStartTime - $strStopTime</td>\n";
    print "</tr>\n";
}
print "</table>\n";
print "</div>\n";
$strQuery = "SELECT vcValue FROM tblLDTInfo WHERE vcType = 'Location' ORDER BY iSequence;";
if (!$Result = $dbh->query ($strQuery))
{
    error_log ('Failed to fetch data. Error ('. $dbh->errno . ') ' . $dbh->error);
    error_log ($strQuery);
    print "<p class=\"Attn\" align=center>$ErrMsg</p>\n";
    exit(2);
}

print "<p class=\"LDT_Loc\">Practice Location:<br>\n";
while ($Row = $Result->fetch_assoc())
{
    print $Row['vcValue'];
    print "<br>\n";
}
print "</p>\n";

$strQuery = "SELECT vcType, vcValue FROM tblLDTInfo WHERE vcType in ('Name','Phone', 'Email') ORDER BY iSequence;";
if (!$Result = $dbh->query ($strQuery))
{
    error_log ('Failed to fetch data. Error ('. $dbh->errno . ') ' . $dbh->error);
    error_log ($strQuery);
    print "<p class=\"Attn\" align=center>$ErrMsg</p>\n";
    exit(2);
}
print "<div class=\"LDT_Contact\">If you have any questions, please contact:\n";
while ($Row = $Result->fetch_assoc())
{
    switch ($Row['vcType'])
    {
        case "Name":
            print "<div class=\"LDT_Name\">";
            print $Row['vcValue'];
            print "</div>\n";
            break;
        case "Phone":
            print "<div class=\"LDT_info\">";
            print $Row['vcValue'];
            break;
        case "Email":
            print " or ";
            print $Row['vcValue'];
            print "</div>\n";
            break;
    }
}
print "</div>\n";

require("footer.php");
?>
