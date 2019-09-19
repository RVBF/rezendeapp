/**
 *  permissao.form.ctrl.js
 *
 *  @author  Rafael Vinicius Barros Ferreira
 *	 @version 1.0
 */
(function(window, app, $, toastr)
{
	'use strict';

	function ControladoraFormPermissaoAdministrativa(servicoPermissaoAdministrativa) {
		var _this = this;

		_this.formulario = $('#permissoes_form');
		_this.botaoSubmissao = $('#configurar');
		_this.cancelarModoEdicao = $('#cancelar_edicao');
		_this.obj = null;
		// Cria as opções de validação do formulário
		var criarOpcoesValidacao = function criarOpcoesValidacao() {
			var opcoes = {
			};
			// Irá disparar quando a validação passar, após chamar o método validate().
			opcoes.submitHandler = function submitHandler(form) {
				var obj = _this.conteudo();

				_this.formulario.desabilitar(true);
				
				var terminado = function() {
					_this.formulario.desabilitar(false);
                };
                
                var sucesso = function sucesso(data, textStatus, jqXHR){        
                    if(data.status){
                        toastr.success(data.mensagem);
                    }
                    else{
                        toastr.error(data.mensagem);
                    }
                };

				var jqXHR = servicoPermissaoAdministrativa.configurarPermissoes(obj);
				jqXHR.done(sucesso).fail(window.erro).always(terminado);
			}; // submitHandler

			return opcoes;
		};
        
		// Obtém o conteúdo atual do form como um objeto
		_this.conteudo = function conteudo() {
			return servicoPermissaoAdministrativa.criar( $('#administradores_grupo').val(), $('#administradores_usuario').val());
		};

		_this.configurarBotoes = function configurarBotoes() {
			_this.botaoSubmissao.on('click', _this.salvar);
			_this.cancelarModoEdicao.on('click', _this.cancelar);
		};


		_this.popularUsuarios  =  function popularUsuarios()
		{
			var sucesso = function (resposta) {
				$("#administradores_usuario").empty();
				var ids = Array();

				$.each(resposta.data, function(i ,item) {
					if(item.administrador){

						ids.push(item.id);
					}

					$("#administradores_usuario").append($('<option>', {
						value: item.id,
						text: item.login  + '/' + item.colaborador.nome
					}));
				});
				
				$('#administradores_usuario').val(ids).trigger('change');
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
        
        _this.popularGrupos  =  function popularGrupos() {
			var sucesso = function (resposta) {
				$("#administradores_grupo").empty();
				
				var ids = Array();

				$.each(resposta.data, function(i ,item) {
					if(item.administrador){
						ids.push(item.id);
					}

					$("#administradores_grupo").append($('<option>', {
						value: item.id,
                        text: item.nome
                    }));
				});

				$('#administradores_grupo').val(ids).trigger('change');
			};

			var erro = function(resposta) {
				var mensagem = jqXHR.responseText || 'Erro ao popular select de farmácias.';
				toastr.error(mensagem);
				return false;
			}

			var servicoGrupo = new app.ServicoGrupoUsuario();
			var  jqXHR = servicoGrupo.todos();
			jqXHR.done(sucesso).fail(erro);
		};


		_this.iniciarFormularioModoCadastro = function iniciarFormularioModoCadastro() {
			if(_this.obj != null || _this.obj != undefined) {
				_this.obj = null;
			}
			_this.formulario.parents('#painel_formulario').removeClass('desabilitado').desabilitar(false);
			_this.formulario.parents('#painel_formulario').removeClass('d-none');

			_this.formulario.parents('#painel_formulario').promise().done(function(){
				_this.popularGrupos();
	
                _this.popularUsuarios();
                _this.configurarBotoes();
			});
			
		};
		_this.definirForm = function definirForm() {			
			_this.formulario.submit(false);

            _this.iniciarFormularioModoCadastro();
		}


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
            router.navigate('/tarefa');
		};

		// Configura os eventos do formulário
		_this.configurar = function configurar(status = false) {
			_this.definirForm(status);
			$('.select2').select2({
				theme: 'bootstrap4',
				width: '100%',
			});
		};
	}; // ControladoraFormPermissaoAdministrativa

	// Registrando
	app.ControladoraFormPermissaoAdministrativa = ControladoraFormPermissaoAdministrativa;

})(window, app, jQuery, toastr);
