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
	print "<p>This PHP Development and Test sites is being hosted on server '$Server' via alias $ProtPart[0]://$strURL/</p>\n";
	print "$Server is running $OSType with $ApacheVersion.<br>\nThe document directory root is $DocRoot.<br>\n";
	print "we have xdebug config of '$xdebug'.<br>\n";
	print "</p>";
	print "<h2>module table</h2>\n";
	$Query = "SELECT * FROM tblModules;";
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
		$ID = $Row['iModID'];
		$descr = $Row['vcModuleName'];
		print "Module $ID is '$descr'<br>\n";
	}

	$arrFiles = array();
	$handle = opendir('.');
 
	if ($handle) {
  	while (($entry = readdir($handle)) !== FALSE) {
      $arrFiles[] = $entry;
    }
	}
 
	closedir($handle);
	//print "<p>".var_dump($arrFiles)."</P>";

	$hidefiles = array("DBCon.php","functions.php","index.php","secrets.php");

	print "<h1>Here are the files that are available to test with</h1>";
	foreach($arrFiles as $file)
	{
		if (substr($file,-3)=="php" and ! in_array($file,$hidefiles))
		{
			print "<p><a href='$file' target='_blank'>$file</a></P>";
		}
	}
	print "</center>\n";
	print "</body>\n";
	print "</html>\n";
?>