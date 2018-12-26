<?php
use phputil\datatables\DataTablesRequest;
use phputil\datatables\DataTablesResponse;
use Symfony\Component\Validator\Validation as Validacao;
use \phputil\JSON;
use \phputil\RTTI;
use Illuminate\Database\Capsule\Manager as DB;



/**
 * Controladora de Categoria
 *
 * @author	Rafael Vinicius Barros Ferreira
 * @version	0.1
 */
class ControladoraCategoria {

	private $params;
	private $colecaoCategoria;
	private $servicoLogin;

	function __construct($params,  Sessao $sessao) {
		$this->params = $params;
		$this->servicoLogin = new ServicoLogin($sessao);
		$this->colecaoCategoria = Dice::instance()->create('ColecaoCategoria');
	}

	function todos() {
		try {
			if($this->servicoLogin->verificarSeUsuarioEstaLogado() == false) {
				throw new Exception("Erro ao acessar página.");				
			}
	
			$dtr = new DataTablesRequest($this->params);
			$contagem = 0;
			$objetos = [];
			$erro = null;

			$objetos = $this->colecaoCategoria->todos($dtr->start, $dtr->length);

			$contagem = $this->colecaoCategoria->contagem();
		}
		catch (\Exception $e )
		{
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

	function adicionar() {
		DB::beginTransaction();

		try {

			if($this->servicoLogin->verificarSeUsuarioEstaLogado() == false) {
				throw new Exception("Erro ao acessar página.");				
			}
	
			$inexistentes = \ArrayUtil::nonExistingKeys(['titulo'], $this->params);
	
			$categoria = new Categoria(
				0,
				\ParamUtil::value($this->params, 'titulo')
			);
	
			$resposta = [];

			$categoria = $this->colecaoCategoria->adicionar($categoria);
			DB::commit();

			$resposta = ['categoria'=> RTTI::getAttributes( $categoria, RTTI::allFlags()), 'status' => true, 'mensagem'=> 'Categoria cadastrada com sucesso.']; 
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

			$inexistentes = \ArrayUtil::nonExistingKeys(['id', 'titulo'], $this->params);

			$resposta = [];

			if (count($inexistentes) > 0) {
				$msg = 'Os seguintes campos não foram enviados: ' . implode(', ', $inexistentes);
				throw new Exception($msg);
			}
	
	
			$categoria = new Categoria(
				\ParamUtil::value($this->params, 'id'),
				\ParamUtil::value($this->params, 'titulo')
			);
	
			$this->colecaoCategoria->atualizar($categoria);
			
			DB::commit();

			$resposta = ['categoria'=> RTTI::getAttributes( $categoria, RTTI::allFlags()), 'status' => true, 'mensagem'=> 'Categoria atualizada com sucesso.']; 
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
			$resposta = [];
			
			$status = $this->colecaoCategoria->remover($id);
			DB::commit();

			$resposta = ['status' => true, 'mensagem'=> 'Categoria removida com sucesso.']; 
		}
		catch (\Exception $e) {
			DB::rollback();

			$resposta = ['status' => false, 'mensagem'=> $e->getMessage()]; 
		}

		return $resposta;
	}
}
?>