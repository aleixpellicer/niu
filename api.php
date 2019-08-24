<?php
include 'config.php';

$token_post = 'pagesize=100&token='.$token.'&sn='.$serialNumber;

$ch1 = curl_init();
$ch2 = curl_init();
$ch3 = curl_init();

$rides = $apiUrl . '/v3/motor_data/track';
curl_setopt_array($ch1, array(
  CURLOPT_URL => $rides,
  CURLOPT_POSTFIELDS => $token_post,
  CURLOPT_RETURNTRANSFER => true,
  CURLOPT_ENCODING => "",
  CURLOPT_MAXREDIRS => 10,
  CURLOPT_TIMEOUT => 60,
  CURLOPT_SSL_VERIFYPEER => false,
  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
  CURLOPT_CUSTOMREQUEST => "POST",
  CURLOPT_HTTPHEADER => array(
    "cache-control: no-cache"
  ),
));

$battery = $apiUrl . '/v3/motor_data/index_info?sn='.$serialNumber;
curl_setopt_array($ch2, array(
  CURLOPT_URL => $battery,
  CURLOPT_POSTFIELDS => $token_post,
  CURLOPT_RETURNTRANSFER => true,
  CURLOPT_ENCODING => "",
  CURLOPT_MAXREDIRS => 10,
  CURLOPT_TIMEOUT => 60,
  CURLOPT_SSL_VERIFYPEER => false,
  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
  CURLOPT_CUSTOMREQUEST => "GET",
  CURLOPT_HTTPHEADER => array(
    "cache-control: no-cache"
  ),
));

$tally = $apiUrl . '/motoinfo/overallTally';
curl_setopt_array($ch3, array(
  CURLOPT_URL => $tally,
  CURLOPT_POSTFIELDS => $token_post,
  CURLOPT_RETURNTRANSFER => true,
  CURLOPT_ENCODING => "",
  CURLOPT_MAXREDIRS => 10,
  CURLOPT_TIMEOUT => 60,
  CURLOPT_SSL_VERIFYPEER => false,
  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
  CURLOPT_CUSTOMREQUEST => "POST",
  CURLOPT_HTTPHEADER => array(
    "cache-control: no-cache"
  ),
));

$mh = curl_multi_init();

curl_multi_add_handle($mh, $ch1);
curl_multi_add_handle($mh, $ch2);
curl_multi_add_handle($mh, $ch3);

$running = null;
do {
  curl_multi_exec($mh, $running);
} while ($running);

curl_multi_remove_handle($mh, $ch1);
curl_multi_remove_handle($mh, $ch2);
curl_multi_remove_handle($mh, $ch3);
curl_multi_close($mh);

$tracks = json_decode(curl_multi_getcontent($ch1));
$batteryInfo = json_decode(curl_multi_getcontent($ch2));
$totalMileage = json_decode(curl_multi_getcontent($ch3));

$ride = $tracks->data[0];
$json_response = array();

$startDate = DateTime::createFromFormat('U.u', number_format($ride->startPoint->date/1000, 6, '.', ''));
$startDate = $startDate->setTimeZone(new DateTimeZone('Europe/Madrid'));

$endDate = DateTime::createFromFormat('U.u', number_format($ride->lastPoint->date/1000, 6, '.', ''));
$endDate = $endDate->setTimeZone(new DateTimeZone('Europe/Madrid'));

$date = DateTime::createFromFormat('Ymd', $ride->date, new DateTimeZone('Europe/Madrid'));

$dtF = new DateTime('@0');
$dtT = new DateTime("@$ride->ridingtime");
$humanReadableSeconds = $dtF->diff($dtT)->format('%H:%I:%S');

$json_response['totalMileage'] = $totalMileage->data->totalMileage;

if($batteryNumber == 2){
  $json_response['batteryInfo'] = floor(($batteryInfo->data->batteries->compartmentA->batteryCharging + $batteryInfo->data->batteries->compartmentB->batteryCharging)/2);
} else {
  $json_response['batteryInfo'] = $batteryInfo->data->batteries->compartmentA->batteryCharging;
}

$restToCharge = ($desiredChargeLevel - $json_response['batteryInfo']);
$minutesToCharge = round($restToCharge * $minutesPercent);
$startCharging = (new DateTime($endChargingTime))->sub(DateInterval::createFromDateString($minutesToCharge . ' minutes'))->format('H:i');

$json_response['startCharging'] = $startCharging;

$json_response['date'] = $date->format("d/m/Y");
$json_response['start'] = $startDate->format("H:i");
$json_response['end'] = $endDate->format("H:i");
$json_response['duration'] = $humanReadableSeconds;
$json_response['avSpeed'] = $ride->avespeed;
$json_response['distance'] = round($ride->distance/1000, 1);

$batteryUsage = ($ride->startPoint->battery - $ride->lastPoint->battery);
$batteryPerKm = $batteryUsage / ($ride->distance/1000);
$powerUsage = ($batteryCapacityWh/100)*$batteryUsage;
$ridePrice = $powerUsage/1000 * $kwhPrice;

$json_response['batteryUsage'] = $batteryUsage;
$json_response['batteryPerKm'] = round($batteryPerKm, 2);
$json_response['ridePrice'] = round($ridePrice, 2);

header('Content-Type: application/json');
echo json_encode($json_response);