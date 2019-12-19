/**
 *  setor.serv.js
 *
 *  @author	Rafael Vinicius Barros Ferreira
 */
 (function(app, $)
 {
	'use strict';

	function Loja(id, razaoSocial, nomeFantasia, endereco) {
		this.id = id  || 0;
        this.razaoSocial = razaoSocial  || '';
		this.nomeFantasia = nomeFantasia  || '';
		this.endereco = endereco || '';
	};

	function ServicoLoja() { // Model
		var _this = this;
		// Rota no servidor
        _this.rota = function rota() {
			return app.api + '/loja';
		};

		// Cria um objeto de categoria
		this.criar = function criar(id, razaoSocial, nomeFantasia, endereco) {
 			return {
                id : id  || 0,
                razaoSocial : razaoSocial  || '',
				nomeFantasia : nomeFantasia  || '',
				endereco : endereco || ''
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
	app.Loja = Loja;
	app.ServicoLoja = ServicoLoja;
})(app, $);