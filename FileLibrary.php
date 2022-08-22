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
  $DocRoot = $ConfArray["AdminUploadDir"];
	if (isset($_POST['btnSubmit']))
	{
    $btnSubmit = $_POST['btnSubmit'];
	}
	else
	{
    $btnSubmit = "";
	}

  if ($btnSubmit == "")
  {
    printPg("Here are the files in the folder $DocRoot","note");
    $arrFiles = scandir($DocRoot);
    print "<div class=\"CenterBox\">\n";
    print "<form method=\"POST\">\n";
    $iNum = 1;
    foreach ($arrFiles as $strFileName) 
    {
      if (substr($strFileName,0,1) != ".")
      {
        $strBoxName = "File$iNum";
        $strLink = "<a href=\"$DocRoot/$strFileName\" target=_blank>$strFileName</a>";
        print "<input type=\"checkbox\" name=\"$strBoxName\" value=\"$strFileName\">$strLink<br>\n";
        $iNum++;
      }
    }
    printPg("Delete is permanent and there is no undo.<br>\n There is no confirmation either!!!","alert");
    print "<div class=\"BlueNote\">\n";
    print "<input type=\"submit\" value=\"Delete\" name=\"btnSubmit\">\n";
    print "<input type=\"submit\" value=\"Delete ALL\" name=\"btnSubmit\">\n";
    print "</div>\n";
    print "</form>\n";
    print "</div>\n";
  }

  if ($btnSubmit == "Delete")
  {
    print "<div class=\"CenterBox\">\n";
    foreach ($_POST as $key => $value) 
    {
      if(substr($key,0,4)=="File")
      {
        $FileName = "$DocRoot/$value";
        print "Deleting $FileName<br>\n";
        unlink($FileName);
      }
    }
    print "</div>\n";
  }

  if ($btnSubmit == "Delete ALL")
  {
    print "<div class=\"CenterBox\">\n";
    printPg("Deleting everything","alert");
    $arrFiles = scandir($DocRoot);
    foreach ($arrFiles as $strFileName)
    {
      if (substr($strFileName,0,1) != ".")
      {
        unlink("$DocRoot/$strFileName");
      }
    }
    printPg("Done","note");
    print "</div>\n";
  }
  require("footer.php");
?>
