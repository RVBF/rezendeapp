<?php

/**
 *	Bairro
 *
 *  @author Irlon Lamblet
 *  @version	1.0
 */
class Bairro {
	private $id;
	private $nome;
	private $cidade;

	function __construct($id = '', $nome = '', $cidade = '')
	{
		$this->id = (int) $id;
		$this->nome =  $nome;
		$this->cidade = $cidade;
 	}
}

?>