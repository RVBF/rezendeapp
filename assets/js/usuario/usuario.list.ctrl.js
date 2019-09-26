/**
 *  usuario.list.ctrl.js
 *
 *  @author	Rafael Vinicius Barros Ferreira
 */
(function(window, app, $, toastr)
{
	'use strict';

	function ControladoraListagemUsuario(servicoUsuario)
	{
		var _this = this;
		var _cont = 0;
		var _tabela = null;
		_this.botaoCadastrar = $('#cadastrar');
		_this.botaoEditar = $('#editar');
		_this.botaoRemover = $('#remover');
		_this.botaoAtualizar = $('#atualizar');
		_this.idTabela = $('#listagem_colaboradores');
		var ctrlFormulario = new app.ControladoraFormUsuario(servicoUsuario);

		//Configura a tabela
		_this.opcoesDaTabela = function opcoesDaTabela() {
			var objeto =  new Object();
			objeto.ajax = servicoUsuario.rota();

			objeto.carregando = true;
			objeto.pageLength = 20;
			objeto.lengthMenu =  [20, 30, 40, 100];
			objeto.searching= true;
			objeto.ordering= true;
			objeto.searching = true;
			objeto.searchDelay = 600;	
			objeto.order = 'DESC';
			objeto.columnDefs = function (data){
				var html = '';
				html += '<div class="col co-lg-2 col-md-2 col-sm-2 col-4">';
				html += '<img src="https://acotelha.com.br/wp-content/uploads/2018/02/AVATAR-PARA-O-SITE-02.png" class="demo-avatar"></img>';
				html += '</div>';
				
				html += '<div class="col co-lg-6 col-md-6 col-sm-6 col-8">'
				html += '<p class="f-12-dto"><strong>Nome : </strong>'+ data.colaborador.nome + ' ' + data.colaborador.sobrenome + '</p>'
				html += '<p class="f-12-dto"><strong>Email : </strong>'+ data.colaborador.email + '</p>'
				
				html += '<p class="f-12-dto"><strong>Setor : </strong>TI</p>';
				html += '<p class="f-12-dto"> <strong>Loja</strong>  Loja Conselheiro - Nova Friburgo</p>';
				html += '<p class="f-12-dto"> <strong>Usuário: </strong>  '+ data.login +  '</p>';
				html += '</div>';

				html += '<div class="col co-lg-4 col-md-4 col-sm-4 col-12 opcoes">';
				html += '<p class="f-12-dto"><i class="mdi mdi-table-edit"></i> Editar</p>';
				html += '<p class="f-12-dto"><i class="mdi mdi-loupe"></i> Visualizar</p>';
				html += '<p class="f-12-dto"><i class="mdi mdi-delete"> </i> Excluir</p>';
				html += '</div>';

				return html;
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

			contexto.promise().done(function () {
				ctrlFormulario.configurar(modoEdicao);
			});		
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
			
			contexto.promise().done(function () {
				ctrlFormulario.configurar(modoEdicao);
				ctrlFormulario.desenhar(objeto);			
			});
		};

		_this.atualizar = function atualizar(){
 			_tabela.ajax.reload();
		};

		_this.remover = function remover(event){
			var objeto = _tabela.row('.selected').data();

			BootstrapDialog.show({
				type	: BootstrapDialog.TYPE_DANGER,
				title	: 'Deseja remover esta Grupo?',
				message	: 'ID : ' + objeto.id + ', Login: '+ objeto.login + '.',
				size	: BootstrapDialog.SIZE_LARGE,
				buttons	: [ {
						label	: '<u>S</u>im',
						hotkey	: 'S'.charCodeAt(0),
						action	: function(dialog){
							servicoUsuario.remover(objeto.id).done(window.sucessoPadrao).fail(window.erro);
							_this.atualizar();
							$('.depende_selecao').each(function(){
								$(this).prop('disabled', true);
							});

							var contexto = $('#painel_formulario');
							contexto.addClass('desabilitado');
				
							contexto.addClass('d-none');
							contexto.desabilitar(true);
							contexto.find('form')[0].reset();
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
			_this.botaoCadastrar.on('click',_this.cadastrar);
			_this.botaoEditar.on('click', _this.editar)
			_this.botaoAtualizar.on('click',_this.atualizar);
			_this.botaoRemover.on('click', _this.remover);;
		};
	} // ControladoraListagemUsuario

	// Registrando
	app.ControladoraListagemUsuario = ControladoraListagemUsuario;
})(window, app, jQuery, toastr);