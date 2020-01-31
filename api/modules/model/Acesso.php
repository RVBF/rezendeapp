<?php
use phputil\traits\ToArray;
use phputil\traits\GetterSetterWithBuilder;
use phputil\traits\FromArray;
/**
 *	Acesso
 *
 *  @author Leonardo Carvalhães Bernardo
 *  @version	1.0
 */
class Acesso {
   use GetterSetterWithBuilder;
   use ToArray;
   use FromArray;

   private $id;
   private $recurso;
   private $acessante;
   private $acao;

   const TABELA = 'acesso';

   const PERMITIR = 'Permitir';
   const NEGAR = 'Negar';

   function __construct($id = 0, $recurso = null, $acessante = null, $acao = self::PERMITIR) {
      $this->id = $id;
      $this->recurso = $recurso;
      $this->acessante = $acessante;
      $this->acao = $acao;
   }

   public static function vericarAcesso($usuarioId, $caminho, $metodo) {
      return false;
   }
}
?>