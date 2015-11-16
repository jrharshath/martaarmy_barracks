<?php

include('lib/db.php');
init_db();

$stmt = $_DB->prepare("SELECT userid, data FROM operation_participants");
$stmt->execute();
$results = $stmt->get_result();

while($row = $results->fetch_array(MYSQLI_NUM)) {
	$userid = $row[0];
	$data = json_decode($row[1]);

	foreach($data as $sj) {
		$stopname = $sj->val;
		$id = $sj->id;
		$stopid = NULL; $agency = NULL;
		if(property_exists($sj, 'stopid')) {
			$stopid = $sj->stopid;
			$agency = $sj->agency;

			if(empty($stopname)) { $stopname = $agency.'_'.$stopid; }
		}
		
		$given = (property_exists($sj, 'given')) ? 1 : 0;
		$nameonsign = $given ? $sj->nameonsign : NULL;

		$adoptedtime = '2015-09-27 18:0:0'; // some arbit
		$abandoned = 0;

		$stmt = $_DB->prepare(
			"INSERT INTO adoptedstops (id, userid, stopname, agency, stopid, adoptedtime, nameonsign, given, abandoned) VALUES (?,?,?,?,?,?,?,?,?)");
		$stmt->bind_param('sisssssii', $id, $userid, $stopname, $agency, $stopid,  $adoptedtime, $nameonsign, $given, $abandoned);
		$result = $stmt->execute();
		
		if(!$result) {
			echo "Failed: user id: $userid - stop record id: $id - " . $_DB->error . "<br/>";
		}
	}
}



?>