<?php

header('Content-Type: application/json');

function finishWith($status, $sid=NULL) {
	exit(json_encode(array('status'=>$status)));
}

if(!isset($_POST['name']) || !isset($_POST['email']) || !isset($_POST['phone']) || !isset($_POST['comment']) || !isset($_POST['stopmode'])) {
	finishWith('incomplete');
}

$name = trim($_POST['name']);
$email = trim($_POST['email']);
$phone = trim($_POST['phone']);
$notes = trim($_POST['comment']);

if($name === '') { finishWith('noname'); }
if(!filter_var($email, FILTER_VALIDATE_EMAIL)) { finishWith('bademail'); }
if($notes === '') { finishWith('nocomment'); }

include('../lib/db.php');
include('../lib/email-lib.php');
init_db();

$op_result = '';
$userid = createOrGetUser($name, $email, $phone, $notes, $op_result);
if($userid === FALSE) { finishWith('failure'); }

$stopmode = $_POST['stopmode'];
if($stopmode=='stopids') {
	if(!isset($_POST['stopids']) || !isset($_POST['stopnames'])) {
		finishWith('nostoptoadopt');
	}
	$stopid_inps = $_POST['stopids'];
	$stopnames = $_POST['stopnames'];

	if(count($stopid_inps)==0) { finishWith('nostoptoadopt'); }
	if(count($stopid_inps)!=count($stopnames)) { finishWith('nostoptoadopt'); }

	for($i=0; $i<count($stopid_inps); $i++) {
		$stopid_inp = $stopid_inps[$i];
		$stopname = $stopnames[$i];
		$parts = explode('_', $stopid_inp);
		$agency = $parts[0];
		$stopid = $parts[1];

		$result = addAdoptedStop($userid, $stopname, $stopid, $agency);
		if(!$result) {
			finishWith('failure');
		}
	}

	finishWith('success');

} else if($stopmode=='stopaddress') {
	if(!isset($_POST['stopaddress'])) {
		finishWith('incomplete');
	}

	$stoptoadopt = trim($_POST['stopaddress']);
	if(empty($stoptoadopt)) {
		finishWith('nostoptoadopt');
	}

	$result = addAdoptedStop($userid, $stoptoadopt, null, null);
	if(!$result) { finishWith('failure'); }

	if($op_result=='newuser') {
		sendWelcomeEmail();
	} else if($op_result=='already') {
		sendNewStopsAdoptedEmail();
	}

	finishWith('success');

} else {
	finishWith('invalidstopmode');
}



?>