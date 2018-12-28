(function(window ,app, document, $) {
	'use strict';
	function iniciarFuncoesPadroesSistema(event)
	{
		var evento = event;
		
		// window.exibirMenu();

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
					min :  moment()
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

			$(evento.target).find('.toltip').each(function(){
			    $(this).tooltip(); 
			});

			$(evento.target).find(".efetuar_logout").on('click', function(event){
				var servico = new app.ServicoLogout();
				var controladoraLogout = new app.ControladoraLogout(servico);
				controladoraLogout.sair(event);
			});

			$(evento.target).find(".categoria_link").on('click', function(event){
				event.preventDefault();
				router.navigate('/categorias');
			});
	
			$(evento.target).find('.setor_link').on('click', function(event){
				event.preventDefault();
				router.navigate('/setor');
			});

			$(evento.target).find('.usuario_link').on('click', function(event){
				event.preventDefault();
				router.navigate('/configuracao/usuario');
			});

			$(evento.target).find('.redireciona_mobile').on('click', function(event){
				$('.meanclose').click();
			});

			$(evento.target).find('.loja_link').on('click', function(event){
				event.preventDefault();
				router.navigate('/loja');
			});

			$(evento.target).find('.grupo_usuario_link').on('click', function(event){
				event.preventDefault();
				router.navigate('/configuracao/grupo-usuario');
			});

			$(evento.target).find('.tarefaListagemCompleta_link').on('click', function(event){
				event.preventDefault();
				router.navigate('/tarefa');
			});

			$(evento.target).find('.permissoes_link').on('click', function(event) {
				event.preventDefault();
				router.navigate('/configurar-permissoes');
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

	$('body').on('click', '.nav-link', function () {
		var id = $(this).attr('href');

		if($(id).length == 0)
		{
			$('#opcoes_drop_nav').find('div.tab-pane').each(function (i, item) {
				$(item).removeClass('active');
				$(item).removeClass('show');
			});
		}
	});

	window.meanBar  = function meanBar() {
		$(".chosen")[0] && $(".chosen").chosen({
            width: "100%",
            allow_single_deselect: !0
        });
		/*--------------------------
		 auto-size Active Class
		---------------------------- */	
		$(".auto-size")[0] && autosize($(".auto-size"));
		/*--------------------------
		 Collapse Accordion Active Class
		---------------------------- */	
		$(".collapse")[0] && ($(".collapse").on("show.bs.collapse", function(e) {
            $(this).closest(".panel").find(".panel-heading").addClass("active")
        }), $(".collapse").on("hide.bs.collapse", function(e) {
            $(this).closest(".panel").find(".panel-heading").removeClass("active")
        }), $(".collapse.in").each(function() {
            $(this).closest(".panel").find(".panel-heading").addClass("active")
        }));
		/*----------------------------
		 jQuery tooltip
		------------------------------ */
		$('[data-toggle="tooltip"]').tooltip();
		/*--------------------------
		 popover
		---------------------------- */	
		$('[data-toggle="popover"]')[0] && $('[data-toggle="popover"]').popover();
		/*--------------------------
		 File Download
		---------------------------- */	
		$('.btn.dw-al-ft').on('click', function(e) {
			e.preventDefault();
		});
		/*--------------------------
		 Sidebar Left
		---------------------------- */	
		$('#sidebarCollapse').on('click', function () {
			 $('#sidebar').toggleClass('active');
			 
		 });
		$('#sidebarCollapse').on('click', function () {
			$("body").toggleClass("mini-navbar");
			SmoothlyMenu();
		});
		$('.menu-switcher-pro').on('click', function () {
			var button = $(this).find('i.nk-indicator');
			button.toggleClass('notika-menu-befores').toggleClass('notika-menu-after');
			
		});
		$('.menu-switcher-pro.fullscreenbtn').on('click', function () {
			var button = $(this).find('i.nk-indicator');
			button.toggleClass('notika-back').toggleClass('notika-next-pro');
		});
		/*--------------------------
		 Button BTN Left
		---------------------------- */	
		
		$(".nk-int-st")[0] && ($("body").on("focus", ".nk-int-st .form-control", function() {
            $(this).closest(".nk-int-st").addClass("nk-toggled")
        }), $("body").on("blur", ".form-control", function() {
            var p = $(this).closest(".form-group, .input-group"),
                i = p.find(".form-control").val();
            p.hasClass("fg-float") ? 0 == i.length && $(this).closest(".nk-int-st").removeClass("nk-toggled") : $(this).closest(".nk-int-st").removeClass("nk-toggled")
        })), $(".fg-float")[0] && $(".fg-float .form-control").each(function() {
            var i = $(this).val();
            0 == !i.length && $(this).closest(".nk-int-st").addClass("nk-toggled")
		});

		/*----------------------------
		jQuery MeanMenu
		------------------------------ */
		jQuery('nav#dropdown').meanmenu();
	}

	$(document).ready(function()
	{			
		$('.toltip').tooltip(); 
		
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
				contexto.find('form').find('.msg').parents('.row').removeClass('d-none');
			}
		};
	});
})(window , app, document, jQuery);