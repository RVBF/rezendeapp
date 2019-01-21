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
	};

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


	// Rotas: adicione sua rota ACIMA das existentes, a seguir. -Thiago
    router.get('/login', criarRotaPara('login.html'));

	router.get('/categorias', criarRotaPara('categoria.html'));
	router.get('/loja', criarRotaPara('loja.html'));
	router.get('/tarefa', criarRotaPara('tarefa.html'));
	router.get('/setor', criarRotaPara('setor.html'));
	router.get('/plano-acao', criarRotaPara('plano_acao.html'));
	router.get('/resposta/:tarefaId', criarRotaPara('resposta.html'));
	router.get('/configurar-permissoes', criarRotaPara('permissoes.html'));

	router.get('/tarefa/:id/pergunta', criarRotaPara('pergunta.html'));
	router.get('/tarefa/:id/pergunta/cadastrar-perguntas', criarRotaPara('perguntaCadastroMultiplo.html'));
	router.get('/tarefa/:id/pergunta/responder-perguntas', criarRotaPara('reponderPerguntas.html'));

	router.get('/configuracao/usuario', criarRotaPara('usuario.html'));
	router.get('/configuracao/grupo-usuario', criarRotaPara('grupo_usuario.html'));


    router.get('/', criarRotaPara('tarefa.html'));

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
