<?php

include('lib/db.php');
init_db();

$stmt = $_DB->prepare("SELECT id FROM users");
$stmt->execute();
$results = $stmt->get_result();



while($row = $results->fetch_array(MYSQLI_NUM)) {
	$userid = $row[0];
	$passwordhash = create_hash('password');

	$upd_stmt = $_DB->prepare("UPDATE users SET passwordhash=? WHERE id=?");
	$upd_stmt->bind_param('si', $passwordhash, $userid);
	$upd_success = $upd_stmt->execute();

	if($upd_success !== TRUE) {
		echo "$userid - failed<br/>";
		continue;
	}
	
	if($_DB->affected_rows != 0) {
		echo "$userid - updated<br/>";
	} else {
		echo "$userid - NOT updated<br/>";
	}
}

?>