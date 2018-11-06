/**
 * includes.js
 *
 * @author	Rafael  Vinicius Barros Ferreira
 */

(function(window)
{
	'use strict';

	function Loader() {

		var _this = this;

		var createElement = function createElement(name) {
			return window.document.createElement(name );
		};

		var addToHead = function addToHead(element) {
			window.document.head.appendChild(element );
		};

		var addToBody = function addToBody(element) {
			window.document.body.appendChild(element);
		};

		/**
		 * Carrega um script no documento atual.
		 *
		 * @param string	src			Arquivo de script.
		 * @param boolean	isAsync		Se o carregamento é assíncrono.
		 * @param callable	onLoad		Método a ser executado quando carregar.
		 *								Opcional.
		 * @return element	O elemento de script criado.
		 */
		_this.script = function script(src, isAsync, onLoad, type) {
			var e = createElement('script' );

			e.src = src;
			e.async = isAsync === true;
			if (onLoad !== undefined)
				{ e.onload = onLoad; }
			e.type = type !== undefined ? type : 'text/javascript';

			addToHead(e );
		};

		_this.link = function link(href, rel, type) {
			var e = createElement('link' );
			e.href = href;
			if (rel !== undefined)
			{
				e.rel = rel;
			}
			if (type !== undefined)
			{
				e.type = type;
			}

			addToHead(e );
		};

		_this.css = function css(href) {
			return _this.link(href, 'stylesheet', 'text/css' );
		};

		_this.font = function font(href) {
			return _this.link(href, undefined, 'application/octet-stream' );
		};
	} // class

	// Registrando no window
	window.Loader = Loader;
})(window);


(function(window)
{
    'use strict';
    var dependenciasCSS = [];
	dependenciasCSS.push({ url :'assets/styles/estilo.css' });
	dependenciasCSS.push({ url :'assets/styles/meanmenu.min.css' });
	dependenciasCSS.push({ url :'assets/styles/animate.css' });
	dependenciasCSS.push({ url :'assets/styles/normalize.css' });
	dependenciasCSS.push({ url :'assets/styles/responsive.css' });

	dependenciasCSS.push({ url : 'https://fonts.googleapis.com/css?family=Roboto:100,300,400,700,900' });
	dependenciasCSS.push({ url : 'assets/styles/main.css' });
	dependenciasCSS.push({ url : 'vendor/font-awesome/web-fonts-with-css/css/fontawesome-all.css' });
	dependenciasCSS.push({ url : 'vendor/bootstrap/dist/css/bootstrap.css' });
	dependenciasCSS.push({ url :'assets/styles/bootstrap.css' });

	dependenciasCSS.push({ url : 'vendor/bootstrap/dist/css/bootstrap-reboot.css' });
	dependenciasCSS.push({ url : 'vendor/bootstrap/dist/css/bootstrap-grid.css' });
	dependenciasCSS.push({ url : 'vendor/datatables.net-dt/css/jquery.dataTables.min.css' });
	dependenciasCSS.push({ url : 'vendor/datatables/media/css/dataTables.bootstrap.css' });
	dependenciasCSS.push({ url : 'vendor/datatables/media/css/dataTables.bootstrap4.css' });
	dependenciasCSS.push({ url : 'vendor/datatables.net-dt/css/jquery.dataTables.css' });
	dependenciasCSS.push({ url : 'vendor/datatables.net-responsive-bs4/css/responsive.bootstrap4.css' });
	dependenciasCSS.push({ url : 'vendor/datatables.net-select-dt/css/select.dataTables.min.css' });
	dependenciasCSS.push({ url : 'vendor/bootstrap-dialog/dist/css/bootstrap-dialog.min.css' });
	dependenciasCSS.push({ url : 'vendor/toastr/toastr.min.css' });
	dependenciasCSS.push({ url : 'vendor/pickadate/lib/compressed/themes/default.css' });
	dependenciasCSS.push({ url : 'vendor/pickadate/lib/compressed/themes/default.time.css' });
	dependenciasCSS.push({ url : 'vendor/pickadate/lib/compressed/themes/default.date.css' });
	dependenciasCSS.push({ url : 'vendor/select2/dist/css/select2.min.css' });
	dependenciasCSS.push({ url : 'vendor/select2-bootstrap-theme/dist/select2-bootstrap.min.css'});
	dependenciasCSS.push({ url : 'vendor/select2-bootstrap4-theme/dist/select2-bootstrap4.min.css'});

	
	var loader = new window.Loader();

	dependenciasCSS.forEach(function(e, index, arr)
	{
		loader.css(e.url );
	});
})(window);


(function(window)
{
    'use strict';

	var dependenciasJavaScript = [];

	dependenciasJavaScript.push({url : 'vendor/jquery/dist/jquery.js', async : true, onLoad : function(dependenciasJavaScript){
		loader.script('vendor/bootstrap/dist/js/bootstrap.js');
		loader.script('vendor/bootstrap/dist/js/bootstrap.bundle.js');       
		loader.script('vendor/datatables/media/js/jquery.dataTables.js');
		loader.script('vendor/datatables/media/js/dataTables.bootstrap.js');
		loader.script('vendor/datatables/media/js/dataTables.dataTables.js');
		loader.script('vendor/datatables/media/js/dataTables.bootstrap4.min.js');
		loader.script('vendor/datatables.net-responsive-dt/js/responsive.dataTables.min.js');
		loader.script('vendor/datatables.net-select/js/dataTables.select.min.js');
		loader.script('vendor/datatables.net-select-dt/js/select.dataTables.js');
		loader.script('vendor/toastr/toastr.min.js');
		loader.script('vendor/jquery-validation/dist/jquery.validate.min.js');
		loader.script('vendor/grapnel/dist/grapnel.min.js');
		loader.script('assets/js/bootstrap.js');
		loader.script('vendor/bootstrap-dialog/dist/js/bootstrap-dialog.min.js');
		loader.script('vendor/pickadate/lib/compressed/picker.js');
		loader.script('vendor/pickadate/lib/compressed/picker.date.js');
		loader.script('vendor/pickadate/lib/compressed/picker.time.js');
		loader.script('vendor/select2/dist/js/select2.min.js');


        // Inicialização
        loader.script('assets/js/jquery.meanmenu.js');
		loader.script('assets/js/main.js');
		loader.script('assets/js/app.js');
		loader.script('assets/js/rotas.js');
		loader.script('assets/js/funcoesSistema.js');

		
		loader.script('assets/js/categoria/categoria.serv.js');
		loader.script('assets/js/categoria/categoria.form.ctrl.js');
		loader.script('assets/js/categoria/categoria.list.ctrl.js');
		
		loader.script('assets/js/checklist/checklist.serv.js');
		loader.script('assets/js/checklist/checklist.form.ctrl.js');
		loader.script('assets/js/checklist/checklist.list.ctrl.js');

		loader.script('assets/js/loja/loja.serv.js');
		// loader.script('assets/js/loja/loja.form.ctrl.js');
		// loader.script('assets/js/loja/loja.list.ctrl.js');
	}});
	


	var loader = new window.Loader();

	dependenciasJavaScript.forEach(function(e, index, arr)
	{
		loader.script(e.url, e.async, e.onLoad);
	});
})(window);