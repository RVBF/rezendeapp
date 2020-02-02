/**
 *  questionamento.form.ctrl.js
 *
 *  @author  Rafael Vinicius Barros Ferreira
 *	 @version 1.0
 */
(function(window, app, $, toastr) {
	'use strict';

	function ControladoraFormPendenciaExecucao(servicoPendencia) {
		var _this = this;

		_this.alterar;
		_this.formulario = $('#pendenciaexecucao_form');
		_this.idPendencia = window.location.href.split('#')[1].substring(1, url.length).split('/')[1];
		_this.objeto = null
		_this.anexos = [];


		// Cria as opções de validação do formulário
		var criarOpcoesValidacao = function criarOpcoesValidacao() {
			var opcoes = {
			};

			// Irá disparar quando a validação passar, após chamar o método validate().
			opcoes.submitHandler = function submitHandler(form) {
				var obj = _this.conteudo();
				var terminado = function() {
					_this.formulario.desabilitar(false);
				};

				_this.formulario.desabilitar(true);

				var jqXHR = servicoPendencia.executar(obj.id);

				jqXHR.done(function(resposta) {
					if(resposta.status){
						router.navigate('/pendencia');
						toastr.success(resposta.mensagem);
					}
					else{
						_this.formulario.find('#msg').empty().removeClass('d-none').append(resposta.mensagem);
						if(resposta != undefined && resposta.mensagem) toastr.error(resposta.mensagem);
					}
				}).always(terminado).fail(window.erro);

				if(_this.alterar){
					$('.depende_selecao').each(function(){
						$(this).prop('disabled', true);
					});
				}
			}; // submitHandler

			return opcoes;
		};

		// Obtém o conteúdo atual do form como um objeto
		_this.conteudo = function conteudo() {
			return servicoPendencia.criar(
				_this.objeto.id,
				_this.objeto.descricao,
				_this.objeto.dataLimite,
				_this.objeto.solucao,
				_this.objeto.responsavel,
				_this.objeto.loja,
				$('#destalhes_execucao').val(),
				_this.anexos,
				_this.objeto.dataCadastro,
				_this.objeto.dataExecucao
			);
		};

		_this.configurarEventos = function configurarEventos() {
			_this.formulario.find('#salvar').on('click',_this.salvar);
		};

		_this.buscarPendencia  =  function buscarPendencia(valor = 0)	{
			var sucesso = function (resposta) {
				_this.objeto = resposta.conteudo;
				_this.desenhar(_this.objeto);
			};

			var erro = function(resposta)
			{
				var mensagem = jqXHR.responseText || 'Erro ao popular select de farmácias.';
				toastr.error(mensagem);
				return false;
			}
			var jqXHR = servicoPendencia.comId(_this.idPendencia);
			jqXHR.done(sucesso).fail(erro);
		};

		_this.desenhar = function desenhar(obj) {
			var html = '';
			var dataLimite = moment(obj.dataLimite);

			html += '<div class="row">';
			html += '<div class="col-12  col-sm-12 col-md-12 col-lg-12">';
			html += '<p class="text-danger text-uppercase font-weight-bold"><strong>Pendência</strong</p>';
			html += '<p>'+obj.descricao+'</p>';
			html += '<p class="text-danger text-uppercase font-weight-bold"><strong>Descrição da solução</strong</p>';
			html += '<p>'+obj.solucao+'</p>';
			html += '<p class="text-danger text-uppercase font-weight-bold"><strong>Data Limite</strong</p>';
			html += '<p>'+dataLimite.format('DD/MM/YYYY')+'</p>'
			html += '</div>';
			html += '</div>';

			$('body #detalhes_pe').append(html);
			_this.formulario.find('#id').val(obj.id)
		};

        _this.definirForm = function definirForm() {
			_this.formulario.submit(false);
			_this.buscarPendencia();
			_this.configurarEventos();
        }

		_this.salvar = function salvar() {
			_this.formulario.validate(criarOpcoesValidacao());
		};

		// Configura os eventos do formulário
		_this.configurar = function configurar() {
			_this.definirForm();
		};
	}; // ControladoraFormPlanocaoExecucao

	// Registrando
	app.ControladoraFormPendenciaExecucao = ControladoraFormPendenciaExecucao;

})(window, app, jQuery, toastr);