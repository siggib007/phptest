<?php
    $RegCol1 = 400;
    $RegCol2 = 500;
    $iNumCol = 3;
    print "<table border=\"0\" width=\"900\">\n";
    print "<tr>\n<td width=\"$RegCol1\" align=\"right\" class=\"lbl\">Name: </td>\n";
    print "<td width=\"$RegCol2\"><input type=\"text\" name=\"txtName\" size=\"50\" value=\"$strName\">\n";
    print "<span class=\"Attn\">Required</span>\n";
    print "<input type=\"hidden\" name=\"iUserID\" size=\"5\" value=\"$strUserID\"></td>\n</tr>\n";
    print "<tr>\n<td colspan=2 align=center class=\"lbl\">Mailing Address</td>\n</tr>\n";
    print "<tr>\n<td width=\"$RegCol1\" align=\"right\" class=\"lbl\">Street: </td>\n";
    print "<td><input type=\"text\" name=\"txtAddr1\" size=\"50\" value=\"$strAddr1\"></td>\n</tr>\n";
    print "<tr>\n<td width=\"$RegCol1\" align=\"right\" class=\"lbl\">Street2: </td>\n";
    print "<td><input type=\"text\" name=\"txtAddr2\" size=\"50\" value=\"$strAddr2\"></td>\n</tr>\n";
    print "<tr>\n<td width=\"$RegCol1\" align=\"right\" class=\"lbl\">City:</td>\n";
    print "<td><input type=\"text\" name=\"txtCity\" size=\"50\" value=\"$strCity\"> </td>\n</tr>\n";
    print "<tr>\n";
    print "<td width=\"$RegCol1\" align=\"right\" class=\"lbl\">State:</td>\n";
    print "<td>";
    $strQuery = "select * from US_States;";
    if (!$Result2 = $dbh->query ($strQuery))
    {
        error_log ('Failed to fetch data. Error ('. $dbh->errno . ') ' . $dbh->error);
        error_log ($strQuery);
        print "<p class=\"Attn\" align=center>$ErrMsg</p>\n";
        exit(2);
    }
    if ($strState=="")
    {
        $strState = "WA";
    }
    print "<select size=\"1\" name=\"cmbState\">\n";
    print "<option>Please Select State</option>\n";
    while ($Row2 = $Result2->fetch_assoc())
    {
        if ($Row2['vcStateAbr'] == $strState)
        {
                print "<option selected value=\"{$Row2['vcStateAbr']}\">{$Row2['vcStateName']}</option>\n";
        }
        else
        {
                print "<option value=\"{$Row2['vcStateAbr']}\">{$Row2['vcStateName']}</option>\n";
        }
    }
    print "</select>";
    print "</td>\n";
    print "</tr>\n";
    print "<tr>\n";
    print "<td width=\"$RegCol1\" align=\"right\" class=\"lbl\">Zip:</td>\n";
    print "<td> <input type=\"text\" name=\"txtZip\" size=\"10\" value=\"$strZip\"></td>\n";
    print "</tr>\n";
    print "<tr>\n";
    print "<td width=\"$RegCol1\" align=\"right\" class=\"lbl\">Country:</td>\n";
    print "<td>";
    $strQuery = "select * from CountryCodes;";
    if (!$Result2 = $dbh->query ($strQuery))
    {
        error_log ('Failed to fetch data. Error ('. $dbh->errno . ') ' . $dbh->error);
        error_log ($strQuery);
        print "<p class=\"Attn\" align=center>$ErrMsg</p>\n";
        exit(2);
    }
    if ($strCountry == "")
    {
        $strCountry = "UNITED STATES";
    }
    print "<select size=\"1\" name=\"cmbCountry\">\n";
    while ($Row2 = $Result2->fetch_assoc())
    {
        if ($Row2['vcCountryName'] == $strCountry)
        {
            print "<option selected value=\"{$Row2['vcCountryName']}\">{$Row2['vcCountryName']}</option>\n";
        }
        else
        {
            print "<option value=\"{$Row2['vcCountryName']}\">{$Row2['vcCountryName']}</option>\n";
        }
    }
    print "</select>";
    print "</td>\n";
    print "</tr>\n";
    print "<tr>\n";
    print "<td align=\"right\" class=\"lbl\">Home Phone Number: </td>\n";
    print "<td><input type=\"text\" name=\"txtPhone\" size=\"15\" value=\"$strPhone\"> </td>\n";
    print "</tr>\n";
    print "<tr>\n";
    print "<td align=\"right\" class=\"lbl\">Cell Phone Number: </td>\n";
    print "<td><input type=\"text\" name=\"txtCell\" size=\"15\" value=\"$strCell\"> </td>\n";
    print "</tr>\n";
    print "<tr>\n<td width=\"$RegCol1\" align=\"right\" class=\"lbl\">Email address: </td>\n";
    print "<td><input type=\"text\" name=\"txtEmail\" size=\"50\" value=\"$strEmail\">\n<span class=\"Attn\">Required</span>\n";
    print "<input type=\"hidden\" name=\"txtOEmail\" size=\"50\" value=\"$strEmail\"> </td>\n</tr>\n";
    print "<tr>\n";
    print "<td align=\"right\" class=\"lbl\">Birthdate: </td>\n";
    print "<td><input type=\"text\" name=\"txtBDate\" size=\"25\" value=\"$strBdate\"> </td>\n";
    print "</tr>\n";
    print "<tr>\n";
    print "<td align=\"right\" class=\"lbl\">Wedding Anniversary: </td>\n";
    print "<td><input type=\"text\" name=\"txtWedAnn\" size=\"25\" value=\"$strWedAnn\"> </td>\n";
    print "</tr>\n";
    print "<tr>\n";
    print "<td align=\"right\" class=\"lbl\">Gender: </td>\n";
    print "<td>\n";
    switch (trim($strGender))
    {
        case "male" :
            print "<input type=\"radio\" name=\"gender\" value=\"male\" checked> Male";
            print "<input type=\"radio\" name=\"gender\" value=\"female\"> Female";
            break;
        case "female" :
            print "<input type=\"radio\" name=\"gender\" value=\"male\"> Male";
            print "<input type=\"radio\" name=\"gender\" value=\"female\" checked> Female";
            break;
        default :
            print "<input type=\"radio\" name=\"gender\" value=\"male\"> Male";
            print "<input type=\"radio\" name=\"gender\" value=\"female\"> Female";
    }

    print "&nbsp&nbsp&nbsp<span class=\"Attn\">Required</span>\n</td>\n";
    print "<tr>\n";
    print "<td align=\"right\" class=\"lbl\">Any Health Issues that could affect dancing?: </td>\n";
    print "<td><input type=\"text\" name=\"txtHealth\" size=\"50\" value=\"$strHealth\"> </td>\n";
    print "</tr>\n";
    print "<tr>\n";
    print "<td align=\"right\" class=\"lbl\">How did you find out about Studio&nbsp;B&nbsp;Dance?: </td>\n";
    print "<td><input type=\"text\" name=\"txtLocate\" size=\"50\" value=\"$strLocate\"> </td>\n";
    print "</tr>\n";
    print "<tr><td>&nbsp</td></tr>";
   if ($strUserID)
    {
        $strQuery="CALL spUserMap($strUserID)";
    }
    else
    {
        $strQuery = "SELECT iInterestId, vcInterest, '' bChecked FROM tblInterests order by iSortNum;";
    }

    if (!$Result = $dbh->query ($strQuery,MYSQLI_USE_RESULT))
    {
        error_log ('Failed to fetch data. Error ('. $dbh->errno . ') ' . $dbh->error);
        error_log ($strQuery);
        print "<p class=\"Attn\" align=center>$ErrMsg</p>\n";
        exit(2);
    }
    print "<tr>\n";
    print "<td align=\"right\" class=\"lbl\">Dance Styles of Interest (check all that apply): </td>\n";
    print "</tr>\n";
    $i = 1;
    print "<tr>\n<td colspan=\"2\">\n<table align=\"center\">\n<tr>\n";
    while ($Row = $Result->fetch_assoc())
    {
        $vcInterest = $Row['vcInterest'];
        $iInterestID = $Row['iInterestId'];
        $bChecked = $Row['bChecked'];
        print "<td><input type=\"checkbox\" name=\"chkInterst[]\" value=\"$iInterestID\" $bChecked>$vcInterest</td>\n";
        if ($i<$iNumCol)
        {
            $i=$i+1;
        }
        else
        {
            $i=1;
            print "</tr>\n<tr>\n";
        }
    }
    while ($dbh->next_result())
    {
        if ($Result=$dbh->store_result())
        {
            $Result->free();
        }
    }
    $OtherColSpan = $iNumCol - $i + 1;
    print "<td colspan=\"$OtherColSpan\">";
    if ($strUserID)
    {
        $strQuery = "SELECT vcComment FROM tblInterestMap WHERE iUserID = $strUserID AND iInterestID = -1  limit 1;";
        if (!$Result2 = $dbh->query ($strQuery))
        {
            error_log ('Failed to fetch data. Error ('. $dbh->errno . ') ' . $dbh->error);
            error_log ($strQuery);
            print "<p class=\"Attn\" align=center>$ErrMsg</p>\n";
            exit(2);
        }
        $Row2 = $Result2->fetch_assoc();
        $strOther = $Row2['vcComment'];
        if ($strOther)
        {
            $bChecked="checked";
        }
    }
    else
    {
        $strOther = "";
        $bChecked="";
    }
    print "<input type=\"checkbox\" name=\"chkInterst[]\" value=\"-1\" $bChecked>Other\n" .
          "<input type=\"text\" name=\"txtOther\" size=\"30\" value=\"$strOther\"></td>\n";
    print "</tr>\n</table>\n";
?>
