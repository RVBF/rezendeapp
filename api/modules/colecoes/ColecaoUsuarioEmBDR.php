<?php
use Illuminate\Database\Capsule\Manager as Db;
/**
 *	Coleção de Usuario em Banco de Dados Relacional.
 *
 *  @author		Rafael Vinicius Barros Ferreira
 *	@version	0.1
 */

class ColecaoUsuarioEmBDR implements ColecaoUsuario
{

	const TABELA = 'usuario';

	function __construct(){}

	function adicionar(&$obj) {
		try {	
			$id = Db::table(self::TABELA)->insertGetId([ 'login' => $obj->getLogin(), 'senha' => $obj->getSenha()]);
			
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
            
            $removido = DB::table(self::TABELA)->where('id', $id)->where('checklist_id', $idChecklist)->delete();
            
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

			Db::table(self::TABELA)->where('id', $obj->getId())->update([ 'login' => $obj->getTitulo(), 'senha' => $obj->getSenha()]);

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
		$usuario = new Usuario($row['id'],$row['login'], $row['senha']);

		return $usuario;
	}	

    function contagem() {
		return Db::table(self::TABELA)->count();
	}

	function comLogin($login)
	{
		try {
			$usuario = $this->construirObjeto(DB::table(self::TABELA)->where('login', $login)->get()[0]);

			return $usuario;
		}
		catch (\Exception $e)
		{
			throw new ColecaoException($e->getMessage(), $e->getCode(), $e);
		}
	}

	function novaSenha($senhaAtual, $novaSenha, $confirmacaoSenha)
	{
		$this->validarTrocaDeSenha($senhaAtual, $novaSenha, $confirmacaoSenha);

		$hash = new HashSenha($novaSenha);

		$novaSenha = $hash->gerarHashDeSenhaComSaltEmMD5();

		try
		{
			$sql = 'UPDATE ' . self::TABELA . ' SET
			 	senha = :senha
			 	WHERE id = :id';

			$this->pdoW->execute($sql, [
				'senha' => $novaSenha,
				'id' => $this->getUsuario()->getId()
			]);
		}
		catch (\Exception $e)
		{
			throw new ColecaoException($e->getMessage(), $e->getCode(), $e);
		}
	}


}

?>