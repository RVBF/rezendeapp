/**
 *  logout.js
 *
 *  @author	Rafael Vinicius Barros Ferreira
 */
(function(window, app, $, toastr) {
	'use strict';

	function ServicoLogout() { // Model

		var _this = this;

		// Rota no servidor
		_this.rota = function rota() {
			return app.api + '/logout';
		};

		_this.sair = function sair()
		{
			return $.ajax({
				type: "post",
				url:_this.rota()
			});
		};

	};
	
	app.ServicoLogout = ServicoLogout;

})(window, app, jQuery, toastr);
