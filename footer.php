<?php
	print "<br>\n";
	print "<center>\n";

	print "<p class=\"footer\">\n|&nbsp;&nbsp;";
        $strQuery = "SELECT * FROM tblContactInfo WHERE vcType = 'Address' ORDER BY iSequence";
        if (!$Result = $dbh->query ($strQuery))
        {
                error_log ('Failed to fetch data. Error ('. $dbh->errno . ') ' . $dbh->error);
                error_log ($strQuery);
                print "<p class=\"Attn\" align=center>$ErrMsg</p>\n";
                exit(2);
        }
        $NumAffected = $Result->num_rows;
        while ($Row = $Result->fetch_assoc())
        {
                print $Row['vcValue'] . "&nbsp;&nbsp;|&nbsp;&nbsp;";
        }
        print "&nbsp;&nbsp;<a href=\"contact.php\">Contact Us</a>&nbsp;&nbsp;|\n";
        print "&nbsp;&nbsp;Copyright &copy; 2012 <a href=\"http://www.supergeek.us\" target=\"_blank\">"
        . "Siggi Bjarnason</a>&nbsp;&nbsp;|\n";
	print "&nbsp;&nbsp;Your IP is $strRemoteIP $strHost&nbsp;&nbsp;|\n";
	print "</p>\n";
	print "</center>\n";
	print "</body>\n";
	print "</html>\n";
?>