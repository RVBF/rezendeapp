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

			$query = DB::table(ColecaoPendenciaEmBDR::TABELA)->select(
			DB::raw('DISTINCT ( select count(*) from '. ColecaoPendenciaEmBDR::TABELA .' p1 where p1.`deleted_at` is null and p1.status =  "' . StatusPendenciaEnumerado::AGUARDANDO_EXECUCAO . '" and DATE('. ColecaoPendenciaEmBDR::TABELA.'.datacadastro) = DATE(p1.datacadastro)) as qtdStatusAgaExecucao, DATE('. ColecaoPendenciaEmBDR::TABELA.'.datacadastro) as "Data"'));
			$query->where('deleted_at', NULL)->orderBy('Data', 'asc');
				
			$pEsAbertasPorData = $query->get();
			$query = DB::table(ColecaoPlanoAcaoEmBDR::TABELA)->select(
				DB::raw('DISTINCT ( select count(*) from '. ColecaoPlanoAcaoEmBDR::TABELA .' p1 where p1.`deleted_at` is null and p1.status =  "' . StatusPaEnumerado::AGUARDANDO_EXECUCAO . '" and DATE('. ColecaoPlanoAcaoEmBDR::TABELA.'.datacadastro) = DATE(p1.datacadastro)) as qtdStatusAgaExecucao, DATE('. ColecaoPlanoAcaoEmBDR::TABELA.'.datacadastro) as "Data"'));
			$pAsAbertasPorData = $query->get();


			$resposta = ['status' => true, 'resposta'=> ['pEsAbertasPorData'=> $pEsAbertasPorData, 'pAsAbertasPorData'=>$pAsAbertasPorData]]; 
		}
		catch (\Exception $e )
		{
			$resposta = ['status' => false, 'mensagem'=> 'Erro ao consultar contadores!']; 
		}

		return  $resposta;
	}
}
?>
