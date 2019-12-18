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
class ControladoraPendencia {

	private $params;
	private $colecaoChecklist;
	private $servicoLogin;
	private $colecaoUsuario;
	private $colecaoColaborador;
	private $colecaoLoja;
	private $colecaoPlanoAcao;
    private $colecaoQuestionamento;
    private $colecaoPendencia;
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
        $this->colecaoPendencia = Dice::instance()->create('ColecaoPendencia');
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

			$objetos = $this->colecaoPendencia->todosComResponsavelId($dtr->start, $dtr->length, (isset($dtr->search->value)) ? $dtr->search->value : '', $colaborador->getId());
			$contagem = $this->colecaoPendencia->contagem($colaborador->getId());
		}
		catch (\Exception $e ) {
			throw new Exception("Erro ao listar pêndencias");
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
			$objetos = $this->colecaoPendencia->todosComChecklistId($dtr->start, $dtr->length, (isset($dtr->search->value)) ? $dtr->search->value : '', $colaborador->getId(), $checklistId);

			$contagem = $this->colecaoPendencia->contagem($colaborador->getId());
		}
		catch (\Exception $e ) {
			throw new Exception("Erro ao listar checklists");
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

			$pendencia = new Pendencia(
				0,
				StatusPendenciaEnumerado::AGUARDANDO_EXECUCAO,
				\ParamUtil::value($this->params, 'descricao'),
				$dataLimite,
				'',
				$responsavel
			);
			
			$resposta = [];
						
			$this->colecaoPendencia->adicionar($pendencia);

			$resposta = ['status' => true, 'mensagem'=> 'Plano de ação atualizado com sucesso.']; 
			
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

			$pendencia = new Pendencia(); $pendencia->fromArray($this->colecaoPendencia->comId(\ParamUtil::value($this->params, 'id')));

			$pendencia->setDescricao(\ParamUtil::value($this->params, 'descricao'));
			$pendencia->setSolucao(\ParamUtil::value($this->params, 'solucao'));
			$pendencia->setDataLimite($dataLimite);
			$pendencia->setUnidade($loja);
			$pendencia->setResponsavel($responsavel);
			$resposta = [];
						
			$this->colecaoPendencia->atualizar($pendencia);
			$resposta = ['status' => true, 'mensagem'=> 'Pendência atualizada com sucesso.']; 
			
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
			
			$resposta = ['status' => true, 'mensagem'=> 'Pendência removida com sucesso.']; 
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

			$planoAcao = $this->colecaoPendencia->comId($id);

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

    function executar($id){
		DB::beginTransaction();

		try {
			if($this->servicoLogin->verificarSeUsuarioEstaLogado() == false) throw new Exception("Erro ao acessar página.");		
			
			// if(!$this->servicoLogin->eAdministrador()){
			// 	throw new Exception("Usuário sem permissão para executar ação.");
			// }

			$resposta = [];
			$colaborador = new Colaborador();  $colaborador->fromArray($this->colecaoColaborador->comUsuarioId($this->servicoLogin->getIdUsuario()));
			if(!isset($colaborador) and !($colaborador instanceof Colaborador)){
				throw new Exception("Colaborador não encontrado na base de dados.");
			}	

			$pendencia = new Pendencia(); $pendencia->fromArray($this->colecaoPendencia->comId($id));
			if(!isset($pendencia) and !($pendencia instanceof Pendencia)){
				throw new Exception("Pendência não encontrada na base de dados.");
			}	
			
			$responsavel = new Colaborador(); $responsavel->fromArray($pendencia->getResponsavel());

			if($colaborador->getId() != $responsavel->getId()) throw new Exception("Só o usuário Responsável pode executar essa pendência!");
			
			$pendencia->setStatus(StatusPendenciaEnumerado::EXECUTADO);
			$pendencia->setDataExecucao(Carbon::now());

			$this->colecaoPendencia->atualizar($pendencia);

			$questionamento = null;
			if($this->colecaoQuestionamento->contagemPorColuna($id, 'pendencia_id') > 0){
				$questionamento = new Questionamento(); $questionamento->fromArray($this->colecaoQuestionamento->comPendenciaId($id));

				if(!isset($questionamento) and !($questionamento instanceof Questionamento)){
					throw new Exception("Questionamento não encontrado na base de dados.");
				}	
				
				if($questionamento->getPlanoAcao() != null){
					$planoAcao =  new PlanoAcao(); $planoAcao->fromArray($questionamento->getPlanoAcao());
					if($planoAcao->getStatus() != StatusPaEnumerado::EXECUTADO){
						$questionamento->setStatus(TipoQuestionamentoEnumerado::RESPONDIDO);
						$this->colecaoQuestionamento->atualizar($questionamento);
					}
				}
				else if($questionamento->getPlanoAcao() == null){
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
						$checklist->setStatus(StatusChecklistEnumerado::EM_PROGRESSO);
						$this->colecaoChecklist->atualizar($checklist);	
					}
				}
			}
		
			$resposta = ['status' => true, 'mensagem'=> 'Pendência executada com sucesso!']; 
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