<!DOCTYPE html>
<html manifest="cache.manifest.php">
<head>
	<title>Niu</title>
	<meta http-equiv="Content-type" content="text/html; charset=utf-8" />
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<meta name="apple-mobile-web-app-capable" content="yes">
	<link href="https://fonts.googleapis.com/css?family=Roboto:100,300,400,500,700" rel="stylesheet">
	<link rel="stylesheet" href="css/custom.css?v=2" type="text/css" charset="utf-8">

	<link rel="apple-touch-icon" sizes="180x180" href="img/favicon/apple-touch-icon.png">
	<link rel="icon" type="image/png" sizes="32x32" href="img/favicon/favicon-32x32.png">
	<link rel="icon" type="image/png" sizes="16x16" href="img/favicon/favicon-16x16.png">
	<link rel="manifest" href="img/favicon/site.webmanifest">
	<link rel="mask-icon" href="img/favicon/safari-pinned-tab.svg" color="#5bbad5">
	<link rel="shortcut icon" href="img/favicon/favicon.ico">
	<meta name="msapplication-TileColor" content="#ffffff">
	<meta name="msapplication-config" content="img/favicon/browserconfig.xml">
	<meta name="theme-color" content="#ffffff">
</head>
<body>
	<h1><img src="img/niu-logo.svg"></h1>

  <div class="tracks-list">

    <?php
    include 'config.php';

    $rides = $apiUrl . '/v3/motor_data/track';
    $token_post = 'index=0&pagesize=100&token='.$token.'&sn='.$serialNumber;

      $curl = curl_init();
      curl_setopt_array($curl, array(
        CURLOPT_URL => $rides,
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
      $response = curl_exec($curl);
      $err = curl_error($curl);
      curl_close($curl);

    $tracks = json_decode($response);

    echo '<table>';
    echo '<thead><tr>';
    echo '<td class="td-date">Date</td>';
    echo '<td class="td-length">Length</td>';
    echo '<td class="td-battery">Battery</td>';
    echo '<td class="td-efficiency">Per 100Km.</td>';
    echo '<td class="td-cost">Cost</td>';
    echo '</tr></thead><tbody>';

    $totalAvespeed = 0;
    $totalDistance = 0;
    $totalRidingTime = 0;
    $totalBatteryUsage = 0;
    $totalBatteryPerKm = 0;
    $totalPowerUsage = 0;
    $totalRidePrice = 0;
    $totalCarRidePrice = 0;
    $powerAverage100kms = 0;
    $costAverage100kms = 0;

    foreach($tracks->data as $ride){

      $startDate = DateTime::createFromFormat('U.u', number_format($ride->startPoint->date/1000, 6, '.', ''));
      $startDate = $startDate->setTimeZone(new DateTimeZone('Europe/Madrid'));

      $endDate = DateTime::createFromFormat('U.u', number_format($ride->lastPoint->date/1000, 6, '.', ''));
      $endDate = $endDate->setTimeZone(new DateTimeZone('Europe/Madrid'));

      $date = DateTime::createFromFormat('Ymd', $ride->date, new DateTimeZone('Europe/Madrid'));

      $dtF = new DateTime('@0');
      $dtT = new DateTime("@$ride->ridingtime");
      $humanReadableSeconds = $dtF->diff($dtT)->format('%H:%I:%S');

      $totalRidingTime = $totalRidingTime + $ride->ridingtime;

      echo '<tr>';
      echo '<td>' . $date->format("d/m/Y") .'<br>' . $startDate->format("H:i") .'-' . $endDate->format("H:i") .'</td>';
      echo '<td>' . round($ride->distance/1000, 1) .' Km.<br>' . $humanReadableSeconds .'</td>';

      $startPoint = $ride->startPoint->battery;

      $batteryUsage = ($startPoint - $ride->lastPoint->battery);
      $batteryPerKm = $batteryUsage / ($ride->distance/1000);
      $powerUsage = ($batteryCapacityWh/100)*$batteryUsage;
      $ridePrice = $powerUsage/1000 * 0.12;
      $carPrice = ($ride->distance/1000) * 0.075;
      $power100kms = ($powerUsage / ($ride->distance/1000)) * 100;
      $cost100kms = ($power100kms/1000) * 0.12;

      echo '<td>-' . $batteryUsage .'%<br>' . round($powerUsage/1000, 1) .' kWh</td>';
      echo '<td>' . round($batteryPerKm, 2) .'%<br>' . round($power100kms/1000,1) .' kWh</td>';
      echo '<td>' . round($ridePrice, 2) .' €</td>';
      echo '</tr>';

      $totalAvespeed = $totalAvespeed + $ride->avespeed;
      $totalDistance = $totalDistance + $ride->distance;
      $totalBatteryPerKm = $totalBatteryPerKm + $batteryPerKm;
      $totalBatteryUsage = $totalBatteryUsage + $batteryUsage;
      $totalPowerUsage = $totalPowerUsage + $powerUsage;
      $totalRidePrice = $totalRidePrice + $ridePrice;
      $totalCarRidePrice = $totalCarRidePrice + $carPrice;
      $powerAverage100kms = $powerAverage100kms + $power100kms;
      $costAverage100kms = $costAverage100kms + $cost100kms;
    }

    $rows = count($tracks->data);

    $totalRidingTime = $totalRidingTime/3600;
    $totalDistance = $totalDistance / 1000;
    $totalAvespeed = $totalDistance / $totalRidingTime;
    $totalBatteryUsage = $totalBatteryUsage / $rows;
    $totalBatteryPerKm = $totalBatteryPerKm / $rows;
    $totalPowerUsage = $totalPowerUsage / $rows;
    $powerAverage100kms = $powerAverage100kms / $rows;
    $costAverage100kms = $costAverage100kms / $rows;


    echo '<tr>';
    echo '<td>TOTALS / AVERAGES</td>';
    echo '<td>' . round($totalRidingTime) . ' h<br>' . round($totalDistance) .' Km.</td>';
    echo '<td>-' . round($totalBatteryUsage) .'%<br>' . round($totalPowerUsage/1000, 1) .' kWh</td>';
    echo '<td>' . round($totalBatteryPerKm, 2) .' %<br>' . round($powerAverage100kms/1000, 1) .' kWh</td>';
    echo '<td>' . round($totalRidePrice, 2) .' €</td>';
    echo '</tr>';

    ?>
    </tbody></table>

  </div>

	<h3><a href="index.php">Homepage</a><a href="javascript: void(0)" id="reloadData">Reload data</a></h3>

	<script src="js/jquery.js"></script>
	<script src="js/custom.js?v=3"></script>
</body>
</html>