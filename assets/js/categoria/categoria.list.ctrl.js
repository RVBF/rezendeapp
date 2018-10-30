/**
 *  categoria.list.ctrl.js
 *
 *  @author	Rafael Vinicius Barros Ferreira
 */
(function(window, app, $, toastr, BootstrapDialog)
{
	'use strict';

	function ControladoraListagemCategoria(servicoCategoria)
	{
		var _this = this;
		var _cont = 0;
		var _tabela = null;
		_this.botaoCadastrar = $('#cadastrar');
		_this.botaoEditar = $('#editar');
		_this.botaoRemover = $('#remover');
		_this.botaoAtualizar = $('#atualizar');
		_this.idTabela = $('#categoria');

		//Configura a tabela
		_this.opcoesDaTabela = function opcoesDaTabela()
		{
			var objeto = $.extend(true, {}, app.dtOptions);
			objeto.ajax = servicoCategoria.rota();

			objeto.columnDefs = [ {
					data: 'id',
					targets: 0

				},
				{
					data: 'titulo',
					responsivePriority: 1,
					targets: 1
				}
			];

			return objeto;
		};

		_this.cadastrar = function cadastrar() {
			var ctrlFormulario = new app.ControladoraFormCategoria(servicoCategoria, _this);
			ctrlFormulario.configurar();
		};

		_this.editar = function editar() {
			
			var objeto = _tabela.row('.selected').data();
			var modoEdicao = true;
			var ctrlFormulario = new app.ControladoraFormCategoria(servicoCategoria, _this);
			ctrlFormulario.configurar(modoEdicao);
			ctrlFormulario.desenhar(objeto);
		};

		_this.atualizar = function atualizar(){
 			_tabela.ajax.reload();
		};

		_this.remover = function remover(event){
			var objeto = _tabela.row('.selected').data();
			console.log(objeto);
            servicoCategoria.remover(objeto.id).done(window.sucesso).fail(window.erro);
		}; // remover


		_this.visualizar = function visualizar(){
			var objeto = _tabela.row($(this).parent(' td').parent('tr')).data();
			router.navigate('/categoria/visualizar/' +  objeto.id + '/');

		};

		_this.selecionar = function selecionar() {
			var objeto = _tabela.row('.selected').data();
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

			_tabela.on('select',_this.selecionar);
		};
	} // ControladoraListagemCategoria

	// Registrando
	app.ControladoraListagemCategoria = ControladoraListagemCategoria;
})(window, app, jQuery, toastr, BootstrapDialog);