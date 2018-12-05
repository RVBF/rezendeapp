/**
 *  resposta.list.ctrl.js
 *
 *  @author	Rafael Vinicius Barros Ferreira
 */
(function(window, app, $, toastr, BootstrapDialog)
{
	'use strict';

	function ControladoraListagemResposta(servicoResposta)
	{
		var _this = this;
		var _cont = 0;
		var _tabela = null;
		_this.botaoCadastrar = $('#cadastrar');
		_this.botaoEditar = $('#editar');
		_this.botaoRemover = $('#remover');
		_this.botaoAtualizar = $('#atualizar');
		_this.idTabela = $('#resposta');
		var ctrlFormulario = new app.ControladoraFormResposta(servicoResposta, _this);

		//Configura a tabela
		_this.opcoesDaTabela = function opcoesDaTabela() {
			var objeto = $.extend(true, {}, app.dtOptions);
			objeto.ajax = servicoResposta.rota();

			objeto.columnDefs = [ {
					data: 'id',
					targets: 0,
					responsivePriority: 5,
					visible : true
				}, {
					data: 'pergunta.pergunta',
					responsivePriority: 1,
					targets: 1
				}, {
					data: function(data){
						var opcao = new app.Opcao();
						var opcaoSelecionada = data.opcaoSelecionada;

						return opcao.getpcoes()[opcaoSelecionada];
					},
					responsivePriority: 2,
					targets: 2
				}, {
					data: function() {
						return '<a href="anexos.html" class="anexos"><i class="fas fa-paperclip"></i></a>';
					},
					responsivePriority: 3,
					targets: 3
				}

			];

			objeto.fnDrawCallback = function(settings){
				$('tbody tr').on('click', '.anexos', function (event) {
					event.preventDefault();
					var objeto = _tabela.row($(this).parents('tr')).data();


					$('#anexos').modal();
				});
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
			ctrlFormulario.configurar(modoEdicao);
		};

		_this.editar = function editar() {
			var objeto = _tabela.row('.selected').data();
			var modoEdicao = true;
			ctrlFormulario.configurar(modoEdicao);
			ctrlFormulario.desenhar(objeto);
		}

		_this.configurar = function configurar() {
			_tabela = _this.idTabela.DataTable(_this.opcoesDaTabela());
		};
	} // ControladoraListagemResposta

	// Registrando
	app.ControladoraListagemResposta = ControladoraListagemResposta;
})(window, app, jQuery, toastr, BootstrapDialog);