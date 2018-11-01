(function(window ,app, document, $) {
	'use strict';
	function iniciarFuncoesPadroesSistema(event)
	{
		var evento = event;
		if(typeof(evento) != 'undefined')
		{
			$(evento.target).find('.desabilitado').each(function(i) {
				$(this).desabilitar(true)
			});

			$(evento.target).find('.bootstrap-dialog-header').each(function(i) {
				console.log($(this));
				$(this).find('.bootstrap-dialog-close-button').addClass('d-none')
			});

			
		}
	}

	var bodyEvento = {target: 'body'};
	iniciarFuncoesPadroesSistema(bodyEvento);

	$('body').on('DOMNodeInserted',function(evento)
	{
		iniciarFuncoesPadroesSistema(evento);
	});

	$(document).ready(function()
	{
		window.router.navigate('/');

		window.validarSeONavegadorSuporta  = function validarSeONavegadorSuporta() {
			// Verificando se o navegador tem suporte aos recursos para redimensionamento
			if (!window.File || !window.FileReader || !window.FileList || !window.Blob) {
				alert('O navegador não suporta os recursos utilizados pelo aplicativo');
				return;
			}
		};

		window.retornarInteiroEmStrings = function retornarInteiroEmStrings(string) {
			var numero = string.replace(/[^0-9]/g,'');
			return parseInt(numero);
		};

		window.definirMascarasPadroes = function definirMascarasPadroes() {
			var mascara = new Inputmask("decimal", { radixPoint: ".", digits:2, autoGroup: true, groupSeparator: ",", groupSize: 3, rightAlign: false, negative: false });
			mascara.mask($('.decimal'));

			var optionSelct2 = {
				width: 'copy',
				theme: 'bootstrap',
				minimumResultsForSearch: 1
			};

			$(".select2").select2(optionSelct2);

			var optionsDatePicker = {
				format: "dd/mm/yyyy",
				language: 'pt-BR',
				startView: 0,
				startDate: "today",
				autoclose: true,
				todayHighlight: true,
				todayBtn: true
			};

			$('.datepicker').datepicker(optionsDatePicker);

			$('.datepicker').mask('99/99/9999');

			$(".preco").maskMoney({
				symbol:'R$ ',
				showSymbol:true,
				thousands:'.',
				decimal:',',
				symbolStay: true
			});
		};

		window.erro = function erro( jqXHR, textStatus, errorThrown ) {
			toastr.error(jqXHR.responseText);
		};

		window.sucessoPadrao = function sucessoPadrao(data, textStatus, jqXHR){
			if(data.status){
				toastr.success(data.mensagem);
			}
			else{
				toastr.error(data.mensagem);
			}
		};

		window.sucessoParaFormulario = function sucessoParaFormulario( data, textStatus, jqXHR ) {
			var datatable = $('body').find('.dataTable').DataTable();
			var contexto = $('body').find('#painel_formulario');
			
			window.sucessoPadrao( data, textStatus, jqXHR );

			if(data.status){
				datatable.ajax.reload();
				contexto.find('form')[0].reset();
				contexto.desabilitar(true);
				contexto.addClass('desabilitado');
				contexto.addClass('d-none');
			}
			else{
				contexto.desabilitar(false);

				console.log(contexto.find('form').find('.msg'));
				contexto.find('form').find('.msg').empty();
				contexto.find('form').find('.msg').append(data.mensagem);

			}
		};

		$(".categoria_link").on('click', function(event){
			event.preventDefault();
			router.navigate('/categorias');
		});

		$('.checklist_link').on('click', function(){
			event.preventDefault();
			router.navigate('/');
		});
	});
})(window , app, document, jQuery);