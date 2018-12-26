<?php
use Illuminate\Database\Capsule\Manager as DB;
/**
 *	Coleção de Categoria em Banco de Dados Relacional.
 *
 *  @author		Rafael Vinicius Barros Ferreira
 *	@version	0.1
 */

class ColecaoCategoriaEmBDR implements ColecaoCategoria
{

	const TABELA = 'categoria';

	function __construct(){}

	function adicionar(&$obj) {
		if($this->validarCategoria($obj)){
			try {	
				$id = DB::table(self::TABELA)->insertGetId(
					['titulo' => $obj->getTitulo()]
				);
				
				$obj->setId($id);

				return $obj;
			}
			catch (\Exception $e)
			{
				throw new ColecaoException('Erro ao cadastrar categoria.', $e->getCode(), $e);
			}
		}
	}

	function remover($id) {
		if($this->validarDeleteCategoria($id)) {
			try {	
				DB::statement('SET FOREIGN_KEY_CHECKS=0;');
	
				$resultado  = DB::table(self::TABELA)->where('id', $id)->delete();
	
				DB::statement('SET FOREIGN_KEY_CHECKS=1;');

				return $resultado;
	
			}
			catch (\Exception $e) {
				throw new ColecaoException('Erro ao remover categoria', $e->getCode(), $e);
			}
		}
		
	}

	function atualizar(&$obj) {
		if($this->validarCategoria($obj)){
			try {	

				DB::table(self::TABELA)->where('id', $obj->getId())->update(['titulo' => $obj->getTitulo()]);

				return $obj;
			}
			catch (\Exception $e)
			{
				throw new ColecaoException("Erro ao atualizar caregoria.", $e->getCode(), $e);
			}
		}
		
	}

	function comId($id){
		try {	
			return $this->construirObjeto(DB::table(self::TABELA)->where('id', $id)->first());
		}
		catch (\Exception $e)
		{
			throw new ColecaoException("Ero ao buscar categoria", $e->getCode(), $e);
		}
	}

	/**
	 * @inheritDoc
	 */
	function todos($limite = 0, $pulo = 0) {
		try {	
			$categorias = DB::table(self::TABELA)->offset($limite)->limit($pulo)->get();
			$categoriasObjects = [];

			foreach ($categorias as $categoria) {
				$categoriasObjects[] =  $this->construirObjeto($categoria);
			}

			return $categoriasObjects;
		}
		catch (\Exception $e) {
			throw new ColecaoException("Erro ao buscar categorias no banco de dados.", $e->getCode(), $e);
		}
	}

	function construirObjeto(array $row){
		try{
			return new Categoria($row['id'], $row['titulo']);
		}
		catch (\Exception $e) {
			throw new ColecaoException("Erro ao construir o objeto categoria.", $e->getCode(), $e);
		}
	}

    function contagem(){
		return DB::table(self::TABELA)->count();
	}
	
	private function validarCategoria(&$obj) {
		if(!is_string($obj->getTitulo())) {
			throw new ColecaoException('Valor inválido para título.');
		}

		$quantidade = DB::table(self::TABELA)->where('titulo', $obj->getTitulo())->where('id', '<>', $obj->getId())->count();

		if($quantidade > 0){
			throw new ColecaoException('Já exite uma categoria cadastrada com esse título');
		}

		if(strlen($obj->getTitulo()) <= Categoria::TAM_TITULO_MIM && strlen($obj->getTitulo()) > Categoria::TAM_TITULO_MAX) throw new ColecaoException('O título deve conter no mínimo '. Categoria::TAM_TITULO_MIM . ' e no máximo '. Categoria::TAM_TITULO_MAX . '.');

		return true;
	}

	private function validarDeleteCategoria($id){
		$qtdReacionamento = DB::table(ColecaoSetor::TABELA)->where('categoria_id', $id)->count();

		if($quantidade > 0){
			throw new ColecaoException('Essa categoria possue setores relacionados a ela! Exclua todos os setores cadastros e tente novamente.');
		}

		return true;
	}
}

?>