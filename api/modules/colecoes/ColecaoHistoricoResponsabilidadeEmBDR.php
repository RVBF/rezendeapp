<?php
use Illuminate\Database\Capsule\Manager as DB;
use Carbon\Carbon;


/**
 *	Coleção de Histórico de Responsabildiade em Banco de Dados Relacional.
 *
 *  @author		Rafael Vinicius Barros Ferreira
 *	@version	1.0
 */

class ColecaoHistoricoResponsabilidadeEmBDR implements ColecaoHistoricoResponsabilidade {
	const TABELA = 'hitoricoresponsabilidade';

	function __construct(){}

	function adicionar(&$obj) {
		try {	
			DB::statement('SET FOREIGN_KEY_CHECKS=0;');

			$id = DB::table(self::TABELA)->insertGetId([ 
				'datahoramudacanca' => $obj->getData()->toDateTimeString(),
				'planoacao_id' => $obj->getPlanoAcao()->getId(),
				'responsavelatual_id' => $obj->getResponsavelAtual()->getId(),
				'responsavelanterior_id' => $obj->getResponsavelAnterior()->getId()
			]);

			$obj->setId($id);
			
			DB::statement('SET FOREIGN_KEY_CHECKS=0;');
			return $obj;
		}
		catch (\Exception $e) {

			throw new ColecaoException("Erro ao adicionar Checklist ", $e->getCode(), $e);
		}
	}
	function atualizar(&$obj){}
	function remover($id){}

    function comPlanoAcaoId($planoAcaoId = 0){
		try {	
			return (DB::table(self::TABELA)->where('planoacao_id', $planoAcaoId)->count() > 0) ? $this->construirObjeto(DB::table(self::TABELA)->where('planoacao_id', $planoAcaoId)->orderBy('id', 'DESC')->first()) : [];
		}
		catch (\Exception $e)
		{
			throw new ColecaoException("Erro ao buscar historico de responsabilidade  no banco de dados!", $e->getCode(), $e);
		}
	}


	function comId($id){
		try {	
			return (DB::table(self::TABELA)->where('id', $id)->count() > 0) ? $this->construirObjeto(DB::table(self::TABELA)->where('id', $id)->first()) : [];
		}
		catch (\Exception $e)
		{
			throw new ColecaoException("Erro ao buscar historico de responsabilidade  no banco de dados!", $e->getCode(), $e);
		}
	}


	function todos($limite = 0, $pulo = 0) {
		try {	
			$historico = DB::table(self::TABELA)->offset($limite)->limit($pulo)->get();
			$historicoObjects = [];

			foreach ($historico as $key => $historico) {
				$historicoObjects[] =  $this->construirObjeto($historico);
			}

			return $historicoObjects;
		}
		catch (\Exception $e) {
			throw new ColecaoException("Erro ao listar checklists.", $e->getCode(), $e);
		}
	}

	function construirObjeto(array $row) {
		$responsavelAnterior = ($row['responsavelanterior_id'] > 0) ? Dice::instance()->create('ColecaoColaborador')->comUsuarioId($row['responsavelanterior_id']) : '';
		$responsavelAtual = ($row['responsavelatual_id'] > 0) ? Dice::instance()->create('ColecaoColaborador')->comUsuarioId($row['responsavelatual_id']) : '';

		$historico = new HistoricoResponsabilidade(
			$row['id'],
			$row['datahoramudacanca'],
			null,
            $responsavelAtual,
            $responsavelAnterior
		);

		
		return $historico->toArray();
	}	

    function contagem() {
		return DB::table(self::TABELA)->count();
	}
}

?>