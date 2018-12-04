<?php
use phputil\datatables\DataTablesRequest;
use phputil\datatables\DataTablesResponse;
use Symfony\Component\Validator\Validation as Validacao;
use \phputil\JSON;
use \phputil\RTTI;
use Carbon\Carbon;
use Illuminate\Database\Capsule\Manager as Db;


/**
 * Controladora de Resposta
 *
 * @author	Rafael Vinicius Barros Ferreira
 * @version	0.1
 */
class ControladoraResposta {

	private $params;
	private $colecaoPergunta;
	private $colecaoResposta;
	private $colecaoTarefa;
	private $colecaoAnexo;
	private $servicoLogin;
	private $colecaoUsuario;
	private $colecaoFormularioRespondido;
	
	function __construct($params,  Sessao $sessao) {
		$this->params = $params;
		$this->colecaoPergunta = Dice::instance()->create('ColecaoPergunta');
		$this->colecaoResposta = Dice::instance()->create('ColecaoResposta');
		$this->colecaoAnexo = Dice::instance()->create('ColecaoAnexo');
		$this->colecaoTarefa = Dice::instance()->create('ColecaoTarefa');
		$this->colecaoUsuario = Dice::instance()->create('ColecaoUsuario');
		$this->colecaoFormularioRespondido = Dice::instance()->create('ColecaoFormularioRespondido'); 
		$this->servicoArquivo = ServicoArquivo::instance();
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

			$objetos = $this->colecaoResposta->todos($dtr->start, $dtr->length);

			$contagem = $this->colecaoResposta->contagem();
		}
		catch (\Exception $e )
		{
			throw new Exception("Erro ao listar Resposta.");
		}

		$conteudo = new DataTablesResponse(
			$contagem,
			$contagem, //count($objetos ),
			$objetos,
			$dtr->draw,
			$erro
		);

		return $conteudo;
	}

	function adicionar() {
		try {
			if($this->servicoLogin->verificarSeUsuarioEstaLogado() == false) {
				throw new Exception("Erro ao acessar página.");				
			}
			$respostaFront = $respostasCadastradas = [];

			foreach($this->params['obj'] as $key => $parametros) {
				$pergunta = $this->colecaoPergunta->comId($parametros['pergunta']);
				$tarefa = $pergunta->getTarefa();
				$formularioRespondido = new FormularioRespondido();
				$formularioRespondido->setDataHora(Carbon::now());
				$formularioRespondido->setRespondedor($this->colecaoUsuario->comId($this->servicoLogin->getIdUsuario()));

				$formularioRespondido->setPergunta($pergunta);

				$this->colecaoFormularioRespondido->adicionar($formularioRespondido);

				if(!isset($tarefa) and !($tarefa instanceof Tarefa)){
					throw new Exception("Pergunta não encontrada na base de dados.");
				}

				$tarefa->setFormularioRespondido($formularioRespondido);
				$this->colecaoTarefa->atualizar($tarefa);

				$inexistentes = \ArrayUtil::nonExistingKeys(['id', 'opcaoSelecionada','pergunta'], $parametros);

				if(count($inexistentes) > 0) {
					$msg = 'Os seguintes campos obrigatórios da pergunta de id   não foram enviados: ' . implode(', ', $inexistentes);
	
					throw new Exception($msg);
				}

				$resposta = new Resposta(0, \ParamUtil::value($parametros, 'opcaoSelecionada'), '');

				$this->colecaoResposta->adicionarComFormularioID($resposta, $formularioRespondido->getId());

				$formularioRespondido->addResposta($resposta);
				
				if(isset($parametros['files']) and count($parametros['files']) > 0){
					$pastaTarefa = 'tarefa_'. $tarefa->getId();

					foreach($parametros['files'] as $arquivo) {
						$patch = $this->servicoArquivo->validarESalvarImagem($arquivo, $pastaTarefa, $resposta->getId());
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

			$respostaFront = ['Resposta'=> $respostasCadastradas, 'status' => true, 'mensagem'=> 'Resposta cadastrada com sucesso.']; 
			
		}
		catch (\Exception $e) {
			$respostaFront = ['status' => false, 'mensagem'=> $e->getMessage()]; 
		}

		return $respostaFront;
	}
}
?>