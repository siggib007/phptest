<?php
    require("header.php");

    print "<p class=\"Header1\">Frequently Asked Questions (FAQ)</p>\n";
    $strQuery = "select iFAQid, vcQuestion, tAnswer from tblFAQ;";
    if (!$Result = $dbh->query ($strQuery))
    {
        error_log ('Failed to fetch data. Error ('. $dbh->errno . ') ' . $dbh->error);
        error_log ($strQuery);
        print "<p class=\"Attn\" align=center>$ErrMsg</p>\n";
        exit(2);
    }
    while ($Row = $Result->fetch_assoc())
    {
        $vcQuestion = $Row['vcQuestion'];
        $strAnswer = $Row['tAnswer'];
        $iFAQid = $Row['iFAQid'];
        print "<div class=\"ClassName\"><a id=$iFAQid>$vcQuestion</a></div>\n";
        print "<div class=\"ClassDescr\">$strAnswer</div>\n";
    }
    require("footer.php");
?>
