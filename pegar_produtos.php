<?php
 
/*
 * O seguinte codigo retorna para o cliente a lista de produtos 
 * armazenados no servidor. Essa e uma requisicao do tipo GET. 
 * Devem ser enviados os parâmetro de limit e offset para 
 * realização da paginação de dados no cliente.
 * A resposta e no formato JSON.
 */

// conexão com bd
require_once('conexao_db.php');

// autenticação
require_once('autenticacao.php');

// array for JSON resposta
$resposta = array();

// verifica se o usuário conseguiu autenticar
if(autenticar()) {
	
	// Primeiro, verifica-se se todos os parametros foram enviados pelo cliente.
	// limit - quantidade de produtos a ser entregues
	// offset - indica a partir de qual produto começa a lista
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
				// pid (id do produto), o nome do produto e o preço. Nao ha necessidade 
				// de retornar nesse momento todos os campos dos produtos 
				// pois a app cliente, inicialmente, so precisa do nome e preço do mesmo para 
				// exibir na lista de produtos. O campo id e usado pela app cliente 
				// para buscar os detalhes de um produto especifico quando o usuario 
				// o seleciona. Esse tipo de estrategia poupa banda de rede, uma vez 
				// os detalhes de um produto somente serao transferidos ao cliente 
				// em caso de real interesse.
				$produto = array();
				$produto["id"] = $linha["id"];
				$produto["nome"] = $linha["nome"];
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

// fecha conexão com o bd
pg_close($db_con);

// Converte a resposta para o formato JSON.
echo json_encode($resposta);
?>