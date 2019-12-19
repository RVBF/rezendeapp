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
class ControladoraColaborador {

	private $params;
	private $colecaoUsuario;
	private $colecaoLoja;
	private $servicologin;
	private $colecaoColaborador;
	private $colecaoSetor;
	private $colecaoGrupoDeUsuario;
	private $servicoArquivo;
	private $colecaoAnexo;

	function __construct($params, Sessao $sessao) {
		$this->params = $params;
		$this->colecaoUsuario = Dice::instance()->create('ColecaoUsuario');
		$this->colecaoGrupoDeUsuario = Dice::instance()->create('ColecaoGrupoUsuario');
		$this->colecaoLoja = Dice::instance()->create('ColecaoLoja');
		$this->colecaoColaborador = Dice::instance()->create('ColecaoColaborador');
		$this->colecaoSetor = Dice::instance()->create('ColecaoSetor');
		$this->servicoLogin = new ServicoLogin($sessao);
		$this->servicoArquivo = ServicoArquivo::instance();
		$this->colecaoAnexo = Dice::instance()->create('ColecaoAnexo');
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

			$objetos = $this->colecaoColaborador->todos($dtr->start, $dtr->length, (isset($dtr->search->value)) ? $dtr->search->value : '');	
			$contagem = $this->colecaoUsuario->contagem();
		}
		catch (\Exception $e ) {
			throw new Exception("Erro ao listar colaboradores.");
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
			
			$inexistentes = \ArrayUtil::nonExistingKeys(['id', 'nome', 'sobrenome', 'email', 'usuario', 'lojas', 'setor'], $this->params);
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
				\ParamUtil::value($this->params['usuario'], 'login'), 
				\ParamUtil::value($this->params['usuario'], 'senha')
			);

			$this->colecaoUsuario->adicionar($usuario);

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

			$avatar;

            if(isset($this->params['avatar'])){
				$pastaColaborador = 'colaborador_'. $colaborador->getId();
				$patch = $this->servicoArquivo->validarESalvarImagem($this->params['avatar'], Colaborador::CAMINHO_IMAGEM, $pastaColaborador);
				$avatar = new Anexo(
					0,
					$patch,
					$this->params['avatar']['tipo']
				);

				$this->colecaoAnexo->adicionar($avatar);
				$colaborador->setAvatar($avatar);
				$this->colecaoColaborador->atualizarAvatar($colaborador);
	
			}

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

			// if(!$this->servicoLogin->eAdministrador()){
			// 	throw new Exception("Usuário sem permissão para executar ação.");
			// }
			
			$inexistentes = \ArrayUtil::nonExistingKeys(['id', 'nome', 'sobrenome', 'email', 'usuario', 'lojas', 'setor'], $this->params);
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
            $colaborador  = new Colaborador(); $colaborador->fromArray($this->colecaoColaborador->comId(\ParamUtil::value($this->params, 'id')));
            if(!isset($colaborador) and !($colaborador instanceof Colaborador)) throw new Exception("Colaborador não encontrado!");
		  
			$usuario = new Usuario(); $usuario->fromArray($colaborador->getUsuario());
			
			$usuario->setLogin(\ParamUtil::value($this->params['usuario'], 'login'));
            $usuario->setSenha(\ParamUtil::value($this->params['usuario'], 'senha'));
            $this->colecaoUsuario->atualizar($usuario); 

            $colaborador->setNome(\ParamUtil::value($this->params, 'nome'));           
            $colaborador->setSobrenome(\ParamUtil::value($this->params, 'sobrenome'));
            $colaborador->setEmail(\ParamUtil::value($this->params, 'email'));
            $colaborador->setUsuario($usuario);
            $colaborador->setSetor($setor);
            $colaborador->setLojas($lojas);
			if(isset($this->params['avatar'])) $colaborador->setAvatar(null);
			$this->colecaoColaborador->atualizar($colaborador);

            if(isset($this->params['avatar'])){
				$pastaColaborador = 'colaborador_'. $colaborador->getId();
				$patch = $this->servicoArquivo->validarESalvarImagem($this->params['avatar'], Colaborador::CAMINHO_IMAGEM, $pastaColaborador);
				$avatar = new Anexo(
					0,
					$patch,
					$this->params['avatar']['tipo']
				);

				$this->colecaoAnexo->adicionar($avatar);
				$colaborador->setAvatar($avatar);
				$this->colecaoColaborador->atualizarAvatar($colaborador);
			}else{
				if($colaborador->getAvatar() != null){
					$pastaColaborador = 'colaborador_'. $colaborador->getId();
					if(!$this->servicoArquivo->excluiPasta(Colaborador::CAMINHO_IMAGEM, $pastaColaborador, $colaborador->getAvatar()['patch'])){
						throw new Exception("Erro ao excluir arquivo");
					}
				}
			}

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
			
			if (!is_numeric($id)) return $this->geradoraResposta->erro('O id informado não é numérico.', GeradoraResposta::TIPO_TEXTO);
			$colaborador = new Colaborador(); $colaborador->fromArray($this->colecaoColaborador->comId($id));
			if(!($colaborador instanceof Colaborador)) throw new Exception("Colaborador não encontrado!");
			$usuario = new Usuario(); $usuario->fromArray($colaborador->getUsuario());
			$this->colecaoUsuario->remover($usuario->getId());
			$this->colecaoColaborador->remover($id);
			
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

			$usuario = new Colaborador(); $usuario->fromArray($this->colecaoColaborador->comId($id));
		
			$resposta = ['conteudo'=> $usuario->toArray(), 'status' => true]; 
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
				