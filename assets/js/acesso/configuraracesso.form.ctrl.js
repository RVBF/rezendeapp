/**
 *  configuraracesso.form.ctrl.js
 *
 *  @author Leonardo Carvalhães Bernardo
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

      _this.acessanteTipo = function () {
         var parametros = window.location.hash.split('/');

         return parametros[2];
      }();

      _this.acessanteId = function () {
         var parametros = window.location.hash.split('/');

         return parametros[3];
      }();

      _this.obterAcessos = function obterAcessos() {
         servicoAcesso.acessosDoAcessante(_this.acessanteTipo, _this.acessanteId).done(function (resposta) {
            _this.acessos = resposta.data;
         });
      }

      _this.obterRecursos = function obterRecursos() {
         servicoAcesso.todosOsRecursos().done(function (resposta) {
            _this.recursos = resposta.data;
         });
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
                  id: recurso.id,
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

         _this.arvoreDeAcessos.on('select_node.jstree', function (evento, dados) {
            console.log(dados);

            servicoAcesso.adicionar(servicoAcesso.criar(undefined, 'Permitir', dados.node.id, _this.acessanteTipo, _this.acessanteId)).done(function (resposta) {
               console.log(resposta);
            });
         }).jstree({
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