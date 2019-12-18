<?php

/**
 *	Coleção de Laboratorio em Banco de Dados Relacional.
 *
 *  @author	Rafael Vinicius Barros Ferreira
 *	@version	0.1
 */

class ColecaoBairroEmBDR implements ColecaoBairro
{

	const TABELA = 'bairro';

	private $pdoW;

	function __construct(PDOWrapper $pdoW)
	{
		$this->pdoW = $pdoW;
	}

	function adicionar(&$obj)
	{
		if($this->validarBairro($obj))
		{
			try
			{
				$sql = 'INSERT INTO ' . self::TABELA . '(nome, cidade_id) VALUES ( :nome, :cidade_id)';

				$this->pdoW->execute($sql, [
					'nome' => $obj->getNome(),
					'cidade_id' => $obj->getCidade()->getId()
				]);

				$obj->setId($this->pdoW->lastInsertId());
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
			return $this->pdoW->deleteWithId($id, self::TABELA);
		}
		catch(\Exception $e)
		{
			throw new ColecaoException($e->getMessage(), $e->getCode(), $e);
		}
	}

	function atualizar(&$obj)
	{
		if(!$this->validarBairro($bairro))
		{
			try
			{
				$sql = 'UPDATE ' . self::TABELA . ' SET nome = :nome, cidade_id = :cidade_id WHERE id = :id';

				$this->pdoW->execute($sql, ['nome' => $obj->getNome(), 'cidade_id' => $obj->getCidade()->getId(),'id' => $obj->getId()]);
			}
			catch (\Exception $e)
			{
				throw new ColecaoException($e->getMessage(), $e->getCode(), $e);
			}
		}
	}

	function comId($id)
	{
		try
		{
			return $this->pdoW->objectWithId([$this, 'construirObjeto'], $id, self::TABELA);
		}
		catch(\Exception $e)
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
		return new Bairro($row['id'], $row['nome'], $row['cidade_id']);
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

	function comBairroECidade($bairro, $cidadeId)
	{
		try
		{
			$sql = 'SELECT *  FROM ' . self::TABELA .' as bairro join '. ColecaoCidadeEmBDR::TABELA .' as cidade on bairro.cidade_id = cidade.id WHERE bairro.nome like "%'. $bairro .'%" and cidade.id = :cidadeId;';

			return  $this->pdoW->queryObjects([$this, 'construirObjeto'],$sql, ['cidadeId'=>$cidadeId]);
		}
		catch (\Exception $e)
		{
			throw new ColecaoException($e->getMessage(), $e->getCode(), $e);
		}
	}

	private function validarBairro(&$bairro)
	{
		if(!is_string($bairro->getNome()))
		{
			throw new ColecaoException('Valor inválido para bairro.');
		}

		$sql = 'select * from ' . self::TABELA .' where nome like "%' . $bairro->getNome() . '%";';

		$bairroResposta =  $this->pdoW->queryObjects([$this, 'construirObjeto'], $sql);
		if(!empty($bairroResposta))
		{
			$bairroResposta = $bairroResposta[0];
			$bairro->setId($bairroResposta->getId());
			return false;
		}
		else return true;
	}
}

?>