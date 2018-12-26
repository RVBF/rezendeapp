<?php
use Illuminate\Database\Capsule\Manager as DB;
/**
 *	Coleção de Resposta em Banco de Dados Relacional.
 *
 *  @author		Rafael Vinicius Barros Ferreira
 *	@version	0.1
 */

class ColecaRespostaEmBDR implements ColecaResposta
{

	const TABELA = 'resposta';

	function __construct(){}

	function adicionar(&$obj) {
		try {	
			$id = DB::table(self::TABELA)->insertGetId(
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
		try {	
			return DB::table(self::TABELA)->where('id', $id)->delete();
		}
		catch (\Exception $e)
		{
			throw new ColecaoException($e->getMessage(), $e->getCode(), $e);
		}
	}

	function atualizar(&$obj) {
		if($this->validarCategoria($obj)){
			try {	
				DB::table(self::TABELA)->where('id', $obj->getId())->update(['titulo' => $obj->getTitulo()]);

				return $obj;
			}
			catch (\Exception $e)
			{
				throw new ColecaoException($e->getMessage(), $e->getCode(), $e);
			}
		}
		
	}

	function comId($id){
		try {	
			$loja = $this->construirObjeto(DB::table(self::TABELA)->where('id', $id)->get()[0]);

			return $loja;
		}
		catch (\Exception $e)
		{
			throw new ColecaoException($e->getMessage(), $e->getCode(), $e);
		}
	}

	/**
	 * @inheritDoc
	 */
	function todos($limite = 0, $pulo = 0) {
		try {	
			$lojas = DB::table(self::TABELA)->offset($limite)->limit($pulo)->get();
			$lojasObjects = [];
			foreach ($lojas as $loja) {
				$lojasObjects[] =  $this->construirObjeto($loja);
			}

			return $lojasObjects;
		}
		catch (\Exception $e)
		{
			throw new ColecaoException($e->getMessage(), $e->getCode(), $e);
		}
	}

	function todosComId($ids = []) {
		try {	
			$lojas = DB::table(self::TABELA)->whereIn('id', $ids)->get();
			$lojasObjects = [];
			foreach ($lojas as $loja) {
				$lojasObjects[] =  $this->construirObjeto($loja);
			}

			return $lojasObjects;
		}
		catch (\Exception $e)
		{
			throw new ColecaoException($e->getMessage(), $e->getCode(), $e);
		}
	}

	function construirObjeto(array $row) {
		$loja = new Loja($row['id'],$row['razaoSocial'], $row['nomeFantasia']);

		return $loja;
	}	

    function contagem() {
		return DB::table(self::TABELA)->count();
	}
}

?>