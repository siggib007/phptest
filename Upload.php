<?php
    require("header.php");
    if ($WritePriv <=  $Priv)
    {
        $DocRoot = "lib/";
        $PageHeader = "File uploads";
        $ConfirmationMsg = "The following files were uploaded";
    }
    else
    {
        $DocRoot = "upload/";
        $PageHeader = "Contribute your photo's here";
        $ConfirmationMsg = "Thank you $UsersName for your contribution of the following files";
    }
    $MaxConLen = return_bytes(ini_get('post_max_size'));
    $MaxSize = ini_get('post_max_size');
    $MaxFileSize = ini_get('upload_max_filesize');
    $MaxFileCount = ini_get('max_file_uploads');
    if (isset($_FILES['Docfile']))
    {
        $FilesVarCount = count($_FILES['Docfile']['name']);
        if ($FilesVarCount == 1 and $_FILES['Docfile']['name'][0]=="")
        {
            print "<p class=\"Error\">You didn't select any files. ";
            $_POST['btnSubmit'] = "Go Back";
        }
    }
    if (isset($_SERVER['CONTENT_LENGTH']))
    {
        $ContLen = $_SERVER['CONTENT_LENGTH'];
        if ($ContLen > $MaxConLen)
        {
            print "<p class=\"Error\">Your upload was " . with_unit($ContLen ) . ". ";
            print "This exceeds the content limit of $MaxSize. Please do your upload in smaller chunks.</p>\n";
        }
    }
    $dtNow = date("Y-m-d H:i:s");
    $PostVarCount = count($_POST);
    if (!is_dir($DocRoot))
    {
        mkdir($DocRoot);
    }


    if ($strReferer != $strPageURL and $PostVarCount > 0)
    {
        print "<p class=\"Error\">Invalid operation, Bad Reference!!!</p> ";
        exit;
    }

    if ($PostVarCount == 0 or ($_POST['btnSubmit'] == 'Go Back'))
    {
        print "<p class=Header1>$PageHeader</p>";
        print "<p class=\"BlueAttn\"> Please note the following limits in place:<br>\n";
        print "Total upload limit $MaxSize.<br>";
        print "Each file limit $MaxFileSize.<br>";
        print "Total Number of files $MaxFileCount.<br>";
        print "If you exceed any of those limits you'll be returned to this screen without anything uploaded. ";
        print "In this case try uploading fewer smaller files.</p>\n";
        print "<form enctype=\"multipart/form-data\" method=\"POST\">\n";
        print "<p class=\"MainText\">File name: \n";
        print "<input type=\"file\" name=\"Docfile[]\" size=\"50\" multiple>\n</p>\n";
        print "<input type=\"Submit\" value=\"Upload\" name=\"btnSubmit\">";
        print "</form>\n";
    }
    else
    {
        if ($_POST['btnSubmit'] == 'Upload')
        {
            if (isset($_FILES['Docfile']))
            {
                $FileList = "";
                $SizeTotal = 0;
                for ($i = 0; $i < $FilesVarCount; $i++)
                {
                    $DocFileName = $_FILES['Docfile']['name'][$i];
                    $DocBaseName = basename($DocFileName);
                    $newPath = $DocRoot . $DocBaseName;
                    $tmpFile = $_FILES['Docfile']['tmp_name'][$i];
                    $Error = $_FILES['Docfile']['error'][$i];
                    $Size =  $_FILES['Docfile']['size'][$i];
                    $SizeUnit = with_unit($Size);
                    if ($Error == UPLOAD_ERR_OK)
                    {
                        if (move_uploaded_file($tmpFile, $newPath))
                        {
                            print "<div class=\"MainText\">File $DocBaseName uploaded successfully<br></div>";
                            $FileList .= "$DocBaseName, Size: $SizeUnit<br>\n";
                            $SizeTotal += $Size;
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
            print "<p class=\"MainText\">$ConfirmationMsg. <br>\n";
            print $FileList;
            print "Total size uploaded " .with_unit($SizeTotal) . "</p>\n";
            if ($WritePriv >  $Priv)
            {
                $strEmailBody = "$UsersName has submitted the following files for the Studio B Dance photo gallary\n";
                $strEmailBody .= str_replace("<br>","",$FileList);
                $strEmailBody .= "Total size uploaded " .with_unit($SizeTotal) . "\n";
                if (EmailText("$SupportEmail","Studio B Dance File upload Notification",$strEmailBody,$FromEmail))
                {
                    print "<p class=\"MainText\">We have been notified of your contribution</p>\n";
                }
                else
                {
                    print "<p class=\"Error\">Unable to send notification, please notify us so that we can approve your submission</p>\n";
                }
            }
            print "<form method=\"POST\">\n<input type=\"Submit\" value=\"Go Back\" name=\"btnSubmit\"></form>";
        }
    }
    require("footer.php");
?>