<?php
use Illuminate\Database\Capsule\Manager as DB;
/**
 *	Coleção de Anexo em Banco de Dados Relacional.
 *
 *  @author		Rafael Vinicius Barros Ferreira
 *	@version	0.1
 */

class ColecaoAnexoEmBDR implements ColecaoAnexo
{

	const TABELA = 'anexos';

	function __construct(){}

	function adicionar(&$obj) {
		try {	
			DB::statement('SET FOREIGN_KEY_CHECKS=0;');

			$id = DB::table(self::TABELA)->insertGetId([ 'caminho' => $obj->getPatch(), 'tipo' => $obj->getTipo(), 'resposta_id' => $obj->getResposta()->getId()]);
            
			DB::statement('SET FOREIGN_KEY_CHECKS=1;');

			$obj->setId($id);

			return $obj;
		}
		catch (\Exception $e)
		{
			throw new ColecaoException($e->getMessage(), $e->getCode(), $e);
		}
	}

	function adicionarTodas(&$objs){
		try {	
			DB::statement('SET FOREIGN_KEY_CHECKS=0;');

			foreach ($objs as $key => $obj) {
				$id = DB::table(self::TABELA)->insertGetId([ 'pergunta' => $obj->getPergunta(),
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

			DB::table(self::TABELA)->where('id', $obj->getId())->update([ 'pergunta' => $obj->getPergunta(),
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
			$anexo = $this->construirObjeto(DB::table(self::TABELA)->where('id', $id)->get()[0]);

			return $pergunta;
		}
		catch (\Exception $e)
		{
			throw new ColecaoException($e->getMessage(), $e->getCode(), $e);
		}
	}
	
	public function comRespostaId($id){
		try {	

			$anexos = DB::table(self::TABELA)->where('resposta_id', $id)->get();
			$anexosObjects = [];
			foreach ($anexos as $anexo) {
				$anexosObjects[] =  $this->construirObjeto($anexo);
			}

			return $anexosObjects;
		}
		catch (\Exception $e) {
			throw new ColecaoException($e->getMessage(), $e->getCode(), $e);
		}
	}
	/**
	 * @inheritDoc
	 */
	function todos($limite = 0, $pulo = 0) {
		try {	
			$anexos = DB::table(self::TABELA)->offset($limite)->limit($pulo)->get();

			$anexosObjects = [];
			foreach ($anexos as $pergunta) {
				$anexosObjects[] =  $this->construirObjeto($pergunta);
			}

			return $anexosObjects;
		}
		catch (\Exception $e)
		{
			throw new ColecaoException($e->getMessage(), $e->getCode(), $e);
		}
	}

	function construirObjeto(array $row) {
		$arquivo = ServicoArquivo::instance();

		$base64 =  'data:'. $row['tipo'] . ';base64,'. $arquivo->imagemParaBase64($row['caminho']);
		$anexo = new Anexo($row['id'], $row['caminho'], $row['tipo']);
		$anexo->setArquivoBase64($base64);
		return $anexo;
	}	

    function contagem() {
		return DB::table(self::TABELA)->count();
	}
}

?>