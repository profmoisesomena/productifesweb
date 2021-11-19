<?php
 
/*
 * O codigo seguinte retorna os dados detalhados de um produto.
 * Essa e uma requisicao do tipo GET. Um produto e identificado 
 * pelo campo pid.
 */
 
// array que guarda a resposta da requisicao
$response = "";
 
// Verifica se o parametro pid foi enviado na requisicao
if (isset($_GET["pid"])) {
	
	// Aqui sao obtidos os parametros
    $pid = $_GET['pid'];
	
	// Abre uma conexao com o BD.
	// DATABASE_URL e uma variavel de ambiente definida pelo Heroku, servico 
	// utilizado para fazer o deploy dessa aplicacao web. Ela 
	// contem a string de conexao necessaria para acessar o BD fornecido pelo 
	// Heroku. Caso voce nao utilize o servico Heroku, voce deve alterar a 
	// linha seguinte para realizar a conexao correta com o BD de sua escolha.
	$con = pg_connect(getenv("DATABASE_URL"));
 
    // Obtem do BD os detalhes do produto com pid especificado na requisicao GET
    $result = pg_query($con, "SELECT img FROM products WHERE pid = $pid");
 
    if (!empty($result)) {
        if (pg_num_rows($result) > 0) {
 
			// Se o produto existe, os dados de detalhe do produto 
			// sao adicionados no array de resposta.
            $result = pg_fetch_array($result);
			$response = $result["img"];
        } 
    }
	
	// Fecha a conexao com o BD
	pg_close($con);
} 
echo $response;
?>