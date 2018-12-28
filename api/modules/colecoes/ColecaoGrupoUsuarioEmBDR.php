<?php
use Illuminate\Database\Capsule\Manager as DB;
/**
 *	Coleção de Usuario em Banco de Dados Relacional.
 *
 *  @author		Rafael Vinicius Barros Ferreira
 *	@version	0.1
 */

class ColecaoGrupoUsuarioEmBDR implements ColecaoGrupoUsuario
{

	const TABELA = 'grupo_usuario';
	const TABELA_RELACIONAL = 'usuario_grupo_usuario';

	function __construct(){}

	function adicionar(&$obj) {
		if($this->validarGrupoDeUsuario($obj)) {
			try {	
				DB::statement('SET FOREIGN_KEY_CHECKS=0;');

				$id = DB::table(self::TABELA)->insertGetId([ 'nome' => $obj->getNome() ,'descricao' => $obj->getDescricao()]);
				
				$gruposUsuarios = [];

				foreach ($obj->getUsuarios() as $key => $usuario) {
					$gruposUsuarios[] = ['usuario_id' => $usuario->getId(), 'grupo_usuario_id' =>  $id];
				}

				DB::table(self::TABELA_RELACIONAL)->insert($gruposUsuarios);

				$obj->setId($id);
				DB::statement('SET FOREIGN_KEY_CHECKS=1;');
			}
			catch (\Exception $e)
			{
				throw new ColecaoException($e->getMessage(), $e->getCode(), $e);
			}
		}	
	}

	function remover($id) {
		if($this->validarDeleteGrupoDeUsuario($id)){

			try {	
				DB::statement('SET FOREIGN_KEY_CHECKS=0;');
				
				$removido = DB::table(self::TABELA)->where('id', $id)->delete();
				if($removido) $removido = DB::table(self::TABELA_RELACIONAL)->where('grupo_usuario_id', $id)->delete();

				DB::statement('SET FOREIGN_KEY_CHECKS=1;');
				return $removido;
			}
			catch (\Exception $e)
			{
				throw new ColecaoException($e->getMessage(), $e->getCode(), $e);
			}
		}
	}

	function atualizar(&$obj) {
		if($this->validarGrupoDeUsuario($obj)){
			try {

				DB::statement('SET FOREIGN_KEY_CHECKS=0;');

				DB::table(self::TABELA)->where('id', $obj->getId())->update(['nome' => $obj->getNome() ,'descricao' => $obj->getDescricao()]);

				if(count($obj->getUsuarios())){
					DB::table(self::TABELA_RELACIONAL)->where('grupo_usuario_id', $obj->getId())->delete();

					$gruposUsuarios = [];

					foreach($obj->getUsuarios() as $usuario){
					
						$gruposUsuarios[] = ['usuario_id' => $usuario->getId(), 'grupo_usuario_id' =>  $obj->getId()];
					}
					DB::table(self::TABELA_RELACIONAL)->insert($gruposUsuarios);
				}

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
			$usuario = $this->construirObjeto(DB::table(self::TABELA)->where('id', $id)->get()[0]);

			return $usuario;
		}
		catch (\Exception $e) {
			throw new ColecaoException($e->getMessage(), $e->getCode(), $e);
		}
	}

	function comUsuarioId($id = 0){
		try {	
			$grupoDeusuarios = DB::table(self::TABELA)->select(self::TABELA . '.*')->join(self::TABELA_RELACIONAL, self::TABELA_RELACIONAL. '.grupo_usuario_id', '=', self::TABELA . '.id')->where(self::TABELA_RELACIONAL. '.usuario_id', $id)->get();

            $grupoDeusuariosObjects = [];

			foreach($grupoDeusuarios as $grupo) {

				$grupoDeusuariosObjects[] =  $this->construirObjeto($grupo);
			}

			return $grupoDeusuariosObjects;
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
			$grupoDeusuarios = DB::table(self::TABELA)->offset($limite)->limit($pulo)->get();

            $grupoDeusuariosObjects = [];

			foreach($grupoDeusuarios as $grupo) {

				$grupoDeusuariosObjects[] =  $this->construirObjeto($grupo);
			}

			return $grupoDeusuariosObjects;
		}
		catch (\Exception $e)
		{
			throw new ColecaoException($e->getMessage(), $e->getCode(), $e);
		}
	}
	
	function todosComIds($ids = []) {
		try {	
			$usuarios = DB::table(self::TABELA)->whereIn('id', $ids)->get();
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
		$usuarios = Dice::instance()->create('ColecaoUsuario')->comGrupoId($row['id']);
		$grupoDeUsuario = new GrupoUsuario($row['id'], $row['nome'], $row['descricao'], $usuarios);
		$grupoDeUsuario->setAdministrador($row['administrador']);
		return $grupoDeUsuario;
	}	

    function contagem() {
		return DB::table(self::TABELA)->count();
	}

	private function validarGrupoDeUsuario(&$obj) {
		if(!is_string($obj->getNome())) {
			throw new ColecaoException('Valor inválido para o nome do grupo.');
		}

		$quantidade = DB::table(self::TABELA)->whereRaw('nome like "%' . $obj->getNome() . '%"')->where('id', '<>', $obj->getId())->count();

		if($quantidade > 0){
			throw new ColecaoException('Já exite um grupo cadastrado com esse nome.');
		}

		if(strlen($obj->getNome()) <= 2 && strlen($obj->getNome()) >= 100) throw new ColecaoException('O nome deve conter no mínimo 2 e no máximo 100 caracteres.');
		
		if($obj->getDescricao() != '' and strlen($obj->getDescricao()) > 255) throw new ColecaoException('A descrição deve conter no máximo 255 caracteres.');

		return true;
	}

	private function validarDeleteGrupoDeUsuario($id){
		$qtdReacionamento = DB::table(self::TABELA_RELACIONAL)->where('grupo_usuario_id', $id)->count();

		if($qtdReacionamento > 0){
			throw new ColecaoException('Esse grupo possue usuários relacionados a ele! Desfaça todos  os relacionamentos e tente novamente.');
		}

		return true;
	}
}

?>