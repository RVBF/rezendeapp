<?php
use Illuminate\Database\Capsule\Manager as DB;
use Carbon\Carbon;

/**
 *	Coleção de Recurso em Banco de Dados Relacional.
 *
 * @author Leonardo Carvalhães Bernardo
 *	@version	1.0
 */

class ColecaoRecursoEmBDR  implements ColecaoRecurso {
	function __construct() {}

	function todos($limite = 0, $pulo = 0) {
		try {
			$query = DB::table(Recurso::TABELA)->select(Recurso::TABELA . '.*');

         if($limite) $query->limit($limite);
         if($pulo) $query->offset($pulo);

         $recursos = $query->get();

			$recursosObjects = [];

			foreach($recursos as $recurso) {
				$recursosObjects[] =  $this->construirObjeto($recurso);
			}

			return $recursosObjects;
		}
		catch (\Exception $e)
		{
			throw new ColecaoException('Erro ao exibir listagem de recurso.', $e->getCode(), $e);
		}
	}

	function todosComIds($ids = []) {
		try {
			$recursos = DB::table(Recurso::TABELA)->select(Recurso::TABELA . '.*')->whereIn('id', $ids)->get();

			$recursosObjects = [];

			foreach($recursos as $recurso) {
				$recursosObjects[] =  $this->construirObjeto($recurso);
			}

			return $recursosObjects;
		}
		catch (\Exception $e)
		{
			throw new ColecaoException('Erro ao buscar acessos com referências!', $e->getCode(), $e);
		}
	}

	function comNome($nome) {
		try {
			$recursos = DB::table(Recurso::TABELA)->where(Recurso::TABELA . '.nome', $nome)->get();

			$recursosObjects = [];

			foreach ($recursos as $recurso) $recursosObjects[] = $this->construirObjeto($recurso);

			return $recursosObjects;
		}
		catch (\Exception $e)
		{
			throw new ColecaoException('Erro ao buscar acessos com as referências de acesso', $e->getCode(), $e);
		}
	}

	function comModel($model) {
		try {
			$recursos = DB::table(Recurso::TABELA)->where(Recurso::TABELA . '.model', $model)->get();

			$recursosObjects = [];

			foreach ($recursos as $recurso) $recursosObjects[] = $this->construirObjeto($recurso);

			return $recursosObjects;
		}
		catch (\Exception $e)
		{
			throw new ColecaoException('Erro ao buscar acessos com as referências de acesso', $e->getCode(), $e);
		}
	}

	function contagem() {
		return DB::table(Recurso::TABELA)->count();
   }

   function construirObjeto(array $row) {
      $rotas = [];

      $rotasArray = DB::table(Rota::TABELA)->where('recursoId', $row['id'])->get();

      foreach($rotasArray as $rotaArray) $rotas[] = new Rota($rotaArray['id'], $rotaArray['caminho'], $rotaArray['metodo']);

      $recurso = new Recurso($row['id'], $row['nome'], $row['model'], $rotas);

		return $recurso->toArray();
	}
}
?>