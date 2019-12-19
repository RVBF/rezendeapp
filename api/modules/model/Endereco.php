<?php
use phputil\traits\ToArray;
use phputil\traits\GetterSetterWithBuilder;
use phputil\traits\FromArray;

/**
 *	Endereco
 *
 *  @authoRafael Vinicius Barros Ferreira
 *  @version	1.0
 */
class Endereco {
    use GetterSetterWithBuilder;
    use ToArray;
    use FromArray;
    
	private $id;
	private $cep;
	private $logradouro;
	private $numero;
	private $complemento;
	private $bairro;
	private $cidade;
    private $uf;
	    
	function __construct(
		$id = 0,
		$cep = '',
		$logradouro = '',
		$numero = '',
		$complemento = '',
		$bairro = '',
		$cidade = '',
		$uf = ''
	)
	{
		$this->id = (int) $id;
		$this->cep = $cep;
		$this->logradouro = $logradouro;
		$this->numero = $numero;
		$this->complemento = $complemento;
		$this->bairro = $bairro;
		$this->cidade = $cidade;
		$this->uf = $uf;
	}
}

?>