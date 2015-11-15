<?php

header('Content-Type: application/json');

function finishWith($status, $sid=NULL) {
	exit(json_encode(array('status'=>$status)));
}

if(!isset($_POST['name']) || !isset($_POST['email']) || !isset($_POST['stoptoadopt']) || !isset($_POST['comment'])) {
	finishWith('incomplete');
}

$name = trim($_POST['name']);
$email = trim($_POST['email']);
$stoptoadopt = trim($_POST['stoptoadopt']);
$notes = trim($_POST['comment']);
$password = 'password';

if($name === '') { finishWith('noname'); }
if(!filter_var($email, FILTER_VALIDATE_EMAIL)) { finishWith('bademail'); }
if($stoptoadopt === '') { finishWith('nostoptoadopt'); }
if($notes === '') { finishWith('nocomment'); }

include('../lib/db.php');
init_db();

$today = (new DateTime())->format('Y-m-j');
$error = '';
$userid = createUserWithDetails($today, $name, $email, $password, '', $notes, $error);

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

include('../lib/email-lib.php');

sendWelcomeEmail($email, $name);

finishWith('success');

?>