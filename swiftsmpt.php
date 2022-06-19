<?php
require_once 'swift/swift_required.php';
print "<h1>testing Swiftmailer</h1>\n";
print "Create the mail transport configuration<br>\n";
$transport = Swift_SmtpTransport::newInstance('mail.supergeek.us', 587)
  ->setUsername('pf@majorgeek.us')
  ->setPassword('H&{=z]A6kV%i~kml')
  ;
print "Create the Multipart message<br>\n";
$message = Swift_Message::newInstance();
$message->setTo(array("siggi@bjarnason.us" => "Siggi Bjarnason"));
// $message->setTo(array("web-QS7gUp@mail-tester.com"));
$message->setCc("s@supergeek.us", "Supergeek");
$message->setSubject("Multipart email sent using Swift Mailer");
$message->setBody("<h1>Swiftmailer test</h1><p><b>You're</b> our <i>best</i> client ever, and ever.</p>","text/html");
$message->addPart("Swiftmailer test

You're our best client ever, and ever.","text/plain");
$message->setFrom("s@supergeek.us", "Supergeek");
#$message->attach(Swift_Attachment::fromPath("/home/siggib/test/X2-10GB-SR.pdf"));

print "Send the email<br>\n";
$mailer = Swift_Mailer::newInstance($transport);
$count=$mailer->send($message, $failedRecipients);
print "Successfully sent $count recepients<br>\n";
print "Show failed recipients<br>\n";
print_r($failedRecipients);
print "<br>done\n";
?>
