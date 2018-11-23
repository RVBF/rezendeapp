<?php
/**
* Serviço de Login
*
* @author	Rafael Vinicius barros ferreira
*/

class ServicoUsuario {
    private $colecaoUsuario;
	
	function __construct() {
		$this->colecaoUsuario = Dice::instance()->create('ColecaoUsuario');
	}
	
	/**
	*  Método que recebe a identificação (E-mail) e faz a validação de acordo 
	* com os requisitos específicos.
	* 
	* @param string $login E-mail a ser validada.
	* @throws ServicoException.
	*/
	private function validarEmail($login)
	{
		if (! filter_var($login, FILTER_VALIDATE_EMAIL))
		{
			throw new ServicoException('Por favor, informe um e-mail válido.');
		}
		
		$tamEmail = mb_strlen($login);

		if ($tamEmail < Usuario::TAMANHO_MINIMO_EMAIL)
		{
			throw new ServicoException('O e-mail deve possuir no mímino ' . Usuario::TAMANHO_MINIMO_EMAIL . ' caracteres.');
		}
		
		if ($tamEmail > Usuario::TAMANHO_MAXIMO_EMAIL)
		{
			throw new ServicoException('O e-mail deve possuir no máximo ' . Usuario::TAMANHO_MAXIMO_EMAIL . ' caracteres.');
		}
	}

	/**
	*  Método que recebe a identificação (E-mail) e faz a validação de acordo 
	* com os requisitos específicos.
	* 
	* @param string $login E-mail a ser validada.
	* @throws ServicoException.
	*/
	private function validarLogin($login)
	{
		$tamEmail = mb_strlen($login);

		if ($tamEmail < Usuario::TAMANHO_MINIMO_LOGIN)
		{
			throw new ServicoException('O login deve possuir no mímino ' . Usuario::TAMANHO_MINIMO_LOGIN . ' caracteres.');
		}
		
		if ($tamEmail > Usuario::TAMANHO_MAXIMO_LOGIN)
		{
			throw new ServicoException('O login deve possuir no máximo ' . Usuario::TAMANHO_MAXIMO_LOGIN . ' caracteres.');
		}
	}
	
	/**
	*  Método que recebe a senha e faz a validação de acordo com os requisitos específicos.
	* 
	* @param string $senha Senha a ser validada.
	* @throws ServicoException.
	*/
	private function validarSenha($senha)
	{
		$tamSenha = mb_strlen($senha);

		if ($tamSenha < Usuario::TAMANHO_MINIMO_SENHA)
		{
			throw new ServicoException('A senha deve possuir no mínimo ' . Usuario::TAMANHO_MINIMO_SENHA . ' caracteres.');
		}
		if ($tamSenha > Usuario::TAMANHO_MAXIMO_SENHA)
		{
			throw new ServicoException('A senha deve possuir no máximo ' . Usuario::TAMANHO_MAXIMO_SENHA . ' caracteres.');
		}
	}

	/**
	* Método que recebe a identificação e retorna verdadeiro caso a mesma seja um e-mail.
	* 
	* @param string $login E-mail ou Login a ser verificada.
	* @return true.
	*/
	private function ehEmail($login)
	{
		return mb_strstr($login, '@' ) !== false;
	}

	/**
	* Método que recebe a identificação (SIAPE ou e-mail)  e senha e busca um Usuario 
	* ativo, caso o não econtra-lo é lançada uma ServicoException.
	* 
	* @param string $login SIAPE/E-mail a ser procurada.
	* @param string $senha Senha a ser procurada.
	* @return $usuario  Caso encontrado, retorna um Objeto Usuario.
	* @throws ServicoException.
	*/
	private function comloginESenha($login, $senha)
	{

		$ehEmail = $this->ehEmail($login);

		if ($ehEmail)
		{
			$this->validarEmail($login);
		}
		else 
		{
			$this->validarLogin($login);
		}
	
		$this->validarSenha($senha);
	
		$usuario = ($ehEmail ) ? $this->colecaoUsuario->comEmailESenha($login, $senha ) : $this->colecaoUsuario->comLoginESenha($login, $senha);
	
		if (null === $usuario)
		{
			throw new ServicoException('Identificação ou senha inválidos ou você ainda não foi ativado.');
		}
	
		return $usuario;
	}

	/**
	*  Método que recebe a identificação (login ou e-mail)  e senha . Caso o usuário(Usuario) 
	* for encontrado o mesmo irá logar no sistema.
	*
	* @param string $login SIAPE/E-mail a ser procurada.
	* @param string $senha Senha a ser procurada.
	*/
	function logar($login, $senha)
	{
		$usuario = $this->comloginESenha($login, $senha);
		$this->sessao->set('usuario', ['id' => $usuario->getId(), 'nome' => $usuario->getNome()]);
	}
	
	/**
	* 	Método logout de usuário(Usuario).
	*/
	function sair()
	{
		$this->sessao->destroy();
	}

	/**
	*  Método que recebe as senhas atual, nova e de confirmacao, e caso as senhas nova e
	* de confirmacao sejam iguais e a senha atual esteja correta, utiliza o método
	* atualizarSenha da colecao para substituir a senha atual pela nova.
	* @param string $atual senha atual
	* @param string $nova senha nova
	* @param string $confirmacao senha confirmacao
	* @throws ServicoException.
	*/
	function atualizarSenha($atual, $nova, $confirmacao)
	{
		try
		{
			$this->validarSenha($atual);
			$this->validarSenha($nova);
			$this->validarSenha($confirmacao);

			if(!$this->saoSenhasIguais($nova, $confirmacao))
			{
				throw new ServicoException("A senha nova e a senha de confirmação são diferentes!");
			}

			if(!$this->colecaoUsuario->senhaAtualEstaCorreta($this->sessao->get('id'), $atual))
			{
				throw new ServicoException("A senha atual está incorreta!");
			}

			$this->colecaoUsuario->atualizarSenha($this->sessao->get('id'), $nova);
		}
		catch(\Exception $e)
		{
			throw new ServicoException($e->getMessage(), $e->getCode(), $e);
		}
	}

	/**
	*  Método que recebe duas senhas e verifica se elas são iguais.
	* @param string $senha1 senha
	* @param string $senha2 senha
	* @return boolean
	* @throws ServicoException.
	*/
	function saoSenhasIguais($senha1, $senha2)
	{
		return $senha1 === $senha2;
	}

	function validar(Usuario $obj)
	{
		if(!is_string($obj->getNome())) 
		{
			throw new ColecaoException('Por favor, informe um valor válido para o nome  do usuário.');
		}
		
		$tamNome = mb_strlen($obj->getNome());

		if($tamNome >= $obj->getTamanhoMinimoNome())
		{
			throw new ColecaoException('O nome deve possuir pelo menos ' . $obj->getTamanhoMinimoNome() . ' caracteres.');
		}

		if($tamNome <= $obj->getTamanhoMaximoNome())
		{
			throw new ColecaoException('O nome deve possuir no máximo ' .  $obj->getTamanhoMaximoNome() . ' caracteres.');
		}

		// verifica se é email.
		if(! filter_var($obj->getEmail(), FILTER_VALIDATE_EMAIL) and $this->validarFormatoEmail($obj->getEmail()))
		{
			throw new ColecaoException('Por favor, informe o email.');
		}

		$tamEmail = mb_strlen($obj->getEmail());
		
		if($tamEmail >= $obj->getTamanhoMinimoEmail())
		{
			throw new ColecaoException('O email deve possuir pelo menos ' . $obj->getTamanhoMinimoEmail(). ' caracteres.');
		}

		if($tamEmail <= $obj->getTamanhoMaximoEmail())
		{
			throw new ColecaoException('O email deve possuir no máximo ' . $obj->getTamanhoMaximoEmail() . ' caracteres.');
		}		

		if($this->validarFormatoLogin($obj->getLogin()))
		{
			throw new ColecaoException('Por favor, informe um login válido.');
		}

		$tamEmail = mb_strlen($obj->getLogin());
		
		if($tamEmail >= $obj->getTamanhoMinimoLogin())
		{
			throw new ColecaoException('O login deve possuir pelo menos ' . $obj->getTamanhoMinimoLogin(). ' caracteres.');
		}

		if($tamLogin <= $obj->getTamanhoMaximoLogin())
		{
			throw new ColecaoException('O login deve possuir no máximo ' . $obj->getTamanhoMaximoLogin() . ' caracteres.');
		}

		$tamSenha = mb_strlen($obj->getSenha());

		if($tamSenha >= $obj->getTamanhoMinimoSenha())
		{
			throw new ColecaoException('A senha deve possuir pelo menos ' . $obj->getTamanhoMinimoSenha() . ' caracteres.');
		}

		if($tamSenha <= $obj->getTamanhoMaximoSenha())
		{
			throw new ColecaoException('A senha deve possuir no máximo ' . $obj->getTamanhoMaximoSenha() . ' caracteres.');
		}
		//verifica se já existe um email com o mesmo valor no banco de dados.
		$sql = 'SELECT  email FROM ' . self::TABELA . ' WHERE email = :email';
		
		$email = $this->pdoW->run($sql, ['email' => $obj->getEmail()]);
		
		if($email > 0)
		{
			throw new ColecaoException('O email  ' . $obj->getEmail() . ' já está cadastrado.');
		}			

		//verifica se já existe um login com o mesmo valor no banco de dados.
		$sql = 'SELECT  login FROM ' . self::TABELA . ' WHERE login = :login';
		
		$login = $this->pdoW->run($sql, ['login' => $obj->getLogin()]);
		
		if($login > 0)
		{
			throw new ColecaoException('O login  ' . $obj->getLogin() . ' já está cadastrado.');
		}			
	}

	function validarFormatoEmail($email)
	{
		$conta = "^[a-zA-Z0-9\._-]+@";
		$domino = "[a-zA-Z0-9\._-]+.";
		$extensao = "([a-zA-Z]{2,4})$";

		$pattern = $conta.$domino.$extensao;
		
		if (ereg($pattern, $email))
		{
			return true;
		}
		else
		{
			return false;
		}
	}

	function validarFormatoLogin($email)
	{
		$conta = "^[a-zA-Z0-9\._-]+@";
		$domino = "[a-zA-Z0-9\._-]+.";
		$extensao = "([a-zA-Z]{2,4})$";

		$pattern = $conta.$domino.$extensao;
		
		if (ereg($pattern<?php

use phputil\Session;

/**
* Serviço de Login
*
* @author	Rafael Vinicius barros ferreira
*/

class ServicoUsuario {
	
	private $sessao;
	private $colecao;
	
	function __construct(ColecaoUsuario $colecao, Session $sessao)
	{
		$this->colecao = $colecao;
		$this->sessao = $sessao;
	}
	
	/**
	*  Método que recebe a identificação (E-mail) e faz a validação de acordo 
	* com os requisitos específicos.
	* 
	* @param string $login E-mail a ser validada.
	* @throws ServicoException.
	*/
	private function validarEmail($login)
	{
		if (! filter_var($login, FILTER_VALIDATE_EMAIL))
		{
			throw new ServicoException('Por favor, informe um e-mail válido.');
		}
		
		$tamEmail = mb_strlen($login);

		if ($tamEmail < Usuario::TAMANHO_MINIMO_EMAIL)
		{
			throw new ServicoException('O e-mail deve possuir no mímino ' . Usuario::TAMANHO_MINIMO_EMAIL . ' caracteres.');
		}
		
		if ($tamEmail > Usuario::TAMANHO_MAXIMO_EMAIL)
		{
			throw new ServicoException('O e-mail deve possuir no máximo ' . Usuario::TAMANHO_MAXIMO_EMAIL . ' caracteres.');
		}
	}

	/**
	*  Método que recebe a identificação (E-mail) e faz a validação de acordo 
	* com os requisitos específicos.
	* 
	* @param string $login E-mail a ser validada.
	* @throws ServicoException.
	*/
	private function validarLogin($login)
	{
		$tamEmail = mb_strlen($login);

		if ($tamEmail < Usuario::TAMANHO_MINIMO_LOGIN)
		{
			throw new ServicoException('O login deve possuir no mímino ' . Usuario::TAMANHO_MINIMO_LOGIN . ' caracteres.');
		}
		
		if ($tamEmail > Usuario::TAMANHO_MAXIMO_LOGIN)
		{
			throw new ServicoException('O login deve possuir no máximo ' . Usuario::TAMANHO_MAXIMO_LOGIN . ' caracteres.');
		}
	}
	
	/**
	*  Método que recebe a senha e faz a validação de acordo com os requisitos específicos.
	* 
	* @param string $senha Senha a ser validada.
	* @throws ServicoException.
	*/
	private function validarSenha($senha)
	{
		$tamSenha = mb_strlen($senha);

		if ($tamSenha < Usuario::TAMANHO_MINIMO_SENHA)
		{
			throw new ServicoException('A senha deve possuir no mínimo ' . Usuario::TAMANHO_MINIMO_SENHA . ' caracteres.');
		}
		if ($tamSenha > Usuario::TAMANHO_MAXIMO_SENHA)
		{
			throw new ServicoException('A senha deve possuir no máximo ' . Usuario::TAMANHO_MAXIMO_SENHA . ' caracteres.');
		}
	}

	/**
	* Método que recebe a identificação e retorna verdadeiro caso a mesma seja um e-mail.
	* 
	* @param string $login E-mail ou Login a ser verificada.
	* @return true.
	*/
	private function ehEmail($login)
	{
		return mb_strstr($login, '@' ) !== false;
	}

	/**
	* Método que recebe a identificação (SIAPE ou e-mail)  e senha e busca um Usuario 
	* ativo, caso o não econtra-lo é lançada uma ServicoException.
	* 
	* @param string $login SIAPE/E-mail a ser procurada.
	* @param string $senha Senha a ser procurada.
	* @return $usuario  Caso encontrado, retorna um Objeto Usuario.
	* @throws ServicoException.
	*/
	private function comloginESenha($login, $senha)
	{

		$ehEmail = $this->ehEmail($login);

		if ($ehEmail)
		{
			$this->validarEmail($login);
		}
		else 
		{
			$this->validarLogin($login);
		}
	
		$this->validarSenha($senha);
	
		$usuario = ($ehEmail ) ? $this->colecaoUsuario->comEmailESenha($login, $senha ) : $this->colecaoUsuario->comLoginESenha($login, $senha);
	
		if (null === $usuario)
		{
			throw new ServicoException('Identificação ou senha inválidos ou você ainda não foi ativado.');
		}
	
		return $usuario;
	}

	/**
	*  Método que recebe a identificação (login ou e-mail)  e senha . Caso o usuário(Usuario) 
	* for encontrado o mesmo irá logar no sistema.
	*
	* @param string $login SIAPE/E-mail a ser procurada.
	* @param string $senha Senha a ser procurada.
	*/
	function logar($login, $senha)
	{
		$usuario = $this->comloginESenha($login, $senha);
		$this->sessao->set('usuario', ['id' => $usuario->getId(), 'nome' => $usuario->getNome()]);
	}
	
	/**
	* 	Método logout de usuário(Usuario).
	*/
	function sair()
	{
		$this->sessao->destroy();
	}

	/**
	*  Método que recebe as senhas atual, nova e de confirmacao, e caso as senhas nova e
	* de confirmacao sejam iguais e a senha atual esteja correta, utiliza o método
	* atualizarSenha da colecao para substituir a senha atual pela nova.
	* @param string $atual senha atual
	* @param string $nova senha nova
	* @param string $confirmacao senha confirmacao
	* @throws ServicoException.
	*/
	function atualizarSenha($atual, $nova, $confirmacao)
	{
		try
		{
			$this->validarSenha($atual);
			$this->validarSenha($nova);
			$this->validarSenha($confirmacao);

			if(!$this->saoSenhasIguais($nova, $confirmacao))
			{
				throw new ServicoException("A senha nova e a senha de confirmação são diferentes!");
			}

			if(!$this->colecaoUsuario->senhaAtualEstaCorreta($this->sessao->get('id'), $atual))
			{
				throw new ServicoException("A senha atual está incorreta!");
			}

			$this->colecaoUsuario->atualizarSenha($this->sessao->get('id'), $nova);
		}
		catch(\Exception $e)
		{
			throw new ServicoException($e->getMessage(), $e->getCode(), $e);
		}
	}

	/**
	*  Método que recebe duas senhas e verifica se elas são iguais.
	* @param string $senha1 senha
	* @param string $senha2 senha
	* @return boolean
	* @throws ServicoException.
	*/
	function saoSenhasIguais($senha1, $senha2)
	{
		return $senha1 === $senha2;
	}

	function validar(Usuario $obj)
	{
		if(!is_string($obj->getNome())) 
		{
			throw new ColecaoException('Por favor, informe um valor válido para o nome  do usuário.');
		}
		
		$tamNome = mb_strlen($obj->getNome());

		if($tamNome >= $obj->getTamanhoMinimoNome())
		{
			throw new ColecaoException('O nome deve possuir pelo menos ' . $obj->getTamanhoMinimoNome() . ' caracteres.');
		}

		if($tamNome <= $obj->getTamanhoMaximoNome())
		{
			throw new ColecaoException('O nome deve possuir no máximo ' .  $obj->getTamanhoMaximoNome() . ' caracteres.');
		}

		// verifica se é email.
		if(! filter_var($obj->getEmail(), FILTER_VALIDATE_EMAIL) and $this->validarFormatoEmail($obj->getEmail()))
		{
			throw new ColecaoException('Por favor, informe o email.');
		}

		$tamEmail = mb_strlen($obj->getEmail());
		
		if($tamEmail >= $obj->getTamanhoMinimoEmail())
		{
			throw new ColecaoException('O email deve possuir pelo menos ' . $obj->getTamanhoMinimoEmail(). ' caracteres.');
		}

		if($tamEmail <= $obj->getTamanhoMaximoEmail())
		{
			throw new ColecaoException('O email deve possuir no máximo ' . $obj->getTamanhoMaximoEmail() . ' caracteres.');
		}		

		if($this->validarFormatoLogin($obj->getLogin()))
		{
			throw new ColecaoException('Por favor, informe um login válido.');
		}

		$tamEmail = mb_strlen($obj->getLogin());
		
		if($tamEmail >= $obj->getTamanhoMinimoLogin())
		{
			throw new ColecaoException('O login deve possuir pelo menos ' . $obj->getTamanhoMinimoLogin(). ' caracteres.');
		}

		if($tamLogin <= $obj->getTamanhoMaximoLogin())
		{
			throw new ColecaoException('O login deve possuir no máximo ' . $obj->getTamanhoMaximoLogin() . ' caracteres.');
		}

		$tamSenha = mb_strlen($obj->getSenha());

		if($tamSenha >= $obj->getTamanhoMinimoSenha())
		{
			throw new ColecaoException('A senha deve possuir pelo menos ' . $obj->getTamanhoMinimoSenha() . ' caracteres.');
		}

		if($tamSenha <= $obj->getTamanhoMaximoSenha())
		{
			throw new ColecaoException('A senha deve possuir no máximo ' . $obj->getTamanhoMaximoSenha() . ' caracteres.');
		}
		//verifica se já existe um email com o mesmo valor no banco de dados.
		$sql = 'SELECT  email FROM ' . self::TABELA . ' WHERE email = :email';
		
		$email = $this->pdoW->run($sql, ['email' => $obj->getEmail()]);
		
		if($email > 0)
		{
			throw new ColecaoException('O email  ' . $obj->getEmail() . ' já está cadastrado.');
		}			

		//verifica se já existe um login com o mesmo valor no banco de dados.
		$sql = 'SELECT  login FROM ' . self::TABELA . ' WHERE login = :login';
		
		$login = $this->pdoW->run($sql, ['login' => $obj->getLogin()]);
		
		if($login > 0)
		{
			throw new ColecaoException('O login  ' . $obj->getLogin() . ' já está cadastrado.');
		}			
	}

	function validarFormatoEmail($email)
	{
		$conta = "^[a-zA-Z0-9\._-]+@";
		$domino = "[a-zA-Z0-9\._-]+.";
		$extensao = "([a-zA-Z]{2,4})$";

		$pattern = $conta.$domino.$extensao;
		
		if (ereg($pattern, $email))
		{
			return true;
		}
		else
		{
			return false;
		}
	}

	function validarFormatoLogin($email)
	{
		$conta = "^[a-zA-Z0-9\._-]+@";
		$domino = "[a-zA-Z0-9\._-]+.";
		$extensao = "([a-zA-Z]{2,4})$";

		$pattern = $conta.$domino.$extensao;
		
		if (ereg($pattern, $email))
		{
			return true;
		}
		else
		{
			return false;
		}
	}

	function validarFormatoSenha()
	{

		if (preg_match("/^.*(?=.{8,})(?=.*\d)(?=.*[a-z])(?=.*[A-Z]).*$/", $senha)) 
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

		{
			return true;
		}
		else
		{
			return false;<?php

use phputil\Session;

/**
* Serviço de Login
*
* @author	Rafael Vinicius barros ferreira
*/

class ServicoUsuario {
	
	private $sessao;
	private $colecao;
	
	function __construct(ColecaoUsuario $colecao, Session $sessao)
	{
		$this->colecao = $colecao;
		$this->sessao = $sessao;
	}
	
	/**
	*  Método que recebe a identificação (E-mail) e faz a validação de acordo 
	* com os requisitos específicos.
	* 
	* @param string $login E-mail a ser validada.
	* @throws ServicoException.
	*/
	private function validarEmail($login)
	{
		if (! filter_var($login, FILTER_VALIDATE_EMAIL))
		{
			throw new ServicoException('Por favor, informe um e-mail válido.');
		}
		
		$tamEmail = mb_strlen($login);

		if ($tamEmail < Usuario::TAMANHO_MINIMO_EMAIL)
		{
			throw new ServicoException('O e-mail deve possuir no mímino ' . Usuario::TAMANHO_MINIMO_EMAIL . ' caracteres.');
		}
		
		if ($tamEmail > Usuario::TAMANHO_MAXIMO_EMAIL)
		{
			throw new ServicoException('O e-mail deve possuir no máximo ' . Usuario::TAMANHO_MAXIMO_EMAIL . ' caracteres.');
		}
	}

	/**
	*  Método que recebe a identificação (E-mail) e faz a validação de acordo 
	* com os requisitos específicos.
	* 
	* @param string $login E-mail a ser validada.
	* @throws ServicoException.
	*/
	private function validarLogin($login)
	{
		$tamEmail = mb_strlen($login);

		if ($tamEmail < Usuario::TAMANHO_MINIMO_LOGIN)
		{
			throw new ServicoException('O login deve possuir no mímino ' . Usuario::TAMANHO_MINIMO_LOGIN . ' caracteres.');
		}
		
		if ($tamEmail > Usuario::TAMANHO_MAXIMO_LOGIN)
		{
			throw new ServicoException('O login deve possuir no máximo ' . Usuario::TAMANHO_MAXIMO_LOGIN . ' caracteres.');
		}
	}
	
	/**
	*  Método que recebe a senha e faz a validação de acordo com os requisitos específicos.
	* 
	* @param string $senha Senha a ser validada.
	* @throws ServicoException.
	*/
	private function validarSenha($senha)
	{
		$tamSenha = mb_strlen($senha);

		if ($tamSenha < Usuario::TAMANHO_MINIMO_SENHA)
		{
			throw new ServicoException('A senha deve possuir no mínimo ' . Usuario::TAMANHO_MINIMO_SENHA . ' caracteres.');
		}
		if ($tamSenha > Usuario::TAMANHO_MAXIMO_SENHA)
		{
			throw new ServicoException('A senha deve possuir no máximo ' . Usuario::TAMANHO_MAXIMO_SENHA . ' caracteres.');
		}
	}

	/**
	* Método que recebe a identificação e retorna verdadeiro caso a mesma seja um e-mail.
	* 
	* @param string $login E-mail ou Login a ser verificada.
	* @return true.
	*/
	private function ehEmail($login)
	{
		return mb_strstr($login, '@' ) !== false;
	}

	/**
	* Método que recebe a identificação (SIAPE ou e-mail)  e senha e busca um Usuario 
	* ativo, caso o não econtra-lo é lançada uma ServicoException.
	* 
	* @param string $login SIAPE/E-mail a ser procurada.
	* @param string $senha Senha a ser procurada.
	* @return $usuario  Caso encontrado, retorna um Objeto Usuario.
	* @throws ServicoException.
	*/
	private function comloginESenha($login, $senha)
	{

		$ehEmail = $this->ehEmail($login);

		if ($ehEmail)
		{
			$this->validarEmail($login);
		}
		else 
		{
			$this->validarLogin($login);
		}
	
		$this->validarSenha($senha);
	
		$usuario = ($ehEmail ) ? $this->colecaoUsuario->comEmailESenha($login, $senha ) : $this->colecaoUsuario->comLoginESenha($login, $senha);
	
		if (null === $usuario)
		{
			throw new ServicoException('Identificação ou senha inválidos ou você ainda não foi ativado.');
		}
	
		return $usuario;
	}

	/**
	*  Método que recebe a identificação (login ou e-mail)  e senha . Caso o usuário(Usuario) 
	* for encontrado o mesmo irá logar no sistema.
	*
	* @param string $login SIAPE/E-mail a ser procurada.
	* @param string $senha Senha a ser procurada.
	*/
	function logar($login, $senha)
	{
		$usuario = $this->comloginESenha($login, $senha);
		$this->sessao->set('usuario', ['id' => $usuario->getId(), 'nome' => $usuario->getNome()]);
	}
	
	/**
	* 	Método logout de usuário(Usuario).
	*/
	function sair()
	{
		$this->sessao->destroy();
	}

	/**
	*  Método que recebe as senhas atual, nova e de confirmacao, e caso as senhas nova e
	* de confirmacao sejam iguais e a senha atual esteja correta, utiliza o método
	* atualizarSenha da colecao para substituir a senha atual pela nova.
	* @param string $atual senha atual
	* @param string $nova senha nova
	* @param string $confirmacao senha confirmacao
	* @throws ServicoException.
	*/
	function atualizarSenha($atual, $nova, $confirmacao) {
		try
		{
			$this->validarSenha($atual);
			$this->validarSenha($nova);
			$this->validarSenha($confirmacao);

			if(!$this->saoSenhasIguais($nova, $confirmacao))
			{
				throw new ServicoException("A senha nova e a senha de confirmação são diferentes!");
			}

			if(!$this->colecaoUsuario->senhaAtualEstaCorreta($this->sessao->get('id'), $atual))
			{
				throw new ServicoException("A senha atual está incorreta!");
			}

			$this->colecaoUsuario->atualizarSenha($this->sessao->get('id'), $nova);
		}
		catch(\Exception $e)
		{
			throw new ServicoException($e->getMessage(), $e->getCode(), $e);
		}
	}

	/**
	*  Método que recebe duas senhas e verifica se elas são iguais.
	* @param string $senha1 senha
	* @param string $senha2 senha
	* @return boolean
	* @throws ServicoException.
	*/
	function saoSenhasIguais($senha1, $senha2)
	{
		return $senha1 === $senha2;
	}

	function validar(Usuario $obj)
	{
		if(!is_string($obj->getNome())) {
			throw new ColecaoException('Por favor, informe um valor válido para o nome  do usuário.');
		}
		
		$tamNome = mb_strlen($obj->getNome());

		if($tamNome >= $obj->getTamanhoMinimoNome()) {
			throw new ColecaoException('O nome deve possuir pelo menos ' . $obj->getTamanhoMinimoNome() . ' caracteres.');
		}

		if($tamNome <= $obj->getTamanhoMaximoNome()) {
			throw new ColecaoException('O nome deve possuir no máximo ' .  $obj->getTamanhoMaximoNome() . ' caracteres.');
		}

		// verifica se é email.
		if(! filter_var($obj->getEmail(), FILTER_VALIDATE_EMAIL) and $this->validarFormatoEmail($obj->getEmail())) {
			throw new ColecaoException('Por favor, informe o email.');
		}

		$tamEmail = mb_strlen($obj->getEmail());
		
		if($tamEmail >= $obj->getTamanhoMinimoEmail()) {
			throw new ColecaoException('O email deve possuir pelo menos ' . $obj->getTamanhoMinimoEmail(). ' caracteres.');
		}

		if($tamEmail <= $obj->getTamanhoMaximoEmail()) {
			throw new ColecaoException('O email deve possuir no máximo ' . $obj->getTamanhoMaximoEmail() . ' caracteres.');
		}		

		if($this->validarFormatoLogin($obj->getLogin()))
		{
			throw new ColecaoException('Por favor, informe um login válido.');
		}

		$tamEmail = mb_strlen($obj->getLogin());
		
		if($tamEmail >= $obj->getTamanhoMinimoLogin()) {
			throw new ColecaoException('O login deve possuir pelo menos ' . $obj->getTamanhoMinimoLogin(). ' caracteres.');
		}

        if($tamLogin <= $obj->getTamanhoMaximoLogin()) {
			throw new ColecaoException('O login deve possuir no máximo ' . $obj->getTamanhoMaximoLogin() . ' caracteres.');
		}

		$tamSenha = mb_strlen($obj->getSenha());

		if($tamSenha >= $obj->getTamanhoMinimoSenha()) {
			throw new ColecaoException('A senha deve possuir pelo menos ' . $obj->getTamanhoMinimoSenha() . ' caracteres.');
		}

		if($tamSenha <= $obj->getTamanhoMaximoSenha()) {
			throw new ColecaoException('A senha deve possuir no máximo ' . $obj->getTamanhoMaximoSenha() . ' caracteres.');
		}
		//verifica se já existe um email com o mesmo valor no banco de dados.
		$sql = 'SELECT  email FROM ' . self::TABELA . ' WHERE email = :email';
		
		$email = $this->pdoW->run($sql, ['email' => $obj->getEmail()]);
		
		if($email > 0) {
			throw new ColecaoException('O email  ' . $obj->getEmail() . ' já está cadastrado.');
		}			

		//verifica se já existe um login com o mesmo valor no banco de dados.
		$sql = 'SELECT  login FROM ' . self::TABELA . ' WHERE login = :login';
		
		$login = $this->pdoW->run($sql, ['login' => $obj->getLogin()]);
		
		if($login > 0) {
			throw new ColecaoException('O login  ' . $obj->getLogin() . ' já está cadastrado.');
		}			
	}

	function validarFormatoEmail($email)
	{
		$conta = "^[a-zA-Z0-9\._-]+@";
		$domino = "[a-zA-Z0-9\._-]+.";
		$extensao = "([a-zA-Z]{2,4})$";

		$pattern = $conta.$domino.$extensao;
		
		if (ereg($pattern, $email)) {
			return true;
		}
		else {
			return false;
		}
	}

	function validarFormatoLogin($email) {
		$conta = "^[a-zA-Z0-9\._-]+@";
		$domino = "[a-zA-Z0-9\._-]+.";
		$extensao = "([a-zA-Z]{2,4})$";

		$pattern = $conta.$domino.$extensao;
		
		if (ereg($pattern, $email)) return true;
        else return false;
	}

	function validarFormatoSenha() {
		if (preg_match("/^.*(?=.{8,})(?=.*\d)(?=.*[a-z])(?=.*[A-Z]).*$/", $senha)) return true; 
		else  return false;
	}
}

?>

		}
	}

	function validarFormatoSenha()
	{

		if (preg_match("/^.*(?=.{8,})(?=.*\d)(?=.*[a-z])(?=.*[A-Z]).*$/", $senha)) 
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
