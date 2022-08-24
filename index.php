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

  print "<p class=Header1>$PageHeader</p>";
  if (substr($PageText,0,1)=="<")
  {
    print "$PageText\n";
  }
  else
  {
    if ($bCRLF == 1)
    {
        $PageText = str_replace("\n","<br>\n",$PageText);
    }
    $PageText = str_replace("\r\n","\n",$PageText);
    $PageText = str_replace("\r","\n",$PageText);
    $PageText = str_replace("\n\n","\n</p>\n<p class=MainText>\n",$PageText);
    // $varpos = stripos($PageText,"$");
    // if ($varpos !== False)
    // {
    //   $varEnd = stripos($PageText," ",$varpos);
    //   if (!is_numeric($a[$varpos+1]))
    //   {
    //     $varName = substr($a,$varpos,$varend-$varpos);
    //     $PageText = str_replace($varName,"$varName",$PageText);
    //   }
    // }
    print "<p class=MainText>\n$PageText</p>\n";
  }
  printPg("This is an alert","alert");
  printPg("This is an error","error");
  printPg("This is a blue note","note");
  printPg("This normal centered","center");
  printPg("This normal text","normal");
  $strQuery = "SELECT iMenuOrder FROM tblmenutype WHERE iMenuID = 1;";
  $QueryData = QuerySQL($strQuery);
  $arrTmp = array_values($QueryData[1][0]);
  $itmp = $arrTmp[0];
  $itmp--;
  printPg($itmp,"note");
  require("footer.php");
?>
