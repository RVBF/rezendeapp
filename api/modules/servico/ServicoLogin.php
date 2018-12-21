<?php
/**
 *  Serviço de login.
 *  
 *  @author	Rafael Vinicius Barros Ferreira
 */

class ServicoLogin {
	
	private $inatividadeEmMinutos = 30;
	
	private $sessaoUsuario;
	private $colecaoUsuario;
	
	function __construct(Sessao $sessaoUsuario,ColecaoUsuario $colecaoUsuario = null ) 
	{
		$this->sessaoUsuario = $sessaoUsuario;
		$this->colecaoUsuario = $colecaoUsuario;
	}
	
	function login($login, $senha) {
		$this->validarSenha($senha);

		$usuario = null;
		$senhaCriptografada = HashSenha::instance();
		$senhaCriptografada = $senhaCriptografada->gerarHashDeSenhaComSaltEmMD5($senha);		

		if($this->validarLogin($login) and $resultado = $this->colecaoUsuario->comLogin($login))
		{
			if(count($resultado) === 1)
			{
				$usuario = $resultado;
				if($usuario->getSenha() === $senhaCriptografada || $usuario->getSenha() == $senha)
				{
					$this->sessaoUsuario->criar(		
						$usuario->getId(),
						$usuario->getLogin(), 
						$ultimaRequisicao = time()
					);
				}
				else
				{
					throw new Exception("Senha incorreta!");
				}
			}
			else
			{
				throw new Exception("O login inserido não corresponde a nenhuma conta cadastrada no sistema.");
			}
		}

		return $usuario;
	}
	
	/**
	 *  Realiza o logout de um usuário.
	 */
	function logout()
	{
		$this->sessaoUsuario->destruir();
	}
	
	/**
	 *  Realiza o logout se o usuário estiver logado e inativo.
	 *  Retorna true se realizou o logout.
	 *  
	 *  @return bool
	 */
	function sairPorInatividade()
	{
		$estado = $this->estaLogado() && $this->estaInativo();
		
		if($estado)
		{
			$this->logout();
		}

		return $estado;
	}
	
	/**
	 *  Registra atividade do usuário, para que não seja considerado inativo.
	 */
	function atualizaAtividadeUsuario() {
		$this->sessaoUsuario->atualizarUltimaRequisicao();
	}	
	
	/**
	 *	Retorna true se o tempo de inatividade for maior ou igual ao limite.  
	 *
	 *  @return bool
	 */
	function estaInativo() {
		$decorrido = time() - $this->sessaoUsuario->ultimaRequisicao();
		return $decorrido >= ( $this->inatividadeEmMinutos * 60 );
	}
	
	/**
	 *  Retorna true se o usuário estiver logado.
	 */
	function estaLogado() {
		return $this->sessaoUsuario->existe();
	}

	function verificarSeUsuarioEstaLogado()
	{
		if($this->estaLogado())
		{
			if(!$this->sairPorInatividade())
			{
				$this->atualizaAtividadeUsuario();
				return true;
			}
			else
			{
				return false;
			}
		}
		else
		{
			return false;
		}
	}

	function getIdUsuario()
	{
		return $this->sessaoUsuario->idUsuario();
	}


	/**
	*  Valida o e-mail do usuário, lançando uma exceção caso haja algo inválido.
	*  @throws ColecaoException
	*/
	private function validarEmail($email)	
	{
		if(!$this->validarFormatoDeEmail($email))
		{
			throw new Exception("Formato de e-mail inválido, o e-mail deve possuir o seguinte formato (exemplo@domínio.extensão)");
		}

		if(!is_string($email))
		{
			throw new ColecaoException( 'Valor inválido para e-mail, o campo e-mail é um campo do tipo texto.' );
		}

		$resultado = $this->colecaoUsuario->comEmail($email);

		if(count($resultado) == 0)
		{
			throw new ColecaoException( 'O email  ' . $email . ' não corresponde a nenhuma conta cadastrada no sistema.' );
		}

		return true;
	}


	/**
	*  Valida o login do usuário, lançando uma exceção caso haja algo inválido.
	*  @throws ColecaoException
	*/
	private function validarLogin($login)	
	{
		if(!$this->validarFormatoLogin($login)) {
			throw new Exception("Formato de Login inválido.");
		}

		if(!is_string($login)) {
			throw new ColecaoException( 'Valor inválido para login, o campo login é um campo do tipo texto.' );
		}

		$tamLogin = mb_strlen($login);

		if($tamLogin <= Usuario::TAMANHO_MINIMO_LOGIN)
		{
			throw new ColecaoException('O login deve conter no minímo ' . Usuario::TAMANHO_MINIMO_LOGIN . ' caracteres.');
		}
		if ($tamLogin >= Usuario::TAMANHO_MAXIMO_LOGIN)
		{
			throw new ColecaoException('O login deve conter no máximo ' . Usuario::TAMANHO_MAXIMO_LOGIN . ' caracteres.');
		}


		$resultado = $this->colecaoUsuario->comLogin($login);

		if(count($resultado) == 0)
		{
			throw new ColecaoException( 'O login  ' . $login . ' não corresponde a nenhuma conta cadastrada no sistema.' );
		}

		return true;
	}

	/**
	*  Valida o senha do usuário, lançando uma exceção caso haja algo inválido.
	*  @throws ColecaoException
	*/
	private function validarSenha($senha) {
		if(!is_string($senha))
		{
			throw new ColecaoException( 'Valor inválido para senha.' );
		}

		$tamSenha = mb_strlen($senha);

		if($tamSenha <= Usuario::TAMANHO_MINIMO_SENHA)
		{
			throw new ColecaoException('O senha deve conter no minímo ' . Usuario::TAMANHO_MINIMO_SENHA . ' caracteres.');
		}
		if ($tamSenha >= Usuario::TAMANHO_MAXIMO_SENHA)
		{
			throw new ColecaoException('O senha deve conter no máximo ' . Usuario::TAMANHO_MAXIMO_SENHA . ' caracteres.');
		}

		return true;
	}	

	/**
	*  Valida o formato do e-mail do usuário, lançando uma exceção caso haja algo inválido.
	*  @throws ColecaoException
	*/
	private function validarFormatoDeEmail($email) {
		$pattern = '/^[^!@#$%¨&*()0-9][a-zA-Z0-9_.]+([.][a-zA-Z0-9_]+)*[@][a-zA-Z0-9_]+([.][a-zA-Z0-9_]+)*[.][a-zA-Z]{2,4}$/';

		if(preg_match($pattern, $email))
		{
			return true;	
		}
		else
		{
			return false;	
		}	
	}
	
	/**
	*  Valida formato do login do usuário, lançando uma exceção caso haja algo inválido.
	*  @throws ColecaoException
	*/
	private function validarFormatoLogin($login)
	{
		$formato = '/[a-zA-Z0-9\. _-]+./';

		if (preg_match($formato, $login))
		{
			return true;	
		}
		else
		{
			return false;	
		}	
	}
}
?>
