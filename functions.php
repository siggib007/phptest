<?php

//Copyright © 2009,2015  Siggi Bjarnason.
//
//This program is free software: you can redistribute it and/or modify
//it under the terms of the GNU General Public License as published by
//the Free Software Foundation, either version 3 of the License, or
//(at your option) any later version.
//
//This program is distributed in the hope that it will be useful,
//but WITHOUT ANY WARRANTY; without even the implied warranty of
//MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
//GNU General Public License for more details.
//
//You should have received a copy of the GNU General Public License
//along with this program.  If not, see <http://www.gnu.org/licenses/>

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
    print "<center><img border=\"0\" src=\"$imgname\" align=\"center\" height=\"$ImgHeight\"></center>\n";
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

    if ($dbh->query ($strQuery))
    {
        $NumAffected = $dbh->affected_rows;
        print "<p class=\"MainText\">Database $type of $NumAffected record successful<br>\n";
        return TRUE;
    }
    else
    {
        $strError = "Database $type failed. Error (". $dbh->errno . ") " . $dbh->error . "\n";
        If ($dbh->errno =="1451")
        {
            print "\n<p class=\"error\">Unable to delete the selected value as it is still in use in other parts of the system</p>\n";
        }
        else
        {
            print "\n<p class=\"error\">Database $type failed: </p>\n";
            error_log($strError);
            error_log("SQL: $strQuery");
            if(mail("$SupportEmail","Automatic Error Report","$strError\n$strQuery",$FromEmail))
            {
                print "<p class=\"error\">We seem to be experiencing technical difficulties. We have been notified. " .
                "Please try again later. Thank you.</p>";
            }
            else
            {
                $strError = str_replace("\n","<br>\n",$strError);
                print "<p class=\"error\">We seem to be experiencing technical difficulties. " .
                        "Please send us a message at $SupportEmail with information about " .
                        "what you were doing.</p>";
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
        print "Database update successful<br>\n";
        return TRUE;
    }
    else
    {
        $strError = 'Database update failed. Error ('. $dbh->errno . ') ' . $dbh->error;

        print "\nDatabase update failed: \n";
        error_log($strError);
        error_log("SQL: $strQuery");
        if(mail("$SupportEmail","Automatic Error Report","$strError\n$strQuery",$FromEmail))
        {
                print "We seem to be experiencing technical difficulties. We have been notified. " .
                "Please try again later. Thank you.<br>";
        }
        else
        {
                $strError = str_replace("\n","<br>\n",$strError);
                print "We seem to be experiencing technical difficulties. " .
                                        "Please send us a message at $SupportEmail with information about " .
                                        "what you were doing.</p>";
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
            mail("$SupportEmail","Automatic Error Report",$strError,"From:$SupportEmail");
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

function format_phone_us($phone)
{
    // note: strip out everything but numbers
    $phone = preg_replace("/[^0-9]/", "", $phone);
    $length = strlen($phone);
    switch($length)
    {
        case 0:
            return 'notphone';
        case 7:
            return preg_replace("/([0-9]{3})([0-9]{4})/", "$1-$2", $phone);
            break;
        case 10:
            return preg_replace("/([0-9]{3})([0-9]{3})([0-9]{4})/", "($1) $2-$3", $phone);
            break;
        case 11:
            $phone=  substr($phone, 1);
            return preg_replace("/([0-9]{3})([0-9]{3})([0-9]{4})/", "($1) $2-$3", $phone);
            break;
        default:
            return $phone;
            break;
    }
}

function SendHTMLAttach ($strHTMLMsg, $FromEmail, $toEmail, $strSubject, $strFileName, $strAttach,$strAddHeader)
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
    if ($strAddHeader != "")
    {
        $headerparts = explode(":", $strAddHeader);
        $headers = $message->getHeaders();
        $headers->addTextHeader($headerparts[0],$headerparts[1]);
    }
    $mailer = Swift_Mailer::newInstance($transport);
    return $mailer->send($message);
}

?>