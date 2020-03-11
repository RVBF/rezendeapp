<?php
use phputil\datatables\DataTablesRequest;
use phputil\datatables\DataTablesResponse;
use Symfony\Component\Validator\Validation as Validacao;
use \phputil\JSON;
use \phputil\RTTI;
use Illuminate\Database\Capsule\Manager as DB;

/**
 * Controladora de Relatorio
 *
 * @author	Rafael Vinicius Barros Ferreira
 * @version	1.0
 */
class ControladoraRelatorio {

    private $params;
	private $servicoLogin;
	private $colecaoChecklist;
	private $colecaoPa;
	private $colecaoPe;

	function __construct($params, Sessao $sessao) {
		$this->params = $params;
		$this->servicoLogin = new ServicoLogin($sessao);
		$this->colecaoPa = Dice::instance()->create('ColecaoPlanoAcao');
		$this->colecaoPe = Dice::instance()->create('ColecaoPendencia');
		$this->colecaoChecklist = Dice::instance()->create('ColecaoChecklist'); 
	}

	function contadores() {
		try {
			if($this->servicoLogin->verificarSeUsuarioEstaLogado() == false) {
				throw new Exception("Erro ao acessar página.");
			}
			$resposta = ['status' => true, 'resposta'=> [
				'contagemChecklist' => $this->colecaoChecklist->contagem(),
				'contagemPendencia' =>$this->colecaoPe ->contagem(),
				'contagemPlanoAcao' => $this->colecaoPe->contagem(),
				'contagemPorLoja' => $this->colecaoChecklist->contagemPorLoja()
			]]; 

		}
		catch (\Exception $e )
		{
			Util::printr($e->getMessage());
			$resposta = ['status' => false, 'mensagem'=> 'Erro ao consultar contadores!']; 
      }

		return  $resposta;
	}
		   
	function checklistsPorStatus(){
		try {
			if($this->servicoLogin->verificarSeUsuarioEstaLogado() == false) throw new Exception("Erro ao acessar página.");

			$resultados = $this->colecaoChecklist->quantidadePorStatuseData();

			$resposta = ['status' => true, 'resposta'=> $resultados]; 

		}
		catch (\Exception $e )
		{
			$resposta = ['status' => false, 'mensagem'=> 'Erro ao consultar contadores!']; 
		}

		return  $resposta;
	}

	function quantidadePaPE(){
		try {
			if($this->servicoLogin->verificarSeUsuarioEstaLogado() == false) throw new Exception("Erro ao acessar página.");

			$resposta = ['status' => true, 'resposta'=> [
				'contagemPendencia' =>$this->colecaoPe ->contagem(),
				'contagemPlanoAcao' => $this->colecaoPe->contagem(),
			]]; 

			$resposta = ['status' => true, 'resposta'=> '']; 
		}
		catch (\Exception $e )
		{
			$resposta = ['status' => false, 'mensagem'=> 'Erro ao consultar contadores!']; 
		}

		return  $resposta;
	}
}
?>
