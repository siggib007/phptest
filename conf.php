<?php
    require("header.php");

    if ($strReferer != $strPageURL and $PostVarCount > 0)
    {
        print "<p class=\"Error\">Invalid operation, Bad Reference!!!</p> ";
        exit;
    }

    print("<p class=\"Header1\">Site Configuration</p>\n");

    if (($PostVarCount == 1) and ($_POST['btnSubmit'] == 'Go Back'))
    {
        header("Location: $strPageURL");
    }

    if (isset($_POST['btnSubmit']))
    {
            $btnSubmit = $_POST['btnSubmit'];
    }
    else
    {
            $btnSubmit = "";
    }

    if ($btnSubmit == 'Save')
    {
        $strValueName = CleanSQLInput(substr(trim($_POST['txtValueName']),0,49));
        $strValue = "False";
        if (isset($_POST['txtValue']))
        {
            $strValue = CleanSQLInput(substr(trim($_POST['txtValue']),0,49));
        }
        if (isset($_POST['chkValue']))
        {
            $strValue = "True";
        }
        $strQuery = "update tblconf set vcValue = '$strValue' where vcValueName = '$strValueName';";
        UpdateSQL ($strQuery,"update");
    }

    print "<table>\n";
    $strQuery = "SELECT * FROM tblconf where vcValueName not in ('Maintenance','ROOTPATH');";
    if (!$Result = $dbh->query ($strQuery))
    {
        error_log ('Failed to fetch data. Error ('. $dbh->errno . ') ' . $dbh->error);
        error_log ($strQuery);
        print "<p class=\"Attn\" align=center>$ErrMsg</p>\n";
        exit(2);
    }
    while ($Row = $Result->fetch_assoc())
    {
        $Key = $Row['vcValueName'];
        $Value = $Row['vcValue'];
        $ValueDescr = $Row['vcValueDescr'];
        $ValueType = $Row['vcValueType'];
        if ($WritePriv <=  $Priv)
        {
            print "<form method=\"POST\">\n";
            print "<tr valign=\"top\">\n";
            print "<td class=\"lblright\"><input type=\"hidden\" value=\"$Key\" name=\"txtValueName\">$ValueDescr: </td>\n";
            print "<td>";
            switch ($ValueType)
            {
                case "Boolean":
                    if ($Value=="True")
                    {
                        $strChecked = "checked";
                    }
                    else
                    {
                        $strChecked = "";
                    }
                    print "<input type=\"checkbox\" name=\"chkValue\" $strChecked>";
                    break;
                case "int":
                case "text":
                    print "<input type=\"text\" value=\"$Value\" name=\"txtValue\" size=\"50\" >";
                    break;
                default :
                    $strQuery = "SELECT vcType, vcText FROM $ValueType ORDER BY iOrder;";
                    if (!$Result2 = $dbh->query ($strQuery))
                    {
                        error_log ('Failed to fetch data. Error ('. $dbh->errno . ') ' . $dbh->error);
                        error_log ($strQuery);
                        print "<p class=\"Attn\" align=center>$ErrMsg</p>\n";
                        exit(2);
                    }

                    print "<select size=\"1\" name=\"txtValue\">\n";
                    while ($Row2 = $Result2->fetch_assoc())
                    {
                        if ($Row2['vcType'] == $Value)
                        {
                            print "<option selected value=\"{$Row2['vcType']}\">{$Row2['vcText']}</option>\n";
                        }
                        else
                        {
                            print "<option value=\"{$Row2['vcType']}\">{$Row2['vcText']}</option>\n";
                        }
                    }
                    print "</select>\n";
            }
            print "</td>\n";
            print "<td><input type=\"Submit\" value=\"Save\" name=\"btnSubmit\"></td>";
            print "</tr>\n";
            print "</form>\n";
        }
        else
        {
            print "$ValueDescr : $Value<br>\n";
        }
    }
    print "</table>\n";
    require("footer.php");
?>
