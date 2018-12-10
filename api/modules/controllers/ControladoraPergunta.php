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
	private $servicoLogin;
	
	function __construct($params,  Sessao $sessao) {
		$this->params = $params;
		$this->colecaoPergunta = Dice::instance()->create('ColecaoPergunta');
		$this->colecaoTarefa = Dice::instance()->create('ColecaoTarefa');
		$this->servicoLogin = new ServicoLogin($sessao);
	}

	function todos($idTarefa) {
		try {
			if($this->servicoLogin->verificarSeUsuarioEstaLogado() == false) {
				throw new Exception("Erro ao acessar página.");				
			}

			$dtr = new DataTablesRequest($this->params);
			$contagem = 0;
			$objetos = [];
			$erro = null;
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
		try {

			if($this->servicoLogin->verificarSeUsuarioEstaLogado() == false) {
				throw new Exception("Erro ao acessar página.");				
			}
			$inexistentes = \ArrayUtil::nonExistingKeys(['pergunta'], $this->params);
			
			if(count($inexistentes) > 0) {
				$msg = 'Os seguintes campos obrigatórios não foram enviados: ' . implode(', ', $inexistentes);

				throw new Exception($msg);
			}

			$tarefa = $this->colecaoTarefa->comId($tarefaId);

			if(!isset($tarefa) and !($tarefa instanceof Tarefa)){
				throw new Exception("Setor não encontrada na base de dados.");
			}

			$pergunta = new Pergunta(
				0,
				\ParamUtil::value($this->params, 'pergunta'),
				$tarefa
			);

			$resposta = [];
			
			$pergunta = $this->colecaoPergunta->adicionar($pergunta);
			$resposta = ['pergunta'=> RTTI::getAttributes( $pergunta, RTTI::allFlags()), 'status' => true, 'mensagem'=> 'Pergunta cadastrada com sucesso.']; 
		}
		catch (\Exception $e) {
			$resposta = ['status' => false, 'mensagem'=> $e->getMessage()]; 
		}

		return $resposta;
	}

		
	function adicionarTodas($tarefaId) {

		try {
			if($this->servicoLogin->verificarSeUsuarioEstaLogado() == false) {
				throw new Exception("Erro ao acessar página.");				
			}
			$inexistentes = \ArrayUtil::nonExistingKeys(['data'], $this->params);
			
			if(count($inexistentes) > 0) {
				$msg = 'Os seguintes campos obrigatórios não foram enviados: ' . implode(', ', $inexistentes);

				throw new Exception($msg);
			}

			$tarefa = $this->colecaoTarefa->comId($tarefaId);

			if(!isset($tarefa) and !($tarefa instanceof Tarefa)){
				throw new Exception("Setor não encontrada na base de dados.");
			}
	
			$objetos = [];

			foreach ($this->params['data'] as $obj) {

				$objetos[] = new Pergunta(
					$obj['id'],
					$obj['pergunta'],
					$tarefa
				);
			}

			$resposta = [];
			
			$objetos = $this->colecaoPergunta->adicionarTodas($objetos);
			$resposta = ['perguntas'=> $objetos, 'status' => true, 'mensagem'=> 'Pergunta cadastrada com sucesso.']; 
		}
		catch (\Exception $e) {
			$resposta = ['status' => false, 'mensagem'=> $e->getMessage()]; 
		}

		return $resposta;
	}

	function atualizar($tarefaId) {

		try {
			if($this->servicoLogin->verificarSeUsuarioEstaLogado() == false) {
				throw new Exception("Erro ao acessar página.");				
			}		

			$inexistentes = \ArrayUtil::nonExistingKeys(['id', 'pergunta'], $this->params);
			
			if(count($inexistentes) > 0) {
				$msg = 'Os seguintes campos obrigatórios não foram enviados: ' . implode(', ', $inexistentes);

				throw new Exception($msg);
			}

			$tarefa = $this->colecaoTarefa->comId($tarefaId);

			if(!isset($tarefa) and !($tarefa instanceof Tarefa)){
				throw new Exception("Setor não encontrada na base de dados.");
			}

			$pergunta = new Pergunta(
				\ParamUtil::value($this->params, 'id'),
				\ParamUtil::value($this->params, 'pergunta'),
				$tarefa
			);

			$resposta = [];
			
			$pergunta = $this->colecaoPergunta->atualizar($pergunta);
			$resposta = ['pergunta'=> RTTI::getAttributes( $pergunta, RTTI::allFlags()), 'status' => true, 'mensagem'=> 'Pergunta atualizada com sucesso.']; 
		}
		catch (\Exception $e) {
			$resposta = ['status' => false, 'mensagem'=> $e->getMessage()]; 
		}

		return $resposta;
	}
	function comTarefaId($tarefaId){
		try {
			if($this->servicoLogin->verificarSeUsuarioEstaLogado() == false) {
				throw new Exception("Erro ao acessar página.");				
			}

			$dtr = new DataTablesRequest($this->params);
			$contagem = 0;
			$objetos = [];
			$erro = null;

            $objetos = $this->colecaoPergunta->todos($dtr->start, $dtr->length, $tarefaId);

			$contagem = $this->colecaoPergunta->contagem();
		}
		catch (\Exception $e ) {
			throw new Exception("Erro ao listar Pergunta");
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

	function comIdPergunta($id){
		$resposta = [];
		try {
			if($this->servicoLogin->verificarSeUsuarioEstaLogado() == false) {
				throw new Exception("Erro ao acessar página.");				
			}
			$resposta = [];
			
			$pergunta = $this->colecaoPergunta->comId($id);

			$resposta = ['pergunta'=> RTTI::getAttributes($pergunta, RTTI::allFlags()), 'status' => true, 'mensagem'=> 'Pergunta  encontrada com sucesso.']; 
		}
		catch (\Exception $e ) {
			$resposta = ['status' => false, 'mensagem'=> $e->getMessage()]; 
		}

		return $resposta;
	}
	function remover($id, $tarefaId) {
		try {
			if($this->servicoLogin->verificarSeUsuarioEstaLogado() == false) {
				throw new Exception("Erro ao acessar página.");				
			}
			$resposta = [];

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