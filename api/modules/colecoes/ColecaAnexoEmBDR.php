<?php
use Illuminate\Database\Capsule\Manager as DB;
/**
 *	Coleção de Anexo em Banco de Dados Relacional.
 *
 *  @author		Rafael Vinicius Barros Ferreira
 *	@version	1.0
 */

class ColecaoAnexoEmBDR implements ColecaoAnexo
{

	const TABELA = 'anexo';

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
			throw new ColecaoException('Erro ao adiocnar o anexo ao banco de dados!', $e->getCode(), $e);
		}
	}

	function remover($id) {
		try {	
			return DB::table(self::TABELA)->where('id', $id)->delete();
		}
		catch (\Exception $e)
		{
			throw new ColecaoException('Erro ao Remover o anexo do banco de dados!', $e->getCode(), $e);
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
				throw new ColecaoException('Erro aoo atualizar o Anexo no banco dade dados!', $e->getCode(), $e);
			}
		}
		
	}

	function comId($id){
		try {	
			$loja = $this->construirObjeto(DB::table(self::TABELA)->where('id', $id)->get()[0]);

			return (DB::table(self::TABELA)->where('id', $id)->count()) ? $this->construirObjeto(DB::table(self::TABELA)->where('id', $id)->first()) : [];
		}
		catch (\Exception $e)
		{
			throw new ColecaoException('Erro ao buscar anexo com o id no banco de dados!', $e->getCode(), $e);
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
			throw new ColecaoException("Erro ao buscar  anexos no banco de dados!", $e->getCode(), $e);
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
			throw new ColecaoException("Erro ao buscar anexos no banco de dados!", $e->getCode(), $e);
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