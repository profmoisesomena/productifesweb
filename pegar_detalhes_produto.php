<?php
 
/*
 * O codigo seguinte retorna os dados detalhados de um produto.
 * Essa e uma requisicao do tipo GET. Um produto e identificado 
 * pelo campo id.
 */

require_once('conexao_db.php');
require_once('autenticacao.php');

// array for JSON resposta
$resposta = array();

if(autenticar()) {
 
	// Verifica se o parametro id foi enviado na requisicao
	if (isset($_GET["id"])) {
		
		// Aqui sao obtidos os parametros
		$id = $_GET['id'];
	 
		// Obtem do BD os detalhes do produto com id especificado na requisicao GET
		$res_consulta = pg_query($db_con, "SELECT * FROM produtos WHERE id = $id");
	 
		if (!empty($res_consulta)) {
			if (pg_num_rows($res_consulta) > 0) {
	 
				// Se o produto existe, os dados de detalhe do produto 
				// sao adicionados no array de resposta.
				$linha = pg_fetch_array($res_consulta);
	 
				$resposta["nome"] = $linha["nome"];
				$resposta["preco"] = $linha["preco"];
				$resposta["descricao"] = $linha["descricao"];
				$resposta["img"] = $linha["img"];
				$resposta["criado_em"] = $linha["criado_em"];
				$resposta["criado_por"] = $linha["usuarios_login"];
				
				// Caso o produto exista no BD, o cliente 
				// recebe a chave "sucesso" com valor 1.
				$resposta["sucesso"] = 1;
				
			} else {
				// Caso o produto nao exista no BD, o cliente 
				// recebe a chave "sucesso" com valor 0. A chave "erro" indica o 
				// motivo da falha.
				$resposta["sucesso"] = 0;
				$resposta["erro"] = "Produto não encontrado";
			}
		} else {
			// Caso o produto nao exista no BD, o cliente 
			// recebe a chave "sucesso" com valor 0. A chave "erro" indica o 
			// motivo da falha.
			$resposta["sucesso"] = 0;
			$resposta["erro"] = "Produto não encontrado";
		}
	} else {
		// Se a requisicao foi feita incorretamente, ou seja, os parametros 
		// nao foram enviados corretamente para o servidor, o cliente 
		// recebe a chave "sucesso" com valor 0. A chave "erro" indica o 
		// motivo da falha.
		$resposta["sucesso"] = 0;
		$resposta["erro"] = "Campo requerido não preenchido";
	}
}
else {
	// senha ou usuario nao confere
	$resposta["sucesso"] = 0;
	$resposta["error"] = "usuario ou senha não confere";
}
// Fecha a conexao com o BD
pg_close($db_con);

// Converte a resposta para o formato JSON.
echo json_encode($resposta);
?>