/**
 *  dashboard.serv.js
 *
 *  @author	Rafael Vinicius Barros Ferreira
 */
 (function(app, $)
 {
	'use strict';

	function ServicoDashboard()
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
			return app.api + '/dashboard';
		};

		_this.contadores = function contadores(){
			// console.log(_this.rota())
			return $.ajax({
				type: "GET",
				url: _this.rota() + '/contadores',
			});
		}
	
	}; // ServicoDashboard

	// Registrando
	app.ServicoDashboard = ServicoDashboard;
})(app, $);