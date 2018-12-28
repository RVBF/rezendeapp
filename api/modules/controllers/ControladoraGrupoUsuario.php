<?php
use phputil\datatables\DataTablesRequest;
use phputil\datatables\DataTablesResponse;
use Symfony\Component\Validator\Validation as Validacao;
use \phputil\JSON;
use \phputil\RTTI;
use Illuminate\Database\Capsule\Manager as DB;

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
	private $servicoLogin;
	private $colecaoUsuario;
	
	function __construct($params, Sessao $sessao) {
		$this->params = $params;
		$this->colecaoGrupoUsuario = Dice::instance()->create('ColecaoGrupoUsuario');
		$this->colecaoUsuario = Dice::instance()->create('ColecaoUsuario');
		$this->servicoLogin = new ServicoLogin($sessao);
		$this->sessao = $sessao;
	}

	function todos() {
		try {
			if($this->servicoLogin->verificarSeUsuarioEstaLogado() == false) {
				throw new Exception("Erro ao acessar página.");				
			}

			if(!$this->servicoLogin->eAdministrador()){
				throw new Exception("Usuário sem permissão para executar ação.");
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
		DB::beginTransaction();

		try {
			if($this->servicoLogin->verificarSeUsuarioEstaLogado() == false) {
				throw new Exception("Erro ao acessar página.");				
			}

			if(!$this->servicoLogin->eAdministrador()){
				throw new Exception("Usuário sem permissão para executar ação.");
			}

			$inexistentes = \ArrayUtil::nonExistingKeys(['id', 'nome','descricao', 'usuarios'], $this->params);
		
			$usuarios = $this->colecaoUsuario->todosComIds($this->params['usuarios']);
			if(!isset($usuarios) and !($usuarios instanceof Usuario)){
				throw new Exception("Usuários não encontrados na base de dados.");
			}

			if(count($inexistentes) > 0) {
				$msg = 'Os seguintes campos obrigatórios não foram enviados: ' . implode(', ', $inexistentes);

				throw new Exception($msg);
			}

			$grupoUsuario = new GrupoUsuario( 0, \ParamUtil::value($this->params, 'nome'), \ParamUtil::value($this->params, 'descricao'), $usuarios);

			$resposta = ['grupoUsuario'=> RTTI::getAttributes($this->colecaoGrupoUsuario->adicionar($grupoUsuario), RTTI::allFlags()), 'status' => true, 'mensagem'=> 'Grupo de usuário cadastrado com sucesso.']; 
			DB::commit();
		}
		catch (\Exception $e) {
			DB::rollback();

			$resposta = ['status' => false, 'mensagem'=> $e->getMessage()]; 
		}

		return $resposta;
	}

    function atualizar() {
		DB::beginTransaction();

		try {
			if($this->servicoLogin->verificarSeUsuarioEstaLogado() == false) {
				throw new Exception("Erro ao acessar página.");				
			}

			if(!$this->servicoLogin->eAdministrador()){
				throw new Exception("Usuário sem permissão para executar ação.");
			}

			$inexistentes = \ArrayUtil::nonExistingKeys(['id', 'nome','descricao'], $this->params);

			if(count($inexistentes) > 0) {
				$msg = 'Os seguintes campos obrigatórios não foram enviados: ' . implode(', ', $inexistentes);

				throw new Exception($msg);
			}

			$usuarios = $this->colecaoUsuario->todosComIds($this->params['usuarios']);
			if(!isset($usuarios) and !($usuarios instanceof Usuario)){
				throw new Exception("Usuários não encontrados na base de dados.");
			}

			$grupoUsuario = new GrupoUsuario( \ParamUtil::value($this->params, 'id'), \ParamUtil::value($this->params, 'nome'), \ParamUtil::value($this->params, 'descricao'), $usuarios);

			$resposta = ['grupoUsuario'=> RTTI::getAttributes($this->colecaoGrupoUsuario->atualizar($grupoUsuario), RTTI::allFlags()), 'status' => true, 'mensagem'=> 'Grupo de usuário cadastrado com sucesso.']; 
			
			DB::commit();
		}
		catch (\Exception $e) {
			DB::rollback();

			$resposta = ['status' => false, 'mensagem'=> $e->getMessage()]; 
		}

		return $resposta;
	}

	function remover($id) {
		DB::beginTransaction();

		try {
			if($this->servicoLogin->verificarSeUsuarioEstaLogado() == false) {
				throw new Exception("Erro ao acessar página.");				
			}

			if(!$this->servicoLogin->eAdministrador()){
				throw new Exception("Usuário sem permissão para executar ação.");
			}

			if (! is_numeric($id)) {
				$msg = 'O id informado não é numérico.';
				return $this->geradoraResposta->erro($msg, GeradoraResposta::TIPO_TEXTO);
			}

			$this->colecaoGrupoUsuario->remover($id);

			DB::commit();

			$resposta = ['status' => true, 'mensagem'=> 'Grupo de usuário removido com sucesso.']; 
		}
		catch (\Exception $e) {
			DB::rollback();

			$resposta = ['status' => false, 'mensagem'=> $e->getMessage()]; 
		}
		
		return $resposta;
	}
}
?>