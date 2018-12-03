<?php
use Illuminate\Database\Capsule\Manager as Db;
/**
 *	Coleção de Pergunta em Banco de Dados Relacional.
 *
 *  @author		Rafael Vinicius Barros Ferreira
 *	@version	0.1
 */

class ColecaoPerguntaEmBDR implements ColecaoPergunta {

	const TABELA = 'pergunta';

	function __construct(){}

	function adicionar(&$obj) {
		try {	
			DB::statement('SET FOREIGN_KEY_CHECKS=0;');

			$id = Db::table(self::TABELA)->insertGetId([ 'pergunta' => $obj->getPergunta(),
					'tarefa_id' => $obj->getTarefa()->getId(),
				]
			);

			DB::statement('SET FOREIGN_KEY_CHECKS=1;');

			$obj->setId($id);

			return $obj;
		}
		catch (\Exception $e)
		{
			throw new ColecaoException($e->getMessage(), $e->getCode(), $e);
		}
	}

	function adicionarTodas(&$objs){
		try {	
			DB::statement('SET FOREIGN_KEY_CHECKS=0;');

			foreach ($objs as $key => $obj) {
				$id = Db::table(self::TABELA)->insertGetId([ 'pergunta' => $obj->getPergunta(), 'tarefa_id' => $obj->getTarefa()->getId()]);
				$obj->setId($id);
				$objs[$key] = $obj;
			}

			DB::statement('SET FOREIGN_KEY_CHECKS=1;');


			return $objs;
		}
		catch (\Exception $e) {
			throw new ColecaoException($e->getMessage(), $e->getCode(), $e);
		}
	}

	function remover($id, $idTarefa) {
		try {	
			DB::statement('SET FOREIGN_KEY_CHECKS=0;');
			$removido = DB::table(self::TABELA)->where('id', $id)->where('tarefa_id', $idTarefa)->delete();
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

			Db::table(self::TABELA)->where('id', $obj->getId())->update([ 'pergunta' => $obj->getPergunta(),
				'tarefa_id' => $obj->getTarefa()->getId(),
				'resposta_id' => $obj->getResposta()->getId()
			]);

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
			$pergunta = $this->construirObjeto(DB::table(self::TABELA)->where('id', $id)->get()[0]);

			return $pergunta;
		}
		catch (\Exception $e)
		{
			throw new ColecaoException($e->getMessage(), $e->getCode(), $e);
		}
	}

	/**
	 * @inheritDoc
	 */
	function todos($limite = 0, $pulo = 0, $idTarefa) {
		try {	
			$perguntas = Db::table(self::TABELA)->where('tarefa_id', $idTarefa)->offset($limite)->limit($pulo)->get();
			$perguntasObjects = [];
		
			foreach ($perguntas as $key => $pergunta) {

				$perguntasObjects[] =  $this->construirObjeto($pergunta);
			}

			return $perguntasObjects;
		}
		catch (\Exception $e)
		{
			throw new ColecaoException($e->getMessage(), $e->getCode(), $e);
		}
	}

	function todosComId($ids = []) {
		try {	
			$perguntas = Db::table(self::TABELA)->whereIn('id', $ids)->get();
			$perguntasObjects = [];

			foreach ($perguntas as $pergunta) {
				$perguntasObjects[] =  $this->construirObjeto($pergunta);
			}

			return $perguntasObjects;
		}
		catch (\Exception $e)
		{
			throw new ColecaoException($e->getMessage(), $e->getCode(), $e);
		}
	}


	function comTarefaId($limite = 0, $pulo = 0, $tarefaId){
		try {	
			$perguntas = Db::table(self::TABELA)->where('tarefa_id', $tarefaId)->offset($limite)->limit($pulo)->get();

			$perguntasObjects = [];
			foreach ($perguntas as $pergunta) {
				$perguntasObjects[] =  $this->construirObjeto($pergunta);
			}

			return $perguntasObjects;
		}
		catch (\Exception $e)
		{
			throw new ColecaoException($e->getMessage(), $e->getCode(), $e);
		}
	}

	function construirObjeto(array $row) {
		$tarefa = Dice::instance()->create('ColecaoTarefa')->comId($row['tarefa_id']);

		$pergunta = new Pergunta($row['id'],$row['pergunta'], $tarefa);

		return $pergunta;
	}	

    function contagem() {
		return Db::table(self::TABELA)->count();
	}
}

?>