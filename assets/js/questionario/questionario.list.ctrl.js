/**
 *  Questionario.list.ctrl.js
 *
 *  @author	Rafael Vinicius Barros Ferreira
 */
(function(window, app, $, toastr)
{
	'use strict';

	function ControladoraListagemQuestionario(servicoQuestionario)
	{
		var _this = this;
		var _cont = 0;
		var _tabela = null;
		_this.botaoCadastrar = $('#cadastrar');
		_this.botaoEditar = $('#editar');
		_this.botaoRemover = $('#remover');
		_this.botaoAtualizar = $('#atualizar');
		_this.idTabela = $('#questionario');
		var ctrlFormulario = new app.ControladoraFormQuestionario(servicoQuestionario, _this);

		//Configura a tabela
		_this.opcoesDaTabela = function opcoesDaTabela() {
			var objeto =  new Object();
			objeto.ajax = servicoQuestionario.rota();

			objeto.carregando = true;
			objeto.pageLength = 10;
			objeto.lengthMenu =  [10, 30, 40, 100];
			objeto.searching= true;
			objeto.ordering= true;
			objeto.searching = true;
			objeto.searchDelay = 600;	
			objeto.cadastrarLink = 'cadastrar_questionario_link';
			objeto.columnDefs = function (data){
				var html = '';
				html += '<div class="col col-12 col-lg-12 col-md-12 col-sm-12 mb-0-dto">';
					html += '<div class="row mb-0-dto">';
						html += '<div class="col co-lg-8 col-md-8 col-sm-8 col-12" >'
							html += '<p class="f-12-dto"><strong>Título: </strong>'+ data.titulo + '</p>'
							html += '<p class="f-12-dto"><strong>Descrição : </strong>'+ data.descricao + '</p>'
							html += '<p class="f-12-dto"><strong>Tipo de QUestionário : </strong>'+ data.tipoQuestionario + '</p>'                
						html += '</div>';


						html += '<div class="col col-12 col-lg-12 col-md-12 col-sm-12 mb-0-dto opc_tabela">';
							html += '<p class="mb-0-dto">';
							html += '<a href="#" class="detalhes-dto visualizar_questionario">';
							html += '<i class="mdi mdi-eye-outline small orange-text text-accent-4"></i>';
							html += 'VER DETALHES';
							html += '</a>';
							html += '</p>';
						html += '</div>';
					html += '</div>';
				html += '</div>';


				return html;
			};

			objeto.rowsCallback = function(resposta){
				$('.visualizar_questionario').on('click',function(event){
					event.preventDefault();
					var objeto = _tabela.getObjetos()[$(this).parents('.listagem-padrao-item').index()];
					router.navigate('/visualizar-questionario/'+ objeto.id);
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
							servicoQuestionario.remover(objeto.id).done(window.sucessoPadrao).fail(window.erro);
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
	} // ControladoraListagemQuestionario

	// Registrando
	app.ControladoraListagemQuestionario = ControladoraListagemQuestionario;
})(window, app, jQuery, toastr);