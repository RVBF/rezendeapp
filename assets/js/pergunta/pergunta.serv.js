/**
 *  Pergunta.serv.js
 *
 *  @author	Rafael Vinicius Barros Ferreira
 */
 (function(app, $)
 {
	'use strict';

	function Pergunta(id, pergunta, anexos) {
		this.id = id  || 0;
		this.pergunta = pergunta  || '';
        this.anexos = anexos  || [];
	};

	function ServicoPergunta() { // Model
		var _this = this;
		// Rota no servidor
        _this.rota = function rota(idTarefa) {
			return app.api + '/tarefa/' + idTarefa  + '/pergunta';
		};

		// Cria um objeto de Pergunta
		this.criar = function criar(id, pergunta, anexos){
 			return {
                id : id  || 0,
                pergunta : pergunta  || '',
                anexos : anexos  || []
			};
		};

		_this.adicionar = function adicionar(obj, idTarefa) {
			return $.ajax({
				type: "POST",
				url: _this.rota(idTarefa),
				data: obj
			});
		};

		_this.adicionarTodas = function adicionarTodas(objs, idTarefa) {
			return $.ajax({
				type: "POST",
				dataType: "json",
				url: _this.rota(idTarefa) + '/cadastrar-varias',
				data: objs
			});
		};


		_this.todos = function todos(idTarefa) {
			return $.ajax({
				type : "GET",
				url: _this.rota(idTarefa)
			});
		};

		_this.atualizar = function atualizar(obj, idTarefa) {
			return $.ajax({
				type: "PUT",
				url: _this.rota(idTarefa),
				data: obj
			});
		};

		_this.remover = function remover(id, idCheklist) {
			return $.ajax({
				type: "DELETE",
				url: _this.rota(idCheklist) + '/' + id
			});
		};

		_this.comId = function comId(id, idTarefa) {
			return $.ajax({
				type: "GET",
				url: _this.rota(idTarefa) + '/' + id
			});
		};

		_this.comTarefaId = function comTarefaId(idTarefa){
			return $.ajax({
				type : "GET",
				url: _this.rota(idTarefa) + '/tarefa-com-id'
			});
		};
	}; // ServicoPergunta

	// Registrando
	app.Pergunta = Pergunta;
	app.ServicoPergunta = ServicoPergunta;

})(app, $);