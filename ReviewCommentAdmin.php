<?php
	require "header.php";
        
        $DocRoot = "ReviewMedia/";

	if ($strReferer != $strPageURL and $PostVarCount > 0)
	{
		print "<p class=\"Error\">Invalid operation, Bad Reference!!!</p> ";
		exit;
	}
	if (isset($_POST['btnSubmit']))
	{
		$btnSubmit = $_POST['btnSubmit'];
	}
	else
	{
		$btnSubmit = "";
	}

	print "<p class=\"Header1\">Feedback Administration</p>\n";

	if ($btnSubmit == 'Save')
	{
		$iFeedbackID = CleanSQLInput(substr(trim($_POST['iFeedbackID']),0,9));
		$strFeedbackName = CleanSQLInput(substr(trim($_POST['txtFeedbackName']),0,99));
		$strDescr = CleanSQLInput($_POST['txtDescr']);
		$strMMURL= CleanSQLInput(substr(trim($_POST['txtMMURL']),0,99));
                $strImgPath="";
                
                if (isset($_FILES['fPict']))
                {
                    if ($_FILES['fPict']['name']!="")
                    {
                        $tmpFile = $_FILES['fPict']['tmp_name'];
                        $Error = $_FILES['fPict']['error'];
                        $DocFileName = $_FILES['fPict']['name'];
                        $DocBaseName = str_replace(" ","_",basename($DocFileName));
                        $newPath = $DocRoot . $DocBaseName;
                        if ($Error == UPLOAD_ERR_OK)
                        {
                            if (move_uploaded_file($tmpFile, $newPath))
                            {
                                $strImgPath = $newPath;
                                print "<div class=\"MainText\">";
                                print "File $DocBaseName uploaded successfully<br>";
                                print "</div>\n";
                            }
                            else
                            {
                                print "<p class=\"Error\">Couldn't move file to $newPath</p>";
                            }
                        }
                        else
                        {
                            $ErrMsg = codeToMessage($Error);
                            print "<p class=\"Error\">Error \"$ErrMsg\" while uploading $DocFileName</p>\n";
                        }
                    }
                }
                
                if ($strImgPath == '')
                {
                    $strImgPath = $strMMURL;
                }
                
		$strQuery = "update tblFeedback set vcFeedbackName = '$strFeedbackName', " 
                          . "tFeedbackDescr = '$strDescr', vcImgPath = '$strImgPath' where iFeedbackID = $iFeedbackID;";
		UpdateSQL ($strQuery,"update");
	}
	
	if ($btnSubmit == 'Delete')
	{
		$iFeedbackID = CleanSQLInput(substr(trim($_POST['iFeedbackID']),0,9));
		
		$strQuery = "delete from tblFeedback where iFeedbackID = $iFeedbackID;";
		UpdateSQL ($strQuery,"delete");
	}

	if ($btnSubmit == 'Insert')
	{
		$strFeedbackName = CleanSQLInput(substr(trim($_POST['txtFeedbackName']),0,99));
		$strDescr = CleanSQLInput($_POST['txtDescr']);
		$strMMURL= CleanSQLInput(substr(trim($_POST['txtMMURL']),0,99));
                $strImgPath="";
                
                if (isset($_FILES['fPict']))
                {
                    if ($_FILES['fPict']['name']!="")
                    {
                        $tmpFile = $_FILES['fPict']['tmp_name'];
                        $Error = $_FILES['fPict']['error'];
                        $DocFileName = $_FILES['fPict']['name'];
                        $DocBaseName = basename($DocFileName);
                        $newPath = $DocRoot . $DocBaseName;
                        if ($Error == UPLOAD_ERR_OK)
                        {
                            if (move_uploaded_file($tmpFile, $newPath))
                            {
                                $strImgPath = $newPath;
                                print "<div class=\"MainText\">";
                                print "File $DocBaseName uploaded successfully<br>";
                                print "</div>\n";
                            }
                            else
                            {
                                print "<p class=\"Error\">Couldn't save file to $newPath</p>";
                            }
                        }
                        else
                        {
                            $ErrMsg = codeToMessage($Error);
                            print "<p class=\"Error\">Error \"$ErrMsg\" while uploading $DocFileName</p>\n";
                        }
                    }
                    else
                    {
                        $strImgPath = $strMMURL;
                    }
                }
                else
                {
                    $strImgPath = $strMMURL;
                }
                
		if ($strFeedbackName == '')
		{
			print "<p>Please provide a name for the feedback to insert</p>\n";
		}
		else
		{
			$strQuery = "insert tblFeedback (vcFeedbackName, tFeedbackDescr, vcImgPath) " 
                                  . "values ('$strFeedbackName','$strDescr', '$strImgPath');";
			UpdateSQL ($strQuery,"insert");
		}
	}
	
	//Print the normal form after update is complete.
	print "<table>\n";
        print "<tr>\n";
        print "<th>Update existing feedback</th>\n";
        print "<th width = 100></th>\n";
        if ($btnSubmit != 'Edit')
        {
            print "<th class=lbl>Or Insert New one</th>";   
        }
	print "</tr>\n";
        print "<tr>\n";
        print "<td valign=\"top\">\n";
        print "<table border = 0>\n";
	$strQuery = "select iFeedbackID, vcFeedbackName, tFeedbackDescr from tblFeedback;";
	if (!$Result = $dbh->query ($strQuery))
	{
            error_log ('Failed to fetch data. Error ('. $dbh->errno . ') ' . $dbh->error);
            error_log ($strQuery);
            print "<p class=\"Attn\" align=center>$ErrMsg</p>\n";
            exit(2);
	}
	while ($Row = $Result->fetch_assoc())
	{
            $vcFeedbackName = $Row['vcFeedbackName'];
            $iFeedbackID = $Row['iFeedbackID'];
            if ($WritePriv <=  $Priv)
            {
                print "<form method=\"POST\">\n";
                print "<tr valign=\"top\">\n";
                print "<td class=\"lbl\"><input type=\"hidden\" value=\"$iFeedbackID\" name=\"iFeedbackID\"></td>\n";
                print "<td>$vcFeedbackName</td>\n";
                print "<td><input type=\"Submit\" value=\"Edit\" name=\"btnSubmit\"></td>";
                print "<td><input type=\"Submit\" value=\"Delete\" name=\"btnSubmit\"></td>";
                print "</tr>\n";
                print "</form>\n";
            }
            else
            {
                print "<tr><td>$vcFeedbackName</td></tr>\n";
            }		
	}
	print "</table>\n";	    
        print "<form method=\"POST\">\n";
        print "<input type=\"Submit\" value=\"Go Back\" name=\"btnSubmit\">\n";
        print "</form>";
	print "</td>\n";
        print "<td>\n";
        print "</td>\n";
        print "<td valign=\"top\">\n";
        if (isset($_POST['iFeedbackID']) and $btnSubmit == 'Edit')
        {
            $iFeedbackID = $_POST['iFeedbackID'];
            $strQuery = "select iFeedbackID, vcFeedbackName, tFeedbackDescr, vcImgPath from tblFeedback where iFeedbackID = $iFeedbackID;";
            if (!$Result = $dbh->query ($strQuery))
            {
                error_log ('Failed to fetch data. Error ('. $dbh->errno . ') ' . $dbh->error);
                error_log ($strQuery);
                print "<p class=\"Attn\" align=center>$ErrMsg</p>\n";
                exit(2);
            }
            $Row = $Result->fetch_assoc();
            $strFeedbackName = $Row['vcFeedbackName'];
            $strClassDescr = $Row['tFeedbackDescr'];
            $strMMURL = $Row['vcImgPath'];
            $strBtnLabel = "Save";
        }
        else
        {
            $strFeedbackName = "";
            $strClassDescr = "";                         
            $iFeedbackID = "";
            $strMMURL ="";
            $strBtnLabel = "Insert";
        }
	print "<form method=\"POST\" enctype=\"multipart/form-data\">\n";
        print "<input type=\"hidden\" value=\"$iFeedbackID\" name=\"iFeedbackID\">";
	print "<span class=\"lbl\">Feedback Name:</span>\n";
	print "<input type=\"text\" name=\"txtFeedbackName\" size=\"70\" value=\"$strFeedbackName\"><br>\n";
        print "<span class=\"lbl\">Attach Picture: </span>\n";
        print "<input type=\"File\" name=\"fPict\" size=\"30\" >\n<br>\n";
        print "<span class=\"lbl\">Or specify a URL to picture or video:</span>\n";
	print "<input type=\"text\" name=\"txtMMURL\" size=\"47\" value=\"$strMMURL\"><br>\n";
        print "<div class=\"lbl\">Description:</div>\n";
        print "<textarea name=\"txtDescr\" rows=\"20\" cols=\"80\">$strClassDescr</textarea>\n<br>\n";        
	print "<div align=\"center\"><input type=\"Submit\" value=\"$strBtnLabel\" name=\"btnSubmit\"></div>\n";
	print "</form>\n";
        if (isset($_POST['iFeedbackID']))
        {
            print "<form method=\"POST\">\n";
            print "<input type=\"Submit\" value=\"Go Back\" name=\"btnSubmit\">\n";
            print "</form>\n";   
        }
	print "</td>\n";
        print "</tr>\n";
        print "</table>\n";
        
	require "footer.php"; 
?>
