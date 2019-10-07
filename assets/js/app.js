var app = {
  isLoading: true,
  api : '/api'
};

(function(app, document, $, toastr,  window) {
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
		highlight: function(element)
		{
			$(element).closest('.row').addClass('has-error');
		},
		unhighlight: function(element)
		{
			$(element).closest('.row').removeClass('has-error');
		},
		errorElement: 'span',
		errorClass: 'help-block',
		errorPlacement: function (error, element)
		{
			var possivelSelect2 = element.parent('div');
			var possivelInputaAddon = element.parent('div .input-group').nextAll('div .menu_input_addon_erro:first');
			if(possivelSelect2.length)
			{
				element = possivelSelect2;
			}
			else
			{
				if(possivelInputaAddon.length)
				{
					element = possivelInputaAddon;
				}
			}

			element.append(error);
		}
	});

	$.fn.extend({
		desabilitar: function (status) {
			$(this).find("*").each(function(){
				$(this).prop('disabled', status);
			});
		}
	});
})(app, document, jQuery, toastr,  window);
  