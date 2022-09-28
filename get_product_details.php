<?php
 
/*
 * O codigo seguinte retorna os dados detalhados de um produto.
 * Essa e uma requisicao do tipo GET. Um produto e identificado 
 * pelo campo pid.
 */

require_once('connect_db.php');
require_once('authenticate.php');

// array for JSON response
$response = array();

if(authenticate()) {
 
	// Verifica se o parametro pid foi enviado na requisicao
	if (isset($_GET["pid"])) {
		
		// Aqui sao obtidos os parametros
		$pid = $_GET['pid'];
	 
		// Obtem do BD os detalhes do produto com pid especificado na requisicao GET
		$result = pg_query($con, "SELECT * FROM products WHERE pid = $pid");
	 
		if (!empty($result)) {
			if (pg_num_rows($result) > 0) {
	 
				// Se o produto existe, os dados de detalhe do produto 
				// sao adicionados no array de resposta.
				$row = pg_fetch_array($result);
	 
				$response["name"] = $row["name"];
				$response["price"] = $row["price"];
				$response["description"] = $row["description"];
				$response["img"] = $row["img"];
				$response["created_at"] = $row["created_at"];
				$response["created_by"] = $row["usuarios_login"];
				
				// Caso o produto exista no BD, o cliente 
				// recebe a chave "success" com valor 1.
				$response["success"] = 1;
				
			} else {
				// Caso o produto nao exista no BD, o cliente 
				// recebe a chave "success" com valor 0. A chave "message" indica o 
				// motivo da falha.
				$response["success"] = 0;
				$response["message"] = "Produto não encontrado";
			}
		} else {
			// Caso o produto nao exista no BD, o cliente 
			// recebe a chave "success" com valor 0. A chave "message" indica o 
			// motivo da falha.
			$response["success"] = 0;
			$response["message"] = "Produto não encontrado";
		}
	} else {
		// Se a requisicao foi feita incorretamente, ou seja, os parametros 
		// nao foram enviados corretamente para o servidor, o cliente 
		// recebe a chave "success" com valor 0. A chave "message" indica o 
		// motivo da falha.
		$response["success"] = 0;
		$response["message"] = "Campo requerido não preenchido";
	}
}
else {
	// senha ou usuario nao confere
	$response["success"] = 0;
	$response["error"] = "usuario ou senha não confere";
}
// Fecha a conexao com o BD
pg_close($con);

// Converte a resposta para o formato JSON.
echo json_encode($response);
?>