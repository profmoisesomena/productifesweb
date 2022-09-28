<?php
 
/*
 * Following code will create a new product row
 * All product details are read from HTTP Post Request
 */

require_once('conexao_db.php');

// array for JSON resposta
$resposta = array();
 
// check for required fields
if (isset($_POST['novo_login']) && isset($_POST['nova_senha'])) {
 
	$novo_login = trim($_POST['novo_login']);
	$nova_senha = trim($_POST['nova_senha']);
	
	$token = password_hash($nova_senha, PASSWORD_DEFAULT);
		
	$usuario_existe = pg_query($db_con, "SELECT login FROM usuarios WHERE login='$novo_login'");
	// check for empty result
	if (pg_num_rows($usuario_existe) > 0) {
		$resposta["sucesso"] = 0;
		$resposta["erro"] = "usuario ja cadastrado";
	}
	else {
		// mysql inserting a new row
		$res_consulta = pg_query($db_con, "INSERT INTO usuarios(login, token) VALUES('$novo_login', '$token')");
	 
		if ($res_consulta) {
			$resposta["sucesso"] = 1;
		}
		else {
			$resposta["sucesso"] = 0;
			$resposta["erro"] = "erro BD: ".pg_last_error($db_con);
		}
	}
}
else {
    $resposta["sucesso"] = 0;
	$resposta["erro"] = "faltam parametros";
}

pg_close($db_con);
echo json_encode($resposta);
?>