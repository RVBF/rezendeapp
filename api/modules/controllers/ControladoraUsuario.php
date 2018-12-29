<?php
use phputil\datatables\DataTablesRequest;
use phputil\datatables\DataTablesResponse;
use Symfony\Component\Validator\Validation as Validacao;
use \phputil\JSON;
use \phputil\RTTI;
use Illuminate\Database\Capsule\Manager as DB;

/**
 * Controladora de Usuario
 *
 * @author	Rafael Vinicius Barros Ferreira
 * @version	0.1
 */
class ControladoraUsuario {

	private $params;
	private $colecaoUsuario;
	private $colecaoLoja;
	private $servicologin;
	private $colecaoColaborador;
	private $colecaoGrupoDeUsuario;

	private $session;

	
	function __construct($params, Sessao $sessao) {
		$this->params = $params;
		$this->colecaoUsuario = Dice::instance()->create('ColecaoUsuario');
		$this->colecaoGrupoDeUsuario = Dice::instance()->create('ColecaoGrupoUsuario');
		$this->colecaoLoja = Dice::instance()->create('ColecaoLoja');
		$this->colecaoColaborador = Dice::instance()->create('ColecaoColaborador');
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

			$objetos = $this->colecaoUsuario->todos($dtr->start, $dtr->length);
	
			foreach ($objetos as $key => $obj) {
				$colaborador = $this->colecaoColaborador->comUsuarioId($obj->getId());
				if(!isset($colaborador) and !($colaborador instanceof Colaborador)){
					throw new Exception("Colaborador não encontrada na base de dados.");
				}

				$objetos[$key]->setColaborador($colaborador);
			}

			$contagem = $this->colecaoUsuario->contagem();
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

			$inexistentes = \ArrayUtil::nonExistingKeys(['id', 'nome', 'sobrenome', 'email', 'login','senha', 'lojas'], $this->params);
			if(count($inexistentes) > 0) {
				$msg = 'Os seguintes campos obrigatórios não foram enviados: ' . implode(', ', $inexistentes);

				throw new Exception($msg);
			}			
			

			$lojas = $this->colecaoLoja->todosComIds($this->params['lojas']);

			if(!isset($lojas) and !($lojas instanceof Loja)){
				throw new Exception("Loja não encontrada na base de dados.");
			}

			$hash = HashSenha::instance();
			$usuario = new Usuario( 
				0, 
				\ParamUtil::value($this->params, 'login'), 
				$hash->gerarHashDeSenhaComSaltEmMD5(\ParamUtil::value($this->params, 'senha'))
			);


			$this->colecaoUsuario->adicionar($usuario);

			$colaborador = new Colaborador(
				0, 
				\ParamUtil::value($this->params, 'nome'), 
				\ParamUtil::value($this->params, 'sobrenome'), 
				\ParamUtil::value($this->params, 'email'), 
				$usuario,
				$lojas
			);

			$this->colecaoColaborador->adicionar($colaborador);

			DB::commit();

			$resposta = ['status' => true, 'mensagem'=> 'Usuário cadastrado com sucesso.']; 
		}
		catch (\Exception $e) {
			DB::rollback();
			$resposta = ['status' => false, 'mensagem'=> $e->getMessage()]; 
		}

		return $resposta;
	}

	function atualizar(){
		DB::beginTransaction();

		try {
			if($this->servicoLogin->verificarSeUsuarioEstaLogado() == false) {
				throw new Exception("Erro ao acessar página.");				
			}

			$inexistentes = \ArrayUtil::nonExistingKeys(['id', 'nome', 'sobrenome', 'email', 'login'], $this->params);

			if(count($inexistentes) > 0) {
				$msg = 'Os seguintes campos obrigatórios não foram enviados: ' . implode(', ', $inexistentes);

				throw new Exception($msg);
			}		
			$lojas = null;

			if(isset($this->params['lojas'])) $lojas = $this->colecaoLoja->todosComIds($this->params['lojas']);

			if(!isset($lojas) and !($lojas instanceof Loja) and count($lojas)){
				throw new Exception("Loja não encontrada na base de dados.");
			}

			$hash = HashSenha::instance();
			$usuario = new Usuario( 
				\ParamUtil::value($this->params, 'id'), 
				\ParamUtil::value($this->params, 'login')
			);

			$usuario->setColaborador($this->colecaoColaborador->comUsuarioId($usuario->getId()));

			$this->colecaoUsuario->atualizar($usuario);

			$colaborador = $usuario->getColaborador();

			$colaborador->setNome(\ParamUtil::value($this->params, 'nome'));
			$colaborador->setSobrenome(\ParamUtil::value($this->params, 'sobrenome'));
			$colaborador->setEmail(\ParamUtil::value($this->params, 'email'));
			$colaborador->setLojas($lojas);

			$this->colecaoColaborador->atualizar($colaborador);

			DB::commit();

			$resposta = ['status' => true, 'mensagem'=> 'Usuário cadastrado com sucesso.']; 
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
			if($this->colecaoUsuario->remover($id)){
				throw new Exception("Erro ao remover usuário.");
			}
			
			DB::commit();

			$resposta = ['status' => true, 'mensagem'=> 'Usuário removido com sucesso.']; 
		}
		catch (\Exception $e) {
			DB::rollback();
			$resposta = ['status' => false, 'mensagem'=>  $e->getMessage()]; 
		}

		return $resposta;

	}

	function comId($id) {

		try {
			if($this->servicoLogin->verificarSeUsuarioEstaLogado() == false) {
				throw new Exception("Erro ao acessar página.");				
			}
			if (! is_numeric($id)) {
				$msg = 'O id informado não é numérico.';
				return $this->geradoraResposta->erro($msg, GeradoraResposta::TIPO_TEXTO);
			}

			$usuario = $this->colecaoUsuario->comId($id);
			$usuario->setGruposUsuario($this->colecaoGrupoDeUsuario->comUsuarioId($usuario->getId()));

			$resposta = ['conteudo'=> RTTI::getAttributes($usuario, RTTI::allFlags()), 'status' => true, 'mensagem'=> 'Usuário removido com sucesso.']; 
		}
		catch (\Exception $e) {
			DB::rollback();
			$resposta = ['status' => false, 'mensagem'=>  $e->getMessage()]; 
		}

		return $resposta;

	}
}
?>
				