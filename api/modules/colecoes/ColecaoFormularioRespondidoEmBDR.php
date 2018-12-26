<?php
use Illuminate\Database\Capsule\Manager as DB;
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

			$id = DB::table(self::TABELA)->insertGetId([ 'data_resposta' => $obj->getDataHora()->toDateTimeString(), 'respondedor_id' => $obj->getRespondedor()->getId()]);
			
			$perguntasFormulario = [];

			foreach($obj->getPerguntas() as $pergunta){
				
				$perguntasFormulario[] = ['pergunta_id' => $pergunta->getId(), 'formulario_respondido_id' =>  $id];
			}
			DB::table(self::TABELA_RELACIONAL)->insert($perguntasFormulario);

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

			DB::table(self::TABELA)->where('id', $obj->getId())->update([ 'data_resposta' => $obj->getData(),
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


	function comPerguntaId($id){
		try {
			$formularioRespondido = DB::table(self::TABELA)->join(self::TABELA_RELACIONAL, self::TABELA_RELACIONAL . '.formulario_respondido_id', '=', self::TABELA . '.id')->where(self::TABELA_RELACIONAL . '.pergunta_id', $id)->get();
			$formularioRespondidoObj = null;

			if(count($formularioRespondido) >  0 ) {
				 $formularioRespondidoObj = $this->construirObjeto($formularioRespondido[0]);

			}
			return $formularioRespondidoObj;
		}
		catch (\Exception $e)
		{
			throw new ColecaoException("Erro ao buscar formulário respondido com a referência de pergunta.", $e->getCode(), $e);
		}
	}


	/**
	 * @inheritDoc
	 */
	function todos($limite = 0, $pulo = 0) {
		try {	
			$formularioRespondidos = DB::table(self::TABELA)->offset($limite)->limit($pulo)->get();

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
		$respondedor = ($row['respondedor_id'] > 0) ? Dice::instance()->create('ColecaoColaborador')->comUsuarioId($row['respondedor_id']) : null;

		$formularioRespondido = new FormularioRespondido($row['id'], $row['data_resposta'], $respondedor);

		return $formularioRespondido;
	}	

    function contagem() {
		return DB::table(self::TABELA)->count();
	}
}

?>