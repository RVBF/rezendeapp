/**
 *  pendencia.list.ctrl.js
 *
 *  @author	Rafael Vinicius Barros Ferreira
 */
(function(window, app, $, toastr)
{
	'use strict';

	function ControladoraListagemPendencia(servicoPendencia) {
		var _this = this;
		var _cont = 0;
		var _tabela = null;
		_this.botaoCadastrar = $('#cadastrar');
		_this.botaoEditar = $('#editar');
		_this.botaoRemover = $('#remover');
		_this.botaoAtualizar = $('#atualizar');
		_this.idTabela = $('#pendencia');

		//Configura a tabela
		_this.opcoesDaTabela = function opcoesDaTabela() {
			var objeto =  new Object();
			objeto.ajax = servicoPendencia.rota();

			objeto.carregando = true;
			objeto.pageLength = 10;
			objeto.lengthMenu =  [10, 30, 40, 100];
			objeto.searching= true;
			objeto.ordering= true;
			objeto.searching = true;
			objeto.searchDelay = 600;
			objeto.cadastrarLink = 'cadastrar_pendencia_link';
			objeto.columnDefs = function (data){
				var html = '';
				var dataLimite = moment(data.dataLimite);
				var diferencaDias = dataLimite.diff(moment(),'days');
				var textoDiasRestantes = (diferencaDias > 0) ? Math.abs(diferencaDias) +' dias para que o cheklist seja executado!' : Math.abs(diferencaDias) +' dias de atraso!';
				var textoDiasRestantes = (diferencaDias > 0) ? Math.abs(diferencaDias) +' dias para que o cheklist seja executado!' : Math.abs(diferencaDias) +' dias de atraso!';
				if(data.status == 'Executado'){
					textoDiasRestantes = 'Encerrado!';
				}
				var tipoClasse = !(data.status == 'Executado') ? ' agenda-dto ' : ' agenda-dto cinza ';
				html += '<div class="col col-12 col-lg-12 col-md-12 col-sm-12 mb-0-dto">';
				html += '<div class="row '+ tipoClasse + ' plano-dto">';
					html += '<div class="col col-12 col-lg-3 col-md-3 col-sm-4">';
						html += '<p class="dia '+((diferencaDias > 0) ? 'black-text': ' red-text text-accent-4 ') +'">'+ dataLimite.format('ddd') + '</p>';
						html += '<p class="data">'+ dataLimite.format('DD/MM/YYYY')+ '</p>';
					html += '</div>';
					html += '<div class="col col-12 col-lg-9 col-md-9 col-sm-8">';
						if(data.status == 'Aguardando Responsável' ) html += '<span class="info_checklist yellow darken-2 btn-small">'+ data.status +'</span>';
						else if(data.status == 'Executado' ) html += '<span class="info_checklist green darken-1 btn-small">'+ data.status +'</span>';
						else if(data.status == 'Aguardando Execução') html += '<span class="info_checklist light-blue darken-3 btn-small">'+ data.status +'</span>';

						'<span class="info_checklist orange darken-4 btn-small">PA Pendente</span>';
						'<span class="info_checklist grey darken-1 btn-small">Pendência Pendente</span>';

						// html += '<p><i class="mdi mdi-map-marker-radius orange-text text-accent-4"></i> <strong>' + data.loja.razaoSocial + '</strong> ' + data.loja.nomeFantasia + '</p>';
						html += '<a href="#" class="executar_pe"><p><i class="mdi mdi-clipboard-check orange-text text-accent-4"></i> <strong class="black-text descricao">' + data.descricao + '</strong></p></a>';
						html += '<p class="dias_restantes  '+((diferencaDias > 0) ? 'black-text': ' red-text text-accent-4 ')+'"><i class="mdi mdi-calendar-clock orange-text text-accent-4"></i> <strong>'+textoDiasRestantes+'</strong></p>';
						html += '<p class="descricao"><strong>Descrição da solução : </strong> ' + data.solucao + '</p>';
					html += '</div>';

					html += '<div class="col col-12 col-lg-12 col-md-12 col-sm-12 mb-0-dto opc_tabela">';
							html += '<div class="row">'
								html += '<div class="col col-12 col-lg-4 col-md-4 col-sm-4 mb-0-dto">';
									html += '<p class="mb-0-dto">';
									html += '<a href="#" class="detalhes-dto visualizar_pa">';
									html += '<i class="mdi mdi-eye-outline orange-text text-accent-4"></i>';
									html += 'VER DETALHES';
									html += '</a>';
									html += '</p>';
								html += '</div>';
								if(data.status != 'Executado' ){
									html += '<div class="col col-12 col-lg-4 col-md-4 col-sm-4 mb-0-dto">';
									html += '<p class="mb-0-dto">';
									html += '<a href="#" class="detalhes-dto '+((data.status != 'Executado') ? 'executar_pe' : '') + ' ">';
									html += '<i class="mdi mdi-eye-outline orange-text text-accent-4 "></i>';
									html += 'EXECUTAR';
									html += '</a>';
									html += '</p>';
									html += '</div>';
								}
							html += '</div>';

					html += '</div>';
				html += '</div>';


				html += '</div>';

				return html;
			};
			objeto.rowsCallback = function(resposta){
				$('.remover_setor_link').on('click', _this.remover);
				$('.executar_pe').on('click', _this.executar);
				$('.visualizar_pa').on('click', _this.visualizar);
				$('.confirmar_responsabilidade').on('click', _this.confirmarResponsabilidade);
				$('.devolver_responsabilidade').on('click', _this.devolverResponsabilidade);
			};

			return objeto;
		};

		_this.executar = function executar (event) {
			event.preventDefault();
			var objeto = _tabela.getObjetos()[$(this).parents('.listagem-padrao-item').index()];

			router.navigate('/executar-pendencia/'+ objeto.id);
		};

		_this.confirmarResponsabilidade = function confirmarResponsabilidade(event) {
			event.preventDefault();
			var objeto = _tabela.getObjetos()[$(this).parents('.listagem-padrao-item').index()];

			servicoPendencia.confirmarResponsabilidade(objeto.id).done(function (resposta) {
				if(resposta.status){
					_this.atualizar();
					toastr.success(resposta.mensagem);

				}
				else{
					if(resposta != undefined && resposta.mensagem) toastr.error(resposta.mensagem);
				}
			});
		}

		_this.visualizar = function visualizar(event) {
			event.preventDefault();
			var objeto = _tabela.getObjetos()[$(this).parents('.listagem-padrao-item').index()];
			router.navigate('/visualizar-pendencia/'+ objeto.id);
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
							servicoPendencia.remover(objeto.id).done(window.sucessoPadrao).fail(window.erro);
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
	} // ControladoraListagemPendencia

	// Registrando
	app.ControladoraListagemPendencia = ControladoraListagemPendencia;
})(window, app, jQuery, toastr);