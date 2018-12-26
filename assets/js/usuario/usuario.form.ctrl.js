/**
 *  usuario.form.ctrl.js
 *
 *  @author  Rafael Vinicius Barros Ferreira
 *	 @version 1.0
 */
(function(window, app, $, toastr)
{
	'use strict';

	function ControladoraFormUsuario(servicoUsuario) {
		var _this = this;

		_this.alterar;
		_this.formulario = $('#usuario_form');
		_this.botaoSubmissao = $('#salvar');
		_this.cancelarModoEdicao = $('#cancelar_edicao');
		_this.obj = null;
		// Cria as opções de validação do formulário
		var criarOpcoesValidacao = function criarOpcoesValidacao() {
			var opcoes = {
				rules: {
					"nome" : {
						rangelength : [3, 50]
					},
					"sobrenome" : {
						rangelength : [3, 50]
					},
					"senha": {
						rangelength : [ 3, 20 ]
					},

					"confirmacao_senha": {
						equalTo : "#senha"
					}

				},

				messages: { 
					"senha": {
						rangelength	: $.validator.format("A senha deve ter entre {3} e {50} caracteres.")
					},

					"confirmacao_senha": {
						equalTo	: "O campo senha e confirmação de senha devem ser iguais."
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
				
				var jqXHR = _this.alterar ? servicoUsuario.atualizar(obj) : servicoUsuario.adicionar(obj);
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
			return servicoUsuario.criar(
				$('#id').val(),
				$('#nome').val(),
				$('#sobrenome').val(),
				$('#email').val(),
				$('#login').val(), 
				$('#senha').val(), 
				$('#lojas').val()
			);
		};

		_this.configurarBotoes = function configurarBotoes() {
			_this.botaoSubmissao.on('click', _this.salvar);
			_this.cancelarModoEdicao.on('click', _this.cancelar);
		};


		_this.popularLojas  =  function popularLojas(valor = 0)
		{
			var sucesso = function (resposta) {
				$("#lojas").empty();

				$.each(resposta.data, function(i ,item) {
					$("#lojas").append($('<option>', {
						value: item.id,
						text: item.razaoSocial  + '/' + item.nomeFantasia
					}));
				});

				var ids = Array();

				if(_this.obj != null || _this.obj != undefined) {
					for(var indice in _this.obj.colaborador.lojas){
						var atual =  _this.obj.colaborador.lojas[indice];
						ids.push(atual.id);
					}
					$('#lojas').val(ids).trigger('change');
				}
				else{
					$('#lojas').val(ids).trigger('change');
				}
			
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

		_this.iniciarFormularioModoCadastro = function iniciarFormularioModoCadastro() {
			if(_this.obj != null || _this.obj != undefined) {
				_this.obj = null;
			}
			_this.formulario.parents('#painel_formulario').removeClass('desabilitado').desabilitar(false);
			_this.formulario.parents('#painel_formulario').removeClass('d-none');
			_this.formulario.find('#login').focus();

			_this.popularLojas();

			_this.configurarBotoes();


			if(!$('#senha').hasClass('campo_obrigatorio')){
				$('#login').parent('div').attr('class', 'col-md-4 col-xs-4 co-sm-4 col-12');
				$('#senha').parent('div').removeClass('d-none');
				$('#confirmacao_senha').parent('div').removeClass('d-none');
	
				$('#senha').addClass('campo_obrigatorio');
				$('#lojas').addClass('campo_obrigatorio');
				$('#confirmacao_senha ').addClass('campo_obrigatorio');
			}
		};

		_this.iniciarFormularioModoEdicao = function iniciarFormularioModoEdicao() {
			_this.iniciarFormularioModoCadastro();
			$('#msg').empty();

			$('#login').parent('div').attr('class', 'col-md-12 col-xs-12 co-sm-12 col-12');
			$('#senha').parent('div').addClass('d-none');
			$('#confirmacao_senha').parent('div').addClass('d-none');

			$('#lojas').removeClass('campo_obrigatorio');
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
			_this.obj = obj;
			$('#id').val(obj.id);
			$('#nome').val(obj.colaborador.nome);
			$('#sobrenome').val(obj.colaborador.sobrenome);
			$('#email').val(obj.colaborador.email);
			$('#login').val(obj.login);
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
	}; // ControladoraFormUsuario

	// Registrando
	app.ControladoraFormUsuario = ControladoraFormUsuario;

})(window, app, jQuery, toastr);
