/**
 *  resposta.list.ctrl.js
 *
 *  @author	Rafael Vinicius Barros Ferreira
 */
(function(window, app, $, toastr, BootstrapDialog)
{
	'use strict';

	function ControladoraListagemResposta(servicoResposta)
	{
		var _this = this;
		var _cont = 0;
		var _tabela = null;
		_this.botaoVoltar = $('#voltar');
		_this.botaoAtualizar = $('#atualizar');
		_this.idTabela = $('#resposta');

		_this.idTarefa = window.location.href.split('#')[1].substring(1, url.length).split('/')[1];	

		var ctrlFormulario = new app.ControladoraFormResposta(servicoResposta, _this);

		//Configura a tabela
		_this.opcoesDaTabela = function opcoesDaTabela() {
			var objeto = $.extend(true, {}, app.dtOptions);
			objeto.ajax = servicoResposta.rota() + '/' + _this.idTarefa;

			objeto.columnDefs = [ {
					data: 'id',
					targets: 0,
					visible : true
				}, {
					data: 'pergunta.pergunta',
					targets: 1
				}, {
					data: function(data){
						var opcao = new app.Opcao();
						var opcaoSelecionada = data.opcaoSelecionada;

						return opcao.getpcoes()[opcaoSelecionada];
					},
					targets: 2
				}, {
					data :function(data) {
						return data.pergunta.formularioRespondido.respondedor.nome + ' ' + data.pergunta.formularioRespondido.respondedor.sobrenome;
					},					
					targets: 3
				}, {
					data: function(data) {
						var dataHora = moment(data.pergunta.formularioRespondido.dataHora, "YYYY-MM-DD HH:mm:ss", 'pt-br');
						
						return dataHora.format('DD/MM/YYYY HH:mm:ss').toString() ;
					},
					targets: 4
				},{
					data: function(data) {
						return (data.anexos.length > 0 ) ? '<a href="anexos.html" class="anexos"><i class="fas fa-paperclip"></i></a>' : 'Não há anexos.';
					},
					targets: 5
				}

			];

			objeto.fnDrawCallback = function(settings){
				$('tbody tr').on('click', '.anexos', function (event) {
					event.preventDefault();
					var objeto = _tabela.row($(this).parents('tr')).data();
					$('#anexos').modal();
					$('#anexos').find('#drop-zone').empty();
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

					$('#anexos').find('#drop-zone').append(html);

				});
			};

			return objeto;
		};

		_this.voltar = function voltar() {
			event.preventDefault();

			router.navigate('/tarefa');		
		};

		_this.configurar = function configurar() {
			_tabela = _this.idTabela.DataTable(_this.opcoesDaTabela());
			_this.botaoVoltar.on('click', _this.voltar);
		};
	} // ControladoraListagemResposta

	// Registrando
	app.ControladoraListagemResposta = ControladoraListagemResposta;
})(window, app, jQuery, toastr, BootstrapDialog);