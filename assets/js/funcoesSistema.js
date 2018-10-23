(function(window ,app, document, $) {
	'use strict';
	$(document).ready(function()
	{
		window.validarSeONavegadorSuporta  = function validarSeONavegadorSuporta()
		{
			// Verificando se o navegador tem suporte aos recursos para redimensionamento
			if (!window.File || !window.FileReader || !window.FileList || !window.Blob) {
				alert('O navegador não suporta os recursos utilizados pelo aplicativo');
				return;
			}
		};

		window.converterEmFloat = function converterEmFloat(moeda)
		{
			moeda = moeda.replace(".","");

			moeda = moeda.replace(",",".");

			return parseFloat(moeda);
		};

		window.converterEmMoeda = function converterEmMoeda(numero, casasDecimais)
		{
			if(casasDecimais == undefined)
			{
				var casasDecimais = 2;
			}

			var number = parseFloat(numero).toFixed(casasDecimais);

			numero += '';

			numero = number.replace(".", ",");
			return numero;
		};

		window.desabilitarFormulario = function desabilitarFormulario(status = true)
		{
			$('form input,select,textarea,checkbox').each(function(){
				$(this).prop('disabled', status);
			});
		};

		window.desabilitarBotoesDeFormulario = function desabilitarBotoesDeFormulario(status = true)
		{
			$('button').each(function(){
				$(this).prop('disabled', status);
			});
        };
        
		window.retornarInteiroEmStrings = function retornarInteiroEmStrings(string)
		{
			var numero = string.replace(/[^0-9]/g,'');
			return parseInt(numero);
		};

		window.definirMascarasPadroes = function definirMascarasPadroes()
		{
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

		$(".categoria_link").on('click', function(event){
			event.preventDefault();
			router.navigate('/categorias');
		});
	});
})(window , app, document, jQuery);

