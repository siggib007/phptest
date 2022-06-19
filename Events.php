<?php
    require "header.php";
    if (isset($_GET['a']))
    {
        $strType = $_GET['a'];
    }
    else
    {
        $strType="Curr";
    }

    if ($strType=="Curr")
    {
        print "<center>\n";
        print "<a class=\"NavButton\" href=\"$strPageName?a=Past\">View Past Events</a>\n";
        print "</center>\n";
        $strQuery = "select * from tblEvents where dtEventDate > now() order by dtEventDate;";
        if (!$Result = $dbh->query ($strQuery))
        {
            error_log ('Failed to fetch data. Error ('. $dbh->errno . ') ' . $dbh->error);
            error_log ($strQuery);
            print "<p class=\"Attn\" align=center>$ErrMsg</p>\n";
            exit(2);
        }
        $NumAffected = $Result->num_rows;
        if ($NumAffected ==0)
        {
            print "<p class=\"Header1\">No Upcoming Events at this time</p>\n";
            print "<p class=\"Header3C\">Click Button above to Check out our past Events</p>\n";
        }
        else
        {
            print "<p class=\"Header1\">Upcoming Events</p>\n";
        }
    }
    else
    {
        print "<center>\n";
        print "<a class=\"NavButton\" href=\"$strPageName?a=Curr\">View Upcoming Events</a>\n";
        print "</center>\n";
        $strQuery = "select * from tblEvents where dtEventDate < now() order by dtEventDate DESC;";
        if (!$Result = $dbh->query ($strQuery))
        {
            error_log ('Failed to fetch data. Error ('. $dbh->errno . ') ' . $dbh->error);
            error_log ($strQuery);
            print "<p class=\"Attn\" align=center>$ErrMsg</p>\n";
            exit(2);
        }
        $NumAffected = $Result->num_rows;
        if ($NumAffected ==0)
        {
            print "<p class=\"Header1\">No past Events</p>\n";
            print "<p class=\"Header3C\">Click Button above to Check out our future Events</p>\n";
        }
        else
        {
            print "<p class=\"Header1\">Past Events</p>\n";
        }
    }

    while ($Row = $Result->fetch_assoc())
    {
        $iEventID = $Row['iEventID'];
        $strEventName = $Row['vcEventName'];
        $strEventDate = date("$strDateFormat $strTimeFormat",strtotime($Row['dtEventDate']));
        $strEventLocation = $Row['vcEventLocation'];
        $strPoster = $Row['vcPoster'];
        $strVideo = $Row['vcVideo'];

        $strDescr = $Row['tEventInfo'];
        $strDescr = str_replace("\r\n","\n",$strDescr);
        $strDescr = str_replace("\r","\n",$strDescr);
        $strDescr = str_replace("\n","<br>\n",$strDescr);
        $strDescr = str_replace("\n\n","\n</p>\n<p class=MainText>\n",$strDescr);
        print "<div class=\"ClassName\"><a id=$iEventID>$strEventName</a></div>\n";
        print "<div class=\"ClassDescr\">$strEventDate at $strEventLocation</div>\n";
        if ($strPoster != "")
        {
            print "<a class=\"ClassDescr\" href=\"$strPoster\" target=\"_blank\">Download an Event flyer</a>\n";
        }
        print "<div class=\"ClassDescr\">$strDescr</div>\n";
        if ($strVideo != "")
        {
            print "<div class=\"ClassDescr\" style=\"text-align:center\">\n";
            print "Check out this video about the event. Click play to watch.</div>\n";
            print "<p>\n<video style=\"display: block; margin-left: auto; margin-right: auto;\"";
            print "controls=\"controls\" width=\"500\" height=\"300\">";
            print "<source src=\"$strVideo\" type=\"video/mp4\" />";
            print "</video></p>\n";
        }
    }

    require "footer.php";
?>
