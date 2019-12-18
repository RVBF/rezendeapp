<?php

/**
 *	Coleção de Laboratorio em Banco de Dados Relacional.
 *
 *  @author	Rafael Vinicius Barros Ferreira
 *	@version	0.1
 */

class ColecaoCidadeEmBDR implements ColecaoCidade
{

	const TABELA = 'cidade';

	private $pdoW;

	function __construct(PDOWrapper $pdoW)
	{
		$this->pdoW = $pdoW;
	}

	function adicionar(&$obj)
	{
		if($this->validarCidade($obj))
		{
			try
			{
				$sql = 'INSERT INTO ' . self::TABELA . '(nome, estado_id) VALUES ( :nome, :estado_id)';

				$this->pdoW->execute($sql, [
					'nome' => $obj->getNome(),
					'estado_id' => $obj->getEstado()->getId()
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
		}catch(\Exception $e)
		{
			throw new ColecaoException($e->getMessage(), $e->getCode(), $e);
		}
	}

	function atualizar(&$obj)
	{
		if($this->validarCidade($obj))
		{
			try
			{
				$sql = 'UPDATE ' . self::TABELA . ' SET nome = :nome, estado_id = :estado_id WHERE id = :id';

				$this->pdoW->execute($sql, ['nome'=>$obj->getNome(), 'estado_id'=>$obj->getEstado()->getId(), 'id'=>$obj->getId()]);
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
		return new Cidade(
			$row['id'],
			$row['nome'],
			$row['estado_id']
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

	public function comEstadoECidade( $estadoId, $cidade)
	{
		try
		{
			$sql = 'SELECT cidade.id, cidade.nome, cidade.estado_id  FROM ' . self::TABELA .' as cidade join '. ColecaoEstadoEmBDR::TABELA .' as estado on cidade.estado_id = estado.id WHERE cidade.nome like "%'. $cidade .'%";';

			return  $this->pdoW->queryObjects([$this, 'construirObjeto'],$sql, ['estadoId'=> $estadoId]);
		}
		catch (\Exception $e)
		{
			throw new ColecaoException($e->getMessage(), $e->getCode(), $e);
		}
	}

	private function validarCidade(&$cidade)
	{

		if(!is_string($cidade->getNome()))
		{
			throw new ColecaoException('Valor inválido para cidade.');
		}

		$sql = 'select nome from ' . self::TABELA .' where nome like "%:nome%";';

		$cidadeResposta =  $this->pdoW->queryObjects([$this, 'construirObjeto'],$sql, ['nome'=> ucwords(strtolower($cidade->getNome()))]);

		if(!empty($cidadeResposta))
		{
			$cidadeResposta = $cidadeResposta[0];
			$cidade->setId($cidadeResposta->getId());
			return false;
		}
		else return true;
	}
}

?>