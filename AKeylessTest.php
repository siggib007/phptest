<?php
  print "<h1>Fetching secret from AKEYLESS secret management system at akeyless.io</h1>\n";
  
  function FetchKeylessStatic ($arrNames)
  {
    $AccessID = getenv("KEYLESSID");
    $AccessKey = getenv("KEYLESSKEY");
    $APIEndpoint = "https://api.akeyless.io";
    $PostData = array();
    $PostData["access-type"] = "access_key";
    $PostData["access-id"] = $AccessID;
    $PostData["access-key"] = $AccessKey;
    $jsonPostData = json_encode($PostData);
    $Service = "/auth";
    $url = $APIEndpoint.$Service;
    $curl = curl_init();
    $curlOpt = array(
      CURLOPT_URL => $url,
      CURLOPT_POSTFIELDS => $jsonPostData,
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
    
    $arrValues = array();
    $Service = "/get-secret-value";
    $url = $APIEndpoint.$Service;
    $PostData = array();
    $PostData["token"] = $token;
    $PostData["names"] = $arrNames;
    $jsonPostData = json_encode($PostData);

    $curl = curl_init();
    $curlOpt = array(
      CURLOPT_URL => $url,
      CURLOPT_POSTFIELDS => $jsonPostData,
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
    
    return json_decode($response, TRUE);
  }

  $arrname = array();

  $arrname[] = "MySecret1";
  $arrname[] = "MyFirstSecret";
  $arrname[] = "/TSC/AnotherTest2";
  $arrname[] = "/Test/MyPathTest";

  $arrSecretValues = FetchKeylessStatic($arrname);

  print "<p>Here are the secret names and corrensponding values</p>\n";
  foreach ($arrSecretValues as $key => $value) 
  {
    print "$key: $value <br>\n";
  }
?>