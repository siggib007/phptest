<?php
	require("header.php");

	if ($strReferer != $strPageURL and $PostVarCount > 0)
	{
    print "<p class=\"Error\">Invalid operation, Bad Reference!!!</p> ";
    exit;
	}
	if (isset($_POST['btnSubmit']))
	{
    $btnSubmit = $_POST['btnSubmit'];
	}
	else
	{
    $btnSubmit = "";
	}

	print("<p class=\"Header1\">Administrative Categories</p>\n");

	if ($btnSubmit == 'Save')
	{
    $strAdminCat = CleanSQLInput(substr(trim($_POST['txtAdminCat']),0,49));
    $iCatID = CleanSQLInput(substr(trim($_POST['iCatID']),0,49));

    $strQuery = "update tblAdminCategories set vcCatName = '$strAdminCat' where iCatID = $iCatID;";
    UpdateSQL ($strQuery,"update");
	}

	if ($btnSubmit == 'Delete')
	{
    $iCatID = intval(substr(trim($_POST['iCatID']),0,49));

    $strQuery = "delete from tblAdminCategories where iCatID = $iCatID;";
    UpdateSQL ($strQuery,"delete");
	}

	if ($btnSubmit == 'Insert')
	{
    $strAdminCat = CleanSQLInput(substr(trim($_POST['txtAdminCat']),0,49));

    if ($strAdminCat == '')
    {
        print "<p>Please provide a administrative catergory name to insert</p>\n";
    }
    else
    {
        $strQuery = "insert tblAdminCategories (vcCatName) values ('$strAdminCat');";
        UpdateSQL ($strQuery,"insert");
    }
	}

	//Print the normal form after update is complete.
	print "<table>\n";
  print "<tr>\n";
  print "<th class=lbl>Update existing Category</th>\n";
  print "<th width = 100></th>\n";
  print "<th class=lbl>Or Insert New one</th>\n";
  print "</tr>\n";
	print "<tr>\n";
  print "<td>\n";
  print "<table border = 0>\n";
  print "<tr>\n";
  print "<th></th>\n";
  print "<th class=lbl>Category Name</th>\n";
  print "</tr>\n";
	$strQuery = "SELECT vcCatName, iCatID FROM tblAdminCategories order by iCatID;";
	if (!$Result = $dbh->query ($strQuery))
	{
    error_log ('Failed to fetch data. Error ('. $dbh->errno . ') ' . $dbh->error);
    error_log ($strQuery);
    print "<p class=\"Attn\" align=center>$ErrMsg</p>\n";
    exit(2);
	}
	while ($Row = $Result->fetch_assoc())
	{
    $vcAdminCat = $Row['vcCatName'];
    $iCatID = $Row['iCatID'];
    if ($WritePriv <=  $Priv)
    {
      print "<form method=\"POST\">\n";
      print "<tr valign=\"top\">\n";
      print "<td class=\"lbl\"><input type=\"hidden\" value=\"$iCatID\" name=\"iCatID\"> </td>\n";
      print "<td><input type=\"text\" value=\"$vcAdminCat\" name=\"txtAdminCat\" size=\"30\" ></td>\n";
      print "<td><input type=\"Submit\" value=\"Save\" name=\"btnSubmit\"></td>";
      print "<td><input type=\"Submit\" value=\"Delete\" name=\"btnSubmit\"></td>";
      print "</tr>\n";
      print "</form>\n";
    }
    else
    {
      print "$vcAdminCat<br>\n";
    }
	}
	print "</table>\n";
	print "</td>\n";
  print "<td>\n";
  print "</td>\n";
  print "<td valign=top>\n";
	print "<form method=\"POST\">\n";
	print "<table>\n";
	print "<tr>\n<td align = right class = lbl>Category: </td>\n";
	print "<td><input type=\"text\" name=\"txtAdminCat\" size=\"30\" ></td>\n</tr>\n";
	print "<tr><td colspan=2 align=center><input type=\"Submit\" value=\"Insert\" name=\"btnSubmit\"></td></tr>\n";
	print "</table>\n";
	print "</form>\n</td>\n</tr>\n</table>";

	require("footer.php");
?>
