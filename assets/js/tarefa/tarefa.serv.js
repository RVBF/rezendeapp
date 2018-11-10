/**
 *  tarefa.serv.js
 *
 *  @author	Rafael Vinicius Barros Ferreira
 */
(function(app, $)
{
   'use strict';

   function Tarefa(id, titulo) {
       this.id = id  || 0;
       this.titulo = titulo  || '';
   };

   function ServicoTarefa() { // Model
       var _this = this;
       // Rota no servidor
       _this.rota = function rota() {
           return app.api + '/tarefas';
       };

       // Cria um objeto de categoria
       this.criar = function criar(id, titulo) {
            return {
               id : id  || 0,
               titulo : titulo  || ''
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
               type : "GET",
               url: _this.rota()
           });
       };

       _this.atualizar = function atualizar(obj)
       {
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
   app.Tarefa = Tarefa;
   app.ServicoTarefa = ServicoTarefa;

})(app, $);