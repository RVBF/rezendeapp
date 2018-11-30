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
	private $colecaoAnexo;
	private $servicoLogin;
	
	function __construct($params,  Sessao $sessao) {
		$this->params = $params;
		$this->colecaoPergunta = Dice::instance()->create('ColecaoPergunta');
		$this->colecaoResposta = Dice::instance()->create('ColecaoResposta');
		$this->colecaoAnexo = Dice::instance()->create('ColecaoAnexo');
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
			
			$inexistentes = \ArrayUtil::nonExistingKeys(['id', 'opcao','files', 'pergunta'], $this->params);
			$resposta = [];
			
			if(count($inexistentes) > 0) {
				$msg = 'Os seguintes campos obrigatórios não foram enviados: ' . implode(', ', $inexistentes);

				throw new Exception($msg);
			}

			$categoria = $this->colecaoResposta->comId(\ParamUtil::value($this->params, 'categoria'));
			if(!isset($categoria) and !($categoria instanceof Categoria)){
				throw new Exception("Categoria não encontrada na base de dados.");
			}

			$loja = $this->colecaoAnexo->comId($this->params['loja']);

			if(!count($loja)) throw new Exception("As loja selecionadas não se econtra no banco de dados");
		
			$Resposta = new Resposta(
				0,
				\ParamUtil::value($this->params, 'descricao'),
				\ParamUtil::value($this->params, 'dataLimite'),
				'',
				$categoria,
				$loja
			);
			$resposta = ['Resposta'=> RTTI::getAttributes($this->colecaoPergunta->adicionar($Resposta), RTTI::allFlags()), 'status' => true, 'mensagem'=> 'Resposta cadastrada com sucesso.']; 
		}
		catch (\Exception $e) {
			$resposta = ['status' => false, 'mensagem'=> $e->getMessage()]; 
		}

		return $resposta;
	}
}
?>