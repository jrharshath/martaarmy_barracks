<?php

function validateAdminSession($adminsid) {
	global $_DB;

	$stmt = $_DB->prepare("SELECT 1 FROM `adminsessions` WHERE `id` = ?");
	$stmt->bind_param('s', $adminsid);
	$stmt->execute();
	$results = $stmt->get_result();

	if($results->num_rows != 1) {
		return FALSE;
	}
	return TRUE;
}

function getTimelyTripSoldiers() {
	global $_DB;

	$stmt = $_DB->prepare(
		"SELECT u.id, u.name, u.email, u.phone, u.notes, p.data, u.joindate FROM users u ".
		"LEFT JOIN operation_participants p ".
		"ON u.id=p.userid AND p.opid=1  ".
		"ORDER BY u.joindate DESC, u.id DESC");
	$stmt->execute();
	$results = $stmt->get_result();

	$soldiers = array();

	while ($row = $results->fetch_array(MYSQLI_NUM)) {
		$userid = $row[0];
		$name = $row[1];
		$email = $row[2];
		$phone = $row[3];
		$notes = $row[4];
		$joindate = DateTime::createFromFormat('Y-m-j H:i:s', $row[6]);

		$soldier = array('id'=>$userid, 'name'=>$name, 'email'=>$email, 'phone'=>$phone, 'notes'=>$notes, 'joindate'=>$joindate);

		$stops = array();
		if(!is_null($row[5])) {
			$stops_json = json_decode($row[5]);

			foreach($stops_json as $sj) {
				$stopname = $sj->val;
				$id = $sj->id;
				$stopid = NULL; $agency = NULL;
				if(property_exists($sj, 'stopid')) {
					$stopid = $sj->stopid;
					$agency = $sj->agency;

				}
				$agency = (property_exists($sj, 'stopid')) ? $sj->agency : NULL;
				$given = (property_exists($sj, 'given')) ? TRUE : FALSE;
				$nameonsign = $given ? $sj->nameonsign : NULL;
				$abandoned = (property_exists($sj, 'abandoned')) ? TRUE : FALSE;

				$stop = array('name'=>$stopname, 'id'=>$id, 
					          'stopid'=>$stopid, 'agency'=>$agency, 
					          'given' => $given, 'nameonsign' => $nameonsign, 'abandoned' => $abandoned);
				array_push($stops, $stop);
			}
		}
		
		

		$classified_stops = _classifyStops($stops);
		
		$soldier['stops_notgiven'] = $classified_stops['notgiven'];
		$soldier['stops_notasks'] = $classified_stops['notasks'];
		$soldier['stops_pendingtasks'] = $classified_stops['pendingtasks'];
		$soldier['stops_overduetasks'] = $classified_stops['overduetasks'];
		$soldier['stops_uptodate'] = $classified_stops['uptodate'];

		array_push($soldiers, $soldier);
	}

	return $soldiers;
}

function _classifyStops($stops) {
	$notgiven = array();
	$notasks = array();
	$pendingtasks = array();
	$overduetasks = array();
	$uptodate = array();

	foreach($stops as $stop) {
		if($stop['given']===FALSE) {
			array_push($notgiven, $stop);
			continue;
		}
		array_push($notasks, $stop);
	}

	return array(
		'notgiven' => $notgiven,
		'notasks' => $notasks,
		'pendingtasks' => $pendingtasks,
		'overduetasks' => $overduetasks,
		'uptodate' => $uptodate
	);
}

function _getAdoptedStops($userid) {
	global $_DB;
	
	$stmt = $_DB->prepare(
		"SELECT id, data FROM operation_participants WHERE opid=1 AND userid=?");
	$stmt->bind_param('i', $userid);
	$stmt->execute();
	$results = $stmt->get_result();
	
	$row = $results->fetch_array(MYSQLI_NUM);

	return json_decode($row[1]);
}

function _findStopById($stops, $id) {
	foreach($stops as $stop) {
		if($stop->id == $id) {
			return $stop;
		}
	}

	return NULL;
}

function _updateStopsData($userid, $stops) {
	global $_DB;

	$newdata = json_encode($stops);

	$stmt = $_DB->prepare("UPDATE operation_participants SET data=? WHERE userid=?");
	$stmt->bind_param("si", $newdata, $userid);
	$result = $stmt->execute();
	
	return $result;
}

function updateAdoptedStop($userid, $id, $stopname, $stopid, $agency, $given, $nameonsign, $abandoned) {
	global $_DB;

	$stops = _getAdoptedStops($userid);
	$stoptoupdate = NULL;
	$stoptoupdate = _findStopById($stops, $id);

	if(is_null($stoptoupdate)) return 'nostop';

	$stoptoupdate->val = $stopname;

	if(!is_null($stopid)) { 
		$stoptoupdate->stopid = $stopid;
		$stoptoupdate->agency = $agency;
	}
	else if(property_exists($stoptoupdate, 'stopid')) { 
		unset($stoptoupdate->stopid);
		unset($stoptoupdate->agency);
	}
	
	if($given===TRUE) { 
		$stoptoupdate->given = TRUE;
		$stoptoupdate->nameonsign = $nameonsign;
	}
	else if(property_exists($stoptoupdate, 'given')) { 
		unset($stoptoupdate->given); 
		unset($stoptoupdate->nameonsign); 
	}

	if($abandoned===TRUE) { $stoptoupdate->abandoned = TRUE; }
	else if(property_exists($stoptoupdate, 'abandoned')) { unset($stoptoupdate->abandoned); }

	return _updateStopsData($userid, $stops);
}

function addAdoptedStop($userid, $stopname, $stopid, $agency) {
	global $_DB;
	
	$stmt = $_DB->prepare("SELECT 1 FROM users WHERE id=?");
	$stmt->bind_param('i', $userid);
	$stmt->execute();
	$results = $stmt->get_result();
	
	if($results->num_rows != 1) {
		return 'nouserid';
	}

	$stop = new stdClass();
	$stop->val = $stopname;
	$stop->id = get_random_string_len(8);
	if(!is_null($stopid) && !is_null($agency)) {
		$stop->stopid = $stopid;
		$stop->agency = $agency;
	}

	$stmt = $_DB->prepare(
		"SELECT data FROM operation_participants WHERE opid=1 AND userid=?");
	$stmt->bind_param('i', $userid);
	$stmt->execute();
	$results = $stmt->get_result();

	if($results->num_rows == 0) {
		$newdata = json_encode(array($stop));
		$stmt = $_DB->prepare(
			"INSERT INTO operation_participants (opid, userid, data) VALUES(1,?, ?)");
		$stmt->bind_param('is', $userid, $newdata);
		$result = $stmt->execute();
		
		return $result;
	} else {
		$row = $results->fetch_array(MYSQLI_NUM);

		$data = $row[0];
		$newdata = null;

		if(is_null($data)) {
			$newdata = json_encode(array($stop));
		} else {
			$data = json_decode($data);
			array_push($data, $stop);
			$newdata = json_encode($data);
		}

		$stmt = $_DB->prepare(
			"UPDATE operation_participants SET data=? WHERE opid=1 AND userid=?");
		$stmt->bind_param('si', $newdata, $userid);
		$result = $stmt->execute();
		
		return $result;
	}

}

?>