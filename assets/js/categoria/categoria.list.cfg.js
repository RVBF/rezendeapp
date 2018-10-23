/**
 *  categoria.list.cfg.js
 *
 *  @author	Rafael Vinicius Barros Ferreira
 */
(function(app, $, document, window)
{
	'use strict';
	$(document).ready(function()
	{
		var servicoCategoria = new app.ServicoCategoria();

		var controladoraCategoria = new app.ControladoraListagemCategoria(servicoCategoria);
		controladoraCategoria.configurar();
	}); // ready
})(app, jQuery, document, window);