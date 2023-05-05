<?php
	//***********************************************************************************************
	// AUTOR: Ricardo Erick Rebъlo
	// Objetivo: classe de conexуo com o banco de dados da academia
	// Alteraчѕes:
	// 0.1   03/05/2023 - consultaExercicios
	// 1.0   03/05/2023 - primeira publicaчуo
	// 1.1   03/05/2023 - primeira publicaчуo com o namespace corrigido
	// 1.2   04/05/2023 - corrigida diferenчa entre servidores com arquivo de configuraчуo
	// 1.3   05/05/2023 - adicionado parтmetro para pesquisa
	// 1.4   05/05/2023 - arquivo de configuraчуo agora tem uma constante no lugar de variсvel
	// 1.5   05/05/2023 - incluiAlunos

	//***********************************************************************************************
	// Classe academia

	namespace jacknpoe;

	require_once( 'configuracoes.php');
	$cabecalho = "Content-Type: text/html; charset=" . CARACTERES;
	header( $cabecalho, true);

	class academia
	{
		public $conexao;

		function __construct()
		{
			require_once( 'connect.php');
			$this->conexao = new \mysqli( $hostname, $username, $password, $database);

			// Checa se a conexуo teve sucesso
			if ( $this->conexao->connect_errno)
			{
			    die( "Falha ao conectar: (" . $this->conexao->connect_errno . ") " . $this->conexao->connect_error);
			}
		}

		function __destruct()
		{
			$this->conexao->close();

			// Checa se a desconexуo teve sucesso
/*			if ( $this->conexao->errno)
			{
			    die( "Falha ao desconectar: (" . $this->conexao->errno . ") " . $this->conexao->error);
			} */
		}

		function consultaExercicios( $valor = "")
		{
			$valor = "'%" . str_replace( "'", "\'", str_replace( '\\', '\\\\', $valor)) . "%'";

			$resultado = $this->conexao->query( "SELECT exercicio.NM_EXERCICIO, grupo.NM_GRUPO FROM exercicio INNER JOIN grupo ON exercicio.CD_GRUPO = grupo.CD_GRUPO WHERE UPPER( exercicio.NM_EXERCICIO) LIKE " . $valor . " OR UPPER( grupo.NM_GRUPO) LIKE " . $valor . " ORDER BY exercicio.NM_EXERCICIO");

			// Checa se a query teve sucesso
			if ( $this->conexao->errno)
			{
			    die( "Falha ao consultar: (" . $this->conexao->errno . ") " . $this->conexao->error);
			}

			return $resultado;
		}

		function incluiAlunos( $valor = "")
		{
			$valor = str_replace( "'", "\'", str_replace( '\\', '\\\\', $valor));

			$resultado = $this->conexao->query( "INSERT INTO `aluno` (`CD_ALUNO`, `NM_ALUNO`) VALUES (NULL, '" . $valor . "');");

			// Checa se a query teve sucesso
			if ( $this->conexao->errno)
			{
			    die( "Falha ao inserir: (" . $this->conexao->errno . ") " . $this->conexao->error);
			}

			return $resultado;
		}
	}
?>