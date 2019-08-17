<?php
use phputil\datatables\DataTablesRequest;
use phputil\datatables\DataTablesResponse;
use Symfony\Component\Validator\Validation as Validacao;
use \phputil\JSON;
use \phputil\RTTI;
use Carbon\Carbon;
use Illuminate\Database\Capsule\Manager as DB;

/**
 * Controladora de Setor
 *
 * @author	Rafael Vinicius Barros Ferreira
 * @version	0.1
 */
class ControladoraSetor {

	private $params;
	private $colecaoSetor;
	private $colecaoCategoria;
	private $colecaoLoja;
	private $servicoLogin;
	
	function __construct($params,  Sessao $sessao) {
		$this->servicoLogin = new ServicoLogin($sessao);
		$this->params = $params;
		$this->colecaoSetor = Dice::instance()->create('ColecaoSetor');
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

			$objetos = $this->colecaoSetor->todos($dtr->start, $dtr->length, (isset($dtr->search->value)) ? $dtr->search->value : '');

			$contagem = $this->colecaoSetor->contagem();
		}
		catch (\Exception $e )
		{
			throw new Exception("Erro ao listar setor.");
		}

		$conteudo = new DataTablesResponse($contagem,$contagem, $objetos, $dtr->draw, $erro);

		return $conteudo;
	}

	function adicionar() {
		DB::beginTransaction();

		try {
			if($this->servicoLogin->verificarSeUsuarioEstaLogado() == false) {
				throw new Exception("Erro ao acessar página.");				
			}

			if(!$this->servicoLogin->eAdministrador()){
				throw new Exception("Usuário sem permissão para executar ação.");
			}

			$inexistentes = \ArrayUtil::nonExistingKeys(['id', 'titulo','descricao','categoria'], $this->params);
			$resposta = [];

			if(is_countable($inexistentes) ? count($inexistentes) > 0 : 0) {
				$msg = 'Os seguintes campos obrigatórios não foram enviados: ' . implode(', ', $inexistentes);

				throw new Exception($msg);
			}

			$categoria = $this->colecaoCategoria->comId(\ParamUtil::value($this->params, 'categoria'));

			if(!isset($categoria) and !($categoria instanceof Categoria)){
				throw new Exception("Categoria não encontrada na base de dados.");
			}

			$setor = new Setor(
				0,
				\ParamUtil::value($this->params, 'titulo'),
				\ParamUtil::value($this->params, 'descricao'),
				$categoria
			);
			$resposta = ['setor'=> RTTI::getAttributes($this->colecaoSetor->adicionar($setor), RTTI::allFlags()), 'status' => true, 'mensagem'=> 'Setor cadastrado com sucesso.']; 
			
			DB::commit();

		}
		catch (\Exception $e) {
			DB::rollback();

			$resposta = ['status' => false, 'mensagem'=> $e->getMessage()]; 
		}

		return $resposta;
	}

	function atualizar() {
		DB::beginTransaction();

		try {
			if($this->servicoLogin->verificarSeUsuarioEstaLogado() == false) {
				throw new Exception("Erro ao acessar página.");				
			}		
			
			if(!$this->servicoLogin->eAdministrador()){
				throw new Exception("Usuário sem permissão para executar ação.");
			}

			$inexistentes = \ArrayUtil::nonExistingKeys(['id', 'titulo','descricao'], $this->params);
			$resposta = [];

			if(is_countable($inexistentes) ? count($inexistentes) > 0 : 0) {
				$msg = 'Os seguintes campos obrigatórios não foram enviados: ' . implode(', ', $inexistentes);

				throw new Exception($msg);
			}
			$categoria = $this->colecaoCategoria->comId(\ParamUtil::value($this->params, 'categoria'));
			if(!isset($categoria) and !($categoria instanceof Categoria)){
				throw new Exception("Categoria não encontrada na base de dados.");
			}
			
			$setor = new Setor(
				\ParamUtil::value($this->params, 'id'),
				\ParamUtil::value($this->params, 'titulo'),
				\ParamUtil::value($this->params, 'descricao'),
				$categoria
			);
			$resposta = ['setor'=> RTTI::getAttributes($this->colecaoSetor->atualizar($setor), RTTI::allFlags()), 'status' => true, 'mensagem'=> 'Setor atualizado com sucesso.']; 
			DB::commit();
		}
		catch (\Exception $e) {
			DB::rollback();

			$resposta = ['status' => false, 'mensagem'=> $e->getMessage()]; 
		}

		return $resposta;
	}

	function remover($id) {
		DB::beginTransaction();

		try {
			if($this->servicoLogin->verificarSeUsuarioEstaLogado() == false) {
				throw new Exception("Erro ao acessar página.");				
			}	

			if(!$this->servicoLogin->eAdministrador()){
				throw new Exception("Usuário sem permissão para executar ação.");
			}

			$resposta = [];

			$status = $this->colecaoSetor->remover($id);
			
			$resposta = ['status' => true, 'mensagem'=> 'Setor removido com sucesso.']; 
			DB::commit();

		}
		catch (\Exception $e) {
			DB::rollback();

			$resposta = ['status' => false, 'mensagem'=> $e->getMessage()]; 
		}

		return $resposta;
	}
}
?>