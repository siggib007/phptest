<?php
  /*
  Copyright © 2009,2015,2022  Siggi Bjarnason.
  Licensed under GNU GPL v3 and later. Check out LICENSE.TXT for details   
  or see <https://www.gnu.org/licenses/gpl-3.0-standalone.html>
  */

  require("header.php");
  $strQuery = "SELECT * FROM tblContent WHERE iMenuID = '$iMenuID' and dtTimeStamp = (select max(dtTimeStamp) from tblContent where iMenuID = '$iMenuID');";
  $QueryData = QuerySQL($strQuery);

  if($QueryData[0] > 0)
  {
    foreach($QueryData[1] as $Row)
    {
      $PageHeader = $Row['vcPageHeader'];
      $PageText = $Row['tPageText'];
      $bCRLF = $Row['bLineBreak'];
    }
  }
  else
  {
    error_log("Rowcount: $QueryData[0] Msg:$QueryData[1]");
    $PageHeader = "Error occured";
    $PageText = "Failed to fetch the pagetext from the database";
    $bCRLF = "";
  }

  printPg("$PageHeader","h1");
  if(substr($PageText,0,1)=="<")
  {
    print "$PageText\n";
  }
  else
  {
    if($bCRLF == 1)
    {
      $PageText = str_replace("\n","<br>\n",$PageText);
    }
    $PageText = str_replace("\r\n","\n",$PageText);
    $PageText = str_replace("\r","\n",$PageText);
    $PageText = str_replace("\n\n","\n</p>\n<p class=MainText>\n",$PageText);

    printPg("$PageText");
  }
  printPg("This is an alert","alert");
  printPg("This is an error","error");
  printPg("This is a blue note","note");
  printPg("This normal centered","center");
  printPg("This normal text","normal");
  printPg("This default normal");
  $strQuery = "SELECT iMenuOrder FROM tblmenutype WHERE iMenuID = 11;";
  $QueryData = QuerySQL($strQuery);
  $arrTmp = array_values($QueryData[1][0]);
  $itmp = $arrTmp[0];
  $itmp--;
  printPg($itmp,"note");
  if(strtolower($DevEnvironment) != "true")
  {
    printPg("This is not a dev Env","note");
  }
  else 
  {
    printPg("Welcome to your Dev Env","note");
  }

  require("footer.php");
?>
