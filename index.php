<?php
if(file_exists('config.php')){
	include 'config.php';
} else {
	die('Please, duplicate the file config.sample.php and rename it to config.php, and then edit the created file with your own information');
}
?>
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

	<h3>NIU N-GT</h3>

	<ul class="last-tracks">
		<li><span class="track-label">Battery level</span><span class="track-value"><data id="batteryLevel">-</data>%</span></li>
		<li><span class="track-label">Charge to <?php echo $desiredChargeLevel; ?>%</span><span class="track-value"><data id="startCharging">-</data> - 07:30</span></li>
		<li><span class="track-label">Total distance</span><span class="track-value"><data id="totalMileage">-</data> Km.</span></li>
	</ul>

	<h3>Last ride</h3>

	<ul class="last-tracks">
		<li><span class="track-label">Date</span><span class="track-value track-value--data"><data id="date">-</data></span></li>
		<li><span class="track-label">Start</span><span class="track-value"><data id="start">-</data></span></li>
		<li><span class="track-label">End</span><span class="track-value"><data id="end">-</data></span></li>
		<li><span class="track-label">Distance</span><span class="track-value"><data id="distance">-</data> Km.</span></li>
		<li><span class="track-label">Battery usage</span><span class="track-value">-<data id="batteryUsage">-</data>%</span></li>
		<li><span class="track-label">Battery/Km.</span><span class="track-value"><data id="batteryKm">-</data>%</span></li>
		<li><span class="track-label">Duration</span><span class="track-value"><data id="duration">-</data></span></li>
		<li><span class="track-label">Average Km/h</span><span class="track-value"><data id="averageSpeed">-</data> Km/h</span></li>
		<li><span class="track-label">Ride cost</span><span class="track-value"><data id="rideCost">-</data> €</span></li>
	</ul>

	<h3><a href="tracks.php">View more rides</a><a href="javascript: void(0)" id="reloadData">Reload data</a></h3>

	<script src="js/jquery.js"></script>
	<script src="js/custom.js?v=3"></script>
</body>
</html>