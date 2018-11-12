<?php
use Illuminate\Database\Capsule\Manager as Db;
/**
 *	Coleção de Checklist em Banco de Dados Relacional.
 *
 *  @author		Rafael Vinicius Barros Ferreira
 *	@version	0.1
 */

class ColecaoChecklistEmBDR implements ColecaoChecklist
{

	const TABELA = 'checklist';

	function __construct(){}

	function adicionar(&$obj) {
		if($this->validarChecklist($obj)){
			try {	

				$id = Db::table(self::TABELA)->insertGetId(['descricao' => $obj->getDescricao(),
					'data_limite'=> $obj->getDataLimite(),
					'categoria_id'=> $obj->getCategoria()->getId(),
					'loja_id'=> $obj->getLoja()->getId()
				]);
				
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
			DB::statement('SET FOREIGN_KEY_CHECKS=0;');

			$removido = DB::table(self::TABELA)->where('id', $id)->delete();
			
			DB::statement('SET FOREIGN_KEY_CHECKS=1;');

			return $removido;

		}
		catch (\Exception $e)
		{
			throw new ColecaoException($e->getMessage(), $e->getCode(), $e);
		}
	}

	function atualizar(&$obj) {
		if($this->validarChecklist($obj)){
			try {
				DB::statement('SET FOREIGN_KEY_CHECKS=0;');

				DB::table(self::TABELA)->where('id', $obj->getId())->update(['descricao' => $obj->getDescricao(),
					'data_limite'=> $obj->getDataLimite(),
					'categoria_id'=> $obj->getCategoria()->getId(),
					'loja_id'=> $obj->getLoja()->getId()
				]);

				DB::statement('SET FOREIGN_KEY_CHECKS=1;');
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
			$categoria = $this->construirObjeto(DB::table(self::TABELA)->where('id', $id)->get()[0]);

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
			$checklists = Db::table(self::TABELA)->select(self::TABELA . '.*')->offset($limite)->limit($pulo)->get();

			$checklistObjects = [];
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

	function construirObjeto(array $row) {
		$categoria = Dice::instance()->create('ColecaoCategoria')->comId($row['categoria_id']);
		$loja = Dice::instance()->create('ColecaoLoja')->comId($row['loja_id']);
		$checklist = new Checklist($row['id'],$row['descricao'], $row['data_limite'], $row['data_cadastro'], $categoria, $loja);
		return $checklist;
	}

    function contagem() {
		return Db::table(self::TABELA)->count();
	}
	
	private function validarChecklist(&$obj) {
		if(strlen($obj->getDescricao()) <= Checklist::TAM_MIN_DESCRICAO && strlen($obj->getDescricao()) > Checklist::TAM_MAX_DESCRICAO) throw new ColecaoException('A Descrição deve conter no mínimo '.  Checklist::TAM_MIN_DESCRICAO. ' e no máximo '. Categoria::TAM_MAX_DESCRICAO . '.');

		return true;
	}

}

?>