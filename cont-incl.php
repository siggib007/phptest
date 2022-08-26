<?php
  /*
  Copyright © 2009,2015,2022  Siggi Bjarnason.
  Licensed under GNU GPL v3 and later. Check out LICENSE.TXT for details   
  or see <https://www.gnu.org/licenses/gpl-3.0-standalone.html>

  Modular code to insert contact information into any page
  */

  printPg("Feel free to contact us with any questions, concerns or feedback.");

  $strQuery = "SELECT * FROM tblContactInfo WHERE vcType = 'Address' ORDER BY iSequence";
  $QueryData = QuerySQL($strQuery);
  if($QueryData[0] > 0)
  {
    print "<div class=\"Header3\">Address</div>";
    print "<div class=\"MainText\">";
    foreach($QueryData[1] as $Row)
    {
      print $Row['vcValue'] . "<br>\n";
    }
    print "</div>\n";
  }
  else
  {
    $strMsg = implode(";",$QueryData[1]);
    error_log("Query of $strQuery did not return data. Rowcount: $QueryData[0] Msg:$strMsg");
    printPg("Error occured fetching admin menu from DB","error");
  }

  $strQuery = "SELECT * FROM tblContactInfo WHERE vcType = 'Email' ORDER BY iSequence";
  $QueryData = QuerySQL($strQuery);
  if($QueryData[0] > 0)
  {
    print "<div class=\"Header3\">Electronic mail</div>";
    print "<p class=\"MainText\">";
    foreach($QueryData[1] as $Row)
    {
      print $Row['vcLabel'] . ": <a href=\"mailto:" . $Row['vcValue'] . "\">" .
            $Row['vcValue'] . "</a><br>\n";
    }
    print "</p>\n";
  }
  else
  {
    $strMsg = implode(";",$QueryData[1]);
    error_log("Query of $strQuery did not return data. Rowcount: $QueryData[0] Msg:$strMsg");
    printPg("Error occured fetching admin menu from DB","error");
  }

  $strQuery = "SELECT * FROM tblContactInfo WHERE vcType = 'Phone' ORDER BY iSequence";
  $QueryData = QuerySQL($strQuery);
  if($QueryData[0] > 0)
  {
    print "<div class=\"Header3\">Phone Numbers</div>";
    print "<p class=\"MainText\">";
    foreach($QueryData[1] as $Row)
    {
      print $Row['vcLabel'] . ": " . $Row['vcValue'] . "<br>\n";
    }
    print "</p>\n";
  }
  else
  {
    $strMsg = implode(";",$QueryData[1]);
    error_log("Query of $strQuery did not return data. Rowcount: $QueryData[0] Msg:$strMsg");
    printPg("Error occured fetching admin menu from DB","error");
  }
?>
