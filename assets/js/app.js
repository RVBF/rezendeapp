var app = {
  isLoading: true,
  api : '/api'
};

(function(app, document, $, toastr, BootstrapDialog, window) {
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

	// Opções para diálogos
	BootstrapDialog.DEFAULT_TEXTS[ BootstrapDialog.TYPE_DEFAULT ] = 'Informação';
	BootstrapDialog.DEFAULT_TEXTS[ BootstrapDialog.TYPE_INFO ] = 'Informação';
	BootstrapDialog.DEFAULT_TEXTS[ BootstrapDialog.TYPE_PRIMARY ] = 'Informação';
	BootstrapDialog.DEFAULT_TEXTS[ BootstrapDialog.TYPE_SUCCESS ] = 'Sucesso';
	BootstrapDialog.DEFAULT_TEXTS[ BootstrapDialog.TYPE_WARNING ] = 'Aviso';
	BootstrapDialog.DEFAULT_TEXTS[ BootstrapDialog.TYPE_DANGER ] = 'Erro';
	BootstrapDialog.DEFAULT_TEXTS[ 'OK' ] = 'OK';
	BootstrapDialog.DEFAULT_TEXTS[ 'CANCEL' ] = 'Cancelar';
	BootstrapDialog.DEFAULT_TEXTS[ 'CONFIRM' ] = 'Confirmação';

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
			var possivelSelect2 = element.nextAll('span .select2:first');
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

			element.after(error);
		}
	});

	// Opções padrão para o DataTables ----------------------------------------
	app.dtOptions = {
		"responsive": true,
		"autoWidth": false,
		"processing":true,
		"serverSide":true,
		"destroy": true,
		"select": true,
		"ajax" :{
			 "type": "POST"
		},
		"pageLength": 10,
		"lengthMenu":[ [10, 25, 50, 100], [10, 25, 50,100] ],
		"paging":true,
		"searching":false,
		"ordering":false,
		"language"	: {
			"sEmptyTable": "Nenhum registro encontrado",
			"sInfo": "Mostrando de _START_ até _END_ de _TOTAL_ registros",
			"sInfoEmpty": "Mostrando 0 até 0 de 0 registros",
			"sInfoFiltered": "(Filtrados de _MAX_ registros)",
			"sInfoPostFix": "",
			"sInfoThousands": ".",
			"sLengthMenu": "_MENU_ resultados por página",
			"sLoadingRecords": "Carregando...",
			"sProcessing": "Processando...",
			"sZeroRecords": "Nenhum registro encontrado",
			"sSearch": "Pesquisar",
			"oPaginate": {
				"sNext": "Próximo",
				"sPrevious": "Anterior",
				"sFirst": "Primeiro",
				"sLast": "Último"
			},
			"oAria": {
				"sSortAscending": ": Ordenar colunas de forma ascendente",
				"sSortDescending": ": Ordenar colunas de forma descendente"
			},
			"select": {
				"rows": {
					"_": "Selecionado %d linhas",
					"0": "Nenhuma linha selecionada",
					"1": "Selecionado 1 linha"
				}
			}
		},
		"bFilter" : true,
		"searching" : true,
		"order": [[0, 'desc']]
	};

	$.fn.extend({
		desabilitar: function (status) {
			$(this).find("*").each(function(){
				$(this).prop('disabled', status);
			});
		}
	});


	function iniciarFuncoesPadroesSistema(event)
	{
		var evento = event;
		if(typeof(evento) != 'undefined')
		{
			$(evento.target).find('.desabilitado').each(function(i) {
				$(this).desabilitar(true)
			});
		}
	}

	var bodyEvento = {target: 'body'};
	iniciarFuncoesPadroesSistema(bodyEvento);

	$('body').on('DOMNodeInserted', '#app',function(evento)
	{
		iniciarFuncoesPadroesSistema(evento);
	});

	$(document).ready(function()
	{
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

		window.sucessoParaFormulario = function sucessoParaFormulario( data, textStatus, jqXHR ) {
			var datatable = $('body').find('.dataTable').DataTable();
			var contexto = $('body').find('#painel_formulario');
			datatable.row.add({
				'id' : data.categoria.id,
				'titulo' : data.categoria.titulo
			}).draw();

			contexto.find('form')[0].reset();
			contexto.desabilitar(true);
			contexto.addClass('desabilitado');
			contexto.addClass('d-none');

			toastr.success(data.mensagem);
		};

		$(".categoria_link").on('click', function(event){
			event.preventDefault();
			router.navigate('/categorias');
		});
	});
})(app, document, jQuery, toastr, BootstrapDialog, window);
  