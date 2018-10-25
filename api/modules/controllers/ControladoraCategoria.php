<?php
use phputil\datatables\DataTablesRequest;
use phputil\datatables\DataTablesResponse;

/**
 * Controladora de Categoria
 *
 * @author	Rafael Vinicius Barros Ferreira
 * @version	0.1
 */
class ControladoraCategoria {

	private $params;
	private $colecaoCategoria;
	
	function __construct($params)
	{
		$this->params = $params;
		$this->colecaoCategoria = Dice::instance()->create('ColecaoCategoria');
	}

	function todos()
	{
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
}

?>