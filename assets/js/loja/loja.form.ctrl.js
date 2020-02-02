/**
 *  loja.form.ctrl.js
 *
 *  @author  Rafael Vinicius Barros Ferreira
 *	 @version 1.0
 */
(function (window, app, $, toastr) {
   'use strict';

   function ControladoraFormLoja(servicoLoja) {
      var _this = this;

      _this.alterar;
      _this.formulario = $('#loja_form');
      _this.botaoSubmissao = $('#salvar')
      _this.servicoEndereco = new app.ServicoEndereco();

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
               "razao_social": { required: true, rangelength: [2, 100] },
               "nome_fantasia": { required: true, rangelength: [2, 100] },
               "cep": { required: true, minlength: 9, maxlength: 9 },
               "endereco": { required: true },
               "bairro": { required: true },
               "cidade": { required: true },
               "numero": { required: true },
               "complemento": { required: true }
            },

            messages: {
               "razao_social": {
                  required: "O campo razão social é obrigatório.",
                  rangelength: "o campo razão social deve conter no mínimo {2} e  no máximo {100} caracteres."
               },
               "nome_fantasia": {
                  required: "O campo lojas é obrigatório.",
                  rangelength: "o campo nome fantasia deve conter no mínimo {2} e  no máximo {100} caracteres."
               },
               "cep": {
                  required: " O campo CEP é obrigatório.",
                  minlength: "O campo CEP deve ter no mínimo 8 caracteres.",
                  maxlength: "O campo CEP ter no máximo 8 caracteres.",
               },
               "endereco": {
                  required: "Campo endereço é obrigatório."
               },
               "bairro": {
                  required: "Campo bairro é obrigatório."
               },
               "cidade": {
                  required: "Campo cidade é obrigatório."
               },

               "numero": {
                  required: "Campo bairro é obrigatório."
               },

               "complemento": {
                  required: "Campo cidade é obrigatório."
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

            var jqXHR = (window.location.href.search('editar') != -1) ? servicoLoja.atualizar(obj) : servicoLoja.adicionar(obj);

            jqXHR.done(function (resposta) {
               if (resposta.status) {
                  router.navigate('/lojas');
                  toastr.success(resposta.mensagem);
               }
               else {
                  terminado();
                  if (resposta != undefined && resposta.mensagem) $('body #msg').empty().removeClass('d-none').append(resposta.mensagem).focus();
                  if (resposta != undefined && resposta.mensagem) toastr.error(resposta.mensagem);
               }

            }).fail(window.erro).always(terminado);
         }; // submitHandler

         return opcoes;
      };

      // Obtém o conteúdo atual do form como um objeto
      _this.conteudo = function conteudo() {
         return servicoLoja.criar(
            $('#id').val(),
            $('#razao_social').val(),
            $('#nome_fantasia').val(),
            _this.servicoEndereco.criar(
               0,
               $('#cep').val(),
               $('#endereco').val(),
               $('#numero').val(),
               $('#complemento').val(),
               $('#bairro').val(),
               $('#cidade').val(),
               $('#estado').html()
            )
         );
      };

      _this.configurarBotoes = function configurarBotoes() {
         _this.botaoSubmissao.on('click', _this.salvar);
         $('#cep').mask('00000-000');
         $('#cep').on('change', function () {
            if ($(this).val().length == 9) {
               var jqXHR = _this.servicoEndereco.consultarCepViaCEP($(this).val());
               jqXHR.done(function (resposta) {
                  var elemento = JSON.parse(JSON.stringify(resposta));
                  $('#endereco').val(elemento.logradouro).focus().blur();
                  $('#cidade').val(elemento.localidade).focus().blur();
                  $('#bairro').val(elemento.bairro).focus().blur();
                  $('#estado').html(elemento.uf);
               });
            }

         });
      };

      _this.definirForm = function definirForm() {
         _this.formulario.submit(false);

         _this.formulario.find('#razao_social').focus();

         _this.configurarBotoes();

         if (window.location.href.search('visualizar') != -1) {
            $('#msg').empty();
            servicoLoja.comId(pegarId(window.location.href, 'visualizar-loja')).done(_this.desenhar);
         }
         else if (window.location.href.search('editar') != -1) {
            $('#msg').empty();
            servicoLoja.comId(pegarId(window.location.href, 'editar-loja')).done(_this.desenhar);
         } else {
            _this.formulario.find('#botoes').prepend(' <div class="col col-md-2 col-4 col-sm-2 col-lg-2"><button type="submit" id="cadastrar" class="waves-effect waves-light btn white grey-text text-darken-4 button-dto quebra-linha f-12-dto"><i class="mdi mdi-checkbox-marked-circle-outline orange-text text-accent-4 "></i>Cadastrar</button></div>').promise().done(function () {
               $('#botoes').find('#cadastrar').on('click', _this.salvar);
            });
         }
      }

      // Desenha o objeto no formulário
      _this.desenhar = function desenhar(resposta) {
         _this.obj = resposta.conteudo;
         $('#id').val(_this.obj.id);
         $('#razao_social').val(_this.obj.razaoSocial).focus().blur();
         $('#nome_fantasia').val(_this.obj.nomeFantasia).focus().blur();
         $('#cep').val(_this.obj.endereco.cep).focus().blur();
         $('#endereco').val(_this.obj.endereco.logradouro).focus().blur();
         $('#numero').val(_this.obj.endereco.numero).focus().blur();
         $('#complemento').val(_this.obj.endereco.complemento).focus().blur();
         $('#bairro').val(_this.obj.endereco.bairro).focus().blur();
         $('#cidade').val(_this.obj.endereco.cidade).focus().blur();
         $('#cidade').html(_this.obj.endereco.uf);

         if (window.location.href.search('visualizar') != -1) {
            _this.formulario.desabilitar(true);
            _this.formulario.find('#botoes').desabilitar(false);
            _this.formulario.find('#botoes').prepend(' <div class="col col-md-2 col-4 col-sm-2 col-lg-2"><button type="submit" id="renover" class="waves-effect waves-light btn white grey-text text-darken-4 button-dto quebra-linha f-12-dto"><i class="mdi mdi-delete red-text text-darken-4"></i>Remover</button></div>').promise().done(function () {
               $('#botoes').find('#renover').on('click', _this.remover);
            });
            _this.formulario.find('#botoes').prepend(' <div class="col col-md-2 col-4 col-sm-2 col-lg-2"><button type="button" id="editar" class="waves-effect waves-light btn white grey-text text-darken-4 button-dto quebra-linha f-12-dto"><i class="mdi mdi-checkbox-marked-circle-outline orange-text text-accent-4 "></i>Editar</button></div>').promise().done(function () {
               _this.formulario.find('#editar').on('click', function (event) {
                  router.navigate('/editar-loja/' + _this.obj.id);
               });
            });

         } else if (window.location.href.search('editar') != -1) {
            _this.alterar = true;
            var html = '';
            html += '<div class="col col-md-2 col-4 col-sm-2 col-lg-2">';
            html += '<button id="salvar" type="submit" class="waves-effect waves-light btn white grey-text text-darken-4 button-dto quebra-linha f-12-dto">';
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
            title: 'Deseja remover este Loja?',
            message: 'Id: ' + _this.obj.id + '.<br> Título: ' + (_this.obj.razaoSocial + '. <br> Nome fantasia : ' + _this.obj.nomeFantasia) + '. <br> Endereço: ' + _this.obj.endereco.logradouro + ', ' + _this.obj.endereco.numero + '-' + _this.obj.endereco.complemento + ', ' + _this.obj.endereco.bairro + ', ' + _this.obj.endereco.cidade + '/' + _this.obj.endereco.uf + '-' + _this.obj.endereco.cep + '.',
            size: BootstrapDialog.SIZE_LARGE,
            buttons: [{
               label: '<u>S</u>im',
               hotkey: 'S'.charCodeAt(0),
               action: function (dialog) {
                  servicoLoja.remover(_this.obj.id).done(function (resposta) {
                     console.log(resposta);
                     if (resposta.status) {
                        router.navigate('/lojas');
                        toastr.success(resposta.mensagem);
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
      _this.configurar = function configurar() {
         _this.definirForm();

      };
   }; // ControladoraFormLoja

   // Registrando
   app.ControladoraFormLoja = ControladoraFormLoja;

})(window, app, jQuery, toastr);


var url = window.location.href;
