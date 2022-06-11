<?php
  require("secrets.php");
  $url = "https://rest.akeyless-security.com/auth?access-id=$AccessID&access-type=api_key&access-key=$AccessKey";
  $curl = curl_init();
  $curlOpt = array(
    CURLOPT_URL => $url,
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_ENCODING => '',
    CURLOPT_MAXREDIRS => 10,
    CURLOPT_TIMEOUT => 0,
    CURLOPT_FOLLOWLOCATION => true,
    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
    CURLOPT_CUSTOMREQUEST => 'POST',
    CURLOPT_HTTPHEADER => array('accept: application/json'),);
  curl_setopt_array($curl, $curlOpt);

  $response = curl_exec($curl);

  curl_close($curl);
  echo $response;
?>