<?php
use phputil\datatables\DataTablesRequest;
use phputil\datatables\DataTablesResponse;
use Symfony\Component\Validator\Validation as Validacao;
use \phputil\JSON;
use \phputil\RTTI;
use Illuminate\Database\Capsule\Manager as DB;

/**
 * Controladora de Usuario
 *
 * @author	Rafael Vinicius Barros Ferreira
 * @version	1.0
 */
class ControladoraUsuario {

	private $params;
	private $colecaoUsuario;
	private $colecaoLoja;
	private $servicologin;
	private $colecaoColaborador;
	private $colecaoSetor;
	private $colecaoGrupoDeUsuario;

	function __construct($params, Sessao $sessao) {
		$this->params = $params;
		$this->colecaoUsuario = Dice::instance()->create('ColecaoUsuario');
		$this->colecaoGrupoDeUsuario = Dice::instance()->create('ColecaoGrupoUsuario');
		$this->colecaoLoja = Dice::instance()->create('ColecaoLoja');
		$this->colecaoColaborador = Dice::instance()->create('ColecaoColaborador');
		$this->colecaoSetor = Dice::instance()->create('ColecaoSetor');
		$this->servicoLogin = new ServicoLogin($sessao);
		$this->sessao = $sessao;

	}

	function todos() {
		try {
			if($this->servicoLogin->verificarSeUsuarioEstaLogado() == false) {
				throw new Exception("Erro ao acessar página.");				
			}

			// if(!$this->servicoLogin->eAdministrador()){
			// 	throw new Exception("Usuário sem permissão para executar ação.");
			// }

			$dtr = new DataTablesRequest($this->params);
			$contagem = 0;
			$objetos = [];
			$erro = null;	

			$objetos = $this->colecaoUsuario->todos($dtr->start, $dtr->length, (isset($dtr->search->value)) ? $dtr->search->value : '');
			
			foreach ($objetos as $key => $obj) {
				$colaborador = $this->colecaoColaborador->comUsuarioId($obj['id']);

				if(!isset($colaborador) and !($colaborador instanceof Colaborador)){
					throw new Exception("Colaborador não encontrada na base de dados.");
				}

				$objetos[$key] = $colaborador;
			}

			
			$contagem = $this->colecaoUsuario->contagem();
		}
		catch (\Exception $e )
		{
			throw new Exception("Erro ao listar lojas.");
		}
		$conteudo = new DataTablesResponse(
			$contagem,
			count($objetos), //count($objetos ),
			$objetos,
			$dtr->draw,
			$erro
		);

		return  RTTI::getAttributes($conteudo, RTTI::allFlags());
    }
    
    function adicionar() {
		DB::beginTransaction();

		try {
			if($this->servicoLogin->verificarSeUsuarioEstaLogado() == false) {
				throw new Exception("Erro ao acessar página.");				
			}

			// if(!$this->servicoLogin->eAdministrador()){
			// 	throw new Exception("Usuário sem permissão para executar ação.");
			// }
			
			$inexistentes = \ArrayUtil::nonExistingKeys(['id', 'nome', 'sobrenome', 'email', 'login','senha', 'lojas', 'setor'], $this->params);
			if(is_array($inexistentes) ? count($inexistentes) > 0 : 0) {
				$msg = 'Os seguintes campos obrigatórios não foram enviados: ' . implode(', ', $inexistentes);

				throw new Exception($msg);
			}			
			
			$setor = new Setor(); $setor->fromArray($this->colecaoSetor->comId($this->params['setor']));
	
			if(!isset($setor) and !($setor instanceof Setor)){
				throw new Exception("Setor não encontrada na base de dados.");
			}

			$lojas = $this->colecaoLoja->todosComIds($this->params['lojas']);

			if(!isset($lojas) and !($lojas instanceof Loja)){
				throw new Exception("Loja não encontrada na base de dados.");
			}

			$usuario = new Usuario( 
				0, 
				\ParamUtil::value($this->params, 'login'), 
				\ParamUtil::value($this->params, 'senha')
			);


			$this->colecaoUsuario->adicionar($usuario);

			$avatar;
			if(isset($this->params['avatar'])){
				Util::printr($this->params['avatar']);
				$pastaTarefa = 'colaborador_'. $questionamento->getId();
				$patch = $this->servicoArquivo->validarESalvarImagem($this->params['avatar'], $pastaTarefa, 'colaborador_' . $questionamento->getId());


				foreach($this->params['anexos'] as $arquivo) {
					$anexo = new Anexo(
						0,
						$patch,
						$arquivo['tipo'],
						$questionamento
					);

					$this->colecaoAnexo->adicionar($anexo);
				}
			}
			else{
				throw new Exception("Para respostas inferior a bom é necessário adicionar ao menos 1 anexo para comprovação do problema!");
			}

			$colaborador = new Colaborador(
				0, 
				\ParamUtil::value($this->params, 'nome'), 
				\ParamUtil::value($this->params, 'sobrenome'), 
				\ParamUtil::value($this->params, 'email'), 
				$usuario,
				$setor,
				$lojas
			);


			$this->colecaoColaborador->adicionar($colaborador);

			DB::commit();

			$resposta = ['status' => true, 'mensagem'=> 'Usuário cadastrado com sucesso.']; 
		}
		catch (\Exception $e) {
			DB::rollback();
			$resposta = ['status' => false, 'mensagem'=> $e->getMessage()]; 
		}

		return $resposta;
	}

	function atualizar(){
		DB::beginTransaction();

		try {
			if($this->servicoLogin->verificarSeUsuarioEstaLogado() == false) {
				throw new Exception("Erro ao acessar página.");				
			}

			$inexistentes = \ArrayUtil::nonExistingKeys(['id', 'nome', 'sobrenome', 'email', 'login'], $this->params);

			if(is_array($inexistentes) ? count($inexistentes) > 0 : 0) {
				$msg = 'Os seguintes campos obrigatórios não foram enviados: ' . implode(', ', $inexistentes);

				throw new Exception($msg);
			}		
			$lojas = null;

			if(isset($this->params['lojas'])) $lojas = $this->colecaoLoja->todosComIds($this->params['lojas']);

			if(!isset($lojas) and !($lojas instanceof Loja) and count($lojas)){
				throw new Exception("Loja não encontrada na base de dados.");
			}

			$hash = HashSenha::instance();
			$usuario = new Usuario( 
				\ParamUtil::value($this->params, 'id'), 
				\ParamUtil::value($this->params, 'login')
			);

			$usuario->setColaborador($this->colecaoColaborador->comUsuarioId($usuario->getId()));

			$this->colecaoUsuario->atualizar($usuario);

			$colaborador = $usuario->getColaborador();

			$colaborador->setNome(\ParamUtil::value($this->params, 'nome'));
			$colaborador->setSobrenome(\ParamUtil::value($this->params, 'sobrenome'));
			$colaborador->setEmail(\ParamUtil::value($this->params, 'email'));
			$colaborador->setLojas($lojas);

			$this->colecaoColaborador->atualizar($colaborador);

			DB::commit();

			$resposta = ['status' => true, 'mensagem'=> 'Usuário cadastrado com sucesso.']; 
		}
		catch (\Exception $e) {
			DB::rollback();
			$resposta = ['status' => false, 'mensagem'=> $e->getMessage()]; 
		}

		return $resposta;
	}

	function remover($id) {
		DB::beginTransaction();

		try {
			if($this->servicoLogin->verificarSeUsuarioEstaLogado() == false) throw new Exception("Erro ao acessar página.");				
			
			if(!$this->servicoLogin->eAdministrador()) throw new Exception("Usuário sem permissão para executar ação.");

			if (! is_numeric($id)) return $this->geradoraResposta->erro('O id informado não é numérico.', GeradoraResposta::TIPO_TEXTO);
			
			if(!$this->colecaoUsuario->remover($id)) throw new Exception("Erro ao remover usuário.");
			
			DB::commit();

			$resposta = ['status' => true, 'mensagem'=> 'Usuário removido com sucesso.']; 
		}
		catch (\Exception $e) {
			DB::rollback();
			$resposta = ['status' => false, 'mensagem'=>  $e->getMessage()]; 
		}

		return $resposta;
	}

	function comId($id) {
		try {
			if($this->servicoLogin->verificarSeUsuarioEstaLogado() == false) throw new Exception("Erro ao acessar página.");				
			
			if (! is_numeric($id)) return $this->geradoraResposta->erro('O id informado não é numérico.', GeradoraResposta::TIPO_TEXTO);

			$usuario = new Usuario(); $usuario->fromArray($this->colecaoUsuario->comId($id));
			
			$usuario->setGruposUsuarios($this->colecaoGrupoDeUsuario->comUsuarioId($usuario->getId()));

			$resposta = ['conteudo'=> $usuario->toArray(), 'status' => true, 'mensagem'=> 'Usuário removido com sucesso.']; 
		}
		catch (\Exception $e) {
			DB::rollback();
			$resposta = ['status' => false, 'mensagem'=>  $e->getMessage()]; 
		}

		return $resposta;
	}

	function atualizarSenha() {
		DB::beginTransaction();
		try {
			if($this->servicoLogin->verificarSeUsuarioEstaLogado() == false) {
				throw new Exception("Erro ao acessar página.");				
			}
			
			$inexistentes = \ArrayUtil::nonExistingKeys(['senha', 'novaSenha', 'confirmacaoSenha'], $this->params);
			if(is_array($inexistentes) ? count($inexistentes) > 0 : 0) {
				$msg = 'Os seguintes campos obrigatórios não foram enviados: ' . implode(', ', $inexistentes);

				throw new Exception($msg);
			}	
			$usuario = new Usuario(); $usuario->fromArray($this->colecaoUsuario->comId($this->servicoLogin->getIdUsuario()));
			if(empty($usuario)) throw new Exception("Usuário não encontrado.");

			$this->colecaoUsuario->novaSenha(
				$usuario->getId(),
				\ParamUtil::value($this->params, 'senha'),
				\ParamUtil::value($this->params, 'novaSenha'),
				\ParamUtil::value($this->params, 'confirmacaoSenha')
			);

			$resposta = ['status' => true, 'mensagem'=> 'Senha atualizada com sucesso.']; 
		}
		catch (\Exception $e) {
			DB::rollback();
			$resposta = ['status' => false, 'mensagem'=> $e->getMessage()]; 
		}

		return $resposta;
	}
}
?>
				