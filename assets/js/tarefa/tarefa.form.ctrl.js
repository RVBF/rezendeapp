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
		_this.idChecklist = window.location.href.split('#')[1].substring(1, url.length).split('/')[1];	

		// Cria as opções de validação do formulário
		var criarOpcoesValidacao = function criarOpcoesValidacao() {
			var opcoes = {
				rules: {
					"titulo": {
						required    : true,
						rangelength : [ 2, 85 ]
					},

					'descricao':{
						rangelength : [10,255]
					}
				},

				messages:
				{
					"titulo": {
						required    : "O campo título é obrigatório.",
						rangelength : $.validator.format("O campo nome deve ter no mínimo  {2} e no máximo {85} caracteres.")
					},

					"descricao": {
						rangelength : $.validator.format("O campo nome deve ter no mínimo  {10} e no máximo {255} caracteres.")
					}
				}
			};
			// Irá disparar quando a validação passar, após chamar o método validate().
			opcoes.submitHandler = function submitHandler(form) {
				_this.formulario.desabilitar(true);
				
				var terminado = function() {
					_this.formulario.desabilitar(false);
				};
				
				var obj = _this.conteudo();
				var jqXHR = _this.alterar ? servicoTarefa.atualizar(obj, _this.idChecklist) : servicoTarefa.adicionar(obj, _this.idChecklist);
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
                $('#descricao').val()
			);
		};

		_this.configurarBotoes = function configurarBotoes() {
			_this.botaoSubmissao.on('click', _this.salvar);
			_this.cancelarModoEdicao.on('click', _this.cancelar);
		};

		_this.iniciarFormularioModoCadastro = function iniciarFormularioModoCadastro() {
			_this.formulario.parents('#painel_formulario').removeClass('desabilitado').desabilitar(false);
			_this.formulario.parents('#painel_formulario').removeClass('d-none');
			_this.formulario.find('#tiulo').focus();
			_this.configurarBotoes();
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
			_this.formulario.find('#id').val(obj.id);
			_this.formulario.find('#titulo').val(obj.titulo);
			_this.formulario.find('#descricao').val(obj.descricao);
		};

		_this.salvar = function salvar() {
			_this.formulario.validate(criarOpcoesValidacao());
        };
		
		_this.cancelar = function cancelar(event) {
			var contexto = _this.formulario.parents('#painel_formulario');
			contexto.addClass('desabilitado');
			_this.formulario.find('.msg').empty();

			contexto.addClass('d-none');
			contexto.desabilitar(true);

		};

		// Configura os eventos do formulário
		_this.configurar = function configurar(status = false) {
			_this.definirForm(status);
		};
	}; // ControladoraFormTarefa

	// Registrando
	app.ControladoraFormTarefa = ControladoraFormTarefa;

})(window, app, jQuery, toastr);