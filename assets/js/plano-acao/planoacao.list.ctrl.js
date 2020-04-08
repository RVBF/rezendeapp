/**
 *  planoacao.list.ctrl.js
 *
 *  @author	Rafael Vinicius Barros Ferreira
 */
(function(window, app, $, toastr)
{
	'use strict';

	function ControladoraListagemPlanoAcao(servicoPlanoAcao)
	{
		var _this = this;
		var _cont = 0;
		var _tabela = null;
		_this.botaoCadastrar = $('#cadastrar');
		_this.botaoEditar = $('#editar');
		_this.botaoRemover = $('#remover');
		_this.botaoAtualizar = $('#atualizar');
		_this.idTabela = $('#planoacao');

		//Configura a tabela
		_this.opcoesDaTabela = function opcoesDaTabela() {
			var objeto =  new Object();
			objeto.ajax = servicoPlanoAcao.rota();

			objeto.carregando = true;
			objeto.pageLength = 10;
			objeto.lengthMenu =  [10, 30, 40, 100];
			objeto.searching= true;
			objeto.ordering= true;
			objeto.searching = true;
			objeto.searchDelay = 600;
			objeto.cadastrarLink = 'cadastrar_planoacao_link';
			objeto.columnDefs = function (data){
				var html = '';
				var dataLimite = moment(data.dataLimite);
				var textoDiasRestantes = '';
				if(dataLimite.isSameOrAfter(moment())){
					var dataParaComparacao = moment(data.dataLimite);
					var diferencaAnos = dataParaComparacao.diff(moment(),'years');
					var diferencaMeses = (diferencaAnos > 0) ? dataParaComparacao.subtract(diferencaAnos, 'years').diff(moment(),'months') : dataParaComparacao.diff(moment(),'months');
					var diferencaDias = (diferencaMeses > 0) ? dataParaComparacao.subtract(diferencaMeses, 'months').diff(moment(),'days') : dataParaComparacao.diff(moment(),'days');
					var diferencaHoras = (diferencaDias > 0) ? dataParaComparacao.subtract(diferencaDias, 'days').diff(moment(),'hours'): dataParaComparacao.diff(moment(),'hours');
					var diferencaMinutos = (diferencaHoras > 0) ? dataParaComparacao.subtract(diferencaHoras, 'hours').diff(moment(),'minutes') : dataParaComparacao.diff(moment(),'minutes');;

					textoDiasRestantes = ((Math.abs(diferencaAnos) == 1) ? Math.abs(diferencaAnos) +' ano' : Math.abs(diferencaAnos) +' anos') + ', '+ ((Math.abs(diferencaMeses) == 1) ? Math.abs(diferencaMeses) +' mês' : Math.abs(diferencaMeses) +' meses') + ', ' + ((Math.abs(diferencaDias) == 1) ? Math.abs(diferencaDias) + ' dia' : Math.abs(diferencaDias) +' dias') + ', '+((diferencaHoras == 1) ?  Math.abs(diferencaHoras) +' hora' : Math.abs(diferencaHoras) +' horas') +' e '+((diferencaMinutos == 1) ?  Math.abs(diferencaMinutos) +' minuto ' : Math.abs(diferencaMinutos) +' minutos ')+'para que o plano de ação seja executado!' 
				}
				else
				{
					textoDiasRestantes = 'O plano de ação está atrasado!'
				}
				if(data.status == 'Executado'){
					textoDiasRestantes = 'Encerrado!';
				}

				var tipoClasse = !(data.status == 'Executado') ? ' agenda-dto ' : ' agenda-dto cinza ';
				html += '<div class="col col-12 col-lg-12 col-md-12 col-sm-12 mb-0-dto">';
				html += '<div class="row '+ tipoClasse + ' plano-dto">';
					html += '<div class="col col-12 col-lg-3 col-md-3 col-sm-4">';
						html += '<p class="dia '+((dataLimite.isSameOrAfter(moment())) ? 'black-text': ' red-text text-accent-4 ') +'">'+ dataLimite.format('ddd') + '</p>';
						html += '<p class="data '+((dataLimite.isSameOrAfter(moment())) ? 'black-text': ' red-text text-accent-4 ') +'">'+ dataLimite.format('DD/MM/YYYY')+ '</p>';
					html += '</div>';
					html += '<div class="col col-12 col-lg-9 col-md-9 col-sm-8">';
						html += '<div class="col col-12 col-lg-12 col-md-12 col-sm-12 mb-0-dto">';
							if(data.status == 'Aguardando Responsável' ) html += '<span class="info_checklist yellow darken-2 btn-small">'+ data.status +'</span>';
							else if(data.status == 'Executado' ) html += '<span class="info_checklist green darken-1 btn-small">'+ data.status +'</span>';
							else if(data.status == 'Aguardando Execução') html += '<span class="info_checklist light-blue darken-3 btn-small">'+ data.status +'</span>';
						html += '</div>';
						if(data.status != 'Executado') html += '<a href="#" class="executar_pa"><p><i class="mdi mdi-clipboard-check orange-text text-accent-4"></i> <strong class="black-text descricao">' + data.descricao + '</strong></p></a>';
						else html += '</i> <strong class="black-text descricao">' + data.descricao + '</strong></p>';
						html += '<p class="dias_restantes '+((dataLimite.isSameOrAfter(moment())) ? 'black-text': ' red-text text-accent-4 ')+'"><i class="mdi mdi-calendar-clock orange-text text-accent-4"></i> <strong>'+textoDiasRestantes+'</strong></p>';
						html += '<p><strong>Ações para Solução : </strong> ';
						for (const index in data.solucao.acoes) {
							var acao =  data.solucao.acoes[index];
							html += acao.acao + ((parseInt(index)+1 == data.solucao.acoes.length) ? '.' : ',');
						}
						html += '</p>';
					html += '</div>';

					html += '<div class="col col-12 col-lg-12 col-md-12 col-sm-12 mb-0-dto opc_tabela">';
							html += '<div class="row">'
								html += '<div class="col col-12 col-lg-3 col-md-3 col-sm-3 mb-0-dto">';

									html += '<p class="mb-0-dto">';
									html += '<a href="#" class="detalhes-dto visualizar_pa">';
									html += '<i class="mdi mdi-eye-outline orange-text text-accent-4"></i>';
									html += 'VER DETALHES';
									html += '</a>';
									html += '</p>';
								html += '</div>';
								if (data.anexos.length > 0 ) {
									html += '<div class="col col-12 col-lg-3 col-md-3 col-sm-3 mb-0-dto">';
										html += '<a href="anexos.html" class="anexos detalhes-dto"><i class="mdi mdi-paperclip orange-text"></i>ANEXOS</a>';
									html += '</div>';
								}
								if(data.status != 'Executado' ) {
									html += '<div class="col col-12 col-lg-3 col-md-3 col-sm-3 mb-0-dto">';
										html += '<p class="mb-0-dto">';
										html += '<a href="#" class="detalhes-dto executar_pa">';
										html += '<i class="mdi mdi-clipboard-check orange-text text-accent-4"></i>';
										html += 'EXECUTAR';
										html += '</a>';
										html += '</p>';
									html += '</div>';
								}

								if(!data.responsabilidade){
									html += '<div class="col col-12 col-lg-3 col-md-3 col-sm-3 mb-0-dto">';
										html += '<p class="mb-0-dto">';
										html += '<a href="#" class="detalhes-dto confirmar_responsabilidade">';
										html += '<i class="far fa-check-square orange-text text-accent-4"></i>';
										html += 'Confirmar responsabilidade';
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
				$('.executar_pa').on('click', _this.executar);
				$('.visualizar_pa').on('click', _this.visualizar);
				$('.confirmar_responsabilidade').on('click', _this.confirmarResponsabilidade);
				$('.devolver_responsabilidade').on('click', _this.devolverResponsabilidade);

				$('.anexos').on('click', function (event) {
					event.preventDefault();
					var objeto = _tabela.getObjetos()[$(this).parents('.listagem-padrao-item').index()];
					$('.modal').modal();
					$('.modal').find('#drop-zone').empty();
					var contador = 0;

					var html = '';
					for(var indice in objeto.anexos) {
						var caminho = objeto.anexos[indice].patch.split('/');
						var nome = caminho[caminho.length -1];
						var conteudo = objeto.anexos[indice].arquivoBase64.split(';')[1];

						html += (contador == 0) ? '<div class="row">' : '';
						html += (contador >= 0 && contador <= 3) ? '<div class="col-md-4 col-sm-4 col-xs-4 col-4" >' : '' ;
						html += '<a  class="download" href="' +  objeto.anexos[indice].arquivoBase64 + '" arquivo="' + conteudo + '" nomeArquivo="' + nome + '"  tipo="'+ objeto.anexos[indice].tipo +'" download>';
						html += (objeto.anexos[indice].tipo.split('/')[0] == 'image') ? '<i class="fas fa-file-image"></i>' : '<i class="far fa-file-audio"></i>';
						html += '<br><span class="name toltip" title="' + nome + '">' + nome.substring(1, 10) + '</span></a>';
						html += (contador >= 0 && contador <= 3) ?  '</div>' : '';
						html +=  (contador == 3) ? '</div>': '';

						contador++;

						if(contador == 3) contador = 0;
						else contador++;
					}

					$('.modal').find('#drop-zone').append(html);

				});
			};

			return objeto;
		};

		_this.executar = function executar (event) {
			event.preventDefault();
			var objeto = _tabela.getObjetos()[$(this).parents('.listagem-padrao-item').index()];

			router.navigate('/executar-pa/'+ objeto.id);
		};

		_this.confirmarResponsabilidade = function confirmarResponsabilidade(event) {
			event.preventDefault();
			var objeto = _tabela.getObjetos()[$(this).parents('.listagem-padrao-item').index()];

			servicoPlanoAcao.confirmarResponsabilidade(objeto.id).done(function (resposta) {
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
			router.navigate('/visualizar-pa/'+ objeto.id);
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
							servicoPlanoAcao.remover(objeto.id).done(window.sucessoPadrao).fail(window.erro);
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
	} // ControladoraListagemPlanoAcao

	// Registrando
	app.ControladoraListagemPlanoAcao = ControladoraListagemPlanoAcao;
})(window, app, jQuery, toastr);