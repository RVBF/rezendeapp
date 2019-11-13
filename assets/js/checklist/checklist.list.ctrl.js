/**
 *  setor.list.ctrl.js
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
			objeto.searching= true;
			objeto.ordering= true;
			objeto.searching = true;
			objeto.searchDelay = 600;	
			objeto.cadastrarLink = 'cadastrar_checklist_link';
			objeto.columnDefs = function (data){
				var estaRespondido = function estaRespondido() {
					var i = 0;
					for (var indice in data.questionamentos) {
						var elemento = data.questionamentos[indice];
						if(elemento.status == 'Não Respondido' || elemento.status == 'Respondido Com Pendências')i++;
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
				var dataLimite = moment(data.dataLimite);
				var diferencaDias = dataLimite.diff(moment(),'days');
				var textoDiasRestantes = (diferencaDias > 0) ? Math.abs(diferencaDias) +' dias para que o cheklist seja executado!' : Math.abs(diferencaDias) +' dias de atraso!';
					html += '<div class="col col-12 col-lg-12 col-md-12 col-sm-12">';
						html += '<div class="row agenda-dto">'
							html += '<div class="col col-12 col-lg-3 col-md-3 col-sm-4">';
								html += '<p class="dia '+((diferencaDias > 0) ? 'teal-text text-darken-1': ' red-text text-accent-4 ') +'">'+ dataLimite.format('ddd') + '</p>';
								html += '<p class="data">'+ dataLimite.format('DD/MM/YYYY')+ '</p>';
							html += '</div>';
							html += '<div class="col col-12 col-lg-9 col-md-9 col-sm-8">';
								if(data.status == 'Em Progresso' ) html += '<span class="info_checklist yellow darken-2 btn-small">'+ data.status +'</span>';
								else if(data.status == 'Executado' ) html += '<span class="info_checklist green darken-1 btn-small">'+ data.status +'</span>';
								else if(data.status == 'Aguardando Execução') html += '<span class="info_checklist red accent-4 btn-small">'+ data.status +'</span>';

								html += (temPaPendente()) ? '<span class="info_checklist orange darken-4 btn-small">PA Pendente</span>' : '';
								html += (temPendenciaPendente()) ? '<span class="info_checklist grey darken-1 btn-small">PA Pendente</span>' : '';
								
								html += '<p><i class="mdi mdi-map-marker-radius orange-text text-accent-4"></i> <strong>' + data.loja.razaoSocial + '</strong> ' + data.loja.nomeFantasia + '</p>';
								if(!estaRespondido())html += '<a href="#" class="executar_checklist"><p><i class="mdi mdi-clipboard-check orange-text text-accent-4"></i> <strong class="orange-text text-accent-4">' + data.titulo + '</strong></p></a>';
								else html += '<a href="#" class="ver_questionamentos_respondidos"><p><i class="mdi mdi-clipboard-check orange-text text-accent-4"></i> <strong class="orange-text text-accent-4">' + data.titulo + '</strong></p></a>';
								// html += '<a href="#" class="inteligencia_link"><p><i class="mdi mdi-clipboard-check orange-text text-accent-4"></i> <strong class="orange-text text-accent-4">' + data.titulo + '</strong></p></a>';								
								html += '<p class="'+((diferencaDias > 0) ? 'teal-text text-darken-1': ' red-text text-accent-4 ')+'"><i class="mdi mdi-calendar-clock orange-text text-accent-4"></i> <strong>'+textoDiasRestantes+'</strong></p>';
								html += '<p><strong>Descrição : </strong> ' + data.descricao+ '</p>';
							html += '</div>';
						html += '</div>';
					html += '</div>';
					
						
							
					return html;
				};

			objeto.rowsCallback = function(resposta){
				$('.remover_setor_link').on('click', _this.remover);
				$('.executar_checklist').on('click', _this.executar);
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

		_this.remover = function remover(event){
			var objeto = _tabela.getObjetos()[$(this).parents('.listagem-padrao-item').index()];
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
			_tabela = _this.idTabela.listar(_this.opcoesDaTabela());
		};
	} // ControladoraListagemChecklist

	// Registrando
	app.ControladoraListagemChecklist = ControladoraListagemChecklist;
})(window, app, jQuery, toastr);