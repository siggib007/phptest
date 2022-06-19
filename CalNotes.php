<?php
    $DocRoot = "calimg/";
    $dtNote = "";
    $strNote = "";
    $iItemID = "";
    $bClosed = "";


    if (isset($_POST['btnSubmit']))
    {
        $btnSubmit = $_POST['btnSubmit'];
    }
    else
    {
        $btnSubmit = "";
    }

    require("header.php");

    if ($strReferer != $strPageURL and $PostVarCount > 0)
    {
        print "<p class=\"Error\">Invalid operation, Bad Reference!!!</p> ";
        exit;
    }
    print("<p class=\"Header1\">Calendar Notes</p>\n");


    if ($btnSubmit == 'Save')
    {
        $bOK = TRUE;
        $strImgPath = "";
        $strComment = CleanSQLInput(substr(trim($_POST['txtComment']),0,99));
        $iItem = substr(trim($_POST['txtItemID']),0,49);

        $iNoteDate = strtotime($_POST['txtDate']);
        $strNoteDate = date("Y-m-d",$iNoteDate);

        if (isset($_POST['chkClosed']))
        {
            $bClosed = 1;
        }
        else
        {
            $bClosed = 0;
        }

        if ($iNoteDate < time())
        {
            $bOK = FALSE;
            print "<p>Please specify a date in the future</p>\n";
        }

        if ($strComment == '')
        {
            print "<p>Please provide a updated note</p>\n";
            $bOK = FALSE;
        }

        if ($bOK)
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
            $strQuery = "update tblCalItems set dtDate='$strNoteDate', vcComment='$strComment'";
            $strQuery .= ", bClosed = '$bClosed'";
            if ($strImgPath != "")
            {
                $strQuery .= ", vcPicturePath='$strImgPath' ";
            }
            $strQuery .= " where iItemID = '$iItem';";
            UpdateSQL ($strQuery,"update");
        }
    }

    if ($btnSubmit == 'Delete')
    {
        $iItem = substr(trim($_POST['txtItemID']),0,49);

        $strQuery = "delete from tblCalItems where iItemID = $iItem;";
        UpdateSQL ($strQuery,"delete");
    }

    if ($btnSubmit == 'Insert')
    {
        $bOK = TRUE;
        $strImgPath = "";
        $strComment = CleanSQLInput(substr(trim($_POST['txtComment']),0,99));
        if (isset($_POST['chkClosed']))
        {
            $bClosed = 1;
        }
        else
        {
            $bClosed = 0;
        }
        $iNoteDate = strtotime($_POST['txtDate']);
        $strNoteDate = date("Y-m-d",$iNoteDate);

        if ($iNoteDate < time())
        {
            $bOK = FALSE;
            print "<p>Please specify a date in the future</p>\n";
        }

        if ($strComment == '')
        {
            print "<p>Please provide a note to insert</p>\n";
            $bOK = FALSE;
        }

        if ($bOK)
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

            $strQuery = "delete from tblCalItems where dtDate = '$strNoteDate';";
            UpdateSQL ($strQuery,"delete");

            $strQuery = "insert tblCalItems (dtDate, vcComment, vcPicturePath, bClosed) " .
                        "values ('$strNoteDate','$strComment','$strImgPath', '$bClosed' );";
            UpdateSQL ($strQuery,"insert");
        }
    }

    if ($btnSubmit == 'Edit')
    {
        $iItemID = substr(trim($_POST['txtItemID']),0,49);

        $strQuery = "SELECT dtDate, vcComment, bClosed FROM tblCalItems WHERE iItemID = '$iItemID';";
        if (!$Result = $dbh->query ($strQuery))
        {
            error_log ('Failed to fetch Menu data. Error ('. $dbh->errno . ') ' . $dbh->error);
            error_log ($strQuery);
            print "<p class=\"Attn\" align=center>$ErrMsg</p>\n";
            exit(2);
        }
        $Row = $Result->fetch_assoc();
        $dtNote = $Row['dtDate'];
        $strNote = $Row['vcComment'];
        $bClosed = $Row['bClosed'];
    }

    //Print the normal form after update is complete.
    print "<table border = 0>\n";
    print "<tr>\n";
    print "<th>Update existing Calander item</th>\n";
    print "<th width = 100></th>\n";
    If (! $iItemID)
    {
        print "<th>Or Insert New one</th>\n";
    }
    print "</tr>\n";
    print "<tr>\n";
    print "<td>\n";
    print "<table border = 0>\n";
    print "<tr>\n";
    print "<th>Date</th>\n";
    print "<th>Comment</th>";
    print "<th>Picture</th>\n";
    print "<th>Closed</th>\n";
    print "</tr>\n";
    $strQuery = "SELECT iItemID, dtDate, vcComment, vcPicturePath, bClosed FROM tblCalItems order by dtDate;";
    if (!$Result = $dbh->query ($strQuery))
    {
        error_log ('Failed to fetch data. Error ('. $dbh->errno . ') ' . $dbh->error);
        error_log ($strQuery);
        print "<p class=\"Attn\" align=center>$ErrMsg</p>\n";
        exit(2);
    }
    while ($Row = $Result->fetch_assoc())
    {
        $dtDate = date("m/d/Y",strtotime($Row['dtDate']));
        $strComment = $Row['vcComment'];
        $strPict = $Row['vcPicturePath'];
        $iItem = $Row['iItemID'];
        $iClosed = $Row['bClosed'];

        print "<tr>\n";
        print "<td>$dtDate</td><td>$strComment</td>\n";
        print "<td><img src=\"$strPict\" height=\"35\"></td>\n";
        if ($iClosed == 0)
        {
            print "<td align=center><input type=\"checkbox\" disabled></td>\n";
        }
        else
        {
            print "<td align=center><input type=\"checkbox\" checked disabled></td>\n";
        }
        if ($WritePriv <=  $Priv)
        {
            print "<td>\n";
            print "<form method=\"POST\">\n";
            print "<input type=\"hidden\" value=\"$iItem\" name=\"txtItemID\">\n";
            print "<input type=\"Submit\" value=\"Edit\" name=\"btnSubmit\">\n";
            print "<input type=\"Submit\" value=\"Delete\" name=\"btnSubmit\">";
            print "</td>\n";
            print "</form>\n";
        }
        print "</tr>\n";
    }
    print "</table>\n";
    print "</td>\n";
    print "<td>\n";
    print "</td>\n";
    print "<td valign=\"top\">\n";
    print "<form method=\"POST\" enctype=\"multipart/form-data\">\n";
    If ($iItemID)
    {
        print "<input type=\"hidden\" value=\"$iItemID\" name=\"txtItemID\">\n";
        $strBtnLabel = "Save";
    }
    else
    {
        $strBtnLabel = "Insert";
    }
    print "<table border = 0>\n";
    print "<tr>\n";
    print "<td align = right class =\"lbl\">Date: </td>\n";
    print "<td><input type=\"date\" name=\"txtDate\" size=\"30\" value=\"$dtNote\"></td>\n";
    print "</tr>\n";
    print "<tr>\n<td align = right class =\"lbl\">Comment: </td>\n";
    print "<td><input type=\"text\" name=\"txtComment\" size=\"30\" value=\"$strNote\"></td></tr>\n";
    print "<tr>\n<td align = right class =\"lbl\">We are Closed: </td>\n";
    if ($bClosed == 0)
    {
        print "<td><input type=\"checkbox\" name=\"chkClosed\"></td></tr>\n";
    }
    else
    {
        print "<td><input type=\"checkbox\" name=\"chkClosed\" checked></td></tr>\n";
    }
    print "<tr>\n<td align = right class =\"lbl\">Attach Picture: </td>\n";
    print "<td><input type=\"File\" name=\"fPict\" size=\"30\" ></td></tr>\n";
    print "<tr><td colspan=2 align=center><input type=\"Submit\" value=\"$strBtnLabel\" name=\"btnSubmit\">";
    if ($iItemID)
    {
        print "<input type=\"Submit\" value=\"Reset\" name=\"btnSubmit\">";
    }
    print "</td></tr>\n";
    print "</table>\n";
    print "</form>\n</td>\n</tr>\n</table>";

    require("footer.php");
?>
