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

		
	function adicionarTodas($tarefaId) {
		$inexistentes = \ArrayUtil::nonExistingKeys(['data'], $this->params);
		
		if(count($inexistentes) > 0) {
			$msg = 'Os seguintes campos obrigatórios não foram enviados: ' . implode(', ', $inexistentes);

			throw new Exception($msg);
		}

		$tarefa = $this->colecaoTarefa->comId($tarefaId);

		if(!isset($tarefa) and !($tarefa instanceof Tarefa)){
			throw new Exception("Checklist não encontrada na base de dados.");
		}
 
		$objetos = [];

		foreach ($this->params['data'] as $obj) {

			$objetos[] = new Pergunta(
				$obj['id'],
				$obj['pergunta'],
				null, 
				null,
				$tarefa
			);
		}

		$resposta = [];
		
		try {

			$objetos = $this->colecaoPergunta->adicionarTodas($objetos);
			$resposta = ['perguntas'=> $objetos, 'status' => true, 'mensagem'=> 'Pergunta cadastrada com sucesso.']; 
		}
		catch (\Exception $e) {
			$resposta = ['status' => false, 'mensagem'=> $e->getMessage()]; 
		}

		return $resposta;
	}

	function atualizar($tarefaId) {
		$inexistentes = \ArrayUtil::nonExistingKeys(['id', 'pergunta'], $this->params);
		
		if(count($inexistentes) > 0) {
			$msg = 'Os seguintes campos obrigatórios não foram enviados: ' . implode(', ', $inexistentes);

			throw new Exception($msg);
		}

		$tarefa = $this->colecaoTarefa->comId($tarefaId);

		if(!isset($tarefa) and !($tarefa instanceof Tarefa)){
			throw new Exception("Checklist não encontrada na base de dados.");
		}

		$pergunta = new Pergunta(
			\ParamUtil::value($this->params, 'id'),
            \ParamUtil::value($this->params, 'pergunta'),
            null, 
            null,
            $tarefa
		);

		$resposta = [];
		
		try {

			$pergunta = $this->colecaoPergunta->atualizar($pergunta);
			$resposta = ['pergunta'=> RTTI::getAttributes( $pergunta, RTTI::allFlags()), 'status' => true, 'mensagem'=> 'Pergunta atualizada com sucesso.']; 
		}
		catch (\Exception $e) {
			$resposta = ['status' => false, 'mensagem'=> $e->getMessage()]; 
		}

		return $resposta;
	}
	function comTarefaId($tarefaId){
		$dtr = new DataTablesRequest($this->params);
		$contagem = 0;
		$objetos = [];
		$erro = null;

		try {
            $objetos = $this->colecaoPergunta->todos($dtr->start, $dtr->length, $tarefaId);

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

	function remover($id, $tarefaId) {
		$resposta = [];

		try {
			$status = $this->colecaoPergunta->remover($id, $tarefaId);
			
			$resposta = ['status' => true, 'mensagem'=> 'Categoria removida com sucesso.']; 
		}
		catch (\Exception $e) {
			$resposta = ['status' => false, 'mensagem'=> $e->getMessage()]; 
		}

		return $resposta;
	}
}
?>