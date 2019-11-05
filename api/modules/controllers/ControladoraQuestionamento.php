<?php
use phputil\datatables\DataTablesRequest;
use phputil\datatables\DataTablesResponse;
use Symfony\Component\Validator\Validation as Validacao;
use \phputil\JSON;
use \phputil\RTTI;
use Illuminate\Database\Capsule\Manager as DB;
use Carbon\Carbon;




/**
 * Controladora de Questionamento
 *
 * @author	Rafael Vinicius Barros Ferreira
 * @version	1.0
 */
class ControladoraQuestionamento {

	private $params;
	private $colecaoQuestionamento;
	private $colecaoPlanoAcao;
	private $servicoLogin;
	
	function __construct($params,  Sessao $sessao) {
		$this->params = $params;
		$this->colecaoQuestionamento = Dice::instance()->create('ColecaoQuestionamento');
		$this->colecaoPlanoAcao = Dice::instance()->create('ColecaoPlanoAcao');
		$this->servicoLogin = new ServicoLogin($sessao);

	}

	function todos() {
		try {
			if($this->servicoLogin->verificarSeUsuarioEstaLogado() == false) {
				throw new Exception("Erro ao acessar página.");				
			}
			
			$dtr = new DataTablesRequest($this->params);
			$contagem = 0;
			$objetos = [];
			$erro = null;
			$objetos = $this->colecaoQuestionamento->todos($dtr->start, $dtr->length, (isset($dtr->search->value)) ? $dtr->search->value : '');

			$contagem = $this->colecaoQuestionamento->contagem();
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

		return $conteudo;
	}

    function executar(){
		DB::beginTransaction();

		try {
			if($this->servicoLogin->verificarSeUsuarioEstaLogado() == false) {
				throw new Exception("Erro ao acessar página.");				
			}
			
			// if(!$this->servicoLogin->eAdministrador()){
			// 	throw new Exception("Usuário sem permissão para executar ação.");
			// }
			
			$inexistentes = \ArrayUtil::nonExistingKeys(['id', 'status','formularioPergunta', 'formularioResposta'], $this->params);
			$resposta = [];

			if(is_array($inexistentes) ? count($inexistentes) > 0 : 0) {
				$msg = 'Os seguintes campos obrigatórios não foram enviados: ' . implode(', ', $inexistentes);

				throw new Exception($msg);
			}

			$planoDeAcao = null;
			if($this->params['formularioResposta']['opcao'] != OpcoesRespostaEnumerada::BOM){
				if(isset($this->params['planoAcao'])){
					if(
						strlen($this->params['planoAcao']['descricao']) > 0 and
						strlen($this->params['planoAcao']['dataLimite']) > 0 and
						strlen($this->params['planoAcao']['solucao']) > 0 and
						$this->params['planoAcao']['responsavel'] > 0
					){
						$dataLimitePA = new Carbon($this->params['planoAcao']['dataLimite'], 'America/Sao_Paulo');
	
						$planoDeAcao = new PlanoAcao(
							0,
							$this->params['planoAcao']['descricao'],
							$dataLimitePA,
							$this->params['planoAcao']['solucao'],
							'',
							$this->params['planoAcao']['responsavel']
						);
					}

					Debuger::printr($this->params['planoAcao']['responsavel']);
	
					$this->colecaoPlanoAcao->adicionar($planoDeAcao);
				}
				Debuger::printr($planoDeAcao);
	
				if(isset($this->params['pendencia'])){
					if(
						strlen($this->params['pendecia']['descricao']) > 0 and
						strlen($this->params['pendecia']['dataLimite']) > 0 and
						strlen($this->params['pendecia']['solucao']) > 0
					){
						$dataLimitePe = new Carbon($this->params['pdencai']['dataLimite'], 'America/Sao_Paulo');
	
						$planoDeAcao = new PlanoAcao(
							0,
							$this->params['pendecia']['descricao'],
							$dataLimitePe,
							$this->params['pendecia']['solucao'],
							''
						);
					}
	
				}
	
				Debuger::printr($planoDeAcao);
			}


			$resposta = ['loja'=> RTTI::getAttributes($this->colecaoLoja->adicionar($loja), RTTI::allFlags()), 'status' => true, 'mensagem'=> 'Loja cadastrada com sucesso.']; 
			DB::commit();

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
			if($this->servicoLogin->verificarSeUsuarioEstaLogado() == false) {
				throw new Exception("Erro ao acessar página.");				
			}	
			
			if(!$this->servicoLogin->eAdministrador()){
				throw new Exception("Usuário sem permissão para executar ação.");
			}

			$resposta = [];

			$status = $this->colecaoQuestionamento->remover($id);
			
			$resposta = ['status' => $status, 'mensagem'=> 'Loja removida com sucesso.']; 
			
			DB::commit();
		}
		catch (\Exception $e) {
			DB::rollback();

			$resposta = ['status' => false, 'mensagem'=> $e->getMessage()]; 
		}

		return $resposta;
	}
}
?>