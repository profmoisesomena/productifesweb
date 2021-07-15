<?php
 
/*
 * O codigo seguinte retorna os dados detalhados de um produto.
 * Essa e uma requisicao do tipo GET. Um produto e identificado 
 * pelo campo pid.
 */
 
// array que guarda a resposta da requisicao
$response = array();
 
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
    $result = pg_query($con, "SELECT *FROM products WHERE pid = $pid");
 
    if (!empty($result)) {
        if (pg_num_rows($result) > 0) {
 
			// Se o produto existe, os dados de detalhe do produto 
			// sao adicionados no array de resposta.
            $result = pg_fetch_array($result);
 
            $product = array();
            $product["name"] = $result["name"];
            $product["price"] = $result["price"];
            $product["description"] = $result["description"];
			$product["img"] = $result["img"];
            $product["created_at"] = $result["created_at"];
            
            // Caso o produto exista no BD, o cliente 
			// recebe a chave "success" com valor 1.
            $response["success"] = 1;
 
            $response["product"] = array();
 
			// Converte a resposta para o formato JSON.
            array_push($response["product"], $product);
			
			// Fecha a conexao com o BD
			pg_close($con);
 
            // Converte a resposta para o formato JSON.
            echo json_encode($response);
        } else {
            // Caso o produto nao exista no BD, o cliente 
			// recebe a chave "success" com valor 0. A chave "message" indica o 
			// motivo da falha.
            $response["success"] = 0;
            $response["message"] = "Produto não encontrado";
			
			// Fecha a conexao com o BD
			pg_close($con);
 
            // Converte a resposta para o formato JSON.
            echo json_encode($response);
        }
    } else {
        // Caso o produto nao exista no BD, o cliente 
		// recebe a chave "success" com valor 0. A chave "message" indica o 
		// motivo da falha.
        $response["success"] = 0;
        $response["message"] = "Produto não encontrado";
 
		// Fecha a conexao com o BD
		pg_close($con);
 
        // Converte a resposta para o formato JSON.
        echo json_encode($response);
    }
} else {
    // Se a requisicao foi feita incorretamente, ou seja, os parametros 
	// nao foram enviados corretamente para o servidor, o cliente 
	// recebe a chave "success" com valor 0. A chave "message" indica o 
	// motivo da falha.
    $response["success"] = 0;
    $response["message"] = "Campo requerido não preenchido";
 
    // Converte a resposta para o formato JSON.
    echo json_encode($response);
}
?>