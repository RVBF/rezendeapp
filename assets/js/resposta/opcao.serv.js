/**
 *  opcao.serv.js
 *
 *  @author	Rafael Vinicius Barros Ferreira
 */
 (function(app, $)
 {
	'use strict';

	function Opcao() {
		this.excelente = 'Excelente';
		this.bom = 'Bom';
		this.ruim = 'Ruim';
		this.naoAplica = 'Não se Aplica';

		this.getpcoes = function(){
			return { 1 : this.excelente, 2 : this.bom, 3 : this.ruim, 4 : this.naoAplica };
		}
	};

	// Registrando
	app.Opcao = Opcao;
})(app, $);