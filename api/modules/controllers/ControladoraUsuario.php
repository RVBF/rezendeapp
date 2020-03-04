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

	function todosOpcoes() {
		try {
			if($this->servicoLogin->verificarSeUsuarioEstaLogado() == false) {
				throw new Exception("Erro ao acessar página.");
			}

			$resposta['contagem'] = 0;
			$resposta['objetos'] = [];
			$resposta['erro'] = null;
			$resposta['data'] = $this->colecaoUsuario->todosOpcoes();
			$resposta['contagem'] = $this->colecaoUsuario->contagem();
		}
		catch (\Exception $e )
		{
			throw new Exception($e->getMessage());
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
			
			DB::commit();

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
