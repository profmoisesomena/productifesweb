<?php

require_once('connect_db.php');
require_once('authenticate.php');

// array for JSON response
$response = array();

if(authenticate()) {
	$response["success"] = 1;
}
else {
	// senha ou usuario nao confere
	$response["success"] = 0;
	$response["error"] = "usuario ou senha não confere";
}

pg_close($con);
echo json_encode($response);
?>