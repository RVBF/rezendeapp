/**
 *  resposta.serv.js
 *
 *  @author	Rafael Vinicius Barros Ferreira
 */
 (function(app, $)
 {
	'use strict';

	function Resposta(id = 0, opcaoSelecionada = 0, files = [], comentario, pergunta = null) {
		this.id = id  || 0;
        this.opcaoSelecionada = opcaoSelecionada  || 0;
		this.files = files || [];
		this.comentario = comentario;
		this.pergunta = pergunta;
	};

	function ServicoResposta() { // Model
		var _this = this;
		// Rota no servidor
        _this.rota = function rota() {
			return app.api + '/resposta';
		};

		// Cria um objeto de categoria
		this.criar = function criar(id = 0,  opcaoSelecionada = 0, files = [], comentario, pergunta = null) {
 			return {
                id : id  || 0,
                opcaoSelecionada : opcaoSelecionada  || 0,
				files : files  || [],
				comentario : comentario || '',
				pergunta : pergunta || null             
			};
		};

		_this.adicionar = function adicionar(obj) {
			return $.ajax({
				type: "POST",
				url: _this.rota(),
				data: {obj},
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
	app.Resposta = Resposta;
	app.ServicoResposta = ServicoResposta;
})(app, $);