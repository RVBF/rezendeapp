<?php
use phputil\datatables\DataTablesRequest;
use phputil\datatables\DataTablesResponse;
use Symfony\Component\Validator\Validation as Validacao;
use \phputil\JSON;
use \phputil\RTTI;


/**
 * Controladora de Checklist
 *
 * @author	Rafael Vinicius Barros Ferreira
 * @version	0.1
 */
class ControladoraChecklist {

	private $params;
	private $colecaoChecklist;
	
	function __construct($params) {
		$this->params = $params;
		$this->colecaoChecklist = Dice::instance()->create('Colecao');
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
		$inexistentes = \ArrayUtil::nonExistingKeys(['titulo'], $this->params);

		$categoria = new Categoria(
			0,
			\ParamUtil::value($this->params, 'titulo')
		);

		$resposta = [];
		
		try {
			$categoria = $this->colecaoChecklist->adicionar($categoria);
			$resposta = ['categoria'=> RTTI::getAttributes( $categoria, RTTI::allFlags()), 'status' => true, 'mensagem'=> 'Categoria cadastrada com sucesso.']; 
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