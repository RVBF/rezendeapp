/**
 *  permissao.serv.js
 *
 *  @author	Rafael Vinicius Barros Ferreira
 */
(function(app, $)
{
   'use strict';

   function ServicoIndex() { // Model
       var _this = this;
       // Rota no servidor
       _this.rota = function rota() {
           return app.api + '/index';
       };

       // Cria um objeto de usuario
       this.criar = function criar(grupos = [], usuarios = []) {
           return {
               grupos : grupos  || 0,
               usuarios : usuarios || ''
           };
       };

       _this.temPermissao = function temPermissao() {
           return $.ajax({
               type: "GET",
               url: _this.rota() + '/tem-permissao'
           });
       };
   }; // ServicoIndex

   // Registrando
   app.ServicoIndex = ServicoIndex;
})(app, $);


/**
 *  index.list.ctrl.js
 *
 *  @author	Rafael Vinicius Barros Ferreira
 */
(function(window, app, $, toastr, BootstrapDialog)
{
	'use strict';

	function ControladoraIndex(servicoIndex)
	{
        var _this = this;
        _this.opcoesDropNavbarParaHTML = function opcoesDropNavbarParaHTML(temPermissao){
            var opcoesParaHTML = '';

            opcoesParaHTML += (!temPermissao) ? '' :'<div id="tarefa" class="tab-pane notika-tab-menu-bg animated flipInX show active">';
            opcoesParaHTML += (!temPermissao) ? '' :'<div class="col-lg-12 col-md-9 col-sm-12 col-xs-12">';
            opcoesParaHTML += (!temPermissao) ? '' :'<ul class="notika-main-menu-dropdown">';
            opcoesParaHTML += (!temPermissao) ? '' :'<li><a href="categoria.html" class="categoria_link">Categoria</a></li>';
            opcoesParaHTML += (!temPermissao) ? '' :'<li><a href="loja.html" class="loja_link">Loja</a></li>';
            opcoesParaHTML += (!temPermissao) ? '' :'<li><a href="tarefaListagemCompleta.html" class="setor_link">Setor</a></li>';
            opcoesParaHTML += (!temPermissao) ? '' :'</ul>';
            opcoesParaHTML += (!temPermissao) ? '' :'</div>';
            opcoesParaHTML += (!temPermissao) ? '' :'</div>';

            opcoesParaHTML += (!temPermissao) ? '' :  '<div id="config" class="tab-pane notika-tab-menu-bg animated flipInX">';
            opcoesParaHTML += (!temPermissao) ? '' :  '<div class="col-lg-12 col-md-9 col-sm-12 col-xs-12">';
            opcoesParaHTML += (!temPermissao) ? '' :  '<ul class="notika-main-menu-dropdown">';
            opcoesParaHTML += (!temPermissao) ? '' :  '<li><a href="usuario.html" class="usuario_link">Usuário</a></li>';
            opcoesParaHTML += (!temPermissao) ? '' :  '<li><a href="grupo_usuario.html" class="grupo_usuario_link">Grupo de Usuário</a></li>';
            opcoesParaHTML += (!temPermissao) ? '' :  '<li><a href="permissoes.html" class="permissoes_link">Permissões</a></li>';
            opcoesParaHTML += (!temPermissao) ? '' :  '</ul>';
            opcoesParaHTML += (!temPermissao) ? '' :  '</div>';
            opcoesParaHTML += (!temPermissao) ? '' :  '</div>';

            opcoesParaHTML += '<div id="opc_user" class="tab-pane notika-tab-menu-bg animated flipInX">';
            opcoesParaHTML += '<div class="col-lg-12 col-md-9 col-sm-12 col-xs-12"> ';
            opcoesParaHTML += '<ul class="notika-main-menu-dropdown">';
            opcoesParaHTML += '<li><a href="login.html" class="efetuar_logout">Sair</a></li>';
            opcoesParaHTML += '</ul>';
            opcoesParaHTML += '</div>';
            opcoesParaHTML += '</div>';

            $('#opcoes_drop_nav').append(opcoesParaHTML);
        };

        _this.opcoesNavbarParaHTML = function opcoesNavbarParaHTML(temPermissao){
            var opcoesParaHTML = '';

            opcoesParaHTML += '<li class="nav-item">';
            opcoesParaHTML += '<a data-toggle="tab" href="#tarefa" aria-expanded="false" class="checklistListagemCompleta_link nav-link active"><i class="rezende-icon fas fa-tasks"></i>Checklist</a>';            
            opcoesParaHTML += '</li>';

            opcoesParaHTML += (!temPermissao) ? '' :'<li class="nav-item">';
            opcoesParaHTML += (!temPermissao) ? '' : '<a data-toggle="tab" href="#plano_acao" aria-expanded="false" class="planoAcao_link nav-link"><i class=" rezende-icon far fa-check-square"></i>Plano de ação</a>';
            opcoesParaHTML += (!temPermissao) ? '' :'</li>';

            opcoesParaHTML += (!temPermissao) ? '' :'<li class="nav-item">';
            opcoesParaHTML += (!temPermissao) ? '' :'<a data-toggle="tab" href="#config" aria-expanded="false" class="nav-link"><i class="rezende-icon fas fa-cog"></i>Configuração</a>';
            opcoesParaHTML += (!temPermissao) ? '' :'</li>';

            opcoesParaHTML += '<li class="nav-item">';
            opcoesParaHTML += '<a data-toggle="tab" href="#opc_user" aria-expanded="false" class="nav-link"><i class="rezende-icon fas fa-sign-out-alt"></i>Opções de usuário</a>';
            opcoesParaHTML += '</li>';

            $('#menu_nav').append(opcoesParaHTML);
        };

        _this.renderizarOpcoesMobile = function renderizarOpcoesMobile(temPermissao){
            var opcoesParaHTML = '';
            opcoesParaHTML += '<li>';
            opcoesParaHTML += '<a data-toggle="collapse" data-target="#tarefa" class="checklistListagemCompleta_link link_menu_mobile redireciona_mobile">Checklist</a>';
            opcoesParaHTML += (!temPermissao) ? '' :'<ul class="collapse dropdown-header-top">';
            opcoesParaHTML += (!temPermissao) ? '' :'<li>';
            opcoesParaHTML += (!temPermissao) ? '' :'<a href="categoria.html" class="categoria_link link_menu_mobile redireciona_mobile">Categoria</a>';
            opcoesParaHTML += (!temPermissao) ? '' :'</li>';
            opcoesParaHTML += (!temPermissao) ? '' :'';
            opcoesParaHTML += (!temPermissao) ? '' :'<li>';
            opcoesParaHTML += (!temPermissao) ? '' :'<a href="loja.html" class="loja_link link_menu_mobile redireciona_mobile">Lojas</a>';
            opcoesParaHTML += (!temPermissao) ? '' :'</li>';
            opcoesParaHTML += (!temPermissao) ? '' :'';
            opcoesParaHTML += (!temPermissao) ? '' :'<li>';
            opcoesParaHTML += (!temPermissao) ? '' :'<a href="tarefaListagemCompleta.html" class="setor_link link_menu_mobile redireciona_mobile">Setor</a>';
            opcoesParaHTML += (!temPermissao) ? '' :'</li>';
            opcoesParaHTML += (!temPermissao) ? '' :'</ul>';
            opcoesParaHTML += '</li>';

            opcoesParaHTML += (!temPermissao) ? '' :'<li>';
            opcoesParaHTML += (!temPermissao) ? '' :'<a data-toggle="collapse" data-target="#setor" class="configuracoes_link link_menu_mobile">Configurações</a>';
            opcoesParaHTML += (!temPermissao) ? '' :'';
            opcoesParaHTML += (!temPermissao) ? '' :'<ul class="collapse dropdown-header-top">';
            opcoesParaHTML += (!temPermissao) ? '' :'<li>';
            opcoesParaHTML += (!temPermissao) ? '' :'<a href="usuario.html" class="usuario_link link_menu_mobile redireciona_mobile">Usuário</a>';
            opcoesParaHTML += (!temPermissao) ? '' :'</li>';
            opcoesParaHTML += (!temPermissao) ? '' :'';
            opcoesParaHTML += (!temPermissao) ? '' :'<li>';
            opcoesParaHTML += (!temPermissao) ? '' :'<a href="grupo_usuario.html" class="grupo_usuario_link link_menu_mobile redireciona_mobile">Grupo de  Usuário</a>';
            opcoesParaHTML += (!temPermissao) ? '' :'</li>';
            opcoesParaHTML += (!temPermissao) ? '' :  '<li><a href="permissoes.html" class="permissoes_link link_menu_mobile redireciona_mobile">Permissões</a></li>';

            opcoesParaHTML += (!temPermissao) ? '' :'</ul>';
            opcoesParaHTML += (!temPermissao) ? '' :'</li>';

            opcoesParaHTML += '<li>';
            opcoesParaHTML += '<a data-toggle="collapse" data-target="#setor" class="configuracoes_link link_menu_mobile">Opções de usuário</a>';

            opcoesParaHTML += '<ul class="collapse dropdown-header-top">';
            opcoesParaHTML += '<li>';
            opcoesParaHTML += '<a href="login.html" class="efetuar_logout link_menu_mobile redireciona_mobile">Sair</a>';
            opcoesParaHTML += '</li>';
            opcoesParaHTML += '</ul>';
            opcoesParaHTML += '</li>';

            $('#opcoes_mobile').append(opcoesParaHTML).promise().done(function () {
                window.meanBar();
            });

        };

        _this.renderizarOpcoesHTML = function renderizarOpcoesHTMLname() {
            var sucesso = function (resposta) {
                _this.opcoesDropNavbarParaHTML(resposta.status);
                _this.opcoesNavbarParaHTML(resposta.status);

                _this.renderizarOpcoesMobile(resposta.status);
            };
            
			var  jqXHR = servicoIndex.temPermissao();
			jqXHR.done(sucesso);
        };

		_this.configurar = function configurar() {
            this.renderizarOpcoesHTML();
		};
	} // ControladoraIndex

	// Registrando
	app.ControladoraIndex = ControladoraIndex;
})(window, app, jQuery, toastr, BootstrapDialog);

$(document).ready(function() {
    var servicoIndex = new app.ServicoIndex();
    var crltIndex = new app.ControladoraIndex(servicoIndex);
    crltIndex.configurar();
});