/**
 *  login.form.ctrl.js
 *
 *  @author  Rafael Vinicius Barros Ferreira
 */
(function(window, app, $, toastr)
{
	'use strict';

	function ControladoraFormLogin(servico)
	{ // Model

		var _this = this;
		_this.modoAlteracao = true;
		_this.formulario = $('#form_login');
		_this.botaoLogar = $('#entrar')

		// Obtém o conteúdo atual do form como um objeto
		_this.conteudo = function conteudo() {
			return servico.criar($('#login').val(), $('#senha').val());
		};

		/*Envia os dados para o servidor e o coloca na sessão.*/
		_this.logar = function logar(event) {
			// Ao validar e tudo estiver correto, é disparado o método submitHandler(),
			// que é definido nas opções de validação.
			$("#form_login").validate(criarOpcoesValidacao());
		};

		// Cria as opções de validação do formulário
		var criarOpcoesValidacao = function criarOpcoesValidacao(){
			var opcoes = {
				rules: {
					"login": {
						required	: true,
					},
					"senha": {
						required	: true,
						rangelength : [ 3, 20 ]
					}
				},
				messages: {
					"login": {
						required	: "O campo login é obrigatório.",
						rangelength	: $.validator.format("A identificação deve ter entre {3} e {20} caracteres."),
					},
					"senha": {
						required	: "O campo senha é obrigatório.",
						rangelength	: $.validator.format("A Senha deve ter entre {0} e {1} caracteres.")
					}
				}
			};

			// Irá disparar quando a validação passar, após chamar o método validate().
			opcoes.submitHandler = function submitHandler(form) {
				var obj = _this.conteudo();

				_this.formulario.desabilitar(true);

				var sucesso = function sucesso(data, textStatus, jqXHR)
				{
					window.sucessoPadrao(data, textStatus, jqXHR);

					if(data.status){
						window.sessionStorage.setItem('usuario', JSON.stringify(data.usuario));
						if(!$('#app').length){
							$('body').empty().load('index.html', function(){
								router.navigate('/');
							});
						}
					}
				};

				var terminado = function() {
					_this.formulario.desabilitar(false);
				};

				var jqXHR = servico.logar(obj);

				_this.formulario.desabilitar(true);

				jqXHR.done(sucesso).fail(erro).always(terminado);
			}; // submitHandler

			return opcoes;
		}; // criarOpcoesValidacao

		// Configura os eventos do formulário
		_this.configurar = function configurar() {
			_this.formulario.find('#login').focus(); // Coloca o foco no 1° input = nome;
			_this.formulario.submit(false);
			_this.botaoLogar.on('click', _this.logar);
		};
	}; // ControladoraFormLogin

	// Registrando
	app.ControladoraFormLogin = ControladoraFormLogin;

})(window, app, jQuery, toastr);
