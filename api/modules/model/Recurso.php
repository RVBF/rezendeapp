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

   public static function todosOsRecursosERotas() {
      return [
         ['id'=> 1, 'nome'=> 'Visualizar Checklists', 'model'=> 'Checklist', 'rotas'=> [
            ['id'=> 1, 'caminho'=> '/checklist', 'metodo'=> 'get'],
            ['id'=> 16, 'caminho'=> '/checklist/{id}', 'metodo'=> 'get']
         ]],
         ['id'=> 2, 'nome'=> 'Cadastrar Checklists', 'model'=> 'Checklist', 'rotas'=> [
            ['id'=> 2, 'caminho'=> '/checklist', 'metodo'=> 'post']
         ]],
         ['id'=> 3, 'nome'=> 'Editar Checklists', 'model'=> 'Checklist', 'rotas'=> [
            ['id'=> 3, 'caminho'=> '/checklist', 'metodo'=> 'put']
         ]],
         ['id'=> 5, 'nome'=> 'Remover Checklists', 'model'=> 'Checklist', 'rotas'=> [
            ['id'=> 5, 'caminho'=> '/checklist', 'metodo'=> 'delete']
         ]],

         ['id'=> 6, 'nome'=> 'Visualizar PAs', 'model'=> 'PA', 'rotas'=> [
            ['id'=> 6, 'caminho'=> '/plano-acao', 'metodo'=> 'get'],
            ['id'=> 17, 'caminho'=> '/plano-acao/{id}', 'metodo'=> 'get']
         ]],
         ['id'=> 7, 'nome'=> 'Cadastrar PAs', 'model'=> 'PA', 'rotas'=> [
            ['id'=> 7, 'caminho'=> '/plano-acao', 'metodo'=> 'post']
         ]],
         ['id'=> 8, 'nome'=> 'Editar PAs', 'model'=> 'PA', 'rotas'=> [
            ['id'=> 8, 'caminho'=> '/plano-acao', 'metodo'=> 'put']
         ]],
         ['id'=> 9, 'nome'=> 'Executar PAs', 'model'=> 'PA', 'rotas'=> [
            ['id'=> 9, 'caminho'=> '/plano-acao/executar', 'metodo'=> 'post']
         ]],
         ['id'=> 10, 'nome'=> 'Remover PAs', 'model'=> 'PA', 'rotas'=> [
            ['id'=> 10, 'caminho'=> '/plano-acao', 'metodo'=> 'delete']
         ]],

         ['id'=> 11, 'nome'=> 'Visualizar PEs', 'model'=> 'PE', 'rotas'=> [
            ['id'=> 11, 'caminho'=> '/pendencia', 'metodo'=> 'get'],
            ['id'=> 18, 'caminho'=> '/pendencia/{id}', 'metodo'=> 'get']
         ]],
         ['id'=> 12, 'nome'=> 'Cadastrar PEs', 'model'=> 'PE', 'rotas'=> [
            ['id'=> 12, 'caminho'=> '/pendencia', 'metodo'=> 'post']
         ]],
         ['id'=> 13, 'nome'=> 'Editar PEs', 'model'=> 'PE', 'rotas'=> [
            ['id'=> 13, 'caminho'=> '/pendencia', 'metodo'=> 'put']
         ]],
         ['id'=> 14, 'nome'=> 'Executar PEs', 'model'=> 'PE', 'rotas'=> [
            ['id'=> 14, 'caminho'=> '/pendencia/exeutar/{id}', 'metodo'=> 'get']
         ]],
         ['id'=> 15, 'nome'=> 'Remover PEs', 'model'=> 'PE', 'rotas'=> [
            ['id'=> 15, 'caminho'=> '/pendencia', 'metodo'=> 'delete']
         ]],
      ];
   }
}
?>