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

		_this.link = function link(href, rel, type, opcoes) {
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

			for(var i in opcoes){
				e.setAttribute(i, opcoes[i]);
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


(function(window) {
    'use strict';
	var dependenciasCSS = [];
	
	dependenciasCSS.push({ url : 'https://fonts.googleapis.com/icon?family=Material+Icons' });
	dependenciasCSS.push({ url : 'https://fonts.googleapis.com/css?family=Open+Sans:300,300i,400,400i,600,600i,700,700i,800,800i&display=swap' });
	dependenciasCSS.push({ url : 'vendor/bootstrap/dist/css/bootstrap.min.css'});	
	dependenciasCSS.push({ url : 'assets/styles/bootstrap3.css'});
	
	dependenciasCSS.push({ url : 'vendor/bootstrap/dist/css/bootstrap-reboot.css' });
	dependenciasCSS.push({ url : 'vendor/bootstrap/dist/css/bootstrap-grid.css' });
	
	dependenciasCSS.push({ url : 'assets/styles/materialdesignicons.css', opcoes : { media : 'screen,projection' }});
	dependenciasCSS.push({ url : 'assets/styles/materialize.css', opcoes : { media : 'screen,projection' }});
	dependenciasCSS.push({ url : 'vendor/font-awesome/css/all.min.css', opcoes : { media : 'screen,projection' }});
	dependenciasCSS.push({ url : 'assets/styles/style.css', opcoes : { media : 'screen,projection' }});
	dependenciasCSS.push({ url : 'assets/styles/estilo.css', opcoes : { media : 'screen,projection' }});

	dependenciasCSS.push({ url : 'vendor/tether/dist/css/tether.min.css' });	
	dependenciasCSS.push({ url : 'vendor/bootstrap3-dialog/dist/css//bootstrap-dialog.min.css' });
	dependenciasCSS.push({ url : 'vendor/toastr/toastr.min.css' });
	dependenciasCSS.push({ url : 'vendor/pickerjs/dist/picker.min.css'});

	// dependenciasCSS.push({ url : 'vendor/datatables.net-dt/css//jquery.dataTables.min.css' });
	// dependenciasCSS.push({ url : 'vendor/datatables/media/css//dataTables.bootstrap4.css' });
	// dependenciasCSS.push({ url : 'vendor/datatables.net-responsive-dt/css//responsive.dataTables.min.css' });

	// dependenciasCSS.push({ url : 'vendor/datatables.net-responsive-bs4/css//responsive.bootstrap4.css' });
	// dependenciasCSS.push({ url : 'vendor/datatables.net-select-dt/css//select.dataTables.min.css' });

	// dependenciasCSS.push({ url : 'vendor/select2-bootstrap-theme/dist/select2-bootstrap.min.css'});	
	// dependenciasCSS.push({ url : 'vendor/select2-bootstrap4-theme/dist/select2-bootstrap4.min.css'});
	// dependenciasCSS.push({ url : 'vendor/select2/dist/css//select2.min.css' });
	
	var loader = new window.Loader();

	dependenciasCSS.forEach(function(e, index, arr) {
		if(e.hasOwnProperty('opcoes') && (!!e.aviso)) loader.link(e.url,  'stylesheet', 'text/css', e.opcoes);
		else loader.css(e.url);
	});
})(window);


(function(window)
{
    'use strict';

	var dependenciasJavaScript = [];

	dependenciasJavaScript.push({url : 'vendor/jquery/dist/jquery.min.js', async : true, onLoad : function(dependenciasJavaScript){
		// loader.script('vendor/popper.js/dist/umd/popper.min.js');
		loader.script('vendor/tether/dist/js/tether.min.js');
		loader.script('vendor/bootstrap/dist/js/bootstrap.min.js');	
		loader.script('vendor/toastr/toastr.min.js');
		loader.script('assets/js/bootstrap.js');

		loader.script('vendor/bootstrap3-dialog/dist/js/bootstrap-dialog.min.js');

		loader.script('vendor/jquery-validation/dist/jquery.validate.min.js');
		loader.script('vendor/grapnel/dist/grapnel.min.js');		
		loader.script('vendor/pickerjs/dist/picker.min.js');				
		loader.script('vendor/moment/min/moment.min.js');
		loader.script('vendor/moment/locale/pt-br.js');
		// loader.script('vendor/datatables/media/js/jquery.dataTables.js');
		// loader.script('vendor/datatables/media/js/dataTables.bootstrap4.min.js');
		// loader.script('vendor/datatables/media/js/dataTables.bootstrap.js');
		// loader.script('vendor/datatables/media/js/dataTables.dataTables.js');
		// loader.script('vendor/datatables.net-responsive/js/dataTables.responsive.min.js');

		// loader.script('vendor/datatables.net-responsive-dt/js/responsive.dataTables.min.js');
		// loader.script('vendor/datatables.net-responsive-bs4/js/responsive.bootstrap4.js');
		// loader.script('vendor/datatables.net-select/js/dataTables.select.min.js');
		// loader.script('vendor/datatables.net-select-dt/js/select.dataTables.js');
	
		// loader.script('vendor/pickadate/lib/compressed/picker.date.js');
		// loader.script('vendor/pickadate/lib/compressed/picker.time.js');
		// loader.script('vendor/select2/dist/js/select2.min.js');
		// loader.script('vendor/piexifjs/piexif.js');
		// loader.script('vendor/downloadjs/download.min.js');

		// Inicialização
		loader.script('assets/js/listagem.js');
		loader.script('assets/js/materialize.js');		
		loader.script('assets/js/init.js');		
		loader.script('assets/js/app.js');
		loader.script('assets/js/sessao/sessao.serv.js');
		loader.script('assets/js/rotas.js');

		loader.script('assets/js/usuario/usuario.serv.js');
		loader.script('assets/js/login/login.serv.js');
		loader.script('assets/js/logout/logout.serv.js');

		loader.script('assets/js/funcoesSistema.js');
		loader.script('assets/js/index.list.ctrl.js');

		loader.script('assets/js/categoria/categoria.serv.js');
		loader.script('assets/js/categoria/categoria.form.ctrl.js');
		loader.script('assets/js/categoria/categoria.list.ctrl.js');

		loader.script('assets/js/questionario/tipoquestionario.serv.js');
		loader.script('assets/js/questionario/questionario.serv.js');
		loader.script('assets/js/questionario/questionario.form.ctrl.js');
		loader.script('assets/js/questionario/questionario.list.ctrl.js');

		loader.script('assets/js/setor/setor.serv.js');
		loader.script('assets/js/setor/setor.form.ctrl.js');
		loader.script('assets/js/setor/setor.list.ctrl.js');

		loader.script('assets/js/checklist/tipoChecklist.serv.js');
		loader.script('assets/js/checklist/checklist.serv.js');
		loader.script('assets/js/checklist/checklistExecucao.form.ctrl.js');
		loader.script('assets/js/checklist/checklist.form.ctrl.js');
		loader.script('assets/js/checklist/checklist.list.ctrl.js');
		loader.script('assets/js/checklist/checklistatividades.list.ctrl.js');

		loader.script('assets/js/loja/loja.serv.js');
		loader.script('assets/js/loja/loja.form.ctrl.js');
		loader.script('assets/js/loja/loja.list.ctrl.js');

		loader.script('assets/js/grupo de usuario/grupousuario.serv.js');
		loader.script('assets/js/grupo de usuario/grupousuario.form.ctrl.js');
		loader.script('assets/js/grupo de usuario/grupousuario.list.ctrl.js');

		loader.script('assets/js/usuario/usuario.serv.js');
		loader.script('assets/js/usuario/usuario.form.ctrl.js');
		loader.script('assets/js/usuario/usuario.list.ctrl.js');
		loader.script('assets/js/login/login.form.ctrl.js');
		
		loader.script('assets/js/permissao/permissao.serv.js');
		loader.script('assets/js/permissao/permissao.form.ctrl.js');
	}});
	var loader = new window.Loader();

	dependenciasJavaScript.forEach(function(e, index, arr)
	{
		loader.script(e.url, e.async, e.onLoad);
	});
})(window);