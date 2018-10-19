/**
 *  categoria.cfg.js
 *  
 *  @author	Rafael Vinicius Barros Ferreira
 */
(function(app){
	'use strict';

	$(document ).ready(function()
	{
		var servico = new app.ServicoCategoria();

		var controladoraForm = new app.ControladoraFormCategoria(servico);
		
		controladoraForm.configurar();
	}); // ready
})(app);