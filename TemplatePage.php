<?php 
require("header.php");
$strQuery = "SELECT * FROM tblContent WHERE iMenuID = '$iMenuID' and dtTimeStamp = (select max(dtTimeStamp) from tblContent where iMenuID = '$iMenuID');";		
if (!$Result = $dbh->query ($strQuery))
{
    error_log ('Failed to fetch Content data. Error ('. $dbh->errno . ') ' . $dbh->error);
    error_log ($strQuery);
    print "<p class=\"Attn\" align=center>$ErrMsg</p>\n";
    exit(2);
}
$Row = $Result->fetch_assoc();
$PageHeader = $Row['vcPageHeader'];
$PageText = $Row['tPageText'];
$bCRLF = $Row['bLineBreak'];
print "<p class=Header1>$PageHeader</p>";
if (substr($PageText,0,1)=="<")
{
    print "$PageText\n";
}
else
{
    $PageText = str_replace("\r\n","\n",$PageText);
    $PageText = str_replace("\r","\n",$PageText);
    if ($bCRLF == 1)
    {
        $PageText = str_replace("\n","<br>\n",$PageText);
    }
    $PageText = str_replace("\n\n","\n</p>\n<p class=MainText>\n",$PageText);
    print "<p class=MainText>\n$PageText</p>\n";
}
require("footer.php");
?>
