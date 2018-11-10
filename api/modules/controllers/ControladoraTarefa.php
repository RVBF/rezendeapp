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
	private $colecaoLoja;
	
	function __construct($params) {
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
}
?>