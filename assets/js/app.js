var app = {
   isLoading: true,
   api: '/api',
   acessoNegado: false
};

(function (app, document, $, toastr, window, BootstrapDialog) {
   'use strict';
   // Opções para mensagens
   toastr.options.closeButton = false;
   toastr.options.debug = false;
   toastr.options.newestOnTop = true;
   toastr.options.progressBar = false;
   toastr.options.positionClass = "toast-top-right";
   toastr.options.preventDuplicates = false;
   toastr.options.onclick = null;
   toastr.options.showDuration = "300";
   toastr.options.hideDuration = "1000";
   toastr.options.timeOut = "2000";
   toastr.options.extendedTimeOut = "1000";
   toastr.options.showEasing = "swing";
   toastr.options.hideEasing = "linear";
   toastr.options.showMethod = "fadeIn";
   toastr.options.hideMethod = "fadeOut";

   var nua = navigator.userAgent
   var isAndroid = (nua.indexOf('Mozilla/5.0') > -1 && nua.indexOf('Android ') > -1 && nua.indexOf('AppleWebKit') > -1 && nua.indexOf('Chrome') === -1)

   if (isAndroid) {
      $('select.form-control').removeClass('form-control').css('width', '100%')
   }

   $.validator.setDefaults({
      ignore: [],
      highlight: function (element) {
         $(element).closest('.row').addClass('has-error');
      },
      unhighlight: function (element) {
         $(element).closest('.row').removeClass('has-error');
      },
      errorElement: 'span',
      errorClass: 'help-block',
      errorPlacement: function (error, element) {
         // var possivelSelect2 = element.parent('div');
         // var possivelInputaAddon = element.parent('div .input-group').nextAll('div .menu_input_addon_erro:first');
         // if(possivelSelect2.length)
         // {
         // 	element = possivelSelect2;
         // }
         // else
         // {
         // 	if(possivelInputaAddon.length)
         // 	{
         // 		element = possivelInputaAddon;
         // 	}
         // }

         element.parents('body').find('.msg').empty().append(error).append('<br>').removeClass('d-none').desabilitar(false);
      }
   });

   // Opções para diálogos
   BootstrapDialog.DEFAULT_TEXTS[BootstrapDialog.TYPE_DEFAULT] = 'Informação';
   BootstrapDialog.DEFAULT_TEXTS[BootstrapDialog.TYPE_INFO] = 'Informação';
   BootstrapDialog.DEFAULT_TEXTS[BootstrapDialog.TYPE_PRIMARY] = 'Informação';
   BootstrapDialog.DEFAULT_TEXTS[BootstrapDialog.TYPE_SUCCESS] = 'Sucesso';
   BootstrapDialog.DEFAULT_TEXTS[BootstrapDialog.TYPE_WARNING] = 'Aviso';
   BootstrapDialog.DEFAULT_TEXTS[BootstrapDialog.TYPE_DANGER] = 'Erro';
   BootstrapDialog.DEFAULT_TEXTS['OK'] = 'OK';
   BootstrapDialog.DEFAULT_TEXTS['CANCEL'] = 'Cancelar';
   BootstrapDialog.DEFAULT_TEXTS['CONFIRM'] = 'Confirmação';


   $.fn.extend({
      desabilitar: function (status, sucesso = null) {
         $(this).find("*").each(function () {
            $(this).prop('disabled', status);
         }).promise().done(function () {
            if (typeof sucesso == 'function') sucesso();
         });
      }
   });

   var loader = $('#mainLoader');
   let tempoDeAnimacao = 250;

   window.mostrarTelaDeCarregamento = function mostrarTelaDeCarregamento() {
      loader.find('.loader').removeClass('d-none');

      $('body').css('overflow', 'hidden');

      loader.find('.loaderPageLeft').animate({ 'width': '60%' }, tempoDeAnimacao);
      loader.find('.loaderPageRight').animate({ 'width': '60%' }, tempoDeAnimacao);
   }

   window.tirarTelaDeCarregamento = function tirarTelaDeCarregamento() {
      loader.find('.loader').addClass('d-none');
      $('body').css('overflow', 'auto');

      loader.find('.loaderPageLeft').animate({ 'width': '0px' }, tempoDeAnimacao);
      loader.find('.loaderPageRight').animate({ 'width': '0px' }, tempoDeAnimacao);
   }

   $(document).ajaxComplete(function (evento, requisicao) {
      var resposta = requisicao.responseJSON;

      if (resposta != undefined && resposta.acessoNegado == true) {
         app.acessoNegado = true;

         if (resposta.metodo == 'get') {
            app.acessoNegado = false;
            window.history.back();
         }

         toastr.error('Acesso negado', undefined, { timeOut: 50000 });
      }
   });
})(app, document, jQuery, toastr, window, BootstrapDialog);
