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

	function __construct(){}

	function adicionar(&$obj) {
		try {	
			$id = Db::table(self::TABELA)->insertGetId(
				['titulo' => $obj->getTitulo()]
			);
			
			$obj->setId($id);

			return $obj;
		}
		catch (\Exception $e)
		{
			throw new ColecaoException($e->getMessage(), $e->getCode(), $e);
		}
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
	function todos($limite = 0, $pulo = 0) {
		try {	
			$categorias = Db::table(self::TABELA)->orderBy('titulo', 'DESC')->offset($limite)->limit($pulo)->get();
			$categoriasObjects = [];

			foreach ($categorias as $categoria) {
				$categoriasObjects[] =  $this->construirObjeto($categoria);
			}

			return $categoriasObjects;
		}
		catch (\Exception $e)
		{
			throw new ColecaoException($e->getMessage(), $e->getCode(), $e);
		}
	}

	function construirObjeto(array $row){
		return new Categoria($row['id'],$row['titulo']);
	}

    function contagem() {
		return Db::table(self::TABELA)->count();
    }
}

?>