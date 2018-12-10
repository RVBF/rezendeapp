<?php
use phputil\datatables\DataTablesRequest;
use phputil\datatables\DataTablesResponse;
use Symfony\Component\Validator\Validation as Validacao;
use \phputil\JSON;
use \phputil\RTTI;
/**
 * Controladora de Usuario
 *
 * @author	Rafael Vinicius Barros Ferreira
 * @version	0.1
 */
class ControladoraUsuario {

	private $params;
	private $colecaoUsuario;
	private $servicologin;
	private $session;

	
	function __construct($params, Sessao $sessao) {
		$this->params = $params;
		$this->colecaoUsuario = Dice::instance()->create('ColecaoUsuario');
		$this->servicoLogin = new ServicoLogin($sessao);
		$this->sessao = $sessao;
	}

	function todos() {
		try {
			if($this->servicoLogin->verificarSeUsuarioEstaLogado() == false) {
				throw new Exception("Erro ao acessar página.");				
			}

			$dtr = new DataTablesRequest($this->params);
			$contagem = 0;
			$objetos = [];
			$erro = null;	

			$objetos = $this->colecaoUsuario->todos($dtr->start, $dtr->length);
			$contagem = $this->colecaoUsuario->contagem();
		}
		catch (\Exception $e ) {
			throw new Exception($e->getMessage());
		}

		$conteudo = new DataTablesResponse($contagem, $contagem, $objetos, $dtr->draw, $erro);

		return $conteudo;
    }
    
    function adicionar() {
		try {
			if($this->servicoLogin->verificarSeUsuarioEstaLogado() == false) {
				throw new Exception("Erro ao acessar página.");				
			}
			$inexistentes = \ArrayUtil::nonExistingKeys(['id', 'nome','login','senha'], $this->params);

			if(count($inexistentes) > 0) {
				$msg = 'Os seguintes campos obrigatórios não foram enviados: ' . implode(', ', $inexistentes);

				throw new Exception($msg);
			}

			$hash = HashSenha::instance();

			$usuario = new Usuario( 0, \ParamUtil::value($this->params, 'nome'), \ParamUtil::value($this->params, 'login'), $hash->gerarHashDeSenhaComSaltEmMD5(\ParamUtil::value($this->params, 'senha')));
			$resposta = ['setor'=> RTTI::getAttributes($this->colecaoUsuario->adicionar($usuario), RTTI::allFlags()), 'status' => true, 'mensagem'=> 'Usuário cadastrado com sucesso.']; 
		}
		catch (\Exception $e) {
			$resposta = ['status' => false, 'mensagem'=> $e->getMessage()]; 
		}

		return $resposta;
	}

	function atualizar() {
		try {
			if($this->servicoLogin->verificarSeUsuarioEstaLogado() == false) {
				throw new Exception("Erro ao acessar página.");				
			}
			$inexistentes = \ArrayUtil::nonExistingKeys(['id', 'nome','login','senha'], $this->params);
			
			if(count($inexistentes) > 0) {
				$msg = 'Os seguintes campos obrigatórios não foram enviados: ' . implode(', ', $inexistentes);

				throw new Exception($msg);
			}

			$hash = HashSenha::instance();

			$usuario = new Usuario( \ParamUtil::value($this->params, 'id'), \ParamUtil::value($this->params, 'nome'), \ParamUtil::value($this->params, 'login'), $hash->gerarHashDeSenhaComSaltEmMD5(\ParamUtil::value($this->params, 'senha')));
			$resposta = ['setor'=> RTTI::getAttributes($this->colecaoUsuario->atualizar($usuario), RTTI::allFlags()), 'status' => true, 'mensagem'=> 'Usuário atualizado com sucesso.']; 
		}
		catch (\Exception $e) {
			$resposta = ['status' => false, 'mensagem'=> $e->getMessage()]; 
		}

		return $resposta;
	}

	function remover($id) {
		try {
			if($this->servicoLogin->verificarSeUsuarioEstaLogado() == false) {
				throw new Exception("Erro ao acessar página.");				
			}
			if (! is_numeric($id)) {
				$msg = 'O id informado não é numérico.';
				return $this->geradoraResposta->erro($msg, GeradoraResposta::TIPO_TEXTO);
			}

			$this->colecaoUsuario->remover($id);

			$resposta = ['status' => true, 'mensagem'=> 'Usuário removido com sucesso.']; 

			return $this->geradoraResposta->semConteudo();
		}
		catch (\Exception $e) {
			$resposta = ['status' => true, 'mensagem'=> 'Usuário removido com sucesso.']; 
		}
	}
}
?>