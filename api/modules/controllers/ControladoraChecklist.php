<?php
use phputil\datatables\DataTablesRequest;
use phputil\datatables\DataTablesResponse;
use Symfony\Component\Validator\Validation as Validacao;
use \phputil\JSON;
use \phputil\RTTI;
use Carbon\Carbon;

/**
 * Controladora de Checklist
 *
 * @author	Rafael Vinicius Barros Ferreira
 * @version	0.1
 */
class ControladoraChecklist {

	private $params;
	private $colecaoChecklist;
	private $colecaoCategoria;
	private $colecaoLoja;
	function __construct($params) {
		$this->params = $params;
		$this->colecaoChecklist = Dice::instance()->create('ColecaoChecklist');
		$this->colecaoCategoria = Dice::instance()->create('ColecaoCategoria');
		$this->colecaoLoja = Dice::instance()->create('ColecaoLoja');

	}

	function todos() {
		$dtr = new DataTablesRequest($this->params);
		$contagem = 0;
		$objetos = [];
		$erro = null;
		try
		{
			$objetos = $this->colecaoChecklist->todos($dtr->start, $dtr->length);

			$contagem = $this->colecaoChecklist->contagem();
		}
		catch (\Exception $e )
		{
			throw new Exception("Erro ao listar checklist.");
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
		$inexistentes = \ArrayUtil::nonExistingKeys(['id', 'descricao','dataLimite','categoria', 'lojas'], $this->params);
		$resposta = [];

		try {
			if(count($inexistentes) > 0) {
				$msg = 'Os seguintes campos obrigatórios não foram enviados: ' . implode(', ', $inexistentes);

				throw new Exception($msg);
			}
			$categoria = $this->colecaoCategoria->comId(\ParamUtil::value($this->params, 'categoria'));
			if(!isset($categoria) and !($categoria instanceof Categoria)){
				throw new Exception("Categoria não encontrada na base de dados.");
			}

			$lojasObject = $this->colecaoLoja->todosComId($this->params['lojas']);

			if(!count($lojasObject)) throw new Exception("As lojas selecionadas não se econtra no banco de dados");
			$checklist = new Checklist(
				0,
				\ParamUtil::value($this->params, 'descricao'),
				\ParamUtil::value($this->params, 'dataLimite'),
				'',
				$categoria,
				$lojasObject
			);

			$checklist = $this->colecaoChecklist->adicionar($checklist);
			$resposta = ['checklist'=> RTTI::getAttributes( $checklist, RTTI::allFlags()), 'status' => true, 'mensagem'=> 'Checklist cadastrada com sucesso.']; 
		}
		catch (\Exception $e) {
			$resposta = ['status' => false, 'mensagem'=> $e->getMessage()]; 
		}

		return $resposta;
	}

	function atualizar() {
		$inexistentes = \ArrayUtil::nonExistingKeys(['id', 'titulo'], $this->params);

		$resposta = [];
		
		try {
			if (count($inexistentes) > 0) {
				$msg = 'Os seguintes campos não foram enviados: ' . implode(', ', $inexistentesb);
				throw new Exception($msg);
			}
	
	
			$categoria = new Categoria(
				\ParamUtil::value($this->params, 'id'),
				\ParamUtil::value($this->params, 'titulo')
			);
	
			$this->colecaoChecklist->atualizar($categoria);
			$resposta = ['categoria'=> RTTI::getAttributes( $categoria, RTTI::allFlags()), 'status' => true, 'mensagem'=> 'Categoria atualizada com sucesso.']; 
		}
		catch (\Exception $e) {
			$resposta = ['status' => false, 'mensagem'=> $e->getMessage()]; 
		}

		return $resposta;
	}

	function remover($id) {
		$resposta = [];

		try {
			$status = $this->colecaoChecklist->remover($id);
			
			$resposta = ['status' => true, 'mensagem'=> 'Categoria removida com sucesso.']; 
		}
		catch (\Exception $e) {
			$resposta = ['status' => false, 'mensagem'=> $e->getMessage()]; 
		}

		return $resposta;
	}
}
?>