/**
 *  usuario.list.ctrl.js
 *
 *  @author	Rafael Vinicius Barros Ferreira
 */
(function(window, app, $, toastr, BootstrapDialog)
{
	'use strict';

	function ControladoraListagemUsuario(servicoUsuario)
	{
		var _this = this;
		var _cont = 0;
		var _tabela = null;
		_this.botaoCadastrar = $('#cadastrar');
		_this.botaoEditar = $('#editar');
		_this.botaoRemover = $('#remover');
		_this.botaoAtualizar = $('#atualizar');
		_this.idTabela = $('#usuario');
		var ctrlFormulario = new app.ControladoraFormUsuario(servicoUsuario);

		//Configura a tabela
		_this.opcoesDaTabela = function opcoesDaTabela() {
			var objeto = $.extend(true, {}, app.dtOptions);
			objeto.ajax = servicoUsuario.rota();

			objeto.columnDefs = [ {
					data: 'id',
					targets: 0,
					visible : true
				}, {
					data: function(data) {
						return data.colaborador.nome + ' ' + data.colaborador.sobrenome 	
					},
					targets: 1
				}, {
					data: 'colaborador.email',
					targets: 2
                }, {
					data: 'login',
					targets: 3
                }
			];

			return objeto;
		};'.selected'

		_this.cadastrar = function cadastrar() {
			var modoEdicao = false;
			var contexto = $('#painel_formulario');
			contexto.addClass('desabilitado');

			contexto.addClass('d-none');
			contexto.desabilitar(true);
			contexto.find('form')[0].reset();
			contexto.find('form').find('.msg').empty();
			ctrlFormulario.configurar(modoEdicao);
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
			
			ctrlFormulario.configurar(modoEdicao);
			ctrlFormulario.desenhar(objeto);
		};

		_this.atualizar = function atualizar(){
 			_tabela.ajax.reload();
		};

		_this.remover = function remover(event){
			var objeto = _tabela.row('.selected').data();

			BootstrapDialog.show({
				type	: BootstrapDialog.TYPE_DANGER,
				title	: 'Deseja remover esta Grupo?',
				message	: 'ID : ' + objeto.id + ', Login: '+ objeto.login + '.',
				size	: BootstrapDialog.SIZE_LARGE,
				buttons	: [ {
						label	: '<u>S</u>im',
						hotkey	: 'S'.charCodeAt(0),
						action	: function(dialog){
							servicoUsuario.remover(objeto.id).done(window.sucessoPadrao).fail(window.erro);
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
			_tabela = _this.idTabela.DataTable(_this.opcoesDaTabela());
			_this.botaoCadastrar.on('click',_this.cadastrar);
			_this.botaoEditar.on('click', _this.editar)
			_this.botaoAtualizar.on('click',_this.atualizar);
			_this.botaoRemover.on('click', _this.remover);;
			_tabela.on('select',_this.selecionar);
			_tabela.on('deselect', _this.deselect);		
		};
	} // ControladoraListagemUsuario

	// Registrando
	app.ControladoraListagemUsuario = ControladoraListagemUsuario;
})(window, app, jQuery, toastr, BootstrapDialog);