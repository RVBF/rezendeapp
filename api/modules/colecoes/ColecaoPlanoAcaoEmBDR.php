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
		Debuger::printr($obj);

		try {	
			DB::statement('SET FOREIGN_KEY_CHECKS=0;');

			$id = DB::table(self::TABELA)->insertGetId([
				'status' => StatusPaEnumerado::AGUARDANDO_RESPONSAVEL,
				'descricaonaoconformidade' => $obj->getDescricao(),
				'descricaosolucao' => $obj->getSolucao(),
				'datalimite' => $obj->getDataLimite()->toDateTimeString(),
				]
			);
			$obj->setId($id);		
			DB::statement('SET FOREIGN_KEY_CHECKS=0;');

		}
		catch (\Exception $e) {

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
		if($this->validarPA($obj)) {
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


	function comPerguntaId($id){
		try {	
			$tarefa = $this->construirObjeto(DB::table(self::TABELA)->select(self::TABELA. '.*')->join(ColecaoPerguntaEmBDR::TABELA, ColecaoPerguntaEmBDR::TABELA . '.tarefa_id', '=', self::TABELA . '.id')->where(ColecaoPerguntaEmBDR::TABELA . '.id', $id)->first());

			return $tarefa;
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

	function todos($limite = 0, $pulo = 0, $search = '') {
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
			$tarefas = DB::table(self::TABELA)->whereIn('id', $ids)->get();
			$tarefasObjects = [];

			foreach ($tarefas as $tarefa) {
				$tarefasObjects[] =  $this->construirObjeto($tarefa);
			}

			return $tarefasObjects;
		}
		catch (\Exception $e)
		{
			throw new ColecaoException($e->getMessage(), $e->getCode(), $e);
		}
	}

	function construirObjeto(array $row) {
		$setor = ($row['setor_id'] > 0) ? Dice::instance()->create('ColecaoSetor')->comId($row['setor_id']) : '';
		$loja = ($row['loja_id'] > 0) ? Dice::instance()->create('ColecaoLoja')->comId($row['loja_id']) : '';
		$questionador = ($row['questionador_id'] > 0) ? Dice::instance()->create('ColecaoColaborador')->comUsuarioId($row['questionador_id']) : '';
		$perguntas = Dice::instance()->create('ColecaoPergunta')->comTarefaId($row['id']);
		$tarefa = new Checklist($row['id'],$row['titulo'], $row['descricao'], $row['data_limite'], $row['data_cadastro'], $setor, $loja, $questionador, $perguntas,($row['encerrada']) ? true : false);
		return $tarefa;
	}	

    function contagem($idsLojas = []) {
		if(is_array($idsLojas))
		return (count() > 0) ?  DB::table(self::TABELA)->whereIn('loja_id', $idsLojas)->count() : DB::table(self::TABELA)->count();
	}
}

?>