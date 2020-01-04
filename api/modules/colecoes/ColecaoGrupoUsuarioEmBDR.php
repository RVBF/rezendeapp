<?php
use Illuminate\Database\Capsule\Manager as DB;
use Carbon\Carbon;

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
				]);
				$obj->setId($id);

				$gruposUsuarios = [];

				foreach ($obj->getUsuarios() as $key => $usuario) {
					$usuarioAtual = new Usuario(); $usuarioAtual->fromArray($usuario);
					$gruposUsuarios[] = ['usuario_id' => $usuarioAtual->getId(), 'grupo_usuario_id' =>  $obj->getId()];
				}

				DB::table(self::TABELA_RELACIONAL)->insert($gruposUsuarios);
			}
			catch (\Exception $e) {
				throw new ColecaoException("Erro ao adicioanr um grupo de usuário ao banco", $e->getCode(), $e);
			}
		}	
	}

	function remover($id) {
		if($this->validarDeleteGrupoDeUsuario($id)){

			try {					
				DB::table(self::TABELA)->where('deleted_at', NULL)->where('id', $id)->update(['deleted_at'=> Carbon::now()->toDateTimeString()]);
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
				DB::table(self::TABELA)->where('deleted_at', NULL)->where('id', $obj->getId())->update([
					'nome' => $obj->getNome(),
					'descricao' => $obj->getDescricao()
				]);

				DB::table(self::TABELA_RELACIONAL)->where('grupo_usuario_id', $obj->getId())->delete();

				if(is_array($obj->getUsuarios()) ? count($obj->getUsuarios()): false){
					$gruposUsuarios = [];

					foreach($obj->getUsuarios() as $usuario){
						$usuarioAtual = new Usuario(); $usuarioAtual->fromArray($usuario);
						$gruposUsuarios[] = ['usuario_id' => $usuarioAtual->getId(), 'grupo_usuario_id' =>  $obj->getId()];
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
			return (DB::table(self::TABELA)->where('deleted_at', NULL)->where('id', $id)->count()) ? $this->construirObjeto(DB::table(self::TABELA)->where('id', $id)->first()) : [];
		}
		catch (\Exception $e) {
			throw new ColecaoException('Erro ao buscar grupo de usuário com id.', $e->getCode(), $e);
		}
	}

	function comUsuarioId($id = 0){
		try {	
			$grupoDeusuarios = DB::table(self::TABELA)->where('deleted_at', NULL)->select(self::TABELA . '.*')->join(self::TABELA_RELACIONAL, self::TABELA_RELACIONAL. '.grupo_usuario_id', '=', self::TABELA . '.id')->where(self::TABELA_RELACIONAL. '.usuario_id', $id)->get();

            $grupoDeusuariosObjects = [];

			foreach($grupoDeusuarios as $grupo) {

				$grupoDeusuariosObjects[] =  $this->construirObjeto($grupo);
			}

			return $grupoDeusuariosObjects;
		}
		catch (\Exception $e)
		{
			throw new ColecaoException('Erro ao buscar grupos de usuário com  o id do usuário!', $e->getCode(), $e);
		}
	}

	/**
	 * @inheritDoc
	*/
	function todos($limite = 0, $pulo = 0, $search = '') {
		try {	
			$query = DB::table(self::TABELA)->where(self::TABELA . '.deleted_at', NULL)->select(self::TABELA . '.*');

			if($search != '') {
				$buscaCompleta = $search;
				$palavras = explode(' ', $buscaCompleta);

				$query->leftJoin(self::TABELA_RELACIONAL, self::TABELA_RELACIONAL . '.grupo_usuario_id', '=', self::TABELA .'.id');
				$query->leftJoin(ColecaoUsuarioEmBDR::TABELA, ColecaoUsuarioEmBDR::TABELA . '.id', '=', self::TABELA_RELACIONAL .'.usuario_id');
				$query->leftJoin(ColecaoColaboradorEmBDR::TABELA, ColecaoColaboradorEmBDR::TABELA . '.usuario_id', '=', ColecaoUsuarioEmBDR::TABELA .'.id');


				$query->where(function($query)  use ($buscaCompleta){
					$query->whereRaw(self::TABELA . '.id like "%' . $buscaCompleta . '%"');
					$query->orWhereRaw(self::TABELA . '.nome like "%' . $buscaCompleta . '%"');
					$query->orWhereRaw(self::TABELA . '.descricao like "%' . $buscaCompleta . '%"');
					$query->orWhereRaw(ColecaoUsuarioEmBDR::TABELA . '.login like "%' . $buscaCompleta . '%"');
					$query->orWhereRaw(ColecaoColaboradorEmBDR::TABELA . '.nome like "%' . $buscaCompleta . '%"');
					$query->orWhereRaw(ColecaoColaboradorEmBDR::TABELA . '.sobrenome like "%' . $buscaCompleta . '%"');
				});

				if($query->count() == 0){
					$query->where(function($query) use ($palavras){
						foreach ($palavras as $key => $palavra) {
							if($palavra != " "){
								$query->whereRaw(self::TABELA . '.id like "%' . $palavra . '%"');
								$query->orWhereRaw(self::TABELA . '.nome like "%' . $palavra . '%"');
								$query->orWhereRaw(self::TABELA . '.descricao like "%' . $palavra . '%"');
								$query->orWhereRaw(ColecaoUsuarioEmBDR::TABELA . '.login like "%' . $palavra . '%"');
								$query->orWhereRaw(ColecaoColaboradorEmBDR::TABELA . '.nome like "%' . $palavra . '%"');
								$query->orWhereRaw(ColecaoColaboradorEmBDR::TABELA . '.sobrenome like "%' . $palavra . '%"');
							}
						}
						
					});
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
		catch (\Exception $e) {
			throw new ColecaoException($e->getMessage(), $e->getCode(), $e);
		}
	}
	
	function todosComIdsDeUsuario($ids = []) {
		try {	
			$usuarios = DB::table(self::TABELA)->where('deleted_at', NULL)->whereIn('id', $ids)->get();
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
		return $grupoDeUsuario->toArray();
	}	

    function contagem() {
		return DB::table(self::TABELA)->where('deleted_at', NULL)->count();
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
		$qtdReacionamento = DB::table(self::TABELA)->where('deleted_at', NULL)->where('id', $id)->count();

		if($qtdReacionamento == 0){
			throw new ColecaoException('O grupo selecionado para remoção, não foi encontrado na base de dados!');
		}

		return true;
	}
}

?>