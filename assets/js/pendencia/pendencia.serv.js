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
		responsavel,
        dataCadastro,
        dataExecucao
    ){
        this.id = id || 0;
        this.descricao = descricao || '';
        this.dataLimite = dataLimite || '';
		this.solucao = solucao || '';
		this.responsavel  = responsavel || 0;
        this.dataCadastro = dataCadastro || '';
        this.dataExecucao = dataExecucao || '';
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
			responsavel,
            dataCadastro,
            dataExecucao
        ) {
			return {
                id : id || 0,
                descricao : descricao || '',
                dataLimite : dataLimite || '',
				solucao : solucao || '',
				responsavel : responsavel ||  0,
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

		_this.pendenciasComID = function pendenciasComID(id){
			return $.ajax({
				type: "GET",
				url: _this.rota() + '/pendencia' + id
			});
		};
	}; // Servicopendencia

	// Registrando
	app.Pendencia = Pendencia;
	app.ServicoPendencia = ServicoPendencia;

})(app, $);