<?php
	print "<html>\n";
	print "<head>\n";
	print "<meta http-equiv=\"Content-Type\" content=\"text/html; charset=iso-8859-1\">\n";
	print "<title>PHP Dev main page</title>\n";
	print "</head>\n";
	print "<body>\n";
	print "<center>\n";
	print "<h1>PHP Development, Test and production sites</h1>\n";
	require("DBCon.php");
	$strURL = $_SERVER["SERVER_NAME"];
	$DocRoot = $_SERVER["DOCUMENT_ROOT"];
	$ServerFQDN = strtoupper(php_uname($mode = "n"));
	$ServerFQDNParts = explode (".",$ServerFQDN);
	$Server = $ServerFQDNParts[0];
	$Protocol = $_SERVER["SERVER_PROTOCOL"];
	$ProtPart = explode('/',$Protocol);
	$OS = PHP_OS;
	$OSName = php_uname($mode = "s");
	$OSRelease = php_uname($mode = "r");
	$OSVersion = php_uname($mode = "v");
	$MachineType = php_uname($mode = "m");
	$phpVersion = phpversion();
	$ApacheVersion = apache_get_version();
	$pos = stripos($ApacheVersion, "php");
	if ($pos === false)
	{
		$ApacheVersion = "$ApacheVersion  and PHP Version $phpVersion";
	}
	$Query = "SELECT vcOS FROM tblservers where vcServerName = '$Server' and vcTechnology like '%php%' limit 1";
  if (!$Result = $dbh->query ($Query))
  {
		$strDBErr = "Failed to fetch data for main page  Error (" . $dbh->errno . ') ' . $dbh->error;
		error_log ($strDBErr);
		error_log ($strQuery);
		print "<p>$strDBErr</p>";
		exit(2);
  }
 	$Row = $Result->fetch_assoc();
	$OSType = $Row['vcOS'];
	print "<p>This PHP Development and Test sites is being hosted on server '$Server' via alias $ProtPart[0]://$strURL/</p>\n";
	print "$Server is running $OSType with $ApacheVersion.<br>\nThe document directory root is $DocRoot";//.\n";
	print "<p>Other Development and Test sites are:<br>\n";
	$Query = "SELECT * FROM tblDemoTEST;";
  if (!$Result = $dbh->query ($Query))
  {
		$strDBErr = "Failed to fetch data for main page  Error (" . $dbh->errno . ') ' . $dbh->error;
		error_log ($strDBErr);
		error_log ($strQuery);
		print "<p>$strDBErr</p>";
		exit(2);
  }
	while ($Row = $Result->fetch_assoc())
	{
		$ID = $Row['iIDnum'];
		$descr = $Row['vcDescr'];
		print "Row $ID has description of '$descr'\n";
	}
	print "</p>";
	print "</center>\n";
	print "</body>\n";
	print "</html>\n";
?>