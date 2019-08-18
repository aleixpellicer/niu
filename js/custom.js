$( document ).ready(function() {

	function loadData(){
		$('#batteryLevel').html('-');
		$('#startCharging').html('-');
		$('#totalMileage').html('-');
		$('#date').html('-');
		$('#start').html('-');
		$('#end').html('-');
		$('#distance').html('-');
		$('#batteryUsage').html('-');
		$('#batteryKm').html('-');
		$('#duration').html('-');
		$('#averageSpeed').html('-');
		$('#rideCost').html('-');

		$.ajax({url: "api.php", success: function(result){
			$('#batteryLevel').html(result.batteryInfo);
			$('#startCharging').html(result.startCharging);
			$('#totalMileage').html(result.totalMileage);
			$('#date').html(result.date);
			$('#start').html(result.start);
			$('#end').html(result.end);
			$('#distance').html(result.distance);
			$('#batteryUsage').html(result.batteryUsage);
			$('#batteryKm').html(result.batteryPerKm);
			$('#duration').html(result.duration);
			$('#averageSpeed').html(result.avSpeed);
			$('#rideCost').html(result.ridePrice);
		}});
	}

	loadData();

	$('#reloadData').on('click', function(){
		loadData();
	});

});