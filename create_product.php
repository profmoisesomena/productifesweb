<?php
 
/*
 * O seguinte codigo abre uma conexao com o BD e adiciona um produto nele.
 * As informacoes de um produto sao recebidas atraves de uma requisicao POST.
 */

require_once('connect_db.php');
require_once('authenticate.php');
 
$target_dir = "products_images/";

if (!file_exists($target_dir)) {
    mkdir($target_dir, 0777, true);
}

// array for JSON response
$response = array();

if(authenticate()) {
	
	// Primeiro, verifica-se se todos os parametros foram enviados pelo cliente.
	// A criacao de um produto precisa dos seguintes parametros:
	// name - nome do produto
	// price - preco do produto
	// description - descricao do produto
	// img - imagem do produto
	if (isset($_POST['name']) && isset($_POST['price']) && isset($_POST['description']) && isset($_FILES['img'])) {
	 
		// Aqui sao obtidos os parametros
		$name = $_POST['name'];
		$price = $_POST['price'];
		$description = $_POST['description'];
		
		$hash = md5_file($_FILES['img']['tmp_name']);
		$filename = $target_dir . $hash;
		if (!move_uploaded_file($_FILES["img"]["tmp_name"], $filename)) {
			$response["success"] = 0;
			$response["message"] = "Erro no upload da imagem. Erro = " . strval($_FILES["img"]["error"]);
		}
		else {
			// A proxima linha insere um novo produto no BD.
			// A variavel result indica se a insercao foi feita corretamente ou nao.
			$result = pg_query($con, "INSERT INTO products(name, price, description, img) VALUES('$name', '$price', '$description', '$filename')");
			
			if ($result) {
				// Se o produto foi inserido corretamente no servidor, o cliente 
				// recebe a chave "success" com valor 1
				$response["success"] = 1;
				$response["message"] = "Produto criado com sucesso";
			} else {
				// Se o produto nao foi inserido corretamente no servidor, o cliente 
				// recebe a chave "success" com valor 0. A chave "message" indica o 
				// motivo da falha.
				$response["success"] = 0;
				$response["message"] = "Erro ao criar produto no BD";
			}
		}
	} else {
		// Se a requisicao foi feita incorretamente, ou seja, os parametros 
		// nao foram enviados corretamente para o servidor, o cliente 
		// recebe a chave "success" com valor 0. A chave "message" indica o 
		// motivo da falha.
		$response["success"] = 0;
		$response["message"] = "Campo requerido nao preenchido";
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