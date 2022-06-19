<?php 
    require("header.php");
    print("<div class=Header1>Links</div>");			
    $strCat = "";
    $strQuery = "SELECT vcCategory,vcLink,vcName,vcComment FROM vwlinks order by iSortNum;";
    if (!$Result = $dbh->query ($strQuery))
    {
        error_log ('Failed to fetch data. Error ('. $dbh->errno . ') ' . $dbh->error);
        error_log ($strQuery);
        print "<p class=\"Attn\" align=center>$ErrMsg</p>\n";
        exit(2);
    }
    while ($Row = $Result->fetch_assoc())
    {
        $strCategory = $Row['vcCategory'];
        $strLink     = $Row['vcLink'];
        $strName     = $Row['vcName'];
        $strComment  = $Row['vcComment'];
        if ($strCat != $strCategory)
        {
            if ($strCat != "")
            {
                print "</ul>\n";
            }
            print "<p class=\"LinkCategoryHeader\">$strCategory</p>\n<ul>\n";
            $strCat = $strCategory;
        }
        if ($ShowLinkURL == "False")
        {
            print "<li class=\"MainText\"><a href=\"$strLink\" target=\"_blank\"><b>$strName</b></a>  $strComment</li> \n";
        }
        else
        {
            print "<li class=\"MainText\"><b>$strName</b> <a href=\"$strLink\" target=\"_blank\">$strLink</a>  $strComment</li> \n";
        }
    }
    print "<p></p>";
    require("footer.php");
?>