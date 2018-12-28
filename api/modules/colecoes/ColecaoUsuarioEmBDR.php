<?php
use Illuminate\Database\Capsule\Manager as DB;
/**
 *	Coleção de Usuario em Banco de Dados Relacional.
 *
 *  @author		Rafael Vinicius Barros Ferreira
 *	@version	0.1
 */

class ColecaoUsuarioEmBDR implements ColecaoUsuario
{

	const TABELA = 'usuario';
	const TABELA_RELACIONAL = 'usuario_grupo_usuario';


	function __construct(){}

	function adicionar(&$obj) {
		if($this->validarUsuario($obj)){
			try {	

				DB::statement('SET FOREIGN_KEY_CHECKS=0;');
	
				$id = DB::table(self::TABELA)->insertGetId([ 
					'login' => $obj->getLogin(),
					'senha' => $obj->getSenha()
				]);
				
				DB::statement('SET FOREIGN_KEY_CHECKS=1;');
	
				$obj->setId($id);
	
				return $obj;
			}
			catch (\Exception $e)
			{
				throw new ColecaoException("Erro ao adicionar usuário.", $e->getCode(), $e);
			}
		}
	}

	function remover($id) {

		try {
			if($this->validarRemocaoUsuario($id)){

				DB::statement('SET FOREIGN_KEY_CHECKS=0;');
				
				$removido = DB::table(self::TABELA)->where('id', $id)->delete();

				
				$removido = DB::table(ColecaoColaboradorEmBDR::TABELA_RELACIONAL)->select(ColecaoColaboradorEmBDR::TABELA_RELACIONAL . '.*')
					->join(ColecaoColaboradorEmBDR::TABELA, ColecaoColaboradorEmBDR::TABELA . '.id', '=', ColecaoColaboradorEmBDR::TABELA_RELACIONAL . '.colaborador_Id')
					->where(ColecaoColaboradorEmBDR::TABELA . '.usuario_id', $id)
					->delete();

				$removido = DB::table(ColecaoColaboradorEmBDR::TABELA)->where('usuario_id', $id)->delete();

				DB::statement('SET FOREIGN_KEY_CHECKS=1;');
				return $removido;
			}
			else return false;
		}
		catch (\Exception $e){
			throw new ColecaoException($e->getMessage(), $e->getCode(), $e);
		}
	}

	function atualizar(&$obj) {
		if($this->validarUsuario($obj)){
			try {
				
				DB::statement('SET FOREIGN_KEY_CHECKS=0;');

				DB::table(self::TABELA)->where('id', $obj->getId())->update([
					'login' => $obj->getLogin()
				]);

				DB::statement('SET FOREIGN_KEY_CHECKS=1;');
				return $obj;
			}
			catch (\Exception $e)
			{
				throw new ColecaoException($e->getMessage(), $e->getCode(), $e);
			}
		}
	}

	function comId($id){
		try {	
			$usuario = $this->construirObjeto(DB::table(self::TABELA)->select('id', 'login', 'administrador')->where('id', $id)->get()[0]);

			return $usuario;
		}
		catch (\Exception $e)
		{
			throw new ColecaoException($e->getMessage(), $e->getCode(), $e);
		}
	}

	function todos($limite = 0, $pulo = 0) {
		try {	
			$usuarios = DB::table(self::TABELA)->select('id', 'login', 'administrador')->offset($limite)->limit($pulo)->get();

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
	
	function todosComIds($ids = []) {
		try {	
			$usuarios = DB::table(self::TABELA)->select('id', 'login', 'administrador')->whereIn('id', $ids)->get();
			$usuariosObjects = [];
			foreach ($usuarios as $usuarios) {
				$usuariosObjects[] =  $this->construirObjeto($usuarios);
			}

			return $usuariosObjects;
		}
		catch (\Exception $e)
		{
			throw new ColecaoException($e->getMessage(), $e->getCode(), $e);
		}
	}
	
	function construirObjeto(array $row) {
		$usuario = new Usuario($row['id'], $row['login'], isset($row['senha']) ? $row['senha'] : '');
		$usuario->setAdministrador($row['administrador']);

		return $usuario;
	}
	
	function comGrupoId($id){
		try {

			$grupos = DB::table(self::TABELA)->select(self::TABELA . '.*')
				->join(self::TABELA_RELACIONAL, self::TABELA_RELACIONAL . '.usuario_id', '=', self::TABELA . '.id')
				->where(self::TABELA_RELACIONAL . '.grupo_usuario_id', $id)->get();
				
			$gruposObjects = [];

			foreach ($grupos as $grupo) {
				$gruposObjects[] = $this->construirObjeto($grupo);			
			}

			return $gruposObjects;
		}
		catch (\Exception $e)
		{
			throw new ColecaoException($e->getMessage(), $e->getCode(), $e);
		}
	}

    function contagem() {
		return DB::table(self::TABELA)->count();
	}

	function comLogin($login)
	{
		try {
			
			if(DB::table(self::TABELA)->where('login', $login)->count() > 0) $usuario = $this->construirObjeto(DB::table(self::TABELA)->where('login', $login)->get()[0]);
			else $usuario = null;
			return $usuario;
		}
		catch (\Exception $e)
		{
			throw new ColecaoException($e->getMessage(), $e->getCode(), $e);
		}
	}

	function novaSenha($senhaAtual, $novaSenha, $confirmacaoSenha) {
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
	
	private function validarUsuario($obj) {
		$this->validarLogin($obj->getLogin());

		if($obj->getSenha() != '' or ($obj->getId() == 0 and $obj->getSenha() != '') ) {
			$this->validarSenha($obj->getSenha());
		}

		$quantidade = DB::table(self::TABELA)->whereRaw(self::TABELA . '.login like "%' . $obj->getLogin() . '%"')->where('id', '<>', $obj->getId())->count();

		if($quantidade > 0)
		{
			throw new ColecaoException( 'O login  ' . $obj->getLogin() . ' já está em uso por outro usuário no sistema.' );
		}

		return true;
	}

	/**
	*  Valida o login do usuário, lançando uma exceção caso haja algo inválido.
	*  @throws ColecaoException
	*/
	private function validarLogin($login) {
		if(!$this->validarFormatoLogin($login)) {
			throw new Exception("Formato de Login inválido.");
		}

		if(!is_string($login)) {
			throw new ColecaoException( 'Valor inválido para login, o campo login é um campo do tipo texto.' );
		}

		$tamLogin = mb_strlen($login);

		if($tamLogin <= Usuario::TAMANHO_MINIMO_LOGIN) {
			throw new ColecaoException('O login deve conter no minímo ' . Usuario::TAMANHO_MINIMO_LOGIN . ' caracteres.');
		}

		if ($tamLogin >= Usuario::TAMANHO_MAXIMO_LOGIN) {
			throw new ColecaoException('O login deve conter no máximo ' . Usuario::TAMANHO_MAXIMO_LOGIN . ' caracteres.');
		}
	}

	/**
	*  Valida o senha do usuário, lançando uma exceção caso haja algo inválido.
	*  @throws ColecaoException
	*/
	private function validarSenha($senha) {
		if(!is_string($senha))
		{
			throw new ColecaoException( 'Valor inválido para senha.' );
		}

		$tamSenha = mb_strlen($senha);

		if($tamSenha <= Usuario::TAMANHO_MINIMO_SENHA)
		{
			throw new ColecaoException('O senha deve conter no minímo ' . Usuario::TAMANHO_MINIMO_SENHA . ' caracteres.');
		}
		if ($tamSenha >= Usuario::TAMANHO_MAXIMO_SENHA)
		{
			throw new ColecaoException('O senha deve conter no máximo ' . Usuario::TAMANHO_MAXIMO_SENHA . ' caracteres.');
		}
	}

	/**
	*  Valida formato do login do usuário, lançando uma exceção caso haja algo inválido.
	*  @throws ColecaoException
	*/
	private function validarFormatoLogin($login) {
		$formato = '/[a-zA-Z0-9\. _-]+./';

		if (preg_match($formato, $login))
		{
			return true;	
		}
		else
		{
			return false;	
		}	
	}

	private function validarTrocaDeSenha($senhaAtual, $novaSenha, $confirmacaoSenha) {
		if(!empty($senhaAtual)) {
			if(!is_string($senhaAtual))
			{
				throw new ColecaoException( 'Valor inválido para o campo senha atual.' );
			}

			$tamSenha = mb_strlen($senhaAtual);

			if($tamSenha <= Usuario::TAMANHO_MINIMO_SENHA)
			{
				throw new ColecaoException('O campo senha atual deve conter no minímo ' . Usuario::TAMANHO_MINIMO_SENHA . ' caracteres.');
			}
			if ($tamSenha >= Usuario::TAMANHO_MAXIMO_SENHA)
			{
				throw new ColecaoException('O campo senha atual conter no máximo ' . Usuario::TAMANHO_MAXIMO_SENHA . ' caracteres.');
			}
		}

		if(!empty($novaSenha))
		{
			if(!is_string($novaSenha))
			{
				throw new ColecaoException( 'Valor inválido para o campo nova senha.' );
			}

			$tamSenha = mb_strlen($novaSenha);

			if($tamSenha <= Usuario::TAMANHO_MINIMO_SENHA)
			{
				throw new ColecaoException('O campo nova senha deve conter no minímo ' . Usuario::TAMANHO_MINIMO_SENHA . ' caracteres.');
			}
			if ($tamSenha >= Usuario::TAMANHO_MAXIMO_SENHA)
			{
				throw new ColecaoException('O campo nova senha conter no máximo ' . Usuario::TAMANHO_MAXIMO_SENHA . ' caracteres.');
			}
		}

		if(!empty($confirmacaoSenha))
		{
			if(!is_string($confirmacaoSenha))
			{
				throw new ColecaoException( 'Valor inválido para o campo confirmação senha.' );
			}

			$tamSenha = mb_strlen($confirmacaoSenha);

			if($tamSenha <= Usuario::TAMANHO_MINIMO_SENHA)
			{
				throw new ColecaoException('O campo confirmação senha deve conter no minímo ' . Usuario::TAMANHO_MINIMO_SENHA . ' caracteres.');
			}
			if ($tamSenha >= Usuario::TAMANHO_MAXIMO_SENHA)
			{
				throw new ColecaoException('O campo confirmação senha conter no máximo ' . Usuario::TAMANHO_MAXIMO_SENHA . ' caracteres.');
			}
		}

		if(!($novaSenha === $confirmacaoSenha))
		{
			throw new Exception("O campo nova senha e confirmação de sneha não correspondem, corrija os dados e tente novamente");
		}

		$hash = new HashSenha($senhaAtual);

		$senhaAtual = $hash->gerarHashDeSenhaComSaltEmMD5();

		$sql = 'select senha from '. self::TABELA .  ' where id = :id';

		$resultado = $this->pdoW->query($sql, ['id' => $this->getUsuario()->getId()]);

		if($resultado[0]['senha'] != $senhaAtual)
		{
			throw new Exception("Senha atuali inválidá");
		}

		if($senhaAtual == $novaSenha)
		{
			throw new Exception("A nova senha deve ser difente da senha atual.");
		}
	}

	private function validarRemocaoUsuario($id){
		$quantidade = DB::table(ColecaoTarefaEmBDR::TABELA)->where('questionador_id', $id)->count();

		if($quantidade > 0)throw new ColecaoException('Não foi possível excluir o usuário por que ele possui tarefas relacionadas a ele. Exclua todas as tarefas relacionadas e tente novamente.');

		$quantidade = DB::table(ColecaoFormularioRespondidoEmBDR::TABELA)->where('respondedor_Id', $id)->count();

		if($quantidade > 0)throw new ColecaoException('Não foi possível excluir o usuário por que ele possui formulários relacionadoss a ele. Exclua todas os formulários relacionados e tente novamente.');

		$quantidade = DB::table(ColecaoLojaEmBDR::TABELA)->select(ColecaoLojaEmBDR::TABELA . '.*')
			->join(ColecaoLojaEmBDR::TABELA_RELACIONAL , ColecaoLojaEmBDR::TABELA_RELACIONAL . '.loja_id', '=', ColecaoLojaEmBDR::TABELA . '.id')
			->join(ColecaoColaboradorEmBDR::TABELA, ColecaoColaboradorEmBDR::TABELA . '.id', '=', ColecaoLojaEmBDR::TABELA_RELACIONAL . '.colaborador_id' )
			->where(ColecaoColaboradorEmBDR::TABELA . '.usuario_id', $id)->count();


		if($quantidade > 0)throw new ColecaoException('Não foi possível excluir o usuário por que ele possui lojas relacionadoss a ele. Retire todas as relações e tente novamente.');

		return true;
	}
}

?>