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

	function ControladoraLogout(servicoLogout) {
		var _this = this;

		// Redireciona para o login
		var irProLogin = function irProLogin() {
			router.navigate('/login');
		};

		_this.sair = function sair(event) {
			event.preventDefault();
			var sucesso = function sucesso(data, textStatus, jqXHR) {
                window.sucessoPadrao(data, textStatus, jqXHR);
                if(data.status) {
                    window.sessionStorage.clear();
                    irProLogin();
                }
			};

			var jqXHR = servicoLogout.sair();

            jqXHR.done(sucesso).fail(erro);
		}

		_this.configurar = function configurar() {
			$('.efetuar_logout').on('click', _this.sair);
		};
	}; // ControladoraLogout

	app.ServicoLogout = ServicoLogout;
	app.ControladoraLogout = ControladoraLogout;
})(window, app, jQuery, toastr);

$(document).ready(function() {
	var servico = new app.ServicoLogout();

	var controladoraLogout = new app.ControladoraLogout(servico);
	controladoraLogout.configurar();
});