<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

$MailHost = getenv("EMAILSERVER");
$MailHostPort = getenv("EMAILPORT");
$MailUser = getenv("EMAILUSER");  
$MailPWD = getenv("EMAILPWD");
$UseSSL = getenv("USESSL");
$UseStartTLS = getenv("USESTARTTLS");

function StripHTML ($content)
{
  $unwanted = ['style','script'];
  foreach ( $unwanted as $tag ) 
  {
    $content = preg_replace( "/(<$tag>.*?<\/$tag>)/is", '', $content );
  }
  unset( $tag );
  $content = strip_tags($content);
  return trim($content);
}

function SendHTMLAttach ($strHTMLMsg, $FromEmail, $toEmail, $strSubject, $strFileName, $strAttach, $strAddHeader, $strFile2Attach = "")
{

  require_once 'PHPMailer/Exception.php';
  require_once 'PHPMailer/PHPMailer.php';
  require_once 'PHPMailer/SMTP.php';

  $ToParts   = explode("|",$toEmail);
  $FromParts = explode("|",$FromEmail);
  $strTxtMsg = StripHTML($strHTMLMsg);

  // create a new PHPMailer object
  $mail = new PHPMailer();

  // configure an SMTP Settings
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
  
  // Construct email message
  $mail->setFrom($FromParts[1], $FromParts[0]);
  $mail->addAddress($ToParts[1], $ToParts[0]);
  $mail->Subject = $strSubject;
  $mail->isHTML(TRUE);
  $mail->Body = $strHTMLMsg;
  $mail->AltBody = $strTxtMsg;

  // add string attachment
  if ($strAttach != "" and $strFileName != "")
  {
    $mail->addStringAttachment($strAttach, $strFileName); 
  }

  // Process any custom headers
  if (is_array($strAddHeader))
  {
    foreach ($strAddHeader as $header)
    {
      $mail->addCustomHeader($header);
    }
  }
  else 
  {
    $mail->addCustomHeader($strAddHeader);
  }

  // Attach file attachment
  if ($strFile2Attach != "")
  {
    $mail->addAttachment($strFile2Attach);
  }
  
  // send the message
 
  if(!$mail->send())
  {
      return "Message could not be sent. Mailer Error: " . $mail->ErrorInfo;
  } else 
  {
      return "Message has been sent";
  }
}
print "<center>\n";
print "<h1>This is only a test</h2>\n";
print "</center>";
print "Testing new email function using phpmailer<br>\nSending email through $MailHost. Use full SSL/TLS: $UseSSL. Use StartTLS: $UseStartTLS<br>\n";
$strFileName = "Testing.txt";
$strAttach = "Wanted something quick and simple to verify that all the components where in place to make a PHP site driven by mySQL/MariaDB database so I put together this test site. The code grabs some env variables and displayes them as well as displays a table from a database. Run the following query in your database to generate the test table to be shown";
$strAddHeader = "X-Testing:This is double test header;X-test2:this is the second";
$arrname = array();
$arrname[] = "X-Testing:This is my test header";
$arrname[] = "X-Test2:This is my second header";
$arrname[] = "X-Test3:This is my third header";
$arrname[] = "X-Test4:This is my fourth header";

$strSubject = "Complex HTML test with picture and txt attachment";
$toEmail = "Sigg Bjarnason|siggi@bjarnason.us";
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
$strHTMLMsg .= "<p>Here is a cute picture for you, which can only see if you are HTML capable";
$strHTMLMsg .= " and have remote pictures turned on as it is a remote inline HTML picture</p>\n";
$strHTMLMsg .= "<img src='https://img.xcitefun.net/users/2015/01/371695,xcitefun-cute-animals-pictures-41.jpg' width=100% >\n";
$strHTMLMsg .= "</body>\n</html>\n";

$resp = SendHTMLAttach ($strHTMLMsg, $FromEmail, $toEmail, $strSubject, $strFileName, $strAttach, $arrname, '/var/log/alternatives.log');
print "<p>$resp</p>\n";
print "<p>I'm all done at " . date(DATE_RFC1123) . "</p>\n";
?>