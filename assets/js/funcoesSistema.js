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
				$(this).find('.bootstrap-dialog-close-button').addClass('d-none')
			});

			$(evento.target).find('.file_input').each(function(){
				$(this).fileinput({
					theme: "explorer",
					// uploadUrl: "/file-upload-batch/2",
					allowedFileExtensions: ['jpg', 'png', 'gif'],
					overwriteInitial: false,
					initialPreviewAsData: true
				});
			});

			// $(evento.target).find('.data').each(function(){
            //     $(this).pickadate({
			// 		// Strings and translations
			// 		monthsFull: ['Janeiro', 'Fevereiro', 'Março', 'Abril', 'Maio', 'Junho', 'Julho', 'Agosto', 'Setembro', 'Outubro', 'Novembro', 'Dezembro'],
			// 		monthsShort: ['Jan', 'Fev', 'Mar', 'Abr', 'Mai', 'Jun', 'Jul', 'Ago', 'Set', 'Out', 'Nov', 'Dez'],
			// 		weekdaysFull: ['Domingo', 'Segunda', 'Terça', 'Quarta', 'Quinta', 'Sexta', 'Sábado'],
			// 		weekdaysShort: ['Dom', 'Seg', 'Ter', 'Qua', 'Qui', 'Sex', 'Sab'],
			// 		showMonthsShort: undefined,
			// 		showWeekdaysFull: undefined,
			// 		// Buttons
			// 		today: 'Hoje',
			// 		clear: 'Limpar',
			// 		close: 'Fechar',

			// 		// Accessibility labels
			// 		labelMonthNext: 'Próximo Mês',
			// 		labelMonthPrev: 'Mês Anterior',
			// 		labelMonthSelect: 'Selecione o Mês',
			// 		labelYearSelect: 'Selecione o Ano',
			// 		min :  moment()
			// 	});
			// });

			$(evento.target).find('.hora').each(function(){
                $(this).pickatime({
					format: 'HH:i',
					interval: 10,
					clear: 'Limpar',
					formatSubmit: 'HH:i',
					min: [7,0],
					max: [19,0],
					hiddenName: true
				});
			});
			
			$(evento.target).find('.home').on('click', function(event){
				event.preventDefault();
				router.navigate('/');
			});


			$(evento.target).find('.checklist_link').on('click', function(event){
				event.preventDefault();
				router.navigate('/checklist');
			});

			$(evento.target).find('.pa_link').on('click', function(event){
				event.preventDefault();
				router.navigate('/plano-acao');
			});

			$(evento.target).find('.checklist_organizacao_link').on('click', function(event){
				event.preventDefault();
				router.navigate('/checklist-organizacao');
			});

			$(evento.target).find('.inteligencia_link').on('click', function(event){
				event.preventDefault();
				router.navigate('/checklist-organizacao');
			});

			$(evento.target).find('.notificacao_link').on('click', function(event){
				event.preventDefault();
				router.navigate('/notificacao');
			});

			$(evento.target).find('.rd_link').on('click', function(event){
				event.preventDefault();
				router.navigate('/rd');
			});

			$(evento.target).find('.configuracao_link').on('click', function(event){
				event.preventDefault();
				router.navigate('/configuracao');
			});

			$(evento.target).find('.add_checklist_link').on('click', function(event){
				event.preventDefault();
				router.navigate('/add-checklist');
			});
		}
	};
	
	var bodyEvento = {target: 'body'};
	iniciarFuncoesPadroesSistema(bodyEvento);

	$('body').on('DOMNodeInserted',function(evento) {
		iniciarFuncoesPadroesSistema(evento);
	});

	$('body').on('click', '.download', function(evento) {
		evento.preventDefault();

		var elemento = $(this);

		download(elemento.attr('href'), elemento.attr('nomeArquivo'), elemento.attr('tipo'));
	});

	$(document).ready(function() {			
		// $('.toltip').tooltip(); 
		if(window.location.href == 'http://rezendeconstrucao.com.br/rezendeapp/' || window.location.href == 'http://rezendeapp.local/'){ 
			console.log('entrei');
			router.navigate('/');
		}
		
		$.validator.addMethod("cRequired", $.validator.methods.required, "Campo obrigatório.");
		$.validator.addMethod("emailFormat", function(email) {
			var pattern = new RegExp(/^(("[\w-\s]+")|([\w-]+(?:\.[\w-]+)*)|("[\w-\s]+")([\w-]+(?:\.[\w-]+)*))(@((?:[\w-]+\.)*\w[\w-]{0,66})\.([a-z]{2,6}(?:\.[a-z]{2})?)$)|(@\[?((25[0-5]\.|2[0-4][0-9]\.|1[0-9]{2}\.|[0-9]{1,2}\.))((25[0-5]|2[0-4][0-9]|1[0-9]{2}|[0-9]{1,2})\.){2}(25[0-5]|2[0-4][0-9]|1[0-9]{2}|[0-9]{1,2})\]?$)/i);
    		return pattern.test(email);
		}, "Formato inválido para e-mail.");

		$.validator.addClassRules("campo_obrigatorio", {cRequired: true});
		$.validator.addClassRules("email_formato", {emailFormat: true});

		
		setInterval(function() {
			app.verficarLogin();
		}, 1800000);
		
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
			// var datatable = $('body').find('.table').DataTable();

			// if(data.status){
			// 	if($('body').find('.table').length) datatable.ajax.reload();
			// 	toastr.success(data.mensagem);
			// }
			// else{
			// 	toastr.error(data.mensagem);
			// }
		};

		window.sucessoParaFormulario = function sucerepostas_formssoParaFormulario( data, textStatus, jqXHR ) {
			var contexto = $('body').find('#painel_formulario');
			
			window.sucessoPadrao(data, textStatus, jqXHR);

			if(data.status){
				contexto.find('form')[0].reset();
				contexto.desabilitar(true);
				contexto.addClass('desabilitado');
				contexto.addClass('d-none');
			}
			else{
				contexto.desabilitar(false);
				contexto.find('form').find('.msg').empty();
				contexto.find('form').find('.msg').append(data.mensagem);
				contexto.find('form').find('.msg').parents('.row').removeClass('d-none');
			}
		};
	});
})(window , app, document, jQuery);