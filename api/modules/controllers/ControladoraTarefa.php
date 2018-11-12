<?php
use phputil\datatables\DataTablesRequest;
use phputil\datatables\DataTablesResponse;
use Symfony\Component\Validator\Validation as Validacao;
use \phputil\JSON;
use \phputil\RTTI;


/**
 * Controladora de Tarefa
 *
 * @author	Rafael Vinicius Barros Ferreira
 * @version	0.1
 */
class ControladoraTarefa {

	private $params;
	private $colecaoTarefa;
	private $colecaoChecklist;
	
	function __construct($params) {
		$this->params = $params;
		$this->colecaoTarefa = Dice::instance()->create('ColecaoTarefa');
		$this->colecaoChecklist = Dice::isntance()->create('ColecaChecklist');
	}

	function todos($idChecklist) {
		$dtr = new DataTablesRequest($this->params);
		$contagem = 0;
		$objetos = [];
		$erro = null;

		try {
			$objetos = $this->colecaoTarefa->todos($dtr->start, $dtr->length, $idChecklist);

			$contagem = $this->colecaoTarefa->contagem();
		}
		catch (\Exception $e ) {
			throw new Exception("Erro ao listar categorias");
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
	
	function adicionar($checklistId) {
		$inexistentes = \ArrayUtil::nonExistingKeys(['titulo', 	'descricao'], $this->params);
		
		if(count($inexistentes) > 0) {
			$msg = 'Os seguintes campos obrigatórios não foram enviados: ' . implode(', ', $inexistentes);

			throw new Exception($msg);
		}
		
		$checklist = $this->colecaoChecklist->comId($checklistId);
		
		if(!isset($checklist) and !($checklist instanceof Checklist)){
			throw new Exception("Checklist não encontrada na base de dados.");
		}
		$tarefa = new Tarefa(
			0,
			\ParamUtil::value($this->params, 'titulo'),
			\ParamUtil::value($this->params, 'descricao'),
			$checklist
		);

		$resposta = [];
		
		try {
			$tarefa = $this->colecaoTarefa->adicionar($tarefa);
			$resposta = ['categoria'=> RTTI::getAttributes( $tarefa, RTTI::allFlags()), 'status' => true, 'mensagem'=> 'Categoria cadastrada com sucesso.']; 
		}
		catch (\Exception $e) {
			$resposta = ['status' => false, 'mensagem'=> $e->getMessage()]; 
		}

		return $resposta;
	}

}
?>