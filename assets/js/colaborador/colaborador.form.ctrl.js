/**
 *  colaborador.form.ctrl.js
 *
 *  @author  Rafael Vinicius Barros Ferreira
 *	 @version 1.0
 */
(function (window, app, $, toastr) {
   'use strict';

   function ControladoraFormColaborador(servicoColaborador) {
      var _this = this;

      _this.formulario = $('#colaborador_form');
      _this.botaoSubmissao = $('#salvar');
      _this.cancelarModoEdicao = $('#cancelar_edicao');
      _this.obj = null;
      _this.servicoUsuario = new app.ServicoUsuario();

      _this.avatar = {};


      var pegarId = function pegarId(url, palavra) {

         // Terminando com "ID/palavra"
         var regexS = palavra + '+\/[0-9]{1,}';

         var regex = new RegExp(regexS);
         var resultado = regex.exec(url);

         if (!resultado || resultado.length < 1) {
            return 0;
         }

         var array = resultado[0].split('/');
         return array[1];
      };

      // Cria as opções de validação do formulário
      var criarOpcoesValidacao = function criarOpcoesValidacao() {
         var opcoes = {
            rules: {
               "nome": {
                  required: true,
                  rangelength: [3, 50]
               },
               "sobrenome": {
                  required: true,
                  rangelength: [3, 50]
               },
               "login": {
                  required: true
               },
               "loja": {
                  required: true
               },
               "setor": {
                  required: true
               },
               "senha": {
                  required: true,
                  rangelength: [3, 20]
               },

               "confirmacao_senha": {
                  required: true,
                  equalTo: "#senha"
               }

            },

            messages: {
               "nome": {
                  required: 'o campo nome é obrigatório.',
                  rangelength: $.validator.format("O campo nome deve ter entre {3} e {50} caracteres.")
               },
               "sobrenome": {
                  required: 'o campo sobrenome é obrigatório.',
                  rangelength: $.validator.format("O campo nome deve ter entre {3} e {50} caracteres.")
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
                  rangelength: $.validator.format("A senha deve ter entre {3} e {50} caracteres.")
               },

               "confirmacao_senha": {
                  required: 'o campo confirmação de senha é obrigatório.',
                  equalTo: "O campo senha e confirmação de senha devem ser iguais."
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

            var jqXHR = (window.location.href.search('editar') != -1) ? servicoColaborador.atualizar(obj) : servicoColaborador.adicionar(obj);

            jqXHR.done(function (resposta) {
               if (resposta.status) {
                  router.navigate('/colaboradores');
                  toastr.success('Colaborador Adicionado com sucesso!');
               }
               else {
                  terminado();
                  if (resposta != undefined && resposta.mensagem) $('body #msg').empty().removeClass('d-none').append(resposta.mensagem).focus();
                  if (resposta != undefined && resposta.mensagem) toastr.error(resposta.mensagem);
               }

            }).fail(window.erro);
         }; // submitHandler

         return opcoes;
      };

      // Obtém o conteúdo atual do form como um objeto
      _this.conteudo = function conteudo() {

         return servicoColaborador.criar(
            $('#id').val(),
            $('#nome').val(),
            $('#sobrenome').val(),
            $('#email').val(),
            _this.servicoUsuario.criar(
               0,
               $('#login').val(),
               $('#senha').val()
            ),
            $('#lojas').formSelect('getSelectedValues'),
            $('#setor').val(),
            _this.avatar
         );
      };

      _this.configurarBotoes = function configurarBotoes() {
         _this.botaoSubmissao.on('click', _this.salvar);

         _this.formulario.find('input[type="file"]').change(function (evt) {
            var elemento = $(this);
            var file = evt.target.files[0];
            var nomeArquivo = $(this).val().split('\\');
            nomeArquivo = nomeArquivo[nomeArquivo.length - 1];
            var reader = new FileReader();
            reader.onerror = function (evt) {
               switch (evt.target.error.code) {
                  case evt.target.error.NOT_FOUND_ERR:
                     alert('File Not Found!');
                     break;
                  case evt.target.error.NOT_READABLE_ERR:
                     alert('File is not readable');
                     break;
                  case evt.target.error.ABORT_ERR:
                     break; // noop
                  default:
                     alert('An error occurred reading this file.');
               };
            };
            reader.onprogress = function updateProgress(evt) {
               var progress = document.querySelector('.percent');

               // evt is an ProgressEvent.
               if (evt.lengthComputable) {
                  var percentLoaded = Math.round((evt.loaded / evt.total) * 100);
                  // Increase the progress bar length.
                  if (percentLoaded < 100) {
                     progress.style.width = percentLoaded + '%';
                     progress.textContent = percentLoaded + '%';
                  }
               }
            };
            reader.onabort = function (e) {
               alert('File read cancelled');
            };

            reader.onload = function () {
               _this.avatar = { 'nome': nomeArquivo, 'arquivo': reader.result, 'tipo': file.type };
               elemento.prev('img').attr('src', reader.result);
            };

            reader.readAsDataURL(file);
         });

         _this.formulario.find('img').on('click', function (event) {
            $(this).next("input[type='file']").trigger('click');
         });
      };

      _this.popularLojas = function popularLojas(valor = 0) {
         var sucesso = function (resposta) {
            $("#lojas").empty();

            $.each(resposta.data, function (i, item) {
               $("#lojas").append($('<option>', {
                  value: item.id,
                  text: item.razaoSocial + '/' + item.nomeFantasia
               }));
            });

            var ids = Array();

            if (_this.obj != null || _this.obj != undefined) {
               for (var indice in _this.obj.colaborador.lojas) {
                  var atual = _this.obj.colaborador.lojas[indice];
                  ids.push(atual.id);
               }
               $('#lojas').formSelect();
            }
            else {
               $('#lojas').formSelect();
            }
         };

         var erro = function (resposta) {
            var mensagem = jqXHR.responseText || 'Erro ao popular select de farmácias.';
            toastr.error(mensagem);
            return false;
         }

         var servicoLoja = new app.ServicoLoja();
         var jqXHR = servicoLoja.todosOpcoes();
         jqXHR.done(sucesso).fail(erro);
      };

      _this.popularSetores = function popularSetores(valor = 0) {
         var sucesso = function (resposta) {
            $("#setor").empty();

            $.each(resposta.data, function (i, item) {
               $("#setor").append($('<option>', {
                  value: item.id,
                  text: item.titulo
               }));
            });

            var ids = Array();

            if (_this.obj != null || _this.obj != undefined) {
               for (var indice in _this.obj.colaborador.lojas) {
                  var atual = _this.obj.colaborador.lojas[indice];
                  ids.push(atual.id);
               }
               $('#setor').formSelect();
            }
            else {
               $('#setor').formSelect();
            }

         };

         var erro = function (resposta) {
            var mensagem = jqXHR.responseText || 'Erro ao popular select de farmácias.';
            toastr.error(mensagem);
            return false;
         }

         var servicoSetor = new app.ServicoSetor();
         var jqXHR = servicoSetor.todosOpcoes();

         jqXHR.done(sucesso).fail(erro);
      };

      _this.definirForm = function definirForm(status) {
         _this.formulario.submit(false);
         
         _this.formulario.find('.avatar').attr('src',  window.location.pathname + 'assets/images/avatar-padrao.png');
         _this.formulario.find('#nome').focus();

         _this.popularLojas();
         _this.popularSetores();

         _this.configurarBotoes();

         if (window.location.href.search('visualizar') != -1) {
            $('#msg').empty();
            $('#senha').parent().parent().parent().remove();
            $('#confirmacao_senha').parent().parent().parent().remove();
            servicoColaborador.comId(pegarId(window.location.href, 'visualizar-colaborador')).done(_this.desenhar);
         }
         else if (window.location.href.search('editar') != -1) {
            $('#msg').empty();
            $('#senha').parent().parent().parent().remove();
            $('#confirmacao_senha').parent().parent().parent().remove();
            servicoColaborador.comId(pegarId(window.location.href, 'editar-colaborador')).done(_this.desenhar);
         } else {
				_this.formulario.find('#botoes').prepend(' <div class="col col-md-4 col-12 col-sm-5 col-lg-4"><button type="submit" id="cadastrar" class="waves-effect waves-light btn white grey-text text-darken-4 col-12 quebra-linha"><i class="mdi mdi-checkbox-marked-circle-outline orange-text text-accent-4 "></i>Cadastrar</button></div>').promise().done(function () {
					$('#botoes').find('#cadastrar').on('click', _this.salvar);
				});
         }
      }

      // Desenha o objeto no formulário
      _this.desenhar = function desenhar(resposta) {
         _this.obj = resposta.conteudo;
         $('#id').val(_this.obj.id).focus().blur();
         $('#nome').val(_this.obj.nome).focus().blur();
         $('#sobrenome').val(_this.obj.sobrenome).focus().blur();
         $('#email').val(_this.obj.email).focus().blur();
         $('#login').val(_this.obj.usuario.login).focus().blur();

         if (_this.obj.avatar != null) {
            var caminho = _this.obj.avatar.patch.split('/');
            var nome = caminho[caminho.length - 1];
            _this.avatar = { 'nome': nome, 'arquivo': _this.obj.avatar.arquivoBase64, 'tipo': _this.obj.avatar.tipo };
            $('.avatar:first').attr('src', _this.obj.avatar.arquivoBase64)
         }

         if (resposta.conteudo.lojas != null) {
            for (const index in resposta.conteudo.lojas) {
               var loja = resposta.conteudo.lojas[index];
               $('#lojas option').each(function (i, value) {
                  if (parseInt($(this).val()) == loja.id) $(this).attr('selected', true);
               });
            }
            $('#lojas').trigger('click').focus().blur();
         }

         if (window.location.href.search('visualizar') != -1) {
            _this.formulario.desabilitar(true);
            _this.formulario.find('#botoes').desabilitar(false);
            _this.formulario.find('#botoes').prepend(' <div class="col col-md-4 col-12 col-sm-5 col-lg-4"><button type="submit" id="remover" class="waves-effect waves-light btn white grey-text text-darken-4 col-12 quebra-linha"><i class="mdi mdi-delete red-text text-darken-4"></i>Remover</button></div>').promise().done(function () {
               $('#botoes').find('#remover').on('click', _this.remover);
            });
				_this.formulario.find('#botoes').prepend(' <div class="col col-md-4 col-12 col-sm-5 col-lg-4"><button type="button" id="editar" class="waves-effect waves-light btn white grey-text text-darken-4 col-12 quebra-linha"><i class="mdi mdi-checkbox-marked-circle-outline orange-text text-accent-4 "></i>Editar</button></div>').promise().done(function () {
					_this.formulario.find('#editar').on('click', function (event) {
                  router.navigate('/editar-colaborador/' + _this.obj.id);
               });
            });

         } else if (window.location.href.search('editar') != -1) {
            _this.alterar = true;
            var html = '';
            html += '<div class="col col-md-4 col-12 col-sm-5 col-lg-4">';
            html += '<button id="salvar" type="submit" class="waves-effect waves-light btn white grey-text text-darken-4 col-12 quebra-linha">';
            html += '<i class="mdi mdi-checkbox-marked-circle-outline orange-text text-accent-4 ">';
            html += '</i>salvar</button>';
            html += '</div>';

            _this.formulario.find('#botoes').prepend(html).promise().done(function () {
               $('#salvar').on('click', _this.salvar);
            });
         }

      };

      _this.salvar = function salvar() {
         _this.formulario.validate(criarOpcoesValidacao());
      };

      _this.remover = function remover() {
         BootstrapDialog.show({
            type: BootstrapDialog.TYPE_DANGER,
            title: 'Deseja remover este colaborador?',
            message: 'Id: ' + _this.obj.id + '.<br> Colaborador: ' + (_this.obj.nome + ' ' + _this.obj.sobrenome) + '.',
            size: BootstrapDialog.SIZE_LARGE,
            buttons: [{
               label: '<u>S</u>im',
               hotkey: 'S'.charCodeAt(0),
               action: function (dialog) {
                  servicoColaborador.remover(_this.obj.id).done(function (resposta) {
                     if (resposta.status) {
                        router.navigate('/colaboradores');
                        toastr.success('Colaborador removido com sucesso!');
                        dialog.close();

                     }
                     else {
                        if (resposta != undefined && resposta.mensagem) toastr.error(resposta.mensagem);

                        dialog.close();
                     }
                  });
               }
            }, {
               label: '<u>N</u>ão',
               hotkey: 'N'.charCodeAt(0),
               action: function (dialog) {
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
   }; // ControladoraFormColaborador

   // Registrando
   app.ControladoraFormColaborador = ControladoraFormColaborador;

})(window, app, jQuery, toastr);
