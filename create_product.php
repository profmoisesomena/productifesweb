<?php
 
/*
 * O seguinte codigo abre uma conexao com o BD e adiciona um produto nele.
 * As informacoes de um produto sao recebidas atraves de uma requisicao POST.
 */
 
// array que guarda a resposta da requisicao
$response = array();
 
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
	
	// Para a imagem do produto, primeiramente se determina qual o tipo de imagem.
	// Isso e feito atraves da obtencao da extensao do arquivo, localizada na parte
	// final do nome do arquivo (ex. ".jpg")
	$imageFileType = strtolower(pathinfo(basename($_FILES["img"]["name"]),PATHINFO_EXTENSION));
	
	// A imagem e convertida de binario para string atraves do metodo de codificacao
	// base64
	$image_base64 = base64_encode(file_get_contents($_FILES['img']['tmp_name']) );
	
	// No futuro, clientes que pedirem pela imagem armazenada no BD devem ser 
	// capazes de converter a string base64 para o formato original binario.
	// Para que isso possa ser feito, contatena-se no inicio da string base64 da 
	// imagem o mimetype do arquivo original. O mimetype e um codigo que indica o 
	// tipo de arquivo e sua extensao.
	$img = 'data:image/'.$imageFileType.';base64,'.$image_base64;
    
 	// Abre uma conexao com o BD.
	// DATABASE_URL e uma variavel de ambiente definida pelo Heroku, servico 
	// utilizado para fazer o deploy dessa aplicacao web. Ela 
	// contem a string de conexao necessaria para acessar o BD fornecido pelo 
	// Heroku. Caso voce nao utilize o servico Heroku, voce deve alterar a 
	// linha seguinte para realizar a conexao correta com o BD de sua escolha.
	$con = pg_connect(getenv("DATABASE_URL"));
	
    // A proxima linha insere um novo produto no BD.
	// A variavel result indica se a insercao foi feita corretamente ou nao.
    $result = pg_query($con, "INSERT INTO products(name, price, description, img) VALUES('$name', '$price', '$description', '$img')");
 
    
    if ($result) {
        // Se o produto foi inserido corretamente no servidor, o cliente 
		// recebe a chave "success" com valor 1
        $response["success"] = 1;
        $response["message"] = "Produto criado com sucesso";
		
		// Fecha a conexao com o BD
		pg_close($con);
 
        // Converte a resposta para o formato JSON.
        echo json_encode($response);
    } else {
        // Se o produto nao foi inserido corretamente no servidor, o cliente 
		// recebe a chave "success" com valor 0. A chave "message" indica o 
		// motivo da falha.
        $response["success"] = 0;
        $response["message"] = "Erro ao criar produto no BD";
		
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
    $response["message"] = "Campo requerido nao preenchido";
 
    // Converte a resposta para o formato JSON.
    echo json_encode($response);
}
?>