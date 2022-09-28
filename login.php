<?php

require_once('conexao_db.php');
require_once('autenticacao.php');

// array for JSON resposta
$resposta = array();

if(autenticar()) {
	$resposta["sucesso"] = 1;
}
else {
	// senha ou usuario nao confere
	$resposta["sucesso"] = 0;
	$resposta["erro"] = "usuario ou senha não confere";
}

pg_close($con);
echo json_encode($resposta);
?>