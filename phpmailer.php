<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

$MailHost = getenv("EMAILSERVER");
$MailHostPort = getenv("EMAILPORT");
$MailUser = getenv("EMAILUSER");  
$MailPWD = getenv("EMAILPWD");
$UseSSL = getenv("USESSL");
$UseStartTLS = getenv("USESTARTTLS");

function SendHTMLAttach ($strHTMLMsg, $FromEmail, $toEmail, $strSubject, $strFileName, $strAttach,$strAddHeader)
{

  require 'PHPMailer/Exception.php';
  require 'PHPMailer/PHPMailer.php';
  require 'PHPMailer/SMTP.php';

  $ToParts   = explode("|",$toEmail);
  $FromParts = explode("|",$FromEmail);
  $strTxtMsg = strip_tags($strHTMLMsg);
  // create a new object
  $mail = new PHPMailer();
  // configure an SMTP
  $mail->isSMTP();
  $mail->Host = $GLOBALS['MailHost'];
  $mail->Port = $GLOBALS['MailHostPort'];
  $mail->SMTPAuth = true;
  $mail->Username = $GLOBALS['MailUser'];
  $mail->Password = $GLOBALS['MailPWD'];
  if ($GLOBALS['UseSSL'])
  {
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
    print "<p>Configured for fully encrypted connection</p>\n";
  }
  else
  {
    if ($GLOBALS['UseStartTLS'])
    {
      $mail->SMTPSecure = 'tls';
      print "<p>Configured for StartTLS</p>\n";
    }
    else
    {
      $mail->SMTPSecure = "";
      print "<p>Configured for no security</p>\n";
    }
  }
  
  $mail->setFrom($FromParts[1], $FromParts[0]);
  $mail->addAddress($ToParts[1], $ToParts[0]);
  $mail->Subject = $strSubject;
  // Set HTML
  $mail->isHTML(TRUE);
  $mail->Body = $strHTMLMsg;
  $mail->AltBody = $strTxtMsg;
  // add attachment
  // $mail->addAttachment('//confirmations/yourbooking.pdf', 'yourbooking.pdf');
  // send the message
  if(!$mail->send())
  {
      print "<p>Message could not be sent. ";
      print "Mailer Error: " . $mail->ErrorInfo . "</p>\n";
  } else 
  {
      echo "<p>Message has been sent</p>\n";
  }
}
print "<center>\n";
print "<h1>This is only a test</h2>\n";
print "</center>";
print "Testing new email function using swiftmail<br>\nFirst from Geek<br>\n";
$strHTMLMsg = "This is a test of the swift mail system with speacial headers, remote image and all.<br><img src=\"http://www.studio-b-dance.com/img/StudioB320.jpg\" height=\"100\"/>";
$FromEmail = "Geek Web Master|web@supergeek.us";
$toEmail = "Siggi Bjarnason|siggi@bjarnason.us";
$strSubject = "Geeky Sendmail function test with special headers";
$strFileName = "";
$strAttach = "";
$strAddHeader = "X-Testing:This is my test header";
$count = SendHTMLAttach ($strHTMLMsg, $FromEmail, $toEmail, $strSubject, $strFileName, $strAttach,$strAddHeader);
print "Successfully sent $count recepients<br>\n";
print "<br>I'm all done<br>\n";
?>