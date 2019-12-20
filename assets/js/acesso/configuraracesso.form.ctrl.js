/**
 *  PlanoAcao.form.ctrl.js
 *
 *  @author  Rafael Vinicius Barros Ferreira
 *	 @version 1.0
 */
(function (window, app, $, toastr) {
   'use strict';

   function ControladoraFormConfigurarAcesso(servicoAcesso) {
      var _this = this;

      _this.alterar = false;
      _this.arvoreDeAcessos = $('#arvoreDeAcessos');
      _this.botaoSubmissao = $('#pronto');
      _this.acessos = null;
      _this.recursos = null;

      _this.obterAcessos = function obterAcessos() {
         // servicoAcesso.todos().done(_this.desenhar);

         _this.acessos = [
            { id: 1, recursoId: 1, acao: 'Permitir', usuarioId: 1 },
            { id: 2, recursoId: 2, acao: 'Permitir', usuarioId: 1 },
            { id: 3, recursoId: 3, acao: 'Permitir', usuarioId: 1 },
            { id: 4, recursoId: 6, acao: 'Permitir', usuarioId: 1 },
            { id: 5, recursoId: 7, acao: 'Permitir', usuarioId: 1 },
            { id: 6, recursoId: 11, acao: 'Permitir', usuarioId: 1 },
            { id: 7, recursoId: 12, acao: 'Permitir', usuarioId: 1 },
            { id: 8, recursoId: 15, acao: 'Permitir', usuarioId: 1 },
         ];

         return _this.acessos;
      }

      _this.obterRecursos = function obterRecursos() {
         // servicoAcesso.todos().done(_this.desenhar);

         _this.recursos = [
            { id: 1, nome: 'Visualizar Checklists', model: 'Checklist' },
            { id: 2, nome: 'Cadastrar Checklists', model: 'Checklist' },
            { id: 3, nome: 'Editar Checklists', model: 'Checklist' },
            { id: 4, nome: 'Executar Checklists', model: 'Checklist' },
            { id: 5, nome: 'Remover Checklists', model: 'Checklist' },

            { id: 6, nome: 'Visualizar PAs', model: 'PA' },
            { id: 7, nome: 'Cadastrar PAs', model: 'PA' },
            { id: 8, nome: 'Editar PAs', model: 'PA' },
            { id: 9, nome: 'Executar PAs', model: 'PA' },
            { id: 10, nome: 'Remover PAs', model: 'PA' },

            { id: 11, nome: 'Visualizar PEs', model: 'PE' },
            { id: 12, nome: 'Cadastrar PEs', model: 'PE' },
            { id: 13, nome: 'Editar PEs', model: 'PE' },
            { id: 14, nome: 'Executar PEs', model: 'PE' },
            { id: 15, nome: 'Remover PEs', model: 'PE' },
         ];

         return _this.acessos;
      }

      // Cria a árvore de recursos
      _this.desenhar = function desenhar() {
         var modelsComRecursos = window.agruparObjetos(_this.recursos, 'model');
         var acessosPorRecursos = window.agruparObjetos(_this.acessos, 'recursoId');

         var nohs = [];

         $.each(modelsComRecursos, function (modelNome, recursos) {
            var nohsDeRecursos = [];

            $.each(recursos, function (indice, recurso) {
               nohsDeRecursos.push({
                  text: recurso.nome,
                  state: { selected: acessosPorRecursos[recurso.id] != undefined },
                  icon: false
               });
            });

            nohs.push({
               text: modelNome,
               state: { opened: true },
               children: nohsDeRecursos,
               icon: false
            });
         });

         _this.arvoreDeAcessos.jstree({
            plugins: ['checkbox'],
            core: {
               data: nohs,
               multiple: true,
               themes: {
                  variant: 'large'
               }
            }
         });
      };

      _this.salvar = function salvar() {
         _this.formulario.validate(criarOpcoesValidacao());
      };

      // Configura os eventos do formulário
      _this.configurar = function configurar() {
         _this.obterRecursos();
         _this.obterAcessos();

         var esperarRecursosEAcessos = window.setInterval(function () {
            if (_this.recursos != null && _this.acessos != null) {
               window.clearInterval(esperarRecursosEAcessos);

               _this.desenhar();
            }
         }, 50);
      };
   }; // ControladoraFormConfigurarAcesso

   // Registrando
   app.ControladoraFormConfigurarAcesso = ControladoraFormConfigurarAcesso;

})(window, app, jQuery, toastr);