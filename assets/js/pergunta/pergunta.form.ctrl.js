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
        
		// Obtém o conteúdo atual do form como um objeto
		_this.conteudo = function conteudo() {

			if(_this.formulario.find('.pergunta').length > 0)
			{
				var objetos = {data : []};
				$('.ids').each(function(){
					var id = $(this).val();
					objetos.data.push({id:0, pergunta: $('#pergunta_'+ id).val()});
				});

				return objetos;
			}
			else{
				return servicoPergunta.criar(
					$('#id').val(),
					$('#pergunta').val()
				);
			}
        };

		_this.configurarBotoes = function configurarBotoes() {
			_this.botaoSubmissao.on('click', _this.salvar);
			_this.cancelarModoEdicao.on('click', _this.cancelar);
			$('body').find('.adicionar_pergunta').on('click', _this.adiconarPergunta);
			$('body').find('#voltar').on('click', function (event) {
				event.preventDefault();

				router.navigate('/tarefa');
			});
			$('body').find('.remover_pergunta').on('click', _this.removerPergunta);
			$('body').find('#atualizar').on('click', function(){
				location.reload();
			});
		};

		_this.iniciarFormularioModoCadastro = function iniciarFormularioModoCadastro() {
			_this.formulario.parents('#painel_formulario').removeClass('desabilitado').desabilitar(false);
			_this.formulario.parents('#painel_formulario').removeClass('d-none');
			_this.formulario.parents('#painel_formulario').promise().done(function(){
				_this.formulario.find('#pergunta').focus();
				_this.configurarBotoes();
			});
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
			_this.formulario.validate({
				submitHandler : function(form){
					var obj = _this.conteudo();

					_this.formulario.desabilitar(true);
					
					var terminado = function() {
						_this.formulario.desabilitar(false);
					};
					
					var jqXHR;

					if(_this.alterar){
						jqXHR = servicoPergunta.atualizar(obj, _this.idTarefa)
					}
					else{
						if(_this.formulario.find('.pergunta').length > 0){
							var objetos  = _this.conteudo();
							jqXHR =  servicoPergunta.adicionarTodas(objetos, _this.idTarefa);
							router.navigate('/tarefa');
						}
						else{
							jqXHR = servicoPergunta.adicionar(obj, _this.idTarefa);
						}
					}

					jqXHR.done(window.sucessoParaFormulario).fail(window.erro).always(terminado);
	
					if(_this.alterar){
						$('.depende_selecao').each(function(){
							$(this).prop('disabled', true);
						});
					}
					
				}
			});
        };
		
		_this.cancelar = function cancelar(event) {
			var contexto = _this.formulario.parents('#painel_formulario');
			contexto.addClass('desabilitado');
			_this.formulario.find('.msg').empty();
			_this.formulario.find('.msg').parents('.row').addClass('d-none');
			contexto.addClass('d-none');
			contexto.desabilitar(true);
			router.navigate('/tarefa');
		};
		
		_this.adiconarPergunta = function(){
			var quantidade = $(this).parents('form').find('.perguntas').find('.pergunta').length + 1;
			var html  = '<div class="pergunta">';
			html += '<input type= "hidden" class="ids"  name="pergunta_' + quantidade + '" value ="'+ quantidade +'">';
			html += '<div class="row form-row">';
			html += '<div class="col-xs-11 col-md-11 col-sm-11 col-11">';
			html += '<label for="pergunta_' + quantidade + '">Pergunta :</label>';
			html += '<input type="text" class="form-control campo_obrigatorio" id="pergunta_' + quantidade + '" name="pergunta_' + quantidade + '">';
			html += '</div>';

			html += '<div class="col-xs-1 col-md-1 col-sm-1 col-1">';
			html += '<div class="bnt_campoextra">';
			html += '<div class="row">';

			html += '<div class="col-xs-4 col-md-4 col-sm-4 col-4">';
			html += '<button aria-hidden="true" role="presentation" type="button" aria-label="Adicionar Pergunta" class="btn bnt_opcoesformulario btn-sm btn-success adicionar_pergunta"><i class="fas fa-plus"></i></button>';
			html += '</div>';

			html += '<div class="col-xs-4 col-md-4 col-sm-4 col-4">';          
			html += '<button aria-hidden="true" role="presentation" type="button" aria-label="Remover Pergunta" class="btn bnt_opcoesformulario btn-sm btn-danger remover_pergunta"><i class="fas fa-minus"></i></button>';
			html += '</div>';
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
		// Configura os eventos do formulário
		_this.configurar = function configurar(status = false) {
			_this.definirForm(status);
		};
	}; // ControladoraFormPergunta

	// Registrando
	app.ControladoraFormPergunta = ControladoraFormPergunta;

})(window, app, jQuery, toastr);