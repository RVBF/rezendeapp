<?php
use phputil\datatables\DataTablesRequest;
use phputil\datatables\DataTablesResponse;
use Symfony\Component\Validator\Validation as Validacao;
use \phputil\JSON;
use \phputil\RTTI;
use Carbon\Carbon;
use Illuminate\Database\Capsule\Manager as DB;


/**
 * Controladora de PlanoAcao
 *
 * @author	Rafael Vinicius Barros Ferreira
 * @version	1.0
 */
class ControladoraPlanoAcao {

	private $params;
	private $colecaoChecklist;
	private $colecaoSetor;
	private $servicoLogin;
	private $colecaoUsuario;
	private $colecaoColaborador;
	private $colecaoLoja;
	private $colecaoPlanoAcao;
	private $colecaoQuestionamento;
	private $servicoArquivo;
	private $colecaoAnexo;

	function __construct($params,  Sessao $sessao) {
		$this->params = $params;
		$this->servicoLogin = new ServicoLogin($sessao);
		$this->colecaoChecklist = Dice::instance()->create('ColecaoChecklist');
		$this->colecaoSetor = Dice::instance()->create('ColecaoSetor');
		$this->colecaoUsuario = Dice::instance()->create('ColecaoUsuario');
		$this->colecaoColaborador = Dice::instance()->create('ColecaoColaborador');
		$this->colecaoLoja = Dice::instance()->create('ColecaoLoja');
		$this->colecaoPlanoAcao  = Dice::instance()->create('ColecaoPlanoAcao');
		$this->colecaoQuestionamento = Dice::instance()->create('ColecaoQuestionamento');
		$this->servicoArquivo = ServicoArquivo::instance();
		$this->colecaoAnexo = Dice::instance()->create('ColecaoAnexo');


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

			$colaborador = new Colaborador();  $colaborador->fromArray($this->colecaoColaborador->comUsuarioId($this->servicoLogin->getIdUsuario()));

			$objetos = $this->colecaoPlanoAcao->todosComResponsavelId($dtr->start, $dtr->length, (isset($dtr->search->value)) ? $dtr->search->value : '', $colaborador->getId());

			$contagem = $this->colecaoPlanoAcao->contagem($colaborador->getId());
		}
		catch (\Exception $e ) {
			throw new Exception("Erro ao listar tarefas");
		}

		$conteudo = new DataTablesResponse(
			$contagem,
			is_array($objetos) ? count($objetos) : 0, //count($objetos ),
			$objetos,
			$dtr->draw,
			$erro
		);
		
		return RTTI::getAttributes($conteudo,  RTTI::allFlags());
	}
	
	function adicionar() {
		DB::beginTransaction();

		try {
			if($this->servicoLogin->verificarSeUsuarioEstaLogado() == false) {
				throw new Exception("Erro ao acessar página.");				
			}

			// if(!$this->servicoLogin->eAdministrador()){
			// 	throw new Exception("Usuário sem permissão para executar ação.");
			// }

			$inexistentes = \ArrayUtil::nonExistingKeys(['id', 'descricao', 'dataLimite', 'solucao', 'responsavel', 'unidade'], $this->params);
		
			if(is_array($inexistentes) ? count($inexistentes) > 0 : 0) throw new Exception('Os seguintes campos obrigatórios não foram enviados: ' . implode(', ', $inexistentes));

			$loja = new Loja();  $loja->fromArray($this->colecaoLoja->comId(ParamUtil::value($this->params, 'unidade')));
			if(!isset($loja) and !($loja instanceof Loja)) throw new Exception("Loja não encontrada na base de dados.");

			$responsavelAtual = new Colaborador(); $responsavelAtual->fromArray($this->colecaoColaborador->comUsuarioId($this->servicoLogin->getIdUsuario()));

			if(!isset($responsavelAtual) and !($responsavelAtual instanceof Colaborador)) throw new Exception("Colaborador não encontrado na base de dados.");

			$responsavel = new Colaborador();  $responsavel->fromArray($this->colecaoColaborador->comId(ParamUtil::value($this->params, 'responsavel')));

			if(!isset($responsavel) and !($responsavel instanceof Responsavel)) throw new Exception("Responsável não encontrada na base de dados.");

			$dataLimite = new Carbon();                  // equivalent to Carbon::now()
			$dataLimite = new Carbon(\ParamUtil::value($this->params, 'dataLimite'), 'America/Sao_Paulo');

			$planoAcao = new PlanoAcao(
				0,
				($responsavel->getId() == $responsavelAtual->getId()) ? StatusPaEnumerado::AGUARDANDO_EXECUCAO : StatusPaEnumerado::AGUARDANDO_RESPONSAVEL,
				\ParamUtil::value($this->params, 'descricao'),
				$dataLimite,
				'',
				'',
				$responsavel,
				$loja,
				'',
				'',
				($responsavel->getId() == $responsavelAtual->getId()) ?  true : false
			);
			
			$resposta = [];
						
			$this->colecaoPlanoAcao->adicionar($planoAcao);

			$resposta = ['status' => true, 'mensagem'=> 'Plano de ação atualizado com sucesso.']; 
			
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

			// if(!$this->servicoLogin->eAdministrador()){
			// 	throw new Exception("Usuário sem permissão para executar ação.");
			// }

			$inexistentes = \ArrayUtil::nonExistingKeys(['id', 'descricao', 'dataLimite', 'solucao', 'responsavel', 'unidade'], $this->params);
		
			if(is_array($inexistentes) ? count($inexistentes) > 0 : 0) throw new Exception('Os seguintes campos obrigatórios não foram enviados: ' . implode(', ', $inexistentes));
				
			$loja = new Loja();  $loja->fromArray($this->colecaoLoja->comId(ParamUtil::value($this->params, 'unidade')));

			if(!isset($loja) and !($loja instanceof Loja)) throw new Exception("Loja não encontrada na base de dados.");


			$responsavel = new Colaborador();  $responsavel->fromArray($this->colecaoColaborador->comId(ParamUtil::value($this->params, 'responsavel')));

			if(!isset($responsavel) and !($responsavel instanceof Responsavel)) throw new Exception("Responsável não encontrada na base de dados.");

			$dataLimite = new Carbon();                  // equivalent to Carbon::now()
			$dataLimite = new Carbon(\ParamUtil::value($this->params, 'dataLimite'), 'America/Sao_Paulo');

			$planoAcao = new PlanoAcao(); $planoAcao->fromArray($this->colecaoPlanoAcao->comId(\ParamUtil::value($this->params, 'id')));
			
			$planoAcao->setDescricao(\ParamUtil::value($this->params, 'descricao'));
			$planoAcao->setSolucao(\ParamUtil::value($this->params, 'solucao'));
			$planoAcao->setDataLimite($dataLimite);
			$planoAcao->setUnidade($loja);
			$planoAcao->setResponsavel($responsavel);
			$resposta = [];
						
			$this->colecaoPlanoAcao->atualizar($planoAcao);

			$resposta = ['status' => true, 'mensagem'=> 'Plano de ação atualizado com sucesso.']; 
			
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

	function comId($id) {
		try {
			if($this->servicoLogin->verificarSeUsuarioEstaLogado() == false) throw new Exception("Erro ao acessar página.");				
			
			if (! is_numeric($id)) return $this->geradoraResposta->erro('O id informado não é numérico.', GeradoraResposta::TIPO_TEXTO);

			$planoAcao = $this->colecaoPlanoAcao->comId($id);

			$resposta = ['conteudo'=> $planoAcao, 'status' => true, 'mensagem'=> 'Plano de ação encontrado com sucesso.']; 
		}
		catch (\Exception $e) {
			$resposta = ['status' => false, 'mensagem'=>  $e->getMessage()]; 
		}

		return $resposta;
	}

	function confirmarResponsabilidade(){
		try {
			if($this->servicoLogin->verificarSeUsuarioEstaLogado() == false) throw new Exception("Erro ao acessar página.");				
			
			$inexistentes = \ArrayUtil::nonExistingKeys(['id'], $this->params);

			if(is_array($inexistentes) ? count($inexistentes) > 0 : 0) {
				$msg = 'Os seguintes campos obrigatórios não foram enviados: ' . implode(', ', $inexistentes);
	
				throw new Exception($msg);
			}

			$colaboradorLogado = new Colaborador(); $colaboradorLogado->fromArray($this->colecaoColaborador->comUsuarioId($this->servicoLogin->getIdUsuario()));

			if(!isset($colaboradorLogado) and !($colaboradorLogado instanceof Colaborador)){
				throw new Exception("Colaborador não encontrado na base de dados.");
			}	

			$planoAcao  = new PlanoAcao(); $planoAcao->fromArray($this->colecaoPlanoAcao->comId($this->params['id']));

			if(!isset($planoAcao) and !($planoAcao instanceof Colaborador)){
				throw new Exception("Colaborador não encontrado na base de dados.");
			}	
			
			$responsavelAtual = new Colaborador(); $responsavelAtual->fromArray($planoAcao->getResponsavel());

			if(!isset($responsavelAtual) and !($planoAcao instanceof Colaborador)){
				throw new Exception("Colaborador não encontrado na base de dados.");
			}	
			
			if($responsavelAtual->getId() != $colaboradorLogado->getId()) throw new Exception("Não é possível confirmar a responsabilidade, porque o colaborador atual é diferente do colaborador logado no sistema!");
			
			$planoAcao->setResponsabilidade(true);
			$planoAcao->setStatus(StatusPaEnumerado::AGUARDANDO_EXECUCAO);

			$this->colecaoPlanoAcao->atualizar($planoAcao);

			$resposta = ['conteudo'=> $planoAcao, 'status' => true, 'mensagem'=> 'Responsabilidade confirmada com sucesso.']; 
		}
		catch (\Exception $e) {
			$resposta = ['status' => false, 'mensagem'=>  $e->getMessage()]; 
		}

		return $resposta;
	}


    function executar(){
		DB::beginTransaction();

		try {
			if($this->servicoLogin->verificarSeUsuarioEstaLogado() == false) throw new Exception("Erro ao acessar página.");		
			
			// if(!$this->servicoLogin->eAdministrador()){
			// 	throw new Exception("Usuário sem permissão para executar ação.");
			// }
			$inexistentes = \ArrayUtil::nonExistingKeys(['id', 'solucao', 'anexos'], $this->params);
			$resposta = [];

			if(is_array($inexistentes) ? count($inexistentes) > 0 : 0) {
				$msg = 'Os seguintes campos obrigatórios não foram enviados: ' . implode(', ', $inexistentes);

				throw new Exception($msg);
			}

			$planoAcao = new PlanoAcao(); $planoAcao->fromArray($this->colecaoPlanoAcao->comId($this->params['id']));
			if(!isset($planoAcao) and !($planoAcao instanceof PlanoAcao)){
				throw new Exception("Questionamento não encontrado na base de dados.");
			}				
		
			$planoAcao->setStatus(StatusPaEnumerado::EXECUTADO);
			$planoAcao->setResposta(\ParamUtil::value($this->params, 'resposta'));
			$planoAcao->setDataExecucao(Carbon::now());

			$this->colecaoPlanoAcao->atualizar($planoAcao);

			$questionamento = new Questionamento(); $questionamento->fromArray($this->colecaoQuestionamento->comPlanodeAcaoid(\ParamUtil::value($this->params, 'id')));
			if(!isset($questionamento) and !($questionamento instanceof Questionamento)){
				throw new Exception("Questionamento não encontrado na base de dados.");
			}	

			$questionamento->setStatus(TipoQuestionamentoEnumerado::RESPONDIDO);

			$this->colecaoQuestionamento->atualizar($questionamento);

			$checklist = new Checklist(); $checklist->fromArray($this->colecaoChecklist->comId($questionamento->getId()));
			if(!isset($checklist) and !($checklist instanceof Checklist)){
				throw new Exception("checklist não encontrado na base de dados.");
			}	
		
			$checklist->setStatus(StatusChecklistEnumerado::EXECUTADO);
			$this->colecaoChecklist->atualizar($checklist);

			if(isset($this->params['anexos']) and count($this->params['anexos']) > 0){
				$pastaTarefa = 'planoacao_'. $questionamento->getId();

				foreach($this->params['anexos'] as $arquivo) {
					$patch = $this->servicoArquivo->validarESalvarImagem($arquivo, $pastaTarefa, 'planoacao_' . $questionamento->getId());
					$anexo = new Anexo(
						0,
						$patch,
						$arquivo['tipo'],
						(isset($questionamento) and $questionamento instanceof Questionamento) ? $questionamento : 0,
						(isset($planoAcao) and $planoAcao instanceof PlanoAcao) ? $planoAcao : 0
					);

					$this->colecaoAnexo->adicionar($anexo);
				}
			}
			else{
				throw new Exception("É necessário  pelo menos um anexo para comprar a execução do plano de ação!");
			}

			$resposta = ['status' => true, 'mensagem'=> 'Plano de ação executado com sucesso.']; 
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