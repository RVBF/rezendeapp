<?php
use Illuminate\Database\Capsule\Manager as Db;
/**
 *	Coleção de Colaborador em Banco de Dados Relacional.
 *
 *  @author		Rafael Vinicius Barros Ferreira
 *	@version	0.1
 */

class ColecaoColaboradorEmBDR implements ColecaoColaborador
{

	const TABELA = 'colaborador';
    const TABELA_RELACIONAL = 'atuacao';
    
	function __construct(){}

	function adicionar(&$obj) {
		try {	

			DB::statement('SET FOREIGN_KEY_CHECKS=0;');

			$id = Db::table(self::TABELA)->insertGetId([ 
				'nome' => $obj->getNome() ,
				'sobrenome' => $obj->getLogin(),
				'email' => $obj->getSenha(),
				'usuario_id' => $obj->getUsuario()->getId()
            ]);
            
            $atuacoesLojas = [];

			foreach($obj->getLojas() as $loja){
				
				$atuacoesLojas[] = ['loja_id' => $loja->getId(), 'colaborador_id' =>  $id];
            }
            
			Db::table(self::TABELA_RELACIONAL)->insert($atuacoesLojas);
			
			DB::statement('SET FOREIGN_KEY_CHECKS=1;');

			$obj->setId($id);

			return $obj;
		}
		catch (\Exception $e)
		{
			throw new ColecaoException("Erro ao adicionar usuário.", $e->getCode(), $e);
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

			Db::table(self::TABELA)->where('id', $obj->getId())->update([ 
				'nome' => $obj->getNome() ,
				'sobrenome' => $obj->getLogin(),
				'email' => $obj->getSenha(),
				'usuario_id' => $obj->getUsuario()->getId()
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
			$usuario = $this->construirObjeto(DB::table(self::TABELA)->where('id', $id)->get()[0]);

			return $usuario;
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
			$usuarios = Db::table(self::TABELA)->offset($limite)->limit($pulo)->get();

            $usuariosObjects = [];

			foreach($usuarios as $usuario) {

				$usuariosObjects[] =  $this->construirObjeto($usuario);
			}

			return $usuariosObjects;
		}
		catch (\Exception $e)
		{
			throw new ColecaoException($e->getMessage(), $e->getCode(), $e);
		}
	}
	
	function todosComId($ids = []) {
		try {	
			$usuarios = Db::table(self::TABELA)->whereIn('id', $ids)->get();
			$usuariosObjects = [];

			foreach ($usuarios as $usuario) {
				$usuariosObjects[] =  $this->construirObjeto($usuario);
			}

			return $usuariosObjects;
		}
		catch (\Exception $e)
		{
			throw new ColecaoException($e->getMessage(), $e->getCode(), $e);
		}
	}

	function construirObjeto(array $row) {
		$usuario = ($row['usuario_id'] > 0) ? Dice::instance()->create('ColecaoUsuario')->comId($row['usuario_id']) : null;
        $lojas = Dice::instance()->create('ColecaoLoja')->comColaboradorId($row['id']);

		$colaborador = new Colaborador($row['id'], $row['nome'], $row['sobrenome'], $row['email'], $usuario, (count($lojas) > 0) ? $lojas : []);

		return $usuario;
	}	

    function contagem() {
		return Db::table(self::TABELA)->count();
	}
}

?>