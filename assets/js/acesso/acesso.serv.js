/**
 *  pendencia.serv.js
 *
 *  @author	Rafael Vinicius Barros Ferreira
 */
(function (app, $) {
   'use strict';

   function Acesso(id, acao, recursoId, usuarioId) {
      this.id = id || 0;
      this.acao = acao || 'Negar';
      this.recursoId = recursoId || 0;
      this.usuarioId = usuarioId || 0;
   };

   function ServicoAcesso() { // Model
      var _this = this;
      // Rota no servidor
      _this.rota = function rota() {
         return app.api + '/acesso';
      };

      // Cria um objeto de Checklist
      this.criar = function criar(id, acao, recursoId, usuarioId) {
         return {
            id: id || 0,
            acao: acao || 'Negar',
            recursoId: recursoId || 0,
            usuarioId: usuarioId || 0
         };
      };

      _this.adicionar = function adicionar(obj) {
         return $.ajax({
            type: "POST",
            url: _this.rota(),
            data: obj
         });
      };

      _this.todos = function todos() {
         return $.ajax({
            type: "GET",
            url: _this.rota()
         });
      };

      _this.atualizar = function atualizar(obj) {
         return $.ajax({
            type: "PUT",
            url: _this.rota(),
            data: obj
         });
      };

      _this.remover = function remover(id) {
         return $.ajax({
            type: "DELETE",
            url: _this.rota() + '/' + id
         });
      };

      _this.comId = function comId(id) {
         return $.ajax({
            type: "GET",
            url: _this.rota() + '/' + id
         });
      };
   }; // ServicoAcesso

   // Registrando
   app.Acesso = Acesso;
   app.ServicoAcesso = ServicoAcesso;

})(app, $);