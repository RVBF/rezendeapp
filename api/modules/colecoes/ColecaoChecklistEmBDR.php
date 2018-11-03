<?php
use Illuminate\Database\Capsule\Manager as Db;
/**
 *	Coleção de Checklist em Banco de Dados Relacional.
 *
 *  @author		Rafael Vinicius Barros Ferreira
 *	@version	0.1
 */

class ColecaoChecklistEmBDR implements Colecao
{

	const TABELA = 'checklist';

	function __construct(){}

	function adicionar(&$obj) {
		if($this->validarCategoria($obj)){
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
			$categoria = $this->construirObjeto(DB::table(self::TABELA)->where('id', $obj->getId())->get());

			return $categoria;
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
			$checklists = Db::table(self::TABELA)->offset($limite)->limit($pulo)->get();
			$checklistObjects = [];
			// Debuger::printr($checklists);
			foreach ($checklists as $checklist) {
				$checklistObjects[] =  $this->construirObjeto($checklist);
			}

			return $checklistObjects;
		}
		catch (\Exception $e)
		{
			throw new ColecaoException($e->getMessage(), $e->getCode(), $e);
		}
	}

	function construirObjeto(array $row){
		$categoria = Dice::instance()->create('ColecaoCategoria')->comId($row['categoria_id']);

		$checklist = new Checklist($row['id'],$row['descricao'], $row['data_limite'], $row['data_cadastro'],$categoria);
		return $checklist;
	}

    function contagem() {
		return Db::table(self::TABELA)->count();
	}
	
	private function validarCategoria(&$obj)
	{
		if(!is_string($obj->getTitulo()))
		{
			throw new ColecaoException('Valor inválido para bairro.');
		}

		$quantidade = DB::table(self::TABELA)->where('titulo', $obj->getTitulo())->where('id', '<>', $obj->getId())->count();

		if($quantidade > 0){
			throw new ColecaoException('Já exite uma categoria cadastrada com esse título');
		}

		if(strlen($obj->getTitulo()) <= 2 && strlen($obj->getTitulo()) > 85) throw new ColecaoException('O título deve conter no mínimo '. Categoria::TAM_TITULO_MIM . ' e no máximo '. Categoria::TAM_TITULO_MAX . '.');

		return true;
	}

}

?>