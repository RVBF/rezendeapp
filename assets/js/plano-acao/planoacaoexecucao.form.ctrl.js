/**
 *  questionamento.form.ctrl.js
 *
 *  @author  Rafael Vinicius Barros Ferreira
 *	 @version 1.0
 */
(function(window, app, $, toastr) {
	'use strict';

	function ControladoraFormPlanoAcaoExecucao(servicoPlanoAcao) {
		var _this = this;

		_this.alterar;
		_this.formulario = $('#planoacaoexecucao_form');
		_this.idPlanoAcao = window.location.href.split('#')[1].substring(1, url.length).split('/')[1];
		_this.objeto = null
		_this.anexos = [];


		// Cria as opções de validação do formulário
		var criarOpcoesValidacao = function criarOpcoesValidacao() {
			var opcoes = {
			};

			// Irá disparar quando a validação passar, após chamar o método validate().
			opcoes.submitHandler = function submitHandler(form) {
				var obj = _this.conteudo();
				console.log(obj);
				var terminado = function() {
					_this.formulario.desabilitar(false);
				};

				_this.formulario.desabilitar(true);

				var jqXHR = servicoPlanoAcao.executar(obj);
			
				jqXHR.done(function(resposta) {
					if(resposta.status){
						router.navigate('/plano-acao');
						toastr.success(resposta.mensagem);
					}
					else{
						_this.formulario.find('#msg').empty().removeClass('d-none').append(resposta.mensagem);
						toastr.success(resposta.error);
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
			return servicoPlanoAcao.criar(
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

			_this.formulario.find('input[type="file"]').change(function(evt){
				var elemento = $(this);
				var file = evt.target.files[0];
				var nomeArquivo = $(this).val().split('\\');
				nomeArquivo = nomeArquivo[nomeArquivo.length -1];
				var reader = new FileReader();
				reader.onerror = function (evt) {
					switch(evt.target.error.code) {
						case evt.target.error.NOT_FOUND_ERR:
						  alert('File Not Found!');
						  break;
						case evt.target.error.NOT_READABLE_ERR:
						  alert('File is not readable');
						  break;
						case evt.target.error.ABORT_ERR:
						  break; // noop
						default:
						  alert('An error occurred reading this file.');
					  };
				};
				reader.onprogress =  function updateProgress(evt) {
					var progress = document.querySelector('.percent');

					// evt is an ProgressEvent.
					if (evt.lengthComputable) {
					  var percentLoaded = Math.round((evt.loaded / evt.total) * 100);
					  // Increase the progress bar length.
					  if (percentLoaded < 100) {
						progress.style.width = percentLoaded + '%';
						progress.textContent = percentLoaded + '%';
					  }
					}
				};
				reader.onabort = function(e) {
					alert('File read cancelled');
				};

				reader.onload = function () {
					_this.anexos.push({'nome': nomeArquivo,'arquivo': reader.result, 'tipo' : file.type});
				};
				
				reader.readAsDataURL(file);
			});


			_this.formulario.find('i').on('click', function (event) {
				$(this).next("input[type='file']").trigger('click');
			});

			_this.formulario.find('#salvar').on('click',_this.salvar);
		};

		_this.buscarQuestionamento  =  function buscarQuestionamento(valor = 0)	{
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
			var jqXHR = servicoPlanoAcao.comId(_this.idPlanoAcao);
			jqXHR.done(sucesso).fail(erro);
		};

		_this.desenhar = function desenhar(obj) {
			var html = '';
			var dataLimite = moment(obj.dataLimite);

			html += '<div class="row">';
			html += '<div class="col-12  col-sm-12 col-md-12 col-lg-12">';
			html += '<p class="text-danger text-uppercase font-weight-bold"><strong>Tarefa</strong</p>';
			html += '<p>'+obj.descricao+'</p>';
			html += '<p class="text-danger text-uppercase font-weight-bold"><strong>Plano de ação</strong</p>';
			html += '<p>'+obj.solucao+'</p>';
			html += '<p class="text-danger text-uppercase font-weight-bold"><strong>Data Limite</strong</p>';
			html += '<p>'+dataLimite.format('DD/MM/YYYY')+'</p>'
			html += '</div>';
			html += '</div>';

			$('body #detalhes_pa').append(html);
			_this.formulario.find('#id').val(obj.id)


		};

        _this.definirForm = function definirForm() {
			_this.formulario.submit(false);
			_this.buscarQuestionamento();
			_this.formulario.find('#destalhes_exeucao').focus();
			_this.configurarEventos();
        }

		_this.salvar = function salvar() {
			console.log(':)');
			_this.formulario.validate(criarOpcoesValidacao());
		};
		
		// Configura os eventos do formulário
		_this.configurar = function configurar() {
			_this.definirForm();
		};
	}; // ControladoraFormPlanocaoExecucao

	// Registrando
	app.ControladoraFormPlanoAcaoExecucao = ControladoraFormPlanoAcaoExecucao;

})(window, app, jQuery, toastr);