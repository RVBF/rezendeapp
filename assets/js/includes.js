/**
 * includes.js
 *
 * @author	Rafael  Vinicius Barros Ferreira
 */

(function (window) {
   'use strict';

   function Loader() {

      var _this = this;

      var createElement = function createElement(name) {
         return window.document.createElement(name);
      };

      var addToHead = function addToHead(element) {
         window.document.head.appendChild(element);
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
         var e = createElement('script');

         e.src = src;
         e.async = isAsync === true;
         if (onLoad !== undefined) { e.onload = onLoad; }
         e.type = type !== undefined ? type : 'text/javascript';

         addToHead(e);
      };

      _this.link = function link(href, rel, type, opcoes) {
         var e = createElement('link');
         e.href = href;
         if (rel !== undefined) {
            e.rel = rel;
         }
         if (type !== undefined) {
            e.type = type;
         }

         for (var i in opcoes) {
            e.setAttribute(i, opcoes[i]);
         }

         addToHead(e);
      };

      _this.css = function css(href) {
         return _this.link(href, 'stylesheet', 'text/css');
      };

      _this.font = function font(href) {
         return _this.link(href, undefined, 'application/octet-stream');
      };
   } // class

   // Registrando no window
   window.Loader = Loader;
})(window);


(function (window) {
   'use strict';
   var dependenciasCSS = [];

   // dependenciasCSS.push({ url: 'https://fonts.googleapis.com/icon?family=Material+Icons' });
   // dependenciasCSS.push({ url: 'https://fonts.googleapis.com/css?family=Open+Sans:300,300i,400,400i,600,600i,700,700i,800,800i&display=swap' });
   // dependenciasCSS.push({ url: 'vendor/bootstrap/dist/css/bootstrap.min.css' });
   // dependenciasCSS.push({ url: 'assets/styles/bootstrap3.css' });

   // dependenciasCSS.push({ url: 'vendor/bootstrap/dist/css/bootstrap-reboot.css' });
   // dependenciasCSS.push({ url: 'vendor/bootstrap/dist/css/bootstrap-grid.css' });

   // dependenciasCSS.push({ url : 'assets/styles/materialdesignicons.css', opcoes : { media : 'screen,projection' }});
   // dependenciasCSS.push({ url : 'assets/styles/materialize.css', opcoes : { media : 'screen,projection' }});
   // dependenciasCSS.push({ url: 'vendor/font-awesome/css/all.min.css', opcoes: { media: 'screen,projection' } });
   // dependenciasCSS.push({ url: 'assets/styles/style.css', opcoes: { media: 'screen,projection' } });
   // dependenciasCSS.push({ url: 'assets/styles/estilo.css', opcoes: { media: 'screen,projection' } });

   // dependenciasCSS.push({ url: 'vendor/tether/dist/css/tether.min.css' });
   // dependenciasCSS.push({ url: 'vendor/bootstrap3-dialog/dist/css//bootstrap-dialog.min.css' });
   // dependenciasCSS.push({ url: 'vendor/toastr/toastr.min.css' });
   // dependenciasCSS.push({ url: 'vendor/pickerjs/dist/picker.min.css' });


   // dependenciasCSS.push({ url : 'vendor/datatables.net-dt/css//jquery.dataTables.min.css' });
   // dependenciasCSS.push({ url : 'vendor/datatables/media/css//dataTables.bootstrap4.css' });
   // dependenciasCSS.push({ url : 'vendor/datatables.net-responsive-dt/css//responsive.dataTables.min.css' });

   // dependenciasCSS.push({ url : 'vendor/datatables.net-responsive-bs4/css//responsive.bootstrap4.css' });
   // dependenciasCSS.push({ url : 'vendor/datatables.net-select-dt/css//select.dataTables.min.css' });

   // dependenciasCSS.push({ url : 'vendor/select2-bootstrap-theme/dist/select2-bootstrap.min.css'});	
   // dependenciasCSS.push({ url : 'vendor/select2-bootstrap4-theme/dist/select2-bootstrap4.min.css'});
   // dependenciasCSS.push({ url : 'vendor/select2/dist/css//select2.min.css' });

   var loader = new window.Loader();

   // dependenciasCSS.forEach(function (e, index, arr) {
   //    if (e.hasOwnProperty('opcoes') && (!!e.aviso)) loader.link(e.url, 'stylesheet', 'text/css', e.opcoes);
   //    else loader.css(e.url);
   // });
})(window);


(function (window) {
   'use strict';

   var dependenciasJavaScript = [];

   dependenciasJavaScript.push({
      url: 'vendor/jquery/dist/jquery.min.js', async: true, onLoad: function (dependenciasJavaScript) {
         loader.script('vendor/popper.js/dist/umd/popper.min.js');
         loader.script('vendor/tether/dist/js/tether.min.js');
         loader.script('vendor/bootstrap/dist/js/bootstrap.min.js');
         loader.script('vendor/toastr/toastr.min.js');
         // loader.script('vendor/jquery-mobile/js/events/touch.js');

         loader.script('assets/js/bootstrap.js');

         loader.script('vendor/bootstrap3-dialog/dist/js/bootstrap-dialog.min.js');

         loader.script('vendor/jquery-validation/dist/jquery.validate.min.js');
         loader.script('vendor/jquery-validation/dist/additional-methods.min.js');
         loader.script('vendor/jquery-mask-plugin/dist/jquery.mask.min.js');

         loader.script('assets/js/jquery.validate.file-master/jquery.validate.file.js');
         

         loader.script('vendor/grapnel/dist/grapnel.min.js');
         loader.script('vendor/pickerjs/dist/picker.min.js');
         loader.script('vendor/moment/min/moment.min.js');
         loader.script('vendor/moment/locale/pt-br.js');
         loader.script('vendor/jstree/dist/jstree.min.js');
         loader.script('vendor/downloadjs/download.min.js');
         loader.script('https://www.gstatic.com/charts/loader.js');
         // Inicialização
         loader.script('assets/js/listagem.js');
         loader.script('assets/js/materialize.js');
         loader.script('assets/js/app.js');
         loader.script('assets/js/sessao/sessao.serv.js');
         loader.script('assets/js/rotas.js');

         loader.script('assets/js/endereco/endereco.serv.js');

         loader.script('assets/js/usuario/usuario.serv.js');
         loader.script('assets/js/login/login.serv.js');
         loader.script('assets/js/logout/logout.serv.js');


         loader.script('assets/js/funcoesSistema.js');
         loader.script('assets/js/index.list.ctrl.js');

         loader.script('assets/js/questionario/tipoquestionario.serv.js');
         loader.script('assets/js/questionario/questionario.serv.js');
         loader.script('assets/js/questionario/questionario.form.ctrl.js');
         loader.script('assets/js/questionario/questionario.list.ctrl.js');

         loader.script('assets/js/setor/setor.serv.js');
         loader.script('assets/js/setor/setor.form.ctrl.js');
         loader.script('assets/js/setor/setor.list.ctrl.js');

         loader.script('assets/js/plano-acao/planoacao.serv.js');
         loader.script('assets/js/plano-acao/planoacao.form.ctrl.js');
         loader.script('assets/js/plano-acao/planoacaoexecucao.form.ctrl.js');
         loader.script('assets/js/plano-acao/planoacao.list.ctrl.js');
         loader.script('assets/js/plano-acao/planoacaopendente.list.ctrl.js');

         loader.script('assets/js/pendencia/pendencia.serv.js');
         loader.script('assets/js/pendencia/pendencia.form.ctrl.js');
         loader.script('assets/js/pendencia/pendenciaexecucao.form.ctrl.js');
         loader.script('assets/js/pendencia/pendencia.list.ctrl.js');
         loader.script('assets/js/pendencia/pendenciapendente.list.ctrl.js');

         loader.script('assets/js/questionamento/questionamento.serv.js');
         loader.script('assets/js/questionamento/questionamento.form.ctrl.js');
         loader.script('assets/js/questionamento/questionamento.list.ctrl.js');


         loader.script('assets/js/checklist/tipoChecklist.serv.js');
         loader.script('assets/js/checklist/checklist.serv.js');
         loader.script('assets/js/checklist/checklist.form.ctrl.js');
         loader.script('assets/js/checklist/checklist.list.ctrl.js');
         loader.script('assets/js/checklist/checklistatividades.list.ctrl.js');

         loader.script('assets/js/loja/loja.serv.js');
         loader.script('assets/js/loja/loja.form.ctrl.js');
         loader.script('assets/js/loja/loja.list.ctrl.js');

         loader.script('assets/js/grupo de usuario/grupousuario.serv.js');
         loader.script('assets/js/grupo de usuario/grupousuario.form.ctrl.js');
         loader.script('assets/js/grupo de usuario/grupousuario.list.ctrl.js');[]

         loader.script('assets/js/usuario/usuario.form.ctrl.js');
         loader.script('assets/js/usuario/usuario_alterarsenha.form.ctrl.js');
         loader.script('assets/js/usuario/usuario.list.ctrl.js');
         loader.script('assets/js/login/login.form.ctrl.js');

         loader.script('assets/js/colaborador/colaborador.serv.js');
         loader.script('assets/js/colaborador/colaborador.form.ctrl.js');
         loader.script('assets/js/colaborador/colaborador.list.ctrl.js');

         loader.script('assets/js/acesso/acesso.serv.js');
         loader.script('assets/js/acesso/configuraracesso.form.ctrl.js');

         loader.script('assets/js/dashboard/dashboard.serv.js')
         loader.script('assets/js/dashboard/dashboard.list.ctrl.js')

      }
   });
   var loader = new window.Loader();

   dependenciasJavaScript.forEach(function (e, index, arr) {
      loader.script(e.url, e.async, e.onLoad);
   });
})(window);