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
	private $latitude;
	private $longitude;
	private $codigoIbge;
    private $bairro;
    
	function __construct(
		$id = 0,
		$cep = '',
		$logradouro = '',
		$latitude = '',
		$longitude = '',
		$codigoIbge = '',
		$bairro = ''
	)
	{
		$this->id = (int) $id;
		$this->cep = $cep;
		$this->logradouro = $logradouro;
		$this->latitude = (double) $latitude;
		$this->longitude = (double) $longitude;
		$this->codigoIbge = $codigoIbge;
		$this->bairro = $bairro;
	}
}

?>