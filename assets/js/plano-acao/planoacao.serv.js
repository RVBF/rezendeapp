/**
 *  planoAcao.serv.js
 *
 *  @author	Rafael Vinicius Barros Ferreira
 */
 (function(app, $) {
	'use strict';

	function PlanoAcao ( 
        id,
        descricao,
        dataLimite,
		solucao,
		responsavel,
		unidade,
		resposta,
		anexos,
        dataCadastro,
		dataExecucao
    ){
        this.id = id || 0;
        this.descricao = descricao || '';
        this.dataLimite = dataLimite || '';
        this.solucao = solucao || '';
		this.resposta = resposta || '';
		this.anexos = anexos || [],
		this.responsavel = responsavel || 0;
		this.unidade = unidade || 0
        this.dataCadastro = dataCadastro || '';
        this.dataExecucao = dataExecucao || '';
    };

	function ServicoPlanoAcao() { // Model
		var _this = this;
		// Rota no servidor
        _this.rota = function rota() {
			return app.api + '/plano-acao';
		};

		// Cria um objeto de Checklist
		this.criar = function criar(        
            id,
            descricao,
            dataLimite,
			solucao,
			responsavel,
			unidade,
			resposta,
			anexos,
            dataCadastro,
            dataExecucao
        ) {
			return {
                id : id || 0,
                descricao : descricao || '',
                dataLimite : dataLimite || '',
				solucao : solucao || '',
				responsavel : responsavel || '',
				unidade : unidade || '',
				resposta : resposta || '',
				anexos : anexos || [],
                dataCadastro : dataCadastro || '',
                dataExecucao : dataExecucao || ''
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

		_this.PlanoAcaosComID = function PlanoAcaosComID(id){
			return $.ajax({
				type: "GET",
				url: _this.rota() + '/plano-acao' + id
			});
		};

		_this.confirmarResponsabilidade = function confirmarResponsabilidade(id){
			return $.ajax({
				type: "POST",
				url: _this.rota() + '/confirmar-responsabilidade',
				data: { 'id' : id}
			});
		};

		_this.executar = function executar(obj){
			return $.ajax({
				type: "POST",
				url: _this.rota() + '/executar',
				data: obj
			});
		}

		_this.paPendentes = function paPendentes(id){
			return _this.rota() + '/pendentes/' + id;
		}
	}; // ServicoPlanoAcao

	// Registrando
	app.PlanoAcao = PlanoAcao;
	app.ServicoPlanoAcao = ServicoPlanoAcao;

})(app, $);