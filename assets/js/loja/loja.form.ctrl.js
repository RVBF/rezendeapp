/**
 *  loja.form.ctrl.js
 *
 *  @author  Rafael Vinicius Barros Ferreira
 *	 @version 1.0
 */
(function(window, app, $, toastr)
{
	'use strict';

	function ControladoraFormLoja(servicoLoja) {
		var _this = this;

		_this.alterar;
		_this.formulario = $('#loja_form');
		_this.botaoSubmissao = $('#salvar')
		_this.cancelarModoEdicao = $('#cancelar_edicao')

		// Cria as opções de validação do formulário
		var criarOpcoesValidacao = function criarOpcoesValidacao() {
			var opcoes = {
				rules: {
					"razao_social": {required : true, rangelength : [2, 100] },
					"nome_fantasia" :{required : true, rangelength : [2, 100] }
				},

				messages: {
					"razao_social": {
						required    : "O campo razão social é obrigatório.",
						rangelength: "o campo razão social deve conter no mínimo {2} e  no máximo {100} caracteres."
					},
					"nome_fantasia": {
						required    : "O campo lojas é obrigatório.",
						rangelength: "o campo nome fantasia deve conter no mínimo {2} e  no máximo {100} caracteres."
                    }				
                }
			};
			// Irá disparar quando a validação passar, após chamar o método validate().
			opcoes.submitHandler = function submitHandler(form) {
				var obj = _this.conteudo();

				_this.formulario.desabilitar(true);
								
				var terminado = function() {
					_this.formulario.desabilitar(false);
				};
				
				var jqXHR = _this.alterar ? servicoLoja.atualizar(obj) : servicoLoja.adicionar(obj);
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
			return servicoLoja.criar($('#id').val(), $('#razao_social').val(), $('#nome_fantasia').val());
		};

		_this.configurarBotoes = function configurarBotoes() {
			_this.botaoSubmissao.on('click', _this.salvar);
			_this.cancelarModoEdicao.on('click', _this.cancelar);
		};

		_this.iniciarFormularioModoCadastro = function iniciarFormularioModoCadastro() {
			_this.formulario.parents('#painel_formulario').removeClass('desabilitado').desabilitar(false);
			_this.formulario.parents('#painel_formulario').removeClass('d-none');
			
			_this.formulario.parents('#painel_formulario').promise().done(function () {
				_this.formulario.find('#tiulo').focus();
				_this.configurarBotoes();	
			});
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
			_this.formulario.find('#razao_social').val(obj.razaoSocial);
			_this.formulario.find('#nome_fantasia').val(obj.nomeFantasia);
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
	}; // ControladoraFormLoja

	// Registrando
	app.ControladoraFormLoja = ControladoraFormLoja;

})(window, app, jQuery, toastr);


var url = window.location.href;
