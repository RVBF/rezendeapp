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
	const TABELA_RELACIONAL = 'checklist_tem_loja';

	function __construct(){}

	function adicionar(&$obj) {
		if($this->validarChecklist($obj)){
			try {	
				$id = Db::table(self::TABELA)->insertGetId(['descricao' => $obj->getDescricao(),
					'data_limite'=> $obj->getDataLimite(),
					'categoria_id'=> $obj->getCategoria()->getId()
				]);
				
				$obj->setId($id);

				$itensRelacionais = [];

				foreach ($obj->getLojas() as $loja) {
					$itensRelacionais[] = ['checklist_id' => $obj->getId(), 'loja_id'=> $loja->getId()];
				}

				DB::table(self::TABELA_RELACIONAL)->insert($itensRelacionais);

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
			return DB::table(self::TABELA)->where('id', $id)->sharedLock()->delete();
		}
		catch (\Exception $e)
		{
			throw new ColecaoException($e->getMessage(), $e->getCode(), $e);
		}
	}

	function atualizar(&$obj) {
		if($this->validarChecklist($obj)){
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
			$checklists = Db::table(self::TABELA)->leftJoin(self::TABELA_RELACIONAL, self::TABELA_RELACIONAL . '.checklist_id', '=', self::TABELA . '.id')->offset($limite)->limit($pulo)->distinct()->get();
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
	
	private function validarChecklist(&$obj) {
		if(strlen($obj->getDescricao()) <= Checklist::TAM_MIN_DESCRICAO && strlen($obj->getDescricao()) > Checklist::TAM_MAX_DESCRICAO) throw new ColecaoException('A Descrição deve conter no mínimo '.  Checklist::TAM_MIN_DESCRICAO. ' e no máximo '. Categoria::TAM_MAX_DESCRICAO . '.');

		return true;
	}

}

?>