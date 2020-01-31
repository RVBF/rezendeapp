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

	function __construct($params, Sessao $sessao) {
		$this->params = $params;
		$this->servicoLogin = new ServicoLogin($sessao);
	}

	function contadores() {
		try {
			if($this->servicoLogin->verificarSeUsuarioEstaLogado() == false) {
				throw new Exception("Erro ao acessar página.");
			}
			$resposta = ['status' => true, 'resposta'=> [
				'contagemChecklist' => Dice::instance()->create('ColecaoChecklist')->contagem(),
				'contagemPendencia' => Dice::instance()->create('ColecaoPendencia')->contagem(),
				'contagemPlanoAcao' => Dice::instance()->create('ColecaoPlanoAcao')->contagem(),
				'contagemPorLoja' => Dice::instance()->create('ColecaoChecklist')->contagemPorLoja()
			]]; 

		}
		catch (\Exception $e )
		{
			Util::printr($e->getMessage());
			$resposta = ['status' => false, 'mensagem'=> 'Erro ao consultar contadores!']; 
      }

		return  $resposta;
   }
}
?>
