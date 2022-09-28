<?php
 
/*
 * O seguinte codigo retorna para o cliente a lista de produtos 
 * armazenados no servidor. Essa e uma requisicao do tipo GET. 
 * Nao sao necessarios nenhum tipo de parametro.
 * A resposta e no formato JSON.
 */

require_once('connect_db.php');
require_once('authenticate.php');

// array for JSON response
$response = array();

if(authenticate()) {
	
	// check for required fields
	if (isset($_GET['limit']) && isset($_GET['offset'])) {
	 
		$limit = $_GET['limit'];
		$offset = $_GET['offset'];
 
		// Realiza uma consulta ao BD e obtem todos os produtos.
		$result = pg_query($con, "SELECT * FROM products LIMIT " . $limit . " OFFSET " . $offset);

		// Caso existam produtos no BD, eles sao armazenados na 
		// chave "products". O valor dessa chave e formado por um 
		// array onde cada elemento e um produto.
		$response["products"] = array();
		$response["success"] = 1;

		if (pg_num_rows($result) > 0) {
			while ($row = pg_fetch_array($result)) {
				// Para cada produto, sao retornados somente o 
				// pid (id do produto) e o nome do produto. Nao ha necessidade 
				// de retornar nesse momento todos os campos de todos os produtos 
				// pois a app cliente, inicialmente, so precisa do nome do mesmo para 
				// exibir na lista de produtos. O campo pid e usado pela app cliente 
				// para buscar os detalhes de um produto especifico quando o usuario 
				// o seleciona. Esse tipo de estrategia poupa banda de rede, uma vez 
				// os detalhes de um produto somente serao transferidos ao cliente 
				// em caso de real interesse.
				$product = array();
				$product["pid"] = $row["pid"];
				$product["name"] = $row["name"];
				$product["img"] = $row["img"];
				$product["price"] = $row["price"];
		 
				// Adiciona o produto no array de produtos.
				array_push($response["products"], $product);
			}
		}
	}
}
else {
	// senha ou usuario nao confere
	$response["success"] = 0;
	$response["error"] = "usuario ou senha não confere";
}

pg_close($con);

// Converte a resposta para o formato JSON.
echo json_encode($response);
?>