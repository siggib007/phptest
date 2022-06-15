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
  if (strtolower($GLOBALS['UseSSL'])=="true")
  {
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
    print "<p>Configured for fully encrypted connection</p>\n";
  }
  else
  {
    if (strtolower($GLOBALS['UseStartTLS'])=="true")
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
  if ($strAttach != "" and $strFileName != "")
  {
    $mail->addStringAttachment($strAttach, $strFileName); 
  }
  foreach ($strAddHeader as $header)
  {
    $mail->addCustomHeader($header);
  }
  // $mail->addAttachment('index.html', 'index.html');
  // $mail->addCustomHeader("X-MyTEst:JustBS");
  
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
print "Testing new email function using phpmailer<br>\nFirst from Geek<br>\n";
$strFileName = "Testing.txt";
$strAttach = "Wanted something quick and simple to verify that all the components where in place to make a PHP site driven by mySQL/MariaDB database so I put together this test site. The code grabs some env variables and displayes them as well as displays a table from a database. Run the following query in your database to generate the test table to be shown";
$strAddHeader = "X-Testing:This is single test header";
$arrname = array();
$arrname[] = "X-Testing:This is my test header";
$arrname[] = "X-Test2:This is my second header";
$arrname[] = "X-Test3:This is my third header";
$arrname[] = "X-Test4:This is my fourth header";

$strSubject = "Complex HTML test with picture, table and MD attachment";
$toEmail = "Joe User|joe.user@example.com";
$FromEmail = "Supergeek Admin|admin@supergeek.us";

$strHTMLMsg  = "";
$strHTMLMsg .= "<html>\n<head>\n<style>\n";
$strHTMLMsg .= "th {background-color: gray;color: white;}\n";
$strHTMLMsg .= "tr:nth-child(odd) {background-color: beige;}\n";
$strHTMLMsg .= "table, th, td {\n";
$strHTMLMsg .= "  border: 1px solid black;\n";
$strHTMLMsg .= "  border-collapse: collapse;\n";
$strHTMLMsg .= "}\n";
$strHTMLMsg .= "</style>\n</head>\n<body>\n";
$strHTMLMsg .= "<h1>Welcome!!!!</h1>\n";
$strHTMLMsg .= "This is a <i>supergeek test</i> where we are testing for custom headers<br>\n";
$strHTMLMsg .= "I hope it works out great<br>\n";
$strHTMLMsg .= "<p>Here is a cute picture for you</p>\n";
$strHTMLMsg .= "<img src='https://img.xcitefun.net/users/2015/01/371695,xcitefun-cute-animals-pictures-41.jpg' width=100% >\n";
$strHTMLMsg .= "</body>\n</html>\n";

$count = SendHTMLAttach ($strHTMLMsg, $FromEmail, $toEmail, $strSubject, $strFileName, $strAttach,$arrname);
print "Successfully sent $count recepients<br>\n";
print "<br>I'm all done<br>\n";
?>