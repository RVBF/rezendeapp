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

			$(evento.target).find('.data').each(function(){
                $(this).pickadate({
					// Strings and translations
					monthsFull: ['Janeiro', 'Fevereiro', 'Março', 'Abril', 'Maio', 'Junho', 'Julho', 'Agosto', 'Setembro', 'Outubro', 'Novembro', 'Dezembro'],
					monthsShort: ['Jan', 'Fev', 'Mar', 'Abr', 'Mai', 'Jun', 'Jul', 'Ago', 'Set', 'Out', 'Nov', 'Dez'],
					weekdaysFull: ['Domingo', 'Segunda', 'Terça', 'Quarta', 'Quinta', 'Sexta', 'Sábado'],
					weekdaysShort: ['Dom', 'Seg', 'Ter', 'Qua', 'Qui', 'Sex', 'Sab'],
					showMonthsShort: undefined,
					showWeekdaysFull: undefined,
					// Buttons
					today: 'Hoje',
					clear: 'Limpar',
					close: 'Fechar',

					// Accessibility labels
					labelMonthNext: 'Próximo Mês',
					labelMonthPrev: 'Mês Anterior',
					labelMonthSelect: 'Selecione o Mês',
					labelYearSelect: 'Selecione o Ano',
					min :  Date.now()
				});
			});

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

			$(evento.target).find('.select2').select2({
				theme: 'bootstrap4',
				width: '100%'
			});

			$(evento.target).find(".categoria_link").on('click', function(event){
				event.preventDefault();
				router.navigate('/categorias');
			});

			$(evento.target).find(".efetuar_logout").on('click', function(event){
				var servico = new app.ServicoLogout();
				var controladoraLogout = new app.ControladoraLogout(servico);
				controladoraLogout.configurar();
			});
	
			$(evento.target).find('.checklist_link').on('click', function(event){
				event.preventDefault();
				router.navigate('/');
			});
	
			$(evento.target).find('.tarefa_link').on('click', function(event){
				event.preventDefault();
				router.navigate('/tarefa');
			});

			$(evento.target).find('.tarefa_link').on('click', function(event){
				event.preventDefault();
				router.navigate('/tarefa');
			});

			$(evento.target).find('.usuario_link').on('click', function(event){
				event.preventDefault();
				router.navigate('/configuracao/usuario');
			});

			$(evento.target).find('.loja_link').on('click', function(event){
				event.preventDefault();
				router.navigate('/loja');
			});

			$(evento.target).find('.grupo_usuario_link').on('click', function(event){
				event.preventDefault();
				router.navigate('/configuracao/grupo-usuario');
			});


			$(evento.target).find('.toltip').each(function(){
			    $(this).tooltip(); 
			});

			$(evento.target).find('.tarefaListagemCompleta_link').on('click', function(event){
				event.preventDefault();
				router.navigate('/tarefa');
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
		$('.toltip').tooltip(); 

		$.validator.addMethod("cRequired", $.validator.methods.required, "Campo obrigatório.");
		$.validator.addClassRules("campo_obrigatorio", {cRequired: true});
		
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
			var datatable = $('body').find('.table').DataTable();

			if(data.status){
				datatable.ajax.reload();
				toastr.success(data.mensagem);
			}
			else{
				toastr.error(data.mensagem);
			}
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

			}
		};
	});
})(window , app, document, jQuery);