<?php
use phputil\datatables\DataTablesRequest;
use phputil\datatables\DataTablesResponse;
use Symfony\Component\Validator\Validation as Validacao;
use \phputil\JSON;
use \phputil\RTTI;


/**
 * Controladora de Loja
 *
 * @author	Rafael Vinicius Barros Ferreira
 * @version	0.1
 */
class ControladoraLoja {

	private $params;
	private $colecaoCategoria;
	
	function __construct($params) {
		$this->params = $params;
		$this->colecaoCategoria = Dice::instance()->create('ColecaoCategoria');
	}

	function todos() {
		$dtr = new DataTablesRequest($this->params);
		$contagem = 0;
		$objetos = [];
		$erro = null;

		try
		{
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
		$inexistentes = \ArrayUtil::nonExistingKeys(['titulo'], $this->params);

		$categoria = new Categoria(
			0,
			\ParamUtil::value($this->params, 'titulo')
		);

		$resposta = [];
		
		try {
			$categoria = $this->colecaoCategoria->adicionar($categoria);
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
	
			$this->colecaoCategoria->atualizar($categoria);
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
			$status = $this->colecaoCategoria->remover($id);
			
			$resposta = ['status' => true, 'mensagem'=> 'Categoria removida com sucesso.']; 
		}
		catch (\Exception $e) {
			$resposta = ['status' => false, 'mensagem'=> $e->getMessage()]; 
		}

		return $resposta;
	}
}
?>