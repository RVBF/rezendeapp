(function (window, app, document, $) {
   'use strict';


   function iniciarFuncoesPadroesSistema(event) {
      var evento = event;
      if (typeof (evento) != 'undefined') {
         $(evento.target).find('.desabilitado').each(function (i) {
            $(this).desabilitar(true)
         });

         $(evento.target).find('.bootstrap-dialog-header').each(function (i) {
            $(this).find('.bootstrap-dialog-close-button').addClass('d-none')
         });

         $(evento.target).find('.file_input').each(function () {
            $(this).fileinput({
               theme: "explorer",
               // uploadUrl: "/file-upload-batch/2",
               allowedFileExtensions: ['jpg', '"png"', 'gif'],
               overwriteInitial: false,
               initialPreviewAsData: true
            });
         });
         var instances = M.FormSelect.init($(evento.target).find('.select'), {});

         $(evento.target).find('.select').on('click', function () {
            $(this).formSelect();
         });

         $(event.target).on('click', '.alterarsenha_link', function (event) {
            event.preventDefault();
            router.navigate('/alterar-senha');
         });

         $(evento.target).on('click', '.efetuar_logout', function (event) {
            event.preventDefault();
            mostrarTelaDeCarregamento();

            var servicoLogout = new app.ServicoLogout();
            var elemento = $(this);

            var sucesso = function sucesso(data, textStatus, jqXHR) {
               tirarTelaDeCarregamento();
               window.sucessoPadrao(data, textStatus, jqXHR);

               if (data.status) {
                  window.sessionStorage.clear();
                  router.navigate('/login');
               }
            };
            var jqXHR = servicoLogout.sair();

            jqXHR.done(sucesso).fail(erro);

         });

         $(evento.target).on('click', '.home', function (event) {
            event.preventDefault();
            router.navigate('/');
         });

         $(evento.target).on('click', '.checklist_link', function (event) {
            event.preventDefault();
            router.navigate('/checklist');
         });

         $(evento.target).on('click', '.pa_link', function (event) {
            event.preventDefault();
            router.navigate('/plano-acao');
         });

         $(evento.target).on('click', '.inteligencia_link', function (event) {
            event.preventDefault();
            router.navigate('/inteligencia');
         });

         $(evento.target).on('click', '.notificacao_link', function (event) {
            event.preventDefault();
            router.navigate('/notificacao');
         });

         $(evento.target).on('click', '.rd_link', function (event) {
            event.preventDefault();
            router.navigate('/rd');
         });

         $(evento.target).on('click', '.configuracao_link', function (event) {
            event.preventDefault();
            router.navigate('/configuracao');
         });

         $(evento.target).on('click', '.configurar_acessos_link', function (event) {
            event.preventDefault();
            router.navigate('/configurar-acessos/' + $(this).attr('parametros'));
         });

         $(evento.target).on('click', '.cadastrar_checklist_link', function (event) {
            event.preventDefault();
            router.navigate('/cadastrar-checklist')
         });

         $(evento.target).on('click', '.colaboradores_link', function (event) {
            event.preventDefault();
            router.navigate('/colaboradores');
         });

         $(evento.target).on('click', '.cadastrar_colaborador_link', function (event) {
            event.preventDefault();
            router.navigate('/cadastrar-colaborador');
         });


         $(evento.target).on('click', '.loja_link', function (event) {
            event.preventDefault();
            router.navigate('/lojas');
         });

         $(evento.target).on('click', '.cadastrar_loja_link', function (event) {
            event.preventDefault();
            router.navigate('/cadastrar-loja');
         });

         $(evento.target).on('click', '.editar_loja_link', function (event) {
            event.preventDefault();
            router.navigate('/editar-loja');
         });

         $(evento.target).on('click', '.setor_link', function (event) {
            event.preventDefault();
            router.navigate('/setores');
         });

         $(evento.target).on('click', '.cadastrar_setor_link', function (event) {
            event.preventDefault();
            router.navigate('/cadastrar-setor');
         });

         $(evento.target).on('click', '.questionario_link', function (event) {
            event.preventDefault();
            router.navigate('/questionarios');
         });

         $(evento.target).on('click', '.cadastrar_questionario_link', function (event) {
            event.preventDefault();
            router.navigate('/cadastrar-questionario');

         });

         $(evento.target).on('click', '.cadastrar_checklist_link', function (event) {
            event.preventDefault();
            router.navigate('/cadastrar-checklist');
         });

         $(evento.target).on('click', '.cadastrar_planoacao_link', function (event) {
            event.preventDefault();
            router.navigate('/cadastrar-pa');
         });

         $(evento.target).on('click', '.pendencia_link', function (event) {
            event.preventDefault();
            router.navigate('/pendencia');
         });

         $(evento.target).on('click', '.cadastrar_pendencia_link', function (event) {
            event.preventDefault();
            router.navigate('/cadastrar-pendencia');
         });

         $(evento.target).on('click', '.grupousuario_link', function (event) {
            event.preventDefault();
            router.navigate('/grupos-de-usuario');
         });

         $(evento.target).on('click', '.cadastrar_grupodeusuario_link', function (event) {
            event.preventDefault();
            router.navigate('/cadastrar-grupo-de-usuario');
         });

         $(evento.target).on('click', '.dashboard_link', function (event) {
            event.preventDefault();
            router.navigate('/dashboard');
         });
      }
   };

   var bodyEvento = { target: 'body' };
   iniciarFuncoesPadroesSistema(bodyEvento);

   $('body').on('DOMNodeInserted', function (evento) {
      iniciarFuncoesPadroesSistema(evento);
   });

   $('body').on('click', '.download', function (evento) {
      evento.preventDefault();

      var elemento = $(this);

      download(elemento.attr('href'), elemento.attr('nomeArquivo'), elemento.attr('tipo'));
   });

   $(document).ready(function () {
      $('.sidenav').sidenav();     
      let url = window.location.href.replace(/^.*\//g, '');

      if (url == '#' || url == '') {
         router.navigate('/');
      }
      navigator.permissions.query({name:'microphone'}).then(function(result) {
         if (result.state == 'granted') {
       
         } else if (result.state == 'prompt') {
       
         } else if (result.state == 'denied') {
       
         }
         result.onchange = function() {
       
         };
		});

      moment.locale('pt-BR');

      $(window).scroll(function () {

         //Display or hide scroll to top button
         if ($(this).scrollTop() > 100) {
            $('.scrollup').fadeIn();
         } else {
            $('.scrollup').fadeOut();
         }
      });

      $('.scrollup').click(function () {
         $("html, body").animate({
            scrollTop: 0
         }, 600);
         return false;
      });

		$('#nav-mobile').on('click', 'a', function () {
			$('body .sidenav-overlay').trigger('click');
		});

      $('.tooltip').tooltip();
      // if (window.location.href == 'http://rezendeconstrucao.com.br/rezendeapp/' || window.location.href == 'http://rezendeapp.local/') {
      //    router.navigate('/');
      // }

      $.validator.addMethod("cRequired", $.validator.methods.required, "Campo obrigatório.");
      $.validator.addMethod("formatoAudio", $.validator.methods.fileType, "Formato de áudio inválido! Formatos permitidos: mp3|wma|aac|ogg|ac3|wav|mp4|m4a!");
      $.validator.addMethod("formatoImagem", $.validator.methods.fileType, "Formato de imagem inválido! Formatos permitidos: bmp|gif|jpeg|png!");
      $.validator.addMethod('filesize', $.validator.methods.maxFileSize, 'Tamanho de arquivo invalido! O arquivo deve ser menor que  100KB e maior que 25MB"!');

      $.validator.addMethod("emailFormat", function (email) {
         var pattern = new RegExp(/^(("[\w-\s]+")|([\w-]+(?:\.[\w-]+)*)|("[\w-\s]+")([\w-]+(?:\.[\w-]+)*))(@((?:[\w-]+\.)*\w[\w-]{0,66})\.([a-z]{2,6}(?:\.[a-z]{2})?)$)|(@\[?((25[0-5]\.|2[0-4][0-9]\.|1[0-9]{2}\.|[0-9]{1,2}\.))((25[0-5]|2[0-4][0-9]|1[0-9]{2}|[0-9]{1,2})\.){2}(25[0-5]|2[0-4][0-9]|1[0-9]{2}|[0-9]{1,2})\]?$)/i);
         return pattern.test(email);
      }, "Formato de e-mail inválido!");

      $.validator.addClassRules("campo_obrigatorio", { cRequired: true });
      $.validator.addClassRules("email_formato", { emailFormat: true });
      $.validator.addClassRules("arquivos_audio", { formatoAudio: { types: ["mp3", "wma", "aac", "ogg", "ac3", "wav", "mp4", "m4a"] } });
      $.validator.addClassRules("arquivos_imagem", { formatoImagem: { types: ["bmp", "gif", "jpeg", "png"] } });
      $.validator.addClassRules("tamanhoArquivosPadrao", { filesize: { size: 25, unit: 'MB' } });


      setInterval(function () {
         app.verficarLogin();
      }, 1800000);

      window.validarSeONavegadorSuporta = function validarSeONavegadorSuporta() {
         // Verificando se o navegador tem suporte aos recursos para redimensionamento
         if (!window.File || !window.FileReader || !window.FileList || !window.Blob) {
            alert('O navegador não suporta os recursos utilizados pelo aplicativo');
            return;
         }
      };

      window.retornarInteiroEmStrings = function retornarInteiroEmStrings(string) {
         var numero = string.replace(/[^0-9]/g, '');
         return parseInt(numero);
      };

      window.definirMascarasPadroes = function definirMascarasPadroes() {
         var mascara = new Inputmask("decimal", { radixPoint: ".", digits: 2, autoGroup: true, groupSeparator: ",", groupSize: 3, rightAlign: false, negative: false });
         mascara.mask($('.decimal'));

         $(".preco").maskMoney({
            symbol: 'R$ ',
            showSymbol: true,
            thousands: '.',
            decimal: ',',
            symbolStay: true
         });
      };

      window.erro = function erro(jqXHR, textStatus, errorThrown) {
         toastr.error(jqXHR.responseText);
         tirarTelaDeCarregamento();

      };

      window.sucessoPadrao = function sucessoPadrao(data, textStatus, jqXHR) {
         if (data.status) {
            // if($('body').find('.table').length) datatable.ajax.reload();
            toastr.success(data.mensagem);
         }
         else {
            toastr.error(data.mensagem);
         }
      };

      window.agruparObjetos = function agruparObjetos(listaDeObjetos, atributo) {
         var objetosAgrupados = {};

         $.each(listaDeObjetos, function (indice, objeto) {
            var subLista = objetosAgrupados[objeto[atributo]];

            if (subLista) {
               subLista.push(objeto);
            } else {
               objetosAgrupados[objeto[atributo]] = [objeto];
            }
         });

         return objetosAgrupados;
      }
   });
})(window, app, document, jQuery);