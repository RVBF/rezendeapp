/**
 *  pendencia.serv.js
 *
 *  @author	Rafael Vinicius Barros Ferreira
 */
(function(app, $) {
	'use strict';

	function Pendencia ( 
        id,
        descricao,
        dataLimite,
		solucao,
		descricaoExecucao,
		responsavel,
		unidade,
        dataCadastro,
		dataExecucao,
		anexos
    ){
        this.id = id || 0;
        this.descricao = descricao || '';
        this.dataLimite = dataLimite || '';
		this.solucao = solucao || '';
		this.descricaoExecucao = descricaoExecucao || '';
		this.responsavel  = responsavel || 0;
		this.unidade = unidade || 0;
        this.dataCadastro = dataCadastro || '';
		this.dataExecucao = dataExecucao || '';
		this.anexos = anexos || '';
    };

	function ServicoPendencia() { // Model
		var _this = this;
		// Rota no servidor
        _this.rota = function rota() {
			return app.api + '/pendencia';
		};

		// Cria um objeto de Checklist
		this.criar = function criar(        
            id,
            descricao,
            dataLimite,
			solucao,
			descricaoExecucao,
			responsavel,
			unidade,
            dataCadastro,
			dataExecucao,
			anexos
        ) {
			return {
                id : id || 0,
                descricao : descricao || '',
                dataLimite : dataLimite || '',
				solucao : solucao || '',
				descricaoExecucao : descricaoExecucao || '',
				responsavel : responsavel ||  0,
				unidade : unidade || 0,
                dataCadastro : dataCadastro || '',
				dataExecucao : dataExecucao || '',
				anexos : anexos || ''
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

		_this.comId = function comId(id) {
			return $.ajax({
				type: "GET",
				url: _this.rota() + '/' + id
			});
		};

		_this.pendenciasComID = function pendenciasComID(id){
			return $.ajax({
				type: "GET",
				url: _this.rota() + '/pendencia' + id
			});
		};

		_this.pePendentes = function pePendentes(id){
			return _this.rota() + '/pendentes/' + id;
		}

		_this.executar = function executar(obj){
			return $.ajax({
				type: "post",
				url: _this.rota() + '/executar',
				data: obj
			});
		}
	}; // Servicopendencia

	// Registrando
	app.Pendencia = Pendencia;
	app.ServicoPendencia = ServicoPendencia;

})(app, $);