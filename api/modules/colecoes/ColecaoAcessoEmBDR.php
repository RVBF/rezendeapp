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
	const TABELA = 'acesso';

	function __construct() {}

	function adicionar(&$obj) {
		if($this->validarAcesso($obj)) {
			try {
				$id = DB::table(self::TABELA)->insertGetId([
					'recursoId' => $obj->getRecurso()->id,
					'acessanteTipo' => get_class($obj->getAcessante()),
					'acessanteId' => $obj->getAcessante()->id,
					'acao' => $obj->getAcao()
				]);

				$obj->setId($id);

				return $obj;
			}
			catch (\Exception $e) {
				throw new ColecaoException("Erro ao adicionar acesso.", $e->getCode(), $e);
			}
		}
	}

	function remover($id) {
		try {
			if($this->validarRemocaoAcesso($id)) DB::table(self::TABELA)->where('deleted_at', NULL)->where('id', $id)->update(['deleted_at' => Carbon::now()->toDateTimeString()]);
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

				DB::table(self::TABELA)->where('deleted_at', NULL)->where('id', $obj->getId())->update($filds);

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
			$query = DB::table(self::TABELA)->where('deleted_at', NULL)->select(self::TABELA . '.*');

			if($search != '') {
				$buscaCompleta = $search;
				$palavras = explode(' ', $buscaCompleta);

				$query->leftJoin(ColecaoColaboradorEmBDR::TABELA, ColecaoColaboradorEmBDR::TABELA . '.usuario_id', '=', self::TABELA . '.id');

				$query->where(function($query) use ($buscaCompleta) {
					$query->whereRaw(self::TABELA . '.id like "%' . $buscaCompleta . '%"');
					$query->orWhereRaw(self::TABELA . '.login like "%' . $buscaCompleta . '%"');
					$query->orWhereRaw(ColecaoColaboradorEmBDR::TABELA . '.nome like "%' . $buscaCompleta . '%"');
					$query->orWhereRaw(ColecaoColaboradorEmBDR::TABELA . '.sobrenome like "%' . $buscaCompleta . '%"');
					$query->orWhereRaw(ColecaoColaboradorEmBDR::TABELA . '.email like "%' . $buscaCompleta . '%"');
				});

				if($query->count() == 0) {
					$query->where(function($query) use ($palavras) {
						foreach ($palavras as $key => $palavra) {
							if($palavra != " ") {
								$query->whereRaw(self::TABELA . '.id like "%' . $palavra . '%"');
								$query->orWhereRaw(self::TABELA . '.login like "%' . $palavra . '%"');
								$query->orWhereRaw(ColecaoColaboradorEmBDR::TABELA . '.nome like "%' . $palavra . '%"');
								$query->orWhereRaw(ColecaoColaboradorEmBDR::TABELA . '.sobrenome like "%' . $palavra . '%"');
								$query->orWhereRaw(ColecaoColaboradorEmBDR::TABELA . '.email like "%' . $palavra . '%"');
							}
						}
					});
				}

				$query->groupBy(self::TABELA.'.id');
			}

			$acessos = $query->offset($limite)->limit($pulo)->get();

			$acessosObjects = [];

			foreach($acessos as $acesso) {
				$acesso['acessante'] = DB::table($acesso['acessanteTipo']::TABELA)->where('id', $acesso['acessanteId'])->first();
				$acesso['acessante'] = $acesso['acessanteTipo']::criarAPartirDoArray($acesso['acessante']);

				$acesso['recurso'] = DB::table(Recurso::TABELA)->where('id', $acesso['recursoId'])->first();
				$acesso['recurso'] = new Recurso($acesso['recurso']['id'], $acesso['recurso']['nome'], $acesso['recurso']['model']);

				$acessosObjects[] =  $this->construirObjeto($acesso);
			}

			return $acessosObjects;
		}
		catch (\Exception $e)
		{
			throw new ColecaoException("Erro ao buscar acessos!", $e->getCode(), $e);
		}
	}

	function todosComIds($ids = []) {
		try {
			$acessos = DB::table(self::TABELA)->where('deleted_at', NULL)->select(self::TABELA . '.*')->whereIn('id', $ids)->get();

			$acessosObjects = [];

			foreach ($acessos as $acessos) {
				$acesso['acessante'] = DB::table($acesso['acessanteTipo']::TABELA)->where('id', $acesso['acessanteId'])->first();
				$acesso['acessante'] = $acesso['acessanteTipo']::criarAPartirDoArray($acesso['acessante']);

				$acesso['recurso'] = DB::table(Recurso::TABELA)->where('id', $acesso['recursoId'])->first();
				$acesso['recurso'] = new Recurso($acesso['recurso']['id'], $acesso['recurso']['nome'], $acesso['recurso']['model']);

				$acessosObjects[] =  $this->construirObjeto($acessos);
			}

			return $acessosObjects;
		}
		catch (\Exception $e)
		{
			throw new ColecaoException("Erro ao buscar acessos com referências!", $e->getCode(), $e);
		}
	}

	function construirObjeto(array $row) {
		$acesso = new Acesso($row['id'], $row['recurso'], $row['acessante'], $row['acao']);

		return $acesso->toArray();
	}

	function comUsuarioId($id) {
		try {
			$acessos = DB::table(self::TABELA)
								->where('deleted_at', NULL)
								->where(Recurso::TABELA . '.acessanteTipo', Usuario::class)
								->where(Recurso::TABELA . '.acessanteId', $id)
								->get();

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

	function comGrupoId($id) {
		try {
			$acessos = DB::table(self::TABELA)
								->where('deleted_at', NULL)
								->where(Recurso::TABELA . '.acessanteTipo', GrupoUsuario::class)
								->where(Recurso::TABELA . '.acessanteId', $id)
								->get();

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

	function comRecursoId($id) {
		try {
			$acessos = DB::table(self::TABELA)->where('deleted_at', NULL)->select(self::TABELA . '.*')
				->join(Recurso::TABELA, Recurso::TABELA . '.id', '=', self::TABELA . '.recursoId')
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
		return DB::table(self::TABELA)->count();
	}

	private function validarRemocaoAcesso($id) {
		return true;
	}
}
?>