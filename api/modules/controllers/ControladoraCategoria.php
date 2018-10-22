<?php
/**
 * Controladora de Categoria
 *
 * @author	Rafael Vinicius Barros Ferreira
 * @version	0.1
 */
class ControladoraCategoria {

	private $params;

	function __construct($params)
	{
		$this->params = $params;
	}

	function todos()
	{
		$dtr = new \DataTablesRequest($this->params);
		$contagem = 0;
		$objetos = [];
		$erro = null;
		try
		{
			$contagem = $this->colecaoCategoria->contagem();

			$objetos = $this->colecaoCategoria->todos($dtr->limit(), $dtr->offset());

			$resposta = [];
		}
		catch (\Exception $e )
		{
			return $this->geradoraResposta->erro($e->getMessage(), GeradoraResposta::TIPO_TEXTO);
		}

		$conteudo = new \DataTablesResponse(
			$contagem,
			$contagem, //count($objetos ),
			$resposta,
			$dtr->draw(),
			$erro
		);

		return $conteudo;
	}
}

?>