<?php
use phputil\datatables\DataTablesRequest;
use phputil\datatables\DataTablesResponse;
use Symfony\Component\Validator\Validation as Validacao;
use \phputil\JSON;
use \phputil\RTTI;
use Carbon\Carbon;
use Illuminate\Database\Capsule\Manager as DB;


/**
 * Controladora de Checklist
 *
 * @author	Rafael Vinicius Barros Ferreira
 * @version	1.0
 */
class ControladoraChecklist {

	private $params;
	private $colecaoChecklist;
	private $colecaoSetor;
	private $servicoLogin;
	private $colecaoUsuario;
	private $colecaoColaborador;
	private $colecaoLoja;
	private $colecaoQuestionario;
	private $colecaoQuestionamento;

	function __construct($params,  Sessao $sessao) {
		$this->params = $params;
		$this->servicoLogin = new ServicoLogin($sessao);
		$this->colecaoChecklist = Dice::instance()->create('ColecaoChecklist');
		$this->colecaoSetor = Dice::instance()->create('ColecaoSetor');
		$this->colecaoUsuario = Dice::instance()->create('ColecaoUsuario');
		$this->colecaoColaborador = Dice::instance()->create('ColecaoColaborador');
		$this->colecaoLoja = Dice::instance()->create('ColecaoLoja');
		$this->colecaoQuestionario = Dice::instance()->create('ColecaoQuestionario');
		$this->colecaoQuestionamento = Dice::instance()->create('ColecaoQuestionamento');
	}

	function todos() {
		try {
			if($this->servicoLogin->verificarSeUsuarioEstaLogado() == false) {
				throw new Exception("Erro ao acessar página.");				
			}

			$colaborador = $this->colecaoColaborador->comUsuarioId($this->servicoLogin->getIdUsuario());

			$idsLojas = [];
			if($colaborador instanceof COlaborador) {
				foreach ($colaborador->getLojas() as $loja) {
					$idsLojas[] = $loja->getId();
				}
			}

			if(!isset($this->params['listagemTemporal'])){
				$dtr = new DataTablesRequest($this->params);
				$contagem = 0;
				$objetos = [];
				$erro = null;
	
				$objetos = $this->colecaoChecklist->todosComLojaIds($dtr->start, $dtr->length, (isset($dtr->search->value)) ? $dtr->search->value : '', $idsLojas);
	
				$contagem = $this->colecaoChecklist->contagem($idsLojas);
	
				$conteudo = new DataTablesResponse(
					$contagem,
					is_array($objetos) ? count($objetos) : 0, //count($objetos ),
					$objetos,
					$dtr->draw,
					$erro
				);
			}
			else{
				$objetos = $this->colecaoChecklist->listagemTemporalcomLojasIds($this->params['homePage'], $this->params['pageLength'], (isset($this->params['search'])) ? $this->params['search'] : '', $idsLojas);
	
				$contagem = $this->colecaoChecklist->contagem($idsLojas);
				
				$conteudo = new DataTablesResponse(
					$contagem,
					is_array($objetos) ? count($objetos) : 0, //count($objetos ),
					$objetos,
					0,
					null
				);
			}
		}
		catch (\Exception $e ) {
			throw new Exception("Erro ao listar tarefas");
		}

		
		
		return $conteudo;
	}
	
	function adicionar($setorId = 0) {
		DB::beginTransaction();
		try {
			$resposta = [];

			if($this->servicoLogin->verificarSeUsuarioEstaLogado() == false) {
				throw new Exception("Erro ao acessar página.");				
			}

			// if(!$this->servicoLogin->eAdministrador()){
			// 	throw new Exception("Usuário sem permissão para executar ação.");
			// }

			$inexistentes = \ArrayUtil::nonExistingKeys(['titulo', 'descricao', 'dataLimite', 'setor', 'loja', 'questionarios'], $this->params);

			if(is_array($inexistentes) ? count($inexistentes) > 0 : 0) {
				$msg = 'Os seguintes campos obrigatórios não foram enviados: ' . implode(', ', $inexistentes);
	
				throw new Exception($msg);
			}
	
			$setor = $this->colecaoSetor->comId(($setorId > 0) ? $setorId : \ParamUtil::value($this->params, 'setor'));

			if(!isset($setor) and !($setor instanceof Setor)){
				throw new Exception("Setor não encontrado na base de dados.");
			}

				
			$loja = $this->colecaoLoja->comId((\ParamUtil::value($this->params, 'loja')> 0) ? \ParamUtil::value($this->params, 'loja') : \ParamUtil::value($this->params, 'loja'));

			if(!isset($loja) and !($loja instanceof Loja)){
				throw new Exception("Loja não encontrada na base de dados.");
			}

			 $questionarios = $this->colecaoQuestionario->todosComId($this->params['questionarios']);

			if(!isset($questionarios) and count($questionarios) == count($this->params['questionarios'])){
				throw new Exception("Alguma opção de questionário seleciona não foi encontrado na base de dados.");
			}


			$responsavel = $this->colecaoColaborador->comId((\ParamUtil::value($this->params, 'responsavel')> 0) ? \ParamUtil::value($this->params, 'responsavel') : \ParamUtil::value($this->params, 'responsavel'));

			if(!isset($responsavel) and !($responsavel instanceof Colaborador)){
				throw new Exception("Responśavel não encontrado na base de dados.");
			}

			$questionador = $this->colecaoColaborador->comId((\ParamUtil::value($this->params, 'questionario')> 0) ? \ParamUtil::value($this->params, 'responsavel') : \ParamUtil::value($this->params, 'responsavel'));

			if(!isset($responsavel) and !($responsavel instanceof Colaborador)){
				throw new Exception("Responśavel não encontrado na base de dados.");
			}


			$dataLimite = new Carbon(\ParamUtil::value($this->params, 'dataLimite'), 'America/Sao_Paulo');

			$checklist = new Checklist(
				0,
				StatusChecklistEnumerado::AGUARDANDO_EXECUCAO,
				\ParamUtil::value($this->params, 'titulo'),
				\ParamUtil::value($this->params, 'descricao'),
				$dataLimite,
				'',
				\ParamUtil::value($this->params, 'tipoChecklist'),
				$setor,
				$loja,
				$questionador,
				$responsavel,
				$questionarios
			);			

			$this->colecaoChecklist->adicionar($checklist);

			$questionarios =$this->params['questionarios'];

			$questionamentos = [];
			foreach($questionarios as $questionarioId){
				$questionario = $this->colecaoQuestionario->comId($questionarioId);

				if(!isset($questionario) and !($questionario instanceof Colaborador)){
					throw new Exception('O Questionário selecionado de id nº ' . $questionarioId . ' não foi encontrado na base de dados.');
				}

				$formulario = json_decode($questionario->getFormulario());

				foreach ($formulario->perguntas as $pergunta) {
					$questionamentos[] = new Questionamento(
						0,
						TipoQuestionamentoEnumerado::NAO_RESPONDIDO,
						json_encode($pergunta),
						'',
						$checklist->getId(),
						null,
						[]
					);
				}
			}

			$this->colecaoQuestionamento->adicionarTodos($questionamentos);

			$resposta = ['status' => true, 'mensagem'=> 'Checklist cadastrado  com sucesso.']; 
			
			DB::commit();

		}
		catch (\Exception $e) {
			DB::rollback();

			$resposta = ['status' => false, 'mensagem'=> $e->getMessage()]; 
		}

		return $resposta;
	}

	function atualizar($setorId = 0) {
		DB::beginTransaction();

		try {
			if($this->servicoLogin->verificarSeUsuarioEstaLogado() == false) {
				throw new Exception("Erro ao acessar página.");				
			}

			if(!$this->servicoLogin->eAdministrador()){
				throw new Exception("Usuário sem permissão para executar ação.");
			}

			$inexistentes = \ArrayUtil::nonExistingKeys(['id', 'titulo', 'descricao', 'dataLimite', 'setor', 'loja'], $this->params);
		
			if(is_array($inexistentes) ? count($inexistentes) > 0 : 0) {
				$msg = 'Os seguintes campos obrigatórios não foram enviados: ' . implode(', ', $inexistentes);
	
				throw new Exception($msg);
			}

			$setor = $this->colecaoSetor->comId(\ParamUtil::value($this->params, 'setor'));

			if(!isset($setor) and !(setor instanceof Setor)){
				throw new Exception("Setor não encontrada na base de dados.");
			}

				
			$loja = $this->colecaoLoja->comId((\ParamUtil::value($this->params, 'loja')> 0) ? \ParamUtil::value($this->params, 'loja') : \ParamUtil::value($this->params, 'loja'));

			if(!isset($loja) and !($loja instanceof Loja)){
				throw new Exception("Loja não encontrada na base de dados.");
			}

			$dataLimite = new Carbon();                  // equivalent to Carbon::now()
			$dataLimite = new Carbon(\ParamUtil::value($this->params, 'dataLimite'), 'America/Sao_Paulo');

			$tarefa = $this->colecaoChecklist->comId(\ParamUtil::value($this->params, 'id'));

			if($tarefa->getEncerrada()) throw new Exception("Não é possível editar uma tarefa já encerrada.");

			$tarefa->setTitulo(\ParamUtil::value($this->params, 'titulo'));
			$tarefa->setDescricao(\ParamUtil::value($this->params, 'descricao'));
			$tarefa->setDataLimite($dataLimite);
			$tarefa->setSetor($setor);
			$tarefa->setLoja($loja);
	
			$resposta = [];
					
			$tarefa = $this->colecaoChecklist->atualizar($tarefa);

			$resposta = ['categoria'=> RTTI::getAttributes( $tarefa, RTTI::allFlags()), 'status' => true, 'mensagem'=> 'Categoria cadastrada com sucesso.']; 
			
			DB::commit();

		}
		catch (\Exception $e) {
			DB::rollback();

			$resposta = ['status' => false, 'mensagem'=> $e->getMessage()]; 
		}

		return $resposta;
	}

	function remover($id, $idSetor = 0) {
		DB::beginTransaction();

		try {
			if($this->servicoLogin->verificarSeUsuarioEstaLogado() == false) {
				throw new Exception("Erro ao acessar página.");				
			}

			if(!$this->servicoLogin->eAdministrador()){
				throw new Exception("Usuário sem permissão para executar ação.");
			}
			
			$resposta = [];

			$status = ($idSetor > 0) ? $this->colecaoChecklist->removerComSetorId($id, $idSetor) :  $this->colecaoChecklist->remover($id);
			
			$resposta = ['status' => true, 'mensagem'=> 'Categoria removida com sucesso.']; 
			DB::commit();

		}
		catch (\Exception $e) {
			DB::rollback();

			$resposta = ['status' => false, 'mensagem'=> $e->getMessage()]; 
		}

		return $resposta;
	}
}
?>