<?php
use Illuminate\Database\Capsule\Manager as DB;
use \phputil\RTTI;
use Carbon\Carbon;


/**
 *	Coleção de Colaborador em Banco de Dados Relacional.
 *
 *  @author		Rafael Vinicius Barros Ferreira
 *	@version	1.0
 */

class ColecaoColaboradorEmBDR implements ColecaoColaborador {

	const TABELA = 'colaborador';
    const TABELA_RELACIONAL = 'atuacao';

	function __construct(){
	}

	function adicionar(&$obj) {
		if($this->validarColaborador($obj)) {
			try {

				DB::statement('SET FOREIGN_KEY_CHECKS=0;');
				$id = DB::table(self::TABELA)->insertGetId([
					'nome' => $obj->getNome() ,
					'sobrenome' => $obj->getSobrenome(),
					'email' => $obj->getEmail(),
					'usuario_id' => $obj->getUsuario()->getId(),
					'setor_id'	=> $obj->getSetor()->getId()
				]);

				$atuacoesLojas = [];

				foreach($obj->getLojas() as $loja){
					$lojaAtual = new Loja(); $lojaAtual->fromArray($loja);

					$atuacoesLojas[] = ['loja_id' => $lojaAtual->getId(), 'colaborador_id' =>  $id];
				}

				DB::table(self::TABELA_RELACIONAL)->insert($atuacoesLojas);

				DB::statement('SET FOREIGN_KEY_CHECKS=1;');

				$obj->setId($id);

				return $obj;
			}
			catch (\Exception $e)
			{
				throw new ColecaoException("Erro ao adicionar colaborador.", $e->getCode(), $e);
			}
		}
	}

	function remover($id) {
		if($this->validarRemocaoColaborador($id)) {
			try {
				DB::table(self::TABELA)->where('deleted_at', NULL)->where('id', $id)->update(['deleted_at' =>Carbon::now()->toDateTimeString()]);
			}
			catch (\Exception $e)
			{
				throw new ColecaoException("Erro ao remover colaborador no banco de dados!", $e->getCode(), $e);
			}
		}
	}

	function atualizar(&$obj) {
		if($this->validarColaborador($obj)) {
			try {

				DB::statement('SET FOREIGN_KEY_CHECKS=0;');
				DB::table(self::TABELA)->where('deleted_at', NULL)->where('id', $obj->getId())->update([
					'nome' => $obj->getNome() ,
					'sobrenome' => $obj->getSobrenome(),
					'email' => $obj->getEmail(),
					'usuario_id' => $obj->getUsuario()->getId(),
					'avatar_id' => ($obj->getAvatar() instanceof Anexo) ? $obj->getAvatar()->getId() : 0,
				]);

				DB::table(self::TABELA_RELACIONAL)->where('colaborador_id', $obj->getId())->delete();
				if(is_array($obj->getLojas()) ? count($obj->getLojas()) : false){
					$atuacoesLojas = [];

					DB::table(self::TABELA_RELACIONAL)->where('colaborador_id', $obj->getId())->delete();

					foreach($obj->getLojas() as $loja){
						$lojaAtual = new Loja(); $lojaAtual->fromArray($loja);
						$atuacoesLojas[] = ['loja_id' => $lojaAtual->getId(), 'colaborador_id' =>  $obj->getId()];
					}

					DB::table(self::TABELA_RELACIONAL)->insert($atuacoesLojas);
				}

				DB::statement('SET FOREIGN_KEY_CHECKS=1;');
				return $obj;
			}
			catch (\Exception $e) {
				throw new ColecaoException("Erro ao atualizar colaborador no banco de dados", $e->getCode(), $e);
			}
		}
	}

	function atualizarAvatar(&$obj){
		if($this->validarColaborador($obj)){
			try {
				DB::statement('SET FOREIGN_KEY_CHECKS=0;');

				DB::table(self::TABELA)->where('deleted_at', NULL)->where('id', $obj->getId())->update([
					'avatar_id' => ($obj->getAvatar() instanceof Anexo) ? $obj->getAvatar()->getId() : 0
				]);

				DB::statement('SET FOREIGN_KEY_CHECKS=1;');

			}
			catch (\Exception $e)
			{
				throw new ColecaoException("Erro ao atualizar colaborador no banco de dados", $e->getCode(), $e);
			}
		}
	}

	function comId($id){
		try {
			return (DB::table(self::TABELA)->where('id', $id)->count()) ? $this->construirObjeto(DB::table(self::TABELA)->where('id', $id)->first()) : [];
		}
		catch (\Exception $e)
		{
			throw new ColecaoException("Erro ao buscar colaborador com id no banco de dados!", $e->getCode(), $e);
		}
	}

	function comUsuarioId($id){
		try {
			return (DB::table(self::TABELA)->where('usuario_id', $id)->count()) ? $this->construirObjeto(DB::table(self::TABELA)->where('usuario_id', $id)->first()) : [];
		}
		catch (\Exception $e) {
			throw new ColecaoException("Erro a buscar usuario com usando a referêrencia de colaborador no banco de dados!", $e->getCode(), $e);
		}
	}

	/**
	 * @inheritDoc
	 */
	function todos($limite = 10, $pulo = 0, $search = '') {
		try {
			$query = DB::table(self::TABELA)->selectRaw(self::TABELA . '.*, COUNT('.self::TABELA.'.id) as qtd')->where(self::TABELA .'.deleted_at', NULL);

			if($search != '') {
				$buscaCompleta = $search;
				$palavras = explode(' ', $buscaCompleta);

				$query->leftJoin(ColecaoGrupoUsuarioEmBDR::TABELA_RELACIONAL, ColecaoGrupoUsuarioEmBDR::TABELA_RELACIONAL . '.usuario_id', '=', self::TABELA .'.usuario_id');
				$query->leftJoin(ColecaoGrupoUsuarioEmBDR::TABELA, ColecaoGrupoUsuarioEmBDR::TABELA . '.id', '=', ColecaoGrupoUsuarioEmBDR::TABELA_RELACIONAL .'.grupo_usuario_id');
				$query->leftJoin(self::TABELA_RELACIONAL, self::TABELA_RELACIONAL . '.colaborador_id', '=', self::TABELA .'.id');
				$query->leftJoin(ColecaoLojaEmBDR::TABELA, ColecaoLojaEmBDR::TABELA . '.id', '=', self::TABELA_RELACIONAL .'.loja_id');
				$query->leftJoin(ColecaoSetorEmBDR::TABELA, ColecaoSetorEmBDR::TABELA . '.id', '=', self::TABELA .'.setor_id');


				$query->where(function($query)  use ($buscaCompleta){
					$query->whereRaw(self::TABELA . '.id like "%' . $buscaCompleta . '%"');
					$query->orWhereRaw(self::TABELA . '.nome like "%' . $buscaCompleta . '%"');
					$query->orWhereRaw(self::TABELA . '.sobrenome like "%' . $buscaCompleta . '%"');
					$query->orWhereRaw(self::TABELA . '.email like "%' . $buscaCompleta . '%"');
					$query->orWhereRaw(ColecaoLojaEmBDR::TABELA . '.nomeFantasia like "%' . $buscaCompleta . '%"');
					$query->orWhereRaw(ColecaoLojaEmBDR::TABELA . '.razaoSocial like "%' . $buscaCompleta . '%"');
					$query->orWhereRaw(ColecaoGrupoUsuarioEmBDR::TABELA . '.nome like "%' . $buscaCompleta . '%"');
					$query->orWhereRaw(ColecaoSetorEmBDR::TABELA . '.titulo like "%' . $buscaCompleta . '%"');
				});

				if($query->count() == 0){
					$query->where(function($query) use ($palavras){
						foreach ($palavras as $key => $palavra) {
							if($palavra != " "){
								$query->whereRaw(self::TABELA . '.id like "%' . $palavra . '%"');
								$query->orWhereRaw(self::TABELA . '.nome like "%' . $palavra . '%"');
								$query->orWhereRaw(self::TABELA . '.sobrenome like "%' . $palavra . '%"');
								$query->orWhereRaw(self::TABELA . '.email like "%' . $palavra . '%"');
								$query->orWhereRaw(ColecaoLojaEmBDR::TABELA . '.nomeFantasia like "%' . $palavra . '%"');
								$query->orWhereRaw(ColecaoLojaEmBDR::TABELA . '.razaoSocial like "%' . $palavra . '%"');
								$query->orWhereRaw(ColecaoGrupoUsuarioEmBDR::TABELA . '.nome like "%' . $palavra . '%"');
								$query->orWhereRaw(ColecaoSetorEmBDR::TABELA . '.titulo like "%' . $palavra . '%"');
							}
						}

					});
				}

				$query->groupBy(self::TABELA.'.id');
			}

         if($pulo) $query->skip($pulo);
         if($limite) $query->take($limite);

         $query->groupBy(self::TABELA . '.id', self::TABELA . '.nome', self::TABELA . '.sobrenome', self::TABELA . '.email', self::TABELA . '.deleted_at', self::TABELA . '.usuario_id', self::TABELA . '.setor_id', self::TABELA . '.avatar_id');

			$colaboradores = $query->get();

         $colaboradoresObjects = [];

			foreach($colaboradores as $usuario) $colaboradoresObjects[] =  $this->construirObjeto($usuario);

			return $colaboradoresObjects;
		}
		catch (\Exception $e) {
			throw new ColecaoException('Erro ao listar colaboradores.', $e->getCode(), $e);
		}
	}

	function todosComId($ids = []) {
		try {
			$colaboradores = DB::table(self::TABELA)->where('deleted_at', NULL)->whereIn('id', $ids)->get();
			$colaboradoresObjects = [];

			foreach ($colaboradores as $usuario) {
				$colaboradoresObjects[] =  $this->construirObjeto($usuario);
			}

			return $colaboradoresObjects;
		}
		catch (\Exception $e)
		{
			throw new ColecaoException("Erro ao listar colaboradores com a referência de usuário!", $e->getCode(), $e);
		}
	}

	function construirObjeto(array $row) {
		$avatar =  ($row['avatar_id'] > 0) ? Dice::instance()->create('ColecaoAnexo')->comId($row['avatar_id']) : null;
		$usuario = ($row['usuario_id'] > 0) ? Dice::instance()->create('ColecaoUsuario')->comId($row['usuario_id']) : null;
		$setor = ($row['setor_id'] > 0) ? Dice::instance()->create('ColecaoSetor')->comId($row['setor_id']) : null;
		$lojas = Dice::instance()->create('ColecaoLoja')->comColaboradorId($row['id']);
		$colaborador = new Colaborador($row['id'], $row['nome'], $row['sobrenome'], $row['email'], $usuario, $setor, (is_array($lojas)) ? $lojas : [], $avatar);

		return $colaborador->toArray();
	}

    function contagem() {
		return DB::table(self::TABELA)->count();
	}

	private function validarColaborador(&$obj) {
		if(!is_string($obj->getNome())) throw new ColecaoException('Valor inválido para nome!');

		if(!is_string($obj->getSobrenome())) throw new ColecaoException('Valor inválido para a sobrenome!');
		if(!is_string($obj->getEmail())) throw new ColecaoException('Valor inválido para a e-mail!');

		if(strlen($obj->getNome()) <= Colaborador::TAM_TEXT_MIM && strlen($obj->getNome()) > Colaborador::TAM_TEXT_MAX) throw new ColecaoException('O nome deve conter no mínimo '. Colaborador::TAM_TEXT_MIM . ' e no máximo '. Colaborador::TAM_TEXT_MAX . '.');
		if(strlen($obj->getSobrenome()) <= Colaborador::TAM_TEXT_MIM && strlen($obj->getSobrenome()) > Colaborador::TAM_TEXT_MAX) throw new ColecaoException('O nome deve conter no mínimo '. Colaborador::TAM_TEXT_MIM . ' e no máximo '. Colaborador::TAM_TEXT_MAX . '.');

		if($this->validarFormatoDeEmail($obj->getEmail())) throw new Exception("Formato de e-mail inválido!");

		$quantidade = DB::table(self::TABELA)->where('email', $obj->getEmail())->where('id', '!=', $obj->getId())->count();

		if($quantidade > 0){
			throw new ColecaoException('Já exite uma colaborador cadastrado com esse email.');
		}
		return true;
	}

	private function validarRemocaoColaborador($id){
		$quantidadeUsuario = DB::table(self::TABELA)->where('deleted_at', NULL)->where('id', $id)->count();

		if($quantidadeUsuario == 0) throw new ColecaoException('O colaborador selecionado para delete não foi encontrado!');
		if(DB::table(self::TABELA)->where('deleted_at', NULL)->count() == 1) throw new Exception("Não é possível excluir o colaborador quando há somente 1 colaborador cadastrado, porque é necesssário ao menos 1 colaborador cadastrado para que possa ter relação com outras depências do sistema.");

		return true;
	}

	/**
	*  Valida o formato do e-mail do usuário, lançando uma exceção caso haja algo inválido.
	*  @throws ColecaoException
	*/
	private function validarFormatoDeEmail($email) {
		if (preg_match('"/^([[:alnum:]_.-]){3,}@([[:lower:][:digit:]_.-]{3,})(.[[:lower:]]{2,3})(.[[:lower:]]{2})?$/"', $email)) {
			return true;
		}
		else
		{
			return false;
		}
	}

}
?>
