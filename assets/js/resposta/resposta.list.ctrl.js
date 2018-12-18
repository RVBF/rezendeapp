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
		_this.botaoCadastrar = $('#cadastrar');
		_this.botaoEditar = $('#editar');
		_this.botaoRemover = $('#remover');
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
					responsivePriority: 5,
					visible : true
				}, {
					data: 'pergunta.pergunta',
					responsivePriority: 1,
					targets: 1
				}, {
					data: function(data){
						var opcao = new app.Opcao();
						var opcaoSelecionada = data.opcaoSelecionada;

						return opcao.getpcoes()[opcaoSelecionada];
					},
					responsivePriority: 2,
					targets: 2
				}, {
					data: function() {
						return '<a href="anexos.html" class="anexos"><i class="fas fa-paperclip"></i></a>';
					},
					responsivePriority: 3,
					targets: 3
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
						html += '<a  class="download" href="#" nomeArquivo="' + nome + '" tipo="'+ objeto.anexos[indice].tipo +'" tipo="'+ objeto.anexos[indice].tipo +'" src="' + objeto.anexos[indice].arquivoBase64 + '">';
						html += (objeto.anexos[indice].tipo.split('/')[0] == 'image') ? '<i class="fas fa-file-image"></i>' : '<i class="far fa-file-audio"></i>';
						html += '<br>' + nome + '</a>';
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

		_this.cadastrar = function cadastrar() {
			var modoEdicao = false;
			var contexto = $('#painel_formulario');
			contexto.addClass('desabilitado');

			contexto.addClass('d-none');
			contexto.desabilitar(true);
			contexto.find('form')[0].reset();
			contexto.find('form').find('.msg').empty();
			ctrlFormulario.configurar(modoEdicao);
		};

		_this.editar = function editar() {
			var objeto = _tabela.row('.selected').data();
			var modoEdicao = true;
			var contexto = $('#painel_formulario');
			contexto.addClass('desabilitado');

			contexto.addClass('d-none');
			contexto.desabilitar(true);
			contexto.find('form')[0].reset();
			contexto.find('form').find('.msg').empty();
			
			ctrlFormulario.configurar(modoEdicao);
			ctrlFormulario.desenhar(objeto);
		}

		_this.configurar = function configurar() {
			_tabela = _this.idTabela.DataTable(_this.opcoesDaTabela());

			$('body').on('click', '.download', function() {
				var elemento = $(this);
				// location.href=elemento.attr('src');

				download(elemento.attr('src'), elemento.attr('nomeArquivo'), elemento.attr('tipo'))
			});
		};
	} // ControladoraListagemResposta

	// Registrando
	app.ControladoraListagemResposta = ControladoraListagemResposta;
})(window, app, jQuery, toastr, BootstrapDialog);