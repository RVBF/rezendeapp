/**
 *  colaborador.form.ctrl.js
 *
 *  @author  Rafael Vinicius Barros Ferreira
 *	 @version 1.0
 */
(function(window, app, $, toastr)
{
	'use strict';

	function ControladoraFormGrupoDeUsuario(servicoGrupoDeUsuario) {
		var _this = this;

        _this.formulario = $('#grupousuario_form');
		_this.botaoSubmissao = $('#salvar');
		_this.cancelarModoEdicao = $('#cancelar_edicao');
        _this.obj = null;
        _this.servicoUsuario = new app.ServicoUsuario();

		_this.avatar = {};


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
				rules: {
					"nome" : {
						required : true,
					},
					"descricao" : {
						required : true,
					},
					"usuarios" :{
						required : true
					}
					
				},

				messages: { 
					"nome": { 
						required: 'o campo nome é obrigatório.'
					},
					"descricao": { 
						required: 'o campo descricao é obrigatório.'
					},

					"usuarios": { 
						required: 'o campo usuarios é obrigatório.'
					}
				}
			};

			// Irá disparar quando a validação passar, após chamar o método validate().
			opcoes.submitHandler = function submitHandler(form) {
                var obj = _this.conteudo();
				var terminado = function terminado() {
					_this.formulario.desabilitar(false);
				};
				
				 _this.formulario.desabilitar(true);
			
				var jqXHR = (window.location.href.search('editar') != -1) ? servicoGrupoDeUsuario.atualizar(obj) : servicoGrupoDeUsuario.adicionar(obj);
				
				jqXHR.done(function(resposta) {
					if(resposta.status){
						router.navigate('/grupos-de-usuario');
						toastr.success('Grupo Adicionado com sucesso!');
					}
					else{
						terminado();
						$('body #msg').empty().removeClass('d-none').append(resposta.mensagem).focus();
						toastr.error(resposta.mensagem);
					}

				}).fail(window.erro).always(terminado);
			}; // submitHandler

			return opcoes;
		};
        
		// Obtém o conteúdo atual do form como um objeto
		_this.conteudo = function conteudo() {

			return servicoGrupoDeUsuario.criar(
				$('#id').val(),
				$('#nome').val(),
				$('#descricao').val(),
				$('#usuarios').formSelect('getSelectedValues')
			);
		};

		_this.configurarBotoes = function configurarBotoes() {
			_this.botaoSubmissao.on('click', _this.salvar);
		};

		_this.popularUsuarios  =  function popularUsuarios(valor = 0)
		{
			var sucesso = function (resposta) {
				$("#usuarios").empty();
		
				$.each(resposta.data, function(i ,item) {
					$("#usuarios").append($('<option>', {
						value: item.id,
						text: item.nome  + '/' + item.sobrenome
					}));
				});

				$('#usuarios').formSelect();
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

		_this.definirForm = function definirForm(status) {			
			_this.formulario.submit(false);

			_this.formulario.find('#nome').focus();
			
			_this.popularUsuarios();

			_this.configurarBotoes();

			if(window.location.href.search('visualizar') != -1) {
                $('#msg').empty();
                servicoGrupoDeUsuario.comId(pegarId(window.location.href,'visualizar-grupo-de-usuario')).done(_this.desenhar);
            }
			else  if(window.location.href.search('editar') != -1) {
                $('#msg').empty();
				servicoGrupoDeUsuario.comId(pegarId(window.location.href,'editar-grupo-de-usuario')).done(_this.desenhar);
            }else{
                _this.formulario.find('#botoes').prepend(' <div class="col col-md-2 col-4 col-sm-2 col-lg-2"><button type="submit" id="cadastrar" class="waves-effect waves-light btn white grey-text text-darken-4 button-dto quebra-linha f-12-dto"><i class="mdi mdi-checkbox-marked-circle-outline orange-text text-accent-4 "></i>Cadastrar</button></div>').promise().done(function(){
                    $('#botoes').find('#cadastrar').on('click', _this.salvar);
                });
            }
		}

		// Desenha o objeto no formulário
		_this.desenhar = function desenhar(resposta) {
			_this.obj = resposta.conteudo;
			$('#id').val(_this.obj.id).focus().blur();
			$('#nome').val(_this.obj.nome).focus().blur();
			$('#descricao').val(_this.obj.descricao).focus().blur();

            if(resposta.conteudo.usuarios != null){
                for (const index in resposta.conteudo.usuarios) {
					var usuario = resposta.conteudo.usuarios[index];
					console.log(usuario);
                    $('#usuarios option').each(function (i, value) {
                       if(parseInt($(this).val()) == usuario.id) $(this).attr('selected', true); 
                    });
                }
                $('#usuarios').trigger('click').focus().blur();
            }

            if(window.location.href.search('visualizar') != -1){
                _this.formulario.desabilitar(true);
				_this.formulario.find('#botoes').desabilitar(false);
				_this.formulario.find('#botoes').prepend(' <div class="col col-md-2 col-4 col-sm-2 col-lg-2"><button type="submit" id="remover" class="waves-effect waves-light btn white grey-text text-darken-4 button-dto quebra-linha f-12-dto"><i class="mdi mdi-delete red-text text-darken-4"></i>Remover</button></div>').promise().done(function(){
                    $('#botoes').find('#remover').on('click', _this.remover);
                });
                _this.formulario.find('#botoes').prepend(' <div class="col col-md-2 col-4 col-sm-2 col-lg-2"><button type="button" id="editar" class="waves-effect waves-light btn white grey-text text-darken-4 button-dto quebra-linha f-12-dto"><i class="mdi mdi-checkbox-marked-circle-outline orange-text text-accent-4 "></i>Editar</button></div>').promise().done(function(){
                    _this.formulario.find('#editar').on('click', function(event){
                        router.navigate('/editar-grupo-de-usuario/'+ _this.obj.id);
                    });
                });
			
            } else if(window.location.href.search('editar') != -1){
                _this.alterar = true;
				var html = '';
				html += '<div class="col col-md-2 col-4 col-sm-2 col-lg-2">';
				html += '<button id="salvar" type="submit" class="waves-effect waves-light btn white grey-text text-darken-4 button-dto quebra-linha f-12-dto">';
				html += '<i class="mdi mdi-checkbox-marked-circle-outline orange-text text-accent-4 ">';
				html += '</i>salvar</button>';
				html += '</div>';

				_this.formulario.find('#botoes').prepend(html).promise().done(function(){
					$('#salvar').on('click', _this.salvar);
				});
            }

		};

		_this.salvar = function salvar() {
			_this.formulario.validate(criarOpcoesValidacao());
		};
		
		_this.remover = function remover(){
			BootstrapDialog.show({
				type	: BootstrapDialog.TYPE_DANGER,
				title	: 'Deseja remover este Grupo de Usuário?',
				message	: 'Id: ' + _this.obj.id + '. <br> Grupo de usuário: ' + (_this.obj.nome + '.<br> Descrição : ' + _this.obj.descricao) + '.',
				size	: BootstrapDialog.SIZE_LARGE,
				buttons	: [ {
						label	: '<u>S</u>im',
						hotkey	: 'S'.charCodeAt(0),
						action	: function(dialog){
							servicoGrupoDeUsuario.remover(_this.obj.id).done(function (resposta) {
								if(resposta.status){
									router.navigate('/grupos-de-usuario');
									toastr.success(resposta.mensagem);
									dialog.close();

								}
								else{
									toastr.error(resposta.mensagem);

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
		_this.configurar = function configurar() {
			_this.definirForm();
		};
	}; // ControladoraFormGrupoDeUsuario

	// Registrando
	app.ControladoraFormGrupoDeUsuario = ControladoraFormGrupoDeUsuario;

})(window, app, jQuery, toastr);
