<?php
require_once "Mail.php";
$from = "Sender Name <sender.name@sendersdomain.com>";
$to = "Recipient Name <recipient.name@recipientsdomain.com>";
$subject = " Subject Line Here: ";
$body = " any message you want ";
$host = "smtp.mailtrap.io:2525";
$username = "b9d2e009da0bcf";
$password = "3c3fe1c1ff409f";
$headers = array ('From' => $from,
'To' => $to,
'Subject' => $subject);
$smtp = Mail::factory('smtp',
array ('host' => $host,
'auth' => true,
'username' => $username,
'password' => $password));
$mail = $smtp->send($to, $headers, $body);
if (PEAR::isError($mail)) {
echo("<p>" . $mail->getMessage() . "</p>");
} else {
echo("<p>Message successfully sent!</p>");
}
?>