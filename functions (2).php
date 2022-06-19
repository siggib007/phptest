<?php

function ShowErrHead()
{
    $ROOTPATH = $GLOBALS['ROOTPATH'];
    $HeadImg = $GLOBALS['HeadImg'];
    $CSSName = $GLOBALS['CSSName'];
    $ErrMsg = $GLOBALS['ErrMsg'];
    $ImgHeight = "150";
    $imgname = $ROOTPATH . $HeadImg;
    print "<!DOCTYPE HTML PUBLIC \"-//W3C//DTD HTML 4.01 Transitional//EN\"\n\"http://www.w3.org/TR/html4/loose.dtd\">";
    print "<HTML>\n<HEAD>\n<title>\nTechnical Difficulties\n</title>\n";
    print "<link href=\"$CSSName\" rel=\"stylesheet\" type=\"text/css\">\n</HEAD>\n";
    print "<body>\n";
    print "<div id=\"left\"></div>";
    print "<div id=\"right\"></div>";
    print "<div id=\"top\"></div>";
    print "<div id=\"bottom\"></div>";
    print "<div class=\"BlacktblHead\">";
    print "<TABLE border=\"0\" cellPadding=\"4\" cellSpacing=\"0\">\n";
    print "<TR>\n";
    print "<TD align=\"center\" vAlign=\"middle\">\n";
    print "<img border=\"0\" src=\"$imgname\" align=\"center\" height=\"$ImgHeight\">\n";
    print "</TD>\n";
    print "</TR>\n";
    print "</TABLE>\n</div>\n</div>\n";
    print "<p class=\"Header1\">Technical Difficulties</p>\n";
    print "<p class=\"Attn\" align=center>$ErrMsg</p>\n";
    exit;
}

function UpdateSQL ($strQuery,$type)
{
    $DefaultDB = $GLOBALS['DefaultDB'];
    $dbh = $GLOBALS['dbh'];
    $SupportEmail = $GLOBALS['SupportEmail'];
    $FromEmail = $GLOBALS['FromEmail'];

//	error_log("inside UpdateSQL. $strQuery");
    if ($dbh->query ($strQuery))
    {
        $NumAffected = $dbh->affected_rows;
        print("Database $type of $NumAffected record successful<br>\n");
        return TRUE;
    }
    else
    {
        $strError = "Database $type failed. Error (". $dbh->errno . ") " . $dbh->error . "\n";
        If ($dbh->errno =="1451")
        {
            print "\n<p>Unable to delete the selected value as it is still in use in other parts of the system</p>\n";
        }
        else
        {
            print "\nDatabase $type failed: \n";
            error_log($strError);
            error_log("SQL: $strQuery");
            if(EmailText("$SupportEmail","Automatic Error Report",$strError,$FromEmail))
            {
                print("We seem to be experiencing technical difficulties. We have been notified. " .
                "Please try again later. Thank you.<br>");
            }
            else
            {
                $strError = str_replace("\n","<br>\n",$strError);
                print("We seem to be experiencing technical difficulties. " .
                        "Please send us a message at $SupportEmail with information about " .
                        "what you were doing.</p>");
            }
        }
        return FALSE;
    }
}

function CallSP ($strQuery)
{
    $DefaultDB = $GLOBALS['DefaultDB'];
    $dbh = $GLOBALS['dbh'];
    $SupportEmail = $GLOBALS['SupportEmail'];
    $FromEmail = $GLOBALS['FromEmail'];

    if ($dbh->query ($strQuery))
    {
        print("Database update successful<br>\n");
        return TRUE;
    }
    else
    {
        $strError = 'Database update failed. Error ('. $dbh->errno . ') ' . $dbh->error;
        print "\nDatabase update failed: \n";
        error_log($strError);
        error_log("SQL: $strQuery");
        if(EmailText("$SupportEmail","Automatic Error Report",$strError,$FromEmail))
        {
                print("We seem to be experiencing technical difficulties. We have been notified. " .
                "Please try again later. Thank you.<br>");
        }
        else
        {
                $strError = str_replace("\n","<br>\n",$strError);
                print("We seem to be experiencing technical difficulties. " .
                                        "Please send us a message at $SupportEmail with information about " .
                                        "what you were doing.</p>");
        }
        return FALSE;
    }
}

function CallSPNoOut ($strQuery)
{
    $dbh = $GLOBALS['dbh'];

    if ($dbh->query ($strQuery))
    {
        return TRUE;
    }
    else
    {
        $strError = 'Database update failed. Error ('. $dbh->errno . ') ' . $dbh->error . "\n";

        error_log($strError);
        error_log($strQuery);
        return FALSE;
    }
}

function CleanSQLInput ($InVar)
{
    $InVar = str_replace("\\","",$InVar);
    $InVar = str_replace("'","\'",$InVar);
    $InVar = str_replace(";","",$InVar);
    return $InVar;
}

function CleanReg ($InVar)
{
    $InVar = strip_tags($InVar);
    $InVar = str_replace("\\","",$InVar);
    $InVar = str_replace("'","\'",$InVar);
    $InVar = str_replace('"',"",$InVar);
    return $InVar;
}

function SpamDetect ($InVar)
{
    $dbh = $GLOBALS['dbh'];
    $SupportEmail = $GLOBALS['SupportEmail'];
    $FromEmail = $GLOBALS['FromEmail'];
    $strRemoteIP = $GLOBALS['strRemoteIP'];
    $strURLRegx = "#(http://)|(a href)#i";
    if (preg_match($strURLRegx,$InVar))
    {
        $InVar = str_replace("'","\'",$InVar);
        $strQuery = "INSERT INTO tblSpamLog (vcIPAddress, vcContent) VALUES ('$strRemoteIP', '$InVar');";
        if (!$dbh->query ($strQuery))
        {
            $strError = 'Database insert failed. Error ('. $dbh->errno . ') ' . $dbh->error . "\n";
            $strError .= "$strQuery\n";
            error_log($strError);
            EmailText("$SupportEmail","Automatic Error Report",$strError,"From:$SupportEmail");
        }
        return TRUE;
    }
    else
    {
        return FALSE;
    }
}

function quarterByDate($date)
{
    return (int)floor(date('m', strtotime($date)) / 3.1) + 1;
}

function QuarterYear($date)
{
    $QNum = quarterByDate($date);
    $YearNum = date('Y', strtotime($date));
    return "Q$QNum $YearNum";
}
function Log_BackTrace ($BackTrace, $msg)
{
    error_log("");
    error_log("$msg starting debug backtrace");
    foreach($BackTrace as $key => $value)
    {
        $Level = intval($key)+1;
        error_log("Stack level $Level");
        foreach($value as $key => $value)
        {
            if (is_array($value))
            {
                $pre = $key;
                foreach($value as $key => $value)
                {
                        error_log("  $pre [$key] : $value");
                }
            }
            else
            {
                error_log("$key : $value");
            }
        }
}
    error_log("$msg ending debug backtrace");
}

function Log_Session ($msg)
{
    $Session=$_SESSION;
    error_log("");
    error_log("$msg start dump of SESSION array");
    foreach($Session as $key => $value)
    {
        if (is_array($value))
        {
            $pre = $key;
            foreach($value as $key => $value)
            {
                    error_log("$pre [$key] : $value");
            }
        }
        else
        {
            error_log("$key : $value");
        }
    }
    error_log("$msg ending dump of SESSION array");
}

function Log_Array ($array, $msg)
{
    error_log("");
    error_log("$msg start dump of array");
    foreach($array as $key => $value)
    {
        if (is_array($value))
        {
            $pre = $key;
            foreach($value as $key => $value)
            {
                error_log("$pre [$key] : $value");
            }
        }
        else
        {
            error_log("$key : $value");
        }
    }
    error_log("$msg ending dump of array");
}

function return_bytes($val)
{
    $val = trim($val);
    $last = strtolower($val[strlen($val)-1]);
    switch($last)
    {
        // The 'G' modifier is available since PHP 5.1.0
        case 'g':
            $val *= 1024;
        case 'm':
            $val *= 1024;
        case 'k':
            $val *= 1024;
    }
    return $val;
}

function with_unit($val)
{
    $Units[0]="";
    $Units[1]="KB";
    $Units[2]="MB";
    $Units[3]="GB";
    $Units[4]="TB";
		$val = trim($val);
    $tmp = $val/1024;
    $i=0;
//    print "i: $i<br>\n";
    while ($tmp > 1)
    {
    	$tmp = $val/1024;
    	if ($tmp > 1)
    	{
            $val=$tmp;
            $tmp = $val/1024;
            $i++;
      	}
      	else
      	{
            break;
      	}
    }
    return number_format($val, 2) . " " . $Units[$i];
}

function copyemz($file1,$file2)
{
    $contentx=@file_get_contents($file1);
    $openedfile = fopen($file2, "w");
    fwrite($openedfile, $contentx);
    fclose($openedfile);
    if ($contentx === FALSE)
    {
        $status=false;
    }
    else
    {
        $status=true;
    }
    return $status;
}

function codeToMessage($code)
{
    $MaxFileSize = ini_get('upload_max_filesize');
    switch ($code)
    {
        case UPLOAD_ERR_INI_SIZE:
            $message = "The uploaded file exceeds file size limit of $MaxFileSize";
            break;
        case UPLOAD_ERR_FORM_SIZE:
            $message = "The uploaded file exceeds the MAX_FILE_SIZE directive that was specified in the HTML form";
            break;
        case UPLOAD_ERR_PARTIAL:
            $message = "The uploaded file was only partially uploaded";
            break;
        case UPLOAD_ERR_NO_FILE:
            $message = "No file was uploaded";
            break;
        case UPLOAD_ERR_NO_TMP_DIR:
            $message = "Missing a temporary folder";
            break;
        case UPLOAD_ERR_CANT_WRITE:
            $message = "Failed to write file to disk";
            break;
        case UPLOAD_ERR_EXTENSION:
            $message = "File upload stopped by extension";
            break;
        default:
            $message = "Unknown upload error # $code";
            break;
    }
    return $message;
}

function SendHTMLAttach ($strHTMLMsg, $FromEmail, $toEmail, $strSubject, $strFileName, $strAttach)
{
    require_once 'swift/swift_required.php';
    $MailHost = $GLOBALS['MailHost'];
    $MailHostPort = $GLOBALS['MailHostPort'];
    $MailUser = $GLOBALS['MailUser'];
    $MailPWD = $GLOBALS['MailPWD'];

    $ToParts   = explode("|",$toEmail);
    $FromParts = explode("|",$FromEmail);
    $strTxtMsg = strip_tags($strHTMLMsg);
    $transport = Swift_SmtpTransport::newInstance($MailHost, $MailHostPort )
                 ->setUsername($MailUser)
                 ->setPassword($MailPWD)
                 ;
    $message = Swift_Message::newInstance();
    $message->setTo(array($ToParts[1]=>$ToParts[0]));
    $message->setSubject($strSubject);
    $message->setBody($strHTMLMsg,"text/html");
    $message->addPart($strTxtMsg,"text/plain");
    $message->setFrom(array($FromParts[1]=>$FromParts[0]));
    if ($strFileName !="")
    {
        $attachment = Swift_Attachment::newInstance()
                ->setFilename("$strFileName")
                ->setContentType("application/ics")
                ->setBody($strAttach)
                ;
        $message->attach($attachment);
    }
    $mailer = Swift_Mailer::newInstance($transport);
    return $mailer->send($message);
}

function EmailText($to,$subject,$message,$from)
{
    $strFileName = "";
    $strAttach = "";
    $from = str_replace("<", "|", $from);
    $from = str_replace(">", "", $from);
    $from = str_replace("From:", "", $from);
    if (stripos($from, "|")===FALSE)
    {
        $iPos = stripos($from, "@");
        $name = substr($from, 0,$iPos);
        $from = "$name|$from";
    }
    $to = str_replace("<", "|", $to);
    $to = str_replace(">", "", $to);
    if (stripos($to, "|")===FALSE)
    {
        $iPos = stripos($to, "@");
        $name = substr($to, 0,$iPos);
        $to = "$name|$to";
    }
    $message = str_replace("\n", "<br>\n", $message);
    $count = SendHTMLAttach ($message, $from, $to, $subject, $strFileName, $strAttach);
    if ($count>0)
    {
        return TRUE;
    }
    else
    {
        return FALSE;
    }
}
?>