<?php
use Illuminate\Database\Capsule\Manager as DB;
use \phputil\RTTI;
use Carbon\Carbon;


/**
 *	Coleção de Setor em Banco de Dados Relacional.
 *
 *  @author		Rafael Vinicius Barros Ferreira
 *	@version	1.0
 */

class ColecaoSetorEmBDR implements ColecaoSetor {
	const TABELA = 'setor';

	function __construct(){}

	function adicionar(&$obj) {
		if($this->validarSetor($obj)){
			try {	

				$id = DB::table(self::TABELA)->insertGetId(['titulo' => $obj->getTitulo(), 'descricao'=> $obj->getDescricao()]);

				$obj->setId($id);

				return $obj;
			}
			catch (\Exception $e) {

				throw new ColecaoException("Erro ao cadastrar setor.", $e->getCode(), $e);
			}
		}
	}

	function remover($id) {
		if($this->validarDeleteSetor($id)){
			try {	
				DB::table(self::TABELA)->where('deleted_at', NULL)->where('id', $id)->update(['deleted_at'=> Carbon::now()->toDateTimeString()]);
			}
			catch (\Exception $e) {
				throw new ColecaoException("Erro ao remover setor.", $e->getCode(), $e);
			}
		}
	}

	function atualizar(&$obj) {
		if($this->validarSetor($obj)){
			try {
				DB::statement('SET FOREIGN_KEY_CHECKS=0;');

				DB::table(self::TABELA)->where('deleted_at', NULL)->where('id', $obj->getId())->update(
					['titulo' => $obj->getTitulo(),
					'descricao'=> $obj->getDescricao(),
				]);

				DB::statement('SET FOREIGN_KEY_CHECKS=1;');
				return $obj;
			}
			catch (\Exception $e)
			{
				throw new ColecaoException("Erro ao atualizar setor.", $e->getCode(), $e);
			}
		}
		
	}

	function comId($id){
		try {
			return (DB::table(self::TABELA)->where('id', $id)->count()) ? $this->construirObjeto(DB::table(self::TABELA)->where('id', $id)->first()) : [];
		}
		catch (\Exception $e)
		{
			throw new ColecaoException("Erro ao buscar setor.", $e->getCode(), $e);
		}
	}

	/**
	 * @inheritDoc
	 */
	function todos($limite = 0, $pulo = 0, $search = '') {
		try {	
			$query = DB::table(self::TABELA)->where('deleted_at', NULL)->select(self::TABELA . '.*');

			if($search != '') {
				$buscaCompleta = $search;
				$palavras = explode(' ', $buscaCompleta);
				
				$query->where(function($query) use ($buscaCompleta){
					$query->whereRaw(self::TABELA . '.id like "%' . $buscaCompleta . '%"');
					$query->orWhereRaw(self::TABELA . '.titulo like "%' . $buscaCompleta . '%"');
					$query->orWhereRaw(self::TABELA . '.descricao like "%' . $buscaCompleta . '%"');

				});
				
				
				if($query->count() == 0){
					$query->where(function($query) use ($palavras){
						foreach ($palavras as $key => $palavra) {
							if($palavra != " "){
								$query->whereRaw(self::TABELA . '.id like "%' . $palavra . '%"');
								$query->orWhereRaw(self::TABELA . '.titulo like "%' . $palavra . '%"');
								$query->orWhereRaw(self::TABELA . '.descricao like "%' . $buscaCompleta . '%"');
							}
						}
						
					});
				}
				$query->groupBy(self::TABELA.'.id');
			}

			$setors = $query->offset($limite)->limit($pulo)->get();

			$setorObjects = [];
			foreach ($setors as $setor) {
				$setorObjects[] = $this->construirObjeto($setor);
			}

			return $setorObjects;
		}
		catch (\Exception $e)
		{
			throw new ColecaoException("Erro ao listar setor.", $e->getCode(), $e);
		}
	}

	function construirObjeto(array $row) {
		$setor = new Setor($row['id'],$row['titulo'], $row['descricao']);

		return $setor->toArray();
	}

    function contagem() {
		return DB::table(self::TABELA)->count();
	}
	
	private function validarSetor(&$obj) {
		if(!is_string($obj->getTitulo()) and strlen($obj->getTitulo()) == 0) throw new ColecaoException('O campo título é obrigatório!');


		$quantidade = DB::table(self::TABELA)->where('deleted_at', NULL)->where('titulo', $obj->getTitulo())->where('id', '<>', $obj->getId())->count();

		if($quantidade > 0){
			throw new ColecaoException('Já exite um setor cadastrado com esse título');
		}

		if(strlen($obj->getTitulo()) <= Setor::TAM_MIN_TITUlO && strlen($obj->getTitulo()) > Setor::TAM_MAX_TITUlO) throw new ColecaoException('O titulo deve conter no mínimo '.  Setor::TAM_MIN_TITULO. ' e no máximo '. Setor::TAM_MAX_TITUlO . '.');

		return true;
	}

	private function validarDeleteSetor($id) {
		$quantidadeSetor = DB::table(self::TABELA)->where('deleted_at', NULL)->where('id', $id)->count();
		
		if($quantidadeSetor == 0) throw new ColecaoException('O setor selecionado para delete não foi encontrado');
		if(DB::table(self::TABELA)->where('deleted_at', NULL)->count() == 1) throw new Exception("Não é possível excluir o setor quando há somente 1 setor cadastrado, porque é necesssário ao menos 1 setor cadastrado para que possa ter relação com outras depências do sistema.");
		
		return true;
	}
}
?>