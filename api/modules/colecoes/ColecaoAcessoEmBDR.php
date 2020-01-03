<?php
use Illuminate\Database\Capsule\Manager as DB;
use Carbon\Carbon;

/**
 *	Coleção de Acesso em Banco de Dados Relacional.
 *
 *  @author		Leonardo Carvalhães Bernardo
 *	@version	1.0
 */

class ColecaoAcessoEmBDR  implements ColecaoAcesso {
	function __construct() {}

	function adicionar(&$acessoArray) {

		if($this->validarAcessoArray($acessoArray)) {
			try {
				$id = DB::table(Acesso::TABELA)->insertGetId($acessoArray);

				return $this->comId($id);
			}
			catch (\Exception $e) {
				throw new ColecaoException($e->getMessage(), $e->getCode(), $e);
			}
		}
	}

	function validarAcessoArray($acessoArray) {
		if(!isset($acessoArray['acao']) or !isset($acessoArray['recursoId']) or !isset($acessoArray['acessanteTipo']) or !isset($acessoArray['acessanteId'])) return false;

		return true;
	}

	function remover($id) {
		try {
			if($this->validarRemocaoAcesso($id)) DB::table(Acesso::TABELA)->where('id', $id)->update(['deleted_at' => Carbon::now()->toDateTimeString()]);
		}
		catch (\Exception $e)
		{
			throw new ColecaoException("Erro ao remover acesso com id.", $e->getCode(), $e);
		}
	}

	function atualizar(&$obj) {
		if($this->validarAcesso($obj)) {
			try {
				DB::statement('SET FOREIGN_KEY_CHECKS=0;');

				$filds = [
					'recursoId' => $obj->getRecurso()->id,
					'acessanteTipo' => get_class($obj->getAcessante()),
					'acessanteId' => $obj->getAcessante()->id,
					'acao' => $obj->getAcao()
				];

				DB::table(Acesso::TABELA)->where('id', $obj->getId())->update($filds);

				DB::statement('SET FOREIGN_KEY_CHECKS=1;');
				return $obj;
			}
			catch (\Exception $e)
			{
				throw new ColecaoException("Ero ao atualizar acesso!", $e->getCode(), $e);
			}
		}
	}

	function todos($limite = 0, $pulo = 0, $search = '') {
		try {
			$query = DB::table(Acesso::TABELA)->select(Acesso::TABELA . '.*');

			if($search != '') {
				$buscaCompleta = $search;
				$palavras = explode(' ', $buscaCompleta);

				$query->leftJoin(ColecaoColaboradorEmBDR::TABELA, ColecaoColaboradorEmBDR::TABELA . '.usuario_id', '=', Acesso::TABELA . '.id');

				$query->where(function($query) use ($buscaCompleta) {
					$query->whereRaw(Acesso::TABELA . '.id like "%' . $buscaCompleta . '%"');
					$query->orWhereRaw(Acesso::TABELA . '.login like "%' . $buscaCompleta . '%"');
					$query->orWhereRaw(ColecaoColaboradorEmBDR::TABELA . '.nome like "%' . $buscaCompleta . '%"');
					$query->orWhereRaw(ColecaoColaboradorEmBDR::TABELA . '.sobrenome like "%' . $buscaCompleta . '%"');
					$query->orWhereRaw(ColecaoColaboradorEmBDR::TABELA . '.email like "%' . $buscaCompleta . '%"');
				});

				if($query->count() == 0) {
					$query->where(function($query) use ($palavras) {
						foreach ($palavras as $key => $palavra) {
							if($palavra != " ") {
								$query->whereRaw(Acesso::TABELA . '.id like "%' . $palavra . '%"');
								$query->orWhereRaw(Acesso::TABELA . '.login like "%' . $palavra . '%"');
								$query->orWhereRaw(ColecaoColaboradorEmBDR::TABELA . '.nome like "%' . $palavra . '%"');
								$query->orWhereRaw(ColecaoColaboradorEmBDR::TABELA . '.sobrenome like "%' . $palavra . '%"');
								$query->orWhereRaw(ColecaoColaboradorEmBDR::TABELA . '.email like "%' . $palavra . '%"');
							}
						}
					});
				}

				$query->groupBy(Acesso::TABELA.'.id');
			}

			if($pulo) $query->offset($pulo);
			if($limite) $query->limit($limite);

			$acessos = $query->get();

			$acessosObjects = [];

			foreach ($acessos as $acesso) $acessosObjects[] = $this->construirObjeto($acesso);

			return $acessosObjects;
		}
		catch (\Exception $e)
		{
			throw new ColecaoException($e->getMessage(), $e->getCode(), $e);
		}
	}

	function todosComIds($ids = []) {
		try {
			$acessos = DB::table(Acesso::TABELA)->select(Acesso::TABELA . '.*')->whereIn('id', $ids)->get();

			$acessosObjects = [];

			foreach ($acessos as $acesso) $acessosObjects[] = $this->construirObjeto($acesso);

			return $acessosObjects;
		}
		catch (\Exception $e)
		{
			throw new ColecaoException("Erro ao buscar acessos com referências!", $e->getCode(), $e);
		}
	}

	function comId($id) {
		try {
			$acessos = DB::table(Acesso::TABELA)->where(Acesso::TABELA . '.id', $id)->get();

			$acessosObjects = [];

			foreach ($acessos as $acesso) $acessosObjects[] = $this->construirObjeto($acesso);

			return array_shift($acessosObjects);
		}
		catch (\Exception $e)
		{
			throw new ColecaoException($e->getMessage(), $e->getCode(), $e);
		}
	}

	function comAcessante($acessanteTipo, $acessanteId) {
		try {
			$acessos = DB::table(Acesso::TABELA)
								->where(Acesso::TABELA . '.acessanteTipo', $acessanteTipo)
								->where(Acesso::TABELA . '.acessanteId', $acessanteId)
								->get();

			$acessosObjects = [];

			foreach ($acessos as $acesso) $acessosObjects[] = $this->construirObjeto($acesso);

			return $acessosObjects;
		}
		catch (\Exception $e)
		{
			throw new ColecaoException("Erro ao buscar acessos com as referências de acesso", $e->getCode(), $e);
		}
	}

	function comUsuarioId($id) {
		try {
			$acessos = DB::table(Acesso::TABELA)
								->where(Recurso::TABELA . '.acessanteTipo', Usuario::class)
								->where(Recurso::TABELA . '.acessanteId', $id)
								->get();

			$acessosObjects = [];

			foreach ($acessos as $acesso) $acessosObjects[] = $this->construirObjeto($acesso);

			return $acessosObjects;
		}
		catch (\Exception $e)
		{
			throw new ColecaoException("Erro ao buscar acessos com as referências de acesso", $e->getCode(), $e);
		}
	}

	function comGrupoId($id) {
		try {
			$acessos = DB::table(Acesso::TABELA)
								->where(Recurso::TABELA . '.acessanteTipo', GrupoUsuario::class)
								->where(Recurso::TABELA . '.acessanteId', $id)
								->get();

			$acessosObjects = [];

			foreach ($acessos as $acesso) $acessosObjects[] = $this->construirObjeto($acesso);

			return $acessosObjects;
		}
		catch (\Exception $e)
		{
			throw new ColecaoException("Erro ao buscar acessos com as referências de acesso", $e->getCode(), $e);
		}
	}

	function comRecursoId($id) {
		try {
			$acessos = DB::table(Acesso::TABELA)->select(Acesso::TABELA . '.*')
			->join(Recurso::TABELA, Recurso::TABELA . '.id', '=', Acesso::TABELA . '.recursoId')
			->where(Recurso::TABELA . '.recursoId', $id)->get();

			$acessosObjects = [];

			foreach ($acessos as $acesso) {
				$acesso['acessante'] = DB::table($acesso['acessanteTipo']::TABELA)->where('id', $acesso['acessanteId'])->first();
				$acesso['acessante'] = $acesso['acessanteTipo']::criarAPartirDoArray($acesso['acessante']);

				$acesso['recurso'] = DB::table(Recurso::TABELA)->where('id', $acesso['recursoId'])->first();
				$acesso['recurso'] = new Recurso($acesso['recurso']['id'], $acesso['recurso']['nome'], $acesso['recurso']['model']);

				$acessosObjects[] = $this->construirObjeto($acesso);
			}

			return $acessosObjects;
		}
		catch (\Exception $e)
		{
			throw new ColecaoException("Erro ao buscar acessos com as referências de acesso", $e->getCode(), $e);
		}
	}

	function contagem() {
		return DB::table(Acesso::TABELA)->count();
	}

	function construirObjeto(array $row) {
		// $acessante = DB::table($row['acessanteTipo']::TABELA)->where('id', $row['acessanteId'])->first();
		// $acessante = $row['acessanteTipo']::criarAPartirDoArray($acessante);

		// $recurso = DB::table(Recurso::TABELA)->where('id', $row['recursoId'])->first();
		// $recurso = new Recurso($recurso['id'], $recurso['nome'], $recurso['model']);

		// $acesso = new Acesso($row['id'], $recurso, $acessante, $row['acao']);

		// $acessoArray = $acesso->toArray();

		// $acessoArray['recurso'] = $recurso->toArray();

		return $row;
	}
}
?>