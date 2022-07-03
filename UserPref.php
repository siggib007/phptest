<?php
 print "<p>&nbsp;</p>\n<p class=\"Header2\"><a id=\"mfa\">Account Preferences</a></p>\n";
 $btnValue = "";
 $strQuery = "SELECT t.*,v.vcValue,v.iUserID ".
  "FROM tblUsrPrefTypes t LEFT JOIN tblUsrPrefValues v ON t.iID = v.iTypeID ".
  "WHERE v.iUserID = $iUserID OR v.iUserID IS NULL ORDER BY iSortOrder;";

  if (!$Result = $dbh->query ($strQuery))
  {
    error_log ('Failed to fetch data. Error ('. $dbh->errno . ') ' . $dbh->error);
    error_log ($strQuery);
    print "<p class=\"Attn\" align=center>$ErrMsg</p>\n";
    exit(2);
  }
  while ($Row = $Result->fetch_assoc())
  {
    $Key = $Row['iID'];
    $Value = $Row['vcValue'];
    $ValueDescr = $Row['vcLabel'];
    $ValueType = $Row['vcType'];
    print "<form method=\"POST\">\n";
    print "<input type=\"hidden\" value=\"$Key\" name=\"txtKey\">";
    print "<input type=\"hidden\" value=\"$ValueDescr\" name=\"txtLabel\">";
    print "<p>$ValueDescr: ";
    switch ($ValueType)
    {
      case "Boolean":
        if (strtolower($Value)=="true")
        {
          $btnValue = "Enabled";
        }
        else
        {
          $btnValue = "Disabled";
        }
        break;
      case "int":
      case "text":
        print "<input type=\"text\" value=\"$Value\" name=\"txtValue\" size=\"30\" >";
        $btnValue = "Save";
        break;
      }
      print "<input type=\"Submit\" value=\"$btnValue\" name=\"btnSubmit\"></p>\n";
      print "</form>\n";
  }
?>
