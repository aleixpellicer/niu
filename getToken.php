<?php
include 'config.php';

$login = "https://account.niu.com/appv2/login";
$login_post = 'account='.$userName.'&password='.$pass;

$curl = curl_init();
curl_setopt_array($curl, array(
  CURLOPT_URL => $login,
  CURLOPT_POSTFIELDS => $login_post,
  CURLOPT_RETURNTRANSFER => true,
  CURLOPT_ENCODING => "",
  CURLOPT_MAXREDIRS => 10,
  CURLOPT_TIMEOUT => 30,
  CURLOPT_SSL_VERIFYPEER => false,
  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
  CURLOPT_CUSTOMREQUEST => "POST",
  CURLOPT_HTTPHEADER => array(
    "cache-control: no-cache",
    "postman-token: e6166ae3-8b48-9654-5243-990930f38591"
  ),
));
$response = curl_exec($curl);
$err = curl_error($curl);
curl_close($curl);

$data = json_decode($response);
$token_post = 'token='.$data->data->token;

echo 'Token: '.$data->data->token.'<br><br>';

$curl2 = curl_init();
curl_setopt_array($curl2, array(
  CURLOPT_URL => 'https://app-api.niu.com/motoinfo/list',
  CURLOPT_POSTFIELDS => $token_post,
  CURLOPT_RETURNTRANSFER => true,
  CURLOPT_ENCODING => "",
  CURLOPT_MAXREDIRS => 10,
  CURLOPT_TIMEOUT => 30,
  CURLOPT_SSL_VERIFYPEER => false,
  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
  CURLOPT_CUSTOMREQUEST => "POST",
  CURLOPT_HTTPHEADER => array(
    "cache-control: no-cache",
    "postman-token: e6166ae3-8b48-9654-5243-990930f38591"
  ),
));
$response2 = curl_exec($curl2);
$err = curl_error($curl2);
curl_close($curl2);
$data2 = json_decode($response2);

foreach($data2->data as $vehicle){
  echo 'SerialNumber for vehicle ' . $vehicle->name .' is: '.$vehicle->sn . '<br><br>';
}