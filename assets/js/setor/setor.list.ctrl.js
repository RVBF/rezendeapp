/**
 *  setor.list.ctrl.js
 *
 *  @author	Rafael Vinicius Barros Ferreira
 */
(function(window, app, $, toastr)
{
	'use strict';

	function ControladoraListagemSetor(servicoSetor)
	{
		var _this = this;
		var _cont = 0;
		var _tabela = null;
		_this.botaoCadastrar = $('#cadastrar');
		_this.botaoEditar = $('#editar');
		_this.botaoRemover = $('#remover');
		_this.botaoAtualizar = $('#atualizar');
		_this.idTabela = $('#setor');
		var ctrlFormulario = new app.ControladoraFormSetor(servicoSetor, _this);

		//Configura a tabela
		_this.opcoesDaTabela = function opcoesDaTabela() {
			var objeto =  new Object();
			objeto.ajax = servicoSetor.rota();

			objeto.carregando = true;
			objeto.pageLength = 10;
			objeto.lengthMenu =  [10, 30, 40, 100];
			objeto.searching= true;
			objeto.ordering= true;
			objeto.searching = true;
			objeto.searchDelay = 600;	
			objeto.cadastrarLink = 'cadastrar_setor_link';
			objeto.columnDefs = function (data){
				var html = '';
					
				html += '<div class="col co-lg-8 col-md-8 col-sm-8 col-12 objeto" data="'+ data.id +'">'
				html += '<p class="f-12-dto titulo" data="'+ data.titulo +'"><strong>Título: </strong>'+ data.titulo + '</p>'
				html += '<p class="f-12-dto descricao"  data="'+ data.descricao +'"><strong>Descrição : </strong>'+ data.descricao + '</p>'
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
				title	: 'Deseja remover esta Setor?',
				message	: 'Setor de id: ' + objeto.id + ' e titulo : ' + objeto.titulo + '.',
				size	: BootstrapDialog.SIZE_LARGE,
				buttons	: [ {
						label	: '<u>S</u>im',
						hotkey	: 'S'.charCodeAt(0),
						action	: function(dialog){
							servicoSetor.remover(objeto.id).done(window.sucessoPadrao).fail(window.erro);
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
	} // ControladoraListagemSetor

	// Registrando
	app.ControladoraListagemSetor = ControladoraListagemSetor;
})(window, app, jQuery, toastr);