<?php
use Illuminate\Database\Capsule\Manager as DB;

/**
 *	Coleção de Loja em Banco de Dados Relacional.
 *
 *  @author		Rafael Vinicius Barros Ferreira
 *	@version	1.0
 */

class ColecaoLojaEmBDR implements ColecaoLoja
{

	const TABELA = 'loja';
	const TABELA_RELACIONAL = 'atuacao';


	function __construct(){}

	function adicionar(&$obj) {
		if($this->validarLoja($obj)){
			try {	
				DB::statement('SET FOREIGN_KEY_CHECKS=0;');
				$id = DB::table(self::TABELA)->insertGetId(['razaoSocial' => $obj->getRazaoSocial(), 'nomeFantasia' => $obj->getNomeFantasia()]);

				$obj->setId($id);
				DB::statement('SET FOREIGN_KEY_CHECKS=1;');
			}
			catch (\Exception $e) {
				throw new ColecaoException("Erro ao", $e->getCode(), $e);
			}
		}

		return $obj;
	}

	function remover($id) {
		if($this->validarDeleteLoja($id)){

			try {	
				return DB::table(self::TABELA)->where('id', $id)->delete();
			}
			catch (\Exception $e)
			{
				throw new ColecaoException("Erro ao remover loja!", $e->getCode(), $e);
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
				throw new ColecaoException("Erro ao atualizar loja!", $e->getCode(), $e);
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
			throw new ColecaoException("Erro ao buscar loja!", $e->getCode(), $e);
		}
	}

	function comColaboradorId($id){
		try {

			$lojas = DB::table(self::TABELA)->select(self::TABELA . '.*')
				->join(self::TABELA_RELACIONAL, self::TABELA_RELACIONAL . '.loja_id', '=', self::TABELA . '.id')
				->where(self::TABELA_RELACIONAL . '.colaborador_id', $id)->get();
				
			$lojasObjects = [];

			foreach ($lojas as $loja) {
				$lojasObjects[] = $this->construirObjeto($loja);			
			}

			return $lojasObjects;
		}
		catch (\Exception $e)
		{
			throw new ColecaoException("Erro ao buscar lojas de atuação de um colaborador!", $e->getCode(), $e);
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
					$query->orWhereRaw(self::TABELA . '.razaoSocial like "%' . $buscaCompleta . '%"');
					$query->orWhereRaw(self::TABELA . '.nomeFantasia like "%' . $buscaCompleta . '%"');
				});
				
				
				if($query->count() == 0){
					$query->where(function($query) use ($palavras){
						foreach ($palavras as $key => $palavra) {
							if($palavra != " "){
								$query->whereRaw(self::TABELA . '.id like "%' . $palavra . '%"');
								$query->orWhereRaw(self::TABELA . '.razaoSocial like "%' . $palavra . '%"');
								$query->orWhereRaw(self::TABELA . '.nomeFantasia like "%' . $palavra . '%"');
							}
						}
						
					});
				}

				if($query->count() == 0){
					foreach ($buscaCompleta as $key => $caracterer) {
						$query->where(function($query) use ($caracterer){
							$query->whereRaw(self::TABELA . '.id like "%' . $caracterer . '%"');
							$query->orWhereRaw(self::TABELA . '.razaoSocial like "%' . $caracterer . '%"');
							$query->orWhereRaw(self::TABELA . '.nomeFantasia like "%' . $caracterer . '%"');
						});
					}
				}
				$query->groupBy(self::TABELA.'.id');
			}

			$lojas = $query->offset($limite)->limit($pulo)->get();
			$lojasObjects = [];
			foreach ($lojas as $loja) {
				$lojasObjects[] =  $this->construirObjeto($loja);
			}

			return $lojasObjects;
		}
		catch (\Exception $e)
		{
			throw new ColecaoException("Erro ao listar lojas!", $e->getCode(), $e);
		}
	}

	function todosComIds($ids = []) {
		try {	
			$lojas = DB::table(self::TABELA)->whereIn('id', $ids)->get();
			$lojasObjects = [];
			foreach ($lojas as $loja) {
				$lojasObjects[] =  $this->construirObjeto($loja);
			}

			return $lojasObjects;
		}
		catch (\Exception $e)
		{
			throw new ColecaoException("Erro ao buscar lojas com referências!", $e->getCode(), $e);
		}
	}

	function construirObjeto(array $row) {

		$loja = new Loja($row['id'],$row['razaoSocial'], $row['nomeFantasia']);
		return $loja->toArray();
	}	

    function contagem() {
		return DB::table(self::TABELA)->count();
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
		// $qtdReacionamento = DB::table(ColecaoChecklistEmBDR::TABELA)->where('loja_id', $id)->count();

		// if($qtdReacionamento > 0){
		// 	throw new ColecaoException('Essa loja possue tarefas relacionados a ela! Exclua todos as tarefas cadastros e tente novamente.');
		// }

		// $qtdReacionamento = DB::table(ColecaoColaboradorEmBDR::TABELA_RELACIONAL)->where('loja_id', $id)->count();

		// if($qtdReacionamento > 0){
		// 	throw new ColecaoException('Essa loja possue usuários relacionados a ela! Exclua todos os usuários cadastros e tente novamente.');
		// }

		$qtdReacionamento = DB::table(self::TABELA)->where('id', $id)->count();
		
		if($qtdReacionamento == 0){
			throw new ColecaoException('Loja Inexistente!');
			return false;
		}

		return true;
	}
}

?>