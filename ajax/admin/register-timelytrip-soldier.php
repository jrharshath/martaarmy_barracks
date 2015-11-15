<?php

header('Content-Type: application/json');

function finishWith($status) {
	exit(json_encode(array('status'=>$status)));
}

include('../../lib/db.php');
init_db();

include('../../lib/admindb.php');

if(!isset($_POST['name']) || !isset($_POST['email']) || !isset($_POST['phone']) || 
   !isset($_POST['stoptoadopt']) || !isset($_POST['notes'])) {
	finishWith('incomplete');
}

$name = $_POST['name'];
$email = $_POST['email'];
$stoptoadopt = $_POST['stoptoadopt'];
$phone = $_POST['phone'];
$notes = $_POST['notes'];

$error = NULL;

$userid = createUserWithDetails((new DateTime())->format('Y-m-j'), $name, $email, 'password', $phone, $notes, $error);

if($userid === FALSE) { finishWith($error); }

$stop = new stdClass();
$stop->val = $stoptoadopt;
$stop->id = get_random_string_len(8);
$opdata = array($stop);
$TIMELYTRIP_OPID = 1;

$return = joinOperation($TIMELYTRIP_OPID, $userid, json_encode($opdata));
$status = $return['status'];
if($status != TRUE) {
	finishWith('failjoinop');
}

finishWith('success');

?>