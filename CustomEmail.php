<?php

//Copyright © 2009,2015  Siggi Bjarnason.
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
print "<center>\n";
print "<h1>This is only a test</h2>\n";
print "</center>";
print "Testing new email function using swiftmail<br>\nFirst from Geek<br>\n";
$strHTMLMsg = "This is a test of the swift mail system with speacial headers, remote image and all.<br><img src=\"http://www.studio-b-dance.com/img/StudioB320.jpg\" height=\"100\"/>";
$FromEmail = "Geek Web Master|web@supergeek.us";
$toEmail = "Siggi Bjarnason|siggi@bjarnason.us";
$strSubject = "Geeky Sendmail function test with special headers";
$strFileName = "";
$strAttach = "";
$strAddHeader = "X-Testing:This is my test header";
$count = SendHTMLAttach ($strHTMLMsg, $FromEmail, $toEmail, $strSubject, $strFileName, $strAttach,$strAddHeader);
print "Successfully sent $count recepients<br>\n";
// print "then from studio b<br>\n";
// $strHTMLMsg = "Studio B email testing";
// $FromEmail = "Studio B Dance|info@studio-b-dance.com";
// $toEmail = "Siggi Bjarnason|siggi@bjarnason.us";
// $strSubject = "More Studio B Sendmail function test";
// $count = SendHTMLAttach ($strHTMLMsg, $FromEmail, $toEmail, $strSubject, $strFileName, $strAttach,$strAddHeader);

// print "Successfully sent $count recepients<br>\n";

print "<br>I'm all done<br>\n";
require("footer.php");
?>