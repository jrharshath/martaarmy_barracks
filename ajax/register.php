<?php

header('Content-Type: application/json');

function finishWith($status, $sid=NULL) {
	exit(json_encode(array('status'=>$status)));
}

if(!isset($_POST['name']) || 
	!isset($_POST['email']) || 
	!isset($_POST['password'])) {

	finishWith('incomplete');
}

$name = trim($_POST['name']);
$email = trim($_POST['email']);
$password = $_POST['password'];

if($name === '') { finishWith('noname'); }
if(!filter_var($email, FILTER_VALIDATE_EMAIL)) { finishWith('bademail'); }
if($password === '') { finishWith('nopassword'); }

include('../lib/db.php');
init_db();

$error = '';
$userid = createUser($name, $email, $password, $error);

if($userid === FALSE) { finishWith($error); }

finishWith('success')

?>