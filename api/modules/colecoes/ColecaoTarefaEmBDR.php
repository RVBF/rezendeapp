<?php
use Illuminate\Database\Capsule\Manager as Db;
/**
 *	Coleção de Tarefa em Banco de Dados Relacional.
 *
 *  @author		Rafael Vinicius Barros Ferreira
 *	@version	0.1
 */

class ColecaoTarefaEmBDR implements ColecaoTarefa {
	const TABELA = 'tarefa';

	function __construct(){}

	function adicionar(&$obj) {
		if($this->validarTarefa($obj)){
			try {	
				DB::statement('SET FOREIGN_KEY_CHECKS=0;');
	
				$id = Db::table(self::TABELA)->insertGetId([ 'titulo' => $obj->getTitulo(),
						'descricao' => $obj->getDescricao(),
						'data_limite' => $obj->getDataLimite(),
						'setor_id' => $obj->getSetor()->getId(),
						'questionador_id' =>$obj->getQuestionador()->getId()
					]
				);
				
				DB::statement('SET FOREIGN_KEY_CHECKS=0;');
	
				$obj->setId($id);
	
				return $obj;
			}
			catch (\Exception $e)
			{
				throw new ColecaoException("Erro ao adicionar tarefa ", $e->getCode(), $e);
			}
		}
	}

	function removerComSetorId($id, $idSetor) {
		if($this->validarRemocaoTarefa($id)){
			try {	
				DB::statement('SET FOREIGN_KEY_CHECKS=0;');
				$removido = DB::table(self::TABELA)->where('id', $id)->where('setor_id', $idSetor)->delete();
				DB::statement('SET FOREIGN_KEY_CHECKS=1;');
				return $removido;
			}
			catch (\Exception $e) {
				throw new ColecaoException("Erro ao remover categoria com o id do checklist.", $e->getCode(), $e);
			}
		}

	}

	function remover($id) {
		if($this->validarRemocaoTarefa($id)){
			try {	
				DB::statement('SET FOREIGN_KEY_CHECKS=0;');
				$removido = DB::table(self::TABELA)->where('id', $id)->delete();
				DB::statement('SET FOREIGN_KEY_CHECKS=1;');
				return $removido;
			}
			catch (\Exception $e)
			{
				throw new ColecaoException("Erro ao remover categoria.", $e->getCode(), $e);
			}
		}

	}

	function atualizar(&$obj) {
		if($this->validarTarefa($obj)) {
			try {
				
				DB::statement('SET FOREIGN_KEY_CHECKS=0;');

				$filds = [ 'titulo' => $obj->getTitulo(),
					'descricao' => $obj->getDescricao(),
					'encerrada' => $obj->getEncerrada(),
					'setor_id' => $obj->getSetor()->getId()
				];
				
				Db::table(self::TABELA)->where('id', $obj->getId())->update($filds);

				DB::statement('SET FOREIGN_KEY_CHECKS=1;');

				return $obj;
			}
			catch (\Exception $e)
			{
				throw new ColecaoException("Erro ao atualizar tarefa.", $e->getCode(), $e);
			}
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

	function todos($limite = 0, $pulo = 0, $idSetor) {
		try {	
			if($idSetor > 0) $tarefas = Db::table(self::TABELA)->where('setor_id', $idSetor)->offset($limite)->limit($pulo)->get();
			else $tarefas = Db::table(self::TABELA)->offset($limite)->limit($pulo)->get();
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
		$setor = ($row['setor_id'] > 0) ? Dice::instance()->create('ColecaoSetor')->comId($row['setor_id']) : '';
		$questionador = ($row['questionador_id'] > 0) ? Dice::instance()->create('ColecaoUsuario')->comId($row['questionador_id']) : '';
		$tarefa = new Tarefa($row['id'],$row['titulo'], $row['descricao'], $row['data_limite'], $row['data_cadastro'], $setor, $questionador, [],($row['encerrada']) ? true : false);

		return $tarefa;
	}	

    function contagem() {
		return Db::table(self::TABELA)->count();
	}

	private function validarTarefa(&$obj) {
		if(!is_string($obj->getTitulo())) throw new ColecaoException('Valor inválido para titulo.');
		
		if(!is_string($obj->getDescricao())) throw new ColecaoException('Valor inválido para a descrição.');

		$quantidade = DB::table(ColecaoUsuarioEmBDR::TABELA)->where('id', $obj->getQuestionador()->getId())->count();

		if($quantidade == 0) throw new ColecaoException('O usuário questionador não foi encontrado na base de dados.');


		$quantidade = DB::table(ColecaoSetorEmBDR::TABELA)->where('id', $obj->getSetor()->getId())->count();

		if($quantidade == 0)throw new ColecaoException('Check questionador não foi encontrado na base de dados.');

		if(strlen($obj->getTitulo()) <= Tarefa::TAM_TITULO_MIM && strlen($obj->getTitulo()) > Tarefa::TAM_TITULO_MAX) throw new ColecaoException('O título deve conter no mínimo '. Tarefa::TAM_TITULO_MIM . ' e no máximo '. Tarefa::TAM_TITULO_MAX . '.');
		if(strlen($obj->getdescricao()) <= 255 and $obj->getdescricao() <> '') throw new ColecaoException('A Descrição  deve conter no máximo '. 255 . ' e no máximo '. 1 . '.');

		return true;
	}

	private function validarRemocaoTarefa($id){
		$quantidade = DB::table(ColecaoPerguntaEmBDR::TABELA)->where('tarefa_id', $id)->count();
		if($quantidade == 0)throw new ColecaoException('Não foi possível excluir a tarefa por que ela possui perguntas relacionadas a ela. Exclua todas as perguntas relacionadas e tente novamente.');
		
	}
}

?>