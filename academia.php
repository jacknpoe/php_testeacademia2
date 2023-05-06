<?php
	//***********************************************************************************************
	// AUTOR: Ricardo Erick Reb�lo
	// Objetivo: classe de conex�o com o banco de dados da academia
	// Altera��es:
	// 0.1   03/05/2023 - consultaExercicios
	// 1.0   03/05/2023 - primeira publica��o
	// 1.1   03/05/2023 - primeira publica��o com o namespace corrigido
	// 1.2   04/05/2023 - corrigida diferen�a entre servidores com arquivo de configura��o
	// 1.3   05/05/2023 - adicionado par�metro para pesquisa
	// 1.4   05/05/2023 - arquivo de configura��o agora tem uma constante no lugar de vari�vel
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

			// Checa se a conex�o teve sucesso
			if ( $this->conexao->connect_errno)
			{
			    die( "Falha ao conectar: (" . $this->conexao->connect_errno . ") " . $this->conexao->connect_error);
			}
		}

		function __destruct()
		{
			$this->conexao->close();

			// Checa se a desconex�o teve sucesso
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
			if( $valor == '')
			{
				return false;	// n�o tem nome
			}

			$valor = str_replace( "'", "\'", str_replace( '\\', '\\\\', $valor));

			// procura por um nome j� existente (pra evitar duplica��o por F5)
			$resultado = $this->conexao->query( "SELECT aluno.CD_ALUNO FROM aluno WHERE aluno.NM_ALUNO = '" . $valor . "';");

			$coluna = $resultado->fetch_assoc();	// coluna n�o ser� nula se achar um nome

			if( $coluna)	// se achou um nome,
			{
				return false;	// retorna falso
			}

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