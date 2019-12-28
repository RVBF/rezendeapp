<?php
use Illuminate\Database\Capsule\Manager as DB;
use Carbon\Carbon;
use \phputil\RTTI;


/**
 *	Coleção de Questionamento em Banco de Dados Relacional.
 *
 *  @author		Rafael Vinicius Barros Ferreira
 *	@version	1.0
 */

class ColecaoQuestionamentoEmBDR implements ColecaoQuestionamento {
	const TABELA = 'questionamento';

	function __construct(){}

	function adicionar(&$obj) {
		if($this->validarQuestionamento($obj)){
			try {	
				DB::statement('SET FOREIGN_KEY_CHECKS=0;');

				$id = DB::table(self::TABELA)->insertGetId([ 
					'titulo' => $obj->getTitulo(),
					'tipoQuestionamento' => $obj->getTipoQuestionamento(),
					'descricao' => $obj->getDescricao(),
					'data_limite' => $obj->getDataLimite()->toDateTimeString(),
					'questionador_id' => $obj->getQuestionador()->getId(),
					'responsavel_id' => $obj->getResponsavel()->getId(),
					'setor_id' => $obj->getSetor()->getId(),
					'loja_id' => $obj->getLoja()->getId(),
					'questionador_id' =>$obj->getQuestionador()->getId()
				]);
				
				DB::statement('SET FOREIGN_KEY_CHECKS=0;');
	
				$obj->setId($id);
	
			}
			catch (\Exception $e) {
				throw new ColecaoException("Erro ao adicionar tarefa ", $e->getCode(), $e);
			}
		}
	}
	
	function executar(&$obj){
		try {
			
			DB::statement('SET FOREIGN_KEY_CHECKS=0;');

			$filds = [ 'status' => $obj->getStatus(),
				'formularioresposta' => $obj->getFormularioResposta(),
				'planoacao_id' => ($obj->getPlanoAcao() instanceof PlanoAcao)  ? $obj->getPlanoAcao()->getId() :  0,
				'pendencia_id' => ($obj->getPendencia() instanceof Pendencia)  ? $obj->getPendencia()->getId() : 0
			];

			DB::table(self::TABELA)->where('id', $obj->getId())->update($filds);
			DB::statement('SET FOREIGN_KEY_CHECKS=1;');
		}
		catch (\Exception $e)
		{			
			throw new ColecaoException("Erro ao executar questionamento.", $e->getCode(), $e);
		}
	}
    
    function adicionarTodos($objetos = []){
        try {	
            $inserts = [];

            foreach($objetos as $obj) {
                $inserts[] = [ 
					'status' => $obj->getStatus(),
					'indice' => $obj->getIndice(),
                    'formulariopergunta' => $obj->getFormularioPergunta(),
                    'formularioresposta' => $obj->getFormularioResposta(),
                    'checklist_id' => ($obj->getChecklist() instanceof Checklist) ? $obj->getChecklist()->getId() : 0,
                ];
			}

            DB::table(self::TABELA)->insert($inserts);
        }
        catch (\Exception $e) {
            throw new ColecaoException("Erro ao adicionar tarefa ", $e->getCode(), $e);
        }
    }

	function remover($id) {
		if($this->validarDeleteQuestionamento($id)){
			try {	
				DB::table(self::TABELA)->where('deleted_at', NULL)->where('id', $id)->update(['deleted_at'=>Carbon::now()->toDateTimeString()]);
			}
			catch (\Exception $e) {
				throw new ColecaoException("Erro ao remover Questionamento.", $e->getCode(), $e);
			}
		}

	}

	function atualizar(&$obj) {
		// if($this->validarQuestionamento($obj)) {
			try {
				$filds = [ 
					'status' => $obj->getStatus(),
                    'formulariopergunta' => is_object($obj->getFormularioPergunta()) ? json_encode($obj->getFormularioPergunta()) : $obj->getFormularioPergunta(),
                    'formularioresposta' => is_object($obj->getFormularioResposta()) ? json_encode($obj->getFormularioResposta()) : $obj->getFormularioResposta()
				];

				if($obj->getPendencia() instanceof Pendencia || isset($obj->getPendencia()['id'])) $filds['pendencia_id'] =  ($obj->getPendencia() instanceof Pendencia)  ? $obj->getPendencia()->getId() : $obj->getPendencia()['id'];
				if($obj->getChecklist() instanceof Checklist || isset($obj->getChecklist()['id'])) $filds['checklist_id'] =  ($obj->getChecklist() instanceof Checklist)  ? $obj->getChecklist()->getId() : $obj->getChecklist()['id'];
				if($obj->getPlanoAcao() instanceof PlanoAcao || isset($obj->getPlanoAcao()['id'])) $filds['planoacao_id'] =  ($obj->getPlanoAcao() instanceof PlanoAcao)  ? $obj->getPlanoAcao()->getId() : $obj->getPlanoAcao()['id'];

				DB::table(self::TABELA)->where('deleted_at', NULL)->where('id', $obj->getId())->update($filds);

			}
			catch (\Exception $e)
			{
				throw new ColecaoException("Erro ao atualizar Questionamento.", $e->getCode(), $e);
			}
		// }
	}

	function comId($id){
		try {	
			return (DB::table(self::TABELA)->where('id', $id)->count()) ? $this->construirObjeto(DB::table(self::TABELA)->where('id', $id)->first()) : [];
		}
		catch (\Exception $e)
		{
			throw new ColecaoException("Erro ao buscar  questionamento!", $e->getCode(), $e);
		}
	}

	function questionamentosParaExecucao($checklistId){
		try {	

			$questionamentos = DB::table(self::TABELA)->where('deleted_at', NULL)->where('checklist_id', $checklistId)->where('status', TipoQuestionamentoEnumerado::NAO_RESPONDIDO)->get();
			$questionamentosObjects = [];

			foreach ($questionamentos as $key => $questionamento) {
				$questionamentosObjects[] = $this->construirObjeto($questionamento);
			}


			return $questionamentosObjects;
		}
		catch (\Exception $e)
		{
			throw new ColecaoException("Erro ao buscar questionamentos para execução!", $e->getCode(), $e);
		}
	}

	function questionamentosComChecklistId($checklistId = 0){
		try {
			$questionamentos = DB::table(self::TABELA)->where('deleted_at', NULL)->where('checklist_id', $checklistId)->get();
			$questionamentosObjects = [];
			foreach ($questionamentos as $key => $questionamento) {
				$questionamentosObjects[] = $this->construirObjeto($questionamento);
			}

			return $questionamentosObjects;
		} 		
		catch (\Exception $e) {
			throw new ColecaoException("Erro ao buscar questionamentos com a referência de checklist!", $e->getCode(), $e);
		}
	}

	function comPlanodeAcaoid($planoAcaoId = 0){
		try {	
			return (DB::table(self::TABELA)->where('planoacao_id', $planoAcaoId)->count()) ?  $this->construirObjeto(DB::table(self::TABELA)->where('planoacao_id', $planoAcaoId)->first()) : [];
		}
		catch (\Exception $e)
		{
			throw new ColecaoException("Erro ao buscar questionamento com a referência de plano de ação!", $e->getCode(), $e);
		}
	}

	function comPendenciaid($pendenciaId = 0){
		try {	
			return (DB::table(self::TABELA)->where('pendencia_id', $pendenciaId)->count()) ? $this->construirObjeto(DB::table(self::TABELA)->where('pendencia_id', $pendenciaId)->first()) : [];
		}
		catch (\Exception $e)
		{
			throw new ColecaoException("Erro ao buscar questionamento com a referência de plano de ação!", $e->getCode(), $e);
		}
	}

	function questionamentosComStatus($status = []){
		try {
			$questionamentos = DB::table(self::TABELA)->where('deleted_at', NULL)->whereIn('status', $status)->get();
			$questionamentoObjects = [];
			foreach ($questionamentos as $key => $questionamento) {
				$questionamentosObjects[] = $this->construirObjeto($questionamento);
			}

			return $questionamentosObjects;
		} 		
		catch (\Exception $e)
		{
			throw new ColecaoException("Erro ao buscar questionamentos com a referência de status!", $e->getCode(), $e);
		}
	}

	function todosComChecklistId($pageHome = 0, $pageLength = 10,  $search = '', $checklistId  = 0){
		try {	

			$query = DB::table(self::TABELA)->where('deleted_at', NULL)->select(self::TABELA . '.*')->where('checklist_id', $checklistId);
				
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
			

			$questionamentos = $query->orderBy('id', 'ASC')->offset($pageHome)->limit($pageLength)->get();

			$questionamentosObjects = [];
			foreach ($questionamentos as $key => $tarefa) {
				$questionamentosObjects[] =  $this->construirObjeto($tarefa);
			}

			return $questionamentosObjects;
		}
		catch (\Exception $e) {			
			throw new ColecaoException("Erro ao listar checklists.", $e->getCode(), $e);
		}
	}

	function todos($limite = 0, $pulo = 0) {
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
	
	function todosComId($ids = []) {
		try {	
			$questionamento = DB::table(self::TABELA)->where('deleted_at', NULL)->whereIn('id', $ids)->get();
			$questionamentoObjects = [];

			foreach ($questionamento as $questionamento) {
				$questionamentoObjects[] =  $this->construirObjeto($questionamento);
			}

			return $questionamentoObjects;
		}
		catch (\Exception $e)
		{
			throw new ColecaoException("Erro ao buscar questionamentos com referências!", $e->getCode(), $e);
		}
	}

	function construirObjeto(array $row) {
		$planoAcao = ($row['planoacao_id'] > 0) ? Dice::instance()->create('ColecaoPlanoAcao')->comId($row['planoacao_id']) : null;
		$pendencia = ($row['pendencia_id'] > 0) ? Dice::instance()->create('ColecaoPendencia')->comId($row['pendencia_id']) : null;
		$anexos = Dice::instance()->create('ColecaoAnexo')->comQuestionamentoId($row['id']);

		$questionamento =  new Questionamento(
			$row['id'],
			$row['status'],
			json_decode($row['formulariopergunta']),
			json_decode($row['formularioresposta']),
			$row['checklist_id'],
			$planoAcao,
			$pendencia,
			$anexos
		);

		$questionamento->setIndice($row['indice']);
		
		return $questionamento->toArray();
	}	

    function contagem($checklistId = 0, $status = []) {
		return (count($status) > 0) ? DB::table(self::TABELA)->where('deleted_at', NULL)->where('checklist_id', $checklistId)->whereIn('status',$status)->count() : DB::table(self::TABELA)->where('checklist_id', $checklistId)->where('deleted_at', NULL)->count();
	}

	function contagemPorColuna($valor = 0, $coluna = 'id') {
		return DB::table(self::TABELA)->where('deleted_at', NULL)->where($coluna, $valor)->count();
	}

	private function validarQuestionamento(&$obj) {
		if(!is_string($obj->getTitulo())) throw new ColecaoException('Valor inválido para titulo.');
		
		if(!is_string($obj->getDescricao())) throw new ColecaoException('Valor inválido para a descrição.');

		if($obj->getQuestionador() instanceof Usuario){
			$quantidade = DB::table(ColecaoUsuarioEmBDR::TABELA)->where('id', $obj->getQuestionador()->getId())->count();

			if($quantidade == 0) throw new ColecaoException('O usuário questionador não foi encontrado na base de dados.');
		}
		

		$quantidade = DB::table(ColecaoSetorEmBDR::TABELA)->where('id', $obj->getSetor()->getId())->count();

		if($quantidade == 0)throw new ColecaoException('Setor não foi encontrado na base de dados.');

		if(strlen($obj->getTitulo()) <= Questionamento::TAM_TITULO_MIM && strlen($obj->getTitulo()) > Questionamento::TAM_TITULO_MAX) throw new ColecaoException('O título deve conter no mínimo '. Questionamento::TAM_TITULO_MIM . ' e no máximo '. Questionamento::TAM_TITULO_MAX . '.');
		
		if(strlen($obj->getdescricao()) > 255 and $obj->getdescricao() <> '') throw new ColecaoException('A descrição  deve conter no máximo '. 255 . ' caracteres.');

		$quantidade = DB::table(self::TABELA)->whereRaw('titulo like  "%'. $obj->getTitulo() . '%"')->where('setor_id', $obj->getSetor()->getId())->where(self::TABELA . '.id', '<>', $obj->getId())->count();
		
		if($quantidade > 0){
			throw new ColecaoException('Já exite uma tarefa cadastrada com esse título.');
		}

		if($obj->getDataLimite() instanceof Carbon){
			if($obj->getDataLimite() < Carbon::now() and $obj->getId() == 0) throw new Exception("A data Limite deve ser maior que a atual.");
		}
		
		return true;
	}

	private function validarDeleteQuestionamento($id) {
		$quantidadeQuestionamento = DB::table(self::TABELA)->where('deleted_at', NULL)->where('id', $id)->count();
		
		if($quantidadeQuestionamento == 0) throw new ColecaoException('O questionamento selecionado para delete não foi encontrado.');
		
		return true;
	}
}

?>