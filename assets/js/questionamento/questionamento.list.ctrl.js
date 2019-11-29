/**
 *  questionamento.list.ctrl.js
 *
 *  @author	Rafael Vinicius Barros Ferreira
 */
(function(window, app, $, toastr)
{
	'use strict';

	function ControladoraListagemQuestionamento(servicoQuestionamento)
	{
		var _this = this;
		var _cont = 0;
		var _tabela = null;
		_this.botaoCadastrar = $('#cadastrar');
		_this.botaoEditar = $('#editar');
		_this.botaoRemover = $('#remover');
		_this.botaoAtualizar = $('#atualizar');
		_this.idTabela = $('#questionamentos');
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
			objeto.ajax = servicoQuestionamento.todos(pegarId(window.location.href,'perguntas'));
            objeto.listagemTemporal = true;
			objeto.carregando = true;
			objeto.pageLength = 10;
			objeto.searching= true;
			objeto.searchDelay = 600;	
			objeto.header = 'Questionamentos';
			objeto.hasHeader = true;
            objeto.classesDesignerTabela = 'agenda-dto valign-wrapper';
			objeto.columnDefs = function (data){
				console.log(data);
                var html = '';
                html += '<div class="col col-12 col-lg-4 col-md-4 col-sm-4">';
                    if(data.status == 'Respondido Com Pendências' ) html += '<span class="info_checklist yellow darken-2 btn-small">'+ data.status +'</span>';
                    else if(data.status == 'Respondido' ) html += '<span class="info_checklist green darken-1 btn-small">'+ data.status +'</span>';
                    else if(data.status == 'Não Respondido') html += '<span class="info_checklist red accent-4 btn-small">'+ data.status +'</span>';
                html += '</div>';
                html += '<div class="col col-12 col-lg-6 col-md-6 col-sm-6">';
                    html += '<p class=""><strong> Pergunta : </strong>'+ data.formularioPergunta.pergunta + '</p>';
                  if(typeof data.formularioPergunta.opcao  != 'undefined')  html += '<p class=""><strong>Opção selecionada : </strong>'+ data.formularioResposta.opcao + '</p>';
				html += '</div>';
				
				html += '<div class="col col-12 col-lg-2 col-md-2 col-sm-2 mb-0-dto">';
					if (data.anexos.length > 0 ) html += '<a href="anexos.html" class="anexos detalhes-dto"><i class="mdi mdi-paperclip small orange-text text-accent-4"></i>ANEXOS</a>';
				html += '</div>';


				return html;
			};

			objeto.rowsCallback = function(resposta){
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
							servicoQuestionamento.remover(objeto.id).done(window.sucessoPadrao).fail(window.erro);
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
	} // ControladoraListagemQuestionamento

	// Registrando
	app.ControladoraListagemQuestionamento = ControladoraListagemQuestionamento;
})(window, app, jQuery, toastr);