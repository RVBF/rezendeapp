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
	private $colecaoLoja;
	
	function __construct($params,  Sessao $sessao) {
		$this->params = $params;
		$this->colecaoLoja = Dice::instance()->create('ColecaoLoja');
	}

	function todos() {
		$dtr = new DataTablesRequest($this->params);
		$contagem = 0;
		$objetos = [];
		$erro = null;

		try
		{
			$objetos = $this->colecaoLoja->todos($dtr->start, $dtr->length);

			$contagem = $this->colecaoLoja->contagem();
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
		$inexistentes = \ArrayUtil::nonExistingKeys(['id', 'razaoSocial','nomeFantasia'], $this->params);
		$resposta = [];

		try {
			if(count($inexistentes) > 0) {
				$msg = 'Os seguintes campos obrigatórios não foram enviados: ' . implode(', ', $inexistentes);

				throw new Exception($msg);
			}

			$loja = new Loja(
				0,
				\ParamUtil::value($this->params, 'razaoSocial'),
				\ParamUtil::value($this->params, 'nomeFantasia')
			);

			$resposta = ['loja'=> RTTI::getAttributes($this->colecaoLoja->adicionar($loja), RTTI::allFlags()), 'status' => true, 'mensagem'=> 'Loja cadastrada com sucesso.']; 
		}
		catch (\Exception $e) {
			$resposta = ['status' => false, 'mensagem'=> $e->getMessage()]; 
		}

		return $resposta;
	}

	function atualizar() {
		$inexistentes = \ArrayUtil::nonExistingKeys(['id', 'razaoSocial','nomeFantasia'], $this->params);
		$resposta = [];

		try {
			if(count($inexistentes) > 0) {
				$msg = 'Os seguintes campos obrigatórios não foram enviados: ' . implode(', ', $inexistentes);

				throw new Exception($msg);
			}

			$loja = new Loja(
				\ParamUtil::value($this->params, 'id'),
				\ParamUtil::value($this->params, 'razaoSocial'),
				\ParamUtil::value($this->params, 'nomeFantasia')
			);

			$resposta = ['loja'=> RTTI::getAttributes($this->colecaoLoja->atualizar($loja), RTTI::allFlags()), 'status' => true, 'mensagem'=> 'Loja atualizada com sucesso.']; 
		}
		catch (\Exception $e) {
			$resposta = ['status' => false, 'mensagem'=> $e->getMessage()]; 
		}

		return $resposta;
	}
	
	
	function remover($id) {
		$resposta = [];

		try {
			$status = $this->colecaoLoja->remover($id);
			
			$resposta = ['status' => $status, 'mensagem'=> 'Loja removida com sucesso.']; 
		}
		catch (\Exception $e) {
			$resposta = ['status' => false, 'mensagem'=> $e->getMessage()]; 
		}

		return $resposta;
	}
}
?>