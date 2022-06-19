<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<title>Signup Confirmation</title>
</head>
<?php
	require("header.php");
	require("DBCon.php");
	//$strOptTop = "You provided the following:\n";
	$bUpdate = "n/a";
	$strName = substr(trim($_POST['txtName']),0,49);
	$strNameParts = explode(' ',$strName);
	$strEmail = substr(trim($_POST['txtEmail']),0,49);
	$strAddr1 = substr(trim($_POST['txtAddr1']),0,49);
	$strAddr2 = substr(trim($_POST['txtAddr2']),0,49);
	$strCity = substr(trim($_POST['txtCity']),0,49);
	$strState = substr(trim($_POST['cmbState']),0,49);
	$strZip = substr(trim($_POST['txtZip']),0,10);
	$strCountry = substr(trim($_POST['cmbCountry']),0,99);
	$iCount = substr(trim($_POST['txtCount']),0,9);
	$strAttend = trim($_POST['rAttend']);
	$strComment = substr(trim($_POST['txtComment']),0,999);
	$bIncludeName = $_POST['chkInclude'];
	//print "Include: $bIncludeName\n<br>";
	$strEmail = str_replace("'","",$strEmail);
	$bSpam = "no";
	if (eregi("(http://)|(a href)",$strComment))
	{
		$bSpam = "Yes";
		$strReason = "URL in Comments";
		$strContent = $strComment;
	}
	else if (eregi("(http://)|(a href)",$strName))
	{
		$bSpam = "Yes";
		$strReason = "URL in Name";
		$strContent = $strName;
	}
	else if ($strState == "Please Select State")
	{
		$bSpam = "invalid";
		$strReason = "failed to select proper value for State";
		$strContent = "";

	}
	else if (!$strName)
	{
		$bSpam = "invalid";
		$strReason = "failed to provide name";
		$strContent = "";
	}
	else if (!$strEmail)
	{
		$bSpam = "invalid";
		$strReason = "failed to provide email address";
		$strContent = "";
	}

	else if (!$strCity)
	{
		$bSpam = "invalid";
		$strReason = "failed to provide city";
		$strContent = "";
	}

	if ($bIncludeName == "on")
	{
		$bIncludeName = 1;
	}
	else
	{
		$bIncludeName = 0;
	}
	if (is_numeric($iCount))
	{
		//print "Attendance Count: $iCount<br>\n";
	}
	else
	{
		//print "Invalid count, setting to 0<br>\n";
		$iCount=0;
	}
	if (($strAttend == "No") and ($iCount > 0))
	{
		print "You indicated you were NOT coming with $iCount guests, this doesn't make sense I changed the status to yes. ";
		print "If this isn't correct please log into your account and correct it.";
		$strAttend = "Yes";
	}
	if (($strAttend == "Yes") and ($iCount < 1))
	{
		$iCount = 1;
	}
	//print "Include Post Check: $bIncludeName\n<br>";

	if ($bSpam == "no")
	{
		$strEmail2 = str_replace("'","",$strEmail);
		$strQuery = "select count(*) iRowCount from tblrsvp where vcEmail = '$strEmail2'";
		if (!$Result = $dbh->query ($strQuery))
		{
			error_log ('Failed to fetch data. Error ('. $dbh->errno . ') ' . $dbh->error);
			error_log ($strQuery);
			exit(2);
		}
		$Row = $Result->fetch_assoc();		if ($Row['iRowCount']==0)
		{
			$StrMsg = "Thank you for RSVPing for our wedding on $strHost. You provided the following information during signup:\n\n";
			$StrMsg .= "$strName\n$strAddr1\n$strAddr2\n$strCity, $strState $strZip\n$strCountry\n$strEmail\n\n";
			if ($bIncludeName == 0)
			{
				$StrMsg .= "You requested not to include your name on the public guest list and as such will not be listed on the \"Who's Coming\" page.\n\n";
			}
			$StrMsg .= "Your RSVP as $strAttend with a guest count of $iCount including your self.\n";
			$StrMsg .= "You also entered the following comment\n$strComment\n";
			$String = "This is a very long string that will be used | as the basis in the password generation routine.";
			$EString = md5($String);
			$StringLen = strlen($EString);
			srand((double) microtime() * 1000000);
			$Length = 6; //rand(8,16);
			$Begin = rand(0,($StringLen - $Length -1));
			$Password = substr($EString, $Begin, $Length);
			$HowMany = count($strNameParts);
			$LastIndex = $HowMany - 1;
			$FName = $strNameParts[0];
			$LName = $strNameParts[$LastIndex];
			$strUID = substr($FName,0,1).substr($LName,0,9);
			$strUID = str_replace("'","",$strUID);
			$strQuery = "select count(*) iRowCount from tblrsvp where vcUID = '$strUID'";
			if (!$Result2 = $dbh->query ($strQuery))
			{
				error_log ('Failed to fetch data. Error ('. $dbh->errno . ') ' . $dbh->error);
				error_log ($strQuery);
				exit(2);
			}
			$Row2 = $Result2->fetch_assoc();
			$RowCount = $Row2['iRowCount'];
			//print ("$strQuery<br>\niRowCount=$RowCount\n<br>");
			$i = 1;
			$strUID2 = $strUID;
			while ($RowCount>0)
			{
				//print "userid: $strUID2 already in use trying";
				$strUID2 = $strUID.$i;
				//print " $strUID2.<br>\n";
				$strQuery = "select count(*) iRowCount from tblrsvp where vcUID = '$strUID2'";
				if (!$Result2 = $dbh->query ($strQuery))
				{
					error_log ('Failed to fetch Link Detail data. Error ('. $dbh->errno . ') ' . $dbh->error);
					error_log ($strQuery);
					exit(2);
				}
				$Row2 = $Result2->fetch_assoc();
				$RowCount = $Row2['iRowCount'];
				//print ("$strQuery<br>\niRowCount=$RowCount\n<br>");
				$i += 1;
			}
			$strUID = $strUID2;
			$strURL = "http://" . $strHost . $ROOTPATH . "Login.php";
			$StrMsg2 = "\n\nYour username is $strUID and your Password is: $Password\n\n";
			$StrMsg2 .= "Please login into your account at $strURL ";
			$StrMsg2 .= "and confirm your RSVP so that we know we've got your correct email address and other info.\n";
			//print $StrMsg;
			$strComment = str_replace("\\","",$strComment);
			$strComment = str_replace("'","\'",$strComment);
			$strName = str_replace("\\","",$strName);
			$strName = str_replace("'","\'",$strName);
			$strAddr1 = str_replace("\\","",$strAddr1);
			$strAddr1 = str_replace("'","\'",$strAddr1);
			$strAddr2 = str_replace("\\","",$strAddr2);
			$strAddr2 = str_replace("'","\'",$strAddr2);
			$strCity = str_replace("\\","",$strCity);
			$strCity = str_replace("'","\'",$strCity);
			$strState = str_replace("'","\'",$strState);
			$strZip = str_replace("\\","",$strZip);
			$strZip = str_replace("'","\'",$strZip);
			$strCountry = str_replace("'","\'",$strCountry);
			$strUID = str_replace("\\","",$strUID);
			$strUID = str_replace("'","\'",$strUID);
			$Password = str_replace("\\","",$Password);
			$Password = str_replace("'","\'",$Password);
			$strQuery = "INSERT INTO tblrsvp " .
						"(vcName, vcEmail, vcAddr1, vcAddr2, vcCity, vcState, vcZip, vcCountry, " .
						"iNumGuests, vcAttend, vcComment, vcUID, vcPWD, dMailSent, tMailSent, bInclude)" .
						"VALUES ('$strName', '$strEmail', '$strAddr1', '$strAddr2', '$strCity', '$strState', '$strZip', " .
						"'$strCountry', '$iCount', '$strAttend', '$strComment', '$strUID' , '$Password', CURDATE(), CURTIME(), $bIncludeName); ";
			$bUpdate = UpdateSQL ($strQuery,"update");
			$strComment = str_replace("\\","",$strComment);
			$strName = str_replace("\\","",$strName);
			$strAddr1 = str_replace("\\","",$strAddr1);
			$strAddr2 = str_replace("\\","",$strAddr2);
			$strCity = str_replace("\\","",$strCity);
			$strZip = str_replace("\\","",$strZip);
			$strUID = str_replace("\\","",$strUID);
			$Password = str_replace("\\","",$Password);
			if ($bUpdate)
			{
				if ($strEmail)
				{
					if(EmailText("$strEmail","Thanks for registering at $strHost",$StrMsg . $StrMsg2,"From:$SupportEmail"))
					{
						print("<p class=\"MainText\">Your signup was successful and an confirmation email was sent to $strEmail." .
							" This confirmation email contains a username and password. Please use this user name and password to login and view/change your info." .
							" Please login and confirm your data was received correctly.</p>\n");
					}
					else
					{
						print("<p class=\"Error\">Signup was successful but Failed to send the confirmation email</p>");
						print("<p class=\"Error\">Please notify us at $SupportEmail, " .
								"including the email address you used to sign up with.</p>\n");
					}
				}
				else
				{
					print("<p class=\"Error\">Email address wasn't provided thus can't send confirmation email</p>");
				}
			}
			EmailText("$SupportEmail","New registration at $strHost",$StrMsg,"From:$SupportEmail");
		}
		else
		{
			print("The email address you provided is already registered. Please log in to view/change your information. " .
				"If you have forgot your username or password you can have the systerm email it to you.");
		}
	}
	else
	{
		if ($bSpam == "Yes")
		{
			$strContent = str_replace("\\","",$strContent);
			$strContent = str_replace("'","\'",$strContent);
			$strQuery = "INSERT INTO `siggib_smb`.`tblSpamLog` (`vcIPAddress`, `vcContent`) VALUES ('$strRemoteIP', '$strContent');";
			if ($dbh->query ($strQuery))
				{
					//print("$Database insert successful<br>\n");
				}
			else
				{
					$strError = 'Database insert failed. Error ('. $dbh->errno . ') ' . $dbh->error . "\n";
					$strError .= "$strQuery\n";
					error_log($strError);
					EmailText("$SupportEmail","Automatic Error Report",$strError,"From:$SupportEmail");
				}
			$strComment = "Your registration has been flagged as spam as it contained $strReason, and was not saved. Your IP has been logged";
			print("<p class=\"Error\">$strComment</p>");
		}
		if ($bSpam == "invalid")
		{
			$strComment = "Required information missing. You $strReason. Your registration was not saved, please resubmit.";
			print("<p class=\"Error\">$strComment</p>");
		}
	}
	require("footer.php");
?>

</body>
</html>
