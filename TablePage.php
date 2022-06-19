<?php
	require("header.php");
	
	$strQuery = "SELECT * FROM tblPageTable WHERE iMenuID = '$iMenuID'";
	if (!$Result = $dbh->query ($strQuery))
	{
		error_log ('Failed to fetch data. Error ('. $dbh->errno . ') ' . $dbh->error);
		error_log ($strQuery);
		exit(3);
	}
	$NumAffected = $Result->num_rows;
	if ($NumAffected == 0)
	{
		print "<p class=\"Error\">Unknown Page ID $iMenuID</p>\n";
		exit;
	}		
	$Row = $Result->fetch_assoc();
	$PageHeader = $Row['vcPageHeader'];
	$ColumnList = $Row['vcColumnList'];
	$TableName  = $Row['vcTableName'];
	$FilterStr  = $Row['vcFilterStr'];
	$iLimit     = $Row['iLimit'];
	
	$strQuery = "SELECT $ColumnList FROM $TableName ";
	if ($FilterStr != "")
	{
		$strQuery .= "WHERE $FilterStr ";
	}
	$strQuery .= "LIMIT $iLimit;";
	
	print "<p class=Header1>$PageHeader</p>";

	print "<center>\n<table>\n<tr>\n";
	if (!$Result = $dbh->query ($strQuery))
	{
		$strError  = 'Failed to fetch data. Error ('. $dbh->errno . ') ' . $dbh->error . "<br>\n";
		$strError .= "$strQuery<br>\n";
		print $strError;
		exit(3);
	}
	$Row = $Result->fetch_assoc();
	$RowKeys = array_keys($Row);
	foreach ($RowKeys as $key)
	{
		print "<th>$key</th>\n";
	}
	print "</tr>\n<tr>";
	$RowValues = array_values($Row);
	foreach ($RowValues as $value)
	{
		print "<td>$value</td>";
	}
	print "</tr>\n";
	
	while ($Row = $Result->fetch_assoc())
	{
		print "<tr>";
		foreach($Row as $key => $value)
		{
			print "<td>$value</td>";
		}
		print "</tr>\n";
	}
	print "</table>\n</center>\n";
	$Result->free();
	
	require("footer.php");
?>