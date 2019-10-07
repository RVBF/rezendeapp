/**
 *  permissao.serv.js
 *
 *  @author	Rafael Vinicius Barros Ferreira
 */
(function(app, $) {
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

       _this.carregarListagemDeAtividades = function carregarListagemDeAtividades() {
            return $.ajax({
                type: "GET",
                url: _this.rota() + '/minhas-atividades'
            });
       };

       _this.temPermissao = function temPermissao() {
           return $.ajax({
               type: "GET",
               url: _this.rota() + '/tem-permissao'
           });
       };
   }; // ServicoIndex
   // Registrandonome_usuario
   app.ServicoIndex = ServicoIndex;
})(app, $);


/**
 *  index.list.ctrl.js
 *
 *  @author	Rafael Vinicius Barros Ferreira
 */
(function(window, app, $, toastr){
	'use strict';

	function ControladoraIndex(servicoIndex)
	{
        var _this = this;


        _this.renderizarOpcoesHTML = function renderizarOpcoesHTMLname() {
            var sucesso = function (resposta) {
            };
            
			var  jqXHR = servicoIndex.temPermissao();
			jqXHR.done(sucesso);
        };

        _this.renderizarAtividadesUsuario = function renderizarAtividadesUsuario(){
            
            var sucesso = function (resposta) {
                console.log(resposta);
            };

            var  jqXHR = servicoIndex.carregarListagemDeAtividades();
			jqXHR.done(sucesso);
        };

        _this.renderizarDadosUsuario = function renderizarDadosUsuario() {
            var sessao = new app.Sessao();

            $('body').find('.nome_usuario').each(function() {
                $(this).empty().html(JSON.parse(sessao.getSessao()).nome);
            });
            
            $('body').find('.setor_usuario').each(function() {
                $(this).empty().html('TI');
            });
        }

		_this.configurar = function configurar() {

            _this.renderizarOpcoesHTML();  
            _this.renderizarAtividadesUsuario();
            _this.renderizarDadosUsuario();

		};
	} // ControladoraIndex

	// Registrando
	app.ControladoraIndex = ControladoraIndex;
})(window, app, jQuery, toastr);

$(document).ready(function() {
    var servicoIndex = new app.ServicoIndex();
    var crltIndex = new app.ControladoraIndex(servicoIndex);
    crltIndex.configurar();
});