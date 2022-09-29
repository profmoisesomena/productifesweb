<?php
 
/*
 * O codigo seguinte retorna os dados detalhados de um produto.
 * Essa e uma requisicao do tipo GET. Um produto e identificado 
 * pelo campo pid.
 */
 
// array que guarda a resposta da requisicao
$resposta = "";


// Verifica se o parametro id foi enviado na requisicao
if (isset($_GET["id"])) {
	
	// Aqui sao obtidos os parametros
	$id = $_GET['id'];
 
	// Obtem do BD os detalhes do produto com pid especificado na requisicao GET
	$res_consulta = pg_query($db_con, "SELECT img FROM produtos WHERE id = $id");
 
	if (!empty($res_consulta)) {
		if (pg_num_rows($res_consulta) > 0) {
 
			// Se o produto existe, os dados de detalhe do produto 
			// sao adicionados no array de resposta.
			$linha = pg_fetch_array($res_consulta);
			$resposta = $linha["img"];
		} 
	}
}

// Fecha a conexao com o BD
pg_close($db_con);

echo $resposta;
?>