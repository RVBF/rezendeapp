(function(window, $)
{
	'use strict';

	function ListagemPadrao(elemento, opcoes) {
		var _this = this;
		var listagemPadrao = elemento;
		var ultimaResposta = null;
		_this.paginacao = {};
		_this.objetos = null

		_this.definirEventosTabela = function definirEventosTabela(){
			listagemPadrao.find('.atualizar').on('click', _this.atualizarTabela);
			listagemPadrao.on('click', '.paginate_button',function () {
				var atual = $(this);
				var paginaAnterior = listagemPadrao.find('#listagem_paginacao').find('.pagina-atual').first();

				paginaAnterior.removeClass('pagina-atual');
				atual.addClass('pagina-atual');
				_this.atualizarTabela();
			});
		};

		_this.getObjetos = function getObjetos(){
			return _this.objetos;
		}

		_this.paginaAtual = function paginaAtual(){
			return parseInt(listagemPadrao.find('#listagem_paginacao').find('.pagina-atual').first().attr('data-dt-idx'));
		};

		_this.inicioDaPagina = function inicioDaPagina() {
			let limiteResultadosExibidos = parseInt($('#qtd_resultados').val());

			return (listagemPadrao.find('.linhas').find('.listagem-padrao-item').length == 0) ? 0 : _this.tamanhoPagina() - limiteResultadosExibidos;
		};

		_this.tamanhoPagina = function tamanhoPagina() {
			let limiteResultadosExibidos = parseInt($('#qtd_resultados').val());
			let somatorioDeTamanho = 0;
			
			if(listagemPadrao.find('.linhas').find('.listagem-padrao-item').length == 0){
				return opcoes.length;
			}else{
				for(var i =1; i <= _this.paginaAtual(); i++) {
					somatorioDeTamanho += limiteResultadosExibidos;
					
				}
				return somatorioDeTamanho;
			}
		};
		
		_this.requisitarRegistros = function requisitarRegistros() {
			var parametrosRequisicao =  function parametrosRequisicao() {
				_this.paginacao = {
					start : _this.inicioDaPagina(),
					length : parseInt($('#qtd_resultados').val())
				};

				return _this.paginacao;
			};

			return $.ajax({
				type: "GET",
				url: opcoes.ajax,
				dataType: 'json',
				data: parametrosRequisicao()
			});	
		};

		_this.renderizarRows = function renderizarRows(data){
			_this.objetos = data;
			var html = '';
			html += '<div class="card-panel left-align">';

			if(typeof opcoes.columnDefs == 'function'){
				if(data.length > 0){
					for( let indice in data) {
						html += '<div class="row listagem-padrao-item valign-wrapper">';
						html += opcoes.columnDefs(data[indice]);
						html += '</div>';
					};
				}
				else{
					html += '<p class="text-center">Nenhum resultado encontrado!</p>';
				}
			}

			html += '</div>';

			return html;
		};
		_this.renderizarRegistros = function () {
			var sucesso = function (resposta) {
				ultimaResposta = resposta;
				listagemPadrao.find('.linhas').empty().append(_this.renderizarRows(resposta.data));
				listagemPadrao.find('.info').empty().append(_this.renderizarInfo(resposta));

				if(typeof opcoes.rowsCallback == 'function') opcoes.rowsCallback(resposta);
			};

			var erro = function(resposta) {
				var mensagem = jqXHR.responseText || 'Erro ao popular select de farmácias.';
				toastr.error(mensagem);
				return false;
			};

			var  jqXHR = _this.requisitarRegistros();
			jqXHR.done(sucesso).fail(erro);	
		};

		_this.renderizarOpcoesDePesquisa  = function renderizarOpcoesDePesquisa() {
			var html = '';
			html += '<div class="row">';
				html += '<div class="col col-lg-12 col-md-12 col-12 col-sm-12">';

					html += '<div class="col col-lg-3 col-md-3 col-12 col-sm-12  sem-espacamentos">';
						html += '<div class="col col-lg-4 col-md-4 col-4 col-sm-4 adicao">';
							html += '<a href="#" class="ico-plus-dto  ' + opcoes.cadastrarLink + '">';
								html += '<i class="fas fa-plus orange-text text-accent-3"></i>';
							html += '</a>';
						html += '</div>';
						html += '<div class="col col-lg-4 col-md-4 col-4 col-sm-4 atualizar">';
							html += '<a href="#" class="ico-plus-dto ">';
								html += '<i class="fas fa-sync orange-text text-accent-3"></i>';
							html += '</a>';
						html += '</div>';

						html += '<div class="col col-lg-4 col-md-4 col-4 col-sm-4 pesquisar">';
							html += '<a href="#" class="ico-plus-dto ">';
								html += '<i class="fas fa-search prefix f-12-dto orange-text text-accent-3"></i>';
							html += '</a>';
						html += '</div>';
					html += '</div>';

					html += '<div class="col col-md-7 col-lg-7 col-sm-10 col-9 input-field">';
						html += '<input class="validate f-12-dto" type="search" name="pesquisar_itens" id="pesquisar_itens"/>';
						html += '<label for="pesquisar_itens">Pesquisar</label>';
					html += '</div>';

					html += '<div class="col col-md-2 col-lg-2 col-sm-2 col-3 sem-espacamentos">';
				
						html += ' <select name="qtd_resultados" id="qtd_resultados" class="qtd_resultados_pesquisapadrao">';

							for (var indice in opcoes.lengthMenu) {
								var selected = (indice == 0) ?  ' selected="selected" ' : '';
								html += '<option value="'+ opcoes.lengthMenu[indice] +'" '+ selected +' >' + opcoes.lengthMenu[indice] + '</option>';
							}
					
						html += '</select>';
					html += '</div>';
				html += '</div>';
			html += '</div>';


			listagemPadrao.prepend(html);
		};

		_this.renderizarInfo = function renderizarInfo (data) {
			let inicio = (_this.paginacao.start + 1) , tamanhoPagina = (_this.paginacao.length == undefined) ? parseInt($('#qtd_resultados').val()) : _this.paginacao.length;
			if(_this.objetos.length == 0){
				tamanhoPagina = 0;
				inicio --;
			}

			if(tamanhoPagina > data.recordsTotal) tamanhoPagina = data.recordsTotal;
			var html = '';
			html += '<div class="row">';
				html += '<div class="col col-12 col-sm-12 col-md-6 col-lg-6 informacao_exibicao">';
				html += '<div class="informacoes_listagem" id="informacoes_listagem" role="status" aria-live="polite">Mostrando ' + inicio + ' até ' + tamanhoPagina + ' de ' + data.recordsTotal + ' registros.</div>';
				html += '</div>'

				html += '<div class="col col-12 col-sm-12 col-md-6 col-lg-6 paginacao">';
				html += '<div class="paginacao_listagem " id="listagem_paginacao">';
				html += _this.renderizarBotoes(data);
				html += '</div>';
			html += '</div>';


			return html;
		};

		_this.renderizarTabela  = function  renderizarTabela() {
			_this.renderizarOpcoesDePesquisa();
			_this.renderizarRegistros();
		};

		_this.renderizarBotoes = function renderizarBotoes(data){
			let resultadosPorPagina = listagemPadrao.find('#qtd_resultados').val();
			let quantidadeBotoes =  Math.ceil(data.recordsTotal/resultadosPorPagina);
			let html = '';
			html += '<a class="paginacao-anterior disabled paginate_button" data-dt-idx="0" tabindex="0" id="anterior">';
			html += '<font style="vertical-align: inherit;">';
			html += '<font style="vertical-align: inherit;">Anterior </font>';
			html += '</font>';
			html += '</a>';
			html += '<span>';
			var i;

			for(i = 1; i<= quantidadeBotoes; i++){
				let classes = (i== 1) ? 'pagina-atual paginate_button' : ' paginate_button';

				html += '<a class="' + classes + '" data-dt-idx="' + i  + '" tabindex="0">';
				html += '	<font style="vertical-align: inherit;">';
				html += '	<font style="vertical-align: inherit;">' + i + '</font>';
				html += '</font>';
				html += '</a>';
			}

			html += '<a class="paginacao-proximo paginate_button" data-dt-idx="' + i  +'" tabindex="0" id="proximo">';
			html += '<font style="vertical-align: inherit;">';
			html += '<font style="vertical-align: inherit;"> Próximo</font>';
			html += '</font>';
			html += '</a>';
			html += '</span>';

			return html
		};

		_this.atualizarTabela = function atualizarTabela(event){
			if(event != undefined) event.preventDefault();
			var sucesso = function (resposta) {
				ultimaResposta = resposta;
				listagemPadrao.find('.linhas').empty().append(_this.renderizarRows(resposta.data));
				listagemPadrao.find('.info').empty().append(_this.renderizarInfo(resposta));

				if(typeof opcoes.rowsCallback == 'function') opcoes.rowsCallback(resposta);
			};

			var erro = function(resposta) {
				var mensagem = jqXHR.responseText || 'Erro ao popular select de farmácias.';
				toastr.error(mensagem);
				return false;
			};

			var  jqXHR = _this.requisitarRegistros();
			jqXHR.done(sucesso).fail(erro);	
		};

		_this.configurar = function configurar() {
			_this.renderizarTabela();
			_this.definirEventosTabela();
		};

	}; // ListagemPadrao	

	// Registrando
	window.listagemPadrao = ListagemPadrao;

	$.fn.extend({
		listar: function (opcoes) {
			var listagemPadrao = new ListagemPadrao($(this[0]), opcoes);
			$(this[0]).data('instanciaTabela', listagemPadrao);
			listagemPadrao.configurar();

			return listagemPadrao;
		}
	});
})(window, jQuery);