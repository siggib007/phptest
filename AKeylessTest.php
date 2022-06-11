<?php
  print "<h1>Fetching secret from AKEYLESS secret management system at akeyless.io</h1>";
  $SecretName = "/TSC/AnotherTest2";
  print "<p>Fetching secret named $SecretName</p>";
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
  $arrResponse = json_decode($response, TRUE);
  $token = $arrResponse["token"];
  $url = "https://rest.akeyless-security.com/get-secret-value?name=$SecretName&token=$token";
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

  $arrResponse = json_decode($response, TRUE);
  $secValue = $arrResponse["response"];
  print "<p>The value of that secret is:$secValue[0]</p>";
?>