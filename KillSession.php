<?php
unset ($_SESSION["auth_username"]);
unset ($_SESSION["dtLogin"]);
unset ($_SESSION["iPrivLevel"]);
unset ($_SESSION["LastActivity"]);
unset ($_SESSION["LoginTime"]);
unset ($_SESSION["LogoutReason"]);
unset ($_SESSION["LogoutReasonCount"]);
unset ($_SESSION["UID"]);
unset ($_SESSION["Reason"]);
unset ($_SESSION["ReturnPage"]);
unset ($_SESSION["2FASecret"]);
$_SESSION = array();
unset ($_SESSION);
session_destroy();
?>