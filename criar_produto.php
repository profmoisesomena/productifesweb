<?php
 
/*
 * O seguinte codigo abre uma conexao com o BD e adiciona um produto nele.
 * As informacoes de um produto sao recebidas atraves de uma requisicao POST.
 */

// conexão com bd
require_once('conexao_db.php');

// autenticação
require_once('autenticacao.php');

// array de resposta
$resposta = array();

// verifica se o usuário conseguiu autenticar
if(autenticar()) {
	
	// Primeiro, verifica-se se todos os parametros foram enviados pelo cliente.
	// A criacao de um produto precisa dos seguintes parametros:
	// nome - nome do produto
	// preco - preco do produto
	// descricao - descricao do produto
	// img - imagem do produto
	if (isset($_POST['nome']) && isset($_POST['preco']) && isset($_POST['descricao']) && isset($_FILES['img'])) {
		
		// Aqui sao obtidos os parametros
		$nome = $_POST['nome'];
		$preco = $_POST['preco'];
		$descricao = $_POST['descricao'];
		
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

		// A proxima linha insere um novo produto no BD.
		// A variavel res_consulta indica se a insercao foi feita corretamente ou nao.
		$res_consulta = pg_query($db_con, "INSERT INTO produtos(nome, preco, descricao, img, usuarios_login) VALUES('$nome', '$preco', '$descricao', '$img', '$login')");
		
		if ($res_consulta) {
			// Se o produto foi inserido corretamente no servidor, o cliente 
			// recebe a chave "sucesso" com valor 1
			$resposta["sucesso"] = 1;
			$resposta["erro"] = "Produto criado com sucesso";
		} else {
			// Se o produto nao foi inserido corretamente no servidor, o cliente 
			// recebe a chave "sucesso" com valor 0. A chave "erro" indica o 
			// motivo da falha.
			$resposta["sucesso"] = 0;
			$resposta["erro"] = "Erro ao criar produto no BD";
		}
	} else {
		// Se a requisicao foi feita incorretamente, ou seja, os parametros 
		// nao foram enviados corretamente para o servidor, o cliente 
		// recebe a chave "sucesso" com valor 0. A chave "erro" indica o 
		// motivo da falha.
		$resposta["sucesso"] = 0;
		$resposta["erro"] = "Campo requerido nao preenchido";
	}
}
else {
	// senha ou usuario nao confere
	$resposta["sucesso"] = 0;
	$resposta["erro"] = "usuario ou senha não confere";
}

// Fecha a conexao com o BD
pg_close($db_con);

// Converte a resposta para o formato JSON.
echo json_encode($resposta);
?>