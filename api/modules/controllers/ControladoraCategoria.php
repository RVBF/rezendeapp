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
			Debuger::printr($this->colecaoCategoria->todos($dtr->start, $dtr->length));
			$contagem = $this->colecaoCategoria->contagem();

			// $objetos = $this->colecaoCategoria->todos($dtr->limit(), $dtr->offset());

			$resposta = [];
		}
		catch (\Exception $e )
		{
			// return $this->geradoraResposta->erro($e->getMessage(), GeradoraResposta::TIPO_TEXTO);
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