/**
 *  usuario_alterarsenha.form.ctrl.js
 *
 *  @author  Rafael Vinicius Barros Ferreira
 *	 @version 1.0
 */
(function(window, app, $, toastr)
{
	'use strict';

	function ControladoraFormAlterarSenhaUsuario(servicoUsuario) {
		var _this = this;

		_this.formulario = $('#alterarsenha_form');
		_this.botaoSubmissao = $('#salvar');
		_this.cancelarModoEdicao = $('#cancelar_edicao');
		_this.obj = null;
		// Cria as opções de validação do formulário
		var criarOpcoesValidacao = function criarOpcoesValidacao() {
			var opcoes = {
				rules: {
                    "confirmacao_senha": {
						equalTo : "#nova_senha"
					}

				},

				messages: { 
					"confirmacao_senha": {
						equalTo	: "O campo senha e confirmação de senha devem ser iguais."
					}
				}
			};

			// Irá disparar quando a validação passar, após chamar o método validate().
			opcoes.submitHandler = function submitHandler(form) {
				var terminado = function() {
					_this.formulario.desabilitar(false);
				};
				
                _this.formulario.desabilitar(true);                
                
                var jqXHR = servicoUsuario.atualizarSenha($('#senha_anterior').val(), $('#nova_senha').val(), $('#confirmacao_senha').val());

				jqXHR.done(function(resposta) {
					if(resposta.status){
						router.navigate('/configuracao');
						toastr.success(resposta.mensagem);
					}
					else{
						$('body #msg').empty().removeClass('d-none').append(resposta.mensagem).focus();
						toastr.error(resposta.mensagem);
					}
				}).fail(window.erro).always(terminado);
			}; // submitHandler

			return opcoes;
		};

		_this.configurarBotoes = function configurarBotoes() {
			_this.botaoSubmissao.on('click', _this.salvar);
			_this.cancelarModoEdicao.on('click', _this.cancelar);
        }

		_this.definirForm = function definirForm() {	
            _this.formulario.submit(false);
            _this.configurarBotoes();
            
		}

		_this.salvar = function salvar() {
			_this.formulario.validate(criarOpcoesValidacao());
        };
		
		// Configura os eventos do formulário
		_this.configurar = function configurar() {
			_this.definirForm(status);
		};
	}; // ControladoraFormAlterarSenhaUsuario

	// Registrando
	app.ControladoraFormAlterarSenhaUsuario = ControladoraFormAlterarSenhaUsuario;

})(window, app, jQuery, toastr);
