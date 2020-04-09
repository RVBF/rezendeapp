/**
 *  PlanoAcao.form.ctrl.js
 *
 *  @author  Rafael Vinicius Barros Ferreira
 *	 @version 1.0
 */
(function(window, app, $, toastr)
{
	'use strict';

	function ControladoraFormPendencia(servicoPedencia) {
		var _this = this;

		_this.alterar = false;
		_this.formulario = $('#pendencia_form');
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

				var jqXHR = _this.alterar ? servicoPedencia.atualizar(obj) : servicoPedencia.adicionar(obj);
				jqXHR.done(function(resposta) {
					if(resposta.status){
						router.navigate('/pendencia');
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

		var pegarId = function pegarId(url, palavra)
		{

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

			return servicoPedencia.criar(
				$('#id').val(),
				$('#nao-conformidade').val(),
				dataLimite.toString() + ' ' + $('#hora_limite').val(),
				$('#descricao').val(),
				'',
				$('#responsavel').val(),
				$('#unidade').val()
			);
		};

		_this.configurarEventos = function configurarEventos() {
			if(window.location.href.search('editar') != -1 || window.location.href.search('cadastrar') != -1){
				_this.dataLimite =new Picker($('#data_limite').get()[0], {
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

				_this.horaLimite = new Picker($('#hora_limite').get()[0], {
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
			}
		};

		_this.definirForm = function definirForm(status = false) {
			_this.formulario.submit(false);
			if(window.location.href.search('visualizar') != -1) servicoPedencia.comId(pegarId(window.location.href,'visualizar-pendencia')).done(_this.desenhar);
			else  if(window.location.href.search('editar') != -1) servicoPedencia.comId(pegarId(window.location.href,'editar-pendencia')).done(_this.desenhar);
			else{
				_this.alterar = false;
				_this.configurarEventos();
				_this.popularColaboradores();
				_this.popularLojas();
				$('.card-title').html('<h3>Cadastrar Pendência</h3>');
				_this.formulario.find('#botoes').prepend('<div class="col col-md-4 col-12 col-sm-5 col-lg-4"><button type="submit" id="cadastrar" class="waves-effect waves-light btn white grey-text text-darken-4 col-12 quebra-linha"><i class="mdi mdi-checkbox-marked-circle-outline orange-text text-accent-4 "></i>Cadastrar</button></div>').promise().done(function(){
					$('#botoes').find('#cadastrar').on('click', _this.salvar);
				});
			}

		}

		// Desenha o objeto no formulário
		_this.desenhar = function desenhar(resposta) {
			_this.obj = resposta.conteudo;

			_this.configurarEventos();
			_this.popularColaboradores();
			_this.popularLojas();
			_this.formulario.find('#id').val(_this.obj.id).focus().blur();
			_this.formulario.find('#nao-conformidade').val(_this.obj.descricao).focus().blur();
			_this.formulario.find('#descricao').val(_this.obj.solucao).focus().blur();
			_this.formulario.find('#responsavel').val(_this.obj.responsavel.id).focus().blur();
			$("#responsavel").formSelect();
			var dataLimite = moment(_this.obj.dataLimite);
			$('#data_limite').val(dataLimite.format('DD') + ' de ' + dataLimite.format('MMMM') + ' de ' + dataLimite.format('YYYY')).focus().blur();
			$('#hora_limite').val(dataLimite.format('HH') + ':' + dataLimite.format('mm')).focus().blur();

			if(window.location.href.search('visualizar') != -1) {
				_this.formulario.desabilitar(true);
				_this.formulario.find('#botoes').desabilitar(false);

				_this.formulario.find('#botoes').prepend(' <div class="col col-md-4 col-12 col-sm-5 col-lg-4"><button type="submit" id="remover" class="waves-effect waves-light btn white grey-text text-darken-4 col-12 quebra-linha"><i class="mdi mdi-delete red-text text-darken-4"></i>Remover</button></div>').promise().done(function(){
                    $('#botoes').find('#remover').on('click', _this.remover);
				});

				if(_this.obj.status != 'Executado'){
					_this.formulario.find('#botoes').prepend(' <div class="col col-md-4 col-12 col-sm-5 col-lg-4"><button type="button" id="editar" class="waves-effect waves-light btn white grey-text text-darken-4 col-12 quebra-linha"><i class="mdi mdi-checkbox-marked-circle-outline orange-text text-accent-4 "></i>Editar</button></div>').promise().done(function(){
						_this.formulario.find('#editar').on('click', function(event){
							router.navigate('/editar-pendencia/'+ _this.obj.id);
						});
					});
				}
				console.log(_this.obj.descricaoExecucao);
				if(_this.obj.status == 'Executado'){
					let html = '';
					html += '<div class="row form-row">';
					html += '<div class="col col-sm-12 col-md-12 col-12 col-lg-12">';
					html += '<div class="input-field ">';
					html += '<textarea id="descricaoexecucao" class="campo_obrigatorio materialize-textarea validate valid" disabled=""></textarea>';
					html += '<label for="descricaoexecucao" class="active">Descrição execução</label>';
					html += '</div>';
					html += '</div>';
					html += '</div>';

					_this.formulario.find('#data_limite').parents('.row').before(html).promise().done(function(){
						$('#descricaoexecucao').val(_this.obj.descricaoExecucao).focus().blur();
					});
				}

				$('.card-title').html('<h3>Visualizar Pendência</h3>');
			}
			else if(window.location.href.search('editar') != -1) {
				_this.alterar = true;
				_this.dataLimite.setDate(dataLimite.toDate());
				_this.horaLimite.setDate(dataLimite.toDate());
				var html = '';
				html += '<div class="col col-md-4 col-12 col-sm-5 col-lg-4">';
				html += '<button id="salvar" type="submit" class="waves-effect waves-light btn white grey-text text-darken-4 col-12 quebra-linha">';
				html += '<i class="mdi mdi-checkbox-marked-circle-outline orange-text text-accent-4 ">';
				html += '</i>salvar</button>';
				html += '</div>';

				_this.formulario.find('#botoes').prepend(html).promise().done(function(){
					$('#salvar').on('click', _this.salvar);

				});
				$('.card-title').html('<h3>Editar Pendência</h3>');
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
				$("#responsavel").empty();

				$.each(resposta.data, function(i ,item) {
					$("#responsavel").append($('<option>', {
						value: item.id,
						text: item.nome  + ' ' + item.sobrenome
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

		_this.salvar = function salvar() {
			_this.formulario.validate(criarOpcoesValidacao());
        };

		_this.remover = function remover(){
			BootstrapDialog.show({
				type	: BootstrapDialog.TYPE_DANGER,
				title	: 'Deseja remover esta Pendência?',
				message	: 'Id: ' + _this.obj.id + '. <br> Descrição : ' +_this.obj.descricao + '.<br> Solução:'  + _this.obj.solucao + '.',
				size	: BootstrapDialog.SIZE_LARGE,
				buttons	: [ {
						label	: '<u>S</u>im',
						hotkey	: 'S'.charCodeAt(0),
						action	: function(dialog){
							servicoPedencia.remover(_this.obj.id).done(function (resposta) {
								if(resposta.status){
									router.navigate('/pendencia');
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
	}; // ControladoraFormPendencia

	// Registrando
	app.ControladoraFormPendencia = ControladoraFormPendencia;

})(window, app, jQuery, toastr);