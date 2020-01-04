<?php
use Illuminate\Database\Capsule\Manager as DB;
use Carbon\Carbon;

/**
 *	Coleção de Questionario em Banco de Dados Relacional.
 *
 *  @author		Rafael Vinicius Barros Ferreira
 *	@version	1.0
 */

class ColecaoQuestionarioEmBDR implements ColecaoQuestionario {
	const TABELA = 'questionario';

	function __construct(){}

	function adicionar(&$obj) {
		if($this->validarQuestionario($obj)){
			try {	
				$id = DB::table(self::TABELA)->insertGetId([
					'titulo' => $obj->getTitulo(),
					'descricao'=> $obj->getDescricao(),
					'tipoQuestionario' => $obj->getTipoQuestionario(),
					'formulario' => $obj->getFormulario()
				]);

				$obj->setId($id);
			}
			catch (\Exception $e) {
				throw new ColecaoException("Erro ao cadastrar Questionario.", $e->getCode(), $e);
			}
		}
	}

	function remover($id) {
		if($this->validarDeleteQuestionario($id)){
			try {	
				DB::table(self::TABELA)->where('deleted_at', NULL)->where('id', $id)->update(['deleted_at' => Carbon::now()->toDateTimeString()]);
			}
			catch (\Exception $e) {
				throw new ColecaoException("Erro ao remover Questionario.", $e->getCode(), $e);
			}
		}
	}

	function atualizar(&$obj) {
		if($this->validarQuestionario($obj)){
			try {
				DB::table(self::TABELA)->where('deleted_at', NULL)->where('id', $obj->getId())->update([
					'titulo' => $obj->getTitulo(),
					'descricao'=> $obj->getDescricao(),
					'tipoQuestionario' => $obj->getTipoQuestionario(),
					'formulario' => $obj->getFormulario()
				]);
			}
			catch (\Exception $e){
				throw new ColecaoException("Erro ao atualizar Questionario.", $e->getCode(), $e);
			}
		}
		
	}

	function comId($id){
		try {
			return (DB::table(self::TABELA)->where('id', $id)->count()) ? $this->construirObjeto(DB::table(self::TABELA)->where('id', $id)->first()) : [];
		}
		catch (\Exception $e) {
			throw new ColecaoException("Erro ao buscar Questionario.", $e->getCode(), $e);
		}
	}

	/**
	 * @inheritDoc
	 */
	function todos($limite = 0, $pulo = 0, $search = '') {
		try {	
			$query = DB::table(self::TABELA)->select(self::TABELA . '.*')->where(self::TABELA .'.deleted_at', NULL);

			if($search != '') {
				$buscaCompleta = $search;
				$palavras = explode(' ', $buscaCompleta);

				$query->where(function($query) use ($buscaCompleta){
					$query->whereRaw(self::TABELA . '.id like "%' . $buscaCompleta . '%"');
					$query->orWhereRaw(self::TABELA . '.titulo like "%' . $buscaCompleta . '%"');
					$query->orWhereRaw(self::TABELA . '.descricao like "%' . $buscaCompleta . '%"');
					$query->orWhereRaw(self::TABELA . '.tipoQuestionario like "%' . $buscaCompleta . '%"');
				});
				
				
				if($query->count() == 0){
					$query->where(function($query) use ($palavras){
						foreach ($palavras as $key => $palavra) {
							if($palavra != " "){
								$query->whereRaw(self::TABELA . '.id like "%' . $palavra . '%"');
								$query->orWhereRaw(self::TABELA . '.titulo like "%' . $palavra . '%"');
								$query->orWhereRaw(self::TABELA . '.descricao like "%' . $palavra . '%"');
								$query->orWhereRaw(self::TABELA . '.tipoQuestionario like "%' . $palavra . '%"');
							}
						}
						
					});
				}
				$query->groupBy(self::TABELA.'.id');
			}

			$questionarios = $query->groupBy(self::TABELA . '.id', self::TABELA . '.titulo',  self::TABELA . '.descricao')->offset($limite)->limit($pulo)->get();

			$questionarioObjects = [];
			foreach ($questionarios as $questionario) {
				$questionarioObjects[] = $this->construirObjeto($questionario);
			}
			return $questionarioObjects;
		}
		catch (\Exception $e)
		{
			throw new ColecaoException("Erro ao listar Questionario.", $e->getCode(), $e);
		}
	}

	function todosComId($ids = []) {
		try {	
			$tarefas = DB::table(self::TABELA)->where('deleted_at', NULL)->whereIn('id', $ids)->get();
			$tarefasObjects = [];

			foreach ($tarefas as $tarefa) {
				$tarefasObjects[] =  $this->construirObjeto($tarefa);
			}

			return $tarefasObjects;
		}
		catch (\Exception $e)
		{
			throw new ColecaoException("Erro ao buscar questionários com id!", $e->getCode(), $e);
		}
	}

	function construirObjeto(array $row) {
		$questionario = new Questionario(
			$row['id'],
			$row['titulo'],
			$row['descricao'],
			$row['tipoQuestionario'], 
			json_decode($row['formulario'])
		);

		return $questionario->toArray();
	}

    function contagem() {
		return DB::table(self::TABELA)->where('deleted_at', NULL)->count();
	}
	
	private function validarQuestionario(&$obj) {
		if(!is_string($obj->getTitulo()) and strlen($obj->getTitulo()) ==  0) throw new ColecaoException('O campo título é obrigatório.');

		$quantidade = DB::table(self::TABELA)->where('deleted_at', NULL)->where('titulo', $obj->getTitulo())->where('id', '<>', $obj->getId())->count();

		if($quantidade > 0){
			throw new ColecaoException('Já exite um Questionario cadastrado com esse título');
		}

		if(strlen($obj->getTitulo()) <= Questionario::TAM_MIN_TITUlO && strlen($obj->getTitulo()) > Questionario::TAM_MAX_TITUlO) throw new ColecaoException('O titulo deve conter no mínimo '.  Questionario::TAM_MIN_TITULO. ' e no máximo '. Questionario::TAM_MAX_TITUlO . '.');

		return true;
	}

	private function validarDeleteQuestionario($id) {
		$quantidade = DB::table(self::TABELA)->where('deleted_at', NULL)->where('id', $id)->count();

		if($quantidade == 0){
			throw new ColecaoException('Questionário não encontrado na base de dados!');
		}

		return true;
	}
}
?>