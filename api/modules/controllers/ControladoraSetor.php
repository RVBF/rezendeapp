<?php
use phputil\datatables\DataTablesRequest;
use phputil\datatables\DataTablesResponse;
use Symfony\Component\Validator\Validation as Validacao;
use \phputil\JSON;
use \phputil\RTTI;
use Carbon\Carbon;
use Illuminate\Database\Capsule\Manager as DB;

/**
 * Controladora de Setor
 *
 * @author	Rafael Vinicius Barros Ferreira
 * @version	1.0
 */
class ControladoraSetor {

	private $params;
	private $colecaoSetor;
	private $colecaoLoja;
	private $servicoLogin;
	
	function __construct($params,  Sessao $sessao) {
		$this->servicoLogin = new ServicoLogin($sessao);
		$this->params = $params;
		$this->colecaoSetor = Dice::instance()->create('ColecaoSetor');
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

			$objetos = $this->colecaoSetor->todos($dtr->start, $dtr->length, (isset($dtr->search->value)) ? $dtr->search->value : '');
		
			$contagem = $this->colecaoSetor->contagem();
		}
		catch (\Exception $e )
		{
			throw new Exception("Erro ao listar setores.");
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

			$inexistentes = \ArrayUtil::nonExistingKeys(['id', 'titulo','descricao'], $this->params);
			$resposta = [];

			if(is_array($inexistentes) ? count($inexistentes) > 0 : 0) {
				$msg = 'Os seguintes campos obrigatórios não foram enviados: ' . implode(', ', $inexistentes);

				throw new Exception($msg);
			}

			$setor = new Setor(
				0,
				\ParamUtil::value($this->params, 'titulo'),
				\ParamUtil::value($this->params, 'descricao')
			);
			$this->colecaoSetor->adicionar($setor);

			$resposta = ['status' => true, 'mensagem'=> 'Setor cadastrado com sucesso.']; 
			
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

			$inexistentes = \ArrayUtil::nonExistingKeys(['id', 'titulo','descricao'], $this->params);
			$resposta = [];

			if(is_array($inexistentes) ? count($inexistentes) > 0 : 0) {
				$msg = 'Os seguintes campos obrigatórios não foram enviados: ' . implode(', ', $inexistentes);

				throw new Exception($msg);
			}
			
			$setor = new Setor(
				\ParamUtil::value($this->params, 'id'),
				\ParamUtil::value($this->params, 'titulo'),
				\ParamUtil::value($this->params, 'descricao')
			);

			$this->colecaoSetor->atualizar($setor);
			
			$resposta = ['status' => true, 'mensagem'=> 'Setor atualizado com sucesso.']; 
			
			DB::commit();
		}
		catch (\Exception $e) {
			DB::rollback();

			$resposta = ['status' => false, 'mensagem'=> $e->getMessage()]; 
		}

		return $resposta;
	}

	function comId($id) {
		try {
			if($this->servicoLogin->verificarSeUsuarioEstaLogado() == false) throw new Exception("Erro ao acessar página.");				
			
			if (! is_numeric($id)) return $this->geradoraResposta->erro('O id informado não é numérico.', GeradoraResposta::TIPO_TEXTO);

			$setor = new Setor(); $setor->fromArray($this->colecaoSetor->comId($id));
		
			$resposta = ['conteudo'=> $setor->toArray(), 'status' => true]; 
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

			// if(!$this->servicoLogin->eAdministrador()){
			// 	throw new Exception("Usuário sem permissão para executar ação.");
			// }

			$resposta = [];

			$status = $this->colecaoSetor->remover($id);
			
			$resposta = ['status' => true, 'mensagem'=> 'Setor removido com sucesso.']; 
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