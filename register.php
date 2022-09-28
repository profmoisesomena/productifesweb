<?php
 
/*
 * Following code will create a new product row
 * All product details are read from HTTP Post Request
 */

require_once('connect_db.php');

// array for JSON response
$response = array();
 
// check for required fields
if (isset($_POST['newLogin']) && isset($_POST['newPassword'])) {
 
	$newLogin = trim($_POST['newLogin']);
	$newPassword = trim($_POST['newPassword']);
		
	$usuario_existe = pg_query($con, "SELECT login FROM usuarios WHERE login='$newLogin'");
	// check for empty result
	if (pg_num_rows($usuario_existe) > 0) {
		$response["success"] = 0;
		$response["error"] = "usuario ja cadastrado";
	}
	else {
		// mysql inserting a new row
		$result = pg_query($con, "INSERT INTO usuarios(login, password, app_token) VALUES('$newLogin', '$newPassword', '')");
	 
		if ($result) {
			$response["success"] = 1;
		}
		else {
			$response["success"] = 0;
			$response["error"] = "Error BD: ".pg_last_error($con);
		}
	}
}
else {
    $response["success"] = 0;
	$response["error"] = "faltam parametros";
}

pg_close($con);
echo json_encode($response);
?>