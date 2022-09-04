<?php
  /*
  Copyright © 2009,2015,2022  Siggi Bjarnason.
  Licensed under GNU GPL v3 and later. Check out LICENSE.TXT for details   
  or see <https://www.gnu.org/licenses/gpl-3.0-standalone.html>

  Page that imports new and unknown php into the menu table

  */

  require_once("header.php");
  $arrFiles = array();
  $handle = opendir(".");

  if($handle) {
    while(($entry = readdir($handle)) !== FALSE) 
    {
      $arrFiles[] = $entry;
    }
  }

  closedir($handle);
  $strQuery = "SELECT vcLink FROM tblmenu";
  $QueryData = QuerySQL($strQuery);
  $KnownFiles = array();

  if($QueryData[0] > 0)
  {
    foreach($QueryData[1] as $Row)
    {
      $KnownFiles[] = $Row["vcLink"];
    }
  }
  else
  {
    if($QueryData[0] == 0)
    {
      printPg("No Records","note");
    }
    else
    {
      $strMsg = Array2String($QueryData[1]);
      error_log("Query of $strQuery did not return data. Rowcount: $QueryData[0] Msg:$strMsg");
      printPg($ErrMsg,"error");
    }
  }

  printPg("Here are the files that are missing","h1");
  foreach($arrFiles as $file)
  {
    if(substr($file,-3)=="php" and ! in_array($file,$KnownFiles))
    {
      printPg("$file","note");
      $strQuery = "INSERT INTO tblmenu (vcTitle, vcLink, iReadPriv, vcHeader, bAdmin, bSecure) VALUES ('$file', '$file', '500', '$file', '0', '0');";
      if(UpdateSQL($strQuery,"insert"))
      {
        printPg("Inserted successfully","note");
      }
      else 
      {
        printPg("Insert Failed","error");
      }
    }
  }
  require_once("footer.php");
?>
