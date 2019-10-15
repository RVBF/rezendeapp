<?php
use phputil\datatables\DataTablesRequest;
use phputil\datatables\DataTablesResponse;
use Symfony\Component\Validator\Validation as Validacao;
use \phputil\JSON;
use \phputil\RTTI;
use Carbon\Carbon;
use Illuminate\Database\Capsule\Manager as DB;


/**
 * Controladora de Resposta
 *
 * @author	Rafael Vinicius Barros Ferreira
 * @version	1.0
 */
class ControladoraResposta {

	private $params;
	private $colecaoPergunta;
	private $colecaoResposta;
	private $colecaoChecklist;
	private $colecaoAnexo;
	private $servicoLogin;
	private $colecaoUsuario;
	private $colecaoFormularioRespondido;
	
	function __construct($params,  Sessao $sessao) {
		$this->params = $params;
		$this->colecaoPergunta = Dice::instance()->create('ColecaoPergunta');
		$this->colecaoResposta = Dice::instance()->create('ColecaoResposta');
		$this->colecaoAnexo = Dice::instance()->create('ColecaoAnexo');
		$this->colecaoChecklist = Dice::instance()->create('ColecaoChecklist');
		$this->colecaoUsuario = Dice::instance()->create('ColecaoUsuario');
		$this->colecaoFormularioRespondido = Dice::instance()->create('ColecaoFormularioRespondido'); 
		$this->servicoArquivo = ServicoArquivo::instance();
		$this->servicoLogin = new ServicoLogin($sessao);

	}

	function todos($tarefaId) {
		try {
			if($this->servicoLogin->verificarSeUsuarioEstaLogado() == false) {
				throw new Exception("Erro ao acessar página.");				
			}

			$dtr = new DataTablesRequest($this->params);
			$contagem = 0;
			$objetos = [];
			$erro = null;
			$objetos = $this->colecaoResposta->todosComTarefaId($dtr->start, $dtr->length, $tarefaId, (isset($dtr->search->value)) ? $dtr->search->value : '');

			$tarefa = $this->colecaoChecklist->comId($tarefaId);
			if(!isset($tarefa) and !($tarefa instanceof Checklist)){
				throw new Exception("Checklist não encontrada na base de dados.");
			}
			
			foreach ($objetos as $key => $obj) {
				$obj->getPergunta()->setTarefa($tarefa);
			}

			$contagem = $this->colecaoResposta->contagem($tarefaId);
		}
		catch (\Exception $e ) {
			throw new Exception($e->getMessage());
		}


		$conteudo = new DataTablesResponse(
			$contagem,
			is_array($objetos) ? count($objetos) : 0, //count($objetos ),
			$objetos,
			$dtr->draw,
			$erro
		);

		return $conteudo;
	}

	function adicionar() {

		DB::beginTransaction();

		try {
			if($this->servicoLogin->verificarSeUsuarioEstaLogado() == false) {
				throw new Exception("Erro ao acessar página.");				
			}

			$respostaFront = $respostasCadastradas = [];
			$formularioRespondido = new FormularioRespondido();
			$formularioRespondido->setRespondedor($this->colecaoUsuario->comId($this->servicoLogin->getIdUsuario()));
			$formularioRespondido->setDataHora(Carbon::now());
			$tarefa = null;
			foreach($this->params['obj'] as $key => $parametros) {
				$inexistentes = \ArrayUtil::nonExistingKeys(['id', 'opcaoSelecionada','comentario', 'pergunta'], $parametros);

				if(is_array($inexistentes) ? count($inexistentes) > 0 : 0) {
					$msg = 'Os seguintes campos obrigatórios da pergunta de id   não foram enviados: ' . implode(', ', $inexistentes);
	
					throw new Exception($msg);
				}

				$pergunta = $this->colecaoPergunta->comId($parametros['pergunta']);
				if(!isset($pergunta) and !($pergunta instanceof Pergunta)){
					throw new Exception("Pergunta não encontrada na base de dados.");
				}
				if($tarefa == null) $tarefa = $this->colecaoChecklist->comPerguntaId($parametros['pergunta']);

				if(!isset($tarefa) and !($tarefa instanceof Checklist)){
					throw new Exception("Checklist não encontrada na base de dados.");
				}

				if($tarefa->getEncerrada()) throw new Exception("Não é possível adicionar respostas para tarefas já encerrada.");
				
				$formularioRespondido->addPergunta($pergunta);

				$resposta = new Resposta(0, \ParamUtil::value($parametros, 'opcaoSelecionada'), \ParamUtil::value($parametros, 'comentario'), $pergunta);

				$this->colecaoResposta->adicionar($resposta);

				if(isset($parametros['files']) and count($parametros['files']) > 0){
					$pastaTarefa = 'pergunta_'. $tarefa->getId();

					foreach($parametros['files'] as $arquivo) {
						$patch = $this->servicoArquivo->validarESalvarImagem($arquivo, $pastaTarefa, 'resposta_' . $resposta->getId());
						$anexo = new Anexo(
							0,
							$patch,
							$arquivo['tipo'],
							$resposta
						);

						$this->colecaoAnexo->adicionar($anexo);
					}
				}
			
				$respostasCadastradas[] = $resposta;	
	
			}

			$this->colecaoFormularioRespondido->adicionar($formularioRespondido);

			$tarefa->setEncerrada(true);

			$this->colecaoChecklist->atualizar($tarefa);

			DB::commit();

			$respostaFront = ['Resposta'=> $respostasCadastradas, 'status' => true, 'mensagem'=> 'Resposta cadastrada com sucesso.']; 
			
		}
		catch (\Exception $e) {
			DB::rollback();

			$respostaFront = ['status' => false, 'mensagem'=> $e->getMessage()]; 
		}

		return $respostaFront;
	}
}
?>