<?php
use phputil\datatables\DataTablesRequest;
use phputil\datatables\DataTablesResponse;
use Symfony\Component\Validator\Validation as Validacao;
use \phputil\JSON;
use \phputil\RTTI;


/**
 * Controladora de Pergunta
 *
 * @author	Rafael Vinicius Barros Ferreira
 * @version	0.1
 */
class ControladoraPergunta {

	private $params;
	private $colecaoPergunta;
	private $colecaoTarefa;
	
	function __construct($params) {
		$this->params = $params;
		$this->colecaoPergunta = Dice::instance()->create('ColecaoPergunta');
		$this->colecaoTarefa = Dice::instance()->create('ColecaoTarefa');
	}

	function todos($idTarefa) {
		$dtr = new DataTablesRequest($this->params);
		$contagem = 0;
		$objetos = [];
		$erro = null;

		try {
            $objetos = $this->colecaoPergunta->todos($dtr->start, $dtr->length, $idTarefa);

			$contagem = $this->colecaoPergunta->contagem();
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
	
	function adicionar($tarefaId) {
		$inexistentes = \ArrayUtil::nonExistingKeys(['pergunta'], $this->params);
		
		if(count($inexistentes) > 0) {
			$msg = 'Os seguintes campos obrigatórios não foram enviados: ' . implode(', ', $inexistentes);

			throw new Exception($msg);
		}

		$tarefa = $this->colecaoTarefa->comId($tarefaId);

		if(!isset($tarefa) and !($tarefa instanceof Tarefa)){
			throw new Exception("Checklist não encontrada na base de dados.");
		}

		$pergunta = new Pergunta(
			0,
            \ParamUtil::value($this->params, 'pergunta'),
            null, 
            null,
            $tarefa
		);

		$resposta = [];
		
		try {

			$pergunta = $this->colecaoPergunta->adicionar($pergunta);
			$resposta = ['pergunta'=> RTTI::getAttributes( $pergunta, RTTI::allFlags()), 'status' => true, 'mensagem'=> 'Pergunta cadastrada com sucesso.']; 
		}
		catch (\Exception $e) {
			$resposta = ['status' => false, 'mensagem'=> $e->getMessage()]; 
		}

		return $resposta;
	}

	function atualizar($checklistId) {
		$inexistentes = \ArrayUtil::nonExistingKeys(['id', 'titulo', 	'descricao'], $this->params);
		
		if(count($inexistentes) > 0) {
			$msg = 'Os seguintes campos obrigatórios não foram enviados: ' . implode(', ', $inexistentes);

			throw new Exception($msg);
		}

		$checklist = $this->colecaoTarefa->comId($checklistId);

		if(!isset($checklist) and !($checklist instanceof Checklist)){
			throw new Exception("Checklist não encontrada na base de dados.");
		}

		$tarefa = new Tarefa(
			\ParamUtil::value($this->params, 'id'),
			\ParamUtil::value($this->params, 'titulo'),
			\ParamUtil::value($this->params, 'descricao'),
			$checklist
		);

		$resposta = [];
		
		try {

			$tarefa = $this->colecaoPergunta->atualizar($tarefa);
			$resposta = ['categoria'=> RTTI::getAttributes( $tarefa, RTTI::allFlags()), 'status' => true, 'mensagem'=> 'Categoria cadastrada com sucesso.']; 
		}
		catch (\Exception $e) {
			$resposta = ['status' => false, 'mensagem'=> $e->getMessage()]; 
		}

		return $resposta;
	}

	function remover($id, $idChecklist) {
		$resposta = [];

		try {
			$status = $this->colecaoPergunta->remover($id, $idChecklist);
			
			$resposta = ['status' => true, 'mensagem'=> 'Categoria removida com sucesso.']; 
		}
		catch (\Exception $e) {
			$resposta = ['status' => false, 'mensagem'=> $e->getMessage()]; 
		}

		return $resposta;
	}
}
?>