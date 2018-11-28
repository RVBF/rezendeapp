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
	private $servicoLogin;
	
	function __construct($params,  Sessao $sessao) {
		$this->servicoLogin = new ServicoLogin($sessao);
		$this->params = $params;
		$this->colecaoChecklist = Dice::instance()->create('ColecaoChecklist');
		$this->colecaoCategoria = Dice::instance()->create('ColecaoCategoria');
		$this->colecaoLoja = Dice::instance()->create('ColecaoLoja');

	}

	function todos() {
		try
		{
			if($this->servicoLogin->verificarSeUsuarioEstaLogado() == false) {
				throw new Exception("Erro ao acessar página.");				
			}

			$dtr = new DataTablesRequest($this->params);
			$contagem = 0;
			$objetos = [];
			$erro = null;

			$objetos = $this->colecaoChecklist->todos($dtr->start, $dtr->length);

			$contagem = $this->colecaoChecklist->contagem();
		}
		catch (\Exception $e )
		{
			throw new Exception("Erro ao listar checklist.");
		}

		$conteudo = new DataTablesResponse($contagem,$contagem, $objetos, $dtr->draw, $erro);

		return $conteudo;
	}

	function adicionar() {
		try {
			if($this->servicoLogin->verificarSeUsuarioEstaLogado() == false) {
				throw new Exception("Erro ao acessar página.");				
			}

			$inexistentes = \ArrayUtil::nonExistingKeys(['id', 'descricao','dataLimite','categoria', 'loja'], $this->params);
			$resposta = [];

			if(count($inexistentes) > 0) {
				$msg = 'Os seguintes campos obrigatórios não foram enviados: ' . implode(', ', $inexistentes);

				throw new Exception($msg);
			}
			$categoria = $this->colecaoCategoria->comId(\ParamUtil::value($this->params, 'categoria'));
			if(!isset($categoria) and !($categoria instanceof Categoria)){
				throw new Exception("Categoria não encontrada na base de dados.");
			}

			$loja = $this->colecaoLoja->comId($this->params['loja']);

			if(!count($loja)) throw new Exception("As loja selecionadas não se econtra no banco de dados");
		
			$checklist = new Checklist(
				0,
				\ParamUtil::value($this->params, 'descricao'),
				\ParamUtil::value($this->params, 'dataLimite'),
				'',
				$categoria,
				$loja
			);
			$resposta = ['checklist'=> RTTI::getAttributes($this->colecaoChecklist->adicionar($checklist), RTTI::allFlags()), 'status' => true, 'mensagem'=> 'Checklist cadastrada com sucesso.']; 
		}
		catch (\Exception $e) {
			$resposta = ['status' => false, 'mensagem'=> $e->getMessage()]; 
		}

		return $resposta;
	}

	function atualizar() {
		try {
			if($this->servicoLogin->verificarSeUsuarioEstaLogado() == false) {
				throw new Exception("Erro ao acessar página.");				
			}		
			
			$inexistentes = \ArrayUtil::nonExistingKeys(['id', 'descricao','dataLimite','categoria', 'loja'], $this->params);
			$resposta = [];

			if(count($inexistentes) > 0) {
				$msg = 'Os seguintes campos obrigatórios não foram enviados: ' . implode(', ', $inexistentes);

				throw new Exception($msg);
			}
			$categoria = $this->colecaoCategoria->comId(\ParamUtil::value($this->params, 'categoria'));
			if(!isset($categoria) and !($categoria instanceof Categoria)){
				throw new Exception("Categoria não encontrada na base de dados.");
			}

			$loja = $this->colecaoLoja->comId($this->params['loja']);

			if(!count($loja)) throw new Exception("As loja selecionadas não se econtra no banco de dados");
			
			$checklist = new Checklist(
				\ParamUtil::value($this->params, 'id'),
				\ParamUtil::value($this->params, 'descricao'),
				\ParamUtil::value($this->params, 'dataLimite'),
				'',
				$categoria,
				$loja
			);
			$resposta = ['checklist'=> RTTI::getAttributes($this->colecaoChecklist->atualizar($checklist), RTTI::allFlags()), 'status' => true, 'mensagem'=> 'Checklist atualizada com sucesso.']; 
		}
		catch (\Exception $e) {
			$resposta = ['status' => false, 'mensagem'=> $e->getMessage()]; 
		}

		return $resposta;
	}

	function remover($id) {
		try {
			if($this->servicoLogin->verificarSeUsuarioEstaLogado() == false) {
				throw new Exception("Erro ao acessar página.");				
			}	
			
			$resposta = [];

			$status = $this->colecaoChecklist->remover($id);
			
			$resposta = ['status' => true, 'mensagem'=> 'Checklist removida com sucesso.']; 
		}
		catch (\Exception $e) {
			$resposta = ['status' => false, 'mensagem'=> $e->getMessage()]; 
		}

		return $resposta;
	}
}
?>