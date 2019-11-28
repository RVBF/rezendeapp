<?php
use Illuminate\Database\Capsule\Manager as DB;
use Carbon\Carbon;


/**
 *	Coleção de Checklist em Banco de Dados Relacional.
 *
 *  @author		Rafael Vinicius Barros Ferreira
 *	@version	1.0
 */

class ColecaoChecklistEmBDR implements ColecaoChecklist {
	const TABELA = 'checklist';
	const TABELA_RELACIONAL = 'checklist_has_questionario';

	function __construct(){}

	function adicionar(&$obj) {
		if($this->validarTarefa($obj)){
			try {	
				DB::statement('SET FOREIGN_KEY_CHECKS=0;');
				
				$id = DB::table(self::TABELA)->insertGetId([ 
					'titulo' => $obj->getTitulo(),
					'status' => $obj->getStatus(),
					'tipoChecklist' => $obj->getTipoChecklist(),
					'descricao' => $obj->getDescricao(),
					'data_limite' => $obj->getDataLimite()->toDateTimeString(),
					'questionador_id' => $obj->getQuestionador()->getId(),
					'responsavel_id' => $obj->getResponsavel()->getId(),
					'setor_id' => $obj->getSetor()->getId(),
					'loja_id' => $obj->getLoja()->getId(),
					'questionador_id' =>$obj->getQuestionador()->getId()
				]);

				$obj->setId($id);
				$questionariosInserts = [];

				foreach ($obj->getQuestionarios() as $questionario) {
					$questionariosInserts[] = [
						'checklist_id' => $obj->getId(),
						'questionario_id' => $questionario['id'],
					];
				}

				DB::table(self::TABELA_RELACIONAL)->insert($questionariosInserts);
				
				DB::statement('SET FOREIGN_KEY_CHECKS=0;');
	
	
				return $obj;
			}
			catch (\Exception $e) {
				throw new ColecaoException("Erro ao adicionar Checklist ", $e->getCode(), $e);
			}
		}
	}

	function removerComSetorId($id, $idSetor) {
		if($this->validarRemocaoTarefa($id)){
			try {	
				DB::statement('SET FOREIGN_KEY_CHECKS=0;');
				$removido = DB::table(self::TABELA)->where('id', $id)->where('setor_id', $idSetor)->delete();
				DB::statement('SET FOREIGN_KEY_CHECKS=1;');
				return $removido;
			}
			catch (\Exception $e) {
				throw new ColecaoException("Erro ao remover categoria com o id do setor.", $e->getCode(), $e);
			}
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
		// if($this->validarTarefa($obj)) {
			try {
				
				DB::statement('SET FOREIGN_KEY_CHECKS=0;');

				$filds = [ 
					'titulo' => $obj->getTitulo(),
					'status' => $obj->getStatus(),
					'tipoChecklist' => $obj->getTipoChecklist(),
					'descricao' => $obj->getDescricao(),
					'data_limite' => ($obj->getDataLimite() instanceof Carbon) ? $obj->getDataLimite()->toDateTimeString(): $obj->getDataLimite(),
					'questionador_id' => ($obj->getQuestionador() instanceof Colaborador) ? $obj->getQuestionador()->getId() : $obj->getQuestionador()['id'],
					'responsavel_id' => ($obj->getResponsavel() instanceof Colaborador) ? $obj->getResponsavel()->getId() : $obj->getResponsavel()['id'],
					'setor_id' => ($obj->getSetor() instanceof Setor) ? $obj->getSetor()->getId() : $obj->getSetor()['id'],
					'loja_id' => ($obj->getLoja() instanceof Loja) ? $obj->$obj->getLoja()->getId() : $obj->getLoja()['id'],
					'questionador_id' => ($obj->getQuestionador() instanceof Colaborador) ?  $obj->getQuestionador()->getId() : $obj->getQuestionador()['id']
				];

				DB::table(self::TABELA)->where('id', $obj->getId())->update($filds);

				DB::statement('SET FOREIGN_KEY_CHECKS=1;');

				return $obj;
			}
			catch (\Exception $e)
			{
				throw new ColecaoException("Erro ao atualizar tarefa.", $e->getCode(), $e);
			}
		// }
	}

	function comId($id){
		try {	
			return (DB::table(self::TABELA)->where('id', $id)->count() >0 ) ? $this->construirObjeto(DB::table(self::TABELA)->where('id', $id)->first()) : [];
		}
		catch (\Exception $e){
			throw new ColecaoException("Erro ao buscar checklist no banco de dados", $e->getCode(), $e);
		}
	}

	function todosComLojaIds($limite = 0, $pulo = 10, $search = '', $idsLojas = []){
		try {	
			$query = DB::table(self::TABELA)->selectRaw(self::TABELA . '.*, COUNT('.self::TABELA.'.id) as qtd')->whereIn('loja_id',$idsLojas);
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
			
			$checklists = $query->groupBy('id','status', 'titulo','descricao', 'data_limite', 'tipoChecklist', 'data_cadastro', 'encerrado', 'questionador_id', 'responsavel_id', 'setor_id', 'checklist_id', 'loja_id')
								->orderByRaw(self::TABELA . '.status = "' . StatusChecklistEnumerado::EXECUTADO . '" ASC , '. self::TABELA.'.data_limite ASC')
								->offset($limite)
								->limit($pulo)
								->get();

			$checklistsObjects = [];

			foreach ($checklists as $key => $checklist) {
				$checklistsObjects[] = $this->construirObjeto($checklist);
			}

			return $checklistsObjects;
		}
		catch (\Exception $e) {	
			throw new ColecaoException("Erro ao listar checklists.", $e->getCode(), $e);
		}
	}
	
	function todos($limite = 0, $pulo = 0) {
		try {	
			$checklist = DB::table(self::TABELA)->offset($limite)->limit($pulo)->get();
			$checklistObjects = [];

			foreach ($checklist as $key => $checklist) {
				$checklistObjects[] =  $this->construirObjeto($checklist);
			}

			return $checklistObjects;
		}
		catch (\Exception $e) {
			throw new ColecaoException("Erro ao listar checklists.", $e->getCode(), $e);
		}
	}
	
	function listagemTemporalcomLojasIds($pageHome = 0, $pageLength = 10,$search = '', $idsLojas = []){
		try {	
			$checklists = DB::table(self::TABELA)->whereIn('loja_id',$idsLojas)->orderBy('data_limite', 'ASC')->offset($pageHome)->limit($pageLength)->get();

			$checklistsObjects = [];
			foreach ($checklists as $key => $checklist) {
				$checklistsObjects[] =  $this->construirObjeto($checklist);
			}

			return $checklistsObjects;
		}
		catch (\Exception $e) {
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
			throw new ColecaoException("Erro ao buscar checklists no banco de dados!", $e->getCode(), $e);
		}
	}

	function construirObjeto(array $row) {
		$setor = ($row['setor_id'] > 0) ? Dice::instance()->create('ColecaoSetor')->comId($row['setor_id']) : '';
		$loja = ($row['loja_id'] > 0) ? Dice::instance()->create('ColecaoLoja')->comId($row['loja_id']) : '';
		$questionador = ($row['questionador_id'] > 0) ? Dice::instance()->create('ColecaoColaborador')->comUsuarioId($row['questionador_id']) : '';
		$responsavel = ($row['responsavel_id'] > 0) ? Dice::instance()->create('ColecaoColaborador')->comUsuarioId($row['responsavel_id']) : '';		
		$questionamentos = ($row['id'] > 0) ? Dice::instance()->create('ColecaoQuestionamento')->questionamentosComChecklistId($row['id']) : '';


		$checklist = new Checklist(
			$row['id'],
			$row['status'], 
			$row['titulo'],
			$row['descricao'], 
			$row['data_limite'], 
			$row['data_cadastro'], 			
			$row['tipoChecklist'], 			
			$setor,
			$loja,
			$questionador, 
			$responsavel,
			null,
			$questionamentos
		);

		return $checklist->toArray();
	}	

    function contagem($idsLojas = []) {
		return (count($idsLojas) > 0) ?  DB::table(self::TABELA)->whereIn('loja_id', $idsLojas)->count() : DB::table(self::TABELA)->count();
	}

	function temPendencia($idChecklist = 0){
		try {	
			$questionamentos = Dice::instance()->create('ColecaoQuestionamento')->questionamentosComChecklistId($idChecklist);

			foreach($questionamentos as $questionamento){
				$questionamentoAtual = new Questionamento(); $questionamentoAtual->fromArray($questionamento);
				
				if($questionamentoAtual->getStatus() != TipoQuestionamentoEnumerado::RESPONDIDO) {
					return true;
				}
			}

			foreach($questionamentos as $questionamento){
				$questionamentoAtual = new Questionamento(); $questionamentoAtual->fromArray($questionamento);
				
				if($questionamentoAtual->getPlanoAcao()){
					$planoAcao = new PlanoAcao(); $planoAcao->fromArray($questionamentoAtual->getPlanoAcao());

					if($planoAcao->getStatus() != StatusPaEnumerado::EXECUTADO) {
						return true;
					}
				}
			}

			foreach($questionamentos as $questionamento){
				$questionamentoAtual = new Questionamento(); $questionamentoAtual->fromArray($questionamento);
				
				if($questionamentoAtual->getPendencia()){

					$pendencia = new Pendencia(); $pendencia->fromArray($questionamentoAtual->getPendencia());

					if($pendencia->getStatus() != StatusPendenciaEnumerado::EXECUTADO) return true;
				}
			}

			return false;
		}
		catch (\Exception $e)
		{

			throw new ColecaoException("Erro ao buscar checklists no banco de dados!", $e->getCode(), $e);
		}
	}

	private function validarTarefa(&$obj) {
		// if(!is_string($obj->getTitulo())) throw new ColecaoException('Valor inválido para titulo.');
		
		// if(!is_string($obj->getDescricao())) throw new ColecaoException('Valor inválido para a descrição.');

		// if($obj->getQuestionador() instanceof Usuario){
		// 	$quantidade = DB::table(ColecaoUsuarioEmBDR::TABELA)->where('id', $obj->getQuestionador()->getId())->count();

		// 	if($quantidade == 0) throw new ColecaoException('O usuário questionador não foi encontrado na base de dados.');
		// }
		

		// $quantidade = DB::table(ColecaoSetorEmBDR::TABELA)->where('id', $obj->getSetor()->getId())->count();

		// if($quantidade == 0)throw new ColecaoException('Setor não foi encontrado na base de dados.');

		// if(strlen($obj->getTitulo()) <= Checklist::TAM_TITULO_MIM && strlen($obj->getTitulo()) > Checklist::TAM_TITULO_MAX) throw new ColecaoException('O título deve conter no mínimo '. Checklist::TAM_TITULO_MIM . ' e no máximo '. Checklist::TAM_TITULO_MAX . '.');
		
		// if(strlen($obj->getdescricao()) > 255 and $obj->getdescricao() <> '') throw new ColecaoException('A descrição  deve conter no máximo '. 255 . ' caracteres.');

		// $quantidade = DB::table(self::TABELA)->whereRaw('titulo like  "%'. $obj->getTitulo() . '%"')->where('setor_id', $obj->getSetor()->getId())->where(self::TABELA . '.id', '<>', $obj->getId())->count();
		
		// if($quantidade > 0){
		// 	throw new ColecaoException('Já exite uma tarefa cadastrada com esse título.');
		// }

		// if($obj->getDataLimite() instanceof Carbon){
		// 	if($obj->getDataLimite() < Carbon::now() and $obj->getId() == 0) throw new Exception("A data Limite deve ser maior que a atual.");
		// }
		
		return true;
	}

	private function validarRemocaoTarefa($id){
		$quantidade = DB::table(ColecaoPerguntaEmBDR::TABELA)->where('tarefa_id', $id)->count();

		if($quantidade > 0)throw new ColecaoException('Não foi possível excluir a tarefa por que ela possui perguntas relacionadas a ela. Exclua todas as perguntas relacionadas e tente novamente.');
		return true;
	}
}

?>