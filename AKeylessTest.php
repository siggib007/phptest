<?php

  $AccessID = getenv("KEYLESSID");
  $AccessKey = getenv("KEYLESSKEY");
  $client = new http\Client;
  $request = new http\Client\Request;
  $request->setRequestUrl("https://rest.akeyless-security.com/auth?access-id=$AccessID&access-type=api_key&access-key=$AccessKey");
  $request->setRequestMethod('POST');
  $request->setOptions(array());
  $request->setHeaders(array(
    'accept' => 'application/json'
  ));
  $client->enqueue($request)->send();
  $response = $client->getResponse();
  $token = $response["token"]

  $request->setRequestUrl("https://rest.akeyless-security.com/get-secret-value?name=MySecret1&token=$token");
  $request->setRequestMethod('POST');
  $request->setOptions(array());
  $request->setHeaders(array(
    'accept' => 'application/json'
  ));
  $client->enqueue($request)->send();
  $response = $client->getResponse();
  echo $response->getBody();
?>