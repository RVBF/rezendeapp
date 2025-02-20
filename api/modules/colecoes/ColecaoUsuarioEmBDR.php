<?php
use Illuminate\Database\Capsule\Manager as DB;
use Carbon\Carbon;

/**
 *	Coleção de Usuario em Banco de Dados Relacional.
 *
 *  @author		Rafael Vinicius Barros Ferreira
 *	@version	1.0
 */

class ColecaoUsuarioEmBDR  implements ColecaoUsuario {
	const TABELA = 'usuario';
	const VW_TABELA_COLABORADOR_USUARIO = 'vw_colaborador_usuario';
	const TABELA_RELACIONAL = 'usuario_grupo_usuario';

	function __construct(){}

	function adicionar(&$obj) {
		if($this->validarUsuario($obj)) {
			try {
				$hash = HashSenha::instance();

				$id = DB::table(self::TABELA)->insertGetId([
					'login' => $obj->getLogin(),
					'senha' => $hash->gerarHashDeSenhaComSaltEmMD5($obj->getSenha()),
					'administrador' => false
				]);

				$obj->setId($id);

				return $obj;
			}
			catch (\Exception $e) {
				throw new ColecaoException("Erro ao adicionar usuário.", $e->getCode(), $e);
			}
		}
	}

	function remover($id) {
		try {
			if($this->validarRemocaoUsuario($id)) DB::table(self::TABELA)->where('deleted_at', NULL)->where('id', $id)->update(['deleted_at' => Carbon::now()->toDateTimeString()]);
		}
		catch (\Exception $e)
		{
			throw new ColecaoException("Erro ao remover usuário com id.", $e->getCode(), $e);
		}
	}

	function atualizar(&$obj) {
		if($this->validarUsuario($obj)){
			try {
				DB::statement('SET FOREIGN_KEY_CHECKS=0;');
				$filds = ['login' => $obj->getLogin()];
				if(strlen($obj->getSenha()) > 0) {
					$hash = HashSenha::instance();
					$filds['senha'] = $hash->gerarHashDeSenhaComSaltEmMD5($obj->getSenha());
				}

				DB::table(self::TABELA)->where('deleted_at', NULL)->where('id', $obj->getId())->update($filds);

				DB::statement('SET FOREIGN_KEY_CHECKS=1;');
				return $obj;
			}
			catch (\Exception $e)
			{
				throw new ColecaoException("Ero ao atualizar usuário!", $e->getCode(), $e);
			}
		}
	}

	function comId($id){
		try {
			return (DB::table(self::TABELA)->where('id', $id)->count()) ? $this->construirObjeto(DB::table(self::TABELA)->where('id', $id)->first()) : [];
		}
		catch (\Exception $e)
		{
			throw new ColecaoException("Erro ao buscar usuário!", $e->getCode(), $e);
		}
	}

	function todos($limite = 0, $pulo = 0, $search = '') {
		try {
			$query = DB::table(self::TABELA)->where('deleted_at', NULL)->select(self::TABELA . '.id', self::TABELA . '.login', self::TABELA . '.administrador');

			if($search != '') {
				$buscaCompleta = $search;
				$palavras = explode(' ', $buscaCompleta);

				$query->leftJoin(ColecaoColaboradorEmBDR::TABELA, ColecaoColaboradorEmBDR::TABELA . '.usuario_id', '=', self::TABELA . '.id');

				$query->where(function($query) use ($buscaCompleta){
					$query->whereRaw(self::TABELA . '.id like "%' . $buscaCompleta . '%"');
					$query->orWhereRaw(self::TABELA . '.login like "%' . $buscaCompleta . '%"');
					$query->orWhereRaw(ColecaoColaboradorEmBDR::TABELA . '.nome like "%' . $buscaCompleta . '%"');
					$query->orWhereRaw(ColecaoColaboradorEmBDR::TABELA . '.sobrenome like "%' . $buscaCompleta . '%"');
					$query->orWhereRaw(ColecaoColaboradorEmBDR::TABELA . '.email like "%' . $buscaCompleta . '%"');

				});


				if($query->count() == 0){
					$query->where(function($query) use ($palavras){
						foreach ($palavras as $key => $palavra) {
							if($palavra != " "){
								$query->whereRaw(self::TABELA . '.id like "%' . $palavra . '%"');
								$query->orWhereRaw(self::TABELA . '.login like "%' . $palavra . '%"');
								$query->orWhereRaw(ColecaoColaboradorEmBDR::TABELA . '.nome like "%' . $palavra . '%"');
								$query->orWhereRaw(ColecaoColaboradorEmBDR::TABELA . '.sobrenome like "%' . $palavra . '%"');
								$query->orWhereRaw(ColecaoColaboradorEmBDR::TABELA . '.email like "%' . $palavra . '%"');
							}
						}

					});
				}
				$query->groupBy(self::TABELA.'.id');
			}

			$usuarios = $query->offset($limite)->limit($pulo)->get();

            $usuariosObjects = [];

			foreach($usuarios as $usuario) {

				$usuariosObjects[] =  $this->construirObjeto($usuario);
			}

			return $usuariosObjects;
		}
		catch (\Exception $e)
		{
			throw new ColecaoException("Erro ao buscar usuários!", $e->getCode(), $e);
		}
	}

	function todosOpcoes() {
		try {
			$query = DB::table(self::TABELA)
							->selectRaw('distinct ' . self::TABELA . '.id, ' . Colaborador::TABELA . '.nome, ' . Colaborador::TABELA . '.sobrenome, COUNT('.self::TABELA.'.id) as qtd')
							->join(Colaborador::TABELA, Colaborador::TABELA . '.usuario_id', '=', self::TABELA . '.id')
							->where(self::TABELA . '.deleted_at', NULL)->groupBy(self::TABELA . '.id', Colaborador::TABELA . '.nome',  Colaborador::TABELA . '.sobrenome');

			$usuarios = $query->get();

			return $usuarios;
		}
		catch (\Exception $e) {
			throw new ColecaoException($e->getMessage(), $e->getCode(), $e);
		}
	}

	function todosComIds($ids = []) {
		try {
			$usuarios = DB::table(self::TABELA)->where('deleted_at', NULL)->select('id', 'login', 'administrador')->whereIn('id', $ids)->get();
			$usuariosObjects = [];
			foreach ($usuarios as $usuarios) {
				$usuariosObjects[] =  $this->construirObjeto($usuarios);
			}

			return $usuariosObjects;
		}
		catch (\Exception $e)
		{
			throw new ColecaoException("Erro ao buscar usuários com referências!", $e->getCode(), $e);
		}
	}

	function construirObjeto(array $row) {
		$usuario = new Usuario($row['id'], $row['login'], isset($row['senha']) ? $row['senha'] : '');
		$usuario->setAdministrador($row['administrador']);

		return $usuario->toArray();
	}

	function comGrupoId($id){
		try {

			$grupos = DB::table(self::TABELA)->where('deleted_at', NULL)->select(self::TABELA . '.*')
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
			throw new ColecaoException("Erro ao buscar usuários com as referências de grupo", $e->getCode(), $e);
		}
	}

    function contagem() {
		return DB::table(self::TABELA)->count();
	}

	function comLogin($login)
	{
		try {

			if(DB::table(self::TABELA)->where('deleted_at', NULL)->where('login', $login)->count() > 0) $usuario = $this->construirObjeto(DB::table(self::TABELA)->where('login', $login)->get()[0]);
			else $usuario = null;
			return $usuario;
		}
		catch (\Exception $e)
		{
			throw new ColecaoException($e->getMessage(), $e->getCode(), $e);
		}
	}

	function novaSenha($id, $senhaAtual, $novaSenha, $confirmacaoSenha) {
		try {
			if($this->validarTrocaDeSenha($id, $senhaAtual, $novaSenha, $confirmacaoSenha)){
				$hash = HashSenha::instance();
				$novaSenha = $hash->gerarHashDeSenhaComSaltEmMD5($novaSenha);

				DB::table(self::TABELA)->where('id', $id)->where('deleted_at', NULL)->update(['senha' => $novaSenha]);
			}
		}
		catch (\Exception $e) {
			throw new ColecaoException("Erro ao alterar a senha de  usuário!", $e->getCode(), $e);

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

		$tamLogin = strlen($login);
		if($tamLogin < Usuario::TAMANHO_MINIMO_LOGIN or $tamLogin > Usuario::TAMANHO_MAXIMO_LOGIN) {
			throw new ColecaoException('O login deve conter no minímo ' . Usuario::TAMANHO_MINIMO_LOGIN . ' e no máximo ' . Usuario::TAMANHO_MAXIMO_LOGIN . ' caracteres.');
		}
	}

	/**
	*  Valida a senha do usuário, lançando uma exceção caso haja algo inválido.
	*  @throws ColecaoException
	*/
	private function validarSenha($senha) {
		if(!is_string($senha)) {
			throw new ColecaoException( 'Valor inválido para senha.' );
		}

		$tamSenha = strlen($senha);

		if($tamSenha < Usuario::TAMANHO_MINIMO_SENHA or $tamSenha > Usuario::TAMANHO_MAXIMO_SENHA) {
			throw new ColecaoException('A senha deve conter no minímo ' . Usuario::TAMANHO_MINIMO_SENHA . ' e no máximo ' . Usuario::TAMANHO_MAXIMO_SENHA . ' caracteres.');
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

	private function validarTrocaDeSenha($id, $senhaAtual, $novaSenha, $confirmacaoSenha) {
	
		//Validações correspondentes ao tamanho e ao texto contido  do campo senha atual
		if(!is_string($senhaAtual)) throw new ColecaoException( 'Valor inválido para o campo senha atual.' );
		if(strlen($senhaAtual) < Usuario::TAMANHO_MINIMO_SENHA) throw new ColecaoException('O campo senha atual deve conter no minímo ' . Usuario::TAMANHO_MINIMO_SENHA . ' caracteres.');
		if (strlen($senhaAtual) > Usuario::TAMANHO_MAXIMO_SENHA) throw new ColecaoException('O campo senha atual conter no máximo ' . Usuario::TAMANHO_MAXIMO_SENHA . ' caracteres.');
		//Fim das validações correspondentes ao tamanho e ao texto do campo senha atual

		//Validações correspondentes ao tamanho e ao texto contido  do campo nova senha
		if(!is_string($novaSenha)) throw new ColecaoException( 'Valor inválido para o campo nova senha.' );
		if(strlen($novaSenha) < Usuario::TAMANHO_MINIMO_SENHA) throw new ColecaoException('O campo nova senha deve conter no minímo ' . Usuario::TAMANHO_MINIMO_SENHA . ' caracteres.');
		if (strlen($novaSenha) > Usuario::TAMANHO_MAXIMO_SENHA) throw new ColecaoException('O campo nova senha conter no máximo ' . Usuario::TAMANHO_MAXIMO_SENHA . ' caracteres.');
		//Fim das validações correspondentes ao tamanho e ao texto do campo nova senha
		
		//Validações correspondentes ao tamanho e ao texto contido do campo confirmação de senha
		if(!is_string($confirmacaoSenha)) throw new ColecaoException( 'Valor inválido para o campo confirmação senha.' );
		if(strlen($confirmacaoSenha) < Usuario::TAMANHO_MINIMO_SENHA) throw new ColecaoException('O campo confirmação senha deve conter no minímo ' . Usuario::TAMANHO_MINIMO_SENHA . ' caracteres.');
		if (strlen($confirmacaoSenha) > Usuario::TAMANHO_MAXIMO_SENHA) throw new ColecaoException('O campo confirmação senha conter no máximo ' . Usuario::TAMANHO_MAXIMO_SENHA . ' caracteres.');
		//Fim das validações correspondentes ao tamanho e ao texto do campo confirmação de senha
		
		$hash = HashSenha::instance();
		$senhaAtual = $hash->gerarHashDeSenhaComSaltEmMD5($senhaAtual);
		$novaSenha = $hash->gerarHashDeSenhaComSaltEmMD5($novaSenha);
		$confirmacaoSenha = $hash->gerarHashDeSenhaComSaltEmMD5($confirmacaoSenha);

		$senhaAtualUsuario = DB::table(self::TABELA)->select(self::TABELA.'.senha')->where('id', $id)->first();

		if($novaSenha != $confirmacaoSenha)	throw new Exception("O campo nova senha e confirmação de senha não correspondem, corrija os dados e tente novamente!");

		if($senhaAtualUsuario['senha'] != $senhaAtual)throw new Exception("A senha anterior inválida!");

		if($senhaAtual == $novaSenha)	throw new Exception("A nova senha deve ser difente da senha atual.");		

		return true;
	}

	private function validarRemocaoUsuario($id){
		$quantidadeUsuario = DB::table(self::TABELA)->where('deleted_at', NULL)->where('id', $id)->count();

		if($quantidadeUsuario == 0) throw new ColecaoException('O usuário selecionado para delete não foi encontrado!');
		if(DB::table(self::TABELA)->where('deleted_at', NULL)->count() == 1) throw new Exception("Não é possível excluir o usuário quando há somente 1 usuário cadastrado, porque é necesssário ao menos 1 usuário cadastrado para que possa ter relação com outras depências do sistema.");

		return true;
	}
}

?>