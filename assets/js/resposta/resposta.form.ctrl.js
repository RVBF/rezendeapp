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
		
		_this.respostas = [];

		// Cria as opções de validação do formulário
		var criarOpcoesValidacao = function criarOpcoesValidacao() {
			var opcoes = {
				rules: {
					// "categoria": {required : true},
					// "lojas" :{required : true},
					// "data_limite" : { required : true},
					// "hora_limite" : { required : true},
					// "descricao" : {required : true}

				},

				messages: {
					// "categoria": {
					// 	required    : "O campo categoria é obrigatório.",
					// },
					// "lojas": {
					// 	required    : "O campo lojas é obrigatório.",
					// },
					// "data_limite": {
					// 	required    : "O campo data limite é obrigatório.",
					// },
					// "hora_limite": {
					// 	required    : "O campo hora limite é obrigatório.",
					// },
					// "descricao": {
					// 	required    : "O campo descrição é obrigatório.",
					// }
				}
			};
			// Irá disparar quando a validação passar, após chamar o método validate().
			opcoes.submitHandler = function submitHandler(form) {
				_this.formulario.desabilitar(true);

				
				var terminado = function() {
					// _this.respostas = [];

					_this.formulario.desabilitar(false);
				};
				
				var obj =  JSON.stringify(_this.conteudo()).toString();

				// obj.replace('[', '{');
				var jqXHR = _this.alterar ? servicoResposta.atualizar(JSON.parse(obj)) : servicoResposta.adicionar(JSON.parse(obj));
				jqXHR.done(function(data, textStatus, jqXHR){
					window.sucessoParaFormulario(data, textStatus, jqXHR);
					if(data.status) router.navigate('/tarefa')
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
			var perguntas = $('.pergunta');

			perguntas.each(function(){
				var temResposta = false;
				var pergunta = $(this);
				var id = pergunta.find('.ids:first').val();
				var opcao = $('[name=pergunta_' + id + ']:checked').val();
				for(var posicaoAtual in _this.respostas) {
					var atual = _this.respostas[posicaoAtual];

					if(atual.pergunta == id){
						atual.opcaoSelecionada = opcao;
						temResposta = true;
						atual.comentario = $('#pergunta_comentario_' + id).val();
						_this.respostas[posicaoAtual] = atual;
						break;
					}
				};

				if(!temResposta){
					var obj = new app.Resposta();
					obj.pergunta = id;
					obj.opcaoSelecionada = opcao;
					obj.comentario = $('#pergunta_comentario_' + id).val();
					_this.respostas.push(obj);
				}
			});

			return _this.respostas;
        };

        _this.pergutasParaHtml = function pergutasParaHtml(perguntas){
    		var html = '';
			var opcao = new app.Opcao();
		
            for(var posicao in perguntas){
				html += ' <div class="pergunta">';

				html += ' <div class="row form-row">';
				html += '<div class="col-xs-12  col-sm-12 col-md-12 col-12">';
				html += '<input type= "hidden" class="ids" name="pergunta_id_' + perguntas[posicao].id + '" id="pergunta_id_' + perguntas[posicao].id + '" value ="'+  perguntas[posicao].id +'">';

				html += '<div class="row">';
				html += '<div class="col-xs-12  col-sm-12 col-md-12 col-12">';

                html += '<legend class="col-form-legend">' + perguntas[posicao].pergunta + '</legend>';
				html += '</div>';
				html += '</div>';    


                for(var posicaoOp in opcao.getpcoes()){
					html += '<div class="form-check row">';
					html += '<div class="col-xs-12  col-sm-12 col-md-12 col-12">';

								if(posicaoOp == 1) html += '<input class="form-check-input radio-inline" type="radio" name="pergunta_' + perguntas[posicao].id + '" id="pergunta_' + perguntas[posicao].id + '_'+ opcao.getpcoes()[posicaoOp] + '" value="'  + posicaoOp + '" checked="checked"/>';
								else html += '<input class="form-check-input radio-inline" type="radio" name="pergunta_' + perguntas[posicao].id + '" id="pergunta_' + perguntas[posicao].id + '_'+ opcao.getpcoes()[posicaoOp] + '" value="'  + posicaoOp + '"/>';					
								html += ' <label class="radio-inline control-label" for="pergunta_' + perguntas[posicao].id + '">';
								html += opcao.getpcoes()[posicaoOp];
								html += '</label>';
					html += '</div>';    

					html += '</div>';
				}	
				html += '</div>';    
				html += '</div>';

				html += '<div class="row form-row">';
				html += '<div class="col-xs-12  col-sm-12 col-md-12 col-12">';
				html += '<label for="pergunta_comentario_' + perguntas[posicao].id + '">Comentário</label>';
				html += '<textarea class="form-control" rows="3" name="pergunta_comentario_' + perguntas[posicao].id + '" id="pergunta_comentario_' + perguntas[posicao].id + '" ></textarea>';
				html += '</div>';
				html += '</div>';


				html += '<div class="row form-row">';
				html += '<div class="col-xs-2  col-sm-2 col-md-2 col-12">';
				html += '<div class="element">';
				html += '<i class="fas fa-camera"></i></i><span class="name toltip" title="Nenhum arquivo selecionado.">Nenhum arquivo...</span>';
				html += '<input type="file" name="pergunta_foto_' + perguntas[posicao].id + '" id="pergunta_foto_' + perguntas[posicao].id + '" accept="image/*">';
				html += '</div>';
				html += '</div>';

				html += '<div class="col-xs-2  col-sm-2 col-md-2 col-12">';
				html += '<div class="element">';
				html += '<i class="fas fa-file-audio"></i><span class="name toltip" title="Nenhum arquivo selecionado.">Nenhum arquivo...</span>';
				html += '<input type="file" name="pergunta_audio_' + perguntas[posicao].id + '" id="pergunta_audio_' + perguntas[posicao].id + '" accept="audio/*">';
				html += '</div>';
				html += '</div>';
				
				html += '</div>';
				html += '</div>';
			}

            return html;
        };
        
        _this.popularPerguntas  =  function popularPerguntas(valor = 0) {
			var sucesso = function (resposta) {
				_this.formulario.find('.perguntas').append(_this.pergutasParaHtml(resposta.data));

				$('input[type="file"]').change(function(evt){
					var elemento = $(this);
					var file = evt.target.files[0];
					var reader = new FileReader();
					var idPergunta = elemento.attr('name').split('_')[2];
					
					var nomeArquivo = $(this).val().split('\\');
					nomeArquivo = nomeArquivo[nomeArquivo.length -1];

					reader.readAsDataURL(file);

					reader.onload = function () {
						if(_this.respostas.length > 0){
							var estaAdicionado = false;
							for(var posicaoAtual in _this.respostas){
								var atual = _this.respostas[posicaoAtual];
								if(atual.pergunta == idPergunta){
									atual.files.push({'nome': nomeArquivo, 'arquivo': reader.result, 'tipo' : file.type});
									estaAdicionado = true;
									_this.respostas[posicaoAtual] = atual;
									break;
								}
							}
							
							if(!estaAdicionado) {
								var resposta = new app.Resposta();
								resposta.pergunta = idPergunta;
								resposta.files.push({'nome': nomeArquivo, 'arquivo': reader.result, 'tipo' : file.type});
								_this.respostas.push(resposta);
							}
						}
						else{
							
							var resposta = new app.Resposta();
							resposta.pergunta = idPergunta;
							resposta.files.push({'nome': nomeArquivo,'arquivo': reader.result, 'tipo' : file.type});
							_this.respostas.push(resposta);
						}
					};
					reader.onerror = function (error) {
					};
				});

	
				$('i').on('click', function () {
					$(this).parents('.element').find("input[type='file']").trigger('click');
				});
		
				$('input[type="file"]').on('change', function() {
					var val = $(this).val().split('\\');
					val = val[val.length -1];

					$(this).siblings('span').attr('data-original-title', val)
					$(this).siblings('span').html(val.substring( 0, 12) + '...');
				});
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

			$('body').find('#voltar').on('click', function (event) {
				event.preventDefault();

				router.navigate('/tarefa');
			});

			$('body').find('#atualizar').on('click', function(){
				location.reload();
			});
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
			router.navigate('/tarefa');
		};

		// Configura os eventos do formulário
		_this.configurar = function configurar(status = false) {
			_this.definirForm(status);
			$('.select2').select2({
				theme: 'bootstrap4',
				width: '100%',
			});
			$('#data_limite').on('click', function(event){
				event.preventDefault();
			});
		};
	}; // ControladoraFormResposta

	// Registrando
	app.ControladoraFormResposta = ControladoraFormResposta;

})(window, app, jQuery, toastr);


var url = window.location.href;
