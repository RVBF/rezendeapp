<?php
use phputil\datatables\DataTablesRequest;
use phputil\datatables\DataTablesResponse;
use Symfony\Component\Validator\Validation as Validacao;
use \phputil\JSON;
use \phputil\RTTI;
/**
 * Controladora de Grupo de Usuario
 *
 * @author	Rafael Vinicius Barros Ferreira
 * @version	0.1
 */
class ControladoraGrupoUsuario {

	private $params;
	private $colecaoGrupoUsuario;
	private $session;

	
	function __construct($params, Sessao $sessao) {
		$this->params = $params;
		$this->colecaoGrupoUsuario = Dice::instance()->create('ColecaoGrupoUsuario');
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

			$objetos = $this->colecaoGrupoUsuario->todos($dtr->start, $dtr->length);
			$contagem = $this->colecaoGrupoUsuario->contagem();
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
			$resposta = ['checklist'=> RTTI::getAttributes($this->colecaoGrupoUsuario->adicionar($usuario), RTTI::allFlags()), 'status' => true, 'mensagem'=> 'Usuário cadastrado com sucesso.']; 
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
			$resposta = ['checklist'=> RTTI::getAttributes($this->colecaoGrupoUsuario->atualizar($usuario), RTTI::allFlags()), 'status' => true, 'mensagem'=> 'Usuário atualizado com sucesso.']; 
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

			$this->colecaoGrupoUsuario->remover($id);

			$resposta = ['status' => true, 'mensagem'=> 'Usuário removido com sucesso.']; 

			return $this->geradoraResposta->semConteudo();
		}
		catch (\Exception $e) {
			$resposta = ['status' => true, 'mensagem'=> 'Usuário removido com sucesso.']; 
		}
	}
}
?>