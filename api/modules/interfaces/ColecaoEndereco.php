<?php

/**
 *	Coleção de Endereço
 *
 *  @author		Rafael Vinicius Barros
 *  @version	0.1
 */

interface ColecaoEndereco extends Colecao {
	public function comBairroECep( $cep, $bairro);
	public function comCep($cep);
	public function comLatitudeElongitude($latitude, $longitude);
}
?>