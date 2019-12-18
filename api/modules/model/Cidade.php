<?php
use GetterSetterWithBuilder;
use ToArray;
use FromArray;
/**
 *	Cidade
 *
 *  @author Rafael Vinicius Barros Ferreira
 *  @version	0.1
 */
class Cidade {
    use GetterSetterWithBuilder;
    use ToArray;
    use FromArray;

	private $id;
	private $nome;
	private $estado;

	function __construct($id = '', $nome = '', $estado = '')
	{
		$this->id =  (int) $id;
		$this->nome =  $nome;
		$this->estado = $estado;
 	}
}
?>