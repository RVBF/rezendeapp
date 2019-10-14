<?php
use Illuminate\Database\Capsule\Manager as DB;
/**
 *	Coleção de Colaborador em Banco de Dados Relacional.
 *
 *  @author		Rafael Vinicius Barros Ferreira
 *	@version	0.1
 */

class ColecaoColaboradorEmBDR implements ColecaoColaborador {

	const TABELA = 'colaborador';
    const TABELA_RELACIONAL = 'atuacao';
    
	function __construct(){}

	function adicionar(&$obj) {
		if($this->validarColaborador($obj)) {
			try {	

				DB::statement('SET FOREIGN_KEY_CHECKS=0;');
				$id = DB::table(self::TABELA)->insertGetId([ 
					'nome' => $obj->getNome() ,
					'sobrenome' => $obj->getSobreNome(),
					'email' => $obj->getEmail(),
					'usuario_id' => $obj->getUsuario()->getId(),
					'setor_id'	=> $obj->getSetor()->getId()
				]);
				
				$atuacoesLojas = [];

				foreach($obj->getLojas() as $loja){
					
					$atuacoesLojas[] = ['loja_id' => $loja->getId(), 'colaborador_id' =>  $id];
				}
				
				DB::table(self::TABELA_RELACIONAL)->insert($atuacoesLojas);
				
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
		if($this->validarRemocaoColaborador($id)) {
			try {	
				DB::statement('SET FOREIGN_KEY_CHECKS=0;');
				
				$removido = DB::table(self::TABELA)->where('id', $id)->delete();
				if($removido) $removido = DB::table(self::TABELA_RELACIONAL)->where('colaborador_id', $id)->delete();

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
		if($this->validarColaborador($obj)) {
			try {
			
				DB::statement('SET FOREIGN_KEY_CHECKS=0;');
	
				DB::table(self::TABELA)->where('id', $obj->getId())->update([ 
					'nome' => $obj->getNome() ,
					'sobrenome' => $obj->getSobreNome(),
					'email' => $obj->getEmail(),
					'usuario_id' => $obj->getUsuario()->getId()
				]);
				
				DB::table(self::TABELA_RELACIONAL)->where('colaborador_id', $obj->getId())->delete();

				if(is_array($obj->getLojas()) ? count($obj->getLojas()) : false){
					$atuacoesLojas = [];

					foreach($obj->getLojas() as $loja){
					
						$atuacoesLojas[] = ['loja_id' => $loja->getId(), 'colaborador_id' =>  $obj->getId()];
					}
					DB::table(self::TABELA_RELACIONAL)->insert($atuacoesLojas);
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
			$colaborador = $this->construirObjeto(DB::table(self::TABELA)->where('id', $id)->get()[0]);

			return $colaborador;
		}
		catch (\Exception $e)
		{
			throw new ColecaoException($e->getMessage(), $e->getCode(), $e);
		}
	}

	function comUsuarioId($id){
		try {	
			$colaborador = DB::table(self::TABELA)->where('usuario_Id', $id)->get();

			if(is_array($colaborador)) $colaborador = (count($colaborador) > 0) ? $this->construirObjeto($colaborador[0]) : null;

			return $colaborador;
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
			$colaboradores = DB::table(self::TABELA)->offset($limite)->limit($pulo)->get();

            $colaboradoresObjects = [];

			foreach($colaboradores as $usuario) {

				$colaboradoresObjects[] =  $this->construirObjeto($usuario);
			}

			return $colaboradoresObjects;
		}
		catch (\Exception $e)
		{
			throw new ColecaoException($e->getMessage(), $e->getCode(), $e);
		}
	}
	
	function todosComId($ids = []) {
		try {	
			$colaboradores = DB::table(self::TABELA)->whereIn('id', $ids)->get();
			$colaboradoresObjects = [];

			foreach ($colaboradores as $usuario) {
				$colaboradoresObjects[] =  $this->construirObjeto($usuario);
			}

			return $colaboradoresObjects;
		}
		catch (\Exception $e)
		{
			throw new ColecaoException($e->getMessage(), $e->getCode(), $e);
		}
	}

	function construirObjeto(array $row) {
		$usuario = ($row['usuario_id'] > 0) ? Dice::instance()->create('ColecaoUsuario')->comId($row['usuario_id']) : null;
		$setor = ($row['setor_id'] > 0) ? Dice::instance()->create('ColecaoSetor')->comId($row['setor_id']) : null;

        $lojas = Dice::instance()->create('ColecaoLoja')->comColaboradorId($row['id']);

		$colaborador = new Colaborador($row['id'], $row['nome'], $row['sobrenome'], $row['email'], $usuario, $setor, (is_array($lojas)) ? $lojas : []);
		return $colaborador;
	}	

    function contagem() {
		return DB::table(self::TABELA)->count();
	}

	private function validarColaborador(&$obj) {
		if(!is_string($obj->getNome())) throw new ColecaoException('Valor inválido para nome.');
		
		if(!is_string($obj->getSobrenome())) throw new ColecaoException('Valor inválido para a sobrenome.');
		if(!is_string($obj->getEmail())) throw new ColecaoException('Valor inválido para a e-mail.');

		if(strlen($obj->getNome()) <= Colaborador::TAM_TEXT_MIM && strlen($obj->getNome()) > Colaborador::TAM_TEXT_MAX) throw new ColecaoException('O nome deve conter no mínimo '. Colaborador::TAM_TEXT_MIM . ' e no máximo '. Colaborador::TAM_TEXT_MAX . '.');
		if(strlen($obj->getSobrenome()) <= Colaborador::TAM_TEXT_MIM && strlen($obj->getSobrenome()) > Colaborador::TAM_TEXT_MAX) throw new ColecaoException('O nome deve conter no mínimo '. Colaborador::TAM_TEXT_MIM . ' e no máximo '. Colaborador::TAM_TEXT_MAX . '.');

		if($this->validarFormatoDeEmail($obj->getEmail())) throw new Exception("Formato de e-mail inválido.");
		
		$quantidade = DB::table(self::TABELA)->where('email', $obj->getEmail())->where('id', '!=', $obj->getId())->count();
		
		if($quantidade > 0){
			throw new ColecaoException('Já exite uma colaborador cadastrado com esse email.');
		}
		return true;
	}

	private function validarRemocaoColaborador($id){
		$quantidade = DB::table(ColecaoUsuarioEmBDR::TABELA)->where('usuario_id', $id)->count();

		if($quantidade > 0) throw new ColecaoException('Não foi possível excluir o colaborador por que ele possui um usuário relacionado a ele. Exclua todas o usuário relacionado e tente novamente.');
		
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
