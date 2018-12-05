<?php
use Illuminate\Database\Capsule\Manager as Db;
/**
 *	Coleção de Formulario Respondido em Banco de Dados Relacional.
 *
 *  @author		Rafael Vinicius Barros Ferreira
 *	@version	0.1
 */

class ColecaoFormularioRespondidoEmBDR implements ColecaoFormularioRespondido
{

	const TABELA = 'formulario_respondido';
	const TABELA_RELACIONAL = 'fomulario_pergunta';
	
	function __construct(){}

	function adicionar(&$obj) {
		try {	
			DB::statement('SET FOREIGN_KEY_CHECKS=0;');

			$id = Db::table(self::TABELA)->insertGetId([ 'data_resposta' => $obj->getDataHora()->toDateTimeString(), 'respondedor_id' => $obj->getRespondedor()->getId()]);
			
			$perguntasFormulario = [];

			foreach($obj->getPerguntas() as $pergunta){
				
				$perguntasFormulario[] = ['pergunta_id' => $pergunta->getId(), 'formulario_respondido_id' =>  $id];
			}
			Db::table(self::TABELA_RELACIONAL)->insert($perguntasFormulario);

			DB::statement('SET FOREIGN_KEY_CHECKS=0;');

			$obj->setId($id);

			return $obj;
		}
		catch (\Exception $e)
		{
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

			Db::table(self::TABELA)->where('id', $obj->getId())->update([ 'data_resposta' => $obj->getData(),
                'respondedor_id' => $obj->gerRespondedor()->getId()
            ]);

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
			$formularioRespondido = $this->construirObjeto(DB::table(self::TABELA)->where('id', $id)->get()[0]);

			return $formularioRespondido;
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
			$formularioRespondidos = Db::table(self::TABELA)->offset($limite)->limit($pulo)->get();

			$formularioRespondidosObjects = [];
			foreach ($formularioRespondidos as $formularioRespondido) {
				$formularioRespondidosObjects[] =  $this->construirObjeto($formularioRespondido);
			}

			return $formularioRespondidosObjects;
		}
		catch (\Exception $e)
		{
			throw new ColecaoException($e->getMessage(), $e->getCode(), $e);
		}
    }
    
	function construirObjeto(array $row) {

		$respondedor = ($row['respondedor_id'] > 0) ? Dice::instance()->create('ColecaoUsuario')->comId($row['respondedor_id']) : null;
		$perguntas = Dice::instance()->create('ColecaoPergunta')->comFormularioId($row[id]);

		$formularioRespondido = new FormularioRespondido($row['id'], $row['data_resposta'], $respondedor, $perguntas);

		return $formularioRespondido;
	}	

    function contagem() {
		return Db::table(self::TABELA)->count();
	}
}

?>