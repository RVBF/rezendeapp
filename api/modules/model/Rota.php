<?php
use phputil\traits\ToArray;
use phputil\traits\GetterSetterWithBuilder;
use phputil\traits\FromArray;
/**
 *	Rota
 *
 *  @author Leonardo Carvalhães Bernardo
 *  @version	1.0
 */
class Rota {
   use GetterSetterWithBuilder;
   use ToArray;
   use FromArray;

   private $id;
   private $caminho;
   private $metodo;

   const TABELA = 'rota';

   function __construct($id = 0, $caminho = null, $metodo = 'get') {
      $this->id = $id;
      $this->caminho = $caminho;
      $this->metodo = $metodo;
   }
}
?>