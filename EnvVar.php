<?php
$DBServerName = getenv("MYSQL_HOST");
$UID = getenv("MYSQL_USER");
$PWD = getenv("MYSQL_PASSWORD");
$MailUser = getenv("EMAILUSER");
$MailPWD = getenv("EMAILPWD");
$MailHost = getenv("EMAILSERVER");
$MailHostPort = getenv("EMAILPORT");
$UseSSL = getenv("USESSL");
$UseStartTLS = getenv("USESTARTTLS");
$TwilioToken = getenv("TWILIO_KEY");
$FromNumber = getenv("TWILIO_NUM");
$TwilioSID = getenv("TWILIO_SID");
$DevEnvironment = getenv("DEVENV");

?>