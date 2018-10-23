<?php
use Illuminate\Database\Capsule\Manager as Db;
/**
 *	Coleção de Categoria em Banco de Dados Relacional.
 *
 *  @author		Rafael Vinicius Barros Ferreira
 *	@version	0.1
 */

class ColecaoCategoriaEmBDR implements ColecaoCategoria
{

	const TABELA = 'categoria';

	private $db;

	function __construct(Db $db)
	{
		$this->db = $db;
	}

	function adicionar(&$obj) {
	}

	function remover($id) {
	}

	function atualizar(&$obj) {
	}

	function comId($id){
	}

	/**
	 * @inheritDoc
	 */
	function todos($limite = 0, $pulo = 0){
		try
		{
			Debuger::printr($this->db->table(self::TABELA)->offset(10)
			->limit(5)
			->get());

			return $this->pdoW->queryObjects([$this, 'construirObjeto'], $sql);
		}
		catch (\Exception $e)
		{
			throw new ColecaoException($e->getMessage(), $e->getCode(), $e);
		}
	}

	function construirObjeto(array $row){
	}

    function contagem() {
    }
}

?>