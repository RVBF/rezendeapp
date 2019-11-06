/**
 *  Checklist.serv.js
 *
 *  @author	Rafael Vinicius Barros Ferreira
 */
 (function(app, $)
 {
	'use strict';

	function Checklist(id, titulo, descricao, tipoChecklist, dataLimite, responsavel, setor, loja, questionarios) {
		this.id = id  || 0;
		this.titulo = titulo  || '';
		this.descricao = descricao  || '';
		this.tipoChecklist = tipoChecklist || '';
		this.dataLimite = dataLimite || '';
		this.responsavel = responsavel  || 0;
		this.setor = setor  || 0;
		this.loja  = loja || 0;
		this.questionarios  = questionarios || 0;
	};

	function ServicoChecklist() { // Model
		var _this = this;
		// Rota no servidor
        _this.rota = function rota() {
			return app.api + '/checklist';
		};

		// Cria um objeto de Checklist
		this.criar = function criar(id, titulo, descricao, tipoChecklist, dataLimite, responsavel, setor, loja, questionarios) {
			return {
				id : id  || 0,
				titulo : titulo  || '',
				descricao : descricao || '',
				tipoChecklist : tipoChecklist || '',
				dataLimite : dataLimite || '',
				responsavel : responsavel || 0,
				setor : setor || 0,
				loja : loja || 0,
				questionarios : questionarios || 0

			};
		};

		_this.todos = function todos() {
			return $.ajax({
				type: "GET",
				url: _this.rota()
			});
		}

		_this.adicionarComSetorId = function adicionarComSetorId(obj, idSetor) {
			return $.ajax({
				type: "POST",
				url: _this.rota(idSetor),
				data: obj
			});
		};

		_this.adicionar = function adicionar(obj) {
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

		_this.getQuestionamentosParaExecucao = function getQuestionamentosParaExecucao(id){
			return $.ajax({
				type: "GET",
				url: _this.rota() + '/questionamentos/' + id
			});
		};
	}; // ServicoChecklist

	// Registrando
	app.Checklist = Checklist;
	app.ServicoChecklist = ServicoChecklist;

})(app, $);