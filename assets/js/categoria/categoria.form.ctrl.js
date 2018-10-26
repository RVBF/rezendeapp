/**
 *  categoria.form.ctrl.js
 *
 *  @author  Rafael Vinicius Barros Ferreira
 *	 @version 1.0
 */
(function(window, app, $, toastr)
{
	'use strict';

	function ControladoraFormCategoria(servicoCategoria)
	{ // Model
		var _this = this;

		_this.formulario = null;
		_this.alterar = false;

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
					desabilitarFormulario(!b);
					desabilitarBotoesDeFormulario(!b);
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

				var sucesso = function sucesso(data, textStatus, jqXHR)
				{
					toastr.success('Salvo');

					var nomeCategoria = _this.formulario.find('#nome');
					_this.modal.find('form')[0].reset();
					fecharModal();

					var controladraMedicamentoPrecificado = new  app.ControladoraFormMedicamentoPrecificado(
						undefined,
						undefined,
						undefined,
						undefined,
						servicoCategoria
					);

					controladraMedicamentoPrecificado.popularSelectCategoria();
				};

				var obj = _this.conteudo();
				var jqXHR = _this.alterar ? servicoCategoria.atualizar(obj) : servicoCategoria.adicionar(obj);
				jqXHR.done(sucesso).fail(erro).always(terminado);
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

		_this.iniciarFormularioCategoria = function iniciarFormularioCategoria(){

			_this.formulario.find('#tiulo').focus();
		};

		// Desenha o objeto no formulário
		_this.desenhar = function desenhar(obj) {
			_obj = obj;
			$('#id').val(obj.id);
            $('#titulo').val(obj.nome);
		};

		_this.salvar = function salvar(event) {
			_this.formulario.validate(criarOpcoesValidacao());
        };
        
		// Configura os eventos do formulário
		_this.configurar = function configurar() {
			_this.formulario = $('#Categoria_form');
			_this.formulario.submit(false);
		};
	}; // ControladoraFormCategoria

	// Registrando
	app.ControladoraFormCategoria = ControladoraFormCategoria;

})(window, app, jQuery, toastr);


