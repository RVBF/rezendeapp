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
      // Último ID usado para recursos: 57
      // Último ID usado para rotas: 44

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

         ['id'=> 16, 'nome'=> 'Visualizar Setores', 'model'=> 'Setor', 'rotas'=> [
            ['id'=> 19, 'caminho'=> '/setor', 'metodo'=> 'get'],
            ['id'=> 20, 'caminho'=> '/setor/{id}', 'metodo'=> 'get']
         ]],
         ['id'=> 17, 'nome'=> 'Cadastrar Setores', 'model'=> 'Setor', 'rotas'=> [
            ['id'=> 21, 'caminho'=> '/setor', 'metodo'=> 'post']
         ]],
         ['id'=> 18, 'nome'=> 'Editar Setores', 'model'=> 'Setor', 'rotas'=> [
            ['id'=> 22, 'caminho'=> '/setor', 'metodo'=> 'put']
         ]],
         ['id'=> 19, 'nome'=> 'Remover Setores', 'model'=> 'Setor', 'rotas'=> [
            ['id'=> 23, 'caminho'=> '/setor', 'metodo'=> 'delete']
         ]],

         ['id'=> 20, 'nome'=> 'Visualizar Questionários', 'model'=> 'Questionario', 'rotas'=> [
            ['id'=> 24, 'caminho'=> '/questionario', 'metodo'=> 'get'],
            ['id'=> 25, 'caminho'=> '/questionario/{id}', 'metodo'=> 'get']
         ]],
         ['id'=> 21, 'nome'=> 'Cadastrar Questionários', 'model'=> 'Questionario', 'rotas'=> [
            ['id'=> 26, 'caminho'=> '/questionario', 'metodo'=> 'post']
         ]],
         ['id'=> 22, 'nome'=> 'Editar Questionários', 'model'=> 'Questionario', 'rotas'=> [
            ['id'=> 27, 'caminho'=> '/questionario', 'metodo'=> 'put']
         ]],
         ['id'=> 23, 'nome'=> 'Remover Questionários', 'model'=> 'Questionario', 'rotas'=> [
            ['id'=> 28, 'caminho'=> '/questionario', 'metodo'=> 'delete']
         ]],

         ['id'=> 24, 'nome'=> 'Visualizar Lojas', 'model'=> 'Loja', 'rotas'=> [
            ['id'=> 29, 'caminho'=> '/loja', 'metodo'=> 'get'],
            ['id'=> 30, 'caminho'=> '/loja/{id}', 'metodo'=> 'get']
         ]],
         ['id'=> 25, 'nome'=> 'Cadastrar Lojas', 'model'=> 'Loja', 'rotas'=> [
            ['id'=> 31, 'caminho'=> '/loja', 'metodo'=> 'post']
         ]],
         ['id'=> 26, 'nome'=> 'Editar Lojas', 'model'=> 'Loja', 'rotas'=> [
            ['id'=> 32, 'caminho'=> '/loja', 'metodo'=> 'put']
         ]],
         ['id'=> 27, 'nome'=> 'Remover Lojas', 'model'=> 'Loja', 'rotas'=> [
            ['id'=> 33, 'caminho'=> '/loja', 'metodo'=> 'delete']
         ]],

         ['id'=> 28, 'nome'=> 'Visualizar Questionamentos', 'model'=> 'Questionamento', 'rotas'=> [
            ['id'=> 34, 'caminho'=> '/questionamento', 'metodo'=> 'get'],
            ['id'=> 35, 'caminho'=> '/questionamento/{id}', 'metodo'=> 'get']
         ]],
         ['id'=> 29, 'nome'=> 'Cadastrar Questionamentos', 'model'=> 'Questionamento', 'rotas'=> [
            ['id'=> 36, 'caminho'=> '/questionamento', 'metodo'=> 'post']
         ]],
         ['id'=> 30, 'nome'=> 'Editar Questionamentos', 'model'=> 'Questionamento', 'rotas'=> [
            ['id'=> 37, 'caminho'=> '/questionamento', 'metodo'=> 'put']
         ]],
         ['id'=> 31, 'nome'=> 'Remover Questionamentos', 'model'=> 'Questionamento', 'rotas'=> [
            ['id'=> 38, 'caminho'=> '/questionamento', 'metodo'=> 'delete']
         ]],

         ['id'=> 32, 'nome'=> 'Visualizar Colaboradores', 'model'=> 'Colaborador', 'rotas'=> [
            ['id'=> 39, 'caminho'=> '/colaborador', 'metodo'=> 'get'],
            ['id'=> 40, 'caminho'=> '/colaborador/{id}', 'metodo'=> 'get']
         ]],
         ['id'=> 33, 'nome'=> 'Cadastrar Colaboradores', 'model'=> 'Colaborador', 'rotas'=> [
            ['id'=> 41, 'caminho'=> '/colaborador', 'metodo'=> 'post']
         ]],
         ['id'=> 34, 'nome'=> 'Editar Colaboradores', 'model'=> 'Colaborador', 'rotas'=> [
            ['id'=> 42, 'caminho'=> '/colaborador', 'metodo'=> 'put']
         ]],
         ['id'=> 35, 'nome'=> 'Remover Colaboradores', 'model'=> 'Colaborador', 'rotas'=> [
            ['id'=> 43, 'caminho'=> '/colaborador', 'metodo'=> 'delete']
         ]],

         ['id'=> 36, 'nome'=> 'Visualizar Usuários', 'model'=> 'Usuario', 'rotas'=> [
            ['id'=> 44, 'caminho'=> '/usuario', 'metodo'=> 'get'],
            ['id'=> 45, 'caminho'=> '/usuario/{id}', 'metodo'=> 'get']
         ]],
         ['id'=> 37, 'nome'=> 'Cadastrar Usuários', 'model'=> 'Usuario', 'rotas'=> [
            ['id'=> 46, 'caminho'=> '/usuario', 'metodo'=> 'post']
         ]],
         ['id'=> 38, 'nome'=> 'Editar Usuários', 'model'=> 'Usuario', 'rotas'=> [
            ['id'=> 47, 'caminho'=> '/usuario', 'metodo'=> 'put']
         ]],
         ['id'=> 39, 'nome'=> 'Remover Usuários', 'model'=> 'Usuario', 'rotas'=> [
            ['id'=> 48, 'caminho'=> '/usuario', 'metodo'=> 'delete']
         ]],

         ['id'=> 40, 'nome'=> 'Visualizar Grupos de Usuário', 'model'=> 'GrupoDeUsuario', 'rotas'=> [
            ['id'=> 49, 'caminho'=> '/grupo-usuario', 'metodo'=> 'get'],
            ['id'=> 50, 'caminho'=> '/grupo-usuario/{id}', 'metodo'=> 'get']
         ]],
         ['id'=> 41, 'nome'=> 'Cadastrar Grupos de Usuário', 'model'=> 'GrupoDeUsuario', 'rotas'=> [
            ['id'=> 51, 'caminho'=> '/grupo-usuario', 'metodo'=> 'post']
         ]],
         ['id'=> 42, 'nome'=> 'Editar Grupos de Usuário', 'model'=> 'GrupoDeUsuario', 'rotas'=> [
            ['id'=> 52, 'caminho'=> '/grupo-usuario', 'metodo'=> 'put']
         ]],
         ['id'=> 43, 'nome'=> 'Remover Grupos de Usuário', 'model'=> 'GrupoDeUsuario', 'rotas'=> [
            ['id'=> 53, 'caminho'=> '/grupo-usuario', 'metodo'=> 'delete']
         ]],

         ['id'=> 44, 'nome'=> 'Configurar Acessos', 'model'=> 'Acesso', 'rotas'=> [
            ['id'=> 54, 'caminho'=> '/acesso', 'metodo'=> 'get'],
            ['id'=> 55, 'caminho'=> '/acesso/{acessanteTipo}/{acessanteId}', 'metodo'=> 'get'],
            ['id'=> 56, 'caminho'=> '/acesso', 'metodo'=> 'post'],
            ['id'=> 57, 'caminho'=> '/acesso/{recursoId}/{acessanteTipo}/{acessanteId}', 'metodo'=> 'delete']
         ]],
      ];
   }
}
?>