/**
 *  usuario.form.ctrl.js
 *
 *  @author  Rafael Vinicius Barros Ferreira
 *	 @version 1.0
 */
(function(window, app, $, toastr)
{
	'use strict';

	function ControladoraFormUsuario(servicoUsuario) {
		var _this = this;

		_this.alterar;
		_this.formulario = $('#usuario_form');
		_this.botaoSubmissao = $('#salvar');
		_this.cancelarModoEdicao = $('#cancelar_edicao');
		_this.obj = null;
		// Cria as opções de validação do formulário
		var criarOpcoesValidacao = function criarOpcoesValidacao() {
			var opcoes = {
				rules: {
					"nome" : {
						required : true,
						rangelength : [3, 50]
					},
					"sobrenome" : {
						required : true,
						rangelength : [3, 50]
					},
					"login" :{
						required : true
					},
					"loja" :{
						required : true
					},
					"setor" :{
						required : true
					},
					"senha": {
						required : true,
						rangelength : [ 3, 20 ]
					},

					"confirmacao_senha": {
						required : true,
						equalTo : "#senha"
					}

				},

				messages: { 
					"nome": { 
						required: 'o campo nome é obrigatório.',
						rangelength	: $.validator.format("O campo nome deve ter entre {3} e {50} caracteres.")
					},
					"sobrenome": { 
						required: 'o campo sobrenome é obrigatório.',
						rangelength	: $.validator.format("O campo nome deve ter entre {3} e {50} caracteres.")
					},

					"login": { 
						required: 'o campo login é obrigatório.'
					},

					"loja": { 
						required: 'o campo loja é obrigatório.'
					},

					"setor": { 
						required: 'o campo setor é obrigatório.'
					},

					"senha": {
						required: 'o campo senha é obrigatório.',
						rangelength	: $.validator.format("A senha deve ter entre {3} e {50} caracteres.")
					},

					"confirmacao_senha": {
						required: 'o campo confirmação de senha é obrigatório.',
						equalTo	: "O campo senha e confirmação de senha devem ser iguais."
					}
				}
			};

			// Irá disparar quando a validação passar, após chamar o método validate().
			opcoes.submitHandler = function submitHandler(form) {
				var obj = _this.conteudo();

					
				var terminado = function() {
					_this.formulario.desabilitar(false);
				};
				
				_this.formulario.desabilitar(true);
			
				var jqXHR = _this.alterar ? servicoUsuario.atualizar(obj) : servicoUsuario.adicionar(obj);
				jqXHR.done(function(resposta) {
					if(resposta.status){
						router.navigate('/colaboradores');
						toastr.success('Colaborador Adicionado com sucesso!');
					}
					else{
						$('body #msg').empty().removeClass('d-none').append(resposta.mensagem);
						toastr.error(resposta.mensagem);
					}

				}).fail(window.erro).always(terminado);
			}; // submitHandler

			return opcoes;
		};
        
		// Obtém o conteúdo atual do form como um objeto
		_this.conteudo = function conteudo() {
			return servicoUsuario.criar(
				$('#id').val(),
				$('#nome').val(),
				$('#sobrenome').val(),
				$('#email').val(),
				$('#login').val(), 
				$('#senha').val(), 
				$('#lojas').formSelect('getSelectedValues'),
				$('#setor').val()
			);
		};

		_this.configurarBotoes = function configurarBotoes() {
			_this.botaoSubmissao.on('click', _this.salvar);
			_this.cancelarModoEdicao.on('click', _this.cancelar);
		};


		_this.popularLojas  =  function popularLojas(valor = 0)
		{
			var sucesso = function (resposta) {
				$("#lojas").empty();
		
				$.each(resposta.data, function(i ,item) {
					$("#lojas").append($('<option>', {
						value: item.id,
						text: item.razaoSocial  + '/' + item.nomeFantasia
					}));
				});


				var ids = Array();

				if(_this.obj != null || _this.obj != undefined) {
					for(var indice in _this.obj.colaborador.lojas){
						var atual =  _this.obj.colaborador.lojas[indice];
						ids.push(atual.id);
					}
					$('#lojas').formSelect();
				}
				else{
					$('#lojas').formSelect();
				}
			
			};

			var erro = function(resposta) {
				var mensagem = jqXHR.responseText || 'Erro ao popular select de farmácias.';
				toastr.error(mensagem);
				return false;
			}

			var servicoLoja = new app.ServicoLoja();
			var  jqXHR = servicoLoja.todos();
			jqXHR.done(sucesso).fail(erro);
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


				var ids = Array();

				if(_this.obj != null || _this.obj != undefined) {
					for(var indice in _this.obj.colaborador.lojas){
						var atual =  _this.obj.colaborador.lojas[indice];
						ids.push(atual.id);
					}
					$('#setor').formSelect();
				}
				else{
					$('#setor').formSelect();
				}
			
			};

			var erro = function(resposta) {
				var mensagem = jqXHR.responseText || 'Erro ao popular select de farmácias.';
				toastr.error(mensagem);
				return false;
			}

			var servicoSetor = new app.ServicoSetor();
			var  jqXHR = servicoSetor.todos();
			jqXHR.done(sucesso).fail(erro);
		};

		_this.iniciarFormularioModoCadastro = function iniciarFormularioModoCadastro() {
			if(_this.obj != null || _this.obj != undefined) {
				_this.obj = null;
			}
			_this.formulario.parents('#painel_formulario').removeClass('desabilitado').desabilitar(false);
			_this.formulario.parents('#painel_formulario').removeClass('d-none');

			_this.formulario.parents('#painel_formulario').promise().done(function(){
				_this.formulario.find('#nome').focus();

				_this.popularLojas();
				_this.popularSetores();

	
				_this.configurarBotoes();
	
	
				if(!$('#senha').hasClass('campo_obrigatorio')){
					$('#senha').parent('div').removeClass('d-none');
					$('#confirmacao_senha').parent('div').removeClass('d-none');
		
					$('#senha').addClass('campo_obrigatorio');
					$('#lojas').addClass('campo_obrigatorio');
					$('#confirmacao_senha ').addClass('campo_obrigatorio');
				}
			});
			
		};

		_this.iniciarFormularioModoEdicao = function iniciarFormularioModoEdicao() {
			_this.iniciarFormularioModoCadastro();
			$('#msg').empty();

			$('#login').parent('div').attr('class', 'col-md-12 col-xs-12 co-sm-12 col-12');
			$('#senha').parent('div').addClass('d-none');
			$('#confirmacao_senha').parent('div').addClass('d-none');

			$('#lojas').removeClass('campo_obrigatorio');
			$('#senha').removeClass('campo_obrigatorio');
			$('#confirmacao_senha ').removeClass('campo_obrigatorio');
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
			_this.obj = obj;
			$('#id').val(obj.id);
			$('#nome').val(obj.colaborador.nome);
			$('#sobrenome').val(obj.colaborador.sobrenome);
			$('#email').val(obj.colaborador.email);
			$('#login').val(obj.login);
		};

		_this.salvar = function salvar() {
			_this.formulario.validate(criarOpcoesValidacao());
        };
		

		// Configura os eventos do formulário
		_this.configurar = function configurar(status = false) {
			_this.definirForm(status);
		};
	}; // ControladoraFormUsuario

	// Registrando
	app.ControladoraFormUsuario = ControladoraFormUsuario;

})(window, app, jQuery, toastr);
