/**
 *  resposta.form.ctrl.js
 *
 *  @author  Rafael Vinicius Barros Ferreira
 *	 @version 1.0
 */
(function(window, app, $, toastr)
{
	'use strict';

	function ControladoraFormResposta(servicoResposta) {
		var _this = this;

		_this.alterar;
		_this.formulario = $('#repostas_form');
		_this.botaoSubmissao = $('#salvar')
        _this.cancelarModoEdicao = $('#cancelar_edicao');
        _this.idTarefa = window.location.href.split('#')[1].substring(1, url.length).split('/')[1];	


		// Cria as opções de validação do formulário
		var criarOpcoesValidacao = function criarOpcoesValidacao() {
			var opcoes = {
				rules: {
					"categoria": {required : true},
					"lojas" :{required : true},
					"data_limite" : { required : true},
					"hora_limite" : { required : true},
					"descricao" : {required : true}

				},

				messages: {
					"categoria": {
						required    : "O campo categoria é obrigatório.",
					},
					"lojas": {
						required    : "O campo lojas é obrigatório.",
					},
					"data_limite": {
						required    : "O campo data limite é obrigatório.",
					},
					"hora_limite": {
						required    : "O campo hora limite é obrigatório.",
					},
					"descricao": {
						required    : "O campo descrição é obrigatório.",
					}
				}
			};
			// Irá disparar quando a validação passar, após chamar o método validate().
			opcoes.submitHandler = function submitHandler(form) {
				_this.formulario.desabilitar(false);


				var erro = function erro(jqXHR, textStatus, errorThrown) {
					var mensagem = jqXHR.responseText;
					_this.formulario.find('#msg').empty().append('<div class="error" >' + mensagem + '</div>');
				};
				
				var terminado = function() {
					_this.formulario.desabilitar(true);
				};
				
				var obj = _this.conteudo();
				var jqXHR = _this.alterar ? servicoResposta.atualizar(obj) : servicoResposta.adicionar(obj);
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
			return servicoResposta.criar(
                $('#id').val(),
                $('#descricao').val(),
				$('#data_limite').pickadate('picker').get('select', 'yyyy-mm-dd') + ' ' + $('#hora_limite').pickatime('picker').get('select','HH:i'),
				$('#categoria').val(),
				$('#loja').val()
			);
        };

        _this.pergutasParaHtml = function pergutasParaHtml(perguntas){
    		var html = '';
            var opcao = new app.Opcao();

            for(var posicao in perguntas){
				html +=  ' <div class="row form-row">';
				html += '<div class="col-xs-12  col-sm-12 col-md-12 col-12">';
				html += '<fieldset class="form-group row">';

                html += '<legend class="col-form-legend col-xs-12  col-sm-12 col-md-12 col-12">' + perguntas[posicao].pergunta + '</legend>';

                for(var posicaoOp in opcao.getpcoes()){
					html += '<div class="col-xs-12  col-sm-12 col-md-12 col-12">';
                    html += '<div class="form-check">';
					html += ' <label class="form-check-label">';
					html += ' <input class="form-check-input" type="radio" name="pergunta_' + perguntas[posicao].id + '" id="pergunta_' + opcao.getpcoes()[posicaoOp] + '"value="'  + posicaoOp + '" />';
					html += opcao.getpcoes()[posicaoOp];
					html += '</label>';
					html += '</div>';
					html += '</div>';    
				}
				
								
				html += '</fieldset>';
				html += '</div>';    
				html += '</div>';

				html += '<div class="row form-row">';
				html += '<div class="col-xs-12  col-sm-12 col-md-12 col-12">';
				html += '<input id="input-44" name="input44[]" type="file" class="file file_input" data-show-preview="false" multiple>';
				html += '</div>';
				html += '</div>';

            }

            return html;
        };
        
        _this.popularPerguntas  =  function popularPerguntas(valor = 0) {
			var sucesso = function (resposta) {
				_this.formulario.find('.perguntas:first').append(_this.pergutasParaHtml(resposta.data));
			};

			var erro = function(resposta)
			{
				var mensagem = jqXHR.responseText || 'Erro ao popular select de farmácias.';
				toastr.error(mensagem);
				return false;
			}
			var servicoPerguta = new app.ServicoPergunta();
			var  jqXHR = servicoPerguta.comTarefaId( _this.idTarefa);
			jqXHR.done(sucesso).fail(erro);
		};


		_this.configurarBotoes = function configurarBotoes() {
			_this.botaoSubmissao.on('click', _this.salvar);
			_this.cancelarModoEdicao.on('click', _this.cancelar);
		};


		_this.iniciarFormularioModoCadastro = function iniciarFormularioModoCadastro() {
			_this.formulario.parents('#painel_formulario').removeClass('desabilitado').desabilitar(false);
			_this.formulario.parents('#painel_formulario').removeClass('d-none');
            _this.configurarBotoes();
            _this.popularPerguntas();
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
			var dataPicker = $('#data_limite').pickadate('picker');
			var horaPicker = $('#hora_limite').pickatime('picker');


			var data  = obj.dataLimite.split('-');
			var hora = obj.dataLimite.split(' ')[1].split(':');

			_this.formulario.find('#id').val(obj.id);
			_this.formulario.find('#categoria').val(obj.categoria.id).trigger('change');
			_this.formulario.find('#loja').val(obj.categoria.id).trigger('change');
			_this.formulario.find('#descricao').val(obj.descricao);

			dataPicker.set('select', new Date(data[0], data[1], data[2]))
			horaPicker.set('select', hora[0] + ':' + hora[1], { format: 'hh:i' })

		};

		_this.salvar = function salvar() {
			// _this.formulario.validate(criarOpcoesValidacao());

			var files = $('.file_input').fileinput('getFileStack'); // returns file list selected

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
			$('#data_limite').on('click', function(event){
				event.preventDefault();
			});
		};
	}; // ControladoraFormResposta

	// Registrando
	app.ControladoraFormResposta = ControladoraFormResposta;

})(window, app, jQuery, toastr);


var url = window.location.href;
