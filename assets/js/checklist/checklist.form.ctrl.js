/**
 *  Checklist.form.ctrl.js
 *
 *  @author  Rafael Vinicius Barros Ferreira
 *	 @version 1.0
 */
(function(window, app, $, toastr)
{
	'use strict';

	function ControladoraFormChecklist(servicoChecklist, controladoraListagemChecklist) {
		var _this = this;

		_this.alterar;
		_this.formulario = $('#checklist_form');
		_this.botaoSubmissao = $('#salvar')
		_this.cancelarModoEdicao = $('#cancelar_edicao')
		_this.idSetor = window.location.href.split('#')[1].substring(1, url.length).split('/')[1];	
		_this.dataLimite = '';
		_this.horaLimite = '';

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
			
				var jqXHR = _this.alterar ? servicoChecklist.atualizar(obj) : servicoChecklist.adicionar(obj);
				jqXHR.done(function(resposta) {
					if(resposta.status){
						router.navigate('/checklist');
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
		};

		_this.popularSetores  =  function popularSetores(valor = 0)
		{
			var sucesso = function (resposta) {
				$("#setor").empty();

				$.each(resposta.data, function(i ,item) {
					$("#setor").append($('<option>', {
						value: item.id,
						text: item.titulo
					}));
				});

				$('#setor').formSelect();
			};

			var erro = function(resposta)
			{
				var mensagem = jqXHR.responseText || 'Erro ao popular select de farmácias.';
				toastr.error(mensagem);
				return false;
			}

			var servicoSetor = new app.ServicoSetor();
			var  jqXHR = servicoSetor.todos();
			jqXHR.done(sucesso).fail(erro);
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

		_this.popularQuestionarios  =  function popularQuestionarios(valor = 0)
		{
			var sucesso = function (resposta) {
				$("#questionarios").empty();

				$.each(resposta.data, function(i ,item) {

					let opcoes = {
						value: item.id,
						text: item.titulo,
						selected : (i ==0) ? true : false
					};
					$("#questionarios").append($('<option>', opcoes));
				});

				$('#questionarios').formSelect();
			};

			var erro = function(resposta)
			{
				var mensagem = jqXHR.responseText || 'Erro ao popular select de farmácias.';
				toastr.error(mensagem);
				return false;
			}

			var servicoQuestionario = new app.ServicoQuestionario();
			var  jqXHR = servicoQuestionario.todos();
			jqXHR.done(sucesso).fail(erro);
		};

		_this.popularTiposDeChecklist = function popularTiposChecklist() {
			var servicoTipoChecklist = new app.TipoChecklist();
			var  tiposQuestionarios = servicoTipoChecklist.getTipoChecklist();

			$("#tipo-checklist").empty();
			
			$.each(tiposQuestionarios, function(i ,item) {
				$("#tipo-checklist").append($('<option>', {
					value:item,
					text: item
				}));
			});

			$("#tipo-checklist").formSelect();
		};

		_this.definirForm = function definirForm(status) {			
			_this.formulario.submit(false);
			_this.alterar = status;

			if(window.location.href.search('visualizar') != -1) servicoChecklist.comId(pegarId(window.location.href,'visualizar-checklist')).done(_this.desenhar);
			else  if(window.location.href.search('editar') != -1) servicoChecklist.comId(pegarId(window.location.href,'editar-checklist')).done(_this.desenhar);
			else{
				_this.formulario.parents('#painel_formulario').promise().done(function() {
					_this.formulario.find('#titulo').focus();
					_this.popularLojas();
					_this.popularTiposDeChecklist();
					_this.popularColaboradores();
					_this.popularQuestionarios();
					_this.popularSetores();
					_this.configurarBotoes();
					$('.card-title').html('<h3>Cadastrar Checklist</h3>');
					_this.formulario.find('#botoes').prepend(' <div class="col col-md-6 col-6 col-sm-6 col-lg-6 d-flex justify-content-sm-end justify-content-md-end"><button type="submit" id="cadastrar" class="waves-effect waves-light btn white grey-text text-darken-4 button-dto quebra-linha f-12-dto"><i class="mdi mdi-checkbox-marked-circle-outline orange-text text-accent-4 "></i>Cadastrar</button></div>').promise().done(function(){
						$('#botoes').find('#cadastrar').on('click', _this.salvar);
					});
				});
			}
		}

		// Desenha o objeto no formulário
		_this.desenhar = function desenhar(obj) {
			_this.objeto = obj.conteudo;
			_this.popularLojas();
			_this.popularTiposDeChecklist();
			_this.popularColaboradores();
			_this.popularQuestionarios();
			_this.popularSetores();
			_this.configurarBotoes();

			_this.formulario.find('#id').val(_this.objeto.id).focus().blur();
			_this.formulario.find('#titulo').val(_this.objeto.descricao).focus().blur();
			_this.formulario.find('#setor').val(_this.objeto.setor.id).focus().blur();
			_this.formulario.find('#unidade').val(_this.objeto.loja.id).focus().blur();
			_this.formulario.find('#unidade').val(_this.objeto.loja.id).focus().blur();

			var dataLimite = moment(_this.objeto.dataLimite);
			$('#data').val(dataLimite.format('DD') + ' de ' + dataLimite.format('MMMM') + ' de ' + dataLimite.format('YYYY')).focus().blur();
			$('#hora').val(dataLimite.format('HH') + ':' + dataLimite.format('mm')).focus().blur();
			$('#descricao').val(_this.objeto.descricao).focus().blur();
			if(window.location.href.search('visualizar') != -1) {
				_this.formulario.desabilitar(true);
				_this.formulario.find('#botoes').desabilitar(false);
				if(_this.objeto.status != 'Executado'){
					_this.formulario.find('#botoes').prepend(' <div class="col col-md-6 col-6 col-sm-6 col-lg-6 d-flex justify-content-sm-end justify-content-md-end"><button type="button" id="editar" class="waves-effect waves-light btn white grey-text text-darken-4 button-dto quebra-linha f-12-dto"><i class="mdi mdi-checkbox-marked-circle-outline orange-text text-accent-4 "></i>Editar</button></div>').promise().done(function(){
						_this.formulario.find('#editar').on('click', function(event){
							router.navigate('/editar-checklist/'+ _this.objeto.id);
						});
					});
				}
			
				$('.card-title').html('<h3>Visualizar Checklist</h3>');
			}
			else if(window.location.href.search('editar') != -1) {
				_this.alterar = true;
				_this.dataLimite.setDate(dataLimite.toDate());
				_this.horaLimite.setDate(dataLimite.toDate());
				_this.formulario.find('#questionarios').parents('.select-wrapper').desabilitar(true);
				var html = '';
				html += '<div class="col col-md-6 col-6 col-sm-6 col-lg-6 d-flex justify-content-sm-end justify-content-md-end">';
				html += '<button id="salvar" type="submit" class="waves-effect waves-light btn white grey-text text-darken-4 button-dto quebra-linha f-12-dto">';
				html += '<i class="mdi mdi-checkbox-marked-circle-outline orange-text text-accent-4 ">';
				html += '</i>salvar</button>';
				html += '</div>';

				_this.formulario.find('#botoes').prepend(html).promise().done(function(){
					$('#salvar').on('click', _this.salvar);

				});

				$('.card-title').html('<h3>Editar Checklist</h3>');
			}
		};
		_this.salvar = function salvar() {
			_this.formulario.validate(criarOpcoesValidacao());
        };
		
		// Configura os eventos do formulário
		_this.configurar = function configurar(status = false) {
			_this.definirForm(status);
		};
	}; // ControladoraFormChecklist

	// Registrando
	app.ControladoraFormChecklist = ControladoraFormChecklist;

})(window, app, jQuery, toastr);