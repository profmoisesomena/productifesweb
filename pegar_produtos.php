<?php
 
/*
 * O seguinte codigo retorna para o cliente a lista de produtos 
 * armazenados no servidor. Essa e uma requisicao do tipo GET. 
 * Nao sao necessarios nenhum tipo de parametro.
 * A resposta e no formato JSON.
 */

require_once('conexao_db.php');
require_once('autenticacao.php');

// array for JSON resposta
$resposta = array();

if(autenticar()) {
	
	// check for required fields
	if (isset($_GET['limit']) && isset($_GET['offset'])) {
	 
		$limit = $_GET['limit'];
		$offset = $_GET['offset'];
 
		// Realiza uma consulta ao BD e obtem todos os produtos.
		$result = pg_query($db_con, "SELECT * FROM produtos LIMIT " . $limit . " OFFSET " . $offset);

		// Caso existam produtos no BD, eles sao armazenados na 
		// chave "produtos". O valor dessa chave e formado por um 
		// array onde cada elemento e um produto.
		$resposta["produtos"] = array();
		$resposta["sucesso"] = 1;

		if (pg_num_rows($result) > 0) {
			while ($linha = pg_fetch_array($result)) {
				// Para cada produto, sao retornados somente o 
				// pid (id do produto) e o nome do produto. Nao ha necessidade 
				// de retornar nesse momento todos os campos de todos os produtos 
				// pois a app cliente, inicialmente, so precisa do nome do mesmo para 
				// exibir na lista de produtos. O campo pid e usado pela app cliente 
				// para buscar os detalhes de um produto especifico quando o usuario 
				// o seleciona. Esse tipo de estrategia poupa banda de rede, uma vez 
				// os detalhes de um produto somente serao transferidos ao cliente 
				// em caso de real interesse.
				$produto = array();
				$produto["id"] = $linha["id"];
				$produto["nome"] = $linha["nome"];
				$produto["img"] = $linha["img"];
				$produto["preco"] = $linha["preco"];
		 
				// Adiciona o produto no array de produtos.
				array_push($resposta["produtos"], $produto);
			}
		}
	}
}
else {
	// senha ou usuario nao confere
	$resposta["sucesso"] = 0;
	$resposta["erro"] = "usuario ou senha não confere";
}

pg_close($db_con);

// Converte a resposta para o formato JSON.
echo json_encode($resposta);
?>