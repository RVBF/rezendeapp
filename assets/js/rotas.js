
/**
 *  rotas.js
 *
 *  @author	Rafael vinicius barros ferreira
 */

(function(window ,app, document, $, Grapnel)
{
	'use strict';
	var router = new Grapnel();
	var conteudo = $('#app');
	
	var carregarPagina = function carregarPagina(pagina) {
		var sessao = new app.Sessao();
		var sucesso = function sucesso(data, textStatus, jqXHR) {
			if('login.html' ==  pagina) $('body').empty().load(pagina);
			else {
				if(conteudo.length > 0){
					conteudo.empty().load(pagina);
				}
				else{
					$('body').empty().load('index.html', function(){
						router.navigate('/');
					});
				}
			}
		};

		var erro = function erro(jqXHR, textStatus, errorThrown)  {
			var mensagem = jqXHR.responseText || 'Erro ao acessar página.';
			toastr.error(mensagem);

			if(sessao.getSessao() == null || sessao.getSessao() == '') {
				sessao.limparSessionStorage();
			}

			$('body').empty().load('login.html');
		};

		var jqXHR = sessao.verificarSessao();
		jqXHR.fail(erro).done(sucesso);

		// if('login.html' ==  pagina) $('body').empty().load(pagina);
		// 	else {
		// 		if(conteudo.length > 0){
		// 			conteudo.empty().load(pagina);
		// 		}
		// 		else{
		// 			$('body').empty().load('index.html', function(){
		// 				router.navigate('/');
		// 			});
		// 		}
		// 	}
			// if('login.html' ==  pagina) $('body').empty().load(pagina);
			// else {
			// 	if(conteudo.length > 0){
			// 		conteudo.empty().load(pagina);
			// 	}
			// 	else{
			// 		$('body').empty().load('index.html', function(){
			// 			router.navigate('/');
			// 		});
			// 	}
			// }

			// console.log(pagina);
			// router.navigate(pagina);
	};

	let verficarLogin = function (req, event, next) {
		var sessao = new app.Sessao();

		var erro = function erro(jqXHR, textStatus, errorThrown)  {
			var mensagem = jqXHR.responseText || 'Erro ao acessar página.';
			toastr.error(mensagem);

			if(sessao.getSessao() == null || sessao.getSessao() == '') {
				sessao.limparSessionStorage();
			}

			router.navigate('/login');
		};

		var jqXHR = sessao.verificarSessao();
		jqXHR.fail(erro);

		if( typeof next == 'function') { next(); }
	};

	var criarRotaPara = function criarRotaPara(pagina) {
		return function() {
			carregarPagina(pagina);
		};
	};


	// Rotas: adicione sua rota ACIMA das existentes, a seguir. -Rafael
	router.get('/', criarRotaPara('inicio.html'));
	router.get('/checklist', criarRotaPara('checklist.html'));
	router.get('/add-checklist', criarRotaPara('add-checklist.html'));
	router.get('/plano-acao', criarRotaPara('pa-listagem.html'));
	router.get('/checklist-organizacao', criarRotaPara('checklist-organizacao.html'));
	router.get('/inteligencia', criarRotaPara('inteligencia.html'));
	router.get('/notificacao', criarRotaPara('notificacoes.html'));
	router.get('/rd', criarRotaPara('rd.html'));
	router.get('/configuracao', criarRotaPara('configuracoes.html'));
    router.get('/login', criarRotaPara('login.html'));	

	 // 404
	router.get('/*', function(req, e) {
		if(! e.parent())
		{
			carregarPagina('404.html');
		}
	});

	// Registra como global
	window.router = router;
	app.verficarLogin = verficarLogin;

})(window ,app, document, jQuery, Grapnel);
