<?php
use phputil\traits\ToArray;
use phputil\traits\GetterSetterWithBuilder;
use phputil\traits\FromArray;
/**
 *	Estado
 *
 *  @author Rafael Vinicius Barros Ferreira
 *  @version	0.1
 */
class Estado {
    use GetterSetterWithBuilder;
    use ToArray;
    use FromArray;

	private $id;
	private $nome;
	private $sigla;
	private $pais;

	function __construct($id = '', $nome = '',  $sigla = '', $pais = '')
	{
		$this->id =  (int) $id;
		$this->nome =  $nome;
		$this->sigla =  $sigla;
		$this->pais = $pais;
 	}
}

?>