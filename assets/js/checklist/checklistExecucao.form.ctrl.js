/**
 *  checklistExecucao.form.ctrl.js
 *
 *  @author  Rafael Vinicius Barros Ferreira
 *	 @version 1.0
 */
(function(window, app, $, toastr)
{
	'use strict';

	function ControladoraFormChecklistExecucao(servicoChecklist) {
		var _this = this;

		_this.alterar;
		_this.formulario = $('#executarchecklist_form');
        _this.botaoSubmissao = $('#salvar');
		_this.idChecklist = window.location.href.split('#')[1].substring(1, url.length).split('/')[1];
		_this.questionamentos = null;
		_this.indiceQuestionamentos =0;

		// Cria as opções de validação do formulário
		var criarOpcoesValidacao = function criarOpcoesValidacao() {
			var opcoes = {
				rules: {
					"titulo": { rangelength : [ 2, 100 ] },
					'descricao':{ rangelength : [10,255] }
				},

				messages: {
					"titulo": { rangelength : $.validator.format("O campo nome deve ter no mínimo  {2} e no máximo {100} caracteres.") },
					"descricao": { rangelength : $.validator.format("O campo nome deve ter no mínimo  {0} e no máximo {255} caracteres.") }
				}
			};
			// Irá disparar quando a validação passar, após chamar o método validate().
			opcoes.submitHandler = function submitHandler(form) {
				var obj = _this.conteudo();
				
				var terminado = function() {
					_this.formulario.desabilitar(false);
				};
				
				_this.formulario.desabilitar(true);
			
				var jqXHR = _this.alterar ? servicoChecklist.atualizar(obj) : servicoChecklist.adicionar(obj);
				jqXHR.done(function() {
					router.navigate('/configuracao');
					toastr.success('Checklist Adicionado com sucesso!')
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
			var dataLimite = moment(_this.dataLimite.getDate()).format('YYYY-MM-DD');

			return servicoChecklist.criar(
				$('#id').val(),
				$('#titulo').val(),
				$('#descricao').val(),
				$('#tipo-checklist').val(),
				dataLimite.toString() + ' ' + $('#hora').val(),
				$('#responsavel option:selected').val(),
				$('#setor option:selected').val(),
				$('#unidade option:selected').val(),
				$('#questionarios').formSelect('getSelectedValues')
			);
		};

		_this.configurarBotoes = function configurarBotoes() {
			_this.botaoSubmissao.on('click', _this.salvar);
		};

		_this.iniciarFormularioModoCadastro = function iniciarFormularioModoCadastro() {
			_this.buscarQuestionamentos();
			_this.configurarBotoes();
		};

		_this.popularQuestao = function popularQuestao() {
			var objetoAtual = _this.questionamentos[_this.indiceQuestionamentos];
			let html = '';
			html += '<div class="row form-row questionamento">'
				html +='<div class="col col-sm-12 col-md-12 col-lg-12 col-12 mb-0-dto">';

					html +='<div class="card-panel left-align pergunta">';
						html += '<input type= "hidden" class="id"  name="questionamento_' + objetoAtual.id + '" value ="'+ objetoAtual.id  +'">';

						html += '<div class="row form-row mb-0-dto">'
							html +='<div class="col col-sm-12 col-md-12 col-lg-12 col-12 mb-0-dto">';
								html +='<p class="mb-0-dto">';
								html +='<strong class="fw-700-dto">'+(_this.indiceQuestionamentos+1)+'</strong> - ' + objetoAtual.formularioPergunta.pergunta;
								html +='</p>';
							html +='</div>';
						html +='</div>';

						html += '<div class="row form-row mb-0-dto">'
							html +='<div class="col col-sm-4 col-md-4 col-lg-2 col-4 d-flex justify-content-center">';
								html +='<input class="cb-dto opcao" type="radio" id="bom" name="opcao" value="bom">';
								html +='<label class="label-dto" for="bom">';
								html +='<i class="mdi mdi-emoticon-happy-outline large orange-text text-accent-4"></i>';
								html +'</label>'
							html +='</div>';

							html +='<div class="col col-sm-4 col-md-4 col-lg-2 col-4 d-flex justify-content-center">';
								html +='<input class="cb-dto regular opcao" type="radio" id="regular" name="opcao" value="regular">';
								html +='<label class="label-dto" for="regular">';
								html +='<i class="mdi mdi-emoticon-neutral-outline large orange-text text-accent-4"></i>';
								html +'</label>'

							html +='</div>';

							html +='<div class="col col-sm-4 col-md-4 col-lg-2 col-4 d-flex justify-content-center">';
								html +='<input class="cb-dto opcao" type="radio" id="ruim" name="opcao" value="ruim">';
								html +='<label class="label-dto" for="ruim">';
								html +='<i class="mdi mdi-emoticon-sad-outline large orange-text text-accent-4"></i>';
								html +'</label>'
							html +='</div>';
						html +='</div>';

					html +='</div>';

					html += '<div class="opcoes_questionamento col col-sm-12 col-md-12 col-lg-12 col-12 center-align" style="display: none">';
						html += '<div class="row form-row ">'
							html += '<div class="col col-2 col-sm-2 col-lg-2 col-md-2 igs">';
									html += '<div class="element orange accent-4 subicon-dto">';
									html += '<i class="mdi mdi-information-outline white-text"></i></i><span class="name toltip" title="Nenhum arquivo selecionado.">Nenhum arquivo...</span>';
									html += '<input type="file" name="pergunta_foto" id="pergunta_foto" accept="image/*">';
									html += '</div>';
			
								// html += '<a class="orange accent-4 subicon-dto">';
								// html += '<i class="mdi mdi-information-outline white-text"></i>';
								// html += '</a>';
							html += '</div>';
							
							html += '<div class="col col-2 col-sm-2 col-lg-2 col-md-2 igs">';
									html += '<a class="orange accent-4 subicon-dto">';
									html += '<i class="mdi mdi-microphone white-text"></i>';
									html += '</a>';
							html += '</div>';
							
							html += '<div class="col col-2 col-sm-2 col-lg-2 col-md-2 igs">';
									html += '<a class="orange accent-4 subicon-dto">';
									html += '<i class="mdi mdi-camera-outline white-text"></i>';
									html += '</a>';
							html += '</div>';

							html += '<div class="col col-2 col-sm-2 col-lg-2 col-md-2 igs">';
								html += '<a class="orange accent-4 subicon-dto">';
								html += '<i class="mdi mdi-lead-pencil white-text"></i>';
								html += '</a>';
							html += '</div>';
							
							html += '<div class="col col-2 col-sm-2 col-lg-2 col-md-2 igs">';
								html += '<a href="pa-cadastro.html" class="orange accent-4 subicon-dto">';
								html += '<span class="white-text">P.A.</span>';
								html += '</a>';
							html += '</div>';

							html += '<div class="col col-2 col-sm-2 col-lg-2 col-md-2 igs">';
								html += '<a class="orange accent-4 subicon-dto">';
								html += '<span class="white-text">Pend</span>';
								html += '</a>';
							html += '</div>';
						html += '</div>';
					html += '</div>';
			html += '</div>';
			
					
			_this.formulario.find('.perguntas').empty().append(html);


			_this.formulario.find('input[type="radio"]').on('change',function(e){
				if(this.value != "bom"){
					_this.formulario.find('.opcoes_questionamento').show(100);
					$('.modal').find('#nome-categoria').html(objetoAtual.checklist.titulo);
					$('.modal').modal();
				}else {
					_this.formulario.find('.opcoes_questionamento').hide(100);
				}
			});
		}

		_this.buscarQuestionamentos  =  function buscarQuestionamentos(valor = 0)
		{
			var sucesso = function (resposta) {
				_this.questionamentos = resposta.conteudo;

				_this.popularQuestao();

			};

			var erro = function(resposta)
			{
				var mensagem = jqXHR.responseText || 'Erro ao popular select de farmácias.';
				toastr.error(mensagem);
				return false;
			}

			var  jqXHR = servicoChecklist.questionamentosComID(_this.idChecklist);
			jqXHR.done(sucesso).fail(erro);
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

			var data  = obj.dataLimite.split('/');
			var hora = obj.dataLimite.split(' ')[1].split(':');

			_this.formulario.find('#id').val(obj.id);
			_this.formulario.find('#titulo').val(obj.titulo);
			_this.formulario.find('#descricao').val(obj.descricao);
			_this.formulario.find('#setor').val(obj.setor.id);
			_this.formulario.find('#loja').val(obj.loja.id);


			dataPicker.set('select', new Date(data[0], data[1], data[2]))
			horaPicker.set('select', hora[0] + ':' + hora[1], { format: 'hh:i' })
		};

		_this.salvar = function salvar() {
			_this.formulario.validate(criarOpcoesValidacao());
        };

		// Configura os eventos do formulário
		_this.configurar = function configurar(status = false) {
            _this.definirForm(status);
		};
	}; // ControladoraFormChecklistExecucao

	// Registrando
	app.ControladoraFormChecklistExecucao = ControladoraFormChecklistExecucao;

})(window, app, jQuery, toastr);