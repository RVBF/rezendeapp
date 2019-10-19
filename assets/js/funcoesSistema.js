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
			var instances = M.FormSelect.init($(evento.target).find('.select'), {});

			$(evento.target).find('.select').on('click', function () {

				$(this).formSelect();
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

			$(evento.target).find('.cadastrar_checklist_link').on('click', function(event){
				event.preventDefault();
				router.navigate('/cadastrar-checklist')
			});

			$(evento.target).find('.colaboradores_link').on('click', function(event){
				event.preventDefault();
				router.navigate('/colaboradores');
			});

			$(evento.target).find('.cadastrar_colaborador_link').on('click', function (event) {
				event.preventDefault();
				router.navigate('/cadastrar-colaborador');
			});


			$(evento.target).find('.loja_link').on('click', function (event) {
				event.preventDefault();
				router.navigate('/lojas');
			});

			$(evento.target).find('.cadastrar_loja_link').on('click', function (event) {
				event.preventDefault();
				router.navigate('/cadastrar-loja');
			});

			$(evento.target).find('.editar_loja_link').on('click', function (event) {
				event.preventDefault();
				router.navigate('/editar-loja');
			});

			$(evento.target).find('.setor_link').on('click', function (event) {
				event.preventDefault();
				router.navigate('/setores');
			});
			
			$(evento.target).find('.cadastrar_setor_link').on('click', function(event) {
				event.preventDefault();
				router.navigate('/cadastrar-setor');
			});

			$(evento.target).find('.questionario_link').on('click', function(event){
				event.preventDefault();
				router.navigate('/questionarios')
			});

			$(evento.target).find('.cadastrar_questionario_link').on('click', function(event){
				event.preventDefault();
				router.navigate('/cadastrar-questionario')

			});

			$(evento.target).find('.cadastrar_checklist_link').on('click', function(event){
				event.preventDefault();
				router.navigate('/cadastrar-checklist')
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
		moment.locale('pt-BR');
		console.log(moment().format('LLLL')); // 'Freitag, 24. Juni 2016 01:42'

	

		// $('.toltip').tooltip(); 
		if(window.location.href == 'http://rezendeconstrucao.com.br/rezendeapp/' || window.location.href == 'http://rezendeapp.local/'){ 
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
			if(data.status){
				// if($('body').find('.table').length) datatable.ajax.reload();
				toastr.success(data.mensagem);
			}
			else{
				toastr.error(data.mensagem);
			}
		};
	});
})(window , app, document, jQuery);