/**
 *  planoacaopendente.list.ctrl.js
 *
 *  @author	Rafael Vinicius Barros Ferreira
 */
(function(window, app, $, toastr)
{
	'use strict';

	function ControladoraListagemPlanoAcaoPendente(servicoPlanoAcao)
	{
		var _this = this;
		var _cont = 0;
		var _tabela = null;
		_this.botaoCadastrar = $('#cadastrar');
		_this.botaoEditar = $('#editar');
		_this.botaoRemover = $('#remover');
		_this.botaoAtualizar = $('#atualizar');
        _this.idTabela = $('#planoacao_pendentes');

        var pegarId = function pegarId(url, palavra)
		{
			// Terminando com "ID/palavra"
			var regexS = palavra+'+\/[0-9]{1,}';

			var regex = new RegExp(regexS);
			var resultado = regex.exec(url);
			if (!resultado || resultado.length < 1)
			{
				return 0;
			}

			var array = resultado[0].split('/');
			return array[1];
		};

		//Configura a tabela
		_this.opcoesDaTabela = function opcoesDaTabela() {
            var objeto =  new Object();
			objeto.ajax = servicoPlanoAcao.paPendentes(pegarId(window.location.href,'planosacao-pendentes'));

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
				var diferencaDias = dataLimite.diff(moment(),'days');
				var textoDiasRestantes = (diferencaDias > 0) ? Math.abs(diferencaDias) +' dias para que o cheklist seja executado!' : Math.abs(diferencaDias) +' dias de atraso!';
				var textoDiasRestantes = (diferencaDias > 0) ? Math.abs(diferencaDias) +' dias para que o cheklist seja executado!' : Math.abs(diferencaDias) +' dias de atraso!';
				var tipoClasse = !(data.status == 'Executado') ? ' agenda-dto ' : ' agenda-dto cinza ';
				html += '<div class="col col-12 col-lg-12 col-md-12 col-sm-12 mb-0-dto">';
				html += '<div class="row '+ tipoClasse + ' plano-dto">';
					html += '<div class="col col-12 col-lg-3 col-md-3 col-sm-4">';
						html += '<p class="dia '+((diferencaDias > 0) ? 'teal-text text-darken-1': ' red-text text-accent-4 ') +'">'+ dataLimite.format('ddd') + '</p>';
						html += '<p class="data">'+ dataLimite.format('DD/MM/YYYY')+ '</p>';
					html += '</div>';
					html += '<div class="col col-12 col-lg-9 col-md-9 col-sm-8">';
						html += '<div class="col col-12 col-lg-12 col-md-12 col-sm-12 mb-0-dto">';
							if(data.status == 'Aguardando Responsável' ) html += '<span class="info_checklist yellow darken-2 btn-small">'+ data.status +'</span>';
							else if(data.status == 'Executado' ) html += '<span class="info_checklist green darken-1 btn-small">'+ data.status +'</span>';
							else if(data.status == 'Aguardando Execução') html += '<span class="info_checklist red accent-4 btn-small">'+ data.status +'</span>';
						html += '</div>';
						if(data.status != 'Executado') html += '<a href="#" class="executar_pa"><p><i class="mdi mdi-clipboard-check orange-text text-accent-4"></i> <strong class="orange-text text-accent-4">' + data.descricao + '</strong></p></a>';
						else html += '</i> <strong class="orange-text text-accent-4">' + data.descricao + '</strong></p>';
						html += '<p class="'+((diferencaDias > 0) ? ' teal-text text-darken-1 ': ' red-text text-accent-4 ')+'"><i class="mdi mdi-calendar-clock orange-text text-accent-4"></i> <strong>'+textoDiasRestantes+'</strong></p>';
						html += '<p><strong>Descrição da solução : </strong> ' + data.solucao + '</p>';
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
								html += '<div class="col col-12 col-lg-3 col-md-3 col-sm-3 mb-0-dto">';
									if (data.anexos.length > 0 ) html += '<a href="anexos.html" class="anexos"><i class="mdi mdi-paperclip orange-text text-accent-4"></i>ANEXOS</a>';
								html += '</div>';

								if(!data.responsabilidade){
									html += '<div class="col col-12 col-lg-4 col-md-4 col-sm-4 mb-0-dto">';
										html += '<p class="mb-0-dto">';
										html += '<a href="#" class="detalhes-dto confirmar_responsabilidade">';
										html += '<i class="far fa-check-square orange-text text-accent-4"></i>';
										html += 'Confirmar responsabilidade';
										html += '</a>';
										html += '</p>';
									html += '</div>';

									html += '<div class="col col-12 col-lg-4 col-md-4 col-sm-4 mb-0-dto">';
										html += '<p class="mb-0-dto">';
										html += '<a href="#" class="detalhes-dto devolver_responsabilidade">';
										html += '<i class="far fa-window-close orange-text text-accent-4"></i>';
										html += 'Devolver responsabilidade';
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
					toastr.error(resposta.mensagem);
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
	} // ControladoraListagemPlanoAcaoPendente

	// Registrando
	app.ControladoraListagemPlanoAcaoPendente = ControladoraListagemPlanoAcaoPendente;
})(window, app, jQuery, toastr);