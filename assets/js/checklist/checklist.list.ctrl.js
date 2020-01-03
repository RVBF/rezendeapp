/**
 *  checklist.list.ctrl.js
 *
 *  @author	Rafael Vinicius Barros Ferreira
 */
(function(window, app, $, toastr)
{
	'use strict';

	function ControladoraListagemChecklist(servicoChecklist)
	{
		var _this = this;
		var _cont = 0;
		var _tabela = null;
		_this.botaoCadastrar = $('#cadastrar');
		_this.botaoEditar = $('#editar');
		_this.botaoRemover = $('#remover');
		_this.botaoAtualizar = $('#atualizar');
		_this.idTabela = $('#checklist');

		//Configura a tabela
		_this.opcoesDaTabela = function opcoesDaTabela() {
			var objeto =  new Object();
			objeto.ajax = servicoChecklist.rota();

			objeto.carregando = true;
			objeto.pageLength = 10;
			objeto.lengthMenu =  [10, 30, 40, 100];
			objeto.ordering= true;
			objeto.searching = true;
			objeto.searchDelay = 600;	
			objeto.cadastrarLink = 'cadastrar_checklist_link';
			objeto.columnDefs = function (data){
				var estaRespondido = function estaRespondido() {
					var i = 0;
					for (var indice in data.questionamentos) {
						var elemento = data.questionamentos[indice];
						if(elemento.status == 'Respondido' || elemento.status == 'Respondido Com Pendências')i++;
					}

					return ( i == data.questionamentos.length ) ? true : false;
				};
				
				var temPaPendente = function temPaPendente() {
					for (var indice in data.questionamentos) {
						var elemento = data.questionamentos[indice];
					
						if(elemento.planoAcao != null) return true;
					}
					return false;
				};


				var temPendenciaPendente = function temPendenciaPendente() {
					for (var indice in data.questionamentos) {
						var elemento = data.questionamentos[indice];
						if(elemento.pendencia != null) return true;
					}
					return false;
				};
				
				var html = '';

				var tipoClasse = !(data.status == 'Executado') ? ' agenda-dto ' : ' agenda-dto cinza ';
				var dataLimite = moment(data.dataLimite);
				var diferencaDias = dataLimite.diff(moment(),'days');
				var textoDiasRestantes = (diferencaDias > 0) ? Math.abs(diferencaDias) +' dias para que o cheklist seja executado!' : Math.abs(diferencaDias) +' dias de atraso!';
				if(data.status == 'Executado'){
					textoDiasRestantes = 'Encerrado!';
				}
				html += '<div class="col col-12 col-lg-12 col-md-12 col-sm-12 mb-0-dto">';
						html += '<div class="row '+ tipoClasse + '">';
							html += '<div class="col col-12 col-lg-3 col-md-3 col-sm-4 mb-0-dto">';
								html += '<p class="dia '+((diferencaDias > 0) ? 'dark-text': ' red-text text-accent-4 ') +'">'+ dataLimite.format('ddd') + '</p>';
								html += '<p class="data '+((diferencaDias > 0) ? 'dark-text': ' red-text text-accent-4 ') +'">'+ dataLimite.format('DD/MM/YYYY')+ '</p>';
							html += '</div>';
							html += '<div class="col col-12 col-lg-9 col-md-9 col-sm-8 mb-0-dto">';
								if(data.status == 'Em Progresso' ) html += '<span class="info_checklist yellow darken-2 btn-small">'+ data.status +'</span>';
								else if(data.status == 'Executado' ) html += '<span class="info_checklist green darken-1 btn-small">'+ data.status +'</span>';
								else if(data.status == 'Aguardando Execução') html += '<span class="info_checklist light-blue darken-3 btn-small">'+ data.status +'</span>';

								html += (temPaPendente()) ? "<a href='#' class='pas_pendentes'><span class='info_checklist orange darken-4 btn-small'>PA's</span></a>" : '';
								html += (temPendenciaPendente()) ? "<a href='#' class='pes_pendentes'><span class='info_checklist grey darken-1 btn-small'>Pendências</span></a>" : '';
								
								html += '<p><i class="mdi mdi-map-marker-radius orange-text text-accent-4"></i> <strong>' + data.loja.razaoSocial + '</strong> ' + data.loja.nomeFantasia + '</p>';
								if(!estaRespondido())html += '<a href="#" class="executar_checklist"><p><i class="mdi mdi-clipboard-check orange-text text-accent-4"></i> <strong class="black-text">' + data.titulo + '</strong></p></a>';
								else html += '<a href="#" class="ver_questionamentos_respondidos"><p><i class="mdi mdi-clipboard-check orange-text text-accent-4"></i> <strong class="orange-text text-accent-4">' + data.titulo + '</strong></p></a>';
								// html += '<a href="#" class="inteligencia_link"><p><i class="mdi mdi-clipboard-check orange-text text-accent-4"></i> <strong class="orange-text text-accent-4">' + data.titulo + '</strong></p></a>';								
								html += '<p class=" dias_restantes '+ ((diferencaDias >= 0) ?' dark-text ' : ' red-text text-accent-4 ' ) +'"><i class="mdi mdi-calendar-clock orange-text text-accent-4"></i> <strong>'+textoDiasRestantes+'</strong></p>';
								html += '<p><strong>Descrição : </strong> ' + data.descricao+ '</p>';
							html += '</div>';

							html += '<div class="col col-12 col-lg-12 col-md-12 col-sm-12 mb-0-dto opc_tabela">';
								html += '<div class="col col-12 col-lg-4 col-md-4 col-sm-4 mb-0-dto">';
									html += '<p class="mb-0-dto">';
									html += '<a href="#" class="detalhes-dto visualizar_checklist">';
									html += '<i class="mdi mdi-eye-outline small orange-text text-accent-4"></i>';
									html += 'VER DETALHES';
									html += '</a>';
									html += '</p>';
								html += '</div>';

								if(!estaRespondido()){
									html += '<div class="col col-12 col-lg-4 col-md-4 col-sm-4 mb-0-dto">';
										html += '<p class="mb-0-dto">';
										html += '<a href="#" class="detalhes-dto executar_checklist">';
										html += '<i class="mdi mdi-clipboard-check  orange-text text-accent-4"></i>';
										html += 'EXECUTAR';
										html += '</a>';
										html += '</p>';
									html += '</div>';
								}

								html += '<div class="col col-12 col-lg-4 col-md-4 col-sm-4 mb-0-dto">';

								html += '<p class="mb-0-dto">';
								html += '<a href="#" class="detalhes-dto perguntas">';
								html += '<i class="mdi mdi-note-text small  orange-text text-accent-4"></i>';
								html += 'Perguntas';
								html += '</a>';
								html += '</p>';
							html += '</div>';
							html += '</div>';
	
						html += '</div>';
					html += '</div>';
												
					return html;
			};

			objeto.rowsCallback = function(resposta){
				$('.executar_checklist').on('click', _this.executar);
				$('.pas_pendentes').on('click', function () {
					event.preventDefault();
					var objeto = _tabela.getObjetos()[$(this).parents('.listagem-padrao-item').index()];

					router.navigate('/planosacao-pendentes/'+ objeto.id);
				})

				$('.pes_pendentes').on('click', function () {
					event.preventDefault();
					var objeto = _tabela.getObjetos()[$(this).parents('.listagem-padrao-item').index()];
					router.navigate('/pendencias-pendentes/'+ objeto.id);
				})

				// $('.info_checklist').each(function (i, item) {
				// 	if($(item).html() == 'Executado'){
				// 		var htmlExecutado = ''
				// 		htmlExecutado += '<div class="row listagem-padrao-item">'
				// 		htmlExecutado += '<div class="col col-12 col-lg-12 col-md-12 col-sm-12 mb-0-dto">';

				// 		htmlExecutado += '<div id="executados" class="row agenda-dto">';
				// 		htmlExecutado += '<div class="col col-12 col-lg-12 col-md-12 col-sm-12 mb-0-dto">';
				// 		htmlExecutado += '<h6 class="center-align mb-0-dto">EXECUTADOS</h6>';
				// 		htmlExecutado += '</div>';
				// 		htmlExecutado += '</div>';
				// 		htmlExecutado += '</div>';
				// 		htmlExecutado += '</div>';
				// 		$(item).parents('.listagem-padrao-item').before(htmlExecutado)
				// 		return false;

				// 	}
				// }); 

				$('.visualizar_checklist').on('click',function(i, value){
					event.preventDefault();
					var objeto = _tabela.getObjetos()[$(this).parents('.listagem-padrao-item').index()];

					router.navigate('/visualizar-checklist/'+ objeto.id);
				});

				$('.perguntas').on('click',function(i, value){
					event.preventDefault();
					var objeto = _tabela.getObjetos()[$(this).parents('.listagem-padrao-item').index()];

					router.navigate('/checklist/perguntas/'+ objeto.id);
				});
			};

			return objeto;
		};

		_this.executar = function executar (event) {
			event.preventDefault();
			var objeto = _tabela.getObjetos()[$(this).parents('.listagem-padrao-item').index()];

			router.navigate('/executar-checklist/'+ objeto.id);
		};

		_this.atualizar = function atualizar(){
			_tabela.atualizarTabela();
		};

		_this.configurar = function configurar() {
			_tabela = _this.idTabela.listar(_this.opcoesDaTabela());
		};
	} // ControladoraListagemChecklist

	// Registrando
	app.ControladoraListagemChecklist = ControladoraListagemChecklist;
})(window, app, jQuery, toastr);