/**
 *  loja.list.ctrl.js
 *
 *  @author	Rafael Vinicius Barros Ferreira
 */
(function(window, app, $, toastr)
{
	'use strict';

	function ControladoraListagemLoja(servicoLoja)
	{
		var _this = this;
		var _cont = 0;
		var _tabela = null;
		_this.botaoCadastrar = $('#cadastrar');
		_this.botaoEditar = $('#editar');
		_this.botaoRemover = $('#remover');
		_this.botaoAtualizar = $('#atualizar');
		_this.idTabela = $('#loja');
		var ctrlFormulario = new app.ControladoraFormLoja(servicoLoja);

		//Configura a tabela
		_this.opcoesDaTabela = function opcoesDaTabela() {
			var objeto =  new Object();
			objeto.ajax = servicoLoja.rota();

			objeto.carregando = true;
			objeto.pageLength = 20;
			objeto.lengthMenu =  [20, 30, 40, 100];
			objeto.searching= true;
			objeto.ordering= true;
			objeto.searching = true;
			objeto.searchDelay = 600;	
			objeto.order = 'DESC';
			objeto.cadastrarLink = 'cadastrar_loja_link';
			objeto.columnDefs = function (data){
				var html = '';
					
				html += '<div class="col co-lg-12 col-md-12 col-sm-12 col-12">'
				html += '<p class="f-12-dto"><strong>Razão Social : </strong>'+ data.razaoSocial + '</p>'
				html += '<p class="f-12-dto"><strong>Nome Fantasia : </strong>'+ data.nomeFantasia + '</p>'
				html += '<p class="f-12-dto"> <strong>Loja</strong>  Loja Conselheiro - Nova Friburgo</p>';
				html += '</div>';
				return html;
			};

			return objeto;
		};


		_this.cadastrar = function cadastrar() {
			var modoEdicao = false;
			var contexto = $('#painel_formulario');
			contexto.addClass('desabilitado');

			contexto.addClass('d-none');
			contexto.desabilitar(true);
			contexto.find('form')[0].reset();
			contexto.find('form').find('.msg').empty();
			contexto.promise().done(function () {
				ctrlFormulario.configurar(modoEdicao);
			});	
		};

		_this.editar = function editar() {
			var objeto = _tabela.row('.selected').data();
			var modoEdicao = true;
			var contexto = $('#painel_formulario');
			contexto.addClass('desabilitado');

			contexto.addClass('d-none');
			contexto.desabilitar(true);
			contexto.find('form')[0].reset();
			contexto.find('form').find('.msg').empty();
			contexto.promise().done(function () {
				ctrlFormulario.configurar(modoEdicao);
				ctrlFormulario.desenhar(objeto);
			});	
		};

		_this.atualizar = function atualizar(){
 			_tabela.ajax.reload();
		};

		_this.remover = function remover(event){
			var objeto = _tabela.row('.selected').data();

			BootstrapDialog.show({
				type	: BootstrapDialog.TYPE_DANGER,
				title	: 'Deseja remover esta Loja?',
				message	: 'Loja: ' + objeto.razaoSocial + '/' + objeto.nomeFantasia,
				size	: BootstrapDialog.SIZE_LARGE,
				buttons	: [ {
						label	: '<u>S</u>im',
						hotkey	: 'S'.charCodeAt(0),
						action	: function(dialog){
							Loja.remover(objeto.id).done(window.sucessoPadrao).fail(window.erro);
							_this.atualizar();
							$('.depende_selecao').each(function(){
								$(this).prop('disabled', true);
							});

							var contexto = $('#painel_formulario');
							contexto.addClass('desabilitado');
				
							contexto.addClass('d-none');
							contexto.desabilitar(true);
							contexto.find('form')[0].reset();
							dialog.close();
						}
					}, {
						label	: '<u>N</u>ão',
						hotkey	: 'N'.charCodeAt(0),
						action	: function(dialog){
							dialog.close();
						}
					}
				]
			});


		}; // remover

		_this.visualizar = function visualizar(){
			var objeto = _tabela.row($(this).parent(' td').parent('tr')).data();
			router.navigate('/categoria/visualizar/' +  objeto.id + '/');

		};

		_this.selecionar = function selecionar() {
			var objeto = _tabela.row('.selected').data();

			$('.depende_selecao').each(function(){
				$(this).prop('disabled', false);
			});

			$('.opcoes').removeClass('desabilitado').removeClass('d-none');
			$('.opcoes').desabilitar(false);
		};

		_this.deselect = function deselect() {
			$('.depende_selecao').each(function(){
				$(this).prop('disabled', true);
			});
			
			$('.opcoes').addClass('desabilitado').addClass('d-none');
			$('.opcoes').desabilitar(true);
		};

		_this.configurar = function configurar() {
			_tabela = _this.idTabela.listar(_this.opcoesDaTabela());
		};
	} // ControladoraListagemLoja

	// Registrando
	app.ControladoraListagemLoja = ControladoraListagemLoja;
})(window, app, jQuery, toastr);

