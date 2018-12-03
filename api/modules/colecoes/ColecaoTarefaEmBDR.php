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
			DB::statement('SET FOREIGN_KEY_CHECKS=0;');

			$id = Db::table(self::TABELA)->insertGetId([ 'titulo' => $obj->getTitulo(),
					'descricao' => $obj->getDescricao(),
					'checklist_id' => $obj->getChecklist()->getId(),
					'questionador_id' =>$obj->getQuestionador()->getId()
				]
			);
			
			DB::statement('SET FOREIGN_KEY_CHECKS=0;');

			$obj->setId($id);

			return $obj;
		}
		catch (\Exception $e)
		{
			throw new ColecaoException($e->getMessage(), $e->getCode(), $e);
		}
	}

	function remover($id, $idChecklist) {
		try {	
			DB::statement('SET FOREIGN_KEY_CHECKS=0;');
			$removido = DB::table(self::TABELA)->where('id', $id)->where('checklist_id', $idChecklist)->delete();
			DB::statement('SET FOREIGN_KEY_CHECKS=1;');
			return $removido;
		}
		catch (\Exception $e)
		{
			throw new ColecaoException($e->getMessage(), $e->getCode(), $e);
		}
	}

	function atualizar(&$obj) {
		try {
			
			DB::statement('SET FOREIGN_KEY_CHECKS=0;');

			Db::table(self::TABELA)->where('id', $obj->getId())->update([ 'titulo' => $obj->getTitulo(),
					'descricao' => $obj->getDescricao(),
					'checklist_id' => $obj->getChecklist()->getId(),
					'formulario_respondido_id' => $obj->getFormularioRespondido()->getId()
				]
			);

			DB::statement('SET FOREIGN_KEY_CHECKS=1;');
			return $obj;
		}
		catch (\Exception $e)
		{
			throw new ColecaoException($e->getMessage(), $e->getCode(), $e);
		}
		
	}

	function comId($id){
		try {	
			$tarefa = $this->construirObjeto(DB::table(self::TABELA)->where('id', $id)->get()[0]);

			return $tarefa;
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
			foreach ($tarefas as $tarefa) {
				$tarefasObjects[] =  $this->construirObjeto($tarefa);
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
			$tarefas = Db::table(self::TABELA)->whereIn('id', $ids)->get();
			$tarefasObjects = [];

			foreach ($tarefas as $tarefa) {
				$tarefasObjects[] =  $this->construirObjeto($tarefa);
			}

			return $tarefasObjects;
		}
		catch (\Exception $e)
		{
			throw new ColecaoException($e->getMessage(), $e->getCode(), $e);
		}
	}

	function construirObjeto(array $row) {
		$checklist = ($row['checklist_id'] > 0) ? Dice::instance()->create('ColecaoChecklist')->comId($row['checklist_id']) : '';
		$questionador = ($row['questionador_id'] > 0) ? Dice::instance()->create('ColecaoUsuario')->comId($row['questionador_id']) : '';
		$formularioRespondido = ($row['questionador_id'] > 0) ? Dice::instance()->create('ColecaoUsuario')->comId($row['questionador_id']) : '';


		$tarefa = new Tarefa($row['id'],$row['titulo'], $row['descricao'], $checklist, $questionador, $formularioRespondido);

		return $tarefa;
	}	

    function contagem() {
		return Db::table(self::TABELA)->count();
	}
}

?>