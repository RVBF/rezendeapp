<?php
use Illuminate\Database\Capsule\Manager as Db;
/**
 *	Coleção de Tarefa em Banco de Dados Relacional.
 *
 *  @author		Rafael Vinicius Barros Ferreira
 *	@version	0.1
 */

class ColecaoTarefaEmBDR implements ColecaoTarefa
{

	const TABELA = 'tarefa';

	function __construct(){}

	function adicionar(&$obj) {
		try {	

			$id = Db::table(self::TABELA)->insertGetId([ 'titulo' => $obj->getTitulo(),
					'descricao' => $obj->getDescricao(),
					'checklist_id' => $obj->getChecklist()->getId()
				]
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
		try {	

			$id = Db::table(self::TABELA)->where('id', $obj->getId())->update([ 'titulo' => $obj->getTitulo(),
					'descricao' => $obj->getDescricao(),
					'checklist_id' => $obj->getChecklist()->getId()
				]
			);
			
			$obj->setId($id);

			return $obj;
		}
		catch (\Exception $e)
		{
			throw new ColecaoException($e->getMessage(), $e->getCode(), $e);
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
	function todos($limite = 0, $pulo = 0, $idChecklist) {
		try {	
			$tarefas = Db::table(self::TABELA)->where('checklist_id', $idChecklist)->offset($limite)->limit($pulo)->get();

			$tarefasObjects = [];
			foreach ($tarefas as $loja) {
				$tarefasObjects[] =  $this->construirObjeto($loja);
			}

			return $tarefasObjects;
		}
		catch (\Exception $e)
		{
			throw new ColecaoException($e->getMessage(), $e->getCode(), $e);
		}
	}

	function todosComId($ids = []) {
		try {	
			$lojas = Db::table(self::TABELA)->whereIn('id', $ids)->get();
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
		$checklist = Dice::instance()->create('ColecaoChecklist')->comId($row['checklist_id']);

		$tarefa = new Tarefa($row['id'],$row['titulo'], $row['descricao'], $checklist);
		// Debuger::printr($tarefa);

		return $tarefa;
	}	

    function contagem() {
		return Db::table(self::TABELA)->count();
	}
}

?>