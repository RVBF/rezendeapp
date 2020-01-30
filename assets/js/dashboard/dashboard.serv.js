/**
 *  dashboard.serv.js
 *
 *  @author	Rafael Vinicius Barros Ferreira
 */
 (function(app, $)
 {
	'use strict';

	function ServicoDasboard()
	{ // Model
		var _this = this;

		// Cria um objeto de Endereco
		this.criar = function criar(dados)
		{
 			return {
				id : id  || undefined,
				dados : dados || ''
			};
		};

		_this.rota = function rota()
		{
			return app.API + '/dashboard';
		};

	
	}; // ServicoDashboard

	// Registrando
	app.ServicoDashboard = ServicoDashboard;

})(app, $);