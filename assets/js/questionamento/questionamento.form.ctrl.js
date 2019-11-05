/**
 *  questionamento.form.ctrl.js
 *
 *  @author  Rafael Vinicius Barros Ferreira
 *	 @version 1.0
 */
(function(window, app, $, toastr) {
	'use strict';

	function ControladoraFormQuestionamentoExecucao(servicoQuestionamento) {
		var _this = this;

		_this.alterar;
		_this.formulario = $('#executarquestionamento_form');
        _this.botaoSubmissao = $('#salvar');
		_this.idChecklist = window.location.href.split('#')[1].substring(1, url.length).split('/')[1];
		_this.questionamentos = null;
		_this.objetoAtual = null;
		_this.dataLimitePa = '';
		_this.horaLimitePa = '';
		_this.dataLimitePe = '';
		_this.horaLimitePe = '';
		_this.contador =0;
		_this.servicoPlanoAcao = new app.ServicoPlanoAcao();
		_this.servicoPendencia = new app.ServicoPendencia();


		// Cria as opções de validação do formulário
		var criarOpcoesValidacao = function criarOpcoesValidacao() {
			var opcoes = {
			};

			// Irá disparar quando a validação passar, após chamar o método validate().
			opcoes.submitHandler = function submitHandler(form) {
				var obj = _this.conteudo();
				console.log(obj);
				var terminado = function() {
					_this.formulario.desabilitar(false);
				};
				
				// _this.formulario.desabilitar(true);
			
				var jqXHR = _this.alterar ? servicoQuestionamento.atualizar(obj) : servicoQuestionamento.adicionar(obj);
				jqXHR.done(function() {
					// router.navigate('/configuracao');
					// toastr.success('QUestionamento Adicionado com sucesso!')
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
			var dataLimitePa = moment(_this.dataLimitePa.getDate()).format('YYYY-MM-DD');
			var dataLimitePe = moment(_this.dataLimitePe.getDate()).format('YYYY-MM-DD');
			console.log($('#hora_limitepa').val());

			return servicoQuestionamento.criar(
				_this.objetoAtual.id,
				_this.objetoAtual.status,
				_this.objetoAtual.formularioPergunta,
				{
					"opcao" : $("[name='opcao']:checked").val(),
				},
				_this.objetoAtual.checklist,
				_this.servicoPlanoAcao.criar(
					'',
					$('#nao-conformidade-pa').val(),
					dataLimitePa.toString() + ' ' + $('#hora_limitepa').val(),
					$('#descricao-pa').val(),
					$('#responsavelpa').val(),
					'',
				),
				_this.servicoPendencia.criar(
					'',
					$('#descricao-pendencia').val(),
					dataLimitePe.toString() + ' ' + $('#hora_limitepe').val(),
					$('#descricao-solucao').val(),
					$('#responsavelpe').val()
				)
			);
		};

		_this.popularColaboradores  =  function popularColaboradores(valor = 0) {
			var sucesso = function (resposta) {
				$("#responsavelpe").empty();

				$.each(resposta.data, function(i ,item) {
					$("#responsavelpe").append($('<option>', {
						value: item.colaborador.id,
						text: item.colaborador.nome  + ' ' + item.colaborador.sobrenome
					}));

					$("#responsavelpa").append($('<option>', {
						value: item.colaborador.id,
						text: item.colaborador.nome  + ' ' + item.colaborador.sobrenome
					}));
				});
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
			_this.dataLimitePa = new Picker($('#data_limitepa').get()[0], {
				format : 'DD de MMMM de YYYY',
				controls: true,
				inline: true,
				container: '.date-panel-pa',					
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

			_this.horaLimitePa = new Picker($('#hora_limitepa').get()[0], {
				format: 'HH:mm',
				headers: true,
				controls: true,
				inline: true,
				container: '.time-panel-pa',	
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

			_this.dataLimitePe = new Picker($('#data_limitepe').get()[0], {
				format : 'DD de MMMM de YYYY',
				controls: true,
				inline: true,
				container: '.date-panel-pe',					
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

			_this.horaLimitePe = new Picker($('#hora_limitepe').get()[0], {
				format: 'HH:mm',
				headers: true,
				controls: true,
				inline: true,
				container: '.time-panel-pe',	
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
			_this.formulario.find('#proximo').on('click', _this.proximo);
		};

		_this.iniciarFormularioModoCadastro = function iniciarFormularioModoCadastro() {
			_this.popularColaboradores();
			_this.buscarQuestionamentos();
			_this.configurarEventos();
		};

		_this.popularQuestao = function popularQuestao() {
			_this.objetoAtual = _this.questionamentos.shift();
			_this.contador +=1;
			let html = '';
			html += '<div class="row form-row questionamento mb-0-dto">'
				html +='<div class="col col-sm-12 col-md-12 col-lg-12 col-12 mb-0-dto">';

				html +='<div class="card-panel left-align pergunta">';
					html += '<input type= "hidden" class="id"  name="questionamento_' + _this.objetoAtual.id + '" value ="'+ _this.objetoAtual.id  +'">';

					html += '<div class="row form-row mb-0-dto">'
						html +='<div class="col col-sm-12 col-md-12 col-lg-12 col-12 mb-0-dto">';
							html +='<p class="mb-0-dto">';
							html +='<strong class="fw-700-dto">'+_this.contador+'</strong> - ' + _this.objetoAtual.formularioPergunta.pergunta;
							html +='</p>';
						html +='</div>';
					html +='</div>';

					html += '<div class="row form-row mb-0-dto">'
						html +='<div class="col col-sm-4 col-md-4 col-lg-2 col-4 d-flex justify-content-center justify-content-md-start">';
							html +='<input class=" form-control cb-dto opcao" type="radio" id="bom" name="opcao" value="Bom">';
							html +='<label class="label-dto" for="bom">';
							html +='<i class="mdi mdi-emoticon-happy-outline large orange-text text-accent-4"></i>';
							html +'</label>'
						html +='</div>';

						html +='<div class="col col-sm-4 col-md-4 col-lg-2 col-4 d-flex justify-content-center justify-content-md-start">';
							html +='<input class=" form-control cb-dto opcao" type="radio" id="regular" name="opcao" value="Regular">';
							html +='<label class="label-dto" for="regular">';
							html +='<i class="mdi mdi-emoticon-neutral-outline large orange-text text-accent-4"></i>';
							html +'</label>'

						html +='</div>';

						html +='<div class="col col-sm-4 col-md-4 col-lg-2 col-4 d-flex justify-content-center justify-content-md-start">';
							html +='<input class=" form-control cb-dto opcao" type="radio" id="ruim" name="opcao" value="Ruim">';
							html +='<label class="label-dto" for="ruim">';
							html +='<i class="mdi mdi-emoticon-sad-outline large orange-text text-accent-4"></i>';
							html +'</label>'
						html +='</div>';
					html +='</div>';

				html +='</div>';

				html += '<div class="opcoes_questionamento col col-sm-12 col-md-12 col-lg-12 col-12 center-align" style="display: none">';
					html += '<div class="row form-row ">'
						html += '<div class="list-group">';
								html += '<div class="col col-4 col-sm-4 col-lg-2 col-md-2">';
										html += '<a class="list-group-item list-group-item-action orange accent-4 subicon-dto element">';
										html += '<i class="mdi mdi-information-outline white-text"></i>';
										html += '</a>';
								html += '</div>';
								
								html += '<div class="col col-4 col-sm-4 col-lg-2 col-md-2">';
										html += '<a class="list-group-item list-group-item-action orange accent-4 subicon-dto element" data-toggle="tooltip" title="Nenhum arquivo selecionado.">';
										html += '<i class="mdi mdi-microphone white-text"></i>';
										html += '<input class="d-none form-control" type="file" name="pergunta_audio" id="pergunta_audio" accept="image/*">';
										html += '</a>';
								html += '</div>';
								
								html += '<div class="col col-4 col-sm-4 col-lg-2 col-md-2">';
										html += '<a class="list-group-item list-group-item-action orange accent-4 subicon-dto element" data-toggle="tooltip" title="Nenhum arquivo selecionado.">';
										html += '<i class="mdi mdi-camera-outline white-text"></i>';
										html += '<input class="d-none form-control" type="file" ref="file"  name="pergunta_camera" id="pergunta_camera" accept="image/*">';
										html += '</a>';
								html += '</div>';
						
								html += '<div class="col col-4 col-sm-4 col-lg-2 col-md-2">';
									html += '<a class="list-group-item list-group-item-action  orange accent-4 subicon-dto element active" data-target="#comentarios">';
									html += '<i class="mdi mdi-lead-pencil white-text"></i>';
									html += '</a>';
								html += '</div>';
								
								html += '<div class="col col-4 col-sm-4 col-lg-2 col-md-2">';
									html += '<a class=" list-group-item list-group-item-action  orange accent-4 subicon-dto element" data-target="#secao_PA">';
									html += '<span class="white-text">P.A.</span>';
									html += '</a>';
								html += '</div>';

								html += '<div class="col col-4 col-sm-4 col-lg-2 col-md-2">';
									html += '<a class=" list-group-item list-group-item-action  orange accent-4 subicon-dto element" data-target="#secao_pendencia">';
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

				_this.formulario.find('.opcoes_questionamento').on('click', 'a', function (event) {
					let elemento = $(this);
					let destino = $(elemento.attr('data-target'));	
					let elementoAtivo = elemento.parents('.opcoes_questionamento').find('.active');
					let destinoAnterior = $(elementoAtivo.attr('data-target'));	

 					if(destino.hasClass('d-none') && destino.hasClass('desabilitado')){
						destino.removeClass('d-none');
						destino.removeClass('desabilitado');
						destino.desabilitar(false);
						destino.find('.select').formSelect();

						elemento.addClass('active');
						elementoAtivo.removeClass('active');
						destinoAnterior.addClass('d-none');
						destinoAnterior.addClass('desabilitado');
						destinoAnterior.desabilitar(true);

					}
				});

				_this.formulario.find('i').on('click', function (event) {
					$(this).next("input[type='file']").trigger('click');
				});
		
				// _this.formulario.find('input[type="file"]').on('change', function() {
				// 	var val = $(this).val().split('\\');
				// 	val = val[val.length -1];

				// 	$(this).siblings('span').attr('data-original-title', val)
				// 	$(this).siblings('span').html(val.substring( 0, 12) + '...');
				// });


				_this.formulario.find('.seleciona_resposta').on('click', function() {
					var elemento = $(this);

					if(elemento.hasClass('exige_planoacao')){
						var id = parseInt(elemento.attr('name').split('_')[1]);
						var html = '<div class="vertical-divider"></div><div class="col-xs-6  col-sm-6 col-md-6 col-6 plano_acao">';
						html += '<label for="descricao_planoacao_pergunta_' + id + '">Descrição</label>';
						html += '<textarea class="form-control" rows="3" name="descricao_planoacao_pergunta_' + id + '" id="descricao_planoacao_pergunta_' + id + '" ></textarea>';
						html += '</div>'

						elemento.parents('.linha_atual').append(html);
					}
				});

				_this.formulario.find('input[type="radio"]').on('change',function(e){
					if(this.value != "Bom"){
						_this.formulario.find('.opcoes_questionamento').show(100);
						$('.modal').find('#nome-categoria').html(_this.objetoAtual.checklist.titulo);
						$('.modal').modal();

						let destino = $(_this.formulario.find('.opcoes_questionamento').find('.active').attr('data-target'));	

						if(destino.hasClass('d-none') && destino.hasClass('desabilitado')){
							destino.removeClass('d-none');
							destino.removeClass('desabilitado');
						}
					}else {
						_this.formulario.find('.opcoes_questionamento').hide(100);
						_this.formulario.find('.opcao-selecionada').each(function(i, atual) {
							if(!$(atual).hasClass('d-none') && !$(atual).hasClass('desabilitado')){
								$(atual).addClass('d-none');
								$(atual).addClass('desabilitado');
							}
						});
					}
				});

			});
		};

		_this.buscarQuestionamentos  =  function buscarQuestionamentos(valor = 0)	{
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
			var servicoChecklist = new app.ServicoChecklist();
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
		
		};

		_this.salvar = function salvar() {
			_this.formulario.validate(criarOpcoesValidacao());
		};
		
		_this.proximo = function proximo(){
			console.log('netrei');
			_this.formulario.validate(criarOpcoesValidacao());
		}

		// Configura os eventos do formulário
		_this.configurar = function configurar(status = false) {
            _this.definirForm(status);
		};
	}; // ControladoraFormQuestionamentoExecucao

	// Registrando
	app.ControladoraFormQuestionamentoExecucao = ControladoraFormQuestionamentoExecucao;

})(window, app, jQuery, toastr);