<?php
    require("header.php");
    print "<div class=Header1>Administration tasks</div>";

    $strCat = "";
    $i=1;
    if ($strCatID > 0)
    {
        $strWhere = "WHERE iReadPriv <= $Priv and iCatID = $strCatID";
    }
    else
    {
        $strWhere = "WHERE iReadPriv <= $Priv and iCatID > 0";
    }

    $strQuery = "SELECT vcTitle, vcLink, bNewWindow, vcCatName FROM vwAdminCat $strWhere;";
    if (!$Result = $dbh->query ($strQuery))
    {
        error_log ('Failed to fetch data. Error ('. $dbh->errno . ') ' . $dbh->error);
        error_log ($strQuery);
        print "<p class=\"Attn\" align=center>$ErrMsg</p>\n";
        exit(2);
    }
    print "<table class=\"Admin\">\n";
    while ($Row = $Result->fetch_assoc())
    {
        $strLink    = $Row['vcLink'];
        $strName    = $Row['vcTitle'];
        $bNewWindow = $Row['bNewWindow'];
        $strCategory = $Row['vcCatName'];
        if ($strCat != $strCategory)
        {
            if ($strCat != "")
            {
                print "</td>\n";
                if ($i<$iNumCol)
                {
                    $i++;
                }
                else
                {
                    $i=1;
                    print "</tr>\n";
                    print "<tr>\n";
                }
            }
            else
            {
                print "<tr>\n";
            }
            print "<td class=\"Admin\">\n";
            print "<p class=\"AdminCategoryHeader\">$strCategory</p>\n";
            $strCat = $strCategory;
        }
        if ($bNewWindow == 1)
        {
            print "<div class=\"MainText\"> <a href=\"$strLink\" target=\"_blank\">$strName</a></div>\n";
        }
        else
        {
            print "<div class=\"MainText\"> <a href=\"$strLink\">$strName</a></div>\n";
        }
    }
    print "</td>\n";
    print "</tr>\n";
    print "</table>\n";

    require("footer.php");
?>