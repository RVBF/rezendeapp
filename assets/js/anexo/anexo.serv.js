/**
 *  anexo.serv.js
 *
 *  @author	Rafael Vinicius Barros Ferreira
 */
 (function(app, $)
 {
	'use strict';

	function Anexo(id = 0, patch = '', tipo = '', resposta = null) {
		this.id = id;
        this.patch = patch;
		this.tipo = tipo;
		this.resposta = resposta;
	};

	function ServicoAnexo() { // Model
		var _this = this;
		// Rota no servidor
        _this.rota = function rota() {
			return app.api + '/anexo';
		};

		// Cria um objeto de categoria
		this.criar = function criar(id = 0, patch = '', tipo = '', resposta = null) {
 			return {
                id : id  || 0,
                patch : patch  || '',
				tipo : tipo  || '',
				resposta : resposta || null
			};
        };
        
		_this.todos = function todos() {
			return $.ajax({
				type : "GET",
				url: _this.rota()
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
	app.Anexo = Anexo;
	app.ServicoAnexo = ServicoAnexo;
})(app, $);