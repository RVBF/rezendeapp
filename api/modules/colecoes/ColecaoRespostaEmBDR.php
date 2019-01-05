<?php
use Illuminate\Database\Capsule\Manager as DB;
/**
 *	Coleção de Resposta em Banco de Dados Relacional.
 *
 *  @author		Rafael Vinicius Barros Ferreira
 *	@version	0.1
 */

class ColecaoRespostaEmBDR implements ColecaoResposta
{

	const TABELA = 'resposta';

	function __construct(){}

	function adicionar(&$obj) {
		try {	
			$id = DB::table(self::TABELA)->insertGetId([ 'opcaoSelecionada' => $obj->getOpcaoSelecionada(), 'comentario' => $obj->getComentario(), 'pergunta_id' => $obj->getPergunta()->getId()]);

			$obj->setId($id);

			return $obj;
		}
		catch (\Exception $e)
		{
			throw new ColecaoException("Erro ao cadastrar resposta.", $e->getCode(), $e);
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
		try {
			
			DB::statement('SET FOREIGN_KEY_CHECKS=0;');

			DB::table(self::TABELA)->where('id', $obj->getId())->update([ 'opcaoSelecionada' => $obj->getOpcaoSelecionada(), 'comentario' => $obj->getComentario(), 'pergunta_id' => $obj->getPergunta()->getId()]);

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
            $resposta = $this->construirObjeto(DB::table(self::TABELA)->where('id', $id)->get()[0]);

			return $resposta;
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
			$perguntas = DB::table(self::TABELA)->offset($limite)->limit($pulo)->get();
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

	function comPerguntaId($id = 0){
		try {
            $resposta = (DB::table(self::TABELA)->where('pergunta_id', $id)->count() > 0) ? $this->construirObjeto(DB::table(self::TABELA)->where('pergunta_id', $id)->get()[0]) : [];

			return $resposta;
		}
		catch (\Exception $e)
		{
			throw new ColecaoException($e->getMessage(), $e->getCode(), $e);
		}
	}

	function  todosComTarefaId($limite = 0, $pulo = 0, $tarefaid = 0){
		try {	
			$respostas = DB::table(self::TABELA)->select(self::TABELA . '.*')
			->join(ColecaoPerguntaEmBDR::TABELA, ColecaoPerguntaEmBDR::TABELA . '.id', '=', self::TABELA . '.pergunta_id')
			->join(ColecaoTarefaEmBDR::TABELA, ColecaoTarefaEmBDR::TABELA . '.id', '=', ColecaoPerguntaEmBDR::TABELA . '.tarefa_id')
			->where(ColecaoTarefaEmBDR::TABELA .'.id', $tarefaid)
			->offset($limite)
			->limit($pulo)
			->get();

			$respostasObjects = [];

			foreach ($respostas as $pergunta) {
				$respostasObjects[] =  $this->construirObjeto($pergunta);
			}

			return $respostasObjects;
		}
		catch (\Exception $e) {
			throw new ColecaoException($e->getMessage(), $e->getCode(), $e);
		}
	}

	function construirObjeto(array $row) {
		$pergunta  = ($row['pergunta_id'] > 0 ) ? Dice::instance()->create('ColecaoPergunta')->comId($row['pergunta_id']) : null;
		$anexos = Dice::instance()->create('ColecaoAnexo')->comRespostaId($row['id']);	
		$resposta = new Resposta($row['id'], $row['opcaoSelecionada'], $row['comentario'], $pergunta, $anexos);

		return $resposta;
	}	

    function contagem($tarefaId = 0) {
		return ($tarefaId > 0) ? DB::table(self::TABELA)->select(self::TABELA . '.*')
		->join(ColecaoPerguntaEmBDR::TABELA, ColecaoPerguntaEmBDR::TABELA . '.id', '=', self::TABELA . '.pergunta_id')
		->join(ColecaoTarefaEmBDR::TABELA, ColecaoTarefaEmBDR::TABELA . '.id', '=', ColecaoPerguntaEmBDR::TABELA . '.tarefa_id')
		->where(ColecaoTarefaEmBDR::TABELA .'.id', $tarefaId)
		->count() : DB::table(self::TABELA)->count();
	}
}

?>