<?php

include('busdata.php');

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
	<link rel="stylesheet" type="text/css" href="army_34.css"/>
	<script type='text/javascript' src='jquery-2.1.3.min.js'></script>
	<script type='text/javascript' src='underscore.min.js'></script>
	<title>MARTA Army | TimelyTrip</title>
</head>
<body>

<?php

foreach($signsToMake as $sign) {
	pullDataForSign($sign);
	printPageForStop($sign);
}

function pullDataForSign(&$sign) {

}

function printPageForStop($stop) {
	$agency = $stop[0];
	$sid = $stop[1];
	
	$stopId1 = $stop[0];

	$result = getJson("http://atlanta.onebusaway.org/api/api/where/schedule-for-stop/" . $stopId1 . ".json?key=TEST&date=2015-09-08"); // weekday

	$stopName = $result['data']['references']['stops'][0]['name'];
	if (isset($_REQUEST['stopNameOverride']) && $_REQUEST['stopNameOverride'] != "") {
		$stopName = $_REQUEST['stopNameOverride'];
	}

	$routes = $result['data']['references']['routes'];

	$stopSchedules = array();
	$groupedSchedules = array();


	// FIRST bus stop
	// get weekday schedules
	createSchedules($result, $stopSchedules, 'wkday');

	// get saturday schedules
	$result = getJson("http://atlanta.onebusaway.org/api/api/where/schedule-for-stop/" . $stopId1 . ".json?key=TEST&date=2015-04-04"); // saturday
	createSchedules($result, $stopSchedules, 'sat');
	
	// get sunday schedules
	$result = getJson("http://atlanta.onebusaway.org/api/api/where/schedule-for-stop/" . $stopId1 . ".json?key=TEST&date=2015-04-05"); // sunday
	createSchedules($result, $stopSchedules, 'sun');

	// SECOND BUS STOP if available
	$stopId2 = $stop[1];
	if ($stopId2 != "") {
		// get weekday schedules
		$result = getJson("http://atlanta.onebusaway.org/api/api/where/schedule-for-stop/" . $stopId2 . ".json?key=TEST&date=2015-09-08"); // weekday
		createSchedules($result, $stopSchedules, 'wkday');

		// get saturday schedules
		$result = getJson("http://atlanta.onebusaway.org/api/api/where/schedule-for-stop/" . $stopId2 . ".json?key=TEST&date=2015-04-04"); // saturday
		createSchedules($result, $stopSchedules, 'sat');
		
		// get sunday schedules
		$result = getJson("http://atlanta.onebusaway.org/api/api/where/schedule-for-stop/" . $stopId2 . ".json?key=TEST&date=2015-04-05"); // sunday
		createSchedules($result, $stopSchedules, 'sun');
	}

	$groupedSchedules = createMergedSchedules($stopSchedules);
	
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

	// print
	echo "<div class='page'>";
	echo "<div id='fold'></div>";
	printPageHeader($stopName);

	//echo "<div id='pageBody'>";
	echo "<div id='mapFold'>";
	echo "<img src='img/fakemap.png' style='width:100%; height:75%;' />";
	echo "</div>";
	
	echo "<div id='scheduleFold'>";
	echo "<div class='buses'>";
	//echo "<ul>";
	foreach($stopSchedules as $ss) {
		
		//echo "<li>" . $ss['direction2'] . "</li>";
		
		//printRouteInfo($ss);
	}
	foreach($groupedSchedules as $ss) {
		
		//echo "<li>" . $ss['direction2'] . "</li>";
		
		printRouteInfo($ss);
	}
	

	//echo "</ul>";
	echo "</div>";
	echo <<<EOT
	<div class='disclaimer'>
		*Trip times are approximate, may change without notice, and may vary with road conditions, events, and holidays. Data provided by MARTA and OneBusAway.
		<br /><span class='alt-lang'>*Los horarios son indicativos, pueden cambiar sin aviso previo y cambiar en funci&oacute;n de las condiciones de circulaci&oacute;n, eventos, y d&iacute;as festivos.</span> 
	</div>
EOT;
		
	echo "</div>";
	//echo "</div><!-- pageBody -->";
	
	printPageFooter($sid);
	echo "</div>";
	echo "<div style='clear:both; page-break-after: always;'><" . "/div>";
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

function createSchedules($result, &$stopSchedules, $day) {
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
				if ($stopSch == null) {
					foreach($routes as $r) {
						if ($r['id'] == $routeId) {
							$stopSchedules[$scheduleId] = createRouteParams($r);				
							break;
						}
					}
				}				
				$stopSchedules[$scheduleId]['direction'] = $rawHeadsign; // todo stopRouteDirectionSchedules should only have one item in it		
				$stopSchedules[$scheduleId]['direction2'] = $direction2;
				$stopSchedules[$scheduleId]['finalDestination'] = $finalDestination;

				$schedule = array();

				$arrTimes = $rawSchDirn['scheduleStopTimes']; 
				foreach($arrTimes as $at) {
					array_push($schedule, $at['departureTime']); // todo check for arrivalEnabled and departureEnabled
				}
				$stopSchedules[$scheduleId][$day] = $schedule;
			}
		}		
	}
}

function printPageHeader($stopName) {
	global $default_effective_date;
	$sid_loc = $_REQUEST['sid'];
	
	echo <<<EOT
	<div class='header'>
		<img class='logo' src='ttrip.png' />
		<h1>$stopName</h1>		
		<div class='float-right'>
			<h2 style='text-align:left;'>$sid_loc<br />$default_effective_date
			<!--<br/><!--Expiration date goes here if needed.-->
			</h2>
		</div>
		<div class='float-right' style='border-left: 1px solid #D0D0D0; padding-left: 15px;'>
			<h2>Stop#:
			<br />Valid/<span class='alt-lang'>V&aacute;lido</span>:
			<!--<br />&nbsp;<!--Expiration label goes here if needed.--><!--Exp.:-->
			</h2>
			<!--br />Exp. &#x25BA;</h2-->
		</div>
		<!--div class='float-right'>
			<h2>Check your bus departure times* from this stop.
			<br/><span class="alt-lang">Verifica el horario* de su autobus en esta parada.</span>
			</h2>
		</div-->
	</div>
EOT;
}

function printRouteInfo($routeInfo) {
	$name = "";
	$agency = "MARTA";

	echo "		<div class='bus'><div class='title $agency'>";	
	if ($routeInfo != null) {
		$name = $routeInfo['name'];		
		$agency = $routeInfo['agency'];
		
		if (array_key_exists('routes', $routeInfo)) {
			$name = implode('/', $routeInfo['routes']);
		}
		echo "<h3>$name</h3>";
		echo "<ul class='places'>";

		$destArray = explode(" via ", $routeInfo['direction2']);
		echo "<li class='origin-destination'>" . str_replace(". " , ".&nbsp;", str_replace(" to ", "&nbsp;&#x27A4;&nbsp;", $destArray[0])) . "</li>";
		//echo "<li><b>" . $destArray[0] . "</b></li>";
		if (count($destArray) > 1) echo "<li class='waypoints'><i>via</i> " . $destArray[1] . "</li>";		
		echo "</ul>";
	}

	echo "		</div>";

	$shouldPrintWeekday = (count($routeInfo['wkday']) > 0);
	$shouldPrintSaturday = (count($routeInfo['sat']) > 0);
	$shouldPrintSunday = (count($routeInfo['sun']) > 0);

	echo "		<table class='schedule'>";

	if ($routeInfo != null) {
		echo "<thead><tr>";
		
		if ($shouldPrintWeekday) echo '<th>M-F <span class="alt-lang">L-V</span></th>';
		if ($shouldPrintSaturday) echo '<th>Sat <span class="alt-lang">Sab</span></th>';
		if ($shouldPrintSunday) echo '<th>Sun <span class="alt-lang">Dom</span></th>';
		
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
				if($am) echo "<li class='ampm-heading'>A.M.</li>";
				else echo "<li class='ampm-heading'>P.M.</li>";
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

function printPageFooter($stopId) {
	$sid_loc = $_REQUEST['sid'];
	$agency_loc = $_REQUEST['agency'];
	$sid_full = $agency_loc . "_" . $sid_loc;

	echo <<<EOT
		<div class='footer'>
			<div class='ack1'>This stop has been adopted by</div>
			<div class='ack'>
EOT;
	if (isset($_REQUEST['adopter']) && $_REQUEST['adopter'] != "") {					
		$adopter = $_REQUEST['adopter'];

		echo "<div class='logo soldierName' style='max-width:600px;'>";
		echo "<p class='adopter'>$adopter</p>";
		if (isset($_REQUEST['rank']) && $_REQUEST['rank'] != "") {
			$rank = $_REQUEST['rank'];
			echo "<p class='rank'>$rank</p>";
		}
		echo "</div>";
	}
	if (isset($_REQUEST['weblogo']) && $_REQUEST['weblogo'] != "") {
		$logoUrl = $_REQUEST['weblogo'];
		echo "<img class='logo' src='$logoUrl' style='max-width:600px;' />";		
	}
	echo <<<EOT
			</div>
			<div class='QR'>
				<img src='qr.php?p=http://192.168.0.2/bussign/qr_fwd.php?s=$sid_full'/>
				
				<p style="margin:0.3em"><span class='big'>&#x25BA;</span></p>
				<p style="margin:0.3em"><span class='big'>SCAN HERE</span>
					<br />to get live arrival times<br />on your mobile device.
				<!--<br/>($sid_loc)-->

				<div class='adoptPitch' style='color:#000; border: 2px dashed #666;'>
						<p><b>YOU CAN ADOPT A STOP TOO!</b>
						<br />MARTAArmy.org/<b>join</b>an<b>army</b></p>
				</div>
				
				</p>
			</div>
			<!-- todo add a disclaimer from MARTA on this -->
		</div></div><div style='clear:both; page-break-after: always;'></div>		
		</div><div style='clear:both; page-break-after: always;'></div>
EOT;
}

?>

<script type='text/javascript'>
$(document).ready( function() {
	adjustBoxes();
});
function adjustBoxes() {
	var busElemsAndWidths = []; 
	var containerWidth = 1100 - 8;
	var containerHeight = 1700;
	var i = 0;
	var nLineReturns = 0;
	
	// Make widths the width of the inner table to start with.
	//$('.title').css("display", "none");	

	$('div.bus').each(function() {
			var $b = $(this);
					var $t = $b.find('.title');
					var $h3 = $t.find('h3');
					var $ul = $t.find('ul');
					var $s = $b.find('table');
					$ul.width($s.width() - $h3.outerWidth() - 8);
					//$ul.width($s.width());
	});



	$('div.bus').each(function() {
		var $b = $(this);
		busElemsAndWidths[i] = {element: $b, left: $b.offset().left, row: nLineReturns};
		if (i > 0 && busElemsAndWidths[i-1].left > busElemsAndWidths[i].left) {
			nLineReturns++;
			busElemsAndWidths[i].row = nLineReturns;
		}
		i++;
	});


	
	// Switch to 50% split if there is only one row.
	if (nLineReturns == 0) {		
		$("#mapFold").height("43%");
		$("#scheduleFold").offset({left: 0, top: containerHeight / 2});
		$("body").css("font-size", "14.5pt");
		$(".bus table.schedule td ul").css("line-height", "1.25em");
		$(".footer").css("font-size", "70%");
		$(".disclaimer").css("font-size", "70%");
	}
	// Drop the map if there are three lines.
	else if (nLineReturns == 3) {		
		$("#mapFold").css("display", "none");
		$("#scheduleFold").offset({left: 0, top: containerHeight / 2});
		$("body").css("font-size", "14.5pt");
		$(".bus table.schedule td ul").css("line-height", "1.25em");
		$(".footer").css("font-size", "70%");
		$(".disclaimer").css("font-size", "70%");
	}
	
	//busElemsAndWidths.sort(function(a, b) {return a.width - b.width;});


	// Stretch to page width row by row
	for (var nrow = 0; nrow <= nLineReturns; nrow++) {
		var totalWidth = 0;
		var nElementsInRow = 0;
		for (var k = 0; k < busElemsAndWidths.length; k++) {
			if (busElemsAndWidths[k].row == nrow) {
				totalWidth += busElemsAndWidths[k].element.width();
				nElementsInRow++;
			}
		}

		var idealBusWidth = containerWidth;
		if (nElementsInRow > 1) idealBusWidth = containerWidth / nElementsInRow;		
		
		if (totalWidth < containerWidth) {
			// if all bus widths are less than 1/n container width then set width to 1/n container width
			// else scale up.
			
			var allBusWidthsAreLessThanIdeal = true;
			for (var k = 0; k < busElemsAndWidths.length; k++) {
				if (busElemsAndWidths[k].row == nrow) {
					if (busElemsAndWidths[k].element.width() > idealBusWidth) allBusWidthsAreLessThanIdeal = false;
				}
			}
			
			var stretchRatio = containerWidth / totalWidth;
			for (var k = 0; k < busElemsAndWidths.length; k++) {
				if (busElemsAndWidths[k].row == nrow) {
					var $b = busElemsAndWidths[k].element;
					var w = $b.width();
					
					if (nElementsInRow == 1) {
						if (!allBusWidthsAreLessThanIdeal) {
							$b.width(Math.floor(w * stretchRatio));// * 0.999);			
						}
						else {
							$b.width(Math.floor(idealBusWidth));// * 0.999);
						}
					}
					else if (nElementsInRow == 2) {
						if (!allBusWidthsAreLessThanIdeal) {
							$b.width(Math.floor(w * stretchRatio * 0.993));			
						}
						else {
							$b.width(Math.floor(idealBusWidth * 0.993));
						}
					}
					var $t = $b.find('.title');
					var $h3 = $t.find('h3');
					var $ul = $t.find('ul');
					var $s = $b.find('table');
					$ul.width($t.width() - $h3.outerWidth() - 40);
				}
			}			
		}	
	}
	
	// Make heights equal, row by row.
	for (var nrow = 0; nrow <= nLineReturns; nrow++) {
		var cmnHeight = 0;
		for (var k = 0; k < busElemsAndWidths.length; k++) {
			if (busElemsAndWidths[k].row == nrow) {
				var h = busElemsAndWidths[k].element.height();
				if (h > cmnHeight) cmnHeight = h;					
			}
		}
		for (var k = 0; k < busElemsAndWidths.length; k++) {
			if (busElemsAndWidths[k].row == nrow) {
				busElemsAndWidths[k].element.height(cmnHeight);
			}
		}			
	}

	$('.places').css("display", "inline-block");
	
}
</script>


</body>
</html>
