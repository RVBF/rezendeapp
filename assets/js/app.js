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
	BootstrapDialog.DEFAULT_TEXTS[BootstrapDialog.TYPE_DEFAULT] = 'Informação';
	BootstrapDialog.DEFAULT_TEXTS[BootstrapDialog.TYPE_INFO] = 'Informação';
	BootstrapDialog.DEFAULT_TEXTS[BootstrapDialog.TYPE_PRIMARY] = 'Informação';
	BootstrapDialog.DEFAULT_TEXTS[BootstrapDialog.TYPE_SUCCESS] = 'Sucesso';
	BootstrapDialog.DEFAULT_TEXTS[BootstrapDialog.TYPE_WARNING] = 'Aviso';
	BootstrapDialog.DEFAULT_TEXTS[BootstrapDialog.TYPE_DANGER] = 'Erro';
	BootstrapDialog.DEFAULT_TEXTS['OK'] = 'OK';
	BootstrapDialog.DEFAULT_TEXTS['CANCEL'] = 'Cancelar';
	BootstrapDialog.DEFAULT_TEXTS['CONFIRM'] = 'Confirmação';

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
		"autoWidth": true,
		"processing":true,
		"serverSide":true,
		"destroy": true,
		"select": 'single',
		"ajax" :{
			 "type": "POST"
		},
		"pageLength": 10,
		"lengthMenu":[[10, 25, 50, 100], [10, 25, 50,100] ],
		"paging":true,
		"searching":false,
		"ordering":true,
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
})(app, document, jQuery, toastr, BootstrapDialog, window);
  