/**
 *  setor.form.ctrl.js
 *
 *  @author  Rafael Vinicius Barros Ferreira
 *	 @version 1.0
 */
(function(window, app, $, toastr)
{
	'use strict';

	function ControladoraFormSetor(servicoSetor, controladoraListagemSetor) {
		var _this = this;

		_this.alterar;
		_this.formulario = $('#setor_form');
		_this.botaoSubmissao = $('#salvar')
		_this.cancelarModoEdicao = $('#cancelar_edicao')

		// Cria as opções de validação do formulário
		var criarOpcoesValidacao = function criarOpcoesValidacao() {
			var opcoes = {
				rules: {
					"titulo": {required : true,
						rangelength : [ 2, 100 ] 
					},
					"categoria" :{required : true}

				},

				messages: {
					"categoria": {
						required    : "O campo categoria é obrigatório."
					},
					"titulo": {
						required    : "O campo título é obrigatório.",
						rangelength : "O campo deve conter no mínimo {2} e no máximo {100} caracteres."
					}
				}
			};
			// Irá disparar quando a validação passar, após chamar o método validate().
			opcoes.submitHandler = function submitHandler(form) {
				var obj = _this.conteudo();

				_this.formulario.desabilitar(true);
			
				var jqXHR = _this.alterar ? servicoSetor.atualizar(obj) : servicoSetor.adicionar(obj);
				jqXHR.done(window.sucessoParaFormulario).always(function(){
					_this.formulario.desabilitar(false);
				});

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
			return servicoSetor.criar(
                $('#id').val(),
                $('#titulo').val(),
				$('#descricao').val(),
				$('#categoria').val()
			);
		};

		_this.configurarBotoes = function configurarBotoes() {
			_this.botaoSubmissao.on('click', _this.salvar);
			_this.cancelarModoEdicao.on('click', _this.cancelar);
		};

		_this.popularSelectCategorias  =  function popularSelectCategorias(valor = 0)
		{
			var sucesso = function (resposta) {
				$("#categoria").empty();

				$.each(resposta.data, function(i ,item) {
					$("#categoria").append($('<option>', {
						value: item.id,
						text: item.titulo
					}));
				});

				$('#categoria').trigger('change');
			};

			var erro = function(resposta)
			{
				var mensagem = jqXHR.responseText || 'Erro ao popular select de farmácias.';
				toastr.error(mensagem);
				return false;
			}
			var servCategoria = new app.ServicoCategoria();
			var  jqXHR = servCategoria.todos();
			jqXHR.done(sucesso).fail(erro);
		};


		_this.iniciarFormularioModoCadastro = function iniciarFormularioModoCadastro() {
			_this.formulario.parents('#painel_formulario').removeClass('desabilitado').desabilitar(false);
			_this.formulario.parents('#painel_formulario').removeClass('d-none');
			_this.formulario.find('#tiulo').focus();
			_this.configurarBotoes();
			_this.popularSelectCategorias();
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
			_this.formulario.find('#categoria').val(obj.categoria.id).trigger('change');
			_this.formulario.find('#descricao').val(obj.descricao);
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
	}; // ControladoraFormSetor

	// Registrando
	app.ControladoraFormSetor = ControladoraFormSetor;

})(window, app, jQuery, toastr);


var url = window.location.href;
