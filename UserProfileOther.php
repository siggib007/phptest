<?php
  //Copyright © 2009,2015,2022  Siggi Bjarnason.
  //
  //This program is free software: you can redistribute it and/or modify
  //it under the terms of the GNU General Public License as published by
  //the Free Software Foundation, either version 3 of the License, or
  //(at your option) any later version.
  //
  //This program is distributed in the hope that it will be useful,
  //but WITHOUT ANY WARRANTY; without even the implied warranty of
  //MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
  //GNU General Public License for more details.
  //
  //You should have received a copy of the GNU General Public License
  //along with this program.  If not, see <http://www.gnu.org/licenses/>

  require("header.php");

  $arrMFAOptions = LoadMFAOptions($iUserID);

  if ($btnSubmit =="Reset Recovery Code")
  {
    GenerateRecovery($iUserID);
  }

  if ($btnSubmit =="Delete Account")
  {
    $iRegNum = trim($_POST['iUserID']);
    $BeenSubmitted = trim($_POST['BeenSubmitted']);

    if($iRegNum)
    {
      if($BeenSubmitted == "True")
      {
        if(UpdateSQL($strQuery, "delete"))
        {
          $strQuery = "Delete from tblUsers where iUserID='$iRegNum';";
          if ($dbh->query ($strQuery))
          {
            print "Account Deleted successful, please close your browser.<br>\n";
            require_once("KillSession.php");
          }
          else
          {
            $strError = "Database update failed. Error (". $dbh->errno . ") " . $dbh->error . "\n";
            $strError .= "$strQuery\n";
            error_log($strError);
            if(EmailText("$SupportEmail","Automatic Error Report", $strError . "\n\n\n" . $strQuery ,"From:$SupportEmail"))
            {
              print "<p class=\"Error\">We seem to be experiencing technical difficulties. " .
                    "We have been notified. Please try again later. If you have any " .
                    "questions you can contact us at $SupportEmail.</p>";
            }
            else
            {
              print "<p class=\"Error\">We seem to be experiencing technical difficulties. " .
                    "Please send us a message at $SupportEmail with information about " .
                    "what you were doing.</p>";
            }
          }
        }
      }
      else
      {
        print "<center>\n<form method=\"post\">\n";
        print "<p class=\"Error\">Are you sure you want to delete your account? <br>\n";
        print "Just leave this page anyway you please if you do not want to delete it. ";
        print "Otherwise press \"Delete Account\" again.</p>\n";
        print "<input type=\"submit\" value=\"Delete Account\" name=\"btnSubmit\"><br>\n";
        print "<input type=\"hidden\" name=\"BeenSubmitted\" value=\"True\">\n";
        print "<input type=\"hidden\" name=\"iUserID\" value=\"$iRegNum\">\n";
        print "</form>\n</center>\n";
      }
    }
    else
    {
      print "<p class=\"Error\">Registration number seems to have gotten lost in transport. Please try again" .
            "<br>Feel free to contact us at $SupportEmail if you have questions.</p>\n";
    }
  }

  print "<div class=\"MainTextCenter\">\n";

  if ($_SESSION["bMFA_active"])
  {
    print "<p>&nbsp;</p>\n<p class=\"Header2\">Reset Recovery Code</p>\n";
    print "To reset your recovery code, click this button.<br>\n";
    print "<form method=\"post\">\n";
    print "<input type=\"submit\" value=\"Reset Recovery Code\" name=\"btnSubmit\">\n";
    print "</form>\n";
    print "</div>\n";
  }
  print "<p>&nbsp;</p>\n<p>&nbsp;</p>\n<p class=\"Header2\">Account deletion</p>\n";
  print "<div class=\"MainTextCenter\">\n";
  print "<p>If you wish to completely delete your account you can do that here.</p>\n";
  print "<form method=\"post\">\n";
  print "<input type=\"submit\" value=\"Delete Account\" name=\"btnSubmit\">\n";
  print "<input type=\"hidden\" name=\"BeenSubmitted\" value=\"false\">\n";
  print "<input type=\"hidden\" name=\"iUserID\" size=\"5\" value=\"$iUserID\">\n";
  print "</form>\n";
  print "</div>\n";
  require("footer.php");
?>
