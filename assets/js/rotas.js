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

	var mudarConteudo = function mudarConteudo(valor)
	{
		conteudo.empty().html(valor);
		setarCaminho();
	};

	var carregarPagina = function carregarPagina(pagina) {
		conteudo.empty().load(pagina);
	};

	// var verficarLogin = function (req, event, next)
	// {
	// 	var servicoSessao = new app.ServicoSessao();

	// 	var erro = function erro(jqXHR, textStatus, errorThrown)
	// 	{
	// 		var mensagem = jqXHR.responseText || 'Erro ao acessar página.';
	// 		toastr.error(mensagem);

	// 		if(servicoSessao.getSessao() == null || servicoSessao.getSessao() == '')
	// 		{
	// 			servicoSessao.limparSessionStorage();
	// 		}

	// 		servicoSessao.redirecionarParalogin();

	// 		return;
	// 	};

	// 	var, verficarLogin  jqXHR = servicoSessao.verificarSessao();
	// 	jqXHR.fail(erro);

	// 	if( typeof next == 'function')
	// 	{
	// 		next();
	// 	}
	// };

	// var naoEstaLogado = function (req, event, next)
	// {
	// 	var servicoSessao = new app.ServicoSessao();

	// 	var erro = function erro(jqXHR, textStatus, errorThrown)
	// 	{
	// 		if( typeof next == 'function')
	// 		{
	// 			next();
	// 		}
	// 	};

	// 	var sucesso = function sucesso(data, textStatus, jqXHR)
	// 	{
	// 		return;
	// 	};

	// 	var jqXHR = servicoSessao.verificarSessao();
	// 	jqXHR.fail(erro);
	// };

	var criarRotaPara = function criarRotaPara(pagina)
	{
		return function()
		{
			carregarPagina(pagina);
		};
	};

	// Rotas: adicione sua rota ACIMA das existentes, a seguir. -Thiago
    router.get('/logout', criarRotaPara('login.html'));

    // router.get('/categorias', verficarLogin , criarRotaPara('categoria.html'));
    router.get('/categorias' , criarRotaPara('categoria.html'));
    // router.get('/', verficarLogin , criarRotaPara('home.html'));
    // router.get('', verficarLogin , criarRotaPara('home.html'));
    router.get('/', criarRotaPara('home.html'));


	// // 404
	router.get('/*', function(req, e)
	{
		if(! e.parent())
		{
			carregarPagina('404.html');
		}
	});

	// Registra como global
	window.router = router;

})(window ,app, document, jQuery, Grapnel);
