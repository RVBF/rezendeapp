/**
 *  setor.serv.js
 *
 *  @author	Rafael Vinicius Barros Ferreira
 */
 (function(app, $)
 {
	'use strict';

	function Questionario(id, titulo, descricao, tipoQuestionario, configuracao) {
		this.id = id  || 0;
		this.titulo = titulo  || '';
		this.descricao = descricao  || '';
		this.tipoQuestionario = tipoQuestionario || '';
		this.configuracao = configuracao || '';

	};

	function ServicoQuestionario() { // Model
		var _this = this;
		// Rota no servidor
        _this.rota = function rota() {
			return app.api + '/questionario';
		};

		// Cria um objeto de Setor
		this.criar = function criar(id, titulo, descricao, tipoQuestionario, configuracao) {
 			return {
                id : id  || 0,
                titulo : titulo  || '',
				descricao : descricao  || '',
				tipoQuestionario : tipoQuestionario || '',
				configuracao : configuracao || ''
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
	}; // ServicoQuestionario

	// Registrando
	app.Questionario = Questionario;
	app.ServicoQuestionario = ServicoQuestionario;
})(app, $);