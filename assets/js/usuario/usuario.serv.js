/**
 *  usuario.serv.js
 *
 *  @author	Rafael Vinicius Barros Ferreira
 */
 (function(app, $)
 {
	'use strict';

	function Usuario(id = 0, nome = '', sobrenome = '', email = '', login = '', senha = '', lojas = []) {
		this.id = id  || 0;
        this.nome = nome || '';
        this.login = login  || 0;
		this.senha = senha || '';
		this.lojas =  lojas || [];
	};

	function ServicoUsuario() { // Model
		var _this = this;
		// Rota no servidor
        _this.rota = function rota() {
			return app.api + '/usuario';
		};

		// Cria um objeto de usuario
		this.criar = function criar(id = 0, nome = '', sobrenome = '', email = '', login = '', senha = '', lojas = []) {
			return {
				id : id  || 0,
				nome : nome || '',
				sobrenome : sobrenome || '',
				email : email || '',
				login : login  || '',
				senha : senha  || '',
				lojas : lojas || []
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
	app.Usuario = Usuario;
	app.ServicoUsuario = ServicoUsuario;
})(app, $);