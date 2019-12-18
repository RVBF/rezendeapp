<?php

/**
 *	Coleção de Laboratorio em Banco de Dados Relacional.
 *
 *  @author	Rafael Vinicius Barros Ferreira
 *	@version	0.1
 */

class ColecaoEstadoEmBDR implements ColecaoEstado
{

	const TABELA = 'estado';

	private $pdoW;

	function __construct(PDOWrapper $pdoW)
	{
		$this->pdoW = $pdoW;
	}

	function adicionar(&$obj)
	{
		try
		{
			$sql = 'INSERT INTO ' . self::TABELA . '(nome,pais_id)
			VALUES (
				:nome,
				:pais_id
			)';

			$this->pdoW->execute($sql, [
				'nome' => $obj->getNome(),
				'pais_id' => $obj->getPais()->getId()
			]);

			$obj->setId($this->pdoW->lastInsertId());
		}
		catch (\Exception $e)
		{
			throw new ColecaoException($e->getMessage(), $e->getCode(), $e);
		}
	}

	function remover($id)
	{
		try
		{
			return $this->pdoW->deleteWithId($id, self::TABELA);
		}catch(\Exception $e)
		{
			throw new ColecaoException($e->getMessage(), $e->getCode(), $e);
		}
	}

	function atualizar(&$obj)
	{
		try
		{
			$sql = 'UPDATE ' . self::TABELA . ' SET
			 	nome = :nome,
			 	pais_id = :pais_id
			 	WHERE id = :id';

			$this->pdoW->execute($sql, [
				'nome' => $obj->getNome(),
				'pais_id' => 1,
				'id' => $obj->getId()
			]);
		}
		catch (\Exception $e)
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
		return new Estado(
			$row['id'],
			$row['nome'],
			$row['sigla'],
			$row['pais_id']
		);
	}

	public function comUf($uf)
	{
		try
		{
			$sql = 'SELECT *  FROM ' . self::TABELA .' as estado WHERE estado.sigla like "%'. $uf .'%";';

			return  $this->pdoW->queryObjects([$this, 'construirObjeto'],$sql);
		}
		catch (\Exception $e)
		{
			throw new ColecaoException($e->getMessage(), $e->getCode(), $e);
		}
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
}

?>