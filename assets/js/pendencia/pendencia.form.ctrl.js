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
		_this.botaoSubmissao = $('#editar');
		_this.dataLimite = '';
		_this.horaLimite = '';
		_this.objeto =  null;

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
						toastr.error(resposta.mensagem);
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
				$('#responsavel').val(),
				$('#unidade').val(),
				'',
			);
		};

		_this.configurarEventos = function configurarEventos() {
			if(window.location.href.search('visualizar') != -1){

			}else{
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
				_this.formulario.find('#botoes').prepend(' <div class="col col-md-6 col-6 col-sm-6 col-lg-6 d-flex justify-content-sm-end justify-content-md-end"><button type="submit" id="cadastrar" class="waves-effect waves-light btn white grey-text text-darken-4 button-dto quebra-linha f-12-dto"><i class="mdi mdi-checkbox-marked-circle-outline orange-text text-accent-4 "></i>Cadastrar</button></div>').promise().done(function(){
					$('#botoes').find('#cadastrar').on('click', _this.salvar);
				});
			}

		}

		// Desenha o objeto no formulário
		_this.desenhar = function desenhar(resposta) {
			_this.objeto = resposta.conteudo;

			_this.configurarEventos();
			_this.popularColaboradores();
			_this.popularLojas();
			_this.formulario.find('#id').val(_this.objeto.id).focus().blur();
			_this.formulario.find('#nao-conformidade').val(_this.objeto.descricao).focus().blur();
			_this.formulario.find('#descricao').val(_this.objeto.solucao).focus().blur();
			_this.formulario.find('#responsavel').val(_this.objeto.responsavel.id).focus().blur();
			$("#responsavel").formSelect();
			var dataLimite = moment(_this.objeto.dataLimite);
			$('#data_limite').val(dataLimite.format('DD') + ' de ' + dataLimite.format('MMMM') + ' de ' + dataLimite.format('YYYY')).focus().blur();
			$('#hora_limite').val(dataLimite.format('HH') + ':' + dataLimite.format('mm')).focus().blur();

			if(window.location.href.search('visualizar') != -1) {
				_this.formulario.desabilitar(true);
				_this.formulario.find('#botoes').desabilitar(false);
				if(_this.objeto.status != 'Executado'){
					_this.formulario.find('#botoes').prepend(' <div class="col col-md-6 col-6 col-sm-6 col-lg-6 d-flex justify-content-sm-end justify-content-md-end"><button type="button" id="editar" class="waves-effect waves-light btn white grey-text text-darken-4 button-dto quebra-linha f-12-dto"><i class="mdi mdi-checkbox-marked-circle-outline orange-text text-accent-4 "></i>Editar</button></div>').promise().done(function(){
						_this.formulario.find('#editar').on('click', function(event){
							router.navigate('/editar-pa/'+ _this.objeto.id);
						});
					});
				}
			
				$('.card-title').html('<h3>Visualizar Pendência</h3>');
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
		

		// Configura os eventos do formulário
		_this.configurar = function configurar(status = false) {
			_this.definirForm(status);
		};
	}; // ControladoraFormPendencia

	// Registrando
	app.ControladoraFormPendencia = ControladoraFormPendencia;

})(window, app, jQuery, toastr);