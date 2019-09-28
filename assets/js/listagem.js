(function(window, $)
{
	'use strict';

	function ListagemPadrao(elemento, opcoes) {
		var _this = this;
		var listagemPadrao = elemento;

		_this.requisitarRegistros = function requisitarRegistros() {
			return $.ajax({
				type: "GET",
				url: opcoes.ajax
			});	
		};

		_this.renderizarRows = function renderizarRows(resposta){
			var html = '';
			html += '<div class="card-panel left-align">';
			if(typeof opcoes.columnDefs == 'function'){
				for( let indice in resposta) {
					html += '<div class="row listagem-padrao-item valign-wrapper">';
					html += opcoes.columnDefs(resposta[indice]);
					html += '</div>';
				};
			}

			html += '</div>';

			return html;
		};

		_this.renderizarRegistros = function () {
			var sucesso = function (resposta) {
				listagemPadrao.find('.linhas').append(_this.renderizarRows(resposta.data));
				listagemPadrao.find('.info').append(_this.renderizarInfo(resposta));
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
			html += '<div class="sem-espacamentos">';
            html += '<a href="#" class="ico-plus-dto adicao ' + opcoes.cadastrarLink + '">';
            html += '<i class="fas fa-plus orange-text text-accent-3 small"></i>';
            html += '</a>';
        	html += '</div>';
			html += '<div class="col col-md-8 col-lg-10 col-sm-8  col-6 input-field">';
			html += '<i class="fas fa-search prefix f-12-dto orange-text text-accent-3 small"></i>';
			html += '<input class="validate f-12-dto" type="search" name="pesquisar_itens" id="pesquisar_itens"/>';
			html += '<label for="pesquisar_itens">Pesquisar</label>';
			html += '</div>';
			html += '<div class="col col-md-2 col-lg-1 col-sm-2  col-3">';
			html += ' <select name="qtd_resultados" id="qtd_resultados" class="qtd_resultados_pesquisapadrao">';

			for (var indice in opcoes.lengthMenu) {
				var selected = (indice == 0) ?  ' selected="selected" ' : '';
				html += '<option value="'+ opcoes.lengthMenu[indice] +'" '+ selected +' >' + opcoes.lengthMenu[indice] + '</option>';
			}
	
			html += '</select>';
			html += '</div>';
			html += '</div>';

			listagemPadrao.prepend(html);
		};

		_this.renderizarInfo = function renderizarInfo (data) {
			var html = '';
			html += '<div class="col col-12 col-sm-6 col-md-6 col-lg-6 informacao_exibicao">';
			html += '<div class="informacoes_listagem" id="informacoes_listagem" role="status" aria-live="polite">Mostrando ' + data.draw + ' até ' + data.recordsFiltered + ' de ' + data.recordsTotal + ' registros.</div>';
			html += '</div>'

			html += '<div class="col-12 col-sm-6 col-md-6 col-lg-6 paginacao d-flex justify-content-end">';
			html += '<div class="paginacao_listagem " id="listagem_paginacao">';
			html += '<a class="paginate_button previous disabled" aria-controls="example" data-dt-idx="0" tabindex="0" id="example_previous"><font style="vertical-align: inherit;"><font style="vertical-align: inherit;">Anterior </font></font></a><span><a class="paginate_button current" aria-controls="example" data-dt-idx="1" tabindex="0"><font style="vertical-align: inherit;"><font style="vertical-align: inherit;">1 </font></font></a><a class="paginate_button " aria-controls="example" data-dt-idx="2" tabindex="0"><font style="vertical-align: inherit;"><font style="vertical-align: inherit;">2 </font></font></a><a class="paginate_button " aria-controls="example" data-dt-idx="3" tabindex="0"><font style="vertical-align: inherit;"><font style="vertical-align: inherit;">3 </font></font></a><a class="paginate_button " aria-controls="example" data-dt-idx="4" tabindex="0"><font style="vertical-align: inherit;"><font style="vertical-align: inherit;">4 </font></font></a><a class="paginate_button " aria-controls="example" data-dt-idx="5" tabindex="0"><font style="vertical-align: inherit;"><font style="vertical-align: inherit;">5 </font></font></a><a class="paginate_button " aria-controls="example" data-dt-idx="6" tabindex="0"><font style="vertical-align: inherit;"><font style="vertical-align: inherit;">6</font></font></a></span><a class="paginate_button next" aria-controls="example" data-dt-idx="7" tabindex="0" id="example_next"><font style="vertical-align: inherit;"><font style="vertical-align: inherit;"> Próximo</font></font></a></div>';
			html += '</div>';

			return html;
		};

		_this.renderizarTabela  = function  renderizarTabela() {
			_this.renderizarOpcoesDePesquisa();
			_this.renderizarRegistros();
		};

		_this.renderizarBotoes = function renderizarBotoes(data){
			
		};

		_this.atualizarTabelainfo = function atualizarInfo(data) {
			listagemPadrao.find('.informacoes_listagem').empty().html('Mostrando ' + data.draw + ' até ' + data.recordsFiltered + ' de ' + data.recordsTotal + ' registros');
			
			listagemPadrao.find('paginacao_listagem').empty().html(_this.renderizarBotoes(data));
		};

		_this.atualizarTabela = function atualizarTabela(){
			var sucesso = function (resposta) {
				listagemPadrao.find('.linhas').append(_this.renderizarRows(resposta.data));
				listagemPadrao.find('.info').append(_this.renderizarInfo(resposta));
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
		};

	} // ListagemPadrao	

	// Registrando
	window.listagemPadrao = ListagemPadrao;

	$.fn.extend({
		listar: function (opcoes) {
			var listagemPadrao = new ListagemPadrao($(this[0]), opcoes);
			listagemPadrao.configurar();
		}
	});
})(window, jQuery);