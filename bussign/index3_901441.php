<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8" />
	<link rel='stylesheet' type='text/css' href='style3.css'/>
	<script type='text/javascript' src='jquery-2.1.3.min.js'></script>
	<script type='text/javascript' src='underscore.min.js'></script>
</head>
<body>

<?php

date_default_timezone_set('America/New_York');

$stopIds = array(
	array('MARTA', $_REQUEST['sid'])
	//array('MARTA', '901441')
);

$neighbourhoodsData = array(
	'CCT_481' => array(
	    array('522', 'Town Center Park/Ride')
	),

	'CCT_102' => array(
	    array('522', 'Acworth Park/Ride')
	),

	'MARTA_16' => array(
	    array('905146', 'Johnson Rd, Executive Park'),
	    array('904480', 'Old Fourth Ward, Civic Center, Downtown'),
	    array('905024', 'Old Fourth Ward, Civic Center, Downtown'),
	    array('904273', 'Virginia Highland, Johnson Rd, Executive Park'),
	    array('901037', 'Civic Center, Downtown'),
	    array('900947', 'Old Fourth Ward, Virginia Highland, Executive Park')
	),

	'MARTA_1' => array(
	    array('901441', 'Marietta St, Centennial Park, Five Points'),
	    array('902051', 'Marietta Blvd, Coronet Way'),
	    array('210554', 'Five Points Station')
	),

	'MARTA_12' => array(
	    array('901441', '10th Street, Georgia Tech, Midtown Station'),
	    array('902051', 'Howell Mill, Northside Pkwy, Cumberland'),
	    array('904287', 'Midtown Station'),
	    array('904285', 'Howell Mill, Northside Pkwy, Cumberland'),
	    array('904668', 'Midtown Station'),
	    array('904327', 'Howell Mill, Northside Pkwy, Cumberland')
	),

	'99' => array(
	    array('901229', 'Midtown Station'),
	    array('901230', 'Boulevard, Sweet Auburn, Georgia State'),
	    array('903404', 'Sweet Auburn, Georgia State'),
	    array('903226', 'Divide in two: (1) Piedmont Park, Midtown Station; (2) North Avenue Station'),
	    array('900974', 'Georgia State Station'),
	    array('103060', 'Divide in two: (1) Piedmont Park, Midtown Station; (2) North Avenue Station')
	),

	'MARTA_110' => array(
	    array('904552', 'Fox Theatre, Peachtree Center, Five Points'),
	    array('904551', 'Arts Center, Peachtree Rd, Buckhead'),
	    array('902393', 'Fox Theatre, Peachtree Center, Five Points'),
	    array('900021', 'Arts Center, Peachtree Rd, Buckhead')
	),

	'MARTA_27' => array(
	    array('904551', 'Botanical Gardens, Ansley Mall, Cheshire Bridge, Lindbergh Center'),
	    array('901811', 'Cheshire Bridge, Lindbergh Center'),
	    array('902010', 'Ansley Mall, Midtown'),
	    array('900611', 'Cheshire Bridge, Ansley Mall, Midtown'),
	    array('900612', 'Lindbergh Center'),
	    array('211955', 'Botanical Gardens, Ansley Mall, Cheshire Bridge, Lindbergh Center'),
	    array('211956', 'Midtown Station')
	),

	'MARTA_6' => array(
	    array('902218', 'Poncey Highland, Little Five Points, Inman Park'),
	    array('901796', 'Lindbergh Center'),
	    array('900611', 'Emory, Little Five Points, Inman Park'),
	    array('900612', 'Lindbergh Center'),
	    array('900052', 'Inman Park Station'),
	    array('900055', 'Poncey Highland, Emory, Lindbergh Center'),
	    array('900056', 'Emory, Lindbergh Center'),
	    array('900050', 'Inman Park Station')
	),

	'MARTA_2' => array(
	    array('211524', 'Ponce City Market, North Ave Station (via North Ave)'),
	    array('904530', 'Fernbank, Decatur'),
	    array('904527', 'North Ave Station (via North Ave)'),
	    array('904409', 'Poncey Highland, Fernbank, Decatur')
	),

	'MARTA_102' => array(
	    array('211524', 'Ponce City Market, North Ave Station (via Ponce)'),
	    array('904530', 'Little Five Points, Edgewood, Candler Park'),
	    array('904527', 'North Ave Station (via Ponce)'),
	    array('904409', 'Poncey Highland, Little Five Points, Candler Park'),
	    array('900052', 'Candler Park Station (via Edgewood District)'),
	    array('900055', 'Poncey Highland, Ponce City Market, North Ave Station'),
	    array('900056', 'Ponce City Market, North Ave Station'),
	    array('900050', 'Candler Park Station (via Edgewood District)')
	),

	'MARTA_30' => array(
	    array('900611', 'LaVista, Toco Hills, North Lake'),
	    array('900612', 'Lindbergh Center')
	),

	'CCT_477' => array(
	    array('490', 'Powder Springs, Hiram')
	),

	'MARTA_51' => array(
	    array('901371', 'Five Points Station (via Mitchell St)'),
	    array('904422', 'J. Boone Blvd')
	),

	'MARTA_3' => array(
	    array('900052', 'Candler Park Station (via DeKalb Ave)'),
	    array('901134', 'Candler Park Station (via Mc Lendon)'),
	    array('900055', 'Sweet Auburn (via Highland Ave), Downtown, Castleberry Hill, MLK Jr Dr, HE Holmes '),
	    array('901049', 'Sweet Auburn (via Irwin St), Downtown, Castleberry Hill, MLK Jr Dr, HE Holmes '),
	    array('900056', 'Sweet Auburn (via Highland Ave), Downtown, Castleberry Hill, MLK Jr Dr, HE Holmes '),
	    array('900050', 'Candler Park Station (via DeKalb Ave)'),
	    array('101126', 'Five Points (via Forsyth St), Sweet Auburn, Little Five Points, Candler Park'),
	    array('101010', 'MLK Jr Dr, Mozley Park, HE Holmes'),
	    array('101030', 'MLK Jr Dr, Mozley Park, HE Holmes'),
	    array('101031', 'Five Points (via Forsyth St), Sweet Auburn, Little Five Points, Candler Park'),
	    array('101014', 'MLK Jr Dr, Mozley Park, HE Holmes'),
	    array('211668', 'Five Points (via Forsyth St), Sweet Auburn, Little Five Points, Candler Park'),
	    array('101166', 'MLK Jr Dr, Mozley Park, HE Holmes')
	),

	'MARTA_13' => array(
	    array('101126', 'Five Points (via Peachtree St)'),
	    array('101010', 'Fair St, Mozley Park, West Lake'),
	    array('101030', 'Fair St, Mozley Park, West Lake'),
	    array('902902', 'Five Points (via Peachtree St)')
	),

	'MARTA_36' => array(
	    array('901229', 'Midtown Station'),
	    array('901230', 'Virginia Highland, Emory, North Decatur, Avondale'),
	    array('904480', 'Piedmont Park, Midtown'),
	    array('904772', 'Emory, North Decatur, Avondale'),
	    array('902990', 'Piedmont Park, Midtown'),
	    array('902218', 'Virginia Highland, Piedmont Park, Midtown'),
	    array('901796', 'North Decatur, Avondale')
	),

	'GRN' => array(
	    array('10thhemp', 'GTRI Conference Center')
	)

);

$effective_dates = array(
	'MARTA_16' => '3/21/2015',
	'MARTA_1' => '3/21/2015',
	'MARTA_12' => '12/13/2014',
	'99' => '12/14/2013',
	'MARTA_110' => '5/17/2014',
	'MARTA_27' => '12/14/2013',
	'MARTA_6' => '8/23/2014',
	'MARTA_2' => '5/17/2014',
	'MARTA_102' => '5/17/2014',
	'MARTA_30' => '5/17/2014',
	'MARTA_51' => '5/17/2014',
	'MARTA_3' => '5/17/2014',
	'MARTA_13' => '4/20/2013',
	'MARTA_36' => '8/23/2014'
);

foreach($stopIds as $sid) {
	printPageForStop($sid);
}

function printPageForStop($stop) {
	$agency = $stop[0];
	$sid = $stop[1];

	$result = getJson("http://atlanta.onebusaway.org/api/api/where/schedule-for-stop/" . $agency . "_" . $sid . ".json?key=TEST&date=2015-04-01"); // weekday

	$stopName = $result['data']['references']['stops'][0]['name'];

	$stopSchedules = array();
	
	$routes = $result['data']['references']['routes'];
	foreach($routes as $r) {
		$route = array();

		$route['name'] = $r['shortName'];
		$route['direction'] = 'unknown';
		$route['neighbourhoods'] = getNeighbourhoods($agency, $r['shortName'], $sid);
		$route['wkday'] = array();
		$route['sat'] = array();
		$route['sun'] = array();
		$route['effective'] = getEffectiveDate($agency, $r['shortName']);

		$routeId = $r['id'];
		$stopSchedules[$routeId] = $route;
	}

	

	// get weekday schedules
	createSchedules($result, $stopSchedules, 'wkday');

	// get saturday schedules
	$result = getJson("http://atlanta.onebusaway.org/api/api/where/schedule-for-stop/" . $agency . "_" . $sid . ".json?key=TEST&date=2015-04-04"); // saturday
	createSchedules($result, $stopSchedules, 'sat');
	
	// get sunday schedules
	$result = getJson("http://atlanta.onebusaway.org/api/api/where/schedule-for-stop/" . $agency . "_" . $sid . ".json?key=TEST&date=2015-04-05"); // sunday
	createSchedules($result, $stopSchedules, 'sun');

	// var_dump($stopSchedules);

	// print
	echo "<div class='page'>";
	printPageHeader($stopName);

	echo "<div class='buses'>";
	foreach($stopSchedules as $ss) {
		printRouteInfo($ss);
	}
	echo "</div>";
	printPageFooter($sid);
	echo "</div>";
	echo "<div style='clear:both; page-break-after: always;'></div>";
}

function getNeighbourhoods($agency, $route, $sid) {
	global $neighbourhoodsData;
	
	$routeNbhds = $neighbourhoodsData[$agency.'_'.$route];

	for($i = 0; $i < count($routeNbhds); $i++) {
    	if($routeNbhds[$i][0] == $sid) {
    		$nbds = $routeNbhds[$i][1];
    		return explode(',', $nbds);
    	}
	}

	return null;
}

function getEffectiveDate($agency, $route) {
	global $effective_dates;	
	return $effective_dates[$agency.'_'.$route];
}

function getJson($url) {
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($ch, CURLOPT_URL,$url);
	$result=curl_exec($ch);
	curl_close($ch);
	return json_decode($result, true);
}

function createSchedules($result, &$stopSchedules, $day) {
	$rawSchedules = $result['data']['entry']['stopRouteSchedules'];

	foreach($rawSchedules as $rawSch) {
		$routeId = $rawSch['routeId'];

		$stopSchedules[$routeId]['direction'] = $rawSch['stopRouteDirectionSchedules'][0]['tripHeadsign']; // todo stopRouteDirectionSchedules should only have one item in it

		$schedule = array();

		$arrTimes = $rawSch['stopRouteDirectionSchedules'][0]['scheduleStopTimes']; 
		foreach($arrTimes as $at) {
			array_push($schedule, $at['departureTime']); // todo check for arrivalEnabled and departureEnabled
		}
		$stopSchedules[$routeId][$day] = $schedule;
	}
}

function printPageHeader($stopName) {
	echo <<<EOT
	<div class='header'>
		<img class='arrow' height='75px' src='tt-logo.png'/>
		<h1>$stopName</h1>
		<h2>Buses departing from this stop.</h2>
	</div>
EOT;
}

function printRouteInfo($routeInfo) {
	$name = $routeInfo['name'];

	// todo add colour this up in a separate version 

	echo <<<EOT
	<div class='bus'>
		<div class='title'>
			<h3>$name</h3>
			<ul class='places'>
EOT;
	
	foreach($routeInfo['neighbourhoods'] as $nbd) {
		echo "<li>" . trim($nbd) . "</li>";
	}

	echo "</ul>";
	echo '<div class="effective-date">Effective ' . $routeInfo['effective'] . '</div>';
	
	echo <<<EOT
		</div>

		<table class='schedule'>
			<thead><tr><th>Weekdays</th><th>Saturday</th><th>Sunday</th></tr></thead>
			<tbody>
			<tr>
				<td><ul>
EOT;
	
	printTimeTable($routeInfo['wkday']);
	echo "</ul></td><td><ul>";
	printTimeTable($routeInfo['sat']);
	echo "</ul></td><td><ul>";
	printTimeTable($routeInfo['sun']);
	echo "</ul></td></tr></tbody></table></div>";
}

function printTimeTable($tt) {
	$prevH = -1;
	$prevAMPM = 0;
	$first = true;

	foreach($tt as $t) {
		$t /= 1000;
		$h = intval(date('G', $t));
		$m = date('i', $t);
		$am = true;
		if($h >= 12) { $am = false; }
		if($h > 12) { $h -= 12; }
		if($h == 0) {
			$h = 12;
			$am = true;
		}
		$hs = ($h < 10) ? "&nbsp;&nbsp;" . $h : $h;

		if($h != $prevH) {
			$prevH = $h;
			if($first) { $first = false; echo "<li class=\"hour\">"; }
			else { echo "<span class='clearer'></li><li class=\"hour\">"; }

			$thisAMPM = $am ? 1 : 2; 
			if ($thisAMPM != $prevAMPM) {
				if($am) { echo "<span>AM&nbsp;$hs:$m</span>"; }
				else { echo "<span class='pm'>PM&nbsp;$hs:$m</span>"; }			
			}
			else {
				if($am) { echo "<span>$hs:$m</span>"; }
				else { echo "<span class='pm'>$hs:$m</span>"; }
			}
			$prevAMPM = $thisAMPM;
			
		} else {
			if($am) { echo "<span>:$m</span>"; }
			else { echo "<span class='pm'>:$m</span>"; }
		}
	}
	echo "<span class='clearer'></span></li>";
}

function printPageFooter($stopId) {
	echo <<<EOT
	<div class='disclaimer'>Data provided by MARTA and OneBusAway. Trip times are approximate, may change without notice, and may vary with road conditions and other events and holidays.
	</div>
	<div class='footer'>
		<div class='ack'>
			<p style='margin-top: 0;'>Thanks to these organizations and individuals for making this project possible.
			<br/>(TimelyTrip is not affiliated with them.)</p>
			<img src='logos/oba.png' class='logo'/>
			<img src='logos/marta.jpg' class='logo'/> 
			<img src='logos/ioby.jpg' class='logo'/> 
			<img src='logos/kirsch.png' class='logo'/> 
			<img src='logos/ypt.png' class='logo'/> 
			<img src='logos/ilgusto.jpg' class='logo'/> 
		</div>
		<div class='QR'>
			<img style='float:right; margin-left:16px;' height='120px' src='qr.php?p=http://atlanta.onebusaway.org/where/standard/stop.action?id=MARTA_$stopId'/>
			
			<p style='float:right;'><span class='big'>&#x25BA;</span></p>
			<p style='float:right;'><span class='big'>SCAN HERE</span><br />to get live bus predictions<br />on your mobile device.
			<br/><br/><span class='small'>Live predictions provided<br/>by OneBusAway.</span></p> <!-- TODO mention OBA close to this code -->
		</div>
		<!-- todo add a disclaimer from MARTA on this -->
	</div>
EOT;
}

?>

<script type='text/javascript'>
	$('.bus table.schedule td ul li').each(function(el) { 
		var $e = $(this); 
		var len = $e.find('span').length;
		
		$e.width(55 + 25*(len-2)); 
	});
	var cmnHeight = 0;
	var totalWidth = 0;
	var containerWidth = 0;
	$('div.bus').each(function() {
		var $b = $(this);
		var $t = $b.find('div.title');
		var $s = $b.find('table.schedule');
		var w = $s.width();
		$b.width(w); $t.width(w);
		totalWidth += w;
		var h = $s.height();
		if (h > cmnHeight) cmnHeight = h;
	});
	$('div.bus').each(function() {
		var $b = $(this);
		var $s = $b.find('table.schedule');
		$s.height(cmnHeight); 
	});

</script>

</body>
</html>
