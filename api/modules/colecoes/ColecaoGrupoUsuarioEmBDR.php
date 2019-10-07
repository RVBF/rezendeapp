<?php
use Illuminate\Database\Capsule\Manager as DB;
/**
 *	Coleção de Usuario em Banco de Dados Relacional.
 *
 *  @author		Rafael Vinicius Barros Ferreira
 *	@version	1.0
 */

class ColecaoGrupoUsuarioEmBDR implements ColecaoGrupoUsuario {
	const TABELA = 'grupo_usuario';
	const TABELA_RELACIONAL = 'usuario_grupo_usuario';

	function __construct(){}

	function adicionar(&$obj) {
		if($this->validarGrupoDeUsuario($obj)) {
			try {	

				$id = DB::table(self::TABELA)->insertGetId([ 
					'nome' => $obj->getNome() ,
					'descricao' => $obj->getDescricao(), 
					'eadmin' => $obj->getAdministrador()
				]);
				
				$gruposUsuarios = [];

				foreach ($obj->getUsuarios() as $key => $usuario) {
					$gruposUsuarios[] = ['usuario_id' => $usuario->getId(), 'grupo_usuario_id' =>  $id];
				}

				DB::table(self::TABELA_RELACIONAL)->insert($gruposUsuarios);

				$obj->setId($id);
			}
			catch (\Exception $e) {
				throw new ColecaoException($e->getMessage(), $e->getCode(), $e);
			}
		}	
	}

	function remover($id) {
		if($this->validarDeleteGrupoDeUsuario($id)){

			try {					
				$removido = DB::table(self::TABELA)->where('id', $id)->delete();
				if($removido) $removido = DB::table(self::TABELA_RELACIONAL)->where('grupo_usuario_id', $id)->delete();

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

				DB::table(self::TABELA)->where('id', $obj->getId())->update([
					'nome' => $obj->getNome(),
					'descricao' => $obj->getDescricao(),
					'eadmin' => $obj->getAdministrador()
				]);

				DB::table(self::TABELA_RELACIONAL)->where('grupo_usuario_id', $obj->getId())->delete();

				if(is_array($obj->getUsuarios()) ? count($obj->getUsuarios()): false){
					$gruposUsuarios = [];

					foreach($obj->getUsuarios() as $usuario){
					
						$gruposUsuarios[] = ['usuario_id' => $usuario->getId(), 'grupo_usuario_id' =>  $obj->getId()];
					}

					DB::table(self::TABELA_RELACIONAL)->insert($gruposUsuarios);
				}

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
			$usuario = $this->construirObjeto(DB::table(self::TABELA)->where('id', $id)->get());

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
	function todos($limite = 0, $pulo = 0, $search = '') {
		try {	
			$query = DB::table(self::TABELA)->select(self::TABELA . '.*');
			
			if($search != '') {
				$buscaCompleta = $search;
				$palavras = explode(' ', $buscaCompleta);

				$query->where(function($query)  use ($buscaCompleta){
					$query->whereRaw(self::TABELA . '.id like "%' . $buscaCompleta . '%"');
					$query->orWhereRaw(self::TABELA . '.nome like "%' . $buscaCompleta . '%"');
					$query->orWhereRaw(self::TABELA . '.descricao like "%' . $buscaCompleta . '%"');
				});

				
				if($query->count() == 0){
					$query->where(function($query) use ($palavras){
						foreach ($palavras as $key => $palavra) {
							if($palavra != " "){
								$query->whereRaw(self::TABELA . '.id like "%' . $palavra . '%"');
								$query->orWhereRaw(self::TABELA . '.nome like "%' . $palavra . '%"');
								$query->orWhereRaw(self::TABELA . '.descricao like "%' . $palavra . '%"');
							}
						}
						
					});
				}

				if($query->count() == 0){
					foreach ($buscaCompleta as $key => $caracterer) {
						$query->where(function($query) use ($caracterer){
							$query->whereRaw(self::TABELA . '.id like "%' . $caracterer . '%"');
							$query->orWhereRaw(self::TABELA . '.nome like "%' . $caracterer . '%"');
							$query->orWhereRaw(self::TABELA . '.descricao like "%' . $caracterer . '%"');
						});
					}
				}

				$query->groupBy(self::TABELA.'.id');
			}
			
			$grupoDeusuarios = $query->offset($limite)->limit($pulo)->get();

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
	
	function todosComIdsDeUsuario($ids = []) {
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
		$grupoDeUsuario->setAdministrador($row['eadmin']);
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