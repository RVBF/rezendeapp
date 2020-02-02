/**
 *  usuario.serv.js
 *
 *  @author	Rafael Vinicius Barros Ferreira
 */
(function (app, $) {
   'use strict';

   function Usuario(id = 0, login = '', senha = '') {
      this.id = id || 0;
      this.login = login || 0;
      this.senha = senha || '';
   };

   function ServicoUsuario() { // Model
      var _this = this;
      // Rota no servidor
      _this.rota = function rota() {
         return app.api + '/usuario';
      };

      // Cria um objeto de usuario
      _this.criar = function criar(id = 0, login = '', senha = '') {
         return {
            id: id || 0,
            login: login || '',
            senha: senha || ''
         };
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

      _this.comId = function comId(id) {
         return $.ajax({
            type: "GET",
            url: _this.rota() + '/' + id
         });
      };

      _this.atualizarSenha = function atualizarSenha(senha, novaSenha, confirmacaoSenha) {
         return $.ajax({
            type: "PUT",
            url: _this.rota() + '/atualizar-senha',
            data: { senha: senha, novaSenha: novaSenha, confirmacaoSenha: confirmacaoSenha }
         });
      }
   }; // ServicoCategoria

   // Registrando
   app.Usuario = Usuario;
   app.ServicoUsuario = ServicoUsuario;
})(app, $);