/**
 *  questionamento.serv.js
 *
 *  @author	Rafael Vinicius Barros Ferreira
 */
 (function(app, $)
 {
	'use strict';

	function Questionamento ( 
        id,
        status,
        formularioPergunta,
        formularioResposta,
        checklist,
		planoAcao,
		pendencia, 
        anexos
    ){
        this.id = id || 0,
        this.status = status ||  '',
        this.formularioPergunta = formularioPergunta || '',
        this.formularioResposta = formularioResposta || '',
        this.checklist = checklist || null,
		this.planoAcao = planoAcao || null,
		this.pendencia = pendencia || null,
        this.anexos = anexos || []
    };

	function ServicoQuestionamento() { // Model
		var _this = this;
		// Rota no servidor
        _this.rota = function rota() {
			return app.api + '/questionamento';
		};

		// Cria um objeto de Checklist
		this.criar = function criar(        
            id,
            status,
            formularioPergunta,
            formularioResposta,
            checklist,
			planoAcao,
			pendencia,
            anexos
        ) {
			return {
			    id : id || 0,
                status : status ||  '',
                formularioPergunta : formularioPergunta || '',
                formularioResposta : formularioResposta || '',
                checklist : checklist || null,
				planoAcao : planoAcao || null,
				pendencia : pendencia || null,
                anexos : anexos || []
			};
		};

		_this.adicionar = function adicionar(obj) {
			return $.ajax({
				type: "POST",
				url: _this.rota(),
				data: obj
			});
		};

		_this.todos = function todos(id) {
			return _this.rota() + '/' + id;
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


		_this.remover = function remover(id ) {
			return $.ajax({
				type: "DELETE",
				url: _this.rota() + '/' + id
			});
		};

		_this.comId = function comId(id, ) {
			return $.ajax({
				type: "GET",
				url: _this.rota() + '/' + id
			});
		};

		_this.getQuestionamentosParaExecucao = function getQuestionamentosParaExecucao(id){
			return $.ajax({
				type: "GET",
				url: _this.rota() + '/questionamentos/' + id
			});
		};
	}; // ServicoQuestionamento

	// Registrando
	app.Questionamento = Questionamento;
	app.ServicoQuestionamento = ServicoQuestionamento;

})(app, $);