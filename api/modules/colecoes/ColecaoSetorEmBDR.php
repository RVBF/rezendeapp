<?php
use Illuminate\Database\Capsule\Manager as DB;
use \phputil\RTTI;

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
				DB::statement('SET FOREIGN_KEY_CHECKS=0;');
	
				$removido = DB::table(self::TABELA)->where('id', $id)->delete();
				
				DB::statement('SET FOREIGN_KEY_CHECKS=1;');
	
				return $removido;
	
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

				DB::table(self::TABELA)->where('id', $obj->getId())->update(['titulo' => $obj->getTitulo(),
					'descricao'=> $obj->getDescricao(),
					'categoria_id'=> $obj->getCategoria()->getId()
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
			$setor = $this->construirObjeto(DB::table(self::TABELA)->where('id', $id)->get()[0]);

			return $setor;
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
			$query = DB::table(self::TABELA)->select(self::TABELA . '.*');

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
				$setorObjects[] = RTTI::getAttributes($this->construirObjeto($setor),RTTI::allFlags());
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

		return $setor;
	}

    function contagem() {
		return DB::table(self::TABELA)->count();
	}
	
	private function validarSetor(&$obj) {
		if(!is_string($obj->getTitulo())) {
			throw new ColecaoException('Valor inválido para título.');
		}

		$quantidade = DB::table(self::TABELA)->where('titulo', $obj->getTitulo())->where('id', '<>', $obj->getId())->count();

		if($quantidade > 0){
			throw new ColecaoException('Já exite um setor cadastrado com esse título');
		}

		if(strlen($obj->getTitulo()) <= Setor::TAM_MIN_TITUlO && strlen($obj->getTitulo()) > Setor::TAM_MAX_TITUlO) throw new ColecaoException('O titulo deve conter no mínimo '.  Setor::TAM_MIN_TITULO. ' e no máximo '. Categoria::TAM_MAX_TITUlO . '.');

		return true;
	}

	private function validarDeleteSetor($id) {
		// $qtdReacionamento = DB::table(ColecaoChecklistEmBDR::TABELA)->where('setor_id', $id)->count();

		// if($qtdReacionamento > 0){
		// 	throw new ColecaoException('Essa categoria possue setores relacionados a ela! Exclua todos os setores cadastros e tente novamente.');
		// }

		return true;
	}
}
?>