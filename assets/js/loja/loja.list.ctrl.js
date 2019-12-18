/**
 *  loja.list.ctrl.js
 *
 *  @author	Rafael Vinicius Barros Ferreira
 */
(function(window, app, $, toastr, BootstrapDialog)
{
	'use strict';

	function ControladoraListagemLoja(servicoLoja) {
		var _this = this;
		var _cont = 0;
		var _tabela = null;
		_this.botaoCadastrar = $('#cadastrar');
		_this.botaoEditar = $('#editar');
		_this.botaoRemover = $('#remover');
		_this.botaoAtualizar = $('#atualizar');
		_this.idTabela = $('#loja');
		var ctrlFormulario = new app.ControladoraFormLoja(servicoLoja);

		//Configura a tabela
		_this.opcoesDaTabela = function opcoesDaTabela() {
			var objeto =  new Object();
			objeto.ajax = servicoLoja.rota();

			objeto.carregando = true;
			objeto.pageLength = 10;
			objeto.lengthMenu =  [10, 30, 40, 100];
			objeto.searching= true;
			objeto.ordering= true;
			objeto.searching = true;
			objeto.searchDelay = 600;	
			objeto.cadastrarLink = 'cadastrar_loja_link';
			objeto.columnDefs = function (data){
				var html = '';
				html += '<div class="col col-12 col-lg-12 col-md-12 col-sm-12 mb-0-dto">';
					html += '<div class="row mb-0-dto">';
							html += '<div class="col co-lg-10 col-md-10 col-sm-10 col-8">';
								html += '<p class="f-12-dto"><strong>Razão Social : </strong>'+ data.razaoSocial + '</p>';
								html += '<p class="f-12-dto"><strong>Nome Fantasia : </strong>'+ data.nomeFantasia + '</p>';
							html += '</div>';

							html += '<div class="col col-12 col-lg-12 col-md-12 col-sm-12 mb-0-dto opc_tabela">';
								html += '<p class="mb-0-dto">';
								html += '<a href="#" class="detalhes-dto visualizar_loja">';
								html += '<i class="mdi mdi-eye-outline small orange-text text-accent-4"></i>';
								html += 'VER DETALHES';
								html += '</a>';
								html += '</p>';
							html += '</div>';
					html += '</div>';
				html += '</div>';
				

				return html;
			};

			objeto.rowsCallback = function (resposta) {
				$('.visualizar_loja').on('click',function(event){
					event.preventDefault();
					var objeto = _tabela.getObjetos()[$(this).parents('.listagem-padrao-item').index()];
					router.navigate('/visualizar-loja/'+ objeto.id);
				});
			}

			return objeto;
		};


		_this.atualizar = function atualizar(){
			_tabela.atualizarTabela();
		};

		_this.configurar = function configurar() {
			_tabela = _this.idTabela.listar(_this.opcoesDaTabela());
		};
	} // ControladoraListagemLoja

	// Registrando
	app.ControladoraListagemLoja = ControladoraListagemLoja;
})(window, app, jQuery, toastr, BootstrapDialog);

