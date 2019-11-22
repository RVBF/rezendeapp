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
    
	function remover($id) {
		if($this->validarRemocaoTarefa($id)){
			try {	
				DB::statement('SET FOREIGN_KEY_CHECKS=0;');
				$removido = DB::table(self::TABELA)->where('id', $id)->delete();
				DB::statement('SET FOREIGN_KEY_CHECKS=1;');
				return $removido;
			}
			catch (\Exception $e)
			{
				throw new ColecaoException("Erro ao remover categoria.", $e->getCode(), $e);
			}
		}

	}

	function atualizar(&$obj) {
        try {
            
            DB::statement('SET FOREIGN_KEY_CHECKS=0;');

            $filds = [ 
                'data' => $obj->getData(),
                'planoacao_id' => $obj->getPlanoAcao()->getId(),
                'responsavelatual_id' =>$obj->getResponsavelAtual()->getId(),
                'responsavelanterior_id' => $obj->getResponsavelAnterior()->getId()
            ];

            DB::table(self::TABELA)->where('id', $obj->getId())->update($filds);

            DB::statement('SET FOREIGN_KEY_CHECKS=1;');

            return $obj;
        }
        catch (\Exception $e)
        {
            throw new ColecaoException("Erro ao atualizar tarefa.", $e->getCode(), $e);
        }
	}

	function comId($id){
		try {	
			$tarefa = $this->construirObjeto(DB::table(self::TABELA)->where('id', $id)->get()[0]);

			return $tarefa;
		}
		catch (\Exception $e)
		{
			throw new ColecaoException($e->getMessage(), $e->getCode(), $e);
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
		catch (\Exception $e)
		{
			throw new ColecaoException("Erro ao listar tarefas.", $e->getCode(), $e);
		}
	}

	function construirObjeto(array $row) {
		$responsavelAnterior = ($row['responsavelanterior_id'] > 0) ? Dice::instance()->create('ColecaoColaborador')->comUsuarioId($row['responsavelanterior_id']) : '';
		$responsavelAtual = ($row['responsavelatual_id'] > 0) ? Dice::instance()->create('ColecaoColaborador')->comUsuarioId($row['responsavelatual_id']) : '';

		$historico = new HistoricoResponsabilidade(
			$row['id'],
            $row['datahoramudacanca'],
            $responsavelAtual,
            $responsavelAnterior
		);
		return $checklist->toArray();
	}	

    function contagem() {
		return DB::table(self::TABELA)->count();
	}
}

?>