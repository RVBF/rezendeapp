/**
 *  pergunta.form.ctrl.js
 *
 *  @author  Rafael Vinicius Barros Ferreira
 *	 @version 1.0
 */
(function(window, app, $, toastr)
{
	'use strict';

	function ControladoraFormPergunta(servicoPergunta) {
		var _this = this;

		_this.alterar;
		_this.formulario = $('#pergunta_form');
		_this.botaoSubmissao = $('#salvar');
        _this.cancelarModoEdicao = $('#cancelar_edicao');
		_this.idTarefa = window.location.href.split('#')[1].substring(1, url.length).split('/')[1];	

		// Cria as opções de validação do formulário
		var criarOpcoesValidacao = function criarOpcoesValidacao() {
			var opcoes = {
				rules: {
					"titulo": {
						required    : true,
						rangelength : [ 2, 85 ]
					},

					'descricao':{
						rangelength : [10,255]
					}
				},

				messages:
				{
					"titulo": {
						required    : "O campo título é obrigatório.",
						rangelength : $.validator.format("O campo nome deve ter no mínimo  {2} e no máximo {85} caracteres.")
					},

					"descricao": {
						rangelength : $.validator.format("O campo nome deve ter no mínimo  {10} e no máximo {255} caracteres.")
					}
				}
			};
			// Irá disparar quando a validação passar, após chamar o método validate().
			opcoes.submitHandler = function submitHandler(form) {
				_this.formulario.desabilitar(true);
				
				var terminado = function() {
					_this.formulario.desabilitar(false);
				};
				
				var obj = _this.conteudo();
				var jqXHR = _this.alterar ? servicoPergunta.atualizar(obj, _this.idTarefa) : servicoPergunta.adicionar(obj, _this.idTarefa);
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
			return servicoPergunta.criar(
				$('#id').val(),
				$('#pergunta').val()
			);
        };
        
        _this.adicionarPergunta = function adicionarPergunta(){

            var quantidade = _this.formulario.find('.perguntas').find('.pergunta').length + 1;

            var html  = '<div class="pergunta">';
            html += '<input type= "hidden" id="ids"  name="pergunta_' + quantidade + '" value ="'+ quantidade +'">';
            html += '<div class="row form-row">';
            html += '<div class="col-xs-11 col-md-11 col-sm-11 col-11">';
            html += '<label for="pergunta_1">Pergunta :</label>';
            html += '<input type="text" class="form-control" id="pergunta_1" name="pergunta_1">';
            html += '</div>';

            html += '<div class="col-xs-1 col-md-1 col-sm-1 col-1">';
            html += '<div class="bnt_campoextra">';
            html += '<div class="row">';

            html += '<div class="col-xs-4 col-md-4 col-sm-4 col-4">';
            html += '<button aria-hidden="true" role="presentation" type="button" aria-label="Adicionar Pergunta" class="btn btn-sm btn-success adicionar_pergunta"><i class="fas fa-plus"></i></button>';
            html += '</div>';

            html += '<div class="col-xs-4 col-md-4 col-sm-4 col-4">';          
            html += '<button aria-hidden="true" role="presentation" type="button" aria-label="Remover Pergunta" class="btn btn-sm btn-danger remover_pergunta"><i class="fas fa-minus"></i></button>';
            html += '</div>';
            html += '</div>';
            
            html += '</div>';
            html += '</div>';
            html += '</div>';
            html += '</div>';

            _this.formulario.find('.perguntas').append(html);
            _this.configurarBotoes(); 
        };

        _this.removerPergunta = function removerPergunta() {
            if(_this.formulario.find('.perguntas').find('.pergunta').length > 1)  $(this).parents('.pergunta').remove();
        };

		_this.configurarBotoes = function configurarBotoes() {
			_this.botaoSubmissao.on('click', _this.salvar);
            _this.cancelarModoEdicao.on('click', _this.cancelar);
            _this.formulario.find('.adicionar_pergunta').on('click', _this.adicionarPergunta);
            _this.formulario.find('.remover_pergunta').on('click', _this.removerPergunta);  
		};

		_this.iniciarFormularioModoCadastro = function iniciarFormularioModoCadastro() {
			_this.formulario.parents('#painel_formulario').removeClass('desabilitado').desabilitar(false);
			_this.formulario.parents('#painel_formulario').removeClass('d-none');
			_this.formulario.find('#pergunta').focus();
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
			_this.formulario.find('#pergunta').val(obj.pergunta);
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
            _this.formulario.find('.adicionar_pergunta').on('click', _this.adicionarPergunta);
            _this.formulario.find('.remover_pergunta').on('click', _this.removerPergunta);  
		};
	}; // ControladoraFormPergunta

	// Registrando
	app.ControladoraFormPergunta = ControladoraFormPergunta;

})(window, app, jQuery, toastr);