<?php
use Illuminate\Database\Capsule\Manager as Db;
/**
 *	Coleção de Loja em Banco de Dados Relacional.
 *
 *  @author		Rafael Vinicius Barros Ferreira
 *	@version	0.1
 */

class ColecaoLojaEmBDR implements ColecaoLoja
{

	const TABELA = 'loja';

	function __construct(){}

	function adicionar(&$obj) {
		if($this->validarLoja($obj)){
			try {	
				$id = Db::table(self::TABELA)->insertGetId(['razaoSocial' => $obj->getRazaoSocial(), 'nomeFantasia' => $obj->getNomeFantasia()]);
				
				$obj->setId($id);

				return $obj;
			}
			catch (\Exception $e)
			{
				throw new ColecaoException($e->getMessage(), $e->getCode(), $e);
			}
		}
		
	}

	function remover($id) {
		if($this->validarDeleteLoja($id)){

			try {	
				return DB::table(self::TABELA)->where('id', $id)->delete();
			}
			catch (\Exception $e)
			{
				throw new ColecaoException($e->getMessage(), $e->getCode(), $e);
			}
		}
		
	}

	function atualizar(&$obj) {
		if($this->validarLoja($obj)){
			try {	
				DB::table(self::TABELA)->where('id', $obj->getId())->update(['razaoSocial' => $obj->getRazaoSocial(), 'nomeFantasia' => $obj->getNomeFantasia()]);

				return $obj;
			}
			catch (\Exception $e)
			{
				throw new ColecaoException($e->getMessage(), $e->getCode(), $e);
			}
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

	function comColaboradorId($id){
		try {	
			$loja = $this->construirObjeto(DB::table(self::TABELA)
				->join(self::TABELA_RELACIONAL, self::TABELA_RELACIONAL . '.loja_id', '=', self::TABELA . '.id')
				->where(self::TABELA_RELACIONAL . '.colaborador_id', $id)->get()

			);

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
	function todos($limite = 0, $pulo = 0) {
		try {	
			$lojas = Db::table(self::TABELA)->offset($limite)->limit($pulo)->get();
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
		$loja = new Loja($row['id'],$row['razaoSocial'], $row['nomeFantasia']);

		return $loja;
	}	

    function contagem() {
		return Db::table(self::TABELA)->count();
	}

	private function validarLoja(&$obj) {
		if(!is_string($obj->getRazaoSocial())) {
			throw new ColecaoException('Valor inválido para razão social.');
		}

		$quantidade = DB::table(self::TABELA)->where('razaoSocial', $obj->getRazaoSocial())->where('nomeFantasia', $obj->getNomeFantasia())->where('id', '<>', $obj->getId())->count();

		if($quantidade > 0){
			throw new ColecaoException('Já exite uma loja cadastrada com esses dados.');
		}

		if(strlen($obj->getRazaoSocial()) <= Loja::TAM_TEXT_MIM && strlen($obj->getRazaoSocial()) > Loja::TAM_TEXT_MAX) throw new ColecaoException('O título deve conter no mínimo '. Loja::TAM_TEXT_MIM . ' e no máximo '. Loja::TAM_TEXT_MAX . '.');
		if(strlen($obj->getNomeFantasia()) <= Loja::TAM_TEXT_MIM && strlen($obj->getNomeFantasia()) > Loja::TAM_TEXT_MAX) throw new ColecaoException('O nome a fantasia deve conter no mínimo '. Loja::TAM_TEXT_MIM . ' e no máximo '. Loja::TAM_TEXT_MAX . '.');

		return true;
	}

	private function validarDeleteLoja($id){
		$qtdReacionamento = DB::table(ColecaoTarefaEmBDR::TABELA)->where('loja_id', $id)->count();

		if($qtdReacionamento > 0){
			throw new ColecaoException('Essa loja possue tarefas relacionados a ela! Exclua todos as tarefas cadastros e tente novamente.');
		}

		$qtdReacionamento = DB::table(ColecaoUsuarioEmBDR::TABELA)->where('loja_id', $id)->count();

		if($qtdReacionamento > 0){
			throw new ColecaoException('Essa loja possue usuários relacionados a ela! Exclua todos os usuários cadastros e tente novamente.');
		}

		return true;
	}
}

?>