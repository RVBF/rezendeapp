<?php
use phputil\datatables\DataTablesRequest;
use phputil\datatables\DataTablesResponse;
use Symfony\Component\Validator\Validation as Validacao;
use \phputil\JSON;
use \phputil\RTTI;
use Carbon\Carbon;
use Illuminate\Database\Capsule\Manager as DB;

/**
 * Controladora de Questionario
 *
 * @author	Rafael Vinicius Barros Ferreira
 * @version	1.0
 */
class ControladoraQuestionario {

	private $params;
	private $colecaoQuestionario;
	private $colecaoLoja;
	private $servicoLogin;
	
	function __construct($params,  Sessao $sessao) {
		$this->servicoLogin = new ServicoLogin($sessao);
		$this->params = $params;
		$this->colecaoQuestionario = Dice::instance()->create('ColecaoQuestionario');
		$this->colecaoLoja = Dice::instance()->create('ColecaoLoja');

	}

	function todos() {
		try
		{
			if($this->servicoLogin->verificarSeUsuarioEstaLogado() == false) {
				throw new Exception("Erro ao acessar página.");				
            }
            
			$dtr = new DataTablesRequest($this->params);

			$contagem = 0;
			$objetos = [];
			$erro = null;

            $objetos = $this->colecaoQuestionario->todos($dtr->start, $dtr->length, (isset($dtr->search->value)) ? $dtr->search->value : '');
		
			$contagem = $this->colecaoQuestionario->contagem();
		}
		catch (\Exception $e )
		{
			throw new Exception("Erro ao listar questionário.");
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

			$inexistentes = \ArrayUtil::nonExistingKeys(['id', 'titulo','descricao','configuracao'], $this->params);
			$resposta = [];

			if(is_array($inexistentes) ? count($inexistentes) > 0 : 0) {
				$msg = 'Os seguintes campos obrigatórios não foram enviados: ' . implode(', ', $inexistentes);

				throw new Exception($msg);
			}
			$formulario = json_encode($this->params['configuracao']);

			$questionario = new Questionario(
				0,
				\ParamUtil::value($this->params, 'titulo'),
				\ParamUtil::value($this->params, 'descricao'),
				\ParamUtil::value($this->params, 'tipoQuestionario'),
				$formulario
			);

			$this->colecaoQuestionario->adicionar($questionario);

			$resposta = ['status' => true, 'mensagem'=> 'Questionario cadastrado com sucesso.']; 

			DB::commit();

		}
		catch (\Exception $e) {
			DB::rollback();

			$resposta = ['status' => false, 'mensagem'=> $e->getMessage()]; 
		}

		return $resposta;
	}

	function atualizar() {
		DB::beginTransaction();

		try {
			if($this->servicoLogin->verificarSeUsuarioEstaLogado() == false) {
				throw new Exception("Erro ao acessar página.");				
			}		
			
			if(!$this->servicoLogin->eAdministrador()){
				throw new Exception("Usuário sem permissão para executar ação.");
			}

			$inexistentes = \ArrayUtil::nonExistingKeys(['id', 'titulo','descricao'], $this->params);
			$resposta = [];

			if(is_array($inexistentes) ? count($inexistentes) > 0 : 0) {
				$msg = 'Os seguintes campos obrigatórios não foram enviados: ' . implode(', ', $inexistentes);

				throw new Exception($msg);
			}
			
			$Questionario = new Questionario(
				\ParamUtil::value($this->params, 'id'),
				\ParamUtil::value($this->params, 'titulo'),
				\ParamUtil::value($this->params, 'descricao')
			);
			$resposta = ['Questionario'=> RTTI::getAttributes($this->colecaoQuestionario->atualizar($Questionario), RTTI::allFlags()), 'status' => true, 'mensagem'=> 'Questionario atualizado com sucesso.']; 
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

			// if(!$this->servicoLogin->eAdministrador()){
			// 	throw new Exception("Usuário sem permissão para executar ação.");
			// }

			$resposta = [];

			$status = $this->colecaoQuestionario->remover($id);
			
			$resposta = ['status' => true, 'mensagem'=> 'Questionario removido com sucesso.']; 
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