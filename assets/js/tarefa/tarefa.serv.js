/**
 *  Tarefa.serv.js
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
        _this.rota = function rota(idCheklist) {
			return app.api + '/checklist/' + idCheklist  + '/tarefa';
		};

		// Cria um objeto de Tarefa
		this.criar = function criar(id, titulo) {
 			return {
				id : id  || 0,
				titulo : titulo  || ''
			};
		};

		_this.adicionar = function adicionar(obj, idChecklist) {
			return $.ajax({
				type: "POST",
				url: _this.rota(idChecklist),
				data: obj
			});
		};

		_this.todos = function todos(idChecklist) {
			return $.ajax({
				type : "GET",
				url: _this.rota(idChecklist)
			});
		};

		_this.atualizar = function atualizar(obj, idChecklist)
		{
			return $.ajax({
				type: "PUT",
				url: _this.rota(idChecklist),
				data: obj
			});
		};

		_this.remover = function remover(id, idCheklist) {
			return $.ajax({
				type: "DELETE",
				url: _this.rota(idCheklist) + '/' + id
			});
		};

		_this.comId = function comId(id, idChecklist) {
			return $.ajax({
				type: "GET",
				url: _this.rota(idChecklist) + '/' + id
			});
		};
	}; // ServicoTarefa

	// Registrando
	app.Tarefa = Tarefa;
	app.ServicoTarefa = ServicoTarefa;

})(app, $);