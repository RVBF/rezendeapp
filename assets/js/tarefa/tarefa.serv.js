/**
 *  Tarefa.serv.js
 *
 *  @author	Rafael Vinicius Barros Ferreira
 */
 (function(app, $)
 {
	'use strict';

	function Tarefa(id, titulo, descricao, dataLimite, setor, loja) {
		this.id = id  || 0;
		this.titulo = titulo  || '';
		this.descricao = descricao  || '';
		this.dataLimite = dataLimite || '';
		this.setor = setor  || 0;
		this.loja  = loja || 0;
	};

	function ServicoTarefa() { // Model
		var _this = this;
		// Rota no servidor
        _this.rota = function rota(idCheklist) {
			return app.api + '/setor/' + idCheklist  + '/tarefa';
		};

		// Cria um objeto de Tarefa
		this.criar = function criar(id, titulo, descricao, dataLimite, setor, loja) {
		 
			return {
				id : id  || 0,
				titulo : titulo  || '',
				descricao : descricao || '',
				dataLimite : dataLimite || '',
				setor : setor || 0,
				loja : loja || 0

			};
		};

		_this.adicionarComSetorId = function adicionarComSetorId(obj, idSetor) {
			return $.ajax({
				type: "POST",
				url: _this.rota(idSetor),
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

		_this.todos = function todos(idSetor) {
			return $.ajax({
				type : "GET",
				url: _this.rota(idSetor)
			});
		};

		_this.atualizarComSetorId = function atualizarComSetorId(obj, idSetor) {
			return $.ajax({
				type: "PUT",
				url: _this.rota(idSetor),
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

		_this.comId = function comId(id, idSetor) {
			return $.ajax({
				type: "GET",
				url: _this.rota(idSetor) + '/' + id
			});
		};
	}; // ServicoTarefa

	// Registrando
	app.Tarefa = Tarefa;
	app.ServicoTarefa = ServicoTarefa;

})(app, $);