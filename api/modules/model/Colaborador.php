<?php
use phputil\traits\ToArray;
use phputil\traits\GetterSetterWithBuilder;
use phputil\traits\FromArray;

/**
 *	Colaborador
 *
 *  @author Rafael Vinicius Barros Ferreira
 *  @version	1.0
 */
class Colaborador {
	use GetterSetterWithBuilder;
   use ToArray;
   use FromArray;

   private $id;
   private $nome;
   private $sobrenome;
   private $email;
   private $usuario;
   private $lojas;
   private $setor;
   private $avatar;

   const TAM_TEXT_MIM = 3;
   const TAM_TEXT_MAX = 50;
	const CAMINHO_IMAGEM = 'colaboradores';


    function __construct($id = 0, $nome = '', $sobrenome = '', $email = '', $usuario = null,  $setor = null, $lojas = [], $avatar = '') {
        $this->id = $id;
        $this->nome =  $nome;
        $this->sobrenome = $sobrenome;
        $this->email = $email;
        $this->usuario = $usuario;
        $this->lojas = $lojas;
        $this->setor = $setor;
        $this->avatar = $avatar;
    }
}
?>