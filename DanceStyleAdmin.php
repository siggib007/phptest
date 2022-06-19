<?php
	require "header.php";
        
        $DocRoot = "DanceStyleMedia/";

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

	print "<p class=\"Header1\">Dance Style Administration</p>\n";

	if ($btnSubmit == 'Save')
	{
		$iStyleID = CleanSQLInput(substr(trim($_POST['iStyleID']),0,9));
		$strStyleName = CleanSQLInput(substr(trim($_POST['txtStyleName']),0,99));
                $iGenraID = CleanSQLInput(substr(trim($_POST['cmbGenra']),0,9));
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
                
		$strQuery = "update tblDanceStyle set iGenraID = '$iGenraID', vcStyleName = '$strStyleName', " 
                          . "tStyleDescr = '$strDescr', vcImgPath = '$strImgPath' where iStyleID = $iStyleID;";
		UpdateSQL ($strQuery,"update");
	}
	
	if ($btnSubmit == 'Delete')
	{
		$iStyleID = CleanSQLInput(substr(trim($_POST['iStyleID']),0,9));
		
		$strQuery = "delete from tblDanceStyle where iStyleID = $iStyleID;";
		UpdateSQL ($strQuery,"delete");
	}

	if ($btnSubmit == 'Insert')
	{
		$strStyleName = CleanSQLInput(substr(trim($_POST['txtStyleName']),0,99));
		$strDescr = CleanSQLInput($_POST['txtDescr']);
                $iGenraID = CleanSQLInput(substr(trim($_POST['cmbGenra']),0,9));
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
                }
                else
                {
                    $strImgPath = $strMMURL;
                }
                
		if ($strStyleName == '')
		{
			print "<p>Please provide a Dance Style name to insert</p>\n";
		}
		else
		{
			$strQuery = "insert tblDanceStyle (iGenraID, vcStyleName, tStyleDescr, vcImgPath) " 
                                  . "values ('$iGenraID','$strStyleName','$strDescr', '$strImgPath');";
			UpdateSQL ($strQuery,"insert");
		}
	}
	
	//Print the normal form after update is complete.
	print "<table>\n";
        print "<tr>\n";
        print "<th>Update existing Dance Style Descriptions</th>\n";
        print "<th width = 100></th>\n";
        if ($btnSubmit != 'Edit')
        {
            print "<th class=lbl>Or Insert New one</th>";   
        }
	print "</tr>\n";
        print "<tr>\n";
        print "<td valign=\"top\">\n";
        print "<table border = 0>\n";
	$strQuery = "select iStyleID, iGenraID, vcStyleName, tStyleDescr from tblDanceStyle;";
	if (!$Result = $dbh->query ($strQuery))
	{
            error_log ('Failed to fetch data. Error ('. $dbh->errno . ') ' . $dbh->error);
            error_log ($strQuery);
            print "<p class=\"Attn\" align=center>$ErrMsg</p>\n";
            exit(2);
	}
	while ($Row = $Result->fetch_assoc())
	{
            $vcStyleName = $Row['vcStyleName'];
            $iStyleID = $Row['iStyleID'];
            if ($WritePriv <=  $Priv)
            {
                print "<form method=\"POST\">\n";
                print "<tr valign=\"top\">\n";
                print "<td class=\"lbl\"><input type=\"hidden\" value=\"$iStyleID\" name=\"iStyleID\"></td>\n";
                print "<td>$vcStyleName</td>\n";
                print "<td><input type=\"Submit\" value=\"Edit\" name=\"btnSubmit\"></td>";
                print "<td><input type=\"Submit\" value=\"Delete\" name=\"btnSubmit\"></td>";
                print "</tr>\n";
                print "</form>\n";
            }
            else
            {
                print "<tr><td>$vcStyleName</td></tr>\n";
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
        if (isset($_POST['iStyleID']) and $btnSubmit == 'Edit')
        {
            $iStyleID = $_POST['iStyleID'];
            $strQuery = "select iStyleID, vcStyleName, tStyleDescr, vcImgPath from tblDanceStyle where iStyleID = $iStyleID;";
            if (!$Result = $dbh->query ($strQuery))
            {
                error_log ('Failed to fetch data. Error ('. $dbh->errno . ') ' . $dbh->error);
                error_log ($strQuery);
                print "<p class=\"Attn\" align=center>$ErrMsg</p>\n";
                exit(2);
            }
            $Row = $Result->fetch_assoc();
            $strStyleName = $Row['vcStyleName'];
            $strClassDescr = $Row['tStyleDescr'];
            $strMMURL = $Row['vcImgPath'];
            $strBtnLabel = "Save";
        }
        else
        {
            $strStyleName = "";
            $strClassDescr = "";                         
            $iStyleID = "";
            $strMMURL ="";
            $strBtnLabel = "Insert";
        }
	print "<form method=\"POST\" enctype=\"multipart/form-data\">\n";
        print "<input type=\"hidden\" value=\"$iStyleID\" name=\"iStyleID\">";
	print "<span class=\"lbl\">Style Name:</span>\n";
	print "<input type=\"text\" name=\"txtStyleName\" size=\"70\" value=\"$strStyleName\"><br>\n";
	print "<span class=\"lbl\">Dance Genra:</span>\n";
        $strQuery = "select * from tblGenra;";
        if (!$Result2 = $dbh->query ($strQuery))
        {
            error_log ('Failed to fetch Dance Genra data. Error ('. $dbh->errno . ') ' . $dbh->error);
            error_log ($strQuery);
            print "<p class=\"Attn\" align=center>$ErrMsg</p>\n";
            exit(2);
        }
        print "<select size=\"1\" name=\"cmbGenra\">\n";
        while ($Row2 = $Result2->fetch_assoc())
        {
            if ($Row2['iGenraID'] == $Row['iGenraID'])
            {
                print "<option selected value=\"{$Row2['iGenraID']}\">{$Row2['vcGenraName']}</option>\n";
            }
            else
            {
                print "<option value=\"{$Row2['iGenraID']}\">{$Row2['vcGenraName']}</option>\n";
            }
        }			
        print "</select>\n<br>\n";
        print "<span class=\"lbl\">Attach Picture: </span>\n";
        print "<input type=\"File\" name=\"fPict\" size=\"30\" >\n<br>\n";
        print "<span class=\"lbl\">Or specify a URL to picture or video:</span>\n";
	print "<input type=\"text\" name=\"txtMMURL\" size=\"47\" value=\"$strMMURL\"><br>\n";
        print "<div class=\"lbl\">Description:</div>\n";
        print "<textarea name=\"txtDescr\" rows=\"20\" cols=\"80\">$strClassDescr</textarea>\n<br>\n";        
	print "<div align=\"center\"><input type=\"Submit\" value=\"$strBtnLabel\" name=\"btnSubmit\"></div>\n";
	print "</form>\n";
        if (isset($_POST['iStyleID']))
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
