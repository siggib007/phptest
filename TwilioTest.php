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
  if (isset($_POST['txtNumber']))
	{
    $strNumber = CleanReg(trim($_POST['txtNumber']));
	}
	else
	{
    $strNumber ="";
	}
	if (isset($_POST['txtmsg']))
	{
    $strMsg = CleanReg(trim($_POST['txtmsg']));
	}
	else
	{
    $strMsg ="";
	}

	if (isset($_POST['btnSubmit']))
	{
    $btnSubmit = $_POST['btnSubmit'];
	}
	else
	{
    $btnSubmit = "";
	}

	print "<p class=\"Header1\">Twilio SMS test page</p>\n";

	if ($btnSubmit == 'Submit')
	{
    $msg = $strMsg;
    $number = $strNumber;
    $response = SendTwilioSMS ($msg,$number);
    if ($response[0])
    {
      print "Message successfully queued for sending";
    }
    else
    {
      print "A failure occured:<br>\n";
      $arrResponse = json_decode($response[1], TRUE);
      $errmsg = "";
      if (array_key_exists("message",$arrResponse))
      {
        $errmsg .= $arrResponse["message"];
      }
      if (array_key_exists("more_info",$arrResponse))
      {
        $errmsg .= " For more information see " . $arrResponse["more_info"];
      }
      print "$errmsg<br>\n";
    }
  }


  print "<form method=\"POST\">";
  print "Phone number to send to:";
  print "<input type=\"text\" name=\"txtNumber\" size=\"30\"><br>\n";
  print "Message to send:";
  print "<input type=\"text\" name=\"txtmsg\" size=\"30\"><br>\n";
  print "<input type=\"submit\" value=\"Submit\" name=\"btnSubmit\">";
  print "</form>";

  require("footer.php");
?>
