<?php
	$bSpam = "no";
	$bValid = "yes";
	$strReason = "";
	require("validate.php");

	//print "Include: $bIncludeName\n<br>";

	if ($bIncludeName == "on")
	{
		$bIncludeName = 1;
	}
	else
	{
		$bIncludeName = 0;
	}
	if (($bSpam == "no") and ($bValid == "yes"))
	{
		require_once("header.php");
		$strEmail2 = str_replace("'","",$strEmail);
		$strQuery = "select count(*) iRowCount from tblUsers where vcEmail = '$strEmail2'";
		if (!$Result = $dbh->query ($strQuery))
		{
			error_log ('Failed to fetch data. Error ('. $dbh->errno . ') ' . $dbh->error);
			error_log ($strQuery);
			exit(2);
		}
		$Row = $Result->fetch_assoc();
		if ($Row['iRowCount']==0)
		{
			$StrMsg = "Thank you for registering for an account on $strHost. You provided the following information during signup:\n\n";
			$StrMsg .= "$strName\n$strAddr1\n$strAddr2\n$strCity, $strState $strZip\n$strCountry\n$strEmail\n$strPhone\n";
			if ($bIncludeName == 0)
			{
				$StrMsg .= "You requested not to be include in email anouncements. This means you will only get notices required by the bylaws via postal mail.\n\n";
				$StrMsg .= "Please log back into your account if you wish to change this.\n\n";
			}
			$String = "This is a very long string that will be used | as the basis in the password generation routine.";
			$EString = md5($String);
			$StringLen = strlen($EString);
			srand((double) microtime() * 1000000);
			$Begin = rand(0,($StringLen - $PWDLength -1));
			$Password = substr($EString, $Begin, $PWDLength);
			$HowMany = count($strNameParts);
			$LastIndex = $HowMany - 1;
			$FName = $strNameParts[0];
			$LName = $strNameParts[$LastIndex];
			$strUID = substr($FName,0,1).substr($LName,0,9);
			$strUID = str_replace("'","",$strUID);
			$strUID = str_replace("\\","",$strUID);
			$strUID = str_replace("'","\'",$strUID);
			$Password = str_replace("\\","",$Password);
			$Password = str_replace("'","\'",$Password);
			$strUID = str_replace("\\","",$strUID);
			$Password = str_replace("\\","",$Password);

			$strQuery = "select count(*) iRowCount from tblUsers where vcUID = '$strUID'";
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
				$strQuery = "select count(*) iRowCount from tblUsers where vcUID = '$strUID2'";
				if (!$Result2 = $dbh->query ($strQuery))
				{
					error_log ('Failed to fetch data. Error ('. $dbh->errno . ') ' . $dbh->error);
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
			$StrMsg2 .= "and confirm your account to activate it.\n";
			//print $StrMsg;
			$salt = substr($strUID , 0, 4) ;
			$PWD = crypt($Password , $salt);
			$strQuery = "INSERT INTO tblUsers " .
						"(vcName, vcEmail, vcCell, vcAddr1, vcAddr2, vcCity, vcState, vcZip, vcCountry, " .
						"vcUID, vcPWD, dMailSent, tMailSent, bInclude)" .
						"VALUES ('$strName', '$strEmail', '$strCell', '$strAddr1', '$strAddr2', '$strCity', '$strState', '$strZip', " .
						"'$strCountry', '$strUID' , '$PWD', CURDATE(), CURTIME(), $bIncludeName); ";
			$bUpdate = UpdateSQL ($strQuery,"insert");
			if ($bUpdate)
			{
				if ($OSEnv == "win")
				{
					$toEmail = "$strEmail";
					$fromEmail = "From:$eFromAddr";
				}
				else
				{
					$toEmail = "\"$strName\" <$strEmail>";
					$fromEmail = "From:$eFromName <$eFromAddr>";
				}
				if ($strEmail)
				{
					$StrMsg = str_replace("\\'","'",$StrMsg);
					$StrMsg = str_replace("&quot;",'"',$StrMsg);
					if(mail($toEmail,"Thanks for registering at $strHost",$StrMsg . $StrMsg2,$fromEmail))
					{
						print "<p class=\"MainText\">Your signup was successful and an confirmation email was sent to $strEmail." .
							" If you entered the wrong email address just registered again with the correct email." .
							" This confirmation email contains a username and password. Please use this user name and password to login and view/change your info." .
							" Please login to confirm your account.</p>\n<p>Please make sure you check your spam or junk folder for email from $FromEmail</p>\n";
					}
					else
					{
						print "<p class=\"Error\">Signup was successful but Failed to send the confirmation email</p>";
						print "<p class=\"Error\">Please notify us at $SupportEmail, " .
								"including the email address you used to sign up with.</p>\n";
					}
					unset($_SESSION["POSTArray"]);
				}
				else
				{
					print "<p class=\"Error\">Email address wasn't provided thus can't send confirmation email</p>";
				}
			}
			mail("$SupportEmail","New registration at $strHost",$StrMsg,$fromEmail);
		}
		else
		{
			print "The email address you provided is already registered. Please log in to view/change your information. " .
				"If you have forgot your username or password you can have the systerm email it to you.";
		}
	}
	else
	{
		if ($bSpam == "Yes")
		{
			$strContent = str_replace("\\","",$strContent);
			$strContent = str_replace("'","\'",$strContent);
			$strQuery = "INSERT INTO `tblSpamLog` (`vcIPAddress`, `vcContent`) VALUES ('$strRemoteIP', '$strContent');";
			if ($dbh->query ($strQuery))
				{
					//print "$Database insert successful<br>\n";
				}
			else
				{
					$strError = "Database $type failed. Error (". $dbh->errno . ") " . $dbh->error . "\n";
					$strError .= "$strQuery\n";
					error_log($strError);
					mail("$SupportEmail","Automatic Error Report",$strError,$fromEmail);
				}
			$strComment = "Your registration has been flagged as spam as it contained $strReason, and was not saved. Your IP has been logged";
			$_SESSION["SpamComment"]= $strComment;
//			print "<p class=\"Error\">$strComment</p>";
		}
		if ($bValid == "no")
		{
			$strComment = "Required information missing. $strReason Your registration was not saved, please resubmit.";
			$_SESSION["InvalidComment"]= $strComment;
//			print "<p class=\"Error\">$strComment</p>";
		}
		$_SESSION["ReasonArray"]= $arrReason;
		header("Location: register.php");
	}

	require("footer.php");
?>