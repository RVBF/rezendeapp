<?php
use Illuminate\Database\Capsule\Manager as DB;
use Carbon\Carbon;
/**
 *	Coleção de Endereço em Banco de Dados Relacional.
 *
 *  @author		Rafael Vinicius Barros Ferreira
 *	@version	0.1
 */

class ColecaoEnderecoEmBDR implements ColecaoEndereco {
	const TABELA = 'endereco';

	function __construct(){}

	function adicionar(&$obj) {
		if($this->validarEndereco($obj)) {
			try {
				$id = DB::table(self::TABELA)->insertGetId([
					'cep' => $obj->getCep(),
					'logradouro' => $obj->getLogradouro(), 
					'numero' => $obj->getNumero(),
					'complemento' => $obj->getComplemento(),
					'cidade' => $obj->getCidade(),
					'bairro' => $obj->getBairro(),
					'uf' => $obj->getUf()

				]);

				$obj->setId($id);
			}
			catch (\Exception $e) {
				throw new ColecaoException("Erro ao cadastrar endereço!", $e->getCode(), $e);
			}
		}
	}

	function atualizar(&$obj) {
		if($this->validarEndereco($obj)){
			try {	
				DB::statement('SET FOREIGN_KEY_CHECKS=0;');

				DB::table(self::TABELA)->where('deleted_at', NULL)->where('id', $obj->getId())->update([
					'cep' => $obj->getCep(),
					'logradouro' => $obj->getLogradouro(), 
					'numero' => $obj->getNumero(),
					'complemento' => $obj->getComplemento(),
					'cidade' => $obj->getCidade(),
					'bairro' => $obj->getBairro(),
					'uf' => $obj->getUf()
				]);

				DB::statement('SET FOREIGN_KEY_CHECKS=1;');
			}
			catch (\Exception $e)
			{

				throw new ColecaoException("Erro ao atualizar endereco!", $e->getCode(), $e);
			}
		}
		
	}

	function remover($id) {
		if($this->validarDeleteEndereco($id)){
			try {	
				DB::table(self::TABELA)->where('deleted_at', NULL)->where('id', $id)->update(['deleted_at' =>Carbon::now()->toDateTimeString()]);
			}
			catch (\Exception $e) {
				throw new ColecaoException("Erro ao remover endereço!", $e->getCode(), $e);
			}
		}
	}

	function comId($id)
	{

		try {
			return (DB::table(self::TABELA)->where('id', $id)->count()) ? $this->construirObjeto(DB::table(self::TABELA)->where('id', $id)->first()) : [];
		}catch(\Exception $e) {
			throw new ColecaoException('Erro ao buscar endereço com referência de id!', $e->getCode(), $e);
		}
	}

	function todos($limite = 0, $pulo = 0, $search = '') {
		// try {	

		// 	$query = DB::table(self::TABELA)->where('deleted_at', NULL)->select(self::TABELA . '.*');
	
		// 	return $lojasObjects;
		// }
		// catch (\Exception $e)
		// {
		// 	throw new ColecaoException("Erro ao listar lojas!", $e->getCode(), $e);
		// }
	}

	function construirObjeto(array $row) {
		$endereco = new Endereco(
			$row['id'],
			$row['cep'],
			$row['logradouro'],
			$row['numero'],
			$row['complemento'],
			$row['bairro'],
			$row['cidade'],
			$row['uf']
		);

		return $endereco->toArray();
	}

    function contagem() {
		return DB::table(self::TABELA)->count();
	}


	/**
	*  Valida o endereco, lançando uma exceção caso haja algo inválido.
	*  @throws ColecaoException
	*/
	private function validarEndereco(&$obj) {
		if(!is_string($obj->getLogradouro()) || strlen($obj->getLogradouro()) == 0) throw new ColecaoException('O campo logradouro é obrigatório!');
		if(!is_string($obj->getComplemento()) || strlen($obj->getComplemento()) == 0) throw new ColecaoException('O campo complemento é obrigatório!');
		if(!is_string($obj->getBairro()) || strlen($obj->getBairro()) == 0) throw new ColecaoException('O campo bairro é obrigatório!');
		if(!is_string($obj->getCidade()) || strlen($obj->getCidade()) == 0) throw new ColecaoException('O campo cidade é obrigatório!');
		if(!is_string($obj->getUf()) || strlen($obj->getUf()) == 0) throw new ColecaoException('O campo uf é obrigatório!');

		if(strlen($obj->getUf()) != 2) throw new ColecaoException('O campo uf deve conter no máximo 2 caracteres!');

		else return true;
	}

	private function validarDeleteEndereco($id){

		if(DB::table(self::TABELA)->where('deleted_at', NULL)->where('id', $id)->count() == 0){
			throw new ColecaoException('O endereço selecionado não foi encontrado na base de dados');
		}
		
		return true;
	}
}
?>