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
		_this.idTabela = $('#tarefa_listagem');
		
		_this.idSetor = window.location.href.split('#')[1].substring(1, url.length).split('/')[1];	

		var ctrlFormulario = new app.ControladoraFormTarefa(servicoTarefa, _this);
		
		//Configura a tabela
		_this.opcoesDaTabela = function opcoesDaTabela() {
			var objeto = $.extend(true, {}, app.dtOptions);
			objeto.ajax = servicoTarefa.rota();
			objeto.columnDefs = [ {
					data: 'id',
					targets: 0

				}, {
					data: 'titulo',
					targets: 1
				}, {
					data: 'descricao',
					targets: 2
				}, {
					data : 'setor.titulo',
					targets : 3
				}, {
					data : function (data) {
						return data.loja.razaoSocial + '/' + data.loja.nomeFantasia
					},
					targets : 4
				}, {
					data : function(data) {
						var dataLimite = moment(data.dataLimite, "YYYY-MM-DD HH:mm:ss", 'pt-br');
						var hoje = moment();

						var eAntes = hoje.isBetween(hoje.toString(), dataLimite.toString());
						
						return (eAntes) ? '<p class="text-success align-middle">'+ dataLimite.format('DD/MM/YYYY HH:mm:ss').toString() + '</p>' : '<p class="text-danger">'+ dataLimite.format('DD/MM/YYYY HH:mm:ss').toString() + '</p>';
					},
					targets : 5
				}, {
					data :function(data) {
						return data.questionador.nome + ' ' + data.questionador.sobrenome;
					},
					targets : 6
				}, {
					data :function(data){
						var texto = (data.encerrada) ? "Sim" : "Não";
						var classe = (data.encerrada) ? "success" : "danger"

						return  '<div class="p-1 mb-1 bg-' + classe + ' text-white">'+ texto + '</div>';
					},
					targets : 7
				}, {
					data :function(data){
						var html = '';
						html += '<div class="dropdown">';
						html += '<button class="btn btn-primary btn-sm dropdown-toggle" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">';
						html += 'Opções';
						html += '</button>';
						html += '<div class="dropdown-menu" aria-labelledby="dropdownMenuLink">';
						html += (data.perguntas.length > 0 && !_this.eAdministrador) ? '<a class="dropdown-item gerenciar_perguntas" href="#">Ver Perguntas</a>' : '';
						html += (data.encerrada) ?  '<a class="dropdown-item gerenciar_respostas" href="#">Ver Respostas</a>' : '';
						html += (!data.encerrada && !_this.eAdministrador) ?  '<a class="dropdown-item cadastrar_perguntas" href="#">Cadastrar Perguntas</a>' : '';

						html += (!data.encerrada && data.perguntas.length > 0) ? '<a class="dropdown-item responder_perguntas" href="#">Responder Perguntas</a>' : '';

						html += '</div>';
						html += '</div>';

						return  html;
					},
					targets : 8
				}
			];	
			
			objeto.fnDrawCallback = function (settings, json) {

				$('#tarefa_listagem tr').on('click','.gerenciar_perguntas', function (event) {
					event.preventDefault();

					var objeto = _tabela.row($(this).parents('tr')).draw().data();

					router.navigate('/tarefa/' + objeto.id + '/pergunta')
					
				});

				$('#tarefa_listagem tr').on('click','.gerenciar_respostas', function (event) {
					event.preventDefault();
					var objeto = _tabela.row($(this).parents('tr')).data();

					router.navigate('/resposta/' + objeto.id);
				});

				$('#tarefa_listagem tr').on('click','.cadastrar_perguntas', function (event) {
					event.preventDefault();

					var objeto = _tabela.row($(this).parents('tr')).data();

					router.navigate('/tarefa/' + objeto.id + '/pergunta/cadastrar-perguntas')
					
				});

				$('#tarefa_listagem tr').on('click','.responder_perguntas', function (event) {
					event.preventDefault();
					var objeto = _tabela.row($(this).parents('tr')).data();
					router.navigate('/tarefa/' + objeto.id + '/pergunta/responder-perguntas')
				});
				$('#tarefa_listagem .dropdown-toggle').dropdown(); 


				_tabela.on('select',_this.selecionar);
				_tabela.on('deselect', _this.deselect);
			};

			return objeto;
		};
		_this.eAdministrador = function eAdministrador(){
			var eadmin = false
			var sucesso = function (resposta) {
				eadmin = resposta.status;
			};
			
			var  jqXHR = servicoIndex.temPermissao();
			jqXHR.done(sucesso);

			return eadmin;
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
				title	: 'Deseja remover esta tarefa?',
				message	: 'Tarefa de id:' + objeto.id + ' e título :' + objeto.titulo,
				size	: BootstrapDialog.SIZE_LARGE,
				buttons	: [ {
						label	: '<u>S</u>im',
						hotkey	: 'S'.charCodeAt(0),
						action	: function(dialog){
							servicoTarefa.remover(objeto.id, objeto.setor.id).done(window.sucessoPadrao).fail(window.erro);
							
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
			_this.botaoRemover.on('click', _this.remover)
		};
	}; // ControladoraListagemTarefa

	// Registrando
	app.ControladoraListagemTarefa = ControladoraListagemTarefa;
})(window, app, jQuery, toastr, BootstrapDialog);