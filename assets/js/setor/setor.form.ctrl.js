/**
 *  setor.form.ctrl.js
 *
 *  @author  Rafael Vinicius Barros Ferreira
 *	 @version 1.0
 */
(function(window, app, $, toastr)
{
	'use strict';

	function ControladoraFormSetor(servicoSetor) {
		var _this = this;

		_this.alterar;
		_this.formulario = $('#setor_form');
		_this.botaoSubmissao = $('#salvar')
		
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
		// Cria as opções de validação do formulário
		var criarOpcoesValidacao = function criarOpcoesValidacao() {
			var opcoes = {
				rules: {
					"titulo": {required : true,
						rangelength : [ 2, 100 ] 
					},

					"descricao": {required : true}
				},

				messages: {
					"titulo": {
						required    : "O campo título é obrigatório.",
						rangelength : "O campo deve conter no mínimo {2} e no máximo {100} caracteres."
					},

					'descricao' : {
						required : "O campo descrição é obrigatório"
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
			
				var jqXHR = (window.location.href.search('editar') != -1) ? servicoSetor.atualizar(obj) : servicoSetor.adicionar(obj);
				
				jqXHR.done(function(resposta) {
					if(resposta.status){
						router.navigate('/setores');
						toastr.success(resposta.mensagem);
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
			return servicoSetor.criar(
                $('#id').val(),
                $('#titulo').val(),
				$('#descricao').val()
			);
		};

		_this.configurarBotoes = function configurarBotoes() {
			_this.botaoSubmissao.on('click', _this.salvar);
		};

		_this.definirForm = function definirForm(status) {			
			_this.formulario.submit(false);

			_this.formulario.find('#titulo').focus();
			
			_this.configurarBotoes();

			if(window.location.href.search('visualizar') != -1) {
                $('#msg').empty();
                servicoSetor.comId(pegarId(window.location.href,'visualizar-setor')).done(_this.desenhar);
            }
			else  if(window.location.href.search('editar') != -1) {
                $('#msg').empty();
				servicoSetor.comId(pegarId(window.location.href,'editar-setor')).done(_this.desenhar);
            }else{
                _this.formulario.find('#botoes').prepend(' <div class="col col-md-3 col-3 col-sm-3 col-lg-3 d-flex"><button type="submit" id="cadastrar" class="waves-effect waves-light btn white grey-text text-darken-4 button-dto quebra-linha f-12-dto"><i class="mdi mdi-checkbox-marked-circle-outline orange-text text-accent-4 "></i>Cadastrar</button></div>').promise().done(function(){
                    $('#botoes').find('#cadastrar').on('click', _this.salvar);
                });
            }
		}

		// Desenha o objeto no formulário
		_this.desenhar = function desenhar(resposta) {
			_this.obj = resposta.conteudo;
			$('#id').val(_this.obj.id).focus().blur();
			$('#titulo').val(_this.obj.titulo).focus().blur();
			$('#descricao').val(_this.obj.descricao).focus().blur();

            if(window.location.href.search('visualizar') != -1){
                _this.formulario.desabilitar(true);
				_this.formulario.find('#botoes').desabilitar(false);
				_this.formulario.find('#botoes').prepend(' <div class="col col-md-3 col-3 col-sm-3 col-lg-3 d-flex"><button type="submit" id="renover" class="waves-effect waves-light btn white grey-text text-darken-4 button-dto quebra-linha f-12-dto"><i class="mdi mdi-delete red-text text-darken-4"></i>Remover</button></div>').promise().done(function(){
                    $('#botoes').find('#renover').on('click', _this.remover);
                });
                _this.formulario.find('#botoes').prepend(' <div class="col col-md-3 col-3 col-sm-3 col-lg-3 d-flex"><button type="button" id="editar" class="waves-effect waves-light btn white grey-text text-darken-4 button-dto quebra-linha f-12-dto"><i class="mdi mdi-checkbox-marked-circle-outline orange-text text-accent-4 "></i>Editar</button></div>').promise().done(function(){
                    _this.formulario.find('#editar').on('click', function(event){
                        router.navigate('/editar-setor/'+ _this.obj.id);
                    });
                });
			
            } else if(window.location.href.search('editar') != -1){
                _this.alterar = true;
				var html = '';
				html += '<div class="col col-md-3 col-3 col-sm-3 col-lg-3 d-flex">';
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
				title	: 'Deseja remover este Setor?',
				message	: 'Id: ' + _this.obj.id + '; Título: ' + (_this.obj.titulo + '<br> Descrição : ' + _this.obj.descricao) + '!',
				size	: BootstrapDialog.SIZE_LARGE,
				buttons	: [ {
						label	: '<u>S</u>im',
						hotkey	: 'S'.charCodeAt(0),
						action	: function(dialog){
							servicoSetor.remover(_this.obj.id).done(function (resposta) {
								if(resposta.status){
									router.navigate('/setores');
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
	}; // ControladoraFormSetor

	// Registrando
	app.ControladoraFormSetor = ControladoraFormSetor;

})(window, app, jQuery, toastr);


var url = window.location.href;
