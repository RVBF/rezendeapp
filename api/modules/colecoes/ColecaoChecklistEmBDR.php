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
	const TABELA_RELACIONAL = 'checklist_tem_loja';

	function __construct(){}

	function adicionar(&$obj, $lojasRelacionadas) {
		if($this->validarChecklist($obj)){
			try {	

				$id = Db::table(self::TABELA)->insertGetId(['descricao' => $obj->getDescricao(),
					'data_limite'=> $obj->getDataLimite(),
					'categoria_id'=> $obj->getCategoria()->getId()
				]);
				
				$obj->setId($id);

				$lojas = [];
				foreach ($lojasRelacionadas as $loja) {
					$lojas[] =  ['checklist_id' => $obj->getId(), 'loja_id'=> $loja->getId()];
				}

				DB::table(self::TABELA_RELACIONAL)->insert($lojas);
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
			$checklists = Db::table(self::TABELA)->join(self::TABELA_RELACIONAL, self::TABELA_RELACIONAL . '.checklist_id', '=', self::TABELA . '.id')->select(self::TABELA . '.*', self::TABELA_RELACIONAL . '.*')->offset($limite)->limit($pulo)->distinct()->get();
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

	function construirObjeto(array $row){
		$categoria = Dice::instance()->create('ColecaoCategoria')->comId($row['categoria_id']);

		$loja = Dice::instance()->create('ColecaoLoja')->comId($row['loja_id']);
		Debuger::printr($loja);

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