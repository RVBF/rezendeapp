<?php
use Illuminate\Database\Capsule\Manager as DB;
/**
 *	Coleção de Pergunta em Banco de Dados Relacional.
 *
 *  @author		Rafael Vinicius Barros Ferreira
 *	@version	1.0
 */

class ColecaoPerguntaEmBDR implements ColecaoPergunta {

	const TABELA = 'pergunta';
	const TABELA_RELACIONAL = 'resposta_formulariorespondido';

	function __construct(){}

	function adicionar(&$obj) {
		try {	
			if($this->validarPergunta($obj)){
				DB::statement('SET FOREIGN_KEY_CHECKS=0;');
			
				$id = DB::table(self::TABELA)->insertGetId(['pergunta' => $obj->getPergunta(), 'tarefa_id' => $obj->getTarefa()->getId()]);
	
				DB::statement('SET FOREIGN_KEY_CHECKS=1;');
	
				$obj->setId($id);
	
				return $obj;
			}
			
		}
		catch (\Exception $e)
		{
			throw new ColecaoException($e->getMessage(), $e->getCode(), $e);
		}
	}

	function adicionarTodas(&$objs){

		try {	
			DB::statement('SET FOREIGN_KEY_CHECKS=0;');

			$perguntas = [];
			foreach ($objs as $key => $obj) {
				if($this->validarPergunta($obj)){
					$perguntas[] = [ 'pergunta' => $obj->getPergunta(), 'questionario_id' => $obj->getQuestionario()->getId()];
				}
			}

			DB::table(self::TABELA)->insert($perguntas);

			DB::statement('SET FOREIGN_KEY_CHECKS=1;');


			return $objs;
		}
		catch (\Exception $e) {
			throw new ColecaoException($e->getMessage(), $e->getCode(), $e);
		}
	}

	function remover($id, $idTarefa) {
		try {	
			if($this->validarRemocaoPergunta($id)){

				DB::statement('SET FOREIGN_KEY_CHECKS=0;');
				$removido = DB::table(self::TABELA)->where('id', $id)->where('tarefa_id', $idTarefa)->delete();
				DB::statement('SET FOREIGN_KEY_CHECKS=1;');
				return $removido;
			}
		}
		catch (\Exception $e)
		{
			throw new ColecaoException($e->getMessage(), $e->getCode(), $e);
		}
	}

	function atualizar(&$obj) {
		try {
			if($this->validarPergunta($obj)){
			
				DB::statement('SET FOREIGN_KEY_CHECKS=0;');

				DB::table(self::TABELA)->where('id', $obj->getId())->update([ 'pergunta' => $obj->getPergunta(), 'tarefa_id' => $obj->getTarefa()->getId()]);

				DB::statement('SET FOREIGN_KEY_CHECKS=1;');
				return $obj;
			}
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
	function todos($limite = 0, $pulo = 0, $search = '', $idTarefa =  0, $idsLojas = []) {

		try {
			$query = DB::table(self::TABELA)->select(self::TABELA . '.*')->where(self::TABELA .'.tarefa_id', $idTarefa);

			if($search != '') {
				$buscaCompleta = $search;
				$palavras = explode(' ', $buscaCompleta);

				$query->rightJoin(ColecaoRespostaEmBDR::TABELA, self::TABELA .'.id', '=', ColecaoRespostaEmBDR::TABELA .'.pergunta_id');
				$query->rightJoin(ColecaoChecklistEmBDR::TABELA, self::TABELA .'.tarefa_id', '=', ColecaoChecklistEmBDR::TABELA .'.id');

				$query->where(function($query) use ($buscaCompleta){
					$query->whereRaw(self::TABELA . '.id like "%' . $buscaCompleta . '%"');
					$query->orWhereRaw(self::TABELA . '.pergunta like "%' . $buscaCompleta . '%"');
					$query->orWhereRaw(ColecaoRespostaEmBDR::TABELA . '.comentario like "%' . $buscaCompleta . '%"');
					$query->orWhereRaw(ColecaoChecklistEmBDR::TABELA . '.titulo like "%' . $buscaCompleta . '%"');
				});
			
				
				if($query->count() == 0){
					$query->where(function($query) use ($palavras){
						foreach ($palavras as $key => $palavra) {
							if($palavra != " "){
								$query->whereRaw(self::TABELA . '.id like "%' . $palavra . '%"');
								$query->orWhereRaw(self::TABELA . '.pergunta like "%' . $palavra . '%"');
								$query->orWhereRaw(ColecaoRespostaEmBDR::TABELA . '.comentario like "%' . $palavra . '%"');
								$query->orWhereRaw(ColecaoChecklistEmBDR::TABELA . '.titulo like "%' . $palavra . '%"');
							}
						}
						
					});
				}

			}

			$perguntas = $query->offset($limite)->limit($pulo)->get();
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
			$perguntas = DB::table(self::TABELA)->whereIn('id', $ids)->get();
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


	function comTarefaId($tarefaId){
		try {	
			$perguntas = DB::table(self::TABELA)->where('tarefa_id', $tarefaId)->get();

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

	function comFormularioId($id){
		try {	
			$perguntas = DB::table(self::TABELA)->select(self::TABELA . '.*')->join(self::TABELA_RELACIONAL, self::TABELA.'.id', '=', self::TABELA . 'pergunta_id')->where(self::TABELA_RELACIONAL . '.formulario_respondido_id', $id)->get();

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
		$formularioRespondido = Dice::instance()->create('ColecaoFormularioRespondido')->comPerguntaId($row['id']);

		$pergunta = new Pergunta($row['id'],$row['pergunta'], null,$formularioRespondido);

		return $pergunta;
	}	

    function contagem($tarefaId = 0) {
		return ($tarefaId > 0) ? DB::table(self::TABELA)->where('tarefa_id', $tarefaId)->count() : DB::table(self::TABELA)->count();
	}

	private function validarPergunta(&$obj) {
		if(!is_string($obj->getPergunta())) throw new ColecaoException('Valor inválido para titulo.');

		if(strlen($obj->getPergunta()) <= 2 && strlen($obj->getTitulo()) > 255) throw new ColecaoException('A pergunta deve conter no mínimo 2 e no máximo 255.');
		
		// $quantidade = DB::table(self::TABELA)->whereRaw('pergunta like  "%'. $obj->getPergunta() . '%"')->where('tarefa_id', $obj->getTarefa()->getId())->where(self::TABELA . '.id', '<>', $obj->getId())->count();
		
		// if($quantidade > 0){
		// 	throw new ColecaoException('Já exite uma tarefa cadastrada com esse título.');
		// }

		return true;
	}

	private function validarRemocaoPergunta($id){
		return true;
	}
}
?>