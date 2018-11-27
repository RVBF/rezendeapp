/**
 *  grupousuario.form.ctrl.js
 *
 *  @author  Rafael Vinicius Barros Ferreira
 *	 @version 1.0
 */
(function(window, app, $, toastr)
{
	'use strict';

	function ControladoraFormGrupoUsuario(servicoGrupoUsuario) {
		var _this = this;

		_this.alterar;
		_this.formulario = $('#grupousuario_form');
		_this.botaoSubmissao = $('#salvar')
		_this.cancelarModoEdicao = $('#cancelar_edicao')

		// Cria as opções de validação do formulário
		var criarOpcoesValidacao = function criarOpcoesValidacao() {
			var opcoes = {
				rules: {
					"senha": {
						rangelength : [ 8, 50 ]
					},

					"confirmacao_senha": {
						equalTo : "#senha"
					}

				},

				messages: { 
					"senha": {
						rangelength	: $.validator.format("A senha deve ter entre {0} e {1} caracteres.")
					},

					"confirmacao_senha": {
						equalTo	: "O campo senha e confirmação de senha devem ser iguais."
					}
				}
			};

			// Irá disparar quando a validação passar, após chamar o método validate().
			opcoes.submitHandler = function submitHandler(form) {
				// console.log()
				_this.formulario.desabilitar(true);

				var erro = function erro(jqXHR, textStatus, errorThrown) {
					var mensagem = jqXHR.responseText;
					_this.formulario.find('#msg').empty().append('<div class="error" >' + mensagem + '</div>');
				};
				
				var terminado = function() {
					_this.formulario.desabilitar(false);
				};
				
				var obj = _this.conteudo();
				var jqXHR = _this.alterar ? servicoGrupoUsuario.atualizar(obj) : servicoGrupoUsuario.adicionar(obj);
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
			return servicoGrupoUsuario.criar($('#id').val(), $('#nome').val(), $('#login').val(), $('#senha').val());
		};

		_this.configurarBotoes = function configurarBotoes() {
			_this.botaoSubmissao.on('click', _this.salvar);
			_this.cancelarModoEdicao.on('click', _this.cancelar);
		};

		_this.iniciarFormularioModoCadastro = function iniciarFormularioModoCadastro() {
			_this.formulario.parents('#painel_formulario').removeClass('desabilitado').desabilitar(false);
			_this.formulario.parents('#painel_formulario').removeClass('d-none');
			_this.formulario.find('#login').focus();
			_this.configurarBotoes();
		};

		_this.iniciarFormularioModoEdicao = function iniciarFormularioModoEdicao() {
			_this.iniciarFormularioModoCadastro();

			$('#senha').removeClass('campo_obrigatorio');
			$('#confirmacao_senha ').removeClass('campo_obrigatorio');

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
			$('#id').val(obj.id);
			$('#nome').val(obj.nome);
			$('#login').val(obj.login);
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
	}; // ControladoraFormGrupoUsuario

	// Registrando
	app.ControladoraFormGrupoUsuario = ControladoraFormGrupoUsuario;

})(window, app, jQuery, toastr);