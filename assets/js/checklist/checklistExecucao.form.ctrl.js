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
		_this.dataLimite = '';
		_this.horaLimite = '';

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


		_this.popularColaboradores  =  function popularColaboradores(valor = 0)
		{
			var sucesso = function (resposta) {
				$("#responsavel").empty();

				$.each(resposta.data, function(i ,item) {
					$("#responsavel").append($('<option>', {
						value: item.colaborador.id,
						text: item.colaborador.nome  + ' ' + item.colaborador.sobrenome
					}));
				});

				$('#responsavel').formSelect();
			};

			var erro = function(resposta)
			{
				var mensagem = jqXHR.responseText || 'Erro ao popular select de farmácias.';
				toastr.error(mensagem);
				return false;
			}

			var servicoUsuario = new app.ServicoUsuario();
			var  jqXHR = servicoUsuario.todos();
			jqXHR.done(sucesso).fail(erro);
		};

		_this.configurarEventos = function configurarEventos() {
			_this.dataLimite =new Picker($('#data').get()[0], {
				format : 'DD de MMMM de YYYY',
				controls: true,
				inline: true,
				container: '.date-panel',					
				text : {
					title: 'Selecione a data',
					cancel: 'Cancelar',
					confirm: 'OK',
					year: 'Ano',
					month: 'Mês',
					day: 'Dia',
					hour: 'Hora',
					minute: 'Minuto',
					second: 'Segundo',
					millisecond: 'Milissegundos',
				},
				headers : true,
				months : ['Janeiro', 'Fevereiro', 'Março', 'Abril', 'Maio', 'Junho', 'Julho', 'Agosto', 'Setembro', 'Outubro', 'Novembro', 'Dezembro'],
				monthsShort : ['Jan', 'Fev', 'Mar', 'Abr', 'Mai', 'Jun', 'Jul', 'Ago', 'Set', 'Out', 'Nov', 'Dez']
			});

			_this.horaLimite = new Picker($('#hora').get()[0], {
				format: 'HH:mm',
				headers: true,
				controls: true,
				inline: true,
				container: '.time-panel',	
				text : {
					title: 'Selecione a hora',
					cancel: 'Cancelar',
					confirm: 'OK',
					hour: 'Hora',
					minute: 'Minuto',
					second: 'Segundo',
					millisecond: 'Milissegundos',
				},
			});

			_this.botaoSubmissao.on('click', _this.salvar);
		};

		_this.iniciarFormularioModoCadastro = function iniciarFormularioModoCadastro() {
			_this.popularColaboradores();
			_this.buscarQuestionamentos();
			_this.configurarEventos();
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
						html +='<div class="col col-sm-4 col-md-4 col-lg-2 col-4 d-flex justify-content-center justify-content-md-start">';
							html +='<input class="cb-dto opcao" type="radio" id="bom" name="opcao" value="bom">';
							html +='<label class="label-dto" for="bom">';
							html +='<i class="mdi mdi-emoticon-happy-outline large orange-text text-accent-4"></i>';
							html +'</label>'
						html +='</div>';

						html +='<div class="col col-sm-4 col-md-4 col-lg-2 col-4 d-flex justify-content-center justify-content-md-start">';
							html +='<input class="cb-dto regular opcao" type="radio" id="regular" name="opcao" value="regular">';
							html +='<label class="label-dto" for="regular">';
							html +='<i class="mdi mdi-emoticon-neutral-outline large orange-text text-accent-4"></i>';
							html +'</label>'

						html +='</div>';

						html +='<div class="col col-sm-4 col-md-4 col-lg-2 col-4 d-flex justify-content-center justify-content-md-start">';
							html +='<input class="cb-dto opcao" type="radio" id="ruim" name="opcao" value="ruim">';
							html +='<label class="label-dto" for="ruim">';
							html +='<i class="mdi mdi-emoticon-sad-outline large orange-text text-accent-4"></i>';
							html +'</label>'
						html +='</div>';
					html +='</div>';

				html +='</div>';

				html += '<div class="opcoes_questionamento col col-sm-12 col-md-12 col-lg-12 col-12 center-align" style="display: none">';
					html += '<div class="row form-row ">'
						html += '<div class="list-group">';
								html += '<div class="col col-4 col-sm-4 col-lg-2 col-md-2 igs">';
										html += '<a class="list-group-item list-group-item-action orange accent-4 subicon-dto element">';
										html += '<i class="mdi mdi-information-outline white-text"></i>';
										html += '</a>';
								html += '</div>';
								
								html += '<div class="col col-4 col-sm-4 col-lg-2 col-md-2 igs">';
										html += '<a class="list-group-item list-group-item-action orange accent-4 subicon-dto element">';
										html += '<i class="mdi mdi-microphone white-text"><span class="name tooltip" title="Nenhum arquivo selecionado.">Nenhum arquivo...</span></i>';
										html += '<input type="file" name="pergunta_audio" id="pergunta_audio" accept="image/*">';
										html += '</a>';
								html += '</div>';
								
								html += '<div class="col col-4 col-sm-4 col-lg-2 col-md-2 igs">';
										html += '<a class="list-group-item list-group-item-action orange accent-4 subicon-dto element">';
										html += '<i class="mdi mdi-camera-outline white-text"></i>';
										html += '<input type="file" name="pergunta_camera" id="pergunta_camera" accept="image/*">';
										html += '</a>';
								html += '</div>';
						
								html += '<div class="col col-4 col-sm-4 col-lg-2 col-md-2 igs">';
									html += '<a class=" list-group-item list-group-item-action  orange accent-4 subicon-dto element active">';
									html += '<i class="mdi mdi-lead-pencil white-text"></i>';
									html += '</a>';
								html += '</div>';
								
								html += '<div class="col col-4 col-sm-4 col-lg-2 col-md-2 igs">';
									html += '<a href="#" class=" list-group-item list-group-item-action  orange accent-4 subicon-dto">';
									html += '<span class="white-text">P.A.</span>';
									html += '</a>';
								html += '</div>';

								html += '<div class="col col-4 col-sm-4 col-lg-2 col-md-2 igs">';
									html += '<a href="#" class=" list-group-item list-group-item-action  orange accent-4 subicon-dto">';
									html += '<span class="white-text">PEND</span>';
									html += '</a>';
								html += '</div>';
							html += '</div>';
						html += '</div>';


					html += '</div>';
				html += '</div>';
			html += '</div>';
			
					
			_this.formulario.find('.perguntas').empty().append(html).promise().done(function () {
				_this.formulario.find('input[type="file"]').change(function(evt){
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

	
				_this.formulario.find('i').on('click', function () {
					$(this).parents('.element').find("input[type='file']").trigger('click');
				});
		
				_this.formulario.find('input[type="file"]').on('change', function() {
					var val = $(this).val().split('\\');
					val = val[val.length -1];

					$(this).siblings('span').attr('data-original-title', val)
					$(this).siblings('span').html(val.substring( 0, 12) + '...');
				});


				_this.formulario.find('.seleciona_resposta').on('click', function() {
					var elemento = $(this);

					if(elemento.hasClass('exige_planoacao')){
						var id = parseInt(elemento.attr('name').split('_')[1]);
						console.log(id);
						var html = '<div class="vertical-divider"></div><div class="col-xs-6  col-sm-6 col-md-6 col-6 plano_acao">';
						html += '<label for="descricao_planoacao_pergunta_' + id + '">Descrição</label>';
						html += '<textarea class="form-control" rows="3" name="descricao_planoacao_pergunta_' + id + '" id="descricao_planoacao_pergunta_' + id + '" ></textarea>';
						html += '</div>'

						elemento.parents('.linha_atual').append(html);
					}
				});

				_this.formulario.find('input[type="radio"]').on('change',function(e){
					if(this.value != "bom"){
						_this.formulario.find('.opcoes_questionamento').show(100);
						$('.modal').find('#nome-categoria').html(objetoAtual.checklist.titulo);
						$('.modal').modal();
					}else {
						_this.formulario.find('.opcoes_questionamento').hide(100);
					}
				});

			});
		};

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