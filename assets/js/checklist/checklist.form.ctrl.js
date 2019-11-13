/**
 *  Checklist.form.ctrl.js
 *
 *  @author  Rafael Vinicius Barros Ferreira
 *	 @version 1.0
 */
(function(window, app, $, toastr)
{
	'use strict';

	function ControladoraFormChecklist(servicoChecklist, controladoraListagemChecklist) {
		var _this = this;

		_this.alterar;
		_this.formulario = $('#checklist_form');
		_this.botaoSubmissao = $('#salvar')
		_this.cancelarModoEdicao = $('#cancelar_edicao')
		_this.idSetor = window.location.href.split('#')[1].substring(1, url.length).split('/')[1];	
		_this.dataLimite = '';
		_this.horaLimite = '';


		// Cria as opções de validação do formulário
		var criarOpcoesValidacao = function criarOpcoesValidacao() {
			var opcoes = {
				rules: {
					"titulo": { rangelength : [ 2, 100 ] },
					'descricao':{ rangelength : [10,255] }
				},

				messages: {
					"titulo": { rangelength : $.validator.format("O campo nome deve ter no mínimo  {2} e no máximo {100} caracteres.") },
					"descricao": { rangelength : $.validator.format("O campo nome deve ter no mínimo  {0} e no máximo {255} caracteres.") }
				}
			};
			// Irá disparar quando a validação passar, após chamar o método validate().
			opcoes.submitHandler = function submitHandler(form) {
				var obj = _this.conteudo();
				
				var terminado = function() {
					_this.formulario.desabilitar(false);
				};
				
				_this.formulario.desabilitar(true);
			
				var jqXHR = _this.alterar ? servicoChecklist.atualizar(obj) : servicoChecklist.adicionar(obj);
				jqXHR.done(function() {
					router.navigate('/checklist');
					toastr.success('Checklist Adicionado com sucesso!')
				}).fail(window.erro).always(terminado);

				if(_this.alterar){
					$('.depende_selecao').each(function(){
						$(this).prop('disabled', true);
					});
				}
			}; // submitHandler

			return opcoes;
		};
        
		// Obtém o conteúdo atual do form como um objeto
		_this.conteudo = function conteudo() {
			var dataLimite = moment(_this.dataLimite.getDate()).format('YYYY-MM-DD');

			return servicoChecklist.criar(
				$('#id').val(),
				$('#titulo').val(),
				$('#descricao').val(),
				$('#tipo-checklist').val(),
				dataLimite.toString() + ' ' + $('#hora').val(),
				$('#responsavel option:selected').val(),
				$('#setor option:selected').val(),
				$('#unidade option:selected').val(),
				$('#questionarios').formSelect('getSelectedValues')
			);
		};

		_this.configurarBotoes = function configurarBotoes() {
			_this.dataLimite =new Picker($('#data').get()[0], {
					format : 'DD de MMMM de YYYY',
					controls: true,
					inline: true,
					container: '.date-panel',					
					text : {
						title: 'Selecione a data',
						cancel: 'Cancelar',
						confirm: 'OK',
						year: 'Ano',
						month: 'Mês',
						day: 'Dia',
						hour: 'Hora',
						minute: 'Minuto',
						second: 'Segundo',
						millisecond: 'Milissegundos',
					},
					headers : true,
					months : ['Janeiro', 'Fevereiro', 'Março', 'Abril', 'Maio', 'Junho', 'Julho', 'Agosto', 'Setembro', 'Outubro', 'Novembro', 'Dezembro'],
					monthsShort : ['Jan', 'Fev', 'Mar', 'Abr', 'Mai', 'Jun', 'Jul', 'Ago', 'Set', 'Out', 'Nov', 'Dez']
				});

				_this.horaLimite = new Picker($('#hora').get()[0], {
					format: 'HH:mm',
					headers: true,
					controls: true,
					inline: true,
					container: '.time-panel',	
					text : {
						title: 'Selecione a hora',
						cancel: 'Cancelar',
						confirm: 'OK',
						hour: 'Hora',
						minute: 'Minuto',
						second: 'Segundo',
						millisecond: 'Milissegundos',
					},
				});

			_this.botaoSubmissao.on('click', _this.salvar);
			_this.cancelarModoEdicao.on('click', _this.cancelar);
		};

		_this.iniciarFormularioModoCadastro = function iniciarFormularioModoCadastro() {
			_this.formulario.parents('#painel_formulario').promise().done(function() {
				_this.formulario.find('#titulo').focus();
				_this.popularLojas();
				_this.popularTiposDeChecklist();
				_this.popularColaboradores();
				_this.popularQuestionarios();
				_this.popularSetores();
				_this.configurarBotoes();
			});
		};

		_this.popularSetores  =  function popularSetores(valor = 0)
		{
			var sucesso = function (resposta) {
				$("#setor").empty();

				$.each(resposta.data, function(i ,item) {
					$("#setor").append($('<option>', {
						value: item.id,
						text: item.titulo
					}));
				});

				$('#setor').formSelect();
			};

			var erro = function(resposta)
			{
				var mensagem = jqXHR.responseText || 'Erro ao popular select de farmácias.';
				toastr.error(mensagem);
				return false;
			}

			var servicoSetor = new app.ServicoSetor();
			var  jqXHR = servicoSetor.todos();
			jqXHR.done(sucesso).fail(erro);
		};

		_this.popularLojas  =  function popularLojas(valor = 0)
		{
			var sucesso = function (resposta) {
				$("#unidade").empty();

				$.each(resposta.data, function(i ,item) {
					$("#unidade").append($('<option>', {
						value: item.id,
						text: item.razaoSocial  + '/' + item.nomeFantasia
					}));
				});

				$('#unidade').formSelect();
			};

			var erro = function(resposta)
			{
				var mensagem = jqXHR.responseText || 'Erro ao popular select de farmácias.';
				toastr.error(mensagem);
				return false;
			}

			var servicoLoja = new app.ServicoLoja();
			var  jqXHR = servicoLoja.todos();
			jqXHR.done(sucesso).fail(erro);
		};

		_this.popularColaboradores  =  function popularColaboradores(valor = 0)
		{
			var sucesso = function (resposta) {
				$("#responsavel").empty();

				$.each(resposta.data, function(i ,item) {
					$("#responsavel").append($('<option>', {
						value: item.id,
						text: item.nome  + ' ' + item.sobrenome
					}));
				});

				$('#responsavel').formSelect();
			};

			var erro = function(resposta)
			{
				var mensagem = jqXHR.responseText || 'Erro ao popular select de farmácias.';
				toastr.error(mensagem);
				return false;
			}

			var servicoUsuario = new app.ServicoUsuario();
			var  jqXHR = servicoUsuario.todos();
			jqXHR.done(sucesso).fail(erro);
		};

		_this.popularQuestionarios  =  function popularQuestionarios(valor = 0)
		{
			var sucesso = function (resposta) {
				$("#questionarios").empty();

				$.each(resposta.data, function(i ,item) {
					 let opcoes = {
						value: item.id,
						text: item.titulo,
						selected : (i ==0) ? true : false
					};
					$("#questionarios").append($('<option>', opcoes));
				});

				$('#questionarios').formSelect();
			};

			var erro = function(resposta)
			{
				var mensagem = jqXHR.responseText || 'Erro ao popular select de farmácias.';
				toastr.error(mensagem);
				return false;
			}

			var servicoQuestionario = new app.ServicoQuestionario();
			var  jqXHR = servicoQuestionario.todos();
			jqXHR.done(sucesso).fail(erro);
		};

		_this.popularTiposDeChecklist = function popularTiposChecklist() {
			var servicoTipoChecklist = new app.TipoChecklist();
			var  tiposQuestionarios = servicoTipoChecklist.getTipoChecklist();

			$("#tipo-checklist").empty();
			
			$.each(tiposQuestionarios, function(i ,item) {
				$("#tipo-checklist").append($('<option>', {
					value:item,
					text: item
				}));
			});

			$("#tipo-checklist").formSelect();
		};
		


		_this.iniciarFormularioModoEdicao = function iniciarFormularioModoEdicao() {
			_this.iniciarFormularioModoCadastro();
		};

		_this.definirForm = function definirForm(status) {			
			_this.formulario.submit(false);
			_this.alterar = status;

		 	if(!_this.alterar) {
				_this.iniciarFormularioModoCadastro();
			}
			else{
				_this.iniciarFormularioModoEdicao();
			}
		}

		// Desenha o objeto no formulário
		_this.desenhar = function desenhar(obj) {
			var dataPicker = $('#data_limite').pickadate('picker');
			var horaPicker = $('#hora_limite').pickatime('picker');

			var data  = obj.dataLimite.split('/');
			var hora = obj.dataLimite.split(' ')[1].split(':');

			_this.formulario.find('#id').val(obj.id);
			_this.formulario.find('#titulo').val(obj.titulo);
			_this.formulario.find('#descricao').val(obj.descricao);
			_this.formulario.find('#setor').val(obj.setor.id);
			_this.formulario.find('#loja').val(obj.loja.id);


			dataPicker.set('select', new Date(data[0], data[1], data[2]))
			horaPicker.set('select', hora[0] + ':' + hora[1], { format: 'hh:i' })
		};

		_this.salvar = function salvar() {
			_this.formulario.validate(criarOpcoesValidacao());
        };
		
		_this.cancelar = function cancelar(event) {
			var contexto = _this.formulario.parents('#painel_formulario');
			contexto.addClass('desabilitado');
			_this.formulario.find('.msg').empty();
			_this.formulario.find('.msg').parents('.row').addClass('d-none');
			contexto.addClass('d-none');
			contexto.desabilitar(true);

		};

		// Configura os eventos do formulário
		_this.configurar = function configurar(status = false) {
			_this.definirForm(status);
		};
	}; // ControladoraFormChecklist

	// Registrando
	app.ControladoraFormChecklist = ControladoraFormChecklist;

})(window, app, jQuery, toastr);