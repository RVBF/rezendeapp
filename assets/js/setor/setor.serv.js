/**
 *  setor.serv.js
 *
 *  @author	Rafael Vinicius Barros Ferreira
 */
(function (app, $) {
   'use strict';

   function Setor(id, titulo, descricao, categoria) {
      this.id = id || 0;
      this.titulo = titulo || '';
      this.descricao = descricao || '';
      this.categoria = categoria || undefined;
   };

   function ServicoSetor() { // Model
      var _this = this;
      // Rota no servidor
      _this.rota = function rota() {
         return app.api + '/setor';
      };

      // Cria um objeto de categoria
      this.criar = function criar(id, titulo, descricao, categoria) {
         return {
            id: id || 0,
            titulo: titulo || '',
            descricao: descricao || '',
            categoria: categoria || null
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

      _this.todosOpcoes = function todos() {
         return $.ajax({
            type: "GET",
            url: _this.rota() + '/opcoes'
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
   }; // ServicoCategoria

   // Registrando
   app.Setor = Setor;
   app.ServicoSetor = ServicoSetor;
})(app, $);