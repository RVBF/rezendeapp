<?php
use phputil\traits\ToArray;
use phputil\traits\GetterSetterWithBuilder;
use phputil\traits\FromArray;
/**
 *	Usuario
 *
 *  @author Rafael Vinicius Barros Ferreira
 *  @version	1.0
 */
class Usuario {
   use GetterSetterWithBuilder;
   use ToArray;
   use FromArray;

   private $id;
   private $login;
   private $senha;
   private $colaborador;
   private $gruposUsuario;
   private $administrador;

   const TABELA = 'usuario';

   const TAMANHO_MINIMO_LOGIN = 4;
   const TAMANHO_MAXIMO_LOGIN = 30;

   const TAMANHO_MINIMO_SENHA = 3;
   const TAMANHO_MAXIMO_SENHA = 20;

   function __construct($id = 0, $login = '', $senha = '', $gruposUsuario = []) {
      $this->id = $id;
      $this->login = $login;
      $this->senha = $senha;
      $this->gruposUsuarios = $gruposUsuario;
   }

   public static function criarAPartirDoArray($dados) {
      return new self($dados['id'], $dados['login'], $dados['senha']);
   }
}
?>