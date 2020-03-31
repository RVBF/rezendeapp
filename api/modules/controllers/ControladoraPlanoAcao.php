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
	private $colecaoPendencia;
	private $servicoArquivo;
	private $colecaoAnexo;
	private $colecaoHistorico;


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
		$this->colecaoPendencia = Dice::instance()->create('ColecaoPendencia');
		$this->colecaoHistorico = Dice::instance()->create('ColecaoHistoricoResponsabilidade');
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
			throw new Exception("Erro ao listar planos de ação.");
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

	function todosPendentes($checklistId) {
		try {

			if($this->servicoLogin->verificarSeUsuarioEstaLogado() == false) {
				throw new Exception("Erro ao acessar página.");				
			}

			$dtr = new DataTablesRequest($this->params);
			$contagem = 0;
			$objetos = [];
			$erro = null;

			$colaborador = new Colaborador();  $colaborador->fromArray($this->colecaoColaborador->comUsuarioId($this->servicoLogin->getIdUsuario()));

			$objetos = $this->colecaoPlanoAcao->todosComChecklistId($dtr->start, $dtr->length, (isset($dtr->search->value)) ? $dtr->search->value : '', $colaborador->getId(), $checklistId);
			$contagem = $this->colecaoPlanoAcao->contagem($colaborador->getId());
		}
		catch (\Exception $e ) {
			throw new Exception("Erro ao listar planos de ação.");
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

			$dataLimite = new Carbon();
			$dataLimite = new Carbon(\ParamUtil::value($this->params, 'dataLimite'), 'America/Sao_Paulo');

			$planoAcao = new PlanoAcao(
				0,
				($responsavel->getId() == $responsavelAtual->getId()) ? StatusPaEnumerado::AGUARDANDO_EXECUCAO : StatusPaEnumerado::AGUARDANDO_RESPONSAVEL,
				\ParamUtil::value($this->params, 'descricao'),
				$dataLimite,
				json_encode($this->params['solucao']),
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
			$colaborador = new Colaborador();  $colaborador->fromArray($this->colecaoColaborador->comUsuarioId($this->servicoLogin->getIdUsuario()));

			$inexistentes = \ArrayUtil::nonExistingKeys(['id', 'descricao', 'dataLimite', 'solucao', 'responsavel', 'unidade'], $this->params);
		
			if(is_array($inexistentes) ? count($inexistentes) > 0 : 0) throw new Exception('Os seguintes campos obrigatórios não foram enviados: ' . implode(', ', $inexistentes));
				
			$loja = new Loja();  $loja->fromArray($this->colecaoLoja->comId(ParamUtil::value($this->params, 'unidade')));

			if(!isset($loja) and !($loja instanceof Loja)) throw new Exception("Loja não encontrada na base de dados.");


			$responsavel = new Colaborador();  $responsavel->fromArray($this->colecaoColaborador->comId(ParamUtil::value($this->params, 'responsavel')));

			if(!isset($responsavel) and !($responsavel instanceof Responsavel)) throw new Exception("Responsável não encontrada na base de dados.");

			$dataLimite = new Carbon();                  // equivalent to Carbon::now()
			$dataLimite = new Carbon(\ParamUtil::value($this->params, 'dataLimite'), 'America/Sao_Paulo');

			$planoAcao = new PlanoAcao(); $planoAcao->fromArray($this->colecaoPlanoAcao->comId(\ParamUtil::value($this->params, 'id')));
			$responsavelAnterior = new Colaborador();  $responsavelAnterior->fromArray($this->colecaoColaborador->comId($planoAcao->getResponsavel()['id']));

			if($colaborador->getId() != $responsavelAnterior->getId()) throw new Exception("O plano de ação só pode ser editado pelo responsável!");

			$planoAcao->setDescricao(\ParamUtil::value($this->params, 'descricao'));
			$planoAcao->setSolucao(json_encode($this->params['solucao']));
			$planoAcao->setDataLimite($dataLimite);
			$planoAcao->setUnidade($loja);		
			$planoAcao->setResponsavel($responsavel);

			if($responsavel->getId() != $responsavelAnterior->getId()){
				
				$historico = new HistoricoResponsabilidade(
					0,
					Carbon::now(),
					$planoAcao,
					$responsavel,
					$responsavelAnterior
				);

				$this->colecaoHistorico->adicionar($historico);
			}

			
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

	function remover($id) {
		DB::beginTransaction();

		try {
			if($this->servicoLogin->verificarSeUsuarioEstaLogado() == false) {
				throw new Exception("Erro ao acessar página.");				
			}

			$planoAcao = new PlanoAcao(); $planoAcao->fromArray($this->colecaoPlanoAcao->comId($id));

			if(!($planoAcao instanceof PlanoAcao)) throw new ColecaoException("Erro ao buscar plano de ação.");
		
			if($this->colecaoQuestionamento->contagemPorColuna($planoAcao->getId(), 'planoacao_id') > 0){
				$questionamento = new Questionamento(); $questionamento->fromArray($this->colecaoQuestionamento->comPlanodeAcaoid($planoAcao->getId(), 'id'));
				if(!isset($questionamento) and !($questionamento instanceof Questionamento)) throw new Exception("Questionamento não encontrado na base de dados.");
			
				$checklist = new Checklist(); $checklist->fromArray($this->colecaoChecklist->comId($questionamento->getChecklist()));
				if($checklist instanceof Checklist){
					foreach ($checklist->getQuestionamentos() as $key => $questionamento) {
						$questionamentoAtual = new Questionamento(); $questionamentoAtual->fromArray($questionamento);
						$planoAcao = $pendencia = [];
		
						if(!empty($questionamentoAtual->getPlanoAcao())){
							$planoAcao = new PlanoAcao(); $planoAcao->fromArray($questionamentoAtual->getPlanoAcao());
						}
						if(!empty($questionamentoAtual->getPendencia())){
							$pendencia = new Pendencia(); $pendencia->fromArray($questionamentoAtual->getPendencia());
						}
		
						if($planoAcao instanceof PlanoAcao){
							foreach ($planoAcao->getAnexos() as $anexo) {
								$anexoAtual = new Anexo(); $anexoAtual->fromArray($anexo);
								$this->colecaoAnexo->remover($anexoAtual->getId());
							}
		
							$this->colecaoPlanoAcao->remover($planoAcao->getId());
						}
		
						if($pendencia instanceof Pendencia){
							$this->colecaoPendencia->remover($pendencia->getId());
						}
		
						foreach ($questionamentoAtual->getAnexos() as $anexo) {
							$anexoAtual = new Anexo(); $anexoAtual->fromArray($anexo);
							$this->colecaoAnexo->remover($anexoAtual->getId());
						}
		
						$this->colecaoQuestionamento->remover($questionamentoAtual->getId());
					}				
				}	

				$this->colecaoChecklist->remover($checklist->getId());
			}
		
			$this->colecaoPlanoAcao->remover($id);
			
			$resposta = ['status' => true, 'mensagem'=> 'Plano de ação removida com sucesso.']; 
			
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
			$inexistentes = \ArrayUtil::nonExistingKeys(['id', 'solucao'], $this->params);
			$resposta = [];

			if(is_array($inexistentes) ? count($inexistentes) > 0 : 0) {
				$msg = 'Os seguintes campos obrigatórios não foram enviados: ' . implode(', ', $inexistentes);

				throw new Exception($msg);
			}

			// Util::printr($this->params);

			$planoAcao = new PlanoAcao(); $planoAcao->fromArray($this->colecaoPlanoAcao->comId($this->params['id']));
			
			if(!isset($planoAcao) and !($planoAcao instanceof PlanoAcao)) throw new Exception("Plano de ação não encontrado no banco de dados.");
			$responsavel = new Colaborador(); $responsavel->fromArray($planoAcao->getResponsavel());

			$planoAcao->setStatus(StatusPaEnumerado::EXECUTADO);
			$planoAcao->setResposta(\ParamUtil::value($this->params, 'resposta'));
			$planoAcao->setDataExecucao(Carbon::now());
			$this->colecaoPlanoAcao->executar($planoAcao);
			$questionamento = null;

			if($this->colecaoQuestionamento->contagemPorColuna(\ParamUtil::value($this->params, 'id'), 'planoacao_id') > 0){
				$questionamento = new Questionamento(); $questionamento->fromArray($this->colecaoQuestionamento->comPlanodeAcaoid(\ParamUtil::value($this->params, 'id')));

				if(!isset($questionamento) and !($questionamento instanceof Questionamento)){
					throw new Exception("Questionamento não encontrado na base de dados.");
				}	
				if($questionamento->getPendencia() != null){
					$pendencia =  new PlanoAcao(); $pendencia->fromArray($questionamento->getPendencia());
					if($pendencia->getStatus() != StatusPendenciaEnumerado::EXECUTADO){
						$questionamento->setStatus(TipoQuestionamentoEnumerado::RESPONDIDO);
						$this->colecaoQuestionamento->atualizar($questionamento);
					}
				}
				else if($questionamento->getPendencia() == null){
					$questionamento->setStatus(TipoQuestionamentoEnumerado::RESPONDIDO);
					$this->colecaoQuestionamento->atualizar($questionamento);
				}

				$checklist = new Checklist(); $checklist->fromArray($this->colecaoChecklist->comId($questionamento->getChecklist()));
				if(!isset($checklist) and !($checklist instanceof Checklist)){
					throw new Exception("checklist não encontrado na base de dados.");
				}	

				if(!$this->colecaoChecklist->temPendencia($checklist->getId())){
					$checklist->setStatus(StatusChecklistEnumerado::EXECUTADO);
					$this->colecaoChecklist->atualizar($checklist);	
				}
				else{
					if($checklist->getStatus() == StatusChecklistEnumerado::AGUARDANDO_EXECUCAO){
						$checklist->setStatus(StatusChecklistEnumerado::INCOMPLETO);
						$this->colecaoChecklist->atualizar($checklist);	
					}
				}
			}
		
			if(isset($this->params['anexos']) and count($this->params['anexos']) > 0){
				$pastaTarefa = 'planoacao_'. $planoAcao->getId();

				foreach($this->params['anexos'] as $arquivo) {
					$patch = $this->servicoArquivo->validarESalvarImagem($arquivo, $pastaTarefa, 'planoacao_' . $planoAcao->getId());
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
				throw new Exception("É necessário  anexar uma áudio ou foto para comprovar a execução do plano de ação!");
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