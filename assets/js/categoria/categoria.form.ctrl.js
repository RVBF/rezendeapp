/**
 *  categoria.form.ctrl.js
 *
 *  @author  Rafael Vinicius Barros Ferreira
 *	 @version 1.0
 */
(function(window, app, $, toastr)
{
	'use strict';

	function ControladoraFormCategoria(servicoCategoria, controladoraListagemCategoria) {
		var _this = this;

		_this.formulario = $('#categoria_form');
		_this.alterar = false;
		_this.botaoSubmissao = $('#salvar')
		_this.cancelarModoEdicao = $('#cancelar_edicao')

		// Cria as opções de validação do formulário
		var criarOpcoesValidacao = function criarOpcoesValidacao()
		{
			var opcoes = {
				rules: {
					"titulo": {
						required    : true,
						rangelength : [ 2, 85 ]
					}
				},

				messages:
				{
					"titulo": {
						required    : "O campo título é obrigatório.",
						rangelength : $.validator.format("O campo nome deve ter no mínimo  {2} e no máximo {85} caracteres.")
					}
				}
			};

			// Irá disparar quando a validação passar, após chamar o método validate().
			opcoes.submitHandler = function submitHandler(form)
			{
				// Habilita/desabilita os controles
				var controlesHabilitados = function controlesHabilitados(b)
				{
					_this.formulario.desabilitar(!b);
				};

				controlesHabilitados(false);

				var erro = function erro(jqXHR, textStatus, errorThrown)
				{
					var mensagem = jqXHR.responseText;
					_this.formulario.find('#msg').empty().append('<div class="error" >' + mensagem + '</div>');
				};

				var terminado = function terminado()
				{
					controlesHabilitados(true);
				};

				var obj = _this.conteudo();
				var jqXHR = _this.alterar ? servicoCategoria.atualizar(obj) : servicoCategoria.adicionar(obj);
				jqXHR.done(window.sucesso).fail(window.erro).always(terminado);
			}; // submitHandler

			return opcoes;
		};
        
		// Obtém o conteúdo atual do form como um objeto
		_this.conteudo = function conteudo()
		{
			return servicoCategoria.criar(
                $('#id').val(),
                $('#titulo').val()
			);
		};

		_this.configurarBotoes = function configurarBotoes(){
			_this.botaoSubmissao.on('click', _this.salvar());
			_this.cancelarModoEdicao.on('clik', _this.cancelar());
		};

		_this.iniciarFormularioModoCadastro = function iniciarFormularioModoCadastro(){
			_this.formulario.parents('.card').removeClass('desabilitado').desabilitar(false);
			_this.formulario.parents('.card').removeClass('d-none');
			_this.formulario.find('#tiulo').focus();
			_this.configurarBotoes();
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
			_obj = obj;
			$('#id').val(obj.id);
            $('#titulo').val(obj.nome);
		};

		_this.salvar = function salvar(event) {
			_this.formulario.validate(criarOpcoesValidacao());
        };
		
		_this.cancelar = function cancelar(event) {
			var contexto = _this.formulario.parents('.desabilitado');

			contexto.removeClass('desabilitado');
			contexto.addClass('d-none');
		};

		// Configura os eventos do formulário
		_this.configurar = function configurar(status = false) {
			_this.definirForm(status);
		};
	}; // ControladoraFormCategoria

	// Registrando
	app.ControladoraFormCategoria = ControladoraFormCategoria;

})(window, app, jQuery, toastr);


var url = window.location.href;
