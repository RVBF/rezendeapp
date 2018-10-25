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
		_this.botaoAtualizar = $('#atualizar');
		_this.idTabela = $('#categoria');

		//Configura a tabela
		_this.opcoesDaTabela = function opcoesDaTabela()
		{
			var objeto = $.extend(true, {}, app.dtOptions);
			objeto.ajax = servicoCategoria.rota();

			objeto.columnDefs = [ {
					data: 'id',
					targets: 1

				},

				{
					data: 'titulo',
					responsivePriority: 1,
					targets: 2
				}
			];

			objeto.fnDrawCallback = function(settings)
			{
				// eventos da tabela
			};
			console.log(objeto);

			return objeto;
		};

		_this.cadastrar = function cadastrar()
		{
			router.navigate('/categoria/cadastrar/');
		}

		_this.atualizar = function atualizar(){
 			_tabela.ajax.reload();
		};

		_this.visualizar = function visualizar(){
			var objeto = _tabela.row($(this).parent(' td').parent('tr')).data();
			router.navigate('/categoria/visualizar/' +  objeto.id + '/');

		};

		_this.configurar = function configurar()
		{
			_tabela = _this.idTabela.DataTable(_this.opcoesDaTabela());
			_this.botaoCadastrar.click(_this.cadastrar);
			_this.botaoAtualizar.click(_this.atualizar);
		};
	} // ControladoraListagemCategoria

	// Registrando
	app.ControladoraListagemCategoria = ControladoraListagemCategoria;
})(window, app, jQuery, toastr, BootstrapDialog);