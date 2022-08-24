<?php
  /*
  Copyright © 2009,2015,2022  Siggi Bjarnason.
  Licensed under GNU GPL v3 and later. Check out LICENSE.TXT for details   
  or see <https://www.gnu.org/licenses/gpl-3.0-standalone.html>
  */

  require("header.php");
  $page_redirected_from = $_SERVER['REQUEST_URI'];  // this is especially useful with error 404 to indicate the missing page.
  $server_url = "http://" . $_SERVER["SERVER_NAME"];

  switch(getenv("REDIRECT_STATUS"))
  {
      # "400 - Bad Request"
      case 400:
      $error_code = "400 - Bad Request";
      $explanation = "The syntax of the URL submitted by your browser could not be understood. Please verify the address and try again.";
      $redirect_to = "";
      break;

      # "401 - Unauthorized"
      case 401:
      $error_code = "401 - Unauthorized";
      $explanation = "This section requires a password or is otherwise protected. If you feel you have reached this page in error, please return to the login page and try again, or contact the webmaster if you continue to have problems.";
      $redirect_to = "";
      break;

      # "403 - Forbidden"
      case 403:
      $error_code = "403 - Forbidden";
      $explanation = "This section requires a password or is otherwise protected. If you feel you have reached this page in error, please return to the login page and try again, or contact the webmaster if you continue to have problems.";
      $redirect_to = "";
      break;

      # "404 - Not Found"
      case 404:
      $error_code = "404 - Not Found";
      $explanation = "The requested resource '" . $page_redirected_from . "' could not be found on this server. Please verify the address and try again.";
      break;

      # "500 - Not Found"
      case 500:
      $error_code = "500 - Server problem";
      $explanation = "The server is having a really bad day and couldn't for some reason process what you were doing. Sorry about that.";
      break;

      # unknown error
      default:
      $error_code = $_SERVER["REDIRECT_STATUS"] . " Error";
      $explanation = "Unknown error occured";
  }
  
  printPg ("Error Code $error_code","error");
  printPg ($explanation,"error");
  printPg ("If you have any questions please reach out to us at <a href=mailto:$SupportEmail>$SupportEmail</a>","note");
  require("footer.php");  
?>