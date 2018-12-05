/**
 *  tarefa.list.ctrl.js
 *
 *  @author	Rafael Vinicius Barros Ferreira
 */
(function(window, app, $, toastr, BootstrapDialog)
{
	'use strict';

	function ControladoraListagemTarefa(servicoTarefa)
	{
		var _this = this;
		var _cont = 0;
		var _tabela = null;
		_this.botaoCadastrar = $('#cadastrar');
		_this.botaoEditar = $('#editar');
		_this.botaoRemover = $('#remover');
		_this.botaoAtualizar = $('#atualizar');
		_this.idTabela = ($('#tarefa_table').length) ? $('#tarefa_table') :  $('#tarefacompleta_table');
		
		_this.idChecklist = window.location.href.split('#')[1].substring(1, url.length).split('/')[1];	

		var ctrlFormulario = new app.ControladoraFormTarefa(servicoTarefa, _this);
	
		//Configura a tabela
		_this.opcoesDaTabela = function opcoesDaTabela() {
			var objeto = $.extend(true, {}, app.dtOptions);
			objeto.ajax = servicoTarefa.rota(_this.idChecklist);

			objeto.columnDefs = [ {
					data: 'id',
					targets: 0

				}, {
					data: 'titulo',
					responsivePriority: 1,
					targets: 1
				}, {
					data : 'descricao',
					responsivePriority: 2,
					targets : 2
				}
			];

			return objeto;
		};

		_this.opcoesDaTabelaComListagemCompleta = function opcoesDaTabelaComListagemCompleta() {
			var objeto = $.extend(true, {}, app.dtOptions);
			objeto.ajax = servicoTarefa.rota(_this.idChecklist);

			objeto.columnDefs = [ {
					data: 'id',
					targets: 0

				}, {
					data: 'titulo',
					responsivePriority: 1,
					targets: 1
				}, {
					data: 'descricao',
					responsivePriority: 1,
					targets: 2
				}, {
					data : 'checklist.descricao',
					responsivePriority: 2,
					targets : 3
				}, {
					data : 'questionador.nome',
					responsivePriority: 3,
					targets : 4
				}, {
					data :function(data){

						var texto = (data.encerrada) ? "Sim" : "Não";
						var classe = (data.encerrada) ? "success" : "danger"

						return  '<div class="p-1 mb-1 bg-' + classe + ' text-white">'+ texto + '</div>';
					},
					responsivePriority: 3,
					targets : 5
				}
			];

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
		};

		_this.atualizar = function atualizar(){
 			_tabela.ajax.reload();
		};

		_this.remover = function remover(event){
			var objeto = _tabela.row('.selected').data();

			BootstrapDialog.show({
				type	: BootstrapDialog.TYPE_DANGER,
				title	: 'Deseja remover esta tarefa?',
				message	: 'Tarefa de id:' + objeto.id + ' e título :' + objeto.titulo,
				size	: BootstrapDialog.SIZE_LARGE,
				buttons	: [ {
						label	: '<u>S</u>im',
						hotkey	: 'S'.charCodeAt(0),
						action	: function(dialog){
							servicoTarefa.remover(objeto.id, objeto.checklist.id).done(window.sucessoPadrao).fail(window.erro);
							
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
			router.navigate('/tarefa/visualizar/' +  objeto.id + '/');

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
			var funcao =  ($('#tarefa_table').length) ? _this.opcoesDaTabela:  _this.opcoesDaTabelaComListagemCompleta;
			_tabela = _this.idTabela.DataTable(funcao());
			_this.botaoCadastrar.on('click',_this.cadastrar);
			_this.botaoEditar.on('click', _this.editar)
			_this.botaoAtualizar.on('click',_this.atualizar);
			_this.botaoRemover.on('click', _this.remover)
			$('.gerenciar_perguntas').on('click', function (event) {
				event.preventDefault();

				var objeto = _tabela.row('.selected').data();

				router.navigate('/tarefa/' + objeto.id + '/pergunta')
				
			});

			$('.cadastrar_perguntas').on('click', function (event) {
				event.preventDefault();

				var objeto = _tabela.row('.selected').data();

				router.navigate('/tarefa/' + objeto.id + '/pergunta/cadastrar-perguntas')
				
			});

			$('.responder_perguntas').on('click', function (event) {
				event.preventDefault();
				var objeto = _tabela.row('.selected').data();
				router.navigate('/tarefa/' + objeto.id + '/pergunta/responder-perguntas')
			});

			_tabela.on('select',_this.selecionar);
			_tabela.on('deselect', _this.deselect);
		};
	}; // ControladoraListagemTarefa

	// Registrando
	app.ControladoraListagemTarefa = ControladoraListagemTarefa;
})(window, app, jQuery, toastr, BootstrapDialog);