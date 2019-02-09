/**
 *  pergunta.list.ctrl.js
 *
 *  @author	Rafael Vinicius Barros Ferreira
 */
(function(window, app, $, toastr, BootstrapDialog)
{
	'use strict';

	function ControladoraListagemPergunta(servicoPergunta) {
		var _this = this;
		var _cont = 0;
		var _tabela = null;
		_this.botaoCadastrar = $('#cadastrar');
		_this.botaoEditar = $('#editar');
		_this.botaoRemover = $('#remover');
		_this.botaoAtualizar = $('#atualizar');
		_this.botaoVoltar = $('#voltar');
		_this.idTabela = $('#pergunta_table');
		_this.idTarefa = window.location.href.split('#')[1].substring(1, url.length).split('/')[1];	

		var ctrlFormulario = new app.ControladoraFormPergunta(servicoPergunta, _this);
	
		//Configura a tabela
		_this.opcoesDaTabela = function opcoesDaTabela() {
			var objeto = $.extend(true, {}, app.dtOptions);
			objeto.ajax = servicoPergunta.rota(_this.idTarefa);
			console.log( servicoPergunta.rota(_this.idTarefa));

			objeto.columnDefs = [ {
					data: 'id',
					targets: 0

				}, {
					data: 'pergunta',
                    targets: 1
				}, {
					data: function (pergunta) {
						var opcao = new app.Opcao();
						return (pergunta.resposta.opcaoSelecionada != undefined) ? opcao.getpcoes()[pergunta.resposta.opcaoSelecionada] : 'Não respondida.'
					},
                    targets: 2
				}, {
					data: 'tarefa.titulo',
                    targets: 3
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
				title	: 'Deseja remover esta pergunta?',
				message	: 'Pergunta de id:' + objeto.id + ' e título :' + objeto.pergunta,
				size	: BootstrapDialog.SIZE_LARGE,
				buttons	: [ {
						label	: '<u>S</u>im',
						hotkey	: 'S'.charCodeAt(0),
						action	: function(dialog){
							servicoPergunta.remover(objeto.id, objeto.tarefa.id).done(window.sucessoPadrao).fail(window.erro);
							
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
			router.navigate('/pergunta/visualizar/' +  objeto.id + '/');
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

		_this.voltar = function voltar() {
			event.preventDefault();

			router.navigate('/tarefa');		
		};


		_this.configurar = function configurar() {
			_tabela = _this.idTabela.DataTable(_this.opcoesDaTabela());
			_this.botaoCadastrar.on('click',_this.cadastrar);
			_this.botaoEditar.on('click', _this.editar)
			_this.botaoAtualizar.on('click',_this.atualizar);
			_this.botaoRemover.on('click', _this.remover);
			_this.botaoVoltar.on('click', _this.voltar)
			_tabela.on('select',_this.selecionar);
			_tabela.on('deselect', _this.deselect);
		};
	}; // ControladoraListagemPergunta

	// Registrando
	app.ControladoraListagemPergunta = ControladoraListagemPergunta;
})(window, app, jQuery, toastr, BootstrapDialog);