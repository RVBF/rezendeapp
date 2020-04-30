/**
 *  Questionario.form.ctrl.js
 *
 *  @author  Rafael Vinicius Barros Ferreira
 *	 @version 1.0
 */
(function (window, app, $, toastr) {
	'use strict';

	function ControladoraFormQuestionario(servicoQuestionario, controladoraListagemQuestionario) {
		var _this = this;

		_this.alterar;
		_this.formulario = $('#questionario_form');
		_this.botaoSubmissao = $('#salvar');

		var pegarId = function pegarId(url, palavra) {

			// Terminando com "ID/palavra"
			var regexS = palavra + '+\/[0-9]{1,}';

			var regex = new RegExp(regexS);
			var resultado = regex.exec(url);

			if (!resultado || resultado.length < 1) {
				return 0;
			}

			var array = resultado[0].split('/');
			return array[1];
		};

		// Cria as opções de validação do formulário
		var criarOpcoesValidacao = function criarOpcoesValidacao() {
			var opcoes = {
				rules: {
					"titulo": {
						required: true,
						rangelength: [2, 100]
					},

				},

				messages: {
					"titulo": {
						required: "O campo título é obrigatório.",
						rangelength: "O campo deve conter no mínimo {2} e no máximo {100} caracteres."
					}
				}
			};
			// Irá disparar quando a validação passar, após chamar o método validate().
			opcoes.submitHandler = function submitHandler(form) {
				var obj = _this.conteudo();
				var terminado = function () {
					_this.formulario.desabilitar(false);
				};

				_this.formulario.desabilitar(true);
				var jqXHR = (window.location.href.search('editar') != -1) ? servicoQuestionario.atualizar(obj) : servicoQuestionario.adicionar(obj);

				jqXHR.done(function (resposta) {
					if (resposta.status) {
						router.navigate('/questionarios');
						toastr.success(resposta.mensagem)
					} else {
						if (resposta != undefined && resposta.mensagem) $('body #msg').empty().removeClass('d-none').append(resposta.mensagem).focus();
						if (resposta != undefined && resposta.mensagem) toastr.error(resposta.mensagem);
					}
				}).fail(window.erro).always(terminado);
			}; // submitHandler

			return opcoes;
		};

		// Obtém o conteúdo atual do form como um objeto
		_this.conteudo = function conteudo() {
			var configuracaoes = { perguntas: [] };

			if (_this.formulario.find('.pergunta').length > 0) {
				$('.ids').each(function () {
					var id = $(this).val();
					configuracaoes.perguntas.push({ id: id, pergunta: $('#pergunta_' + id).val(), comentario: $('#comentario-pergunta').val() });
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
			var tiposQuestionarios = servicoLoja.getTipoQuestionario();

			$("#tipo-questionario").empty();

			$.each(tiposQuestionarios, function (i, item) {
				$("#tipo-questionario").append($('<option>', {
					value: item,
					text: item
				}));
			});

			$("#tipo-questionario").formSelect();
		};

		_this.adiconarPergunta = function () {
			var quantidade = $(this).parents('form').find('.perguntas').find('.pergunta').length + 1;
			var html = '<div class="pergunta">';
			html += '<input type= "hidden" class="ids"  name="pergunta_' + quantidade + '" value ="' + quantidade + '">';
			html += '<div class="row form-row">';
			html += '<div class="col col-lg-9 col-md-9 col-sm-9 col-12">';
			html += ' <div class="input-field">';
			html += '<input type="text" class="form-control campo_obrigatorio" id="pergunta_' + quantidade + '" name="pergunta_' + quantidade + '">';
			html += '<label for="pergunta_' + quantidade + '">Pergunta nº ' + quantidade + ':</label>';
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
			$('.adicionar_pergunta:last').on('click', _this.adiconarPergunta);
			$('.remover_pergunta:last').on('click', _this.removerPergunta);
		};

		_this.removerPergunta = function () {
			var quantidade = $(this).parents('.perguntas').find('.pergunta').length;

			if (quantidade > 1) $(this).parents('.pergunta').remove();
		};

		_this.configurarBotoes = function configurarBotoes() {
			_this.botaoSubmissao.on('click', _this.salvar);

			$('body').find('.adicionar_pergunta').on('click', _this.adiconarPergunta);
			$('.remover_pergunta:last').on('click', _this.removerPergunta);
		};

		_this.definirForm = function definirForm(status) {
			_this.formulario.submit(false);

			_this.formulario.find('#titulo').focus();

			this.popularTiposDeQuestionarios();
			_this.configurarBotoes();

			if (window.location.href.search('visualizar') != -1) {
				$('#msg').empty();
				servicoQuestionario.comId(pegarId(window.location.href, 'visualizar-questionario')).done(_this.desenhar);
			}
			else if (window.location.href.search('editar') != -1) {
				$('#msg').empty();
				servicoQuestionario.comId(pegarId(window.location.href, 'editar-questionario')).done(_this.desenhar);
			} else {
				_this.formulario.find('#botoes').prepend(' <div class="col col-md-4 col-12 col-sm-5 col-lg-4"><button type="submit" id="cadastrar" class="waves-effect waves-light btn white grey-text text-darken-4 col-12 quebra-linha"><i class="mdi mdi-checkbox-marked-circle-outline orange-text text-accent-4 "></i>Cadastrar</button></div>').promise().done(function () {
					$('#botoes').find('#cadastrar').on('click', _this.salvar);
				});
			}
		}

		// Desenha o objeto no formulário
		_this.desenhar = function desenhar(resposta) {
			_this.obj = resposta.conteudo;
			$('#id').val(_this.obj.id).focus().blur();
			$('#titulo').val(_this.obj.titulo).focus().blur();
			$('#descricao').val(_this.obj.descricao).focus().blur();

			for (var index in _this.obj.formulario.perguntas) {
				var elemento = _this.obj.formulario.perguntas[index];
				if (index == 0) {
					$('#pergunta_1').val(elemento.pergunta).focus().blur();
				} else {
					var html = '<div class="pergunta">';
					html += '<input type= "hidden" class="ids"  name="pergunta_' + elemento.id + '" value ="' + elemento.id + '">';
					html += '<div class="row form-row">';
					html += '<div class="col col-lg-9 col-md-9 col-sm-9 col-12">';
					html += ' <div class="input-field">';
					html += '<input type="text" class="form-control campo_obrigatorio" id="pergunta_' + elemento.id + '" name="pergunta_' + elemento.id + '" value="' + elemento.pergunta + '">';
					html += '<label for="pergunta_' + elemento.id + '">Pergunta nº ' + elemento.id + ':</label>';
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

					_this.formulario.find('.perguntas').append(html).promise().done(function () {
						$(this).find('.pergunta:last').find('#pergunta_' + elemento.id).focus().blur();

						$('.adicionar_pergunta:last').on('click', _this.adiconarPergunta);
						$('.remover_pergunta:last').on('click', _this.removerPergunta);
					});
				}

			}

			if (window.location.href.search('visualizar') != -1) {
				_this.formulario.desabilitar(true);
				_this.formulario.find('#botoes').desabilitar(false);
				_this.formulario.find('#botoes').prepend(' <div class="col col-md-4 col-12 col-sm-5 col-lg-4"><button type="submit" id="remover" class="waves-effect waves-light btn white grey-text text-darken-4 col-12 quebra-linha"><i class="mdi mdi-delete red-text text-darken-4"></i>Remover</button></div>').promise().done(function () {
					$('#botoes').find('#remover').on('click', _this.remover);
				});
				_this.formulario.find('#botoes').prepend(' <div class="col col-md-4 col-12 col-sm-5 col-lg-4"><button type="button" id="editar" class="waves-effect waves-light btn white grey-text text-darken-4 col-12 quebra-linha"><i class="mdi mdi-checkbox-marked-circle-outline orange-text text-accent-4 "></i>Editar</button></div>').promise().done(function () {
					_this.formulario.find('#editar').on('click', function (event) {
						router.navigate('/editar-questionario/' + _this.obj.id);
					});
				});

			} else if (window.location.href.search('editar') != -1) {
				_this.alterar = true;
				var html = '';
				html += '<div class="col col-md-4 col-12 col-sm-5 col-lg-4">';
				html += '<button id="salvar" type="submit" class="waves-effect waves-light btn white grey-text text-darken-4 col-12 quebra-linha">';
				html += '<i class="mdi mdi-checkbox-marked-circle-outline orange-text text-accent-4 ">';
				html += '</i>salvar</button>';
				html += '</div>';

				_this.formulario.find('#botoes').prepend(html).promise().done(function () {
					$('#salvar').on('click', _this.salvar);
				});
			}

		};

		_this.salvar = function salvar() {
			_this.formulario.validate(criarOpcoesValidacao());
		};

		_this.remover = function remover() {
			BootstrapDialog.show({
				type: BootstrapDialog.TYPE_DANGER,
				title: 'Deseja remover este Questionário?',
				message: 'Id: ' + _this.obj.id + '.<br> Título: ' + (_this.obj.titulo + '.<br> Descrição : ' + _this.obj.descricao) + '.',
				size: BootstrapDialog.SIZE_LARGE,
				buttons: [{
					label: '<u>S</u>im',
					hotkey: 'S'.charCodeAt(0),
					action: function (dialog) {
						servicoQuestionario.remover(_this.obj.id).done(function (resposta) {
							if (resposta.status) {
								router.navigate('/questionarios');
								toastr.success(resposta.mensagem);
								dialog.close();
							}
							else {
								if (resposta != undefined && resposta.mensagem) toastr.error(resposta.mensagem);

								dialog.close();
							}
						});
					}
				}, {
					label: '<u>N</u>ão',
					hotkey: 'N'.charCodeAt(0),
					action: function (dialog) {
						dialog.close();
					}
				}
				]
			});
		};

		// Configura os eventos do formulário
		_this.configurar = function configurar() {
			_this.definirForm(status);
		};
	}; // ControladoraFormQuestionario

	// Registrando
	app.ControladoraFormQuestionario = ControladoraFormQuestionario;
})(window, app, jQuery, toastr);


var url = window.location.href;
