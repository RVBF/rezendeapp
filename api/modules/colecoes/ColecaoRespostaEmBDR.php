<?php
use Illuminate\Database\Capsule\Manager as Db;
/**
 *	Coleção de Resposta em Banco de Dados Relacional.
 *
 *  @author		Rafael Vinicius Barros Ferreira
 *	@version	0.1
 */

class ColecaoRespostaEmBDR implements ColecaoResposta
{

	const TABELA = 'resposta';
	const TABELA_RELACIONAL = 'resposta_formulariorespondido';

	function __construct(){}

	function adicionar(&$obj) {
		try {	

			$id = Db::table(self::TABELA)->insertGetId([ 'opcaoSelecionada' => $obj->getOpcaoSelecionada(), 'comentario' => $obj->getComentario()]);

			$obj->setId($id);

			return $obj;
		}
		catch (\Exception $e)
		{
			throw new ColecaoException("Erro ao cadastrar resposta.", $e->getCode(), $e);
		}
	}

	function adicionarComFormularioID(&$obj, $idFormulario) {
		try {	
			DB::statement('SET FOREIGN_KEY_CHECKS=0;');

			$id = Db::table(self::TABELA)->insertGetId([ 'opcaoSelecionada' => $obj->getOpcaoSelecionada(), 'comentario' => $obj->getComentario()]);
			Db::table(self::TABELA_RELACIONAL)->insertGetId([ 'resposta_Id' =>$id, 'formulario_respondido_id' => $idFormulario] );

			DB::statement('SET FOREIGN_KEY_CHECKS=0;');

			$obj->setId($id);

			return $obj;
		}
		catch (\Exception $e) {
			throw new ColecaoException("Erro ao cadastrar formulário de resposta.", $e->getCode(), $e);
		}
	}

	function adicionarTodas(&$objs){
		try {	
			DB::statement('SET FOREIGN_KEY_CHECKS=0;');

			foreach ($objs as $key => $obj) {
				$id = Db::table(self::TABELA)->insertGetId([ 'pergunta' => $obj->getPergunta(),
						'tarefa_id' => $obj->getTarefa()->getId()
					]
				);
				$obj->setId($id);
				$objs[$key] = $obj;
			}

			DB::statement('SET FOREIGN_KEY_CHECKS=1;');


			return $objs;
		}
		catch (\Exception $e) {
			throw new ColecaoException($e->getMessage(), $e->getCode(), $e);
		}
	}

	function remover($id) {
		try {	
			DB::statement('SET FOREIGN_KEY_CHECKS=0;');
			$removido = DB::table(self::TABELA)->where('id', $id)->delete();
			DB::statement('SET FOREIGN_KEY_CHECKS=1;');
			return $removido;
		}
		catch (\Exception $e)
		{
			throw new ColecaoException($e->getMessage(), $e->getCode(), $e);
		}
	}

	function atualizar(&$obj) {
		try {
			
			DB::statement('SET FOREIGN_KEY_CHECKS=0;');

			Db::table(self::TABELA)->where('id', $obj->getId())->update([ 'pergunta' => $obj->getPergunta(),
			'tarefa_id' => $obj->getTarefa()->getId()]);

			DB::statement('SET FOREIGN_KEY_CHECKS=1;');
			return $obj;
		}
		catch (\Exception $e)
		{
			throw new ColecaoException($e->getMessage(), $e->getCode(), $e);
		}
		
	}

	function comId($id){
		try {
            $resposta = $this->construirObjeto(DB::table(self::TABELA)->where('id', $id)->get()[0]);

			return $resposta;
		}
		catch (\Exception $e)
		{
			throw new ColecaoException($e->getMessage(), $e->getCode(), $e);
		}
	}

	/**
	 * @inheritDoc
	 */
	function todos($limite = 0, $pulo = 0) {
		try {	
			$perguntas = Db::table(self::TABELA)->offset($limite)->limit($pulo)->get();

			$perguntasObjects = [];
			foreach ($perguntas as $pergunta) {
				$perguntasObjects[] =  $this->construirObjeto($pergunta);
			}

			return $perguntasObjects;
		}
		catch (\Exception $e)
		{
			throw new ColecaoException($e->getMessage(), $e->getCode(), $e);
		}
	}

	function construirObjeto(array $row) {
		$resposta = new Resposta($row['id'], $row['opcaoSelecionada'], $row['comentario']);
		return $resposta;
	}	

    function contagem() {
		return Db::table(self::TABELA)->count();
	}
}

?>