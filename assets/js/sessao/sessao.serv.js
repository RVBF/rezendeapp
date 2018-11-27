/**
 *  sessao.serv.js
 *  
 *  @author	Rafael Vinicius Barros Ferreira
 */

(function(window, app, $)
 {
	'use strict';
	
	function Sessao() { 
	
		var _this = this;
		
		_this.adicionarUsuarioSessao = function adicionarUsuarioSessao(usuario) {
			window.sessionStorage.setItem('usuario', usuario);
		}

		_this.getSessao = function getSessao() {
			return window.sessionStorage.getItem('usuario');
		}

		_this.limparSessionStorage = function limparSessionStorage() {
			window.sessionStorage.clear();
		};

		// Rota no servidor
		_this.rota = function rota() {
			return app.api + '/sessao';
		};
		
		_this.verificarSessao = function verificarSessao() {
			return $.ajax({
				type: "POST",
				url: _this.rota()+'/verificar-sessao'
			} );
		};
	}; // ServicoLogin
	
	// Registrando
	app.Sessao = Sessao;

})(window, app, jQuery);
