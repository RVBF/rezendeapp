/**
 *  index.list.ctrl.js
 *
 *  @author	Rafael Vinicius Barros Ferreira
 */
(function (window, app, $, toastr) {
   'use strict';

   function ControladoraIndex(servicoIndex) {
      var _this = this;

      _this.renderizarOpcoesHTML = function renderizarOpcoesHTMLname() {
         var sucesso = function (resposta) {
         };

         var jqXHR = servicoIndex.temPermissao();
         jqXHR.done(sucesso);
      };

      _this.renderizarAtividadesUsuario = function renderizarAtividadesUsuario() {

         var sucesso = function (resposta) {
         };

         var jqXHR = servicoIndex.carregarListagemDeAtividades();
         jqXHR.done(sucesso);
      };

      _this.renderizarDadosUsuario = function renderizarDadosUsuario() {
         var sessao = new app.Sessao();
         if (sessao != null) {
            $('body').find('.nome_usuario').each(function () {
               $(this).empty().html(JSON.parse(sessao.getSessao()).nome);
            });

            $('body').find('.setor_usuario').each(function () {
               $(this).empty().html(JSON.parse(sessao.getSessao()).setor);
            });
         }
      }

      _this.configurar = function configurar() {
         // _this.renderizarOpcoesHTML();
         // _this.renderizarAtividadesUsuario();
         _this.renderizarDadosUsuario();
         router.trigger('navigate');
      };
   } // ControladoraIndex

   // Registrando
   app.ControladoraIndex = ControladoraIndex;
})(window, app, jQuery, toastr);