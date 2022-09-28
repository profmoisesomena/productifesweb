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

function autenticar() {
	$login = trim($GLOBALS['login']);
	$senha = trim($GLOBALS['senha']);
	$db_con = $GLOBALS['db_con'];
	// Se a autenticação não foi enviada
	if(!is_null($login)) {
		$res_consulta= pg_query($db_con, "SELECT token FROM usuarios WHERE login='$login'");

		if(pg_num_rows($res_consulta) > 0){
			$linha = pg_fetch_array($res_consulta);
			if(password_verify($senha, $linha['token'])){
				return true;
			}
		}
	}
	return false;
}
?>