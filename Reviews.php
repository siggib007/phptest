<?php
require "header.php";

print "<p class=\"Header1\">Share Your Studio B Experience</p>\n";
print "<center>";

$strQuery = "select vcSiteName, vcSiteURL, vcImgPath from tblReviewSiteURL;";
if (!$Result = $dbh->query ($strQuery))
{
    error_log ('Failed to fetch data. Error ('. $dbh->errno . ') ' . $dbh->error);
    error_log ($strQuery);
    print "<p class=\"Attn\" align=center>$ErrMsg</p>\n";
    exit(2);
}
print "<table>\n";
print "<tr>\n";
while ($Row = $Result->fetch_assoc())
{
    $vcSiteName = $Row['vcSiteName'];
    $vcSiteURL = $Row['vcSiteURL'];
    $vcMMURL = $Row['vcImgPath'];
    print "<td class=\"ReviewLinks\">";
    print "<a href=\"$vcSiteURL\">";
    if ($vcMMURL!='')
    {
        print "<img style=\"border:0;\" src=\"$vcMMURL\" alt=\"$vcSiteName\">\n";
    }
    else
    {
        print "$vcSiteName";
    }
    print "</a>\n";
    print "</td>\n";
}
print "</tr>\n";
print "</table>\n";

$strQuery = "select vcFeedbackName, tFeedbackDescr, vcImgPath from tblFeedback;";
if (!$Result = $dbh->query ($strQuery))
{
    error_log ('Failed to fetch data. Error ('. $dbh->errno . ') ' . $dbh->error);
    error_log ($strQuery);
    print "<p class=\"Attn\" align=center>$ErrMsg</p>\n";
    exit(2);
}
while ($Row = $Result->fetch_assoc())
{
    $vcFeedbackName = $Row['vcFeedbackName'];
    $vcFeedbackDescr = $Row['tFeedbackDescr'];
    $vcMMURL = $Row['vcImgPath'];
    print "<p class=\"Header3\">$vcFeedbackName</p>\n";
    print "<p class=\"MainText\">$vcFeedbackDescr</p>\n";
    if ($vcMMURL!='')
    {
        print "<img src=\"$vcMMURL\" width=65% border=0><br>\n";
    }
}
print "</center>";
require "footer.php";
?>
