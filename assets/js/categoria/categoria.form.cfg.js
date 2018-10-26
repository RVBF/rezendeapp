/**
 *  categoria.form.cfg.js
 *
 *  @author	Rafael
 */
(function(app, $, document, window)
{
	'use strict';
	$(document).ready(function()
	{
		var servicoCategoria = new app.ServicoCategoria();

		var controladoraFormCategoria = new app.ControladoraFormCategoria(servicoCategoria)

 		controladoraFormCategoria.configurar();
	}); // ready
})(app, jQuery, document, window);