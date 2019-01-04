/**
 *  tarefa.form.ctrl.js
 *
 *  @author  Rafael Vinicius Barros Ferreira
 *	 @version 1.0
 */
(function(window, app, $, toastr)
{
	'use strict';

	function ControladoraFormTarefa(servicoTarefa, controladoraListagemTarefa) {
		var _this = this;

		_this.alterar;
		_this.formulario = $('#tarefa_form');
		_this.botaoSubmissao = $('#salvar')
		_this.cancelarModoEdicao = $('#cancelar_edicao')
		_this.idSetor = window.location.href.split('#')[1].substring(1, url.length).split('/')[1];	

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

				_this.formulario.desabilitar(true);
				
				var terminado = function() {
					_this.formulario.desabilitar(false);
				};
				
 				var jqXHR = _this.alterar ? servicoTarefa.atualizarComSetorId(obj, _this.idSetor) : servicoTarefa.adicionarComSetorId(obj, _this.idSetor);
				jqXHR.done(window.sucessoParaFormulario).fail(window.erro).always(terminado);

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
			return servicoTarefa.criar(
				$('#id').val(),
				$('#titulo').val(),
				$('#descricao').val(),
				$('#data_limite').pickadate('picker').get('select', 'yyyy-mm-dd') + ' ' + $('#hora_limite').pickatime('picker').get('select','HH:i'),
				$('#setor option:selected').val(),
				$('#loja option:selected').val()
			);
		};

		_this.configurarBotoes = function configurarBotoes() {
			_this.botaoSubmissao.on('click', _this.salvar);
			_this.cancelarModoEdicao.on('click', _this.cancelar);
		};

		_this.iniciarFormularioModoCadastro = function iniciarFormularioModoCadastro() {
			_this.formulario.parents('#painel_formulario').removeClass('d-none');
			_this.formulario.parents('#painel_formulario').removeClass('desabilitado').desabilitar(false);

			_this.formulario.parents('#painel_formulario').promise().done(function() {
				_this.formulario.find('#titulo').focus();
				_this.configurarBotoes();

				if(_this.idSetor == undefined) _this.popularSetors();
				_this.popularLojas();
			});
		};

		_this.popularSetors  =  function popularSetors(valor = 0)
		{
			var sucesso = function (resposta) {
				$("#setor").empty();

				$.each(resposta.data, function(i ,item) {
					$("#setor").append($('<option>', {
						value: item.id,
						text: item.titulo
					}));
				});

				$('#setor').trigger('change');
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
				$("#loja").empty();

				$.each(resposta.data, function(i ,item) {
					$("#loja").append($('<option>', {
						value: item.id,
						text: item.razaoSocial  + '/' + item.nomeFantasia
					}));
				});

				$('#loja').trigger('change');
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
			
			$('.select2').select2({
				theme: 'bootstrap4',
				width: '100%',
			});
		};
	}; // ControladoraFormTarefa

	// Registrando
	app.ControladoraFormTarefa = ControladoraFormTarefa;

})(window, app, jQuery, toastr);