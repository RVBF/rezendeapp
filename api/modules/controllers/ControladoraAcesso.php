<?php
use phputil\datatables\DataTablesRequest;
use phputil\datatables\DataTablesResponse;
use Symfony\Component\Validator\Validation as Validacao;
use \phputil\JSON;
use \phputil\RTTI;
use Illuminate\Database\Capsule\Manager as DB;

/**
 * Controladora de Acesso
 *
 * @author	Leonardo Carvalhães Bernardo
 * @version	1.0
 */
class ControladoraAcesso {

	private $params;
	private $colecaoAcesso;

	function __construct($params, Sessao $sessao) {
		$this->params = $params;
		$this->colecaoAcesso = Dice::instance()->create('ColecaoAcesso');
		$this->sessao = $sessao;
	}

	function todos() {
		try {
			if($this->servicoLogin->verificarSeAcessoEstaLogado() == false) {
				throw new Exception("Erro ao acessar página.");
			}

			$dtr = new DataTablesRequest($this->params);
			$contagem = 0;
			$objetos = [];
			$erro = null;

			$objetos = $this->colecaoAcesso->todos($dtr->start, $dtr->length, (isset($dtr->search->value)) ? $dtr->search->value : '');

         $contagem = $this->colecaoAcesso->contagem();

         $conteudo = new DataTablesResponse(
            $contagem,
            count($objetos),
            $objetos,
            $dtr->draw,
            $erro
         );
		}
		catch (\Exception $e )
		{
			throw new Exception("Erro ao listar lojas.");
      }

		return  RTTI::getAttributes($conteudo, RTTI::allFlags());
    }

	function remover($id) {
		DB::beginTransaction();

		try {
			if($this->servicoLogin->verificarSeAcessoEstaLogado() == false) throw new Exception("Erro ao acessar página.");

			if(!$this->servicoLogin->eAdministrador()) throw new Exception("Usuário sem permissão para executar ação.");

			if (! is_numeric($id)) return $this->geradoraResposta->erro('O id informado não é numérico.', GeradoraResposta::TIPO_TEXTO);

			if(!$this->colecaoAcesso->remover($id)) throw new Exception("Erro ao remover usuário.");

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
			if($this->servicoLogin->verificarSeAcessoEstaLogado() == false) throw new Exception("Erro ao acessar página.");

			if (! is_numeric($id)) return $this->geradoraResposta->erro('O id informado não é numérico.', GeradoraResposta::TIPO_TEXTO);

			$usuario = new Acesso(); $usuario->fromArray($this->colecaoAcesso->comId($id));

			$usuario->setGruposAcessos($this->colecaoGrupoDeAcesso->comAcessoId($usuario->getId()));

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
			if($this->servicoLogin->verificarSeAcessoEstaLogado() == false) {
				throw new Exception("Erro ao acessar página.");
			}

			$inexistentes = \ArrayUtil::nonExistingKeys(['senha', 'novaSenha', 'confirmacaoSenha'], $this->params);
			if(is_array($inexistentes) ? count($inexistentes) > 0 : 0) {
				$msg = 'Os seguintes campos obrigatórios não foram enviados: ' . implode(', ', $inexistentes);

				throw new Exception($msg);
			}
			$usuario = new Acesso(); $usuario->fromArray($this->colecaoAcesso->comId($this->servicoLogin->getIdAcesso()));
			if(empty($usuario)) throw new Exception("Usuário não encontrado.");

			$this->colecaoAcesso->novaSenha(
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
