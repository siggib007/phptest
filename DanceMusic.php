<?php
    require("header.php");

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

   
    if ($btnSubmit == 'Save')
    {
        $iStyleID = CleanSQLInput(substr(trim($_POST['cmbStyle']),0,9));
        $strSong= CleanSQLInput(substr(trim($_POST['txtSong']),0,99));
        $strPerformer = CleanSQLInput(substr(trim($_POST['txtPerformer']),0,99));
        $iMusicID = CleanSQLInput(substr(trim($_POST['iMusicID']),0,9));

        if ($iStyleID == '')
        {
            $iStyleID = 1;
        }
        if ($strSong== '')
        {
            print "<p>Please provide song name</p>\n";
        }
        else
        {
            $strQuery = "update tblDanceMusic set iStyleID = '$iStyleID', vcSongTitle = '$strSong', " .
                        " vcPerformer = '$strPerformer' where iMusicID = $iMusicID;";
            UpdateSQL ($strQuery,"update");
        }
    }

    if ($btnSubmit == 'Delete')
    {
            $iMusicID = substr(trim($_POST['iMusicID']),0,9);

            $strQuery = "delete from tblDanceMusic where iMusicID = $iMusicID;";
            UpdateSQL ($strQuery,"delete");
    }

    if ($btnSubmit == 'Insert')
    {
        $iStyleID = CleanSQLInput(substr(trim($_POST['cmbStyle']),0,99));
        $strSong= CleanSQLInput(substr(trim($_POST['txtSong']),0,99));
        $strPerformer = CleanSQLInput(substr(trim($_POST['txtPerformer']),0,99));
        
        if ($iStyleID == '')
        {
            $iStyleID = 1;
        }

        if ($strSong== '')
        {
            print "<p>Please provide song name</p>\n";
        }
        else
        {
           $strQuery = "insert tblDanceMusic (iStyleID, vcSongTitle, vcPerformer)"
                      . "values ($iStyleID, '$strSong', '$strPerformer');";
            UpdateSQL ($strQuery,"insert");                
        }
    }
        
    //Print the normal form after update is complete.
    print "<p class=\"Header1\">Dance Music Administration</p>\n";
    
    print "<table>\n";
    print "<tr>\n";
    print "<th class=lbl>Update existing Songs</th>\n";
    print "<th width = 100></th>\n";
    print "<th class=lbl>Or Insert New one</th>\n";
    print "</tr>\n";
    print "<tr>\n";
    print "<td>\n";
    print "<table border = 0>\n";
    print "<tr>\n";
    print "<th class=lbl>Dance Style</th>\n";
    print "<th class=lbl>Song Name</th>\n";
    print "<th class=lbl>Performer</th>\n";
    print "</tr>\n";
    $strQuery = "SELECT iMusicID, iStyleID, vcSongTitle, vcPerformer FROM tblDanceMusic ORDER BY iStyleID;";
    if (!$Result = $dbh->query ($strQuery))
    {
        error_log ('Failed to fetch data. Error ('. $dbh->errno . ') ' . $dbh->error);
        error_log ($strQuery);
        print "<p class=\"Attn\" align=center>$ErrMsg</p>\n";
        exit(2);
    }
    while ($Row = $Result->fetch_assoc())
    {
        $strSong = $Row['vcSongTitle'];
        $strPerformer = $Row['vcPerformer'];
        $iStyleID = $Row['iStyleID'];
        $iMusicID = $Row['iMusicID'];
        if ($WritePriv <=  $Priv)
        {
            print "<form method=\"POST\">\n";
            print "<input type=\"hidden\" value=\"$iMusicID\" name=\"iMusicID\">\n";
            print "<tr valign=\"top\">\n";
            $strQuery = "SELECT iStyleID,vcStyleName FROM tblDanceStyle ORDER BY iStyleID;";
            if (!$Result2 = $dbh->query ($strQuery))
            {
                error_log ('Failed to fetch data. Error ('. $dbh->errno . ') ' . $dbh->error);
                error_log ($strQuery);
                print "<p class=\"Attn\" align=center>$ErrMsg</p>\n";
                exit(2);
            }
            print "<td>\n";
            print "<select size=\"1\" name=\"cmbStyle\">\n";
            while ($Row2 = $Result2->fetch_assoc())
            {
                if ($Row2['iStyleID'] == $Row['iStyleID'])
                {
                    print "<option selected value=\"{$Row2['iStyleID']}\">{$Row2['vcStyleName']}</option>\n";
                }
                else
                {
                    print "<option value=\"{$Row2['iStyleID']}\">{$Row2['vcStyleName']}</option>\n";
                }
            }
            print "</select>\n";
            print "</td>\n";
            print "<td><input type=\"text\" value=\"$strSong\" name=\"txtSong\" size=\"15\" ></td>\n";
            print "<td><input type=\"text\" value=\"$strPerformer\" name=\"txtPerformer\" size=\"15\"></td>\n";
            print "<td><input type=\"Submit\" value=\"Save\" name=\"btnSubmit\"></td>";
            print "<td><input type=\"Submit\" value=\"Delete\" name=\"btnSubmit\"></td>";
            print "</tr>\n";
            print "</form>\n";
        }
    }
    print "</table>\n";	    
    print "</td>\n";
    print "<td>\n";
    print "</td>\n";
    print "<td valign=\"top\">\n";
    print "<form method=\"POST\">\n";
    print "<table>\n";
    print "<tr>\n<td align = right class = lbl>Dance Style: </td>\n";
    print "<td>";
    $strQuery = "SELECT iStyleID,vcStyleName FROM tblDanceStyle ORDER BY iStyleID;";
    if (!$Result = $dbh->query ($strQuery))
    {
        error_log ('Failed to fetch data. Error ('. $dbh->errno . ') ' . $dbh->error);
        error_log ($strQuery);
        print "<p class=\"Attn\" align=center>$ErrMsg</p>\n";
        exit(2);
    }
    print "<select size=\"1\" name=\"cmbStyle\">\n";

    while ($Row = $Result->fetch_assoc())
    {
        print "<option value=\"{$Row['iStyleID']}\">{$Row['vcStyleName']}</option>\n";
    }
    print "</select>\n";
    print "</td>\n";
    print "</tr>\n";
    print "<tr>\n";
    print "<td align=\"right\" class=\"lbl\">Song Name: </td>\n";
    print "<td><input type=\"text\" name=\"txtSong\" size=\"45\"></td>\n";
    print "</tr>\n";
    print "<tr>\n";
    print "<td align=\"right\" class=\"lbl\">Performer: </td>\n";
    print "<td><input type=\"text\" name=\"txtPerformer\" size=\"45\"></td>\n";
    print "</tr>\n";
    print "<tr>\n";
    print "<td colspan=2 align=center><input type=\"Submit\" value=\"Insert\" name=\"btnSubmit\"></td>\n";
    print "</tr>\n";
    print "</table>\n";
    print "</form>\n";
    print "</td>\n";
    print "</tr>\n";
    print "</table>";

    require("footer.php");
?>
