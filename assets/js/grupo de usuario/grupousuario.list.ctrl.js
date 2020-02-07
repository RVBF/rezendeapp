/**
 *  colaborador.list.ctrl.js
 *
 *  @author	Rafael Vinicius Barros Ferreira
 */
(function (window, app, $, toastr) {
   'use strict';

   function ControladoraListagemGrupoUsuario(servicoGrupoDeUsuario) {
      var _this = this;
      var _cont = 0;
      var _tabela = null;
      _this.botaoCadastrar = $('#cadastrar');
      _this.botaoEditar = $('#editar');
      _this.botaoRemover = $('#remover');
      _this.botaoAtualizar = $('#atualizar');
      _this.idTabela = $('#listagem_gruposdeusuario');
      var ctrlFormulario = new app.ControladoraFormUsuario(servicoGrupoDeUsuario);

      //Configura a tabela
      _this.opcoesDaTabela = function opcoesDaTabela() {
         var objeto = new Object();
         objeto.ajax = servicoGrupoDeUsuario.rota();

         objeto.carregando = true;
         objeto.pageLength = 20;
         objeto.lengthMenu = [20, 30, 40, 100];
         objeto.searching = true;
         objeto.ordering = true;
         objeto.searching = true;
         objeto.searchDelay = 600;
         objeto.order = 'DESC';
         objeto.cadastrarLink = 'cadastrar_grupodeusuario_link';
         objeto.columnDefs = function (data) {
            var html = '';
            html += '<div class="col col-12 col-lg-12 col-md-12 col-sm-12 mb-0-dto">';
               html += '<div class="row mb-0-dto">';
                     html += '<div class="col co-lg-10 col-md-10 col-sm-10 col-8">'
                        html += '<p class="f-12-dto"><strong>Nome : </strong>' + data.nome + '</p>'
                        html += '<p class="f-12-dto"><strong>descrição : </strong>' + data.descricao + '</p>'

                        html += '<p class="f-12-dto"><strong>Usuários : </strong>';
                           for (const index in data.usuarios) {
                              var usuario = data.usuarios[index];
                              html += usuario.login + ((parseInt(index) + 1 == data.usuarios.length) ? '.' : ',');
                           }
                        html += '</p>';
                     html += '</div>';

            html += '<div class="col col-12 col-lg-12 col-md-12 col-sm-12 mb-0-dto opc_tabela">';
               html += '<div class="col col-12 col-lg-4 col-md-4 col-sm-4 mb-0-dto">';
                  html += '<p class="mb-0-dto">';
                     html += '<a href="#" class="detalhes-dto visualizar_grupodeusuario">';
                     html += '<i class="mdi mdi-eye-outline small orange-text text-accent-4"></i>';
                     html += 'VER DETALHES';
                     html += '</a>';
                  html += '</p>';
               html += '</div>';
              
               html += '<div class="col col-12 col-lg-4 col-md-4 col-sm-4 mb-0-dto">';
                  html += '<p class="mb-0-dto">';
                     html += '<a href="#" parametros="GrupoUsuario/' + data.id + '" class="detalhes-dto configurar_acessos_link">';
                     html += '<i class="mdi mdi-key small orange-text text-accent-4"></i>';
                     html += 'ACESSOS';
                     html += '</a>';
                  html += '</p>';
               html += '</div>';
                  html += '</div>';
               html += '</div>';
            html += '</div>';



            return html;
         };
         objeto.rowsCallback = function (resposta) {
            $('.visualizar_grupodeusuario').on('click', function (event) {
               event.preventDefault();
               var objeto = _tabela.getObjetos()[$(this).parents('.listagem-padrao-item').index()];
               router.navigate('/visualizar-grupo-de-usuario/' + objeto.id);
            });
         }
         return objeto;
      };

      _this.atualizar = function atualizar() {
         _tabela.ajax.reload();
      };

      _this.configurar = function configurar() {
         _tabela = _this.idTabela.listar(_this.opcoesDaTabela());
         _this.botaoCadastrar.on('click', _this.cadastrar);
         _this.botaoEditar.on('click', _this.editar)
         _this.botaoAtualizar.on('click', _this.atualizar);
         _this.botaoRemover.on('click', _this.remover);;
      };
   } // ControladoraListagemGrupoUsuario

   // Registrando
   app.ControladoraListagemGrupoUsuario = ControladoraListagemGrupoUsuario;
})(window, app, jQuery, toastr);