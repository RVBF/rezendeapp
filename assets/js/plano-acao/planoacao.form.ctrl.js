/**
 *  PlanoAcao.form.ctrl.js
 *
 *  @author  Rafael Vinicius Barros Ferreira
 *	 @version 1.0
 */
(function(window, app, $, toastr)
{
	'use strict';

	function ControladoraFormPlanoAcao(servicoPlanoAcao) {
		var _this = this;

		_this.alterar = false;
		_this.formulario = $('#planoacao_form');
		_this.dataLimite = '';
		_this.horaLimite = '';
		_this.obj =  null;

		// Cria as opções de validação do formulário
		var criarOpcoesValidacao = function criarOpcoesValidacao() {
			var opcoes = {
			};

			// Irá disparar quando a validação passar, após chamar o método validate().
			opcoes.submitHandler = function submitHandler(form) {

				var obj = _this.conteudo();

				var terminado = function() {
					_this.formulario.desabilitar(false);
				};

				_this.formulario.desabilitar(true);

				var jqXHR = _this.alterar ? servicoPlanoAcao.atualizar(obj) : servicoPlanoAcao.adicionar(obj);
				jqXHR.done(function(resposta) {
					if(resposta.status){
						router.navigate('/plano-acao');
						toastr.success(resposta.mensagem);

					}
					else{
						$('body #msg').empty().removeClass('d-none').append(resposta.mensagem);
						if(resposta != undefined && resposta.mensagem) toastr.error(resposta.mensagem);
					}
				}).fail(window.erro).always(terminado);

				if(_this.alterar){
					$('.depende_selecao').each(function(){
						$(this).prop('disabled', true);
					});
				}
			}; // submitHandler

			return opcoes;
		};

		var pegarId = function pegarId(url, palavra) {
			// Terminando com "ID/palavra"
			var regexS = palavra+'+\/[0-9]{1,}';

			var regex = new RegExp(regexS);
			var resultado = regex.exec(url);

			if (!resultado || resultado.length < 1)
			{
				return 0;
			}

			var array = resultado[0].split('/');
			return array[1];
		};

		// Obtém o conteúdo atual do form como um objeto
		_this.conteudo = function conteudo() {
			var dataLimite = moment(_this.dataLimite.getDate()).format('YYYY-MM-DD');
			var configuraoAcoes = {acoes : []};

			if(_this.formulario.find('.acao').length > 0){
				$('.ids').each(function(){
					var id = $(this).val();
					configuraoAcoes.acoes.push({id: id, acao: $('#acao_pa_'+ id).val()});
				});
			}


			return servicoPlanoAcao.criar(
				$('#id').val(),
				$('#nao-conformidade-pa').val(),
				dataLimite.toString() + ' ' + $('#hora_limitepa').val(),
				configuraoAcoes,
				$('#responsavelpa').val(),
				$('#unidade').val(),
				'',
			);
		};

		_this.configurarEventos = function configurarEventos() {
			_this.dataLimite =new Picker($('#data_limitepa').get()[0], {
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

			_this.horaLimite = new Picker($('#hora_limitepa').get()[0], {
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

			$('body').find('.adicionar_acao').on('click', _this.adiconarAcao);
		};

		_this.definirForm = function definirForm(status = false) {
			_this.formulario.submit(false);
			if(window.location.href.search('visualizar') != -1) servicoPlanoAcao.comId(pegarId(window.location.href,'visualizar-pa')).done(_this.desenhar);
			else  if(window.location.href.search('editar') != -1) servicoPlanoAcao.comId(pegarId(window.location.href,'editar-pa')).done(_this.desenhar);
			else{
				_this.alterar = false;
				$('.acoes').find('.acao:first').find('.input-field').prepend('<i class="adicionar_acao prefix mdi mdi-plus-circle-outline"  id="adicionar_acao"></i>');
				_this.configurarEventos();
				_this.popularColaboradores();
				_this.popularLojas();
				$('.card-title').html('<h3>Cadastrar PA</h3>');
				_this.formulario.find('#botoes').prepend(' <div class="col col-md-6 col-6 col-sm-6 col-lg-6 d-flex justify-content-sm-end justify-content-md-end"><button type="submit" id="cadastrar" class="waves-effect waves-light btn white grey-text text-darken-4 button-dto quebra-linha f-12-dto"><i class="mdi mdi-checkbox-marked-circle-outline orange-text text-accent-4 "></i>Cadastrar</button></div>').promise().done(function(){
					$('#botoes').find('#cadastrar').on('click', _this.salvar);
				});

			}
		}

		// Desenha o objeto no formulário
		_this.desenhar = function desenhar(resposta) {
			_this.obj = resposta.conteudo;
			if(window.location.href.search('editar') != -1) $('.acoes').find('.acao:first').find('.input-field').prepend('<i class="adicionar_acao prefix mdi mdi-plus-circle-outline"  id="adicionar_acao"></i>');

			_this.configurarEventos();
			_this.popularColaboradores();
			_this.popularLojas();
			_this.formulario.find('#id').val(_this.obj.id).focus().blur();
			_this.formulario.find('#nao-conformidade-pa').val(_this.obj.descricao).focus().blur();
			_this.formulario.find('.acoes').find('textarea:last').val(_this.obj.solucao.acoes[0].acao).focus().blur();

			for (var i = 1; i < _this.obj.solucao.acoes.length; i++) {
				var elemento = _this.obj.solucao.acoes[i];
				var html  = '';
				html += '<div class="row form-row acao">';
					html += '<input type= "hidden" class="ids"  name="acao_pa_' + (i+1) +'" value ='+ (i+1) +'>';
					html += '<div class="col col-sm-12 col-md-12 col-12 col-lg-12">';
						html += '<div class="input-field ">';
						if(window.location.href.search('editar') != -1)	html += ' <i class="remover_acao prefix mdi mdi-minus-circle-outline"  id="remover_acao"></i>';
							html += '<textarea id="acao_pa_' + (i+1) + '" value="'+elemento.acao+'" class=" campo_obrigatorio materialize-textarea validate">'+elemento.acao+'</textarea>';
							html += '<label for="acao_pa_' + (i+1) + '">Ação ' + (i+1) + '</label>												';
						html += '</div>';
					html += '</div>';
				html += '</div>';

				_this.formulario.find('.acoes').append(html);

				if(window.location.href.search('editar') != -1){
					$('.remover_acao:last').on('click', function () {
						$(this).parents('.acao').remove();
					});
				}


				_this.formulario.find('.acoes').find('textarea:last').focus().blur();
			}

			_this.formulario.find('#responsavelpa').val(_this.obj.responsavel.id).focus().blur();
			$("#responsavelpa").formSelect();
			var dataLimite = moment(_this.obj.dataLimite);
			$('#data_limitepa').val(dataLimite.format('DD') + ' de ' + dataLimite.format('MMMM') + ' de ' + dataLimite.format('YYYY')).focus().blur();
			$('#hora_limitepa').val(dataLimite.format('HH') + ':' + dataLimite.format('mm')).focus().blur();

			if(window.location.href.search('visualizar') != -1) {
				_this.formulario.desabilitar(true);
				_this.formulario.find('#botoes').desabilitar(false);

				_this.formulario.find('#botoes').prepend(' <div class="col col-md-2 col-4 col-sm-2 col-lg-2"><button type="submit" id="remover" class="waves-effect waves-light btn white grey-text text-darken-4 button-dto quebra-linha f-12-dto"><i class="mdi mdi-delete red-text text-darken-4"></i>Remover</button></div>').promise().done(function(){
                    $('#botoes').find('#remover').on('click', _this.remover);
				});

				if(_this.obj.status != 'Executado'){
					_this.formulario.find('#botoes').prepend(' <div class="col col-md-2 col-4 col-sm-2 col-lg-2"><button type="button" id="editar" class="waves-effect waves-light btn white grey-text text-darken-4 button-dto quebra-linha f-12-dto"><i class="mdi mdi-checkbox-marked-circle-outline orange-text text-accent-4 "></i>Editar</button></div>').promise().done(function(){
						_this.formulario.find('#editar').on('click', function(event){
							router.navigate('/editar-pa/'+ _this.obj.id);
						});
					});
				}

				$('.card-title').html('<h3>Visualizar PA</h3>');
			}
			else if(window.location.href.search('editar') != -1) {
				_this.alterar = true;
				_this.dataLimite.setDate(dataLimite.toDate());
				_this.horaLimite.setDate(dataLimite.toDate());
				var html = '';
				html += '<div class="col col-md-6 col-6 col-sm-6 col-lg-6 d-flex justify-content-sm-end justify-content-md-end">';
				html += '<button id="salvar" type="submit" class="waves-effect waves-light btn white grey-text text-darken-4 button-dto quebra-linha f-12-dto">';
				html += '<i class="mdi mdi-checkbox-marked-circle-outline orange-text text-accent-4 ">';
				html += '</i>salvar</button>';
				html += '</div>';

				_this.formulario.find('#botoes').prepend(html).promise().done(function(){
					$('#salvar').on('click', _this.salvar);

				});
				$('.card-title').html('<h3>Editar PA</h3>');
			}
		};

		_this.popularLojas  =  function popularLojas(valor = 0)
		{
			var sucesso = function (resposta) {
				$("#unidade").empty();

				$.each(resposta.data, function(i ,item) {
					$("#unidade").append($('<option>', {
						value: item.id,
						text: item.razaoSocial  + '/' + item.nomeFantasia
					}));
				});

				$('#unidade').formSelect();
			};

			var erro = function(resposta)
			{
				var mensagem = jqXHR.responseText || 'Erro ao popular select de farmácias.';
				toastr.error(mensagem);
				return false;
			}

			var servicoLoja = new app.ServicoLoja();
			var  jqXHR = servicoLoja.todos();
			jqXHR.done(sucesso).fail(erro);
		};

		_this.popularColaboradores  =  function popularColaboradores(valor = 0)
		{
			var sucesso = function (resposta) {
				$("#responsavelpa").empty();

				$.each(resposta.data, function(i ,item) {
					$("#responsavelpa").append($('<option>', {
						value: item.id,
						text: item.nome  + ' ' + item.sobrenome
					}));
				});

				$('#responsavelpa').formSelect();
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

		_this.adiconarAcao = function adiconarAcao(){
			var quantidade = $(this).parents('form').find('.acoes').find('.acao').length + 1;
			var html  = '';
			html += '<div class="row form-row acao">';
				html += '<input type= "hidden" class="ids"  name="acao_pa_' + quantidade +'" value ='+ quantidade +'>';
				html += '<div class="col col-sm-12 col-md-12 col-12 col-lg-12">';
					html += '<div class="input-field ">';
						html += ' <i class="remover_acao prefix mdi mdi-minus-circle-outline"  id="remover_acao"></i>';
						html += '<textarea id="acao_pa_' + quantidade + '" class=" campo_obrigatorio materialize-textarea validate"></textarea>';
						html += '<label for="acao_pa_' + quantidade + '">Ação ' + quantidade + '</label>												';
					html += '</div>';
				html += '</div>';
			html += '</div>';


			_this.formulario.find('.acoes').append(html);
			$('.remover_acao:last').on('click', function () {
				$(this).parents('.acao').remove();
			});
		};

		_this.salvar = function salvar() {
			_this.formulario.validate(criarOpcoesValidacao());
        };

		_this.remover = function remover(){
			BootstrapDialog.show({
				type	: BootstrapDialog.TYPE_DANGER,
				title	: 'Deseja remover este Plano de ação?',
				message	: 'Id: ' + _this.obj.id + '. <br> Não conformidade : ' +_this.obj.descricao + '.',
				size	: BootstrapDialog.SIZE_LARGE,
				buttons	: [ {
						label	: '<u>S</u>im',
						hotkey	: 'S'.charCodeAt(0),
						action	: function(dialog){
							servicoPlanoAcao.remover(_this.obj.id).done(function (resposta) {
								if(resposta.status){
									router.navigate('/plano-acao');
									toastr.success(resposta.mensagem);
									dialog.close();

								}
								else{
									if(resposta != undefined && resposta.mensagem) toastr.error(resposta.mensagem);

									dialog.close();
								}
							});
						}
					}, {
						label	: '<u>N</u>ão',
						hotkey	: 'N'.charCodeAt(0),
						action	: function(dialog){
							dialog.close();
						}
					}
				]
			});
		};

		// Configura os eventos do formulário
		_this.configurar = function configurar(status = false) {
			_this.definirForm(status);
		};
	}; // ControladoraFormPlanoAcao

	// Registrando
	app.ControladoraFormPlanoAcao = ControladoraFormPlanoAcao;

})(window, app, jQuery, toastr);