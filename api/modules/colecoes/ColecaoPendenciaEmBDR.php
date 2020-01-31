<?php
use Illuminate\Database\Capsule\Manager as DB;
use Carbon\Carbon;

/**
 *	Coleção de Pendencia em Banco de Dados Relacional.
 *
 *  @author		Rafael Vinicius Barros Ferreira
 *	@version	1.0
 */

class ColecaoPendenciaEmBDR implements ColecaoPendencia {
	const TABELA = 'pendencia';

	function __construct(){}

	function adicionar(&$obj) {
		try {	
			$id = DB::table(self::TABELA)->insertGetId([
					'status' => $obj->getStatus(),
					'descricao' => $obj->getDescricao(),
					'descricaosolucao' => $obj->getSolucao(),
					'datalimite' => ($obj->getDataLimite() instanceof Carbon) ? $obj->getDataLimite()->toDateTimeString() : $obj->getDataLimite(),
					'responsavel_id' => $obj->getResponsavel()->getId()
				]
			);
			
			$obj->setId($id);	
		}
		catch (\Exception $e) {
			throw new ColecaoException("Erro ao adicionar nova pendência", $e->getCode(), $e);
		}
	
	}

	function remover($id) {
		// if($this->validarRemocaoTarefa($id)){
			try {	
				DB::table(self::TABELA)->where('deleted_at', NULL)->where('id', $id)->update(['deleted_at' => Carbon::now()->toDateTimeString()]);
			}
			catch (\Exception $e) {
				throw new ColecaoException("Erro ao remover checklist.", $e->getCode(), $e);
			}
		// }
	}

	function atualizar(&$obj) {
			try {
				
				$filds = [ 'status' => $obj->getStatus(),
					'descricao' => $obj->getDescricao(),
					'descricaosolucao' => $obj->getSolucao(),
					'dataexecucao' => ($obj->getDataExecucao() instanceof Carbon) ? $obj->getDataExecucao()->toDateTimeString() : $obj->getDataExecucao(), 
					'datalimite' => ($obj->getDataLimite() instanceof Carbon) ? $obj->getDataLimite()->toDateTimeString() : $obj->getDataLimite(), 
					'responsavel_id' => ($obj->getResponsavel() instanceof Colaborador) ? $obj->getResponsavel()->getId() : $obj->getResponsavel()['id']
				];
				
				DB::table(self::TABELA)->where('deleted_at', NULL)->where('id', $obj->getId())->update($filds);

				return $obj;
			}
			catch (\Exception $e) {
				throw new ColecaoException("Erro ao atualizar tarefa.", $e->getCode(), $e);
			}
	}

	function comId($id){
		try {	

			return (DB::table(self::TABELA)->where('id', $id)->count() > 0)	? $this->construirObjeto(DB::table(self::TABELA)->where('id', $id)->first()) : [];
		}
		catch (\Exception $e)
		{
			throw new ColecaoException('Erro ao buscar pendência com id!', $e->getCode(), $e);
		}
	}


	function todosComResponsavelId($limite = 0, $pulo = 0, $search = '', $responsavelId = 0){
		try {	
			$query = DB::table(self::TABELA)->where(self::TABELA . '.deleted_at', NULL)->select(self::TABELA . '.*')->where('responsavel_id', $responsavelId);

			if($search != '') {
				$buscaCompleta = $search;
				$palavras = explode(' ', $buscaCompleta);

				$query->leftJoin(ColecaoLojaEmBDR::TABELA, ColecaoLojaEmBDR::TABELA. '.id', '=', self::TABELA .'.loja_id');
				$query->leftJoin(ColecaoColaboradorEmBDR::TABELA . ' as responsavel', 'responsavel.id', '=', self::TABELA . '.responsavel_id');

				$query->where(function($query) use($buscaCompleta){
					$query->whereRaw(self::TABELA . '.id like "%' . $buscaCompleta . '%"');
					$query->orWhereRaw(self::TABELA . '.status like "%' . $buscaCompleta . '%"');
					$query->orWhereRaw(self::TABELA . '.descricao like "%' . $buscaCompleta . '%"');
					$query->orWhereRaw(self::TABELA . '.descricaosolucao like "%' . $buscaCompleta . '%"');
					$query->orWhereRaw(ColecaoLojaEmBDR::TABELA . '.razaoSocial like "%' . $buscaCompleta . '%"');
					$query->orWhereRaw(ColecaoLojaEmBDR::TABELA . '.nomeFantasia like "%' . $buscaCompleta . '%"');
					$query->orWhereRaw('responsavel.nome like "%' . $buscaCompleta . '%"');
					$query->orWhereRaw('responsavel.sobrenome like "%' . $buscaCompleta . '%"');
					$query->orWhereRaw('DATE_FORMAT('. self::TABELA .'.datalimite, "%d/%m/%Y") like "%' . $buscaCompleta . '%"');
					$query->orWhereRaw('DATE_FORMAT('. self::TABELA .'.dataexecucao, "%d/%m/%Y") like "%' . $buscaCompleta . '%"');
					$query->orWhereRaw('DATE_FORMAT('. self::TABELA .'.datacadastro, "%d/%m/%Y") like "%' . $buscaCompleta . '%"');


				});

			
				if($query->count() == 0){
					$query->where(function($query) use ($palavras){
						foreach ($palavras as $key => $palavra) {
							if($palavra != " "){
								$query->whereRaw(self::TABELA . '.id like "%' . $palavra . '%"');
								$query->orWhereRaw(self::TABELA . '.status like "%' . $palavra . '%"');
								$query->orWhereRaw(self::TABELA . '.descricao like "%' . $palavra . '%"');
								$query->orWhereRaw(self::TABELA . '.descricaosolucao like "%' . $palavra . '%"');
								$query->orWhereRaw(ColecaoLojaEmBDR::TABELA . '.razaoSocial like "%' . $palavra . '%"');
								$query->orWhereRaw(ColecaoLojaEmBDR::TABELA . '.nomeFantasia like "%' . $palavra . '%"');
								$query->orWhereRaw('responsavel.nome like "%' . $palavra . '%"');
								$query->orWhereRaw('responsavel.sobrenome like "%' . $palavra . '%"');
								$query->orWhereRaw('DATE_FORMAT('. self::TABELA .'.datalimite, "%d/%m/%Y") like "%' . $palavra . '%"');
								$query->orWhereRaw('DATE_FORMAT('. self::TABELA .'.dataexecucao, "%d/%m/%Y") like "%' . $palavra . '%"');
								$query->orWhereRaw('DATE_FORMAT('. self::TABELA .'.datacadastro, "%d/%m/%Y") like "%' . $palavra . '%"');
							}
						}
						
					});
				}
				$query->groupBy(self::TABELA.'.id');
			}
			
			$pendencia = $query->groupBy('id','status', 'descricao','descricaosolucao', 'datalimite', 'dataexecucao', 'datacadastro', 'responsavel_id', 'loja_id')
								->orderByRaw(self::TABELA . '.status = "' . StatusPaEnumerado::EXECUTADO . '" ASC , '. self::TABELA.'.datalimite ASC')
								->offset($limite)
								->limit($pulo)->get();

			$pendenciaObjects = [];

			foreach ($pendencia as $key => $pendencia) {	

				$pendenciaObjects[] =  $this->construirObjeto($pendencia);
			}

			return $pendenciaObjects;
		}
		catch (\Exception $e){
			Util::printr($e->getMessage());			
			throw new ColecaoException("Erro ao listar pendência com responsável.", $e->getCode(), $e);
		}
	}


	function todosComLojaIds($limite = 0, $pulo = 0, $search = '', $idsLojas = []){
		try {	

			$query = DB::table(self::TABELA)->where(self::TABELA . '.deleted_at', NULL)->select(self::TABELA . '.*')->whereIn('loja_id', $idsLojas);
				
			if($search != '') {
				$buscaCompleta = $search;
				$palavras = explode(' ', $buscaCompleta);

				$query->leftJoin(ColecaoLojaEmBDR::TABELA, ColecaoLojaEmBDR::TABELA. '.id', '=', self::TABELA .'.loja_id');
				$query->leftJoin(ColecaoUsuarioEmBDR::TABELA, ColecaoUsuarioEmBDR::TABELA. '.id', '=', self::TABELA .'.questionador_id');
				$query->leftJoin(ColecaoColaboradorEmBDR::TABELA, ColecaoColaboradorEmBDR::TABELA. '.usuario_id', '=', ColecaoUsuarioEmBDR::TABELA .'.id');
				$query->leftJoin(ColecaoSetorEmBDR::TABELA, ColecaoSetorEmBDR::TABELA. '.id', '=', self::TABELA .'.setor_id');
				
				$query->where(function($query) use($buscaCompleta){
					$query->whereRaw(self::TABELA . '.id like "%' . $buscaCompleta . '%"');
					$query->orWhereRaw(self::TABELA . '.titulo like "%' . $buscaCompleta . '%"');
					$query->orWhereRaw(self::TABELA . '.descricao like "%' . $buscaCompleta . '%"');
					$query->orWhereRaw(ColecaoLojaEmBDR::TABELA . '.razaoSocial like "%' . $buscaCompleta . '%"');
					$query->orWhereRaw(ColecaoLojaEmBDR::TABELA . '.nomeFantasia like "%' . $buscaCompleta . '%"');
					$query->orWhereRaw(ColecaoSetorEmBDR::TABELA . '.titulo like "%' . $buscaCompleta . '%"');
					$query->orWhereRaw(ColecaoColaboradorEmBDR::TABELA . '.nome like "%' . $buscaCompleta . '%"');
					$query->orWhereRaw(ColecaoColaboradorEmBDR::TABELA . '.sobrenome like "%' . $buscaCompleta . '%"');
					$query->orWhereRaw('DATE_FORMAT('. self::TABELA .'.data_limite, "%d/%m/%Y") like "%' . $buscaCompleta . '%"');
				});

			
				if($query->count() == 0){
					$query->where(function($query) use ($palavras){
						foreach ($palavras as $key => $palavra) {
							if($palavra != " "){
								$query->whereRaw(self::TABELA . '.id like "%' . $palavra . '%"');
								$query->orWhereRaw(self::TABELA . '.titulo like "%' . $palavra . '%"');
								$query->orWhereRaw(self::TABELA . '.descricao like "%' . $palavra . '%"');
								$query->orWhereRaw(ColecaoLojaEmBDR::TABELA . '.razaoSocial like "%' . $palavra . '%"');
								$query->orWhereRaw(ColecaoLojaEmBDR::TABELA . '.nomeFantasia like "%' . $palavra . '%"');
								$query->orWhereRaw(ColecaoSetorEmBDR::TABELA . '.titulo like "%' . $palavra . '%"');
								$query->orWhereRaw(ColecaoColaboradorEmBDR::TABELA . '.nome like "%' . $palavra . '%"');
								$query->orWhereRaw(ColecaoColaboradorEmBDR::TABELA . '.sobrenome like "%' . $palavra . '%"');
								$query->orWhereRaw('DATE_FORMAT('. self::TABELA .'.data_limite, "%d/%m/%Y") like "%' . $palavra . '%"');
							}
						}
						
					});
				}
				$query->groupBy(self::TABELA.'.id');
			}
			

			$tarefas = $query->offset($limite)->limit($pulo)->get();

			$tarefasObjects = [];
			foreach ($tarefas as $key => $tarefa) {
				$tarefasObjects[] =  $this->construirObjeto($tarefa);
			}

			return $tarefasObjects;
		}
		catch (\Exception $e) {			
			throw new ColecaoException("Erro ao listar checklists.", $e->getCode(), $e);
		}
	}

	function todos($limite = 0, $pulo = 0, $search = '') {
		try {	
			$tarefas = DB::table(self::TABELA)->where('deleted_at', NULL)->offset($limite)->limit($pulo)->get();
			$tarefasObjects = [];

			foreach ($tarefas as $key => $tarefa) {
				$tarefasObjects[] =  $this->construirObjeto($tarefa);
			}

			return $tarefasObjects;
		}
		catch (\Exception $e)
		{
			throw new ColecaoException("Erro ao listar checklists.", $e->getCode(), $e);
		}
	}
	

	function todosComChecklistId($limite = 0, $pulo = 10, $search = '', $colaboradorId = 0, $checklistId = 0){
		try {	
			$query = DB::table(self::TABELA)->where(self::TABELA . '.deleted_at', NULL)->select(self::TABELA .'.*')->where(self::TABELA .'.responsavel_id', $colaboradorId);
			$query->leftJoin(ColecaoQuestionamentoEmBDR::TABELA, ColecaoQuestionamentoEmBDR::TABELA. '.pendencia_id', '=', self::TABELA .'.id');
			$query->leftJoin(ColecaoChecklistEmBDR::TABELA, ColecaoQuestionamentoEmBDR::TABELA. '.checklist_id', '=', ColecaoChecklistEmBDR::TABELA .'.id');

			$query->where(ColecaoChecklistEmBDR::TABELA .'.id', $checklistId);
			
			if($search != '') {
				$buscaCompleta = $search;
				$palavras = explode(' ', $buscaCompleta);

				$query->leftJoin(ColecaoLojaEmBDR::TABELA, ColecaoLojaEmBDR::TABELA. '.id', '=', self::TABELA .'.loja_id');
				$query->leftJoin(ColecaoUsuarioEmBDR::TABELA, ColecaoUsuarioEmBDR::TABELA. '.id', '=', self::TABELA .'.questionador_id');
				$query->leftJoin(ColecaoColaboradorEmBDR::TABELA, ColecaoColaboradorEmBDR::TABELA. '.usuario_id', '=', ColecaoUsuarioEmBDR::TABELA .'.id');
				$query->leftJoin(ColecaoSetorEmBDR::TABELA, ColecaoSetorEmBDR::TABELA. '.id', '=', self::TABELA .'.setor_id');
				
				$query->where(function($query) use($buscaCompleta){
					$query->whereRaw(self::TABELA . '.id like "%' . $buscaCompleta . '%"');
					$query->orWhereRaw(self::TABELA . '.titulo like "%' . $buscaCompleta . '%"');
					$query->orWhereRaw(self::TABELA . '.descricao like "%' . $buscaCompleta . '%"');
					$query->orWhereRaw(ColecaoLojaEmBDR::TABELA . '.razaoSocial like "%' . $buscaCompleta . '%"');
					$query->orWhereRaw(ColecaoLojaEmBDR::TABELA . '.nomeFantasia like "%' . $buscaCompleta . '%"');
					$query->orWhereRaw(ColecaoSetorEmBDR::TABELA . '.titulo like "%' . $buscaCompleta . '%"');
					$query->orWhereRaw(ColecaoColaboradorEmBDR::TABELA . '.nome like "%' . $buscaCompleta . '%"');
					$query->orWhereRaw(ColecaoColaboradorEmBDR::TABELA . '.sobrenome like "%' . $buscaCompleta . '%"');
					$query->orWhereRaw('DATE_FORMAT('. self::TABELA .'.data_limite, "%d/%m/%Y") like "%' . $buscaCompleta . '%"');
				});

			
				if($query->count() == 0){
					$query->where(function($query) use ($palavras){
						foreach ($palavras as $key => $palavra) {
							if($palavra != " "){
								$query->whereRaw(self::TABELA . '.id like "%' . $palavra . '%"');
								$query->orWhereRaw(self::TABELA . '.titulo like "%' . $palavra . '%"');
								$query->orWhereRaw(self::TABELA . '.descricao like "%' . $palavra . '%"');
								$query->orWhereRaw(ColecaoLojaEmBDR::TABELA . '.razaoSocial like "%' . $palavra . '%"');
								$query->orWhereRaw(ColecaoLojaEmBDR::TABELA . '.nomeFantasia like "%' . $palavra . '%"');
								$query->orWhereRaw(ColecaoSetorEmBDR::TABELA . '.titulo like "%' . $palavra . '%"');
								$query->orWhereRaw(ColecaoColaboradorEmBDR::TABELA . '.nome like "%' . $palavra . '%"');
								$query->orWhereRaw(ColecaoColaboradorEmBDR::TABELA . '.sobrenome like "%' . $palavra . '%"');
								$query->orWhereRaw('DATE_FORMAT('. self::TABELA .'.data_limite, "%d/%m/%Y") like "%' . $palavra . '%"');
							}
						}
						
					});
				}
				$query->groupBy(self::TABELA.'.id');
			}
			
			$query->groupBy('id','status', 'descricao','descricaosolucao', 'datalimite', 'dataexecucao', 'datacadastro', 'responsavel_id', 'loja_id')
							->orderByRaw(self::TABELA . '.status = "' . StatusPaEnumerado::EXECUTADO . '" ASC , '. self::TABELA.'.datalimite ASC')
							->offset($limite)
							->limit($pulo);

			$pendencias = $query->get();

			$pendenciasObjects = [];

			foreach ($pendencias as $key => $pendencia) {
				$pendenciasObjects[] = $this->construirObjeto($pendencia);
			}

			return $pendenciasObjects;
		}
		catch (\Exception $e)
		{			
			throw new ColecaoException("Erro ao listar checklists.", $e->getCode(), $e);
		}
	}
	function todosComId($ids = []) {
		try {	
			$tarefas = DB::table(self::TABELA)->where(self::TABELA. '.deleted_at', NULL)->whereIn('id', $ids)->get();
			$tarefasObjects = [];

			foreach ($tarefas as $tarefa) {
				$tarefasObjects[] =  $this->construirObjeto($tarefa);
			}

			return $tarefasObjects;
		}
		catch (\Exception $e)
		{
			throw new ColecaoException("Erro ao listar Pendência!", $e->getCode(), $e);
		}
	}

	function construirObjeto(array $row) {
		$responsavel = ($row['responsavel_id'] > 0) ? Dice::instance()->create('ColecaoColaborador')->comId($row['responsavel_id']) : '';

		$pendencia = new Pendencia(
			$row['id'],
            $row['status'],
			$row['descricao'],
			$row['datalimite'],
			$row['descricaosolucao'],
            $responsavel,
			$row['datacadastro'],
            $row['dataexecucao']
		);
		
		return $pendencia->toArray();
	}	

    function contagem($idsLojas = []) {
		if(is_array($idsLojas))
        return (count($idsLojas) > 0) ?  DB::table(self::TABELA)->whereIn('loja_id',
         $idsLojas)->count() : DB::table(self::TABELA)->count();
	}
}

?>