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
			if($this->servicoLogin->verificarSeUsuarioEstaLogado() == false)				throw new Exception("Erro ao acessar página.");				

			$inexistentes = \ArrayUtil::nonExistingKeys(['id', 'titulo','descricao','configuracao'], $this->params);
			$resposta = [];

			if(is_array($inexistentes) ? count($inexistentes) > 0 : 0) throw new Exception('Os seguintes campos obrigatórios não foram enviados: ' . implode(', ', $inexistentes));

			$formulario = json_encode($this->params['configuracao']);

			$questionario = new Questionario(); $questionario->fromArray($this->colecaoQuestionario->comId(\ParamUtil::value($this->params, 'id')));
			if(!($questionario instanceof Questionario)) throw new Exception("Questionário não econtrado na base de dados!");

			$questionario->setTitulo(\ParamUtil::value($this->params, 'titulo'));
			$questionario->setDescricao(\ParamUtil::value($this->params, 'descricao'));
			$questionario->setTipoQuestionario(\ParamUtil::value($this->params, 'tipoQuestionario'));
			$questionario->setFormulario($formulario);

			$this->colecaoQuestionario->atualizar($questionario);

			$resposta = ['status' => true, 'mensagem'=> 'Questionario atualizado com sucesso.']; 

			DB::commit();

		}
		catch (\Exception $e) {
			DB::rollback();

			$resposta = ['status' => false, 'mensagem'=> $e->getMessage()]; 
		}

		return $resposta;
	}
	
	function comId($id){
		try {
			if($this->servicoLogin->verificarSeUsuarioEstaLogado() == false) throw new Exception("Erro ao acessar página.");				
			
			if (! is_numeric($id)) return $this->geradoraResposta->erro('O id informado não é numérico.', GeradoraResposta::TIPO_TEXTO);

			$questionario = new Questionario(); $questionario->fromArray($this->colecaoQuestionario->comId($id));
		
			$resposta = ['conteudo'=> $questionario->toArray(), 'status' => true]; 
		}
		catch (\Exception $e) {
			DB::rollback();
			$resposta = ['status' => false, 'mensagem'=>  $e->getMessage()]; 
		}

		return $resposta;
	}

	function remover($id) {
		DB::beginTransaction();

		try {
			if($this->servicoLogin->verificarSeUsuarioEstaLogado() == false) {
				throw new Exception("Erro ao acessar página.");				
			}	
			$status = $this->colecaoQuestionario->remover($id);
			
			$resposta = ['status' => true, 'mensagem'=> 'Questionário removido com sucesso.']; 
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