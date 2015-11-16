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

class Soldier {
	var $id, $name, $email, $phone, $notes, $joindate;
	var $stops, $stops_given, $stops_notgiven;

	function __construct($id, $name, $email, $phone, $notes, $joindate)
	{
		$this->id = $id;
		$this->name = $name;
		$this->email = $email;
		$this->phone = $phone;
		$this->notes = $notes;
		$this->joindate = $joindate;
		
		$this->stops = array();
		$this->stops_given = array();
		$this->stops_notgiven = array();
	}

	function addStop($stop) {
		$this->stops[] = $stop;
		if($stop->given) {
			$this->stops_given[] = $stop;
		} else {
			$this->stops_notgiven[] = $stop;
		}
	}
}

class Stop {
	var $id, $stopname, $stopid, $agency, $given, $nameonsign, $abandoned;

	function __construct($id, $stopname, $stopid, $agency, $given, $nameonsign, $abandoned) {
		$this->id = $id;
		$this->stopname = $stopname;
		$this->stopid = $stopid;
		$this->agency = $agency;
		$this->given = $given;
		$this->nameonsign = $nameonsign;
		$this->abandoned = $abandoned;
	}
}

function getTimelyTripSoldiers() {
	global $_DB;

	$stmt = $_DB->prepare(
		"SELECT u.id, u.name, u.email, u.phone, u.notes, u.joindate, ".
		"s.id, s.stopname, s.stopid, s.agency, s.given, s.nameonsign, s.abandoned ".
		"FROM users u LEFT JOIN adoptedstops s ON u.id = s.userid ".
		"ORDER BY u.joindate DESC");
	$stmt->execute();
	$results = $stmt->get_result();

	$soldiers = array();

	function findSoldier($soldiers, $userid) {
		$found = null;
		foreach($soldiers as $s) {
			if ($s->id == $userid) {
			    $found = $s;
			    break;
			}
		}
		return $found;
	}

	while ($row = $results->fetch_array(MYSQLI_NUM)) {
		$userid = $row[0];

		$soldier = findSoldier($soldiers, $userid);
		if(is_null($soldier)) {
			$soldier = new Soldier($userid, $row[1], $row[2], $row[3], $row[4], dateTimeFromDb($row[5]));
			$soldiers[] =  $soldier;
		}
		
		if(is_null($row[6])) { continue; }

		$stop = new Stop(
			$row[6], $row[7], $row[8], $row[9], booleanFromDb($row[10]), $row[11], booleanFromDb($row[12]));

		$soldier->addStop($stop);		
	}	
	
	return $soldiers;
}

function _findStopById($stops, $id) {
	foreach($stops as $stop) {
		if($stop->id == $id) {
			return $stop;
		}
	}

	return NULL;
}

function updateAdoptedStop($id, $stopname, $stopid, $agency, $given, $nameonsign, $abandoned) {
	global $_DB;

	$given = booleanToDb($given);
	$abandoned = booleanToDb($abandoned);
	
	$stmt = $_DB->prepare(
		"UPDATE adoptedstops SET stopname=?, stopid=?, agency=?, given=?, nameonsign=?, abandoned=? ".
		"WHERE id=?");
	$stmt->bind_param("sssisis", $stopname, $stopid, $agency, $given, $nameonsign, $abandoned, $id);
	$result = $stmt->execute();
}


?>