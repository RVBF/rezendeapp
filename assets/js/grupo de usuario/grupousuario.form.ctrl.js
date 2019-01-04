/**
 *  grupousuario.form.ctrl.js
 *
 *  @author  Rafael Vinicius Barros Ferreira
 *	 @version 1.0
 */
(function(window, app, $, toastr)
{
	'use strict';

	function ControladoraFormGrupoUsuario(servicoGrupoUsuario) {
		var _this = this;
		_this.obj = null;
		_this.alterar;
		_this.formulario = $('#grupousuario_form');
		_this.botaoSubmissao = $('#salvar')
		_this.cancelarModoEdicao = $('#cancelar_edicao')

		// Cria as opções de validação do formulário
		var criarOpcoesValidacao = function criarOpcoesValidacao() {
			var opcoes = {
			};

			// Irá disparar quando a validação passar, após chamar o método validate().
			opcoes.submitHandler = function submitHandler(form) {
				var obj = _this.conteudo();

				_this.formulario.desabilitar(true);
				
				var jqXHR = _this.alterar ? servicoGrupoUsuario.atualizar(obj) : servicoGrupoUsuario.adicionar(obj);
				jqXHR.done(window.sucessoParaFormulario).fail(window.erro).always(function(){
					_this.formulario.desabilitar(false);
				});

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
			return servicoGrupoUsuario.criar(
				$('#id').val(), 
				$('#nome').val(), 
				$('#descricao').val(), 
				$('#usuarios').val()
			);
		};

		_this.configurarBotoes = function configurarBotoes() {
			_this.botaoSubmissao.on('click', _this.salvar);
			_this.cancelarModoEdicao.on('click', _this.cancelar);
		};

		_this.iniciarFormularioModoCadastro = function iniciarFormularioModoCadastro() {
			if(_this.obj != null || _this.obj != undefined) {
				_this.obj = null;
			}
			_this.formulario.parents('#painel_formulario').removeClass('desabilitado').desabilitar(false);
			_this.formulario.parents('#painel_formulario').removeClass('d-none');
			
			_this.formulario.parents('#painel_formulario').promise().done(function() {
				_this.formulario.find('#nome').focus();
				_this.popularUsuarios();
				_this.configurarBotoes();
			});
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

		_this.popularUsuarios  =  function popularUsuarios()
		{
			var sucesso = function (resposta) {
				$("#usuarios").empty();

				$.each(resposta.data, function(i ,item) {
					$("#usuarios").append($('<option>', {
						value: item.id,
						text: item.login  + '/' + item.colaborador.nome
					}));
				});

				var ids = Array();

				if(_this.obj != null || _this.obj != undefined) {
					for(var indice in _this.obj.usuarios){
						var atual =  _this.obj.usuarios[indice];
						ids.push(atual.id);
					}
					$('#usuarios').val(ids).trigger('change');
				}
				else{
					$('#usuarios').val(ids).trigger('change');
				}
			};

			var erro = function(resposta) {
				var mensagem = jqXHR.responseText || 'Erro ao popular select de farmácias.';
				toastr.error(mensagem);
				return false;
			}

			var servicoUsuario = new app.ServicoUsuario();
			var  jqXHR = servicoUsuario.todos();
			jqXHR.done(sucesso).fail(erro);
		};


		// Desenha o objeto no formulário
		_this.desenhar = function desenhar(obj) {
			_this.obj = obj;
			$('#id').val(obj.id);
			$('#nome').val(obj.nome);
			$('#descricao').val(obj.descricao);
		};

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

		};

		// Configura os eventos do formulário
		_this.configurar = function configurar(status = false) {
			_this.definirForm(status);
			$('.select2').select2({
				theme: 'bootstrap4',
				width: '100%',
			});
		};
	}; // ControladoraFormGrupoUsuario

	// Registrando
	app.ControladoraFormGrupoUsuario = ControladoraFormGrupoUsuario;

})(window, app, jQuery, toastr);