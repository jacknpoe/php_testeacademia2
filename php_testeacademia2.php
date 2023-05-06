<!DOCTYPE html>
<html lang="pt-BR">
	<head>
		<title>Teste da classe academia 2: Incluir aluno</title>
 		<link rel="stylesheet" href="php_testeacademia2.css"/>
		<link rel="icon" type="image/png" href="php_testeacademia2.png"/>
		<meta name="viewport" content="width=device-width, initial-scale=1">
	</head>
	<body>
		<?php
			require_once( 'configuracoes.php');
			$cabecalho = "Content-Type: text/html; charset=" . CARACTERES;
			header( $cabecalho, true);

			$resultado = '';
			$valor = '';

			if( isset( $_POST[ 'incluir']))
			{
				$valor = $_POST["valor"];

				require_once( 'academia.php');
				$classe_academia = new \jacknpoe\academia();
				$resultado = $classe_academia->incluiAlunos( $valor);

				if( $resultado)
				{
					$resultado = "Aluno '" . $valor . "' inclu&iacute;do!";
					$valor = "";
				}
				else
				{
					$resultado = 'Falha ao incluir!';
				}
			}
		?>
		<h1>Teste da classe academia 2: Incluir aluno</h1>

		<form action="php_testeacademia2.php" method="POST" style="border: 0px">
			<p>Valor: <input type="text" name="valor" style="width: 300px" value="<?php echo htmlspecialchars( $valor, ENT_QUOTES | ENT_SUBSTITUTE | ENT_HTML401, CARACTERES); ?>" autofocus></p>
			<p><input type="submit" name="incluir" value="Incluir"></p>
		</form>

		<br>
		<p>Resultado: <?php echo $resultado; ?></p>

		<br>
		<p><a href="https://github.com/jacknpoe/php_testeacademia2">Reposit&oacute;rio no GitHub</a></p><br>
		<form action="index.html" method="POST" style="border: 0px">
			<p><input type="submit" name="voltar" value="Voltar"></p>
		</form>
	</body>
</html>