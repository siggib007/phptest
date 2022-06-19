<?php

	$bSpam = "no";
	$bValid = "yes";
	$strReason = "";
	$strComment = "";

	require ("validate.php");
	$strUID = substr(trim($_POST['txtUID']),0,19);
	$strOUID = substr(trim($_POST['txtOUID']),0,19);
	$iUserID = trim($_POST['iUserID']);
	$Password = substr(trim($_POST['txtPWD']),0,19);
	$PWDConf = substr(trim($_POST['txtPWDConf']),0,19);

	$dtNow = date("Y-m-d H:i:s");
	$uuid = uniqid(mt_rand(), true);
	$strURL = $strURL . "EmailUpdate.php?$uuid";

	$strUID = str_replace("\\","",$strUID);
	$Password = str_replace("\\","",$Password);
	$PWDConf = str_replace("\\","",$PWDConf);
	$strUID = str_replace("'","\'",$strUID);
	$Password = str_replace("'","",$Password);
	$strUID = str_replace("\\","",$strUID);
	$Password = str_replace("\\","",$Password);

	$strOEmail = substr(trim($_POST['txtOEmail']),0,49);

	$iLevel = 0;

	if ($OSEnv == "win")
	{
		$toEmail = "$strEmail";
		$toOldEmail = "$strOEmail";
		$fromEmail = "From:$eFromAddr";
	}
	else
	{
		$toEmail = "\"$strName\" <$strEmail>";
		$toOldEmail = "\"$strName\" <$strOEmail>";
		$fromEmail = "From:$eFromName <$eFromAddr>";
	}

	if ($bSpam == "Yes")
	{
		$strContent = str_replace("\\","",$strContent);
		$strContent = str_replace("'","\'",$strContent);
		$strQuery = "INSERT INTO tblSpamLog (vcIPAddress, vcContent) VALUES ('$strRemoteIP', '$strContent');";
		if (!$dbh->query ($strQuery))
		{
			$strError = 'Database insert failed. Error ('. $dbh->errno . ') ' . $dbh->error . "\n";
			$strError .= "$strQuery\n";
			error_log($strError);
			EmailText("$SupportEmail","Automatic Error Report",$strError,"From:$SupportEmail");
		}
		$strComment = "Your update has been flagged as spam as it contained $strReason, and was not saved. Your IP has been logged";
		$_SESSION["Message"] = $strComment;
		header("Location: myprofile.php" );
		exit;
	}
	if ($bValid == "no")
	{
		$strComment = "Required information missing. $strReason \n<br>Your update was not saved, please resubmit.";
		$_SESSION["Message"] = $strComment;
		header("Location: myprofile.php" );
		exit;
	}

	$strUID = str_replace("'","",$strUID);
	$strQuery = "select count(*) iRowCount from tblUsers where vcUID = '$strUID' and iUserID <> $iUserID";
	if (!$Result = $dbh->query ($strQuery))
	{
		error_log ('Failed to fetch data. Error ('. $dbh->errno . ') ' . $dbh->error);
		error_log ($strQuery);
		exit(2);
	}
	$Row = $Result->fetch_assoc();
	$RowCount = $Row['iRowCount'];
	$i = 1;
	$strUID2 = $strUID;
	while ($RowCount>0)
	{
		$strUID2 = $strUID.$i;
		$strQuery = "select count(*) iRowCount from tblUsers where vcUID = '$strUID2' and iUserID <> $iUserID";
		if (!$Result2 = $dbh->query ($strQuery))
		{
			error_log ('Failed to data. Error ('. $dbh->errno . ') ' . $dbh->error);
			error_log ($strQuery);
			exit(2);
		}
		$Row2 = $Result2->fetch_assoc();
		$RowCount = $Row2['iRowCount'];
		$i += 1;
	}

	if ($strUID2 == $strUID)
	{
		$salt = substr($strUID , 0, 4) ;
		$PWD = crypt($Password , $salt);
	}
	else
	{
		$salt = substr($strOUID , 0, 4) ;
		$PWD = crypt($Password , $salt);
	}

	if ($iUserID)
	{
		if ($strEmail <> $strOEmail)
		{
			$strQuery = "INSERT INTO tblemailupdate (iClientID, vcGUID, vcNewEmail, vcReqIPAdd, dtTimeStamp)"
					. " VALUES ($iUserID, '$uuid', '$strEmail', '$strRemoteIP', '$dtNow');";
			if ($dbh->query ($strQuery))
				{
					$strQuery = "SELECT iChangeID FROM tblemailupdate WHERE vcGUID= '$uuid'";
					if (!$Result = $dbh->query ($strQuery))
					{
						error_log ('Failed to data. Error ('. $dbh->errno . ') ' . $dbh->error);
						error_log ($strQuery);
						exit(2);
					}
					$Row = $Result->fetch_assoc();
					$iChangeNum = $Row['iChangeID'];
					$strEmailText = "Someone (hopefully you) requested that the email address we have on file for $strName " .
						"be changed to $strEmail. If you didn't make this request please notify us at $SupportEmail referncing changeID $iChangeNum.\n";
					EmailText($toOldEmail,"Change of email address has been requested",$strEmailText,$fromEmail);
					$strEmailText = "Before your request to update your email address can be processed you need " .
						"confirm your request by going to $strURL\nIf you no longer wish to make the change " .
						"or you didn't request this, just delete this message as no action will be taken without you visiting that page.";
					EmailText($toEmail,"Change of email address has been requested",$strEmailText,$fromEmail);
					$strComment .="Before we can process your change of email address you need to confirm the new email. " .
						"Please check your new email and follow the instructions in it.<br>\nIf you don't receive the mail " .
						"please contact $SupportEmail with changeid $iChangeNum.<br>\nPlease note your IP has been recorded.<br>\n";
					$_SESSION["Message"] = $strComment;
					header("Location: myprofile.php" );
				}
			else
				{
					$strError = "Database update failed. Error (". $dbh->errno . ") " . $dbh->error . "\n";
					$strError .= "$strQuery\n";
					error_log($strError);
					if(EmailText("$SupportEmail","Automatic Error Report",$strError,$fromEmail))
						{
							$strComment .= "We seem to be experiencing technical difficulties as we were unable to update your email. " .
								  "Other items are processed seperately and may or may not have succeeded. " .
				 				  "We have been notified. Please try again later. Thank you.<br>\n";
						}
					else
						{
							$strComment .= "We seem to be experiencing technical difficulties as we were unable to update your email. " .
								  "Other items are processed seperately and may or may not have succeeded. " .
								  "Please let us know by sending the following error to us at ".
								  "$SupportEmail<br>\n<p>$strError<\p>\nPlease try again later. Thank you.";
						}
				}
		}
		$StrMsg = "$strName has updated their profile. Their new information is:\n";
		$StrMsg .= "$strName\n$strAddr1\n$strAddr2\n$strCity, $strState $strZip\n$strCountry\n$strEmail\n\n";

		$strQuery = "update tblUsers set vcName = '$strName', vcPhone = '$strPhone', vcAddr1 = '$strAddr1', vcAddr2 = '$strAddr2', " .
				 "vcCity = '$strCity', vcState = '$strState', vcZip = '$strZip', vcCountry = '$strCountry', " .
				 "dtUpdated = '$dtNow' ";

		if ($Password =='' and $strOUID == $strUID)
		{
			$strQuery .= " where iUserID='$iUserID'";
		}

		if ($Password =='' and $strOUID != $strUID and $strUID == $strUID2)
		{
			$strQuery .= " where iUserID='$iUserID'";
			$strComment .= "<p>Please provide password to change your user name. Everything else has been updated, except for your username.</p>";
		}

		if ($Password == $PWDConf and $strUID == $strUID2 and $Password != '')
		{
			$strQuery .= ", vcUID = '$strUID', vcPWD = '$PWD' where iUserID='$iUserID'";
		}

		if ($Password != $PWDConf and $strUID == $strUID2)
		{
			$strQuery .= " where iUserID='$iUserID'";
			$strComment .= "<p>Passwords do not match so password and username was not changed. Everything else has been updated.<br>\n";
		}
		if ($Password == $PWDConf and $strUID != $strUID2)
		{
			$strQuery .= ", vcPWD = '$PWD' where iUserID='$iUserID'";
			$strComment .= "<p>Requested username is already in use could not be changed. Everything else has been updated, including password. " .
			"To change the username use a different username that is not in use. For example $strUID2 is available.</p>\n";
		}
		if ($Password != $PWDConf and $strUID != $strUID2)
		{
			$strQuery .= " where iUserID='$iUserID'";
			$strComment .= "<p>Passwords do not match so password was not changed. Requested username is already in use could not be changed. " .
						"Everything else has been updated. To change the username go back and use a different username that is not in use. " .
						"For example $strUID2 is available. When changing the password make sure you type the same one twice.</p>\n";
		}

		if ($dbh->query ($strQuery))
			{
				$NumAffected = $dbh->affected_rows;
				$strComment .= "Database update of $NumAffected record successful<br>\n";
				$_SESSION["Message"] = $strComment;
				$bUpdate = "OK";
				$StrMsg = str_replace("\\'","'",$StrMsg);
				EmailText("$ProfileNotify","registration updated at $strHost",$StrMsg,$fromEmail);
				header("Location: myprofile.php" );
			}
		else
			{
				$strError = "Database $type failed. Error (". $dbh->errno . ") " . $dbh->error . "\n";
				$strError .= "$strQuery\n";
				error_log($strError);
				if(EmailText("$SupportEmail","Automatic Error Report",$strError,$fromEmail))
					{
						$strComment .= "We seem to be experiencing technical difficulties. We have been notified. " .
						"Please try again later. Thank you.<br>\n";
						$_SESSION["Message"] = $strComment;
						header("Location: myprofile.php" );
					}
				else
					{
						$strComment .= "We seem to be experiencing technical difficulties. " .
						"Please send us a message at $SupportEmail with information about " .
						"what you were doing.</p>";
						$_SESSION["Message"] = $strComment;
						header("Location: myprofile.php" );
					}
				$bUpdate = "Failed";
			}
	}
	else
	{
		$_SESSION["Message"] = "Can't update without a registration ID. Contact $SupportEmail if you have any questions.";
		EmailText("$SupportEmail","Automatic Error Report","Failed to update registration for $strName due to missing regid" ,
				$fromEmail);
		header("Location: myprofile.php" );
	}
?>