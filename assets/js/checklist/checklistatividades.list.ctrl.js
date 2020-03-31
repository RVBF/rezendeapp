/**
 *  setor.list.ctrl.js
 *
 *  @author	Rafael Vinicius Barros Ferreira
 */
(function(window, app, $, toastr)
{
	'use strict';

	function ControladoraListagemChecklistAtividades(servicoChecklist)
	{
		var _this = this;
		var _cont = 0;
		var _tabela = null;
		_this.botaoCadastrar = $('#cadastrar');
		_this.botaoEditar = $('#editar');
		_this.botaoRemover = $('#remover');
		_this.botaoAtualizar = $('#atualizar');
		_this.idTabela = $('#minhas_atvidades');

		//Configura a tabela
		_this.opcoesDaTabela = function opcoesDaTabela() {
			var objeto =  new Object();
			objeto.ajax = servicoChecklist.rota();

            objeto.listagemTemporal = true;
			objeto.carregando = true;
			objeto.pageLength = 10;
			objeto.searching= true;
			objeto.searchDelay = 600;	
			objeto.header = 'MINHAS ATIVIDADES';
			objeto.hasHeader = true;
            objeto.cadastrarLink = 'cadastrar_setor_link';
            objeto.classesDesignerTabela = 'agenda-dto valign-wrapper';
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
				html +='<div class="executar_checklist col col-12 col-lg-12 col-md-12 col-sm-12">';
					html += '<div class="col col-12 col-lg-3 col-md-3 col-sm-4 mb-0-dto">';
						html += '<p class="dia '+((diferencaDias > 0) ? 'dark-text': ' red-text text-accent-4 ') +'">'+ dataLimite.format('ddd') + '</p>';
						html += '<p class="data '+((diferencaDias > 0) ? 'dark-text': ' red-text text-accent-4 ') +'">'+ dataLimite.format('DD/MM/YYYY')+ '</p>';
					html += '</div>';
					html += '<div class="col col-12 col-lg-9 col-md-9 col-sm-8 mb-0-dto">';
						if(data.status == 'Incompleto' ) html += '<span class="info_checklist yellow darken-2 btn-small">'+ data.status +'</span>';
						else if(data.status == 'Executado' ) html += '<span class="info_checklist green darken-1 btn-small">'+ data.status +'</span>';
						else if(data.status == 'Aguardando Execução') html += '<span class="info_checklist light-blue darken-3 btn-small">'+ data.status +'</span>';

						html += (temPaPendente()) ? "<a href='#' class='pas_pendentes'><span class='info_checklist orange darken-4 btn-small'>PA's</span></a>" : '';
						html += (temPendenciaPendente()) ? "<a href='#' class='pes_pendentes'><span class='info_checklist grey darken-1 btn-small'>Pendências</span></a>" : '';
						
						html += '<p><i class="mdi mdi-map-marker-radius orange-text text-accent-4"></i> <strong>' + data.loja.razaoSocial + '</strong> ' + data.loja.nomeFantasia + '</p>';
						if(!estaRespondido())html += '<p><i class="mdi mdi-clipboard-check orange-text text-accent-4"></i> <strong class="black-text">' + data.titulo + '</strong></p>';
						else html += '<a href="#" class="ver_questionamentos_respondidos"><p><i class="mdi mdi-clipboard-check orange-text text-accent-4"></i> <strong class="orange-text text-accent-4">' + data.titulo + '</strong></p></a>';
						// html += '<a href="#" class="inteligencia_link"><p><i class="mdi mdi-clipboard-check orange-text text-accent-4"></i> <strong class="orange-text text-accent-4">' + data.titulo + '</strong></p></a>';								
						html += '<p class=" dias_restantes '+ ((diferencaDias >= 0) ?' dark-text ' : ' red-text text-accent-4 ' ) +'"><i class="mdi mdi-calendar-clock orange-text text-accent-4"></i> <strong>'+textoDiasRestantes+'</strong></p>';
						html += '<p><strong>Descrição : </strong> ' + data.descricao+ '</p>';
					html += '</div>';
				html += '</div>';
				

				// html += '<div class="col col-12 col-lg-12 col-md-12 col-sm-12 mb-0-dto opc_tabela">';
				// 	html += '<div class="col col-12 col-lg-4 col-md-4 col-sm-4 mb-0-dto">';
				// 		html += '<p class="mb-0-dto">';
				// 		html += '<a href="#" class="detalhes-dto visualizar_checklist">';
				// 		html += '<i class="mdi mdi-eye-outline small orange-text text-accent-4"></i>';
				// 		html += 'VER DETALHES';
				// 		html += '</a>';
				// 		html += '</p>';
				// 	html += '</div>';

				// 	if(data.status != 'Executado' ){
				// 		html += '<div class="col col-12 col-lg-4 col-md-4 col-sm-4 mb-0-dto">';
				// 			html += '<p class="mb-0-dto">';
				// 			html += '<a href="#" class="detalhes-dto executar_checklist">';
				// 			html += '<i class="mdi mdi-clipboard-check  orange-text text-accent-4"></i>';
				// 			html += 'EXECUTAR';
				// 			html += '</a>';
				// 			html += '</p>';
				// 		html += '</div>';
				// 	}

				// 	html += '<div class="col col-12 col-lg-4 col-md-4 col-sm-4 mb-0-dto">';

				// 		html += '<p class="mb-0-dto">';
				// 		html += '<a href="#" class="detalhes-dto perguntas">';
				// 		html += '<i class="mdi mdi-note-text small  orange-text text-accent-4"></i>';
				// 		html += 'Perguntas';
				// 		html += '</a>';
				// 		html += '</p>';
				// 	html += '</div>';
				html += '</div>';
												
					return html;
			};

			objeto.rowsCallback = function(resposta){
				$('.remover_setor_link').on('click', _this.remover);
				$('.pas_pendentes').on('click', function () {
					event.preventDefault();
					var objeto = _tabela.getObjetosTemporalListagem()[$(this).parents('.listagem-padrao-item').index() -1];

					router.navigate('/planosacao-pendentes/'+ objeto.id);
				});

				$('.pes_pendentes').on('click', function () {
					event.preventDefault();
					var objeto = _tabela.getObjetosTemporalListagem()[$(this).parents('.listagem-padrao-item').index() -1];
					router.navigate('/pendencias-pendentes/'+ objeto.id);
				});

				$('.executar_checklist').on('dblclick', _this.executar);
			};

			return objeto;
		};


		_this.atualizar = function atualizar(){
			_tabela.atualizarTabela();
		};

		_this.executar = function executar (event) {
			event.preventDefault();

			var estaRespondido = function estaRespondido(objeto) {
				var i = 0;
				for (var indice in objeto.questionamentos) {
					var elemento = objeto.questionamentos[indice];
					if(elemento.status == 'Respondido' || elemento.status == 'Respondido Com Pendências')i++;
				}

				return ( i == objeto.questionamentos.length ) ? true : false;
			};
			
			var objeto = _tabela.getObjetosTemporalListagem()[$(this).parents('.listagem-padrao-item').index() -1];
			if(!estaRespondido(objeto)) router.navigate('/executar-checklist/'+ objeto.id);
			else toastr.error('O  Checklist já foi respondido, porém possui pendências a serem resolvidas.')
		};

		_this.remover = function remover(event){
			var objeto = _tabela.getObjetosTemporalListagem()[$(this).parents('.listagem-padrao-item').index() -1];
			BootstrapDialog.show({
				type	: BootstrapDialog.TYPE_DANGER,
				title	: 'Deseja remover este Stor?',
				message	: 'Título: ' + objeto.titulo + '<br> Descrição :' + objeto.descricao,
				size	: BootstrapDialog.SIZE_LARGE,
				buttons	: [ {
						label	: '<u>S</u>im',
						hotkey	: 'S'.charCodeAt(0),
						action	: function(dialog){
							servicoChecklist.remover(objeto.id).done(window.sucessoPadrao).fail(window.erro);
							_this.atualizar();

							dialog.close();
						}
					}, {
						label	: '<u>N</u>ão',
						hotkey	: 'N'.charCodeAt(0),
						action	: function(dialog){
							dialog.close();
						}
					}
				]
			});
		}; // remover

		_this.configurar = function configurar() {
			_tabela = _this.idTabela.listagemTemporal(_this.opcoesDaTabela());
		};
	} // ControladoraListagemChecklistAtividades

	// Registrando
	app.ControladoraListagemChecklistAtividades = ControladoraListagemChecklistAtividades;
})(window, app, jQuery, toastr);