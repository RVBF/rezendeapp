(function (window, $) {
   'use strict';

   function ListagemPadrao(elemento, opcoes) {
      var _this = this;
      var loader = $('#mainLoadertable');
      let tempoDeAnimacao = 250;
      var listagemPadrao = elemento;
      var ultimaResposta = null;
      _this.recordsTotal = null;
      _this.paginacao = {};
      _this.objetos = { 'objetos': [] };
      _this.pesquisaTimeOut;
      _this.caixaPesquisa = '';

      _this.mostrarTelaDeCarregamento = function mostrarTelaDeCarregamento() {
         loader.find('.loadertable').removeClass('d-none');

         $('body .listagem-padrao .card-panel:first').css('overflow', 'hidden');

         loader.find('.loadertablePageLeft').animate({ 'width': '60%' }, tempoDeAnimacao);
         loader.find('.loadertablePageRight').animate({ 'width': '60%' }, tempoDeAnimacao);
      }

      _this.tirarTelaDeCarregamento = function tirarTelaDeCarregamento() {
         loader.find('.loadertable').addClass('d-none');
         $('body .listagem-padrao .card-panel:first').css('overflow', 'auto');

         loader.find('.loadertablePageLeft').animate({ 'width': '0px' }, tempoDeAnimacao);
         loader.find('.loadertablePageRight').animate({ 'width': '0px' }, tempoDeAnimacao);
      }
      
      _this.definirEventosTabela = function definirEventosTabela() {
         listagemPadrao.find('.atualizar').on('click', _this.atualizarTabela);
         listagemPadrao.on('click', '.paginate_button', function () {
            event.preventDefault();
            var elemento = $(this);
            var paginaAnterior = listagemPadrao.find('#listagem_paginacao').find('.pagina-atual:first');
            paginaAnterior.removeClass('pagina-atual');

            if(elemento.hasClass('paginacao-proximo') && paginaAnterior.next().hasClass('d-none')){
               paginaAnterior.nextAll('.d-none:first').removeClass('d-none').addClass('pagina-atual');
               paginaAnterior.prevAll('.paginacao-anterior:first').removeClass('disabled').desabilitar(false);
               listagemPadrao.find('.pagination').find('li').each(function (i, value) {
                  if(!$(this).hasClass('d-none') && !$(this).hasClass('paginacao-anterior')){
                     $(this).addClass('d-none');
                     return false;
                  }
               });

            } 
            else if(elemento.hasClass('paginacao-proximo') && paginaAnterior.next().hasClass('paginacao-proximo')){
               elemento.addClass('disabled').desabilitar(true);
               elemento.prev().addClass('pagina-atual');
            }
            else if(elemento.hasClass('paginacao-proximo')){
               paginaAnterior.next().addClass('pagina-atual');
            }
            else if(elemento.hasClass('paginacao-anterior') && paginaAnterior.prev().hasClass('d-none')){
               paginaAnterior.prevAll('.d-none:first').removeClass('d-none').addClass('pagina-atual');
               paginaAnterior.nextAll('.paginacao-proximo:first').removeClass('disabled').desabilitar(false);
              $( listagemPadrao.find('.pagination').find('li').get().reverse()).each(function (i, value) {
                  if(!$(this).hasClass('d-none') && !$(this).hasClass('paginacao-proximo')){
                     $(this).addClass('d-none');
                     return false;
                  }
               });

            } 
            else if(elemento.hasClass('paginacao-anterior') && paginaAnterior.prev().hasClass('paginacao-anterior')){
               elemento.addClass('disabled').desabilitar(true);
               elemento.next().addClass('pagina-atual');
            }
            else if(elemento.hasClass('paginacao-anterior')){
               paginaAnterior.prev().addClass('pagina-atual');
            }
            else{
               elemento.addClass('pagina-atual');  
            }
            
            _this.atualizarTabela();
         });

         listagemPadrao.on('change', '.qtd_resultados_pesquisapadrao', function () {
            _this.atualizarTabela();
         });

         if (opcoes.listagemTemporal) {
            listagemPadrao.find('.timeline').scroll(function () {
               var scroll = $(this);
               let tamanhoLinhas = parseInt(listagemPadrao.find('.timeline').find('.linhas').height() - listagemPadrao.find('.timeline').height());
               let scrollNumerico = parseInt(listagemPadrao.find('.timeline').scrollTop());
               if ((tamanhoLinhas - scrollNumerico) > 0 && (tamanhoLinhas - scrollNumerico) <= 10) {
                  scrollNumerico = scrollNumerico + (tamanhoLinhas - scrollNumerico);
               }

               if (scrollNumerico >= tamanhoLinhas) {
                  if (_this.recordsTotal == null) {
                     if (listagemPadrao.find('.linhas').find('listagem-padrao-item').length < _this.objetos.objetos.length) {
                        listagemPadrao.find('.timeline').find('.linhas').fadeOut();

                        _this.renderizarRegistrosTabelaTemporal();
                     }
                  }
                  else if (_this.recordsTotal != null) _this.renderizarRegistrosTabelaTemporal();
               }
            });
         }

         if (opcoes.searching) {
            listagemPadrao.find('.pesquisar').on('click', 'a', function () {
               $('#pesquisar_itens').trigger('change');
            });

            listagemPadrao.find('#pesquisar_itens').on('keyup', function () {
               _this.caixaPesquisa = $(this).val();

               clearTimeout(_this.pesquisaTimeOut);
               _this.pesquisaTimeOut = setTimeout(function () {
                  _this.atualizarTabela();
               }, opcoes.searchDelay);
            });
         }
      };

      _this.getObjetos = function getObjetos() {
         return _this.objetos.objetos[0];
      }

      _this.getObjetosTemporalListagem = function getObjetosTemporalListagem() {
         return _this.objetos.objetos;
      }

      _this.paginaAtual = function paginaAtual() {
         return parseInt(listagemPadrao.find('#listagem_paginacao').find('.pagina-atual').first().attr('data-dt-idx'));
      };

      _this.inicioDaPagina = function inicioDaPagina() {
         let limiteResultadosExibidos = parseInt($('#qtd_resultados').val());
         var inicio = (parseInt(listagemPadrao.find('.pagina-atual').find('a').attr('data-dt-idx')) * limiteResultadosExibidos) - limiteResultadosExibidos;

         return (listagemPadrao.find('.linhas').find('.listagem-padrao-item').length == 0) ? 0 : inicio;
      };

      _this.tamanhoPagina = function tamanhoPagina() {
         let limiteResultadosExibidos = parseInt($('#qtd_resultados').val());
         let somatorioDeTamanho = 0;

         if (listagemPadrao.find('.linhas').find('.listagem-padrao-item').length == 0) {
            return opcoes.length;
         } else {
            for (var i = 1; i <= _this.paginaAtual(); i++) {
               somatorioDeTamanho += limiteResultadosExibidos;

            }
            return somatorioDeTamanho;
         }
      };

      _this.requisitarRegistros = function requisitarRegistros() {
         var parametrosRequisicao = function parametrosRequisicao() {

            if (!opcoes.listagemTemporal) {
               _this.paginacao = {
                  start: _this.inicioDaPagina(),
                  length: parseInt($('#qtd_resultados').val())
               };

               if (_this.caixaPesquisa != '' && _this.caixaPesquisa.length >= 3) _this.paginacao.search = { value: _this.caixaPesquisa };
               return _this.paginacao;

            }
            else {
               return {
                  'listagemTemporal': true,
                  'pageLength': opcoes.pageLength,
                  'homePage': (listagemPadrao.find('.listagem-padrao-item').length == 0) ? 0 : listagemPadrao.find('.listagem-padrao-item').length + 1
               }
            }
         };

         return $.ajax({
            type: "GET",
            url: opcoes.ajax,
            dataType: 'json',
            data: parametrosRequisicao()
         });
      };

      _this.renderizarRows = function renderizarRows(data) {
         if (opcoes.listagemTemporal) {
            for (var indice in data) {
               _this.objetos.objetos.push(data[indice]);
            }

         }
         else {
            while (_this.objetos.objetos.length) {
               _this.objetos.objetos.pop();
            }

            _this.objetos.objetos.push(data);
         }

         var html = '';

         if (opcoes.hasHeader && listagemPadrao.find('.listagem-padrao-item').length == 0) {
            html += '<div class="row agenda-dto tabela_titulo">';
            html += '<div class="col col-lg-12 col-md-12 col-sm-12">';
            html += '<h6 class="center-align">' + opcoes.header + '</h6>';
            html += '</div>';
            html += '</div>';
         }
         if (typeof opcoes.columnDefs == 'function') {
            if (data != undefined && data.length > 0) {
               for (let indice in data) {
                  html += '<div class="row listagem-padrao-item ' + opcoes.classesDesignerTabela + '">';
                  html += opcoes.columnDefs(data[indice]);
                  html += '</div>';
               };

               listagemPadrao.find('.linhas').removeClass('dados_nao_encontrado')
            }
            else {
               html += '<p class="text-center">Nenhum resultado encontrado!</p>';
               if(listagemPadrao.find('.linhas').find('.listagem-padrao-item').length == 0) listagemPadrao.find('.linhas').addClass('dados_nao_encontrado')
            }
         }

         return html;
      };

      _this.renderizarRegistros = function () {
         _this.mostrarTelaDeCarregamento();
         var sucesso = function (resposta) {
            if (resposta.data != undefined) {
               ultimaResposta = resposta;
               listagemPadrao.find('.linhas').empty().append(_this.renderizarRows(resposta.data));
               listagemPadrao.find('.info').empty().append(_this.renderizarInfo(resposta));

               if (typeof opcoes.rowsCallback == 'function') opcoes.rowsCallback(resposta);
            }
            _this.tirarTelaDeCarregamento();
         };

         var erro = function (resposta) {
            _this.tirarTelaDeCarregamento();

            var mensagem = jqXHR.responseText || 'Erro ao carregar dados da tabela.';
            toastr.error(mensagem);
            return false;
         };

         var jqXHR = _this.requisitarRegistros();
         jqXHR.done(sucesso).fail(erro);
      };

      _this.renderizarRegistrosTabelaTemporal = function renderizarRegistrosTabelaTemporal() {
         var sucesso = function (resposta) {
            if (resposta.recordsFiltered > 0) {
               ultimaResposta = resposta;
               listagemPadrao.find('.linhas').append(_this.renderizarRows(resposta.data));

               if (typeof opcoes.rowsCallback == 'function') opcoes.rowsCallback(resposta);
               if (opcoes.listagemTemporal) listagemPadrao.find('.timeline').find('.linhas').fadeIn();
            }
            else if (_this.recordsTotal == null) {
               _this.recordsTotal = resposta.recordsTotal;
               ultimaResposta = resposta;
               listagemPadrao.find('.linhas').append(_this.renderizarRows(resposta.data));

               if (typeof opcoes.rowsCallback == 'function') opcoes.rowsCallback(resposta);
               if (opcoes.listagemTemporal) listagemPadrao.find('.timeline').find('.linhas').fadeIn();

            }

            _this.tirarTelaDeCarregamento();
         };

         var erro = function (resposta) {
            var mensagem = jqXHR.responseText || 'Erro ao exibir listagem.';
            _this.tirarTelaDeCarregamento();

            toastr.error(mensagem);
         };


         var jqXHR = _this.requisitarRegistros();
         jqXHR.done(sucesso).fail(erro);

      };

      _this.renderizarOpcoesDePesquisa = function renderizarOpcoesDePesquisa() {
         var html = '';
         html += '<div class="row">';
            html += '<div class="col col-lg-12 col-md-12 col-12 col-sm-12">';
               html += '<div class="col col-lg-6 col-md-6 col-12 col-sm-12">';
         
                  html += '<div class="col col-lg-4 col-md-4 col-4 col-sm-4 adicao">';
                     html += '<a href="#" class="ico-plus-dto  ' + opcoes.cadastrarLink + '">';
                        html += '<i class="fas fa-plus orange-text text-accent-3"></i>';
                        html += 'Cadastrar'
                     html += '</a>';
                  html += '</div>';

                  html += '<div class="col col-lg-4 col-md-4 col-4 col-sm-4 atualizar">';
                     html += '<a href="#" class="ico-plus-dto ">';
                        html += '<i class="fas fa-sync orange-text text-accent-3"></i>';
                        html += 'Atualizar'
                     html += '</a>';
                  html += '</div>';

                  html += '<div class="col col-lg-4 col-md-4 col-4 col-sm-4 pesquisar">';
                     html += '<a href="#" class="ico-plus-dto ">';
                        html += '<i class="fas fa-search prefix f-12-dto orange-text text-accent-3"></i>';
                        html += 'Pesquisar';
                     html += '</a>';
                  html += '</div>';
               html += '</div>';

               html += '<div class="col col-md-4 col-lg-5 col-sm-10 col-10">';
                  html += '<div class="col col-lg-12 col-md-12 col-12 col-sm-12  input-field">';
                     html += '<input class="validate f-12-dto" type="search" name="pesquisar_itens" placeholder="Pesquisar" id="pesquisar_itens"/>';
                  html += '</div>';
               html += '</div>';


               html += '<div class="col col-md-2 col-lg-1 col-sm-2 col-2">';

                  html += ' <select name="qtd_resultados" id="qtd_resultados" class="qtd_resultados_pesquisapadrao">';

                     for (var indice in opcoes.lengthMenu) {
                        var selected = (indice == 0) ? ' selected="selected" ' : '';
                        html += '<option value="' + opcoes.lengthMenu[indice] + '" ' + selected + ' >' + opcoes.lengthMenu[indice] + '</option>';
                     }

                  html += '</select>';
               html += '</div>';
            html += '</div>';
         html += '</div>';


         listagemPadrao.prepend(html);
      };

      _this.renderizarInfo = function renderizarInfo(data) {
         let inicio = (_this.paginacao.start + 1), tamanhoPagina = inicio + parseInt($('#qtd_resultados').val()) -1;
         if (_this.objetos.length == 0) {
            tamanhoPagina = 0;
            inicio--;
         }

         if (tamanhoPagina > data.recordsTotal) tamanhoPagina = data.recordsTotal;

         if ( tamanhoPagina== null) tamanhoPagina = 0;
         if (data.recordsTotal == null) data.recordsTotal  = 0;
         if ( tamanhoPagina== 0 && data.recordsTotal == 0) inicio = 0;

         var html = '';
         html += '<div class="row">';
         html += '<div class="col col-12 col-sm-12 col-md-6 col-lg-6 informacao_exibicao">';
         html += '<div class="informacoes_listagem" id="informacoes_listagem" role="status" aria-live="polite">Mostrando ' + inicio + ' até ' + tamanhoPagina + ' de ' + data.recordsTotal + ' registros.</div>';
         html += '</div>'

         html += '<div class="col col-12 col-sm-12 col-md-6 col-lg-6 paginacao">';
         html += '<div class="paginacao_listagem" id="listagem_paginacao">';
         html += _this.renderizarBotoes(data);
         html += '</div>';
         html += '</div>';


         return html;
      };

      _this.renderizarTabelaTemporal = function renderizarTabelaTemporal() {
         _this.renderizarRegistrosTabelaTemporal();
      };

      _this.renderizarTabela = function renderizarTabela() {
         _this.renderizarOpcoesDePesquisa();
         _this.renderizarRegistros();
      };

      _this.renderizarBotoes = function renderizarBotoes(data) {
         let inicio = (_this.paginacao.start + 1), tamanhoPagina = inicio + parseInt($('#qtd_resultados').val()) -1;
         let resultadosPorPagina = listagemPadrao.find('#qtd_resultados').val();
         let quantidadeBotoes = Math.ceil(data.recordsTotal / resultadosPorPagina);
         let html = '';
         html += '<ul class="pagination">'
            html += '<li  class="paginacao-anterior disabled paginate_button">';
               html += '<a href="#" data-dt-idx="0" tabindex="0" class="page-link">';
               html += 'Anterior';
               html += '</a>';
            html += '</li>';

            var i;

            var pagina = tamanhoPagina / parseInt($('#qtd_resultados').val());
           
            for (i = 1; i <= quantidadeBotoes; i++) {
               let classes = ' paginate_button';

               if(tamanhoPagina == (parseInt($('#qtd_resultados').val()) * i)){
                  classes += ' pagina-atual';
               }else if(tamanhoPagina < parseInt($('#qtd_resultados').val())){
                  classes += ' pagina-atual';
               }

               let inicioVisualizacao = (pagina  <= 5) ? 1 : pagina -4;

               if( i < inicioVisualizacao || (i > pagina && i > 5)) classes += ' d-none';
            
               html += '<li  class="' + classes + '">'; 
                  html += '<a href="#" data-dt-idx="' + i + '" tabindex="0"  class="page-link">' + i + '</a>';
               html += '</li>';
            }

            if((i-1) > 5) {
               html += '<li  class="paginacao-proximo paginate_button">';

               html += '<a href="#" data-dt-idx="6" tabindex="0"  class="page-link">Próximo</a>';
               html += '</li>'
            } 

            html += '</ul>';
            return html
      };

      _this.atualizarTabela = function atualizarTabela(event) {
         _this.mostrarTelaDeCarregamento();
         if (event != undefined) event.preventDefault();
         var sucesso = function (resposta) {

            ultimaResposta = resposta;
            listagemPadrao.find('.linhas').empty().append(_this.renderizarRows(resposta.data));
            listagemPadrao.find('.info').empty().append(_this.renderizarInfo(resposta));

            if (typeof opcoes.rowsCallback == 'function') opcoes.rowsCallback(resposta);
            _this.tirarTelaDeCarregamento();
         };

         var erro = function (resposta) {
            var mensagem = jqXHR.responseText || 'Erro ao listar tabela.';
            toastr.error(mensagem);
            _this.tirarTelaDeCarregamento();
            return false;
         };
         var jqXHR = _this.requisitarRegistros();
         jqXHR.done(sucesso).fail(erro);
      };

      _this.configurar = function configurar() {
         _this.renderizarTabela();
         _this.definirEventosTabela();
      };

      _this.configurarListagemTemporal = function configurar() {
         _this.renderizarTabelaTemporal();
         _this.definirEventosTabela();
      };
   }; // ListagemPadrao

   // Registrando
   window.listagemPadrao = ListagemPadrao;

   $.fn.extend({
      listar: function (opcoes) {
         var listagemPadrao = new ListagemPadrao($(this[0]), opcoes);
         $(this[0]).data('instanciaTabela', listagemPadrao);
         listagemPadrao.configurar();

         return listagemPadrao;
      },

      listagemTemporal: function (opcoes) {
         var listagemPadrao = new ListagemPadrao($(this[0]), opcoes);
         $(this[0]).data('instanciaTabela', listagemPadrao);
         listagemPadrao.configurarListagemTemporal();

         return listagemPadrao;
      }
   });
})(window, jQuery);