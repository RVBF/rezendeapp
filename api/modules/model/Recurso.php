<?php
use phputil\traits\ToArray;
use phputil\traits\GetterSetterWithBuilder;
use phputil\traits\FromArray;
/**
 *	Recurso
 *
 *  @author Leonardo Carvalhães Bernardo
 *  @version	1.0
 */
class Recurso {
   use GetterSetterWithBuilder;
   use ToArray;
   use FromArray;

   private $id;
   private $nome;
   private $model;
   private $rotas;

   const TABELA = 'recurso';

   const PERMITIR = 'Permitir';
   const NEGAR = 'Negar';

   function __construct($id = 0, $nome = null, $model = null, $rotas = []) {
      $this->id = $id;
      $this->nome = $nome;
      $this->model = $model;
      $this->rotas = $rotas;
   }
}
?>