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
	private $colecaoColaborador;
	private $sessao;

	function __construct($params, Sessao $sessao) {
		$this->params = $params;
		$this->sessao = $sessao;
		$this->colecaoUsuario = Dice::instance()->create('ColecaoUsuario');
		$this->colecaoColaborador= Dice::instance()->create('ColecaoColaborador');
		$this->servico = new ServicoLogin($this->sessao, $this->colecaoUsuario);
	}

	function logar() {
		try {
			$inexistentes = \ArrayUtil::nonExistingKeys([ 'login', 'senha' ], $this->params);

			if(is_array($inexistentes) ? count($inexistentes) > 0 : false) {
				$msg = 'Os seguintes campos não foram enviados: ' . implode(', ', $inexistentes);
				throw new Exception($msg);
			}
	
			$usuario = $this->servico->login(\ParamUtil::value($this->params, 'login'), \ParamUtil::value($this->params, 'senha'));
			$colaborador = new Colaborador();
			$colaborador->fromArray($this->colecaoColaborador->comUsuarioId($usuario->getId()));
			$setor = new Setor();
			$setor->fromArray($colaborador->getSetor());
			$conteudo = is_a($usuario, 'Usuario') ? ['id' => $usuario->getId(), 'login'=> $usuario->getLogin(), 'admin' => $usuario->getAdministrador(), 'nome' => $colaborador->getNome() . ' ' . $colaborador->getSobrenome(), 'setor' => $colaborador->getSetor()] : [];
			$resposta = ['usuario'=> $conteudo, 'status' => count($conteudo) ? true : false, 'mensagem'=> count($conteudo) ? 'Logado Com sucesso.' : 'Erro ao logar.' ]; 
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
