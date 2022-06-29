<?php
print "env is: ".$_ENV["USER"]."<br>\n";
print "(doing: putenv fred)<br>\n";
putenv("USER=fred");
print "env is: ".$_ENV["USER"]."<br>\n";
print "getenv is: ".getenv("USER")."<br>\n";
print "(doing: set _env barney)<br>\n";
$_ENV["USER"]="barney";
print "getenv is: ".getenv("USER")."<br>\n";
print "env is: ".$_ENV["USER"]."<br>\n";
phpinfo(INFO_ENVIRONMENT);
?>