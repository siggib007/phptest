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
  if (!$Result = $dbh->query ($strQuery))
  {
    error_log ('Failed to fetch Content data. Error ('. $dbh->errno . ') ' . $dbh->error);
    error_log ($strQuery);
    print "<p class=\"Attn\" align=center>$ErrMsg</p>\n";
    exit(2);
  }
  $Row = $Result->fetch_assoc();
  $PageHeader = $Row['vcPageHeader'];
  $PageText = $Row['tPageText'];
  $bCRLF = $Row['bLineBreak'];
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
    print "<p class=MainText>\n$PageText</p>\n";
  }

  if (isset($iUserID))
  {
    $arrUserPrefs = array();
    $strQuery = "SELECT t.*,v.vcValue,v.iUserID ".
                "FROM tblUsrPrefTypes t LEFT JOIN tblUsrPrefValues v ON t.iID = v.iTypeID ".
                "WHERE v.iUserID = $iUserID OR v.iUserID IS NULL;";

    if (!$Result = $dbh->query ($strQuery))
    {
      error_log ('Failed to fetch data. Error ('. $dbh->errno . ') ' . $dbh->error);
      error_log ($strQuery);
      print "<p class=\"Attn\" align=center>$ErrMsg</p>\n";
      exit(2);
    }
    while ($Row = $Result->fetch_assoc())
    {
      if (is_null($Row["iUserID"]))
      {
        print $Row["vcCode"] ." has NOT been set<br>\n";
        $strQuery = "INSERT INTO tblUsrPrefValues (iTypeID, iUserID) VALUES ($Row[iID],$iUserID );";
        if(UpdateSQL ($strQuery, "insert"))
        {
          print $Row["vcCode"] ." has now been set to ''<br>\n";
        }
        else
        {
          print "Failed to set " . $Row["vcCode"];
        }
      }
      else
      {
        print $Row["vcCode"] ." has been set to '$Row[vcValue]'<br>\n";
      }
      $arrUserPrefs[] = $Row;
    }
  }
  else
  {
    print "Not logged in";
  }


  require("footer.php");
?>
