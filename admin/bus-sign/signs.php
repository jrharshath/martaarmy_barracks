<?php

include('busdata/busdata.php');

date_default_timezone_set('America/New_York');
$default_effective_date = "12-Dec-2015";

$sids = $_REQUEST['sids'];
$adopters = $_REQUEST['adopters'];

if(count($sids) != count($adopters)) {
	die("Count of stop ids is not same as count of adopters!");
}

$signsToMake = array();

for($i=0; $i<count($sids); $i++) {
	$sign = new stdClass();
	$sign->sid = $sids[$i];
	$sign->adopter = $adopters[$i];
	$signsToMake[] = $sign;
}

?>
<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8" />
	<link rel="stylesheet" type="text/css" href="army.css"/>
	<script type='text/javascript' src='jquery.js'></script>
    <script src='https://api.tiles.mapbox.com/mapbox-gl-js/v0.12.0/mapbox-gl.js'></script>
    <script type="text/javascript" src="busdata/busdata.js"></script>
    <script type="text/javascript" src="map.js"></script>

	<title>MARTA Army | TimelyTrip</title>
</head>
<body>

<?php

foreach($signsToMake as $sign) {
	pullDataForSign($sign);
	printPageForStop($sign);
}

function pullDataForSign(&$sign) {
	$sid = $sign->sid;
	
	$result = getJson("http://atlanta.onebusaway.org/api/api/where/schedule-for-stop/" . $sid . ".json?key=TEST&date=2015-12-21"); // weekday
	
	$sign->stopName = $result['data']['references']['stops'][0]['name'];
	
	$stopSchedules = array();
	$groupedSchedules = array();

	// get weekday schedules
	createSchedules($result, $stopSchedules, $groupedSchedules, 'wkday');

	// get saturday schedules
	$result = getJson("http://atlanta.onebusaway.org/api/api/where/schedule-for-stop/" . $sid . ".json?key=TEST&date=2015-12-19"); // saturday
	createSchedules($result, $stopSchedules, $groupedSchedules, 'sat');
	
	// get sunday schedules
	$result = getJson("http://atlanta.onebusaway.org/api/api/where/schedule-for-stop/" . $sid . ".json?key=TEST&date=2015-12-20"); // sunday
	createSchedules($result, $stopSchedules, $groupedSchedules, 'sun');

	// Above we marked certain headsigns as "AMBIGUOUS"
	// b/c the same text might be used for multiple directions of travel.
	// Merge 'AMBIGUOUS' schedules with another schedule of the same route (MARTA 12).

	$deleteCandidates = array();		
	foreach ($groupedSchedules as $ss) {
		if ($ss['direction2'] == 'AMBIGUOUS') {
			foreach ($groupedSchedules as &$ss1) {
				if ($ss1['name'] == $ss['name'] && ($ss1['direction2'] != 'AMBIGUOUS')) {
					array_push($deleteCandidates, $ss);
					
					$ss1['wkday'] = array_merge($ss1['wkday'], $ss['wkday']);
					$ss1['sat'] = array_merge($ss1['sat'], $ss['sat']);
					$ss1['sun'] = array_merge($ss1['sun'], $ss['sun']);

					sort($ss1['wkday']);
					sort($ss1['sat']);
					sort($ss1['sun']);
					
					break;
				}
			}				
		}
	}

	foreach ($deleteCandidates as $dc) {
		unset($groupedSchedules[$dc['finalDestination']]);
	}

	// // This is to center single schedules on the sheet.
	// if (count($stopSchedules) == 1) {
	// 	array_unshift($stopSchedules, null);
	// 	array_push($stopSchedules, null);
	// }
	// if (count($groupedSchedules) == 1) {
	// 	array_unshift($groupedSchedules, null);
	// 	array_push($groupedSchedules, null);
	// }

	$sign->stopSchedules = $stopSchedules;
	$sign->groupedSchedules = $groupedSchedules;
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

function createSchedules($result, &$stopSchedules, &$groupedSchedules, $day) {
	global $directionData;

	$rawSchedules = $result['data']['entry']['stopRouteSchedules'];
	$routes = $result['data']['references']['routes'];
	
	if (isset($rawSchedules)) {
		foreach($rawSchedules as $rawSch) {
			$routeId = $rawSch['routeId'];		
			
			foreach($rawSch['stopRouteDirectionSchedules'] as $rawSchDirn) {
			
				$rawHeadsign = $rawSchDirn['tripHeadsign'];
				$direction2Data = (array_key_exists($rawHeadsign, $directionData) ? $directionData[$rawHeadsign] : null);
				$direction2 = ''; // (array_key_exists($rawHeadsign, $directionData) ? $directionData[$rawHeadsign] : $rawHeadsign);
				$finalDestination = '';
				
				if ($direction2Data != null) {
						$directionSplit = explode(" via ", $direction2Data);
						$directionSplit = explode(" to ", $directionSplit[0]);
						$direction2 = $direction2Data;
						if (count($directionSplit) >= 2) {
							$finalDestination = $directionSplit[1];
						}
						else {
							$finalDestination = $directionSplit[0];							
						}
				}
				else {
					$direction2 = $finalDestination = $rawHeadsign;
				}
				$scheduleId = $routeId . "_" . $direction2;
				
				$stopSch = (array_key_exists($scheduleId, $stopSchedules) ? $stopSchedules[$scheduleId] : null);
				$groupedSch = (array_key_exists($finalDestination, $groupedSchedules) ? $groupedSchedules[$finalDestination] : null);
				if ($stopSch == null) {
					foreach($routes as $r) {
						if ($r['id'] == $routeId) {
							$stopSchedules[$scheduleId] = createRouteParams($r);				
							break;
						}
					}
				}
				if ($groupedSch == null) {
					foreach($routes as $r) {
						if ($r['id'] == $routeId) {
							$groupedSchedules[$finalDestination] = createRouteParams($r);				
							$groupedSchedules[$finalDestination]['direction2'] = $direction2;
							$groupedSchedules[$finalDestination]['finalDestination'] = $finalDestination;
							break;
						}
					}
				}

				foreach($routes as $r) {
					if ($r['id'] == $routeId) {
						$groupedSchedules[$finalDestination]['routes'][$r['shortName']] = $r['shortName'];
						if (count($groupedSchedules[$finalDestination]['routes']) > 1) {
							$groupedSchedules[$finalDestination]['direction2'] = " to " . $finalDestination;					
						}
					}
				}
				
				$stopSchedules[$scheduleId]['direction'] = $rawHeadsign; // todo stopRouteDirectionSchedules should only have one item in it		
				$stopSchedules[$scheduleId]['direction2'] = $direction2;

				$schedule = array();

				$arrTimes = $rawSchDirn['scheduleStopTimes']; 
				foreach($arrTimes as $at) {
					array_push($schedule, $at['departureTime']); // todo check for arrivalEnabled and departureEnabled
				}
				$stopSchedules[$scheduleId][$day] = $schedule;
				$groupedSchedules[$finalDestination][$day] = array_merge($groupedSchedules[$finalDestination][$day], $schedule);
				sort($groupedSchedules[$finalDestination][$day]);
			}
		}		
	}
}

function createRouteParams($r) {
	$route = array();

	$route['name'] = $r['shortName'];
	$route['agency'] = $r['agencyId'];		
	$route['direction'] = 'unknown';
	//$route['neighbourhoods'] = getNeighbourhoods($agency, $r['shortName'], $sid);
	$route['wkday'] = array();
	$route['sat'] = array();
	$route['sun'] = array();
	
	return $route;				
}

function printPageForStop($sign) {
	echo "<div class='page12'>";

	printPageHeader($sign);

	$stopid = $sign->sid;
	echo "<div class='map-container' id='map_$stopid'></div>";
	echo "<div class='buses'>";
	
	$groupedSchedules = $sign->groupedSchedules;
	foreach($groupedSchedules as $ss) {		
		printRouteInfo($ss);
	}

	echo "</div>"; // </.buses>
	
	printPageFooter($sign);
	echo "<div style='clear:both; page-break-after: always;'></div>";
}

function printPageHeader($sign) {
	$sid = explode('_', $sign->sid)[1];
	$stopName = $sign->stopName;
	echo <<<EOT
	<div class='header'>
		<img class='logo' src='img/timelytrip_white.png'/>
		<div class='header-content'>
			<h1>$stopName</h1>		
			<div class='info'>
				<p class='verify'>
					Check your bus departure times* from this stop.<br/>
					Verifica el horario* de su autobus en esta parada.
				</p>
				<p class='stopid'>Stop ID: $sid</p>
			</div>
		</div>
	</div>
EOT;
}

function printRouteInfo($routeInfo) {
	$name = "";
	if (array_key_exists('routes', $routeInfo)) {
		$name = implode('</h3><h3>', $routeInfo['routes']);
	}
	$agency = $routeInfo['agency'];
	$destArray = explode(" via ", $routeInfo['direction2']);
	$originDestination = str_replace(". " , ".&nbsp;", str_replace(" to ", "&nbsp;&#x27A4;&nbsp;", $destArray[0]));

	$waypoints_li = "";
	if (count($destArray) > 1) {
		$waypoints_li = "<li class='waypoints'><i>via</i> " . $destArray[1] . "</li>";	
	}


	echo <<<EOT
	<div class='bus'><div class='title $agency'>
		<h3>$name</h3>
		<ul class='places'>
			<li class='origin-destination'>$originDestination</li>
			$waypoints_li
		</ul>
	</div>
EOT;

	$shouldPrintWeekday = (count($routeInfo['wkday']) > 0);
	$shouldPrintSaturday = (count($routeInfo['sat']) > 0);
	$shouldPrintSunday = (count($routeInfo['sun']) > 0);

	echo "		<table class='schedule'>";

	if ($routeInfo != null) {
		echo "<thead><tr>";
		
		if ($shouldPrintWeekday) echo '<th>Weekdays<br/><span class="alt-lang">En semana</span></th>';
		if ($shouldPrintSaturday) echo '<th>Saturday<br/><span class="alt-lang">S&aacute;bado</span></th>';
		if ($shouldPrintSunday) echo '<th>Sunday<br/><span class="alt-lang">Domingo</span></th>';
		
		echo "</tr></thead><tbody><tr>";
		if ($shouldPrintWeekday) {
			echo "<td><ul>";
			printTimeTable($routeInfo['wkday']);
			echo "</ul></td>";
		}
		if ($shouldPrintSaturday) {
			echo "<td><ul>";
			printTimeTable($routeInfo['sat']);
			echo "</ul></td>";
		}	
		if ($shouldPrintSunday) {
			echo "<td><ul>";
			printTimeTable($routeInfo['sun']);
			echo "</ul></td>";
		}
		echo "</tr></tbody>";
	}
	else {
		echo "<thead></thead><tbody></tbody>";
	}
	echo "</table></div>";
}

function printTimeTable($tt) {
	$prevH = -1;
	$prevM = -1;
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

		$isNewHourRow = ($h != $prevH);
		if($isNewHourRow) {
			$prevH = $h;
			if($first) $first = false; 
			else echo "</li>";
			
			$thisAMPM = $am ? 1 : 2; 
			if ($thisAMPM != $prevAMPM) {
				if($am) echo "<li class='ampm-heading'>AM</li>";
				else echo "<li class='ampm-heading'>PM</li>";
			}
			
			if ($am) echo "<li>"; 
			else echo "<li class=\"pm\">"; 
						
			echo "<span>$h</span>"; 
			$prevAMPM = $thisAMPM;
		}
		
		if ($prevM != $m || $isNewHourRow) { // to avoid duplicate times (have to do from here not from timestamps)
			echo "<span>:$m</span>";
		}
		$prevM = $m;			
	}
	echo "</li>";
}

function printPageFooter($sign) {
	$sid_full =  $sign->sid;
	$sid_split = explode('_', $sid_full);
	$agency_loc = $sid_split[0];
	$sid_loc = $sid_split[1];
	$adopter = $sign->adopter;

	echo <<<EOT
	<div class='footer'>
		<div class='disclaimer'>
			*Trip times are approximate, may change without notice, and may vary with road conditions, events, and holidays. Data provided by MARTA and OneBusAway.
			<br /><span class='alt-lang'>*Los horarios son indicativos, pueden cambiar sin aviso previo y cambiar en funci&oacute;n de las condiciones de circulaci&oacute;n, eventos, y d&iacute;as festivos.</span> 
		</div>

		<p class='adopter'>This stop has been adopted by <span class='adopterName'>$adopter</span></p>
	
		<div class='QR'>
			<img src='qr.php?p=http://192.168.0.2/bussign/qr_fwd.php?s=$sid_full'/>
			<p><span class='big'>SCAN HERE &#x25BA;</span><br/>to get live arrival times<br />on your mobile device.</p>
		</div>
			
		<p class='adoptPitch'>
			<b>YOU CAN ADOPT A STOP TOO!</b><br/>MARTAArmy.org/<b>join</b>an<b>army</b>
		</p>
	</div>
EOT;
}

?>

<script type='text/javascript'>
$(function() {
	var cmnHeight = 0;
	var busWidths = []; 
	var totalWidth = 0;
	var containerWidth = 1100;
	var i = 0;
	var nSkinnyCols = 0;
	
	$('div.bus').each(function() {
		var $b = $(this);
		var $t = $b.find('div.title');
		var $s = $b.find('table.schedule');
		var w = $s.width();		
		$b.width(w); //$t.width(w);		
		busWidths[i] = $b.width();
		i++;

		if (w < 5) nSkinnyCols++;
		else totalWidth += w;
		
		var h = $s.height() + $s.position().top - $t.position().top;
		if (h > cmnHeight) cmnHeight = h;
	});
	
	var idealBusWidth = containerWidth;
	if (busWidths.length > 1) idealBusWidth = containerWidth / busWidths.length;

	var skinnyColWidth = 0;
	if (nSkinnyCols != 0) {
		if (totalWidth < idealBusWidth) skinnyColWidth = totalWidth / 2;
		else skinnyColWidth = (containerWidth - totalWidth) / nSkinnyCols / 1.4;
		
		$('div.bus').each(function() {
			var $b = $(this);
			//var $t = $b.find('div.title');
			//var $s = $b.find('table.schedule');
			var w = $b.width();
			if (w < 5) {
				w = skinnyColWidth;
				$b.width(w); //$t.width(w);		
				totalWidth += w;
			}
		});
	}
	if (totalWidth < containerWidth) {
		// if all bus widths are less than 1/n container width then set width to 1/n container width
		// else scale up.
		
		var allBusWidthsAreLessThanIdeal = true;
		for (var j = 0; j < busWidths.length; j++) {
			if (busWidths[j] > idealBusWidth) allBusWidthsAreLessThanIdeal = false;
		}
		
		var stretchRatio = containerWidth / totalWidth;
		$('div.bus').each(function() {
			var $b = $(this);
			var $t = $b.find('div.title');
			var $s = $b.find('table.schedule');
			var w = $b.width();

			if (!allBusWidthsAreLessThanIdeal || nSkinnyCols != 0) {
				$b.width(Math.floor(w * stretchRatio));// * 0.999);			
			}
			else {
				$b.width(Math.floor(idealBusWidth));// * 0.999);
			}
			//$s.width("100%");

		});	
	}
	
	$('div.bus').each(function() {
		var $b = $(this);
		//var $s = $b.find('table.schedule');
		$b.height(cmnHeight); 
	});

	$('.map-container').each(function(i,el) {
		var mapid = $(el).attr('id');
		var mapid_parts = mapid.split('_');
		var agency = mapid_parts[1];
		var stopid = mapid_parts[2];
		drawMapForStopId(mapid, agency, stopid);
	});
})
</script>
</body>
</html>
