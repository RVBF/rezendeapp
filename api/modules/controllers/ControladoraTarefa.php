<?php
use phputil\datatables\DataTablesRequest;
use phputil\datatables\DataTablesResponse;
use Symfony\Component\Validator\Validation as Validacao;
use \phputil\JSON;
use \phputil\RTTI;
use Carbon\Carbon;


/**
 * Controladora de Tarefa
 *
 * @author	Rafael Vinicius Barros Ferreira
 * @version	0.1
 */
class ControladoraTarefa {

	private $params;
	private $colecaoTarefa;
	private $colecaoSetor;
	private $servicoLogin;
	private $colecaoUsuario;
	private $colecaoLoja;

	function __construct($params,  Sessao $sessao) {
		$this->params = $params;
		$this->servicoLogin = new ServicoLogin($sessao);
		$this->colecaoTarefa = Dice::instance()->create('ColecaoTarefa');
		$this->colecaoSetor = Dice::instance()->create('ColecaoSetor');
		$this->colecaoUsuario = Dice::instance()->create('ColecaoUsuario');
		$this->colecaoLoja = Dice::instance()->create('ColecaoLoja');
	}

	function todos($idSetor = 0) {
		try {
			if($this->servicoLogin->verificarSeUsuarioEstaLogado() == false) {
				throw new Exception("Erro ao acessar página.");				
			}

			$dtr = new DataTablesRequest($this->params);
			$contagem = 0;
			$objetos = [];
			$erro = null;
			$objetos = $this->colecaoTarefa->todos($dtr->start, $dtr->length, $idSetor);

			$contagem = $this->colecaoTarefa->contagem();
		}
		catch (\Exception $e ) {
			throw new Exception("Erro ao listar tarefas");
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
	
	function adicionar($setorId = 0) {
		try {

			if($this->servicoLogin->verificarSeUsuarioEstaLogado() == false) {
				throw new Exception("Erro ao acessar página.");				
			}

			$inexistentes = \ArrayUtil::nonExistingKeys(['titulo', 'descricao', 'dataLimite', 'setor', 'loja'], $this->params);

			if(count($inexistentes) > 0) {
				$msg = 'Os seguintes campos obrigatórios não foram enviados: ' . implode(', ', $inexistentes);
	
				throw new Exception($msg);
			}
	
			$setor = $this->colecaoSetor->comId(($setorId> 0) ? $setorId : \ParamUtil::value($this->params, 'setor'));

			if(!isset($setor) and !($setor instanceof Setor)){
				throw new Exception("Setor não encontrada na base de dados.");
			}

				
			$loja = $this->colecaoLoja->comId((\ParamUtil::value($this->params, 'loja')> 0) ? \ParamUtil::value($this->params, 'loja') : \ParamUtil::value($this->params, 'loja'));

			if(!isset($loja) and !($loja instanceof Loja)){
				throw new Exception("Loja não encontrada na base de dados.");
			}
			$dataLimite = new Carbon();                  // equivalent to Carbon::now()
			$dataLimite = new Carbon(\ParamUtil::value($this->params, 'dataLimite'), 'America/Sao_Paulo');

			$tarefa = new Tarefa(
				0,
				\ParamUtil::value($this->params, 'titulo'),
				\ParamUtil::value($this->params, 'descricao'),
				$dataLimite,
				'',
				$setor,
				$loja,
				$this->colecaoUsuario->comId($this->servicoLogin->getIdUsuario())

			);
	
			$resposta = [];

			$tarefa = $this->colecaoTarefa->adicionar($tarefa);
			$resposta = ['categoria'=> RTTI::getAttributes( $tarefa, RTTI::allFlags()), 'status' => true, 'mensagem'=> 'Categoria cadastrada com sucesso.']; 
		}
		catch (\Exception $e) {
			$resposta = ['status' => false, 'mensagem'=> $e->getMessage()]; 
		}

		return $resposta;
	}

	function atualizar($setorId = 0) {
		try {
			if($this->servicoLogin->verificarSeUsuarioEstaLogado() == false) {
				throw new Exception("Erro ao acessar página.");				
			}

			$inexistentes = \ArrayUtil::nonExistingKeys(['id', 'titulo', 'descricao', 'dataLimite', 'setor', 'loja'], $this->params);
		
			if(count($inexistentes) > 0) {
				$msg = 'Os seguintes campos obrigatórios não foram enviados: ' . implode(', ', $inexistentes);
	
				throw new Exception($msg);
			}
	
			$setor = $this->colecaoSetor->comId((\ParamUtil::value($this->params, 'loja')> 0) ? \ParamUtil::value($this->params, 'loja') : \ParamUtil::value($this->params, 'descricao'));
	
			if(!isset($setor) and !(setor instanceof Setor)){
				throw new Exception("Setor não encontrada na base de dados.");
			}

				
			$loja = $this->colecaoLoja->comId((\ParamUtil::value($this->params, 'loja')> 0) ? \ParamUtil::value($this->params, 'loja') : \ParamUtil::value($this->params, 'loja'));

			if(!isset($loja) and !($loja instanceof Loja)){
				throw new Exception("Loja não encontrada na base de dados.");
			}

			$dataLimite = new Carbon();                  // equivalent to Carbon::now()
			$dataLimite = new Carbon(\ParamUtil::value($this->params, 'dataLimite'), 'America/Sao_Paulo');

			$tarefa = new Tarefa(
				\ParamUtil::value($this->params, 'id'),
				\ParamUtil::value($this->params, 'titulo'),
				\ParamUtil::value($this->params, 'descricao'),
				$dataLimite,
				'',
				$setor,
				$loja
			);
	
			$resposta = [];
					
			$tarefa = $this->colecaoTarefa->atualizar($tarefa);
			$resposta = ['categoria'=> RTTI::getAttributes( $tarefa, RTTI::allFlags()), 'status' => true, 'mensagem'=> 'Categoria cadastrada com sucesso.']; 
		}
		catch (\Exception $e) {
			$resposta = ['status' => false, 'mensagem'=> $e->getMessage()]; 
		}

		return $resposta;
	}

	function remover($id, $idSetor = 0) {
		try {
			if($this->servicoLogin->verificarSeUsuarioEstaLogado() == false) {
				throw new Exception("Erro ao acessar página.");				
			}
			
			$resposta = [];

			$status = ($idSetor > 0) ? $this->colecaoTarefa->removerComSetorId($id, $idSetor) :  $this->colecaoTarefa->remover($id);
			
			$resposta = ['status' => true, 'mensagem'=> 'Categoria removida com sucesso.']; 
		}
		catch (\Exception $e) {
			$resposta = ['status' => false, 'mensagem'=> $e->getMessage()]; 
		}

		return $resposta;
	}
}
?>