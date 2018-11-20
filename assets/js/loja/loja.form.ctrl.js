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
					"razao_social": {required : true},
					"nome_fantasia" :{required : true}
				},

				messages: {
					"razao_social": {
						required    : "O campo categoria é obrigatório.",
					},
					"nome_fantasia": {
						required    : "O campo lojas é obrigatório.",
                    }				
                }
			};
			// Irá disparar quando a validação passar, após chamar o método validate().
			opcoes.submitHandler = function submitHandler(form) {
				_this.formulario.desabilitar(false);

				var erro = function erro(jqXHR, textStatus, errorThrown) {
					var mensagem = jqXHR.responseText;
					_this.formulario.find('#msg').empty().append('<div class="error" >' + mensagem + '</div>');
				};
				
				var terminado = function() {
					_this.formulario.desabilitar(true);
				};
				
				var obj = _this.conteudo();
				var jqXHR = _this.alterar ? servicoLoja.atualizar(obj) : servicoLoja.adicionar(obj);
				jqXHR.done(window.sucessoParaFormulario).fail(window.erro);

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

			contexto.addClass('d-none');
			contexto.desabilitar(true);

		};

		// Configura os eventos do formulário
		_this.configurar = function configurar(status = false) {
			_this.definirForm(status);
		};
	}; // ControladoraFormLoja

	// Registrando
	app.ControladoraFormLoja = ControladoraFormLoja;

})(window, app, jQuery, toastr);


var url = window.location.href;
