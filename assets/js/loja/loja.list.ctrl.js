/**
 *  loja.list.ctrl.js
 *
 *  @author	Rafael Vinicius Barros Ferreira
 */
(function(window, app, $, toastr, BootstrapDialog)
{
	'use strict';

	function ControladoraListagemLoja(servicoLoja) {
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
			objeto.pageLength = 10;
			objeto.lengthMenu =  [10, 30, 40, 100];
			objeto.searching= true;
			objeto.ordering= true;
			objeto.searching = true;
			objeto.searchDelay = 600;	
			objeto.cadastrarLink = 'cadastrar_loja_link';
			objeto.columnDefs = function (data){
				var html = '';
					
				html += '<div class="col co-lg-8 col-md-8 col-sm-8 col-12 objeto" data="'+ data.id +'">'
				html += '<p class="f-12-dto razaoSocial" data="'+ data.razaoSocial +'"><strong>Razão Social : </strong>'+ data.razaoSocial + '</p>'
				html += '<p class="f-12-dto nomeFantasia"  data="'+ data.nomeFantasia +'"><strong>Nome Fantasia : </strong>'+ data.nomeFantasia + '</p>'
				html += '<p class="f-12-dto"> <strong>Loja</strong>  Loja Conselheiro - Nova Friburgo</p>';
				html += '</div>';


				html += '<div class="col co-lg-4 col-md-4 col-sm-4 col-12 opcoes">';
				html += '<div class="col col-4"><a class="f-12-dto grey lighten-4 btn editar_loja_link"><i class="mdi mdi-table-edit"></i> </a></div>';
				html += '<div class="col col-4"><a class="f-12-dto grey lighten-4 btn visualizar_loja_link"><i class="mdi mdi-loupe"></i> </a></div>';
				html += '<div class="col col-4"><a class="f-12-dto grey lighten-4 btn remover_loja_link" id ="remover"><i class="mdi mdi-delete"> </i> </a></div>';
				html += '</div>';

				return html;
			};

			objeto.rowsCallback = function(resposta){
				$('.remover_loja_link').on('click', _this.remover);
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
			let id = $(this).parents('.listagem-padrao-item').find('.objeto').attr('data');
			router.navigate('/editar-loja/' +  objeto.id + '/');
		};

		_this.atualizar = function atualizar(){
			_tabela.atualizarTabela();
		};

		_this.remover = function remover(event){
			let id =$(this).parents('.listagem-padrao-item').find('.objeto').attr('data');
			let razaoSocial = $(this).parents('.listagem-padrao-item').find('.razaoSocial').attr('data');
			let nomeFantasia = $(this).parents('.listagem-padrao-item').find('.nomeFantasia').attr('data');

			BootstrapDialog.show({
				type	: BootstrapDialog.TYPE_DANGER,
				title	: 'Deseja remover esta Loja?',
				message	: 'Razão Social: ' + razaoSocial + '<br> Nome Fantasia :' + nomeFantasia,
				size	: BootstrapDialog.SIZE_LARGE,
				buttons	: [ {
						label	: '<u>S</u>im',
						hotkey	: 'S'.charCodeAt(0),
						action	: function(dialog){
							servicoLoja.remover(id).done(window.sucessoPadrao).fail(window.erro);
							_this.atualizar();

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

		_this.configurar = function configurar() {
			_tabela = _this.idTabela.listar(_this.opcoesDaTabela());
		};
	} // ControladoraListagemLoja

	// Registrando
	app.ControladoraListagemLoja = ControladoraListagemLoja;
})(window, app, jQuery, toastr, BootstrapDialog);

