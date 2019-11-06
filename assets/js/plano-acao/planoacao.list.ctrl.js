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
                console.log(data);
				var html = '';
				var dataLimite = moment(data.dataLimite);
				var diferencaDias = dataLimite.diff(moment(),'days');
				var textoDiasRestantes = (diferencaDias > 0) ? Math.abs(diferencaDias) +' dias para que o cheklist seja executado!' : Math.abs(diferencaDias) +' dias de atraso!';
					html += '<div class="col col-12 col-lg-12 col-md-12 col-sm-12">';
						html += '<div class="row agenda-dto">'

							html += '<div class="col col-12 col-lg-3 col-md-3 col-sm-4">';
								html += '<p class="dia '+((diferencaDias > 0) ? 'teal-text text-darken-1': ' red-text text-accent-4 ') +'">'+ dataLimite.format('ddd') + '</p>';
								html += '<p class="data">'+ dataLimite.format('DD/MM/YYYY')+ '</p>';
							html += '</div>';
							html += '<div class="col col-12 col-lg-9 col-md-9 col-sm-8">';
								html += '<p><i class="mdi mdi-map-marker-radius orange-text text-accent-4"></i> <strong>' + data.loja.razaoSocial + '</strong> ' + data.loja.nomeFantasia + '</p>';
								html += '<a href="#" class="executar_PlanoAcao"><p><i class="mdi mdi-clipboard-check orange-text text-accent-4"></i> <strong class="orange-text text-accent-4">' + data.titulo + '</strong></p></a>';
								// html += '<a href="#" class="inteligencia_link"><p><i class="mdi mdi-clipboard-check orange-text text-accent-4"></i> <strong class="orange-text text-accent-4">' + data.titulo + '</strong></p></a>';								
								html += '<p class="'+((diferencaDias > 0) ? 'teal-text text-darken-1': ' red-text text-accent-4 ')+'"><i class="mdi mdi-calendar-clock orange-text text-accent-4"></i> <strong>'+textoDiasRestantes+'</strong></p>';
								html += '<p><strong>Descrição : </strong> ' + data.descricao+ '</p>';
							html += '</div>';
						html += '</div>';
					html += '</div>';
					
						
							
					return html;
				};

			objeto.rowsCallback = function(resposta){
				$('.remover_setor_link').on('click', _this.remover);
				$('.executar_PlanoAcao').on('click', _this.executar);
			};

			return objeto;
		};

		_this.executar = function executar (event) {
			event.preventDefault();
			var objeto = _tabela.getObjetos()[$(this).parents('.listagem-padrao-item').index()];

			router.navigate('/executar-PlanoAcao/'+ objeto.id);
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