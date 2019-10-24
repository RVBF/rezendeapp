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
		if($this->validarTarefa($obj)){
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
	
				return $obj;
			}
			catch (\Exception $e) {
				Debuger::printr( $e->getMessage());
				throw new ColecaoException("Erro ao adicionar tarefa ", $e->getCode(), $e);
			}
		}
    }
    
    function adicionarTodos($objetos = []){
        try {	
            DB::statement('SET FOREIGN_KEY_CHECKS=0;');
            
            $inserts = [];
            
            foreach($objetos as $obj) {
                $inserts[] = [ 
                    'status' => $obj->getStatus(),
                    'formulariopergunta' => $obj->getFormularioPergunta(),
                    'formularioresposta' => $obj->getFormularioResposta(),
                    'checklist_id' => $obj->getCheckList(),
                ];
            }

            DB::table(self::TABELA)->insert($inserts);
            
            DB::statement('SET FOREIGN_KEY_CHECKS=0;');

            return true;
        }
        catch (\Exception $e) {
            Debuger::printr( $e->getMessage());
            throw new ColecaoException("Erro ao adicionar tarefa ", $e->getCode(), $e);
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
		if($this->validarTarefa($obj)) {
			try {
				
				DB::statement('SET FOREIGN_KEY_CHECKS=0;');

				$filds = [ 'titulo' => $obj->getTitulo(),
					'descricao' => $obj->getDescricao(),
					'data_limite' => ($obj->getDataLimite() instanceof Carbon)  ? $obj->getDataLimite()->toDateTimeString() : $obj->getDataLimite(),
					'encerrada' => $obj->getEncerrada(),
					'setor_id' => $obj->getSetor()->getId(),
					'loja_id' => $obj->getLoja()->getId()
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

	function comChecklistId($id){
		try {	

			$questionamentos = DB::table(self::TABELA)->where('checklist_id', $id)->get();
			$questionamentosObjects = [];

			foreach ($questionamentos as $key => $questionamento) {
				$questionamentosObjects[] =  RTTI::getAttributes($this->construirObjeto($questionamento), RTTI::allFlags());
			}


			return $questionamentosObjects;
		}
		catch (\Exception $e)
		{
			throw new ColecaoException($e->getMessage(), $e->getCode(), $e);
		}
	}

	function todosComLojaIds($limite = 0, $pulo = 0, $search = '', $idsLojas = []){
		try {	

			$query = DB::table(self::TABELA)->select(self::TABELA . '.*')->whereIn('loja_id', $idsLojas);
				
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
		catch (\Exception $e)
		{			
			throw new ColecaoException("Erro ao listar tarefas.", $e->getCode(), $e);
		}
	}

	function todos($limite = 0, $pulo = 0) {
		try {	
			$tarefas = DB::table(self::TABELA)->offset($limite)->limit($pulo)->get();
			$tarefasObjects = [];

			foreach ($tarefas as $key => $tarefa) {
				$tarefasObjects[] =  $this->construirObjeto($tarefa);
			}

			return $tarefasObjects;
		}
		catch (\Exception $e)
		{
			throw new ColecaoException("Erro ao listar tarefas.", $e->getCode(), $e);
		}
	}
	
	function todosComId($ids = []) {
		try {	
			$questionamento = DB::table(self::TABELA)->whereIn('id', $ids)->get();
			$questionamentoObjects = [];

			foreach ($questionamento as $questionamento) {
				$questionamentoObjects[] =  $this->construirObjeto($questionamento);
			}

			return $questionamentoObjects;
		}
		catch (\Exception $e)
		{
			throw new ColecaoException($e->getMessage(), $e->getCode(), $e);
		}
	}

	function construirObjeto(array $row) {
		$checklist = ($row['checklist_id'] > 0) ? Dice::instance()->create('ColecaoChecklist')->comId($row['checklist_id']) : null;
		$planoacao = ($row['planoacao_id'] > 0) ? Dice::instance()->create('ColecaoLoja')->comId($row['planoacao_id']) : null;

		$questionamento =  new Questionamento(
			$row['id'],
			$row['status'],
			json_decode($row['formulariopergunta']),
			json_decode($row['formulariopergunta']),
			$checklist,
			$planoacao ,
			[]
		);

		return $questionamento;
	}	

    function contagem($idsLojas = []) {
		return (count($idsLojas) > 0) ?  DB::table(self::TABELA)->whereIn('loja_id', $idsLojas)->count() : DB::table(self::TABELA)->count();
	}

	private function validarTarefa(&$obj) {
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

	private function validarRemocaoTarefa($id){
		$quantidade = DB::table(ColecaoPerguntaEmBDR::TABELA)->where('tarefa_id', $id)->count();

		if($quantidade > 0)throw new ColecaoException('Não foi possível excluir a tarefa por que ela possui perguntas relacionadas a ela. Exclua todas as perguntas relacionadas e tente novamente.');
		return true;
	}
}

?>