<?php

$login = NULL;
$senha = NULL;

// Método para mod_php (Apache)
if ( isset( $_SERVER['PHP_AUTH_USER'] ) ) {
	$login = $_SERVER['PHP_AUTH_USER'];
	$senha = $_SERVER['PHP_AUTH_PW'];
}
// Método para demais servers
elseif(isset( $_SERVER['HTTP_AUTHORIZATION'])) {
	if(preg_match( '/^basic/i', $_SERVER['HTTP_AUTHORIZATION']))
		list($login, $senha) = explode(':', base64_decode(substr($_SERVER['HTTP_AUTHORIZATION'], 6)));
}

function authenticate() {
	
	// Se a autenticação não foi enviada
	if(!is_null($login)) {
		$login = trim($login);
		$password = trim($password);
		
		$query = pg_query($con, "SELECT password FROM usuarios WHERE login='$login'");

		if(pg_num_rows($query) > 0){
			$row = pg_fetch_array($query);
			if($password == $row['password']){
				return true;
			}
		}
	}
	return false;
}
?>