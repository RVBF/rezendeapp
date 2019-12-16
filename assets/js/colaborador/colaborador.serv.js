/**
 *  colaborador.serv.js
 *
 *  @author	Rafael Vinicius Barros Ferreira
 */
 (function(app, $)
 {
	'use strict';

	function Colaborador(id = 0, nome = '', sobrenome = '', email = '', usuario = null ,lojas = [], setor =0, avatar ={}) {
		this.id = id  || 0;
        this.nome = nome || '';
        this.sobrenome = sobrenome || '';
        this.email = email || '';
        this.usuario = usuario || '';
		this.lojas =  lojas || [];
		this.setor =  setor || 0;
		this.avatar = avatar || '';
	};

	function ServicoColaborador() { // Model
		var _this = this;
		// Rota no servidor
        _this.rota = function rota() {
			return app.api + '/colaborador';
		};

		// Cria um objeto de colaborador
		this.criar = function criar(id = 0, nome = '', sobrenome = '', email = '', usuario = null, lojas = [], setor =0, avatar = {}) {
			return {
				id : id  || 0,
				nome : nome || '',
				sobrenome : sobrenome || '',
				email : email || '',
				usuario : usuario || '',
				lojas : lojas || [],
				setor : setor || 0,
				avatar : avatar || {}
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
	app.Colaborador = Colaborador;
	app.ServicoColaborador = ServicoColaborador;
})(app, $);