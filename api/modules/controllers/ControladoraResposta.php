<?php
use phputil\datatables\DataTablesRequest;
use phputil\datatables\DataTablesResponse;
use Symfony\Component\Validator\Validation as Validacao;
use \phputil\JSON;
use \phputil\RTTI;
use Carbon\Carbon;

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
	
	function __construct($params,  Sessao $sessao) {
		$this->params = $params;
		$this->colecaoPergunta = Dice::instance()->create('ColecaoPergunta');
		$this->colecaoResposta = Dice::instance()->create('ColecaoResposta');
		$this->colecaoAnexo = Dice::instance()->create('ColecaoAnexo');
		$this->colecaoTarefa = Dice::instance()->create('ColecaoTarefa');
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
			$objetos = $this->colecaoPergunta->todos($dtr->start, $dtr->length);

			$contagem = $this->colecaoPergunta->contagem();
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
			$resposta = [];

			foreach($this->params['obj'] as $key => $parametros){
				$pergunta = $this->colecaoPergunta->comId($parametros['pergunta']);
				Debuger::printr($pergunta);

				if(!isset($pergunta) and !($pergunta instanceof pergunta)){
					throw new Exception("Pergunta não encontrada na base de dados.");
				}

				$inexistentes = \ArrayUtil::nonExistingKeys(['id', 'opcaoSelecionada','pergunta'], $parametros);

				if(count($inexistentes) > 0) {
					$msg = 'Os seguintes campos obrigatórios da pergunta de id   não foram enviados: ' . implode(', ', $inexistentes);
	
					throw new Exception($msg);
				}

				$resposta = new Resposta(0, \ParamUtil::value($parametros, 'opcaoSelecionada'), '', $pergunta);

				$this->colecaoResposta->adicionar($resposta);

				$pergunta->setResposta($resposta);

				$this->colecaoPergunta->atualizar($pergunta);

				if(isset($parametros['files'])){
					$pastaPergunta = 'pergunta_'. $pergunta->getId();

					foreach($parametros['files'] as $arquivo) {
						$patch = $this->servicoArquivo->validarESalvarImagem($arquivo, $pastaPergunta);
						$anexo = new Anexo(
							0,
							$patch,
							$arquivo['tipo'],
							$resposta
						);

						$this->colecaoAnexo->adicionar($anexo);
					}
				}
			}


			// $loja = $this->colecaoAnexo->comId($this->params['loja']);

			// if(!count($loja)) throw new Exception("As loja selecionadas não se econtra no banco de dados");
		
			// $Resposta = new Resposta(
			// 	0,
			// 	\ParamUtil::value($this->params, 'descricao'),
			// 	\ParamUtil::value($this->params, 'dataLimite'),
			// 	'',
			// 	$categoria,
			// 	$loja
			// );
			// $resposta = ['Resposta'=> RTTI::getAttributes($this->colecaoPergunta->adicionar($Resposta), RTTI::allFlags()), 'status' => true, 'mensagem'=> 'Resposta cadastrada com sucesso.']; 
				
		}
		catch (\Exception $e) {
			$resposta = ['status' => false, 'mensagem'=> $e->getMessage()]; 
		}

		return $resposta;
	}
}
?>