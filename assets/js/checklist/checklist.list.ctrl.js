/**
 *  checklist.list.ctrl.js
 *
 *  @author	Rafael Vinicius Barros Ferreira
 */
(function(window, app, $, toastr, BootstrapDialog)
{
	'use strict';

	function ControladoraListagemChecklist(servicoCheckList)
	{
		var _this = this;
		var _cont = 0;
		var _tabela = null;
		var ultimoObjetoSelecionado = null;
		_this.botaoCadastrar = $('#cadastrar');
		_this.botaoEditar = $('#editar');
		_this.botaoRemover = $('#remover');
		_this.botaoAtualizar = $('#atualizar');
		_this.idTabela = $('#checklist');
		var ctrlFormulario = new app.ControladoraFormChecklist(servicoCheckList, _this);

		//Configura a tabela
		_this.opcoesDaTabela = function opcoesDaTabela() {
			var objeto = $.extend(true, {}, app.dtOptions);
			objeto.ajax = servicoCheckList.rota();

			objeto.columnDefs = [ {
					data: 'id',
					targets: 0,
					responsivePriority: 5,
					visible : true
				}, {
					data: 'dataLimite',
					responsivePriority: 1,
					targets: 1
				}, {
					data: 'categoria.titulo',
					responsivePriority: 2,
					targets: 2
				}, {
					data: 'descricao',
					responsivePriority: 3,
					targets: 3
				}, {
					data: 'loja.nomeFantasia',
					responsivePriority: 4,
					targets: 4
				},

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
				title	: 'Deseja remover esta Checklist?',
				message	: 'Checklist da categoria: ' + objeto.categoria.titulo + ' e descrição : ' + objeto.descricao,
				size	: BootstrapDialog.SIZE_LARGE,
				buttons	: [ {
						label	: '<u>S</u>im',
						hotkey	: 'S'.charCodeAt(0),
						action	: function(dialog){
							servicoCheckList.remover(objeto.id).done(window.sucessoPadrao).fail(window.erro);
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
			ultimoObjetoSelecionado = objeto;
			if(objeto.id > 0){
				$('.depende_selecao').each(function(){
					$(this).prop('disabled', false);
				});
			}
			else{
				$('.depende_selecao').each(function(){
					$(this).prop('disabled', true);
				});
			}
		};

		_this.configurar = function configurar() {
			_tabela = _this.idTabela.DataTable(_this.opcoesDaTabela());
			_this.botaoCadastrar.on('click',_this.cadastrar);
			_this.botaoEditar.on('click', _this.editar)
			_this.botaoAtualizar.on('click',_this.atualizar);
			_this.botaoRemover.on('click', _this.remover)
			_tabela.on('dblclick', 'tr', function(event){
				event.preventDefault();
				router.navigate('/checklist/' + ultimoObjetoSelecionado.id +'/tarefa' );
			});

			_tabela.on('select',_this.selecionar);
		};
	} // ControladoraListagemChecklist

	// Registrando
	app.ControladoraListagemChecklist = ControladoraListagemChecklist;
})(window, app, jQuery, toastr, BootstrapDialog);