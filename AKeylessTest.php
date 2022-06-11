<?php
  print "<h1>Fetching secret from AKEYLESS secret management system at akeyless.io</h1>\n";
  
  function FetchKeylessStatic ($arrNames)
  {
    $AccessID = getenv("KEYLESSID");
    $AccessKey = getenv("KEYLESSKEY");
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
    
    $arrValues = array();
    foreach ($arrNames as $SecretName) 
    {
      //print "<p>Fetching secret named $SecretName</p>\n";
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
        $arrValues[$SecretName] = $arrResponse["response"][0];
      }
      return $arrValues;
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