/**
 *  grupousuario.serv.js
 *
 *  @author	Rafael Vinicius Barros Ferreira
 */
 (function(app, $)
 {
	'use strict';

	function GrupoUsuario(id, nome, descricao) {
		this.id = id  || 0;
        this.nome = nome  || '';
        this.descricao = descricao  || '';
	};

	function ServicoGrupoUsuario() { // Model
		var _this = this;
		// Rota no servidor
        _this.rota = function rota() {
			return app.api + '/grupo-usuario';
		};

		// Cria um objeto de categoria
		this.criar = function criar(id, nome, descricao) {
 			return {
                id : id  || 0,
                nome : nome  || '',
                descricao : descricao  || ''
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
	}; // ServicoGrupoUsuario

	// Registrando
	app.GrupoUsuario = GrupoUsuario;
	app.ServicoGrupoUsuario = ServicoGrupoUsuario;
})(app, $);