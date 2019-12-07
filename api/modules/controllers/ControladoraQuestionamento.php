<?php
use phputil\datatables\DataTablesRequest;
use phputil\datatables\DataTablesResponse;
use Symfony\Component\Validator\Validation as Validacao;
use \phputil\JSON;
use \phputil\RTTI;
use Illuminate\Database\Capsule\Manager as DB;
use Carbon\Carbon;




/**
 * Controladora de Questionamento
 *
 * @author	Rafael Vinicius Barros Ferreira
 * @version	1.0
 */
class ControladoraQuestionamento {

	private $params;
	private $colecaoQuestionamento;
	private $colecaoPlanoAcao;
	private $servicoLogin;
	private $colecaoColaborador;
	private $colecaoPendencia;
	private $colecaoChecklist;
	private $colecaoHistorico;
	private $servicoArquivo;
	private $colecaoAnexo;
	
	function __construct($params,  Sessao $sessao) {
		$this->params = $params;
		$this->colecaoQuestionamento = Dice::instance()->create('ColecaoQuestionamento');
		$this->colecaoPlanoAcao = Dice::instance()->create('ColecaoPlanoAcao');
		$this->colecaoColaborador = Dice::instance()->create('ColecaoColaborador');
		$this->colecaoPendencia  = Dice::instance()->create('ColecaoPendencia');
		$this->colecaoChecklist = Dice::instance()->create('ColecaoChecklist');
		$this->colecaoHistorico = Dice::instance()->create('ColecaoHistoricoResponsabilidade');
		$this->colecaoAnexo = Dice::instance()->create('ColecaoAnexo');
		$this->servicoArquivo = ServicoArquivo::instance();
		$this->servicoLogin = new ServicoLogin($sessao);
	}

	function todos($checklistId = 0) {
		try {
			if($this->servicoLogin->verificarSeUsuarioEstaLogado() == false) {
				throw new Exception("Erro ao acessar página.");				
			}
			
			$dtr = new DataTablesRequest($this->params);
			$contagem = 0;
			$objetos = [];
			$erro = null;
			$objetos = $this->colecaoQuestionamento->todosComChecklistId($this->params['homePage'], $this->params['pageLength'], (isset($dtr->search->value)) ? $dtr->search->value : '', $checklistId);
			// Util::printr($objetos);
			$contagem = $this->colecaoQuestionamento->contagemPorColuna($checklistId, 'checklist_id');
		}
		catch (\Exception $e )
		{
			throw new Exception("Erro ao listar lojas.");
		}

		$conteudo = new DataTablesResponse(
			$contagem,
			count($objetos), //count($objetos ),
			$objetos,
			$dtr->draw,
			$erro
		);

		return RTTI::getAttributes($conteudo,  RTTI::allFlags());
	}

    function executar(){
		DB::beginTransaction();

		try {
			if($this->servicoLogin->verificarSeUsuarioEstaLogado() == false) throw new Exception("Erro ao acessar página.");		
			
			// if(!$this->servicoLogin->eAdministrador()){
			// 	throw new Exception("Usuário sem permissão para executar ação.");
			// }

			$inexistentes = \ArrayUtil::nonExistingKeys(['id', 'status','formularioPergunta', 'formularioResposta'], $this->params);
			$resposta = [];

			if(is_array($inexistentes) ? count($inexistentes) > 0 : 0) {
				$msg = 'Os seguintes campos obrigatórios não foram enviados: ' . implode(', ', $inexistentes);

				throw new Exception($msg);
			}

			$responsavelAtual = new Colaborador(); $responsavelAtual->fromArray($this->colecaoColaborador->comUsuarioId($this->servicoLogin->getIdUsuario()));

			if(!isset($responsavelAtual) and !($responsavelAtual instanceof Colaborador)){
				throw new Exception("Colaborador não encontrado na base de dados.");
			}	

			$planoDeAcao = null;
			$pendencia = NULL;

			$questionamento = new Questionamento(); $questionamento->fromArray($this->colecaoQuestionamento->comId($this->params['id']));
			if(!isset($questionamento) and !($questionamento instanceof Questionamento)){
				throw new Exception("Questionamento não encontrado na base de dados.");
			}				

			if($this->params['formularioResposta']['opcao'] != OpcoesRespostaEnumerada::BOM){
				if(isset($this->params['planoAcao']) || isset($this->params['pendencia'])){
					if(!(((
						strlen($this->params['pendencia']['descricao']) > 0 and
						strlen($this->params['pendencia']['dataLimite']) > 0 and
						strlen($this->params['pendencia']['solucao']) > 0 and
						$this->params['pendencia']['responsavel'] > 0
					) and !(
						strlen($this->params['planoAcao']['descricao']) > 0 and
						strlen($this->params['planoAcao']['dataLimite']) > 0 and
						strlen($this->params['planoAcao']['solucao']) > 0 and
						$this->params['planoAcao']['responsavel'] > 0
					)) || (!(
						strlen($this->params['pendencia']['descricao']) > 0 and
						strlen($this->params['pendencia']['dataLimite']) > 0 and
						strlen($this->params['pendencia']['solucao']) > 0 and
						$this->params['pendencia']['responsavel'] > 0
					) and (
						strlen($this->params['planoAcao']['descricao']) > 0 and
						strlen($this->params['planoAcao']['dataLimite']) > 0 and
						strlen($this->params['planoAcao']['solucao']) > 0 and
						$this->params['planoAcao']['responsavel'] > 0
					)))) 	throw new Exception("É necessário cadastrar um plano de ação ou uma pendência para questionamentos com resposta inferior a " . strtolower(OpcoesRespostaEnumerada::BOM) .'!');

				
					if(
						strlen($this->params['planoAcao']['descricao']) > 0 and
						strlen($this->params['planoAcao']['dataLimite']) > 0 and
						strlen($this->params['planoAcao']['solucao']) > 0 and
						$this->params['planoAcao']['responsavel'] > 0
					){

						$responsavel = new Colaborador(); $responsavel->fromArray($this->colecaoColaborador->comId($this->params['planoAcao']['responsavel']));

						if(!isset($responsavel) and !($responsavel instanceof Colaborador)){
							throw new Exception("Colaborador não encontrado na base de dados.");
						}	
						

						$dataLimitePA = new Carbon($this->params['planoAcao']['dataLimite'], 'America/Sao_Paulo');

						$planoDeAcao = new PlanoAcao(
							0,
							($responsavel->getId() == $responsavelAtual->getId()) ? StatusPaEnumerado::AGUARDANDO_EXECUCAO : StatusPaEnumerado::AGUARDANDO_RESPONSAVEL,
							$this->params['planoAcao']['descricao'],
							$dataLimitePA,
							$this->params['planoAcao']['solucao'],
							'',
							$responsavel,
							null,
							'',
							'',
							($responsavel->getId() == $responsavelAtual->getId()) ?  true : false
						);

						$this->colecaoPlanoAcao->adicionar($planoDeAcao);


						$historico = new HistoricoResponsabilidade(
							0,
							Carbon::now(),
							$planoDeAcao,
							$responsavelAtual,
							$responsavel
						);

						$this->colecaoHistorico->adicionar($historico);
					}

					if(
						strlen($this->params['pendencia']['descricao']) > 0 and
						strlen($this->params['pendencia']['dataLimite']) > 0 and
						strlen($this->params['pendencia']['solucao']) > 0 and
						$this->params['pendencia']['responsavel'] > 0
					){

						$responsavel = new Colaborador(); $responsavel->fromArray($this->colecaoColaborador->comId($this->params['pendencia']['responsavel']));

						if(!isset($responsavel) and !($responsavel instanceof Colaborador)){
							throw new Exception("Colaborador não encontrado na base de dados.");
						}
						$dataLimitePe = new Carbon($this->params['pendencia']['dataLimite'], 'America/Sao_Paulo');
	
						$pendencia = new Pendencia(
							0,
							StatusPendenciaEnumerado::AGUARDANDO_EXECUCAO,
							$this->params['pendencia']['descricao'],
							$dataLimitePe,
							$this->params['pendencia']['solucao'],
							$responsavel
						);
						$this->colecaoPendencia->adicionar($pendencia);
					}
				}

				if(isset($this->params['anexos']) and count($this->params['anexos']) > 0){
					$pastaTarefa = 'questionamento_'. $questionamento->getId();


					foreach($this->params['anexos'] as $arquivo) {
						$patch = $this->servicoArquivo->validarESalvarImagem($arquivo, $pastaTarefa, 'questionamento_' . $questionamento->getId());
						$anexo = new Anexo(
							0,
							$patch,
							$arquivo['tipo'],
							$questionamento
						);

						$this->colecaoAnexo->adicionar($anexo);
					}
				}
				else{
					throw new Exception("Para respostas inferior a bom é necessário adicionar ao menos 1 anexo para comprovação do problema!");
				}
			}

			$questionamento->setFormularioResposta(json_encode($this->params['formularioResposta']));
			$questionamento->setStatus(($pendencia instanceof Pendencia or $planoDeAcao instanceof PlanoAcao) ? TipoQuestionamentoEnumerado::RESPONDIDO_COM_PENDENCIAS : TipoQuestionamentoEnumerado::RESPONDIDO);
			$questionamento->setPendencia($pendencia);
			$questionamento->setPlanoAcao($planoDeAcao);

			$this->colecaoQuestionamento->executar($questionamento);

			$checklist = new Checklist(); $checklist->fromArray($this->colecaoChecklist->comId($questionamento->getChecklist()));				

			if(!$this->colecaoChecklist->temPendencia($checklist->getId())){
				$checklist->setStatus(StatusChecklistEnumerado::EXECUTADO);
				$this->colecaoChecklist->atualizar($checklist);	
			}
			else{
				if($checklist->getStatus() == StatusChecklistEnumerado::AGUARDANDO_EXECUCAO){
					$checklist->setStatus(StatusChecklistEnumerado::EM_PROGRESSO);
					$this->colecaoChecklist->atualizar($checklist);	
				}
			}

			$this->colecaoChecklist->atualizar($checklist);

			$resposta = ['status' => true, 'mensagem'=> 'Questionamento executado com sucesso.']; 
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

			$resposta = [];

			$status = $this->colecaoQuestionamento->remover($id);
			
			$resposta = ['status' => $status, 'mensagem'=> 'Loja removida com sucesso.']; 
			
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