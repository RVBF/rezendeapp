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

	var mudarConteudo = function mudarConteudo(valor) {
		conteudo.empty().html(valor);
		setarCaminho();
	};

	var carregarPagina = function carregarPagina(pagina) {
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

	var naoEstaLogado = function (req, event, next) {
		var sessao = new app.Sessao();

		var erro = function erro(jqXHR, textStatus, errorThrown)
		{
			if( typeof next == 'function')
			{
				next();
			}
		};

		var sucesso = function sucesso(data, textStatus, jqXHR)
		{
			return;
		};

		var jqXHR = sessao.verificarSessao();
		jqXHR.fail(erro);
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
		return function()
		{
			carregarPagina(pagina);
		};
	};


	// Rotas: adicione sua rota ACIMA das existentes, a seguir. -Thiago
    router.get('/login', naoEstaLogado, criarRotaPara('login.html'));

	router.get('/categorias', verficarLogin, criarRotaPara('categoria.html'));
	router.get('/loja', verficarLogin, criarRotaPara('loja.html'));
	
	router.get('/checklist/:id/tarefa', verficarLogin, criarRotaPara('tarefa.html'));

	router.get('/tarefa/:id/pergunta', verficarLogin, criarRotaPara('pergunta.html'));
	router.get('/tarefa/:id/pergunta/cadastrar-perguntas', verficarLogin, criarRotaPara('perguntaCadastroMultiplo.html'));
	router.get('/tarefa/:id/pergunta/responder-perguntas', verficarLogin, criarRotaPara('reponderPerguntas.html'));

	router.get('/configuracao/usuario', verficarLogin, criarRotaPara('usuario.html'));
	router.get('/configuracao/grupo-usuario', verficarLogin, criarRotaPara('grupo_usuario.html'));


    router.get('/', verficarLogin, criarRotaPara('checklist.html'));

	// // 404
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
