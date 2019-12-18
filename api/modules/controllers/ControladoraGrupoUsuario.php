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
 * @version	1.0
 */
class ControladoraGrupoUsuario {

	private $params;
	private $colecaoGrupoUsuario;
	private $servicoLogin;
	private $colecaoUsuario;
	
	function __construct($params, Sessao $sessao) {
		$this->params = $params;
		$this->colecaoGrupoUsuario = Dice::instance()->create('ColecaoGrupoUsuario');
		$this->colecaoUsuario = Dice::instance()->create('ColecaoUsuario');
		$this->servicoLogin = new ServicoLogin($sessao);
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

			$objetos = $this->colecaoGrupoUsuario->todos($dtr->start, $dtr->length, (isset($dtr->search->value)) ? $dtr->search->value : '');	
			// Util::printr($objetos);
			$contagem = $this->colecaoGrupoUsuario->contagem();
		}
		catch (\Exception $e )
		{
			Util::printr($e->getMessage());
			throw new Exception("Erro ao listar colaboradores.");
		}
		$conteudo = new DataTablesResponse(
			$contagem,
			count($objetos), //count($objetos ),
			$objetos,
			$dtr->draw,
			$erro
		);

		return  RTTI::getAttributes($conteudo, RTTI::allFlags());
    }
    
    function adicionar() {
		try {
			DB::beginTransaction();

			if($this->servicoLogin->verificarSeUsuarioEstaLogado() == false) throw new Exception("Erro ao acessar página.");				

			$inexistentes = \ArrayUtil::nonExistingKeys(['id', 'nome','descricao', 'usuarios'], $this->params);
		
			$usuarios = $this->colecaoUsuario->todosComIds($this->params['usuarios']);
			if(!isset($usuarios) and !($usuarios instanceof Usuario)){
				throw new Exception("Usuários não encontrados na base de dados.");
			}

			if(is_array($inexistentes) ? count($inexistentes) > 0 : 0) {
				$msg = 'Os seguintes campos obrigatórios não foram enviados: ' . implode(', ', $inexistentes);

				throw new Exception($msg);
			}

			$grupoUsuario = new GrupoUsuario( 0, 
				\ParamUtil::value($this->params, 'nome'), 
				\ParamUtil::value($this->params, 'descricao'), 
				$usuarios
			);
			$this->colecaoGrupoUsuario->adicionar($grupoUsuario);

			$resposta = ['status' => true, 'mensagem'=> 'Grupo de usuário cadastrado com sucesso.']; 
			
			DB::commit();
		}
		catch (\Exception $e) {
			DB::rollback();

			$resposta = ['status' => false, 'mensagem'=> $e->getMessage()]; 
		}

		return $resposta;
	}

    function atualizar() {
		try {
			DB::beginTransaction();

			if($this->servicoLogin->verificarSeUsuarioEstaLogado() == false) throw new Exception("Erro ao acessar página.");				
			$inexistentes = \ArrayUtil::nonExistingKeys(['id', 'nome','descricao'], $this->params);

			if(is_array($inexistentes) ? count($inexistentes) > 0 : 0) throw new Exception('Os seguintes campos obrigatórios não foram enviados: ' . implode(', ', $inexistentes));

			$usuarios = (isset($this->params['usuarios'])) ? $this->colecaoUsuario->todosComIds($this->params['usuarios']) : [];
			$grupoDeUsuario = new GrupoUsuario(); $grupoDeUsuario->fromArray($this->colecaoGrupoUsuario->comId(\ParamUtil::value($this->params, 'id')));
			if(!($grupoDeUsuario instanceof GrupoUsuario)) throw new Exception("Grupo de usuário não encontrado na base de dados!");
			
			$grupoDeUsuario->setNome(\ParamUtil::value($this->params, 'nome'));
			$grupoDeUsuario->setDescricao(\ParamUtil::value($this->params, 'descricao'));
			$grupoDeUsuario->setUsuarios($usuarios);
			$this->colecaoGrupoUsuario->atualizar($grupoDeUsuario);

			$resposta = ['status' => true, 'mensagem'=> 'Grupo de usuário cadastrado com sucesso.']; 
			
			DB::commit();
		}
		catch (\Exception $e) {
			DB::rollback();

			$resposta = ['status' => false, 'mensagem'=> $e->getMessage()]; 
		}

		return $resposta;
	}

	function remover($id) {
		try {
			DB::beginTransaction();
			
			if($this->servicoLogin->verificarSeUsuarioEstaLogado() == false) throw new Exception("Erro ao acessar página.");

			if(!$this->servicoLogin->eAdministrador())	throw new Exception("Usuário sem permissão para executar ação.");

			if (!is_numeric($id)) return $this->geradoraResposta->erro('O id informado não é numérico.', GeradoraResposta::TIPO_TEXTO);

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

	function comId($id) {
		try {
			if($this->servicoLogin->verificarSeUsuarioEstaLogado() == false) throw new Exception("Erro ao acessar página.");				
			
			if (! is_numeric($id)) return $this->geradoraResposta->erro('O id informado não é numérico.', GeradoraResposta::TIPO_TEXTO);

			$grupoUsuario = new GrupoUsuario(); $grupoUsuario->fromArray($this->colecaoGrupoUsuario->comId($id));
		
			$resposta = ['conteudo'=> $grupoUsuario->toArray(), 'status' => true]; 
		}
		catch (\Exception $e) {
			DB::rollback();
			$resposta = ['status' => false, 'mensagem'=>  $e->getMessage()]; 
		}

		return $resposta;
	}
}
?>