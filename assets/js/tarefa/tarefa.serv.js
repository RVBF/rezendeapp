/**
 *  Tarefa.serv.js
 *
 *  @author	Rafael Vinicius Barros Ferreira
 */
 (function(app, $)
 {
	'use strict';

	function Tarefa(id, titulo, descricao, checklist) {
		this.id = id  || 0;
		this.titulo = titulo  || '';
		this.descricao = descricao  || '';
		this.checklist = checklist  || 0;
	};

	function ServicoTarefa() { // Model
		var _this = this;
		// Rota no servidor
        _this.rota = function rota(idCheklist) {
			return app.api + '/checklist/' + idCheklist  + '/tarefa';
		};

		// Cria um objeto de Tarefa
		this.criar = function criar(id, titulo, descricao, checklist) {
		 
			return {
				id : id  || 0,
				titulo : titulo  || '',
				descricao : descricao || '',
				checklist : checklist || ''

			};
		};

		_this.adicionarComChecklistId = function adicionarComChecklistId(obj, idChecklist) {
			return $.ajax({
				type: "POST",
				url: _this.rota(idChecklist),
				data: obj
			});
		};

		_this.adcionar = function adcionar(obj) {
			return $.ajax({
				type: "POST",
				url: _this.rota(),
				data: obj
			});
		};

		_this.todos = function todos(idChecklist) {
			return $.ajax({
				type : "GET",
				url: _this.rota(idChecklist)
			});
		};

		_this.atualizarComChecklistId = function atualizarComChecklistId(obj, idChecklist) {
			return $.ajax({
				type: "PUT",
				url: _this.rota(idChecklist),
				data: obj
			});
		};

		_this.atualizar = function atualizar(obj) {
			return $.ajax({
				type: "PUT",
				url: _this.rota(),
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