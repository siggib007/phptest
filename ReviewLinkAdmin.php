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

	print "<p class=\"Header1\">Review Site link Administration</p>\n";

	if ($btnSubmit == 'Save')
	{
		$iSiteID = CleanSQLInput(substr(trim($_POST['iSiteID']),0,9));
		$strSiteName = CleanSQLInput(substr(trim($_POST['txtSiteName']),0,99));
		$strDescr = CleanSQLInput($_POST['txtDescr']);
		$strLogoURL= CleanSQLInput(substr(trim($_POST['txtLogoURL']),0,99));
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
                    $strImgPath = $strLogoURL;
                }
                
		$strQuery = "update tblReviewSiteURL set vcSiteName = '$strSiteName', " 
                          . "vcImgPath = '$strImgPath' where iSiteID = $iSiteID;";
		UpdateSQL ($strQuery,"update");
	}
	
	if ($btnSubmit == 'Delete')
	{
		$iSiteID = CleanSQLInput(substr(trim($_POST['iSiteID']),0,9));
		
		$strQuery = "delete from tblReviewSiteURL where iSiteID = $iSiteID;";
		UpdateSQL ($strQuery,"delete");
	}

	if ($btnSubmit == 'Insert')
	{
		$strSiteName = CleanSQLInput(substr(trim($_POST['txtSiteName']),0,99));
		$strSiteURL = CleanSQLInput(substr(trim($_POST['txtSiteURL']),0,99));
		$strLogoURL= CleanSQLInput(substr(trim($_POST['txtLogoURL']),0,99));
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
                        $strImgPath = $strLogoURL;
                    }                    
                }
                else
                {
                    $strImgPath = $strLogoURL;
                }
                
		if ($strSiteName == '')
		{
			print "<p>Please provide a Site name to insert</p>\n";
		}
		else
		{
			$strQuery = "insert tblReviewSiteURL (vcSiteName, vcImgPath, vcSiteURL) " 
                                  . "values ('$strSiteName', '$strImgPath', '$strSiteURL ');";
			UpdateSQL ($strQuery,"insert");
		}
	}
	
	//Print the normal form after update is complete.
	print "<table>\n";
        print "<tr>\n";
        print "<th>Update existing Review Site link</th>\n";
        print "<th width = 100></th>\n";
        if ($btnSubmit != 'Edit')
        {
            print "<th class=lbl>Or Insert New one</th>";   
        }
	print "</tr>\n";
        print "<tr>\n";
        print "<td valign=\"top\">\n";
        print "<table border = 0>\n";
	$strQuery = "select iSiteID, vcSiteName from tblReviewSiteURL;";
	if (!$Result = $dbh->query ($strQuery))
	{
            error_log ('Failed to fetch data. Error ('. $dbh->errno . ') ' . $dbh->error);
            error_log ($strQuery);
            print "<p class=\"Attn\" align=center>$ErrMsg</p>\n";
            exit(2);
	}
	while ($Row = $Result->fetch_assoc())
	{
            $vcSiteName = $Row['vcSiteName'];
            $iSiteID = $Row['iSiteID'];
            if ($WritePriv <=  $Priv)
            {
                print "<form method=\"POST\">\n";
                print "<tr valign=\"top\">\n";
                print "<td class=\"lbl\"><input type=\"hidden\" value=\"$iSiteID\" name=\"iSiteID\"></td>\n";
                print "<td>$vcSiteName</td>\n";
                print "<td><input type=\"Submit\" value=\"Edit\" name=\"btnSubmit\"></td>";
                print "<td><input type=\"Submit\" value=\"Delete\" name=\"btnSubmit\"></td>";
                print "</tr>\n";
                print "</form>\n";
            }
            else
            {
                print "<tr><td>$vcSiteName</td></tr>\n";
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
        if (isset($_POST['iSiteID']) and $btnSubmit == 'Edit')
        {
            $iSiteID = $_POST['iSiteID'];
            $strQuery = "select iSiteID, vcSiteName, vcSiteURL, vcImgPath from tblReviewSiteURL where iSiteID = $iSiteID;";
            if (!$Result = $dbh->query ($strQuery))
            {
                error_log ('Failed to fetch data. Error ('. $dbh->errno . ') ' . $dbh->error);
                error_log ($strQuery);
                print "<p class=\"Attn\" align=center>$ErrMsg</p>\n";
                exit(2);
            }
            $Row = $Result->fetch_assoc();
            $strSiteName = $Row['vcSiteName'];
            $strSiteURL = $Row['vcSiteURL'];
            $strLogoURL = $Row['vcImgPath'];
            $strBtnLabel = "Save";
        }
        else
        {
            $strSiteName = "";                       
            $iSiteID = "";
            $strSiteURL = "";
            $strLogoURL ="";
            $strBtnLabel = "Insert";
        }
	print "<form method=\"POST\" enctype=\"multipart/form-data\">\n";
        print "<input type=\"hidden\" value=\"$iSiteID\" name=\"iSiteID\">";
	print "<span class=\"lbl\">Review Site Name:</span>\n";
	print "<input type=\"text\" name=\"txtSiteName\" size=\"70\" value=\"$strSiteName\"><br>\n";
	print "<span class=\"lbl\">Review Site URL:</span>\n";
	print "<input type=\"text\" name=\"txtSiteURL\" size=\"70\" value=\"$strSiteURL\"><br>\n";
        print "<span class=\"lbl\">Attach logo: </span>\n";
        print "<input type=\"File\" name=\"fPict\" size=\"30\" >\n<br>\n";
        print "<span class=\"lbl\">Or specify a URL to logo:</span>\n";
	print "<input type=\"text\" name=\"txtLogoURL\" size=\"47\" value=\"$strLogoURL\"><br>\n";
	print "<div align=\"center\"><input type=\"Submit\" value=\"$strBtnLabel\" name=\"btnSubmit\"></div>\n";
	print "</form>\n";
        if (isset($_POST['iSiteID']))
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
