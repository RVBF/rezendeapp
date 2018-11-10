/**
 *  checklist.serv.js
 *
 *  @author	Rafael Vinicius Barros Ferreira
 */
 (function(app, $)
 {
	'use strict';

	function Checklist(id, descricao, dataLimite, categoria, loja) {
		this.id = id  || 0;
        this.descricao = descricao  || '';
        this.dataLimite = dataLimite  || '';
        this.categoria = categoria  || '';
        this.loja = loja  || '';
	};

	function ServicoChecklist() { // Model
		var _this = this;
		// Rota no servidor
        _this.rota = function rota() {
			return app.api + '/checklist';
		};

		// Cria um objeto de categoria
		this.criar = function criar(id, descricao, dataLimite, categoria, loja) {
 			return {
                id : id  || 0,
                descricao : descricao  || '',
                dataLimite : dataLimite  || '',
                categoria : categoria  || '',
                loja : loja  || ''
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
	app.Checklist = Checklist;
	app.ServicoChecklist = ServicoChecklist;
})(app, $);