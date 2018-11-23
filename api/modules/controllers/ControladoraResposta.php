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
	private $colecaoTarefa;
	private $colecaoResposta;
	private $colecaoAnexo;
	
	function __construct($params,  Sessao $sessao) {
		$this->params = $params;
		$this->colecaoTarefa = Dice::instance()->create('ColecaoTarefa');
		$this->colecaoResposta = Dice::instance()->create('ColecaoResposta');
		$this->colecaoAnexo = Dice::instance()->create('ColecaoAnexo');

	}

	function todos() {
		$dtr = new DataTablesRequest($this->params);
		$contagem = 0;
		$objetos = [];
		$erro = null;
		try
		{
			$objetos = $this->colecaoTarefa->todos($dtr->start, $dtr->length);

			$contagem = $this->colecaoTarefa->contagem();
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
		$inexistentes = \ArrayUtil::nonExistingKeys(['id', 'descricao','dataLimite','categoria', 'loja'], $this->params);
		$resposta = [];

		try {
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
			$resposta = ['Resposta'=> RTTI::getAttributes($this->colecaoTarefa->adicionar($Resposta), RTTI::allFlags()), 'status' => true, 'mensagem'=> 'Resposta cadastrada com sucesso.']; 
		}
		catch (\Exception $e) {
			$resposta = ['status' => false, 'mensagem'=> $e->getMessage()]; 
		}

		return $resposta;
	}
}
?>