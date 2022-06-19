<?php
    require "header.php";
    // print "<script src=\"ckeditor/ckeditor.js\"></script>\n";
    $DocRoot = "lib/";
    $MaxSize = ini_get('post_max_size');
    $MaxFileSize = ini_get('upload_max_filesize');

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

    print "<p class=\"Header1\">Event Administration</p>\n";

    if ($btnSubmit == 'Save')
    {
        $iEventID = CleanSQLInput(substr(trim($_POST['iEventID']),0,9));
        $iEventDate = date("Y-m-d H:i",strtotime($_POST['txtEventDate']));
        $strEventLocation = htmlentities(CleanSQLInput(substr(trim($_POST['txtEventLocation']),0,249)));
        $strEventName = htmlentities(CleanSQLInput(substr(trim($_POST['txtEventName']),0,99)));
        $strDescr = CleanSQLInput($_POST['txtDescr']);

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
                        print "File $DocBaseName uploaded successfully<br>" .
                        print "</div>\n";
                    }
                    else
                    {
                        print "<p class=\"Error\">Couldn't move file to $newPath</p>";
                        $strImgPath="";
                    }
                }
                else
                {
                    $ErrMsg = codeToMessage($Error);
                    print "<p class=\"Error\">Error \"$ErrMsg\" while uploading $DocFileName</p>\n";
                }
            }
        }
        if (isset($_FILES['fVideo']))
        {
            if ($_FILES['fVideo']['name']!="")
            {
                $tmpFile = $_FILES['fVideo']['tmp_name'];
                $Error = $_FILES['fVideo']['error'];
                $DocFileName = $_FILES['fVideo']['name'];
                $DocBaseName = basename($DocFileName);
                $newPath = $DocRoot . $DocBaseName;
                if ($Error == UPLOAD_ERR_OK)
                {
                    if (move_uploaded_file($tmpFile, $newPath))
                    {
                        $strVideoPath = $newPath;
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
        $strQuery = "update tblEvents set vcEventName = '$strEventName', tEventInfo = '$strDescr', " .
                    " vcEventLocation = '$strEventLocation', dtEventDate = '$iEventDate' ";
        if ($strImgPath != "")
        {
            $strQuery .= ", vcPoster='$strImgPath' ";
        }
        if ($strVideoPath != "")
        {
            $strQuery .= ", vcVideo='$strVideoPath' ";
        }
        $strQuery .= " where iEventID = $iEventID;";
        UpdateSQL ($strQuery,"update");
    }

    if ($btnSubmit == 'Delete')
    {
            $iEventID = CleanSQLInput(substr(trim($_POST['iEventID']),0,9));

            $strQuery = "delete from tblEvents where iEventID = $iEventID;";
            UpdateSQL ($strQuery,"delete");
    }

    if ($btnSubmit == 'Insert')
    {
        $strImgPath = "";
        $strEventName = htmlentities(CleanSQLInput(substr(trim($_POST['txtEventName']),0,99)));
        $strDescr = htmlentities(CleanSQLInput($_POST['txtDescr']));
        $strEventLocation = htmlentities(CleanSQLInput(substr(trim($_POST['txtEventLocation']),0,249)));
        $iEventDate = date("Y-m-d H:i",strtotime($_POST['txtEventDate']));

        if ($strEventName == '')
        {
            print "<p>Please provide a Event name to insert</p>\n";
        }
        else
        {
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
            if (isset($_FILES['fVideo']))
            {
                if ($_FILES['fVideo']['name']!="")
                {
                    $tmpFile = $_FILES['fVideo']['tmp_name'];
                    $Error = $_FILES['fVideo']['error'];
                    $DocFileName = $_FILES['fVideo']['name'];
                    $DocBaseName = basename($DocFileName);
                    $newPath = $DocRoot . $DocBaseName;
                    if ($Error == UPLOAD_ERR_OK)
                    {
                        if (move_uploaded_file($tmpFile, $newPath))
                        {
                            $strVideoPath = $newPath;
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
            $strQuery = "insert tblEvents (vcEventName, tEventInfo, dtEventDate, vcEventLocation, vcPoster, vcVideo) ".
                        "values ('$strEventName','$strDescr', '$iEventDate', '$strEventLocation', '$strImgPath', '$strVideoPath');";
            UpdateSQL ($strQuery,"insert");
        }
    }

    //Print the normal form after update is complete.
    print "<table width=100% border=0>\n";
    print "<tr>\n";
    print "<th width=40%>Update existing Event</th>\n";
    print "<th width = 2%></th>\n";
    if ($btnSubmit != 'Edit')
    {
        print "<th width=50%>Or Insert New one</th>";
    }
    print "</tr>\n";
    print "<tr>\n";
    print "<td valign=\"top\">\n";
    print "<table border = 0>\n";
    $strQuery = "select * from tblEvents order by dtEventDate;";
    if (!$Result = $dbh->query ($strQuery))
    {
        error_log ('Failed to fetch data. Error ('. $dbh->errno . ') ' . $dbh->error);
        error_log ($strQuery);
        print "<p class=\"Attn\" align=center>$ErrMsg</p>\n";
        exit(2);
    }
    while ($Row = $Result->fetch_assoc())
    {
        $strEventName = $Row['vcEventName'];
        $strEventDate = date("m/d/Y",strtotime($Row['dtEventDate']));;
        $iEventID = $Row['iEventID'];
        if ($WritePriv <=  $Priv)
        {
            print "<form method=\"POST\">\n";
            print "<tr valign=\"top\">\n";
            print "<td class=\"lbl\"><input type=\"hidden\" value=\"$iEventID\" name=\"iEventID\"></td>\n";
            print "<td>$strEventName $strEventDate</td>\n";
            print "<td><input type=\"Submit\" value=\"Edit\" name=\"btnSubmit\"></td>";
            print "<td><input type=\"Submit\" value=\"Delete\" name=\"btnSubmit\"></td>";
            print "</tr>\n";
            print "</form>\n";
        }
        else
        {
            print "<tr><td>$vcEventName</td></tr>\n";
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
    if (isset($_POST['iEventID']) and $btnSubmit == 'Edit')
    {
        $iEventID = $_POST['iEventID'];
        $strQuery = "select * from tblEvents where iEventID = $iEventID;";
        if (!$Result = $dbh->query ($strQuery))
        {
            error_log ('Failed to fetch data. Error ('. $dbh->errno . ') ' . $dbh->error);
            error_log ($strQuery);
            print "<p class=\"Attn\" align=center>$ErrMsg</p>\n";
            exit(2);
        }
        $Row = $Result->fetch_assoc();
        $strEventName = $Row['vcEventName'];
        $strDescr = $Row['tEventInfo'];
        $strLocation = $Row['vcEventLocation'];
        $strFlyer = $Row['vcPoster'];
        $strVideo = $Row['vcVideo'];
        $strEventDate = date("Y-m-d\TH:i",strtotime($Row['dtEventDate']));
        $strBtnLabel = "Save";
    }
    else
    {
        $strEventName = "";
        $strDescr = "";
        $iEventID = "";
        $strBtnLabel = "Insert";
        $strEventDate = "";
        $strLocation = "";
        $strFlyer = "";
        $strVideo = "";
    }
    print "<form method=\"POST\" enctype=\"multipart/form-data\">\n";
    print "<input type=\"hidden\" value=\"$iEventID\" name=\"iEventID\">";
    print "<table>\n";
    print "<tr>\n";
    print "<td class=\"lbl\">Event Name:</td>\n";
    print "<td><input type=\"text\" name=\"txtEventName\" size=\"50\" value=\"$strEventName\"></td>\n";
    print "</tr>\n";
    print "<tr>\n";
    print "<td class=\"lbl\">Event Location:</td>\n";
    print "<td><input type=\"text\" name=\"txtEventLocation\" size=\"50\" value=\"$strLocation\"></td>\n";
    print "</tr>\n";
    print "<tr>\n";
    print "<td class=\"lbl\">Event date and time:</td>\n";
    print "<td><input type=\"datetime-local\" name=\"txtEventDate\" value=\"$strEventDate\"></td>\n";
    print "</tr>\n";
    print "<tr>\n<td>&nbsp;</td>\n</tr>\n";
    print "<tr>\n";
    print "<td colspan=2 align=center class=\"lbl\">File upload/attachments:</td>\n";
    print "</tr>\n";
    print "<tr>\n";
    print "<td colspan=2 class=\"BlueAttn\">Please note a $MaxFileSize file size limit and $MaxSize total limit</td>\n";
    print "</tr>\n";
    if ($strFlyer !="")
    {
        print "<tr>\n";
        print "<td class=\"lbl\">Current PDF Flyer: </td>\n";
        print "<td>$strFlyer</td>\n";
        print "</tr>\n";
        print "<tr>\n";
        print "<td class=\"lbl\">New PDF Flyer: </td>\n";
        print "<td><input type=\"File\" name=\"fPict\" size=\"30\" ></td>\n";
        print "</tr>\n";
    }
    else
    {
        print "<tr>\n";
        print "<td class=\"lbl\">New PDF Flyer: </td>\n";
        print "<td><input type=\"File\" name=\"fPict\" size=\"30\" ></td>\n";
        print "</tr>\n";
    }
    if ($strVideo !="")
    {
        print "<tr>\n";
        print "<td class=\"lbl\">Current Video: </td>\n";
        print "<td>$strVideo</td>\n";
        print "</tr>\n";
        print "<tr>\n";
        print "<td class=\"lbl\">New Video: </td>\n";
        print "<td><input type=\"File\" name=\"fVideo\" size=\"30\" ></td>\n";
        print "</tr>\n";
    }
    else
    {
        print "<tr>\n";
        print "<td class=\"lbl\">New Video of the event: </td>\n";
        print "<td><input type=\"File\" name=\"fVideo\" size=\"30\" ></td>\n";
        print "</tr>\n";
    }
    print "<tr>\n";
    print "<td class=\"lbl\">Description:</td>\n";
    print "</tr>\n";
    print "<tr>\n";
    print "<td colspan=2><textarea name=\"txtDescr\" rows=\"15\" cols=\"70\">$strDescr</textarea>\n</td>\n";
    print "<script>CKEDITOR.replace( 'txtDescr' );</script>\n";
    print "</tr>\n";
    print "<tr>\n";
    print "<td colspan=2 align=\"center\">\n";
    print "<input type=\"Submit\" value=\"$strBtnLabel\" name=\"btnSubmit\">\n";
    if (isset($_POST['iEventID']))
    {
        print "<input type=\"Submit\" value=\"Go Back\" name=\"btnSubmit\">\n";
    }
    print "</td>\n";
    print "</tr>\n";
    print "</table>\n";
    print "</form>\n";
    print "</td>\n";
    print "</tr>\n";
    print "</table>\n";

    require "footer.php";
?>
