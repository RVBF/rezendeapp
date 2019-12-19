<?php
use Illuminate\Database\Capsule\Manager as DB;
use Carbon\Carbon;

/**
 *	Coleção de PlanoAcao em Banco de Dados Relacional.
 *
 *  @author		Rafael Vinicius Barros Ferreira
 *	@version	1.0
 */

class ColecaoPlanoAcaoEmBDR implements ColecaoPlanoAcao {
	const TABELA = 'planoacao';

	function __construct(){}

	function adicionar(&$obj) {
		try {	
			DB::statement('SET FOREIGN_KEY_CHECKS=0;');
	
			$id = DB::table(self::TABELA)->insertGetId( [
					'status' => $obj->getStatus(),
					'descricaonaoconformidade' => $obj->getDescricao(),
					'descricaosolucao' => $obj->getSolucao(),
					'datalimite' => ($obj instanceof Carbon) ? $obj->getDataLimite()->toDateTimeString() : $obj->getDataLimite(),
					'responsabilidade' => $obj->getResponsabilidade(),
					'responsavel_id' => ($obj->getResponsavel() instanceof Colaborador) ? $obj->getResponsavel()->getId() : $obj->getResponsavel()['id']
				]
			);

			$obj->setId($id);		
			DB::statement('SET FOREIGN_KEY_CHECKS=0;');

		}
		catch (\Exception $e) {
			throw new ColecaoException("Erro ao adicionar Plano de ação!", $e->getCode(), $e);
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
				throw new ColecaoException("Erro ao remover checklist.", $e->getCode(), $e);
			}
		}

	}

	function atualizar(&$obj) {
		// if($this->validarPA($obj)) {
			try {
				
				DB::statement('SET FOREIGN_KEY_CHECKS=0;');
				$filds = [
					'status' => $obj->getStatus(),
					'descricaonaoconformidade' => $obj->getDescricao(),
					'descricaosolucao' => $obj->getSolucao(),
					'datalimite' => ($obj instanceof Carbon) ? $obj->getDataLimite()->toDateTimeString() : $obj->getDataLimite(),
					'dataexecucao' => ($obj instanceof Carbon) ? $obj->getDataExecucao()->toDateTimeString() : $obj->getDataExecucao(),
					'responsabilidade' => $obj->getResponsabilidade(),
					'responsavel_id' => ($obj->getResponsavel() instanceof Colaborador) ? $obj->getResponsavel()->getId() : $obj->getResponsavel()['id']
				];

				DB::table(self::TABELA)->where('id', $obj->getId())->update($filds);

				DB::statement('SET FOREIGN_KEY_CHECKS=1;');

				return $obj;
			}
			catch (\Exception $e) {
				throw new ColecaoException("Erro ao atualizar plano de ação.", $e->getCode(), $e);
			}
		// }
	}

	function comId($id){
		try {		
			return (DB::table(self::TABELA)->where('id', $id)->count() > 0) ? $this->construirObjeto(DB::table(self::TABELA)->where('id', $id)->first()) : [];
		}
		catch (\Exception $e){
			throw new ColecaoException("Erro ao buscar plano de ação!", $e->getCode(), $e);
		}
	}

	function todosComResponsavelId($limite = 0, $pulo = 0, $search = '', $responsavelId = 0){
		try {	

			$query = DB::table(self::TABELA)->select(self::TABELA . '.*')->where('responsavel_id', $responsavelId);

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
			
			$planosAcao = $query->groupBy('id','status', 'descricaonaoconformidade','descricaosolucao', 'datalimite', 'dataexecucao', 'responsabilidade', 'datacadastro', 'responsavel_id', 'loja_id')
								->orderByRaw(self::TABELA . '.status = "' . StatusPaEnumerado::EXECUTADO . '" ASC , '. self::TABELA.'.datalimite ASC')
								->offset($limite)
								->limit($pulo)->get();

			$planosAcaoObjects = [];

			foreach ($planosAcao as $key => $planosAcao) {	
				$planosAcaoObjects[] =  $this->construirObjeto($planosAcao);
			}

			return $planosAcaoObjects;
		}
		catch (\Exception $e)
		{			

			throw new ColecaoException("Erro ao listar checklists.", $e->getCode(), $e);
		}
	}

	function todosComChecklistId($limite = 0, $pulo = 10, $search = '', $colaboradorId = 0, $checklistId = 0){
		try {	
			$query = DB::table(self::TABELA)->select(self::TABELA .'.*')->where(self::TABELA .'.responsavel_id', $colaboradorId);
			$query->leftJoin(ColecaoQuestionamentoEmBDR::TABELA, ColecaoQuestionamentoEmBDR::TABELA. '.planoacao_id', '=', self::TABELA .'.id');
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
			
			$query->groupBy('id','status', 'descricaonaoconformidade','descricaosolucao', 'datalimite', 'dataexecucao', 'responsabilidade', 'datacadastro', 'responsavel_id', 'loja_id')
							->orderByRaw(self::TABELA . '.status = "' . StatusPaEnumerado::EXECUTADO . '" ASC , '. self::TABELA.'.datalimite ASC')
							->offset($limite)
							->limit($pulo);

			$planosAcao = $query->get();

			$planosAcaoObjects = [];

			foreach ($planosAcao as $key => $planoAcao) {
				$planosAcaoObjects[] = $this->construirObjeto($planoAcao);
			}

			return $planosAcaoObjects;
		}
		catch (\Exception $e) {			
			throw new ColecaoException("Erro ao listar planos de ação com referência de checklist.", $e->getCode(), $e);
		}
	}

	function todos($limite = 0, $pulo = 0, $search = '') {
		try {	
			$plasnosDeAcao = DB::table(self::TABELA)->offset($limite)->limit($pulo)->get();

			$plasnosDeAcaoObjects = [];

			foreach ($plasnosDeAcao as $key => $planoDeACao) {
				$plasnosDeAcaoObjects[] =  $this->construirObjeto($planoDeACao);
			}

			return $plasnosDeAcaoObjects;
		}
		catch (\Exception $e)
		{

			throw new ColecaoException("Erro ao listar checklists.", $e->getCode(), $e);
		}
	}
	
	function todosComId($ids = []) {
		try {	
			$tarefas = DB::table(self::TABELA)->whereIn('id', $ids)->get();
			$tarefasObjects = [];

			foreach ($tarefas as $tarefa) {
				$tarefasObjects[] =  $this->construirObjeto($tarefa);
			}

			return $tarefasObjects;
		}
		catch (\Exception $e)
		{
			throw new ColecaoException("Ero ao buscar planos de ação!", $e->getCode(), $e);
		}
	}

	function construirObjeto(array $row) {
		$responsavel = ($row['responsavel_id'] > 0) ? Dice::instance()->create('ColecaoColaborador')->comId($row['responsavel_id']) : '';
		$historicoAtual = Dice::instance()->create('ColecaoHistoricoResponsabilidade')->comPlanoAcaoId($row['id']);
		$anexos = Dice::instance()->create('ColecaoAnexo')->comPlanoAcaoId($row['id']);

		$planoDeAcao = new PlanoAcao(
			$row['id'],
			$row['status'],
			$row['descricaonaoconformidade'],
			$row['datalimite'],
			json_decode($row['descricaosolucao']),
			'',
			$responsavel,
			null,
			$row['datacadastro'],
			$row['dataexecucao'],
			$row['responsabilidade'],
			$historicoAtual,
			[],
			$anexos
		);

		return $planoDeAcao->toArray();
	}	

    function contagem($responsavelId = 0) {
		return ($responsavelId  > 0) ?  DB::table(self::TABELA)->where('responsavel_id', $responsavelId)->count() : DB::table(self::TABELA)->count();
	}
}

?>