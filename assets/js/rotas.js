
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
	router.get('/cadastrar-checklist', criarRotaPara('formulario-checklist.html'));
	router.get('/visualizar-checklist/:id', criarRotaPara('formulario-checklist.html'));
	router.get('/editar-checklist/:id', criarRotaPara('formulario-checklist.html'));
	router.get('/executar-checklist/:id', criarRotaPara('formulario-executarchecklist.html'));
	router.get('/checklist/perguntas/:id', criarRotaPara('questionamentos.html'));
	router.get('/questionarios', criarRotaPara('questionarios.html'));
	router.get('/cadastrar-questionario', criarRotaPara('formulario-questionario.html'));

	router.get('/colaboradores', criarRotaPara('colaboradores.html'));
	router.get('/cadastrar-colaborador', criarRotaPara('formulario-colaborador.html'));	
	router.get('/lojas', criarRotaPara('loja.html'));
	router.get('/cadastrar-loja', criarRotaPara('formulario-loja.html'));
	router.get('/editar-loja/:id', criarRotaPara('formulario-loja.html'));

	router.get('/setores', criarRotaPara('setor.html'));
	router.get('/cadastrar-setor', criarRotaPara('formulario-setor.html'));
	router.get('/editar-setor/:id', criarRotaPara('formulario-setor.html'));
	
	router.get('/plano-acao', criarRotaPara('pa-listagem.html'));
	router.get('/cadastrar-pa', criarRotaPara('formulario-pa.html'));	
	router.get('/visualizar-pa/:id', criarRotaPara('formulario-pa.html'));
	router.get('/editar-pa/:id', criarRotaPara('formulario-pa.html'));
	router.get('/executar-pa/:id', criarRotaPara('formulario-executarpa.html'));
	router.get('/planosacao-pendentes/:id', criarRotaPara('pa-pendentes.html'));


	router.get('/pendencia', criarRotaPara('pendencia.html'));
	router.get('/cadastrar-pendencia', criarRotaPara('formulario-pendencia.html'));	
	router.get('/visualizar-pendencia/:id', criarRotaPara('formulario-pendencia.html'));
	router.get('/editar-pendencia/:id', criarRotaPara('formulario-pendencia.html'));
	router.get('/executar-pendencia/:id', criarRotaPara('formulario-executarpe.html'));
	router.get('/pendencias-pendentes/:id', criarRotaPara('pe-pendentes.html'));
	


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
	
	router.on('navigate', function(event){
		let html = '';
		let url = window.location.href.replace(/^.*\//g, '');
		if(url == '#' || url == ''){
			html += '<a id="logo-container" href="#" class="brand-logo center home"><img src="assets/images/logo_branco.png" alt="" class="logo-dto"></a>';
			html += '<a href="#" data-target="nav-mobile" class="sidenav-trigger button-collapse show-on-large"><i class="material-icons">menu</i></a>';
			html +=  '<a href="login.html" class="right m16-dto exit-dto"><i class="material-icons">exit_to_app</i><span>Sair</span></a>';

		}
		else if(url == 'configuracao'){
			html += '<span class="center local-dto">Configurações</span>';
			html += '<a href="#" class="left m16-dto home"><i class="material-icons">navigate_before</i></a>';
			html +=  '<a href="login.html" class="right m16-dto exit-dto"><i class="material-icons">exit_to_app</i><span>Sair</span></a>';
		}
		else if(url == 'rd'){
			html += '<span class="center local-dto">R&D DE TALENTOS</span>';
			html += '<a href="#" class="left m16-dto home"><i class="material-icons">navigate_before</i></a>';
			html +=  '<a href="login.html" class="right m16-dto exit-dto"><i class="material-icons">exit_to_app</i><span>Sair</span></a>';
		}
		else if(url == 'notificacao'){
			html += '<span class="center local-dto">Notificações</span>';
			html += '<a href="#" class="left m16-dto home"><i class="material-icons">navigate_before</i></a>';
			html +=  '<a href="login.html" class="right m16-dto exit-dto"><i class="material-icons">exit_to_app</i><span>Sair</span></a>';
		}
		else if(url == 'pendencia'){
			html += '<span class="center local-dto">Pendências</span>';
			html += '<a href="#" class="left m16-dto home"><i class="material-icons">navigate_before</i></a>';
			html +=  '<a href="login.html" class="right m16-dto exit-dto"><i class="material-icons">exit_to_app</i><span>Sair</span></a>';
		}
		else if(url == 'checklist'){
			html += '<span class="center local-dto">Checklists</span>';
			html += '<a href="#" class="left m16-dto home"><i class="material-icons">navigate_before</i></a>';
			html +=  '<a href="login.html" class="right m16-dto exit-dto"><i class="material-icons">exit_to_app</i><span>Sair</span></a>';
		}
		else if(url == 'plano-acao'){
			html += '<span class="center local-dto">Plano de Ação</span>';
			html += '<a href="#" class="left m16-dto home"><i class="material-icons">navigate_before</i></a>';
			html +=  '<a href="login.html" class="right m16-dto exit-dto"><i class="material-icons">exit_to_app</i><span>Sair</span></a>';
		}
		else if(url == 'inteligencia'){
			html += '<span class="center local-dto">inteligência</span>';
			html += '<a href="#" class="left m16-dto home"><i class="material-icons">navigate_before</i></a>';
			html +=  '<a href="login.html" class="right m16-dto exit-dto"><i class="material-icons">exit_to_app</i><span>Sair</span></a>';
		}
		else if(url == 'cadastrar-checklist'){
			html += '<span class="center local-dto">Cadastrar Checklist</span>';
			html += '<a href="#" class="left m16-dto checklist_link"><i class="material-icons">navigate_before</i></a>';
			html +=  '<a href="login.html" class="right m16-dto exit-dto"><i class="material-icons">exit_to_app</i><span>Sair</span></a>';
		}
		else if(url == 'questionarios'){
			html += '<span class="center local-dto">Questionários</span>';
			html += '<a href="#" class="left m16-dto configuracao_link"><i class="material-icons">navigate_before</i></a>';
			html +=  '<a href="login.html" class="right m16-dto exit-dto"><i class="material-icons">exit_to_app</i><span>Sair</span></a>';
		}
		else if(url == 'colaboradores'){
			html += '<span class="center local-dto">Colaboradores</span>';
			html += '<a href="#" class="left m16-dto configuracao_link"><i class="material-icons">navigate_before</i></a>';
			html +=  '<a href="login.html" class="right m16-dto exit-dto"><i class="material-icons">exit_to_app</i><span>Sair</span></a>';
		}
		else if(url == 'setores'){
			html += '<span class="center local-dto">Setores</span>';
			html += '<a href="#" class="left m16-dto configuracao_link"><i class="material-icons">navigate_before</i></a>';
			html +=  '<a href="login.html" class="right m16-dto exit-dto"><i class="material-icons">exit_to_app</i><span>Sair</span></a>';
		}
		else if(url == 'lojas'){
			html += '<span class="center local-dto">Lojas</span>';
			html += '<a href="#" class="left m16-dto configuracao_link"><i class="material-icons">navigate_before</i></a>';
			html +=  '<a href="login.html" class="right m16-dto exit-dto"><i class="material-icons">exit_to_app</i><span>Sair</span></a>';
		}
		else if(window.location.href.search('executar-checklist') != -1){
			html += '<span class="center local-dto">Executar Checklist</span>';
			html += '<a href="#" class="left m16-dto checklist_link"><i class="material-icons">navigate_before</i></a>';
			html +=  '<a href="login.html" class="right m16-dto exit-dto"><i class="material-icons">exit_to_app</i><span>Sair</span></a>';
		}
		else if(window.location.href.search('pendencias-pendentes') != -1){
			html += '<span class="center local-dto">Pendências pendente</span>';
			html += '<a href="#" class="left m16-dto checklist_link"><i class="material-icons">navigate_before</i></a>';
			html +=  '<a href="login.html" class="right m16-dto exit-dto"><i class="material-icons">exit_to_app</i><span>Sair</span></a>';
		}
		else if(window.location.href.search('Visualizar-Pendência') != -1){
			html += '<span class="center local-dto">Visualizar Pendência</span>';
			html += '<a href="#" class="left m16-dto pendencia_link"><i class="material-icons">navigate_before</i></a>';
			html +=  '<a href="login.html" class="right m16-dto exit-dto"><i class="material-icons">exit_to_app</i><span>Sair</span></a>';
		}
		else if(window.location.href.search('visualizar-pa') != -1){
			html += '<span class="center local-dto">Visualizar PA</span>';
			html += '<a href="#" class="left m16-dto pa_link"><i class="material-icons">navigate_before</i></a>';
			html +=  '<a href="login.html" class="right m16-dto exit-dto"><i class="material-icons">exit_to_app</i><span>Sair</span></a>';
		}
		else if(window.location.href.search('planosacao-pendentes') != -1){
			html += "<span class='center local-dto'>PA's pendentes</span>";
			html += '<a href="#" class="left m16-dto checklist_link"><i class="material-icons">navigate_before</i></a>';
			html +=  '<a href="login.html" class="right m16-dto exit-dto"><i class="material-icons">exit_to_app</i><span>Sair</span></a>';
		}
		else if(window.location.href.search('executar-pa') != -1){
			html += '<span class="center local-dto">Executar Plano de Ação</span>';
			html += '<a href="#" class="left m16-dto pa_link"><i class="material-icons">navigate_before</i></a>';
			html +=  '<a href="login.html" class="right m16-dto exit-dto"><i class="material-icons">exit_to_app</i><span>Sair</span></a>';
		}
		else if(window.location.href.search('executar-pendencia') != -1){
			html += '<span class="center local-dto">Executar Pendência</span>';
			html += '<a href="#" class="left m16-dto pendencia_link"><i class="material-icons">navigate_before</i></a>';
			html +=  '<a href="login.html" class="right m16-dto exit-dto"><i class="material-icons">exit_to_app</i><span>Sair</span></a>';
		}
		else if(window.location.href.search('visualizar-pendencia') != -1){
			html += '<span class="center local-dto">Visualizar Pendência</span>';
			html += '<a href="#" class="left m16-dto pendencia_link"><i class="material-icons">navigate_before</i></a>';
			html +=  '<a href="login.html" class="right m16-dto exit-dto"><i class="material-icons">exit_to_app</i><span>Sair</span></a>';
		}
		else if(window.location.href.search('visualizar-checklist') != -1){
			html += '<span class="center local-dto">Visualizar Checklist</span>';
			html += '<a href="#" class="left m16-dto pendencia_link"><i class="material-icons">navigate_before</i></a>';
			html +=  '<a href="login.html" class="right m16-dto exit-dto"><i class="material-icons">exit_to_app</i><span>Sair</span></a>';
		}
		else if(window.location.href.search('perguntas') != -1){
			html += '<span class="center local-dto">Perguntas</span>';
			html += '<a href="#" class="left m16-dto pendencia_link"><i class="material-icons">navigate_before</i></a>';
			html +=  '<a href="login.html" class="right m16-dto exit-dto"><i class="material-icons">exit_to_app</i><span>Sair</span></a>';
		}

		$('body').find('.topo-opcoes').empty().append(html);
	});
	// Registra como global
	window.router = router;
	app.verficarLogin = verficarLogin;

})(window ,app, document, jQuery, Grapnel);
