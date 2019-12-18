<?php
use phputil\TDateTime;
/**
 *	Coleção de Endereço em Banco de Dados Relacional.
 *
 *  @author		Rafael Vinicius Barros Ferreira
 *	@version	0.1
 */

class ColecaoEnderecoEmBDR implements ColecaoEndereco
{

	const TABELA = 'endereco';

	private $pdoW;

	function __construct(PDOWrapper $pdoW)
	{
		$this->pdoW = $pdoW;
	}

	function adicionar(&$obj)
	{
		if($this->validarEndereco($obj))
		{
			try
			{
				$sql = 'INSERT INTO ' . self::TABELA . ' (cep, logradouro, latitude, longitude, codigo_ibge, bairro_id) VALUES (:cep, :logradouro, :latitude, :longitude, :codigo_ibge, :bairro);';

				$this->pdoW->execute($sql, [
					'cep' => $obj->getCep(),
					'logradouro' => $obj->getLogradouro(),
					'latitude' => $obj->getLatitude(),
					'longitude' => $obj->getLongitude(),
					'codigo_ibge' => $obj->getCodigoIbge(),
					'bairro' => $obj->getBairro()->getId()
				]);

				$obj->setId($this->pdoW->lastInsertId());
			}
			catch (\Exception $e)
			{
				throw new ColecaoException($e->getMessage(), $e->getCode(), $e);
			}
		}
	}

	function atualizar(&$obj)
	{
		if($this->validarEndereco($obj))
		{
			try
			{
				$sql  = 'SET foreign_key_checks = 0';
				$this->pdoW->execute($sql);

				$sql = 'UPDATE ' . self::TABELA . ' SET
					cep := cep,
					logradouro :=logradouro,
					bairro_id := bairro
				WHERE id = :id';

				$this->pdoW->execute($sql, [
					'cep' => $this->retirarCaracteresEspeciais($obj->getCep()),
					'logradouro' => $obj->getLogradouro(),
					'bairro' => $obj->getBairro()->getId(),
					'id' => $obj->getId()
				]);

				$sql  = 'SET foreign_key_checks = 1';
				$this->pdoW->execute($sql);

			}
			catch (\Exception $e)
			{
				throw new ColecaoException($e->getMessage(), $e->getCode(), $e);
			}
		}
	}

	function remover($id)
	{
		try
		{
			$sql  = 'SET foreign_key_checks = 0';
			$this->pdoW->execute($sql);
			if($this->pdoW->deleteWithId($id, self::TABELA))
			{
				$sql  = 'SET foreign_key_checks = 1';
				$this->pdoW->execute($sql);
				return true;
			}
			else
			{
				return false;
			}
		}
		catch(\Exception $e)
		{
			throw new ColecaoException($e->getMessage(), $e->getCode(), $e);
		}
	}

	function comId($id)
	{
		try
		{
			return $this->pdoW->objectWithId([$this, 'construirObjeto'], $id, self::TABELA);
		}catch(\Exception $e)
		{
			throw new ColecaoException($e->getMessage(), $e->getCode(), $e);
		}
	}

	/**
	 * @inheritDoc
	 */
	function todos($limite = 0, $pulo = 0)
	{
		try
		{
			return $this->pdoW->allObjects([$this, 'construirObjeto'], self::TABELA, $limite, $pulo);
		}
		catch(\Exception $e)
		{
			throw new ColecaoException($e->getMessage(), $e->getCode(), $e);
		}
	}

	function construirObjeto(array $row)
	{
		return new Endereco(
			$row['id'],
			$row['cep'],
			$row['logradouro'],
			$row['latitude'],
			$row['longitude'],
			$row['codigo_ibge'],
			$row['bairro_id']
		);
	}

	function contagem()
	{
		try
		{
			return $this->pdoW->countRows(self::TABELA);
		}
		catch (\Exception $e)
		{
			throw new ColecaoException($e->getMessage(), $e->getCode(), $e);
		}
	}

	public function comBairroECep( $cep, $bairroId)
	{
		try
		{
			$sql = 'SELECT *  FROM ' . self::TABELA .' as endereco join '. ColecaoBairroEmBDR::TABELA .' as bairro on endereco.bairro_id = bairro.id WHERE endereco.cep like "%'. $cep .'%" and bairro.id = :bairroId;';

			return  $this->pdoW->queryObjects([$this, 'construirObjeto'], $sql, ['bairroId'=>$bairroId]);
		}
		catch (\Exception $e)
		{
			throw new ColecaoException($e->getMessage(), $e->getCode(), $e);
		}
	}

	public function comLatitudeElongitude($latitude, $longitude)
	{
		try
		{
			$sql = 'SELECT * FROM ' . self::TABELA .' where (
			6371 * acos(
            cos(radians(:latitude)) *
            cos(radians(latitude)) *
            cos(radians(:longitude) - radians(longitude)) +
            sin(radians(:latitude)) *
            sin(radians(latitude))
        )) <= 0.25;';

			return  $this->pdoW->queryObjects([$this, 'construirObjeto'], $sql, ['latitude'=>$latitude, 'longitude'=> $longitude]);
		}
		catch (\Exception $e)
		{
			throw new ColecaoException($e->getMessage(), $e->getCode(), $e);
		}
	}

	public function comCep($cep)
	{
		try
		{
			$sql = 'SELECT *  FROM ' . self::TABELA .' as endereco WHERE endereco.cep like "%'. $cep .'%" ;';

			return  $this->pdoW->queryObjects([$this, 'construirObjeto'],$sql);
		}
		catch (\Exception $e)
		{
			throw new ColecaoException($e->getMessage(), $e->getCode(), $e);
		}
	}

	/**
	*  Valida o endereco, lançando uma exceção caso haja algo inválido.
	*  @throws ColecaoException
	*/
	private function validarEndereco(&$obj)
	{
		if(!is_string($obj->getLogradouro()))
		{
			throw new ColecaoException('Valor inválido para bairro.');
		}

		if($obj->getCep() != '') $this->validarCep($obj->getCep());

		$sql = 'select * from ' . self::TABELA .' where endereco.cep like "%' . $obj->getCep() . '%" and (
			6371 * acos(
            cos(radians(:latitude)) *
            cos(radians(latitude)) *
            cos(radians(:longitude) - radians(longitude)) +
            sin(radians(:latitude)) *
            sin(radians(latitude))
        )) <= 0.25;';

		$enderecoResposta = $this->pdoW->queryObjects([$this, 'construirObjeto'], $sql, ['latitude'=>$obj->getLatitude(), 'longitude'=> $obj->getLongitude()]);

		if(!empty($enderecoResposta))
		{
			$enderecoResposta = $enderecoResposta[0];
			$obj->setId($enderecoResposta->getId());
			return false;
		}
		else return true;
	}
}

?>