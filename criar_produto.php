<?php
 
/*
 * O seguinte codigo abre uma conexao com o BD e adiciona um produto nele.
 * As informacoes de um produto sao recebidas atraves de uma requisicao POST.
 */

require_once('conexao_db.php');
require_once('autenticacao.php');
 
$dir_imagens = "imagens_produtos/";

if (!file_exists($dir_imagens)) {
    mkdir($dir_imagens, 0777, true);
}

// array for JSON resposta
$resposta = array();

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
		
		$login = $GLOBALS['login'];
		
		$hash = md5_file($_FILES['img']['tmp_name']);
		$nome_arq_img = $dir_imagens . $hash;
		if (!move_uploaded_file($_FILES["img"]["tmp_name"], $nome_arq_img)) {
			$resposta["sucesso"] = 0;
			$resposta["erro"] = "Erro no upload da imagem. Erro = " . strval($_FILES["img"]["error"]);
		}
		else {
			// A proxima linha insere um novo produto no BD.
			// A variavel res_consulta indica se a insercao foi feita corretamente ou nao.
			$res_consulta = pg_query($db_con, "INSERT INTO produtos(nome, preco, descricao, img, usuarios_login) VALUES('$nome', '$preco', '$descricao', '$nome_arq_img', '$login')");
			
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