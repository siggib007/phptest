<?php
  $strQuery = "SELECT t.*,v.vcValue,v.iUserID ".
  "FROM tblUsrPrefTypes t LEFT JOIN tblUsrPrefValues v ON t.iID = v.iTypeID ".
  "WHERE v.iUserID = $iUserID OR v.iUserID IS NULL;";

  print "<table>\n";
  if (!$Result = $dbh->query ($strQuery))
  {
    error_log ('Failed to fetch data. Error ('. $dbh->errno . ') ' . $dbh->error);
    error_log ($strQuery);
    print "<p class=\"Attn\" align=center>$ErrMsg</p>\n";
    exit(2);
  }
  while ($Row = $Result->fetch_assoc())
  {
    $Key = $Row['vcCode'];
    $Value = $Row['vcValue'];
    $ValueDescr = $Row['vcLabel'];
    $ValueType = $Row['vcType'];
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
      }
      print "</td>\n";
      print "<td><input type=\"Submit\" value=\"Save\" name=\"btnSubmit\"></td>";
      print "</tr>\n";
      print "</form>\n";
  }
  print "</table>\n";
?>
