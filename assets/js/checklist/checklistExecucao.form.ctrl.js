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
			_this.formulario.parents('#checklist_execucao').promise().done(function() {
                _this.buscarQuestionamentos();
				_this.configurarBotoes();
			});
		};

		_this.buscarQuestionamentos  =  function buscarQuestionamentos(valor = 0)
		{
            console.log(_this.idChecklist);
			var sucesso = function (resposta) {
                console.log(resposta);
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