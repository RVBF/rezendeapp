/**
 *  permissao.serv.js
 *
 *  @author	Rafael Vinicius Barros Ferreira
 */
 (function(app, $)
 {
	'use strict';

	function PermissaoAdministrativa(grupos = [], usuarios = []) {
		this.grupos = grupos  || [];
        this.usuarios = usuarios || [];
	};

	function ServicoPermissaoAdministrativa() { // Model
		var _this = this;
		// Rota no servidor
        _this.rota = function rota() {
			return app.api + '/permissoes';
		};

		// Cria um objeto de usuario
		this.criar = function criar(grupos = [], usuarios = []) {
			return {
				grupos : grupos  || 0,
				usuarios : usuarios || ''
			};
		};

		_this.configurarPermissoes = function configurarPermissoes(obj) {
			return $.ajax({
				type: "POST",
				url: _this.rota(),
				data: obj
			});
		};
	}; // ServicoCategoria

	// Registrando
	app.PermissaoAdministrativa = PermissaoAdministrativa;
	app.ServicoPermissaoAdministrativa = ServicoPermissaoAdministrativa;
})(app, $);