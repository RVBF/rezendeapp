/**
 *  colaborador.list.ctrl.js
 *
 *  @author	Rafael Vinicius Barros Ferreira
 */
(function(window, app, $, toastr)
{
	'use strict';

	function ControladoraListagemColaborador(servicoColaborador)
	{
		var _this = this;
		var _cont = 0;
		var _tabela = null;
		_this.botaoCadastrar = $('#cadastrar');
		_this.botaoEditar = $('#editar');
		_this.botaoRemover = $('#remover');
		_this.botaoAtualizar = $('#atualizar');
		_this.idTabela = $('#listagem_colaboradores');
		var ctrlFormulario = new app.ControladoraFormUsuario(servicoColaborador);

		//Configura a tabela
		_this.opcoesDaTabela = function opcoesDaTabela() {
			var objeto =  new Object();
			objeto.ajax = servicoColaborador.rota();

			objeto.carregando = true;
			objeto.pageLength = 20;
			objeto.lengthMenu =  [20, 30, 40, 100];
			objeto.searching= true;
			objeto.ordering= true;
			objeto.searching = true;
			objeto.searchDelay = 600;	
			objeto.order = 'DESC';
			objeto.cadastrarLink = 'cadastrar_colaborador_link';
			objeto.columnDefs = function (data){
				let imagem = (data.avatar != null) ? data.avatar.arquivoBase64 : '/assets/images/avatar-padrao.png';
				var html = '';
				html += '<div class="col col-12 col-lg-12 col-md-12 col-sm-12 mb-0-dto">';
					html += '<div class="row mb-0-dto">';

							html += '<div class="col co-lg-2 col-md-2 col-sm-2 col-4 ">';
								html += '<img src="'+imagem+'" class="avatar"></img>';
							html += '</div>';
							
							html += '<div class="col co-lg-10 col-md-10 col-sm-10 col-8">'
								html += '<p class="f-12-dto"><strong>Nome : </strong>'+ data.nome + ' ' + data.sobrenome + '</p>'
								html += '<p class="f-12-dto"><strong>Email : </strong>'+ data.email + '</p>'
								
								html += '<p class="f-12-dto"><strong>Setor : </strong>'+ data.setor.titulo + '</p>';
								html += '<p class="f-12-dto"> <strong>Loja</strong>  Loja Conselheiro - Nova Friburgo</p>';
								html += '<p class="f-12-dto"> <strong>Usuário: </strong>  '+ data.usuario.login +  '</p>';
							html += '</div>';

							html += '<div class="col col-12 col-lg-12 col-md-12 col-sm-12 mb-0-dto opc_tabela">';
								html += '<p class="mb-0-dto">';
								html += '<a href="#" class="detalhes-dto visualizar_checklist">';
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
				$('.visualizar_checklist').on('click',function(event){
					event.preventDefault();
					var objeto = _tabela.getObjetos()[$(this).parents('.listagem-padrao-item').index()];
					router.navigate('/visualizar-colaborador/'+ objeto.id);
				});
			}
			return objeto;
		};

		_this.atualizar = function atualizar(){
 			_tabela.ajax.reload();
		};

		_this.configurar = function configurar() {
			_tabela = _this.idTabela.listar(_this.opcoesDaTabela());
			_this.botaoCadastrar.on('click',_this.cadastrar);
			_this.botaoEditar.on('click', _this.editar)
			_this.botaoAtualizar.on('click',_this.atualizar);
			_this.botaoRemover.on('click', _this.remover);;
		};
	} // ControladoraListagemColaborador

	// Registrando
	app.ControladoraListagemColaborador = ControladoraListagemColaborador;
})(window, app, jQuery, toastr);