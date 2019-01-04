<?php

/**
 * Controladora de login
 *
 * @author	Rafael Vinicicus Barros Ferreira
 */

class ControladoraLogin {
	private $params;
	private $servico;
	private $colecaoUsuario;
	private $sessao;

	function __construct($params, Sessao $sessao) {
		$this->params = $params;
		$this->sessao = $sessao;
		$this->colecaoUsuario = Dice::instance()->create('ColecaoUsuario');
		$this->servico = new ServicoLogin($this->sessao, $this->colecaoUsuario);
	}

	function logar() {
		try {
			$inexistentes = \ArrayUtil::nonExistingKeys([ 'login', 'senha' ], $this->params);

			if(count($inexistentes) > 0) {
				$msg = 'Os seguintes campos não foram enviados: ' . implode(', ', $inexistentes);
				throw new Exception($msg);
				
				// return $this->geradoraResposta->erro($msg, GeradoraResposta::TIPO_TEXTO);
			}
	
			$usuario = $this->servico->login(\ParamUtil::value($this->params, 'login'), \ParamUtil::value($this->params, 'senha'));

			$conteudo = ['id' => $usuario->getId(), 'nome'=> $usuario->getLogin()];

			$resposta = ['usuario'=> $conteudo, 'status' => true, 'mensagem'=> 'Logado Com sucesso.']; 
		}
		catch (\Exception $e) {
			$resposta = ['status' => false, 'mensagem'=> $e->getMessage()]; 
		}

		return $resposta;
	}
	
	/**
	*	Método de Logout  que utiliza o método sair do serviço.
	*
	* @throws Exception
	*/
	
	function sair() {
		try {
			if($this->servico->estaLogado()) $this->servico->logout();

			$resposta = ['status' => true, 'mensagem'=> 'Logout efetuado com sucesso.']; 

		} catch(\Exception $e) {
			$resposta = ['status' => false, 'mensagem'=> $e->getMessage()]; 
		}

		return $resposta;
	}
}
