<?php
	//***********************************************************************************************
	// AUTOR: Ricardo Erick Rebêlo
	// Objetivo: classe de conexão com o banco de dados da academia
	// Alterações:
	// 0.1   03/05/2023 - consultaExercicios
	// 1.0   03/05/2023 - primeira publicação
	// 1.1   03/05/2023 - primeira publicação com o namespace corrigido
	// 1.2   04/05/2023 - corrigida diferença entre servidores com arquivo de configuração
	// 1.3   05/05/2023 - adicionado parâmetro para pesquisa
	// 1.4   05/05/2023 - arquivo de configuração agora tem uma constante no lugar de variável
	// 1.5   05/05/2023 - incluiAlunos
	// 1.6   05/05/2023 - não deixa alunos duplicados ou nomes vazios
	// 1.7   06/06/2023 - acrescentada descrição de erro
 	// 1.8   08/06/2023 - acrescentado código de erro (com constantes) / incluiAlunos mudou para incluiAluno

	//***********************************************************************************************
	// Classe academia

	namespace jacknpoe;

	require_once( 'configuracoes.php');
	$cabecalho = "Content-Type: text/html; charset=" . CARACTERES;
	header( $cabecalho, true);

//**********************************************************************************************************************************
	define( 'SEM_FALHA', 0);
	define( 'FALHA_AO_CONECTAR', 1);
	define( 'FALHA_AO_CONSULTAR', 2);
	define( 'NOME_NAO_INFORMADO', 3);
	define( 'ALUNO_JA_EXISTE', 4);
	define( 'FALHA_AO_INSERIR', 5);
	define( 'LOGIN_NAO_INFORMADO', 6);
	define( 'SENHA_NAO_INFORMADA', 7);
	define( 'LOGIN_NAO_ENCONTRADO', 8);
	define( 'SENHA_INCORRETA', 9);
	define( 'USUARIO_INATIVO', 10);
	define( 'EMAIL_NAO_INFORMADO', 11);
	define( 'SENHAS_DIFERENTES', 12);
	define( 'LOGIN_JA_EXISTE', 13);
	define( 'FALHA_AO_SETAR', 14);

//**********************************************************************************************************************************
	class academia
	{
		public $conexao;
		public $erro = "";
		public $erron = SEM_FALHA;

//**********************************************************************************************************************************
		function __construct()
		{
			require_once( 'connect.php');
			$this->conexao = new \mysqli( $hostname, $username, $password, $database);

			// Checa se a conexão teve sucesso
			if ( $this->conexao->connect_errno)
			{
				$this->erro = "Falha ao conectar.";
				$this->erron = FALHA_AO_CONECTAR;
			    die( "Falha ao conectar: (" . $this->conexao->connect_errno . ") " . $this->conexao->connect_error);
			}

			// coloca os resultados para serem UTF-8
			$consulta = $this->conexao->query("SET character_set_results = utf8");
			if ( $this->conexao->errno)
			{
				$this->erro = "Falha ao setar.";
				$this->erron = FALHA_AO_SETAR;
				die("Falha ao setar: (" . $this->conexao->connect_errno . ") " . $this->conexao->connect_error);
			}


			$this->erro = "";
			$this->erron = SEM_FALHA;
			return true;
		}

//**********************************************************************************************************************************
		function __destruct()
		{
			$this->conexao->close();
			$this->erro = "";
			$this->erron = SEM_FALHA;
			return true;

			// Checa se a desconexão teve sucesso
/*			if ( $this->conexao->errno)
			{
			    die( "Falha ao desconectar: (" . $this->conexao->errno . ") " . $this->conexao->error);
			} */
		}

//**********************************************************************************************************************************
		function consultaExercicios( $valor = "")	// consulta os exercícios pelo valor contido
		{
			$valor = "'%" . str_replace( "'", "\'", str_replace( '\\', '\\\\', $valor)) . "%'";	// impede injeção e prepara para o LIKE

			$resultado = $this->conexao->query( "SELECT exercicio.NM_EXERCICIO, grupo.NM_GRUPO FROM exercicio INNER JOIN grupo ON exercicio.CD_GRUPO = grupo.CD_GRUPO WHERE UPPER( exercicio.NM_EXERCICIO) LIKE " . $valor . " OR UPPER( grupo.NM_GRUPO) LIKE " . $valor . " ORDER BY exercicio.NM_EXERCICIO");

			// Checa se a query teve sucesso
			if ( $this->conexao->errno)
			{
				$this->erro = "Falha ao consultar.";
				$this->erron = FALHA_AO_CONSULTAR;
			    die( "Falha ao consultar: (" . $this->conexao->errno . ") " . $this->conexao->error);
			}

			$this->erro = "";
			$this->erron = SEM_FALHA;
			return $resultado;
		}

//**********************************************************************************************************************************
		function incluiAluno( $valor = "")	// inclui um aluno, desde que não exista um com o mesmo nome ou vazio
		{
			if( $valor == '')
			{
				$this->erro = "Nome não informado.";
				$this->erron = NOME_NAO_INFORMADO;
				return false;	// não tem nome
			}

			$valor = str_replace( "'", "\'", str_replace( '\\', '\\\\', $valor));	// impede injeção de código

			// procura por um nome já existente (pra evitar duplicação por F5)
			$resultado = $this->conexao->query( "SELECT aluno.CD_ALUNO FROM aluno WHERE aluno.NM_ALUNO = '" . $valor . "';");

			if ( $this->conexao->errno)
			{
				$this->erro = "Falha ao consultar.";
				$this->erron = FALHA_AO_CONSULTAR;
			    die( "Falha ao consultar: (" . $this->conexao->errno . ") " . $this->conexao->error);
			}

			$coluna = $resultado->fetch_assoc();	// coluna não será nula se achar um nome

			if( $coluna)	// se achou um nome,
			{
				$this->erro = "Aluno já existe.";
				$this->erron = ALUNO_JA_EXISTE;
				return false;	// retorna falso
			}

			$resultado = $this->conexao->query( "INSERT INTO `aluno` (`CD_ALUNO`, `NM_ALUNO`) VALUES (NULL, '" . $valor . "');");

			// Checa se a query teve sucesso
			if ( $this->conexao->errno)
			{
				$this->erro = "Falha ao inserir.";
				$this->erron = FALHA_AO_INSERIR;
			    die( "Falha ao inserir: (" . $this->conexao->errno . ") " . $this->conexao->error);
			}

			$this->erro = "";
			$this->erron = SEM_FALHA;
			return $resultado;
		}

//**********************************************************************************************************************************
		// function verificaUsuario( $login = "", $senha = "")		// verifica se existe o usuário e se a senha está correta
		// {
		// 	if( $login = "")
		// 	{
		// 		$this->erro = "Login não informado.";
		// 		$this->erron = LOGIN_NAO_INFORMADO;
		// 		return false;
		// 	}

		// 	if( $senha = "")
		// 	{
		// 		$this->erro = "Senha não informada.";
		// 		$this->erron = SENHA_NAO_INFORMADA;
		// 		return false;
		// 	}

		// 	$login = str_replace( "'", "\'", str_replace( '\\', '\\\\', $login));	// impede injeção de código

		// 	$resultado = $this->conexao->query(
		// 		"SELECT usuario.NM_SENHA, usuario.BO_ATIVO FROM usuario WHERE usuario.NM_ID = '" . $login . "';");

		// 	$coluna = $resultado->fetch_assoc();	// pega senha (hash) e ativo de usuario

		// 	if( $coluna == false)	// se não encontrou um usuário com o login informado
		// 	{
		// 		$this->erro = "Login não encontrado.";
		// 		$this->erron = LOGIN_NAO_ENCONTRADO;
		// 		return false;
		// 	}

		// 	if( ! password_verify( $senha, $coluna["NM_SENHA"]))	// se a senha não for a mesma do HASH no banco
		// 	{
		// 		$this->erro = "Senha incorreta.";
		// 		$this->erron = SENHA_INCORRETA;
		// 		return false;
		// 	}

		// 	if( $coluna["BO_ATIVO"] != 1)	// se o usuário ainda não ativou a conta
		// 	{
		// 		$this->erro = "O usuário não foi ativado.";
		// 		$this->erron = USUARIO_INATIVO;
		// 		return false;
		// 	}

		// 	$this->erro = "";
		// 	$this->erron = SEM_FALHA;
		// 	return true;
		// }

//**********************************************************************************************************************************
		// function incluiUsuario( $login = "", $nome = "", $email = "", $senha = "", $confirmasenha = "" )
		// // inclui um usuário, desde que não exista um com o mesmo login, algum campo vazio ou senhas diferentes
		// {
		// 	if( $login == '')
		// 	{
		// 		$this->erro = "Login não informado.";
		// 		$this->erron = LOGIN_NAO_INFORMADO;
		// 		return false;	// não tem login
		// 	}

		// 	if( $nome == '')
		// 	{
		// 		$this->erro = "Nome não informado.";
		// 		$this->erron = NOME_NAO_INFORMADO;
		// 		return false;	// não tem nome
		// 	}

		// 	if( $email == '')
		// 	{
		// 		$this->erro = "E-mail não informado.";
		// 		$this->erron= EMAIL_NAO_INFORMADO;
		// 		return false;	// não tem e-mail
		// 	}

		// 	if( $senha == '')
		// 	{
		// 		$this->erro = "Senha não informada.";
		// 		$this->erron = SENHA_NAO_INFORMADA;
		// 		return false;	// não tem senha
		// 	}

		// 	if( $senha != $confirmasenha)
		// 	{
		// 		$this->erro = "Senhas diferentes.";
		// 		$this->erron = SENHAS_DIFERENTES;
		// 		return false;	// as duas senhas são diferentes
		// 	}

		// 	$login = str_replace( "'", "\'", str_replace( '\\', '\\\\', $login));	// impede injeção de código
		// 	$nome  = str_replace( "'", "\'", str_replace( '\\', '\\\\', $nome));	// impede injeção de código
		// 	$email = str_replace( "'", "\'", str_replace( '\\', '\\\\', $email));	// impede injeção de código
		// 	$senha = password_hash( $senha, PASSWORD_BCRYPT);	// a senha precisa ser HASHada ANTES de ser preparada contra injeção
		// 	$senha = str_replace( "'", "\'", str_replace( '\\', '\\\\', $senha));	// impede injeção de código

		// 	// procura por um login já existente
		// 	$resultado = $this->conexao->query( "SELECT usuario.CD_USUARIO FROM usuario WHERE usuario.NM_ID = '" . $login . "';");

		// 	if ( $this->conexao->errno)
		// 	{
		// 		$this->erro = "Falha ao consultar.";
		// 		$this->erron = FALHA_AO_CONSULTAR;
		// 	    die( "Falha ao consultar: (" . $this->conexao->errno . ") " . $this->conexao->error);
		// 	}

		// 	$coluna = $resultado->fetch_assoc();	// coluna não será nula se achar um nome

		// 	if( $coluna)	// se achou um login,
		// 	{
		// 		$this->erro = "Login já existe.";
		// 		$this->erron= LOGIN_JA_EXISTE;
		// 		return false;	// retorna falso
		// 		}

			// CONTINUAR DAQUI

/*			$resultado = $this->conexao->query( "INSERT INTO `aluno` (`CD_ALUNO`, `NM_ALUNO`) VALUES (NULL, '" . $valor . "');");

			// Checa se a query teve sucesso
			if ( $this->conexao->errno)
			{
				$this->erro = "Falha ao inserir.";
				$this->erron = FALHA_AO_INSERIR;
			    die( "Falha ao inserir: (" . $this->conexao->errno . ") " . $this->conexao->error);
			}

			$this->erro = "";
			$this->erron = SEM_FALHA;
			return $resultado; */
//		}
	}
?>