/**
 *  Questionario.form.ctrl.js
 *
 *  @author  Rafael Vinicius Barros Ferreira
 *	 @version 1.0
 */
(function(window, app, $, toastr)
{
	'use strict';

	function ControladoraFormQuestionario(servicoQuestionario, controladoraListagemQuestionario) {
		var _this = this;

		_this.alterar;
		_this.formulario = $('#questionario_form');
		_this.botaoSubmissao = $('#salvar')

		// Cria as opções de validação do formulário
		var criarOpcoesValidacao = function criarOpcoesValidacao() {
			var opcoes = {
				rules: {
					"titulo": {required : true,
						rangelength : [ 2, 100 ] 
					},

				},

				messages: {
					"titulo": {
						required    : "O campo título é obrigatório.",
						rangelength : "O campo deve conter no mínimo {2} e no máximo {100} caracteres."
					}
				}
			};
			// Irá disparar quando a validação passar, após chamar o método validate().
			opcoes.submitHandler = function submitHandler(form) {
				var obj = _this.conteudo();
				var terminado = function() {
					_this.formulario.desabilitar(false);
				};
				
				_this.formulario.desabilitar(true);
			
				var jqXHR = _this.alterar ? servicoQuestionario.atualizar(obj) : servicoQuestionario.adicionar(obj);
				jqXHR.done(function() {
					router.navigate('/questionarios');
					toastr.success('Questionário Adicionado com sucesso!')
				}).fail(window.erro).always(terminado);

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
			var configuracaoes = {perguntas : []};

			if(_this.formulario.find('.pergunta').length > 0){
				$('.ids').each(function(){
					var id = $(this).val();
					configuracaoes.perguntas.push({id: id, pergunta: $('#pergunta_'+ id).val()});
				});
			}
			
			return servicoQuestionario.criar(
                $('#id').val(),
                $('#titulo').val(),
				$('#descricao').val(),
				$('#tipo-questionario').val(),
				configuracaoes
			);
		};

		_this.popularTiposDeQuestionarios = function popularTiposQuestionarios() {
			var servicoLoja = new app.TipoQuestionario();
			var  tiposQuestionarios = servicoLoja.getTipoQuestionario();

			$("#tipo-questionario").empty();
			
			$.each(tiposQuestionarios, function(i ,item) {
				$("#tipo-questionario").append($('<option>', {
					value:item,
					text: item
				}));
			});

			$("#tipo-questionario").formSelect();
		};

		_this.adiconarPergunta = function(){
			var quantidade = $(this).parents('form').find('.perguntas').find('.pergunta').length + 1;
			var html  = '<div class="pergunta">';
			html += '<input type= "hidden" class="ids"  name="pergunta_' + quantidade + '" value ="'+ quantidade +'">';
			html += '<div class="row form-row">';
			html += '<div class="col col-lg-9 col-md-9 col-sm-9 col-12">';
			html += ' <div class="input-field">';
			html += '<input type="text" class="form-control campo_obrigatorio" id="pergunta_' + quantidade + '" name="pergunta_' + quantidade + '">';
			html += '<label for="pergunta_' + quantidade + '">Pergunta nº '+ quantidade +':</label>';
			html += '</div>';
			html += '</div>';


			html += '<div class="col col-lg-3 col-md-3 col-sm-3 col-12">';
			html += '<div class="bnt_campoextra">';

			html += '<div class="col col-lg-6 col-md-6 col-sm-6 col-3">';
			html += '<button aria-hidden="true" role="presentation" type="button" aria-label="Adicionar pergunta" class="btn bnt_opcoesformulario btn-sm red darken-4 adicionar_pergunta"><i class="fas fa-plus"></i></button>';
			html += '</div>';

			html += '<div class="col col-lg-6 col-md-6 col-sm-6 col-9">';          
			html += '<button aria-hidden="true" role="presentation" type="button" aria-label="Remover Pergunta" class="btn bnt_opcoesformulario btn-sm yellow accent-4 remover_pergunta"><i class="fas fa-minus"></i></button>';
			html += '</div>';
			
			html += '</div>';
			html += '</div>';
			html += '</div>';
			html += '</div>';

			_this.formulario.find('.perguntas').append(html);
			$('.adicionar_pergunta:last').on('click',  _this.adiconarPergunta);
			$('.remover_pergunta:last').on('click', _this.removerPergunta);
		};

		_this.removerPergunta = function(){
			var quantidade = $(this).parents('.perguntas').find('.pergunta').length;
			
			if(quantidade > 1) $(this).parents('.pergunta').remove();
		};
		_this.configurarBotoes = function configurarBotoes() {
			$('body').find('.adicionar_pergunta').on('click', _this.adiconarPergunta);
			$('.remover_pergunta:last').on('click', _this.removerPergunta);


			_this.botaoSubmissao.on('click', _this.salvar);
		};

		_this.iniciarFormularioModoCadastro = function iniciarFormularioModoCadastro() {
			_this.formulario.find('#tiulo').focus();
			_this.popularTiposDeQuestionarios();
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


		// Configura os eventos do formulário
		_this.configurar = function configurar(status = false) {
			_this.definirForm(status);
		};
	}; // ControladoraFormQuestionario

	// Registrando
	app.ControladoraFormQuestionario = ControladoraFormQuestionario;
})(window, app, jQuery, toastr);


var url = window.location.href;
