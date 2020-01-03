<?php
use phputil\datatables\DataTablesRequest;
use phputil\datatables\DataTablesResponse;
use Symfony\Component\Validator\Validation as Validacao;
use \phputil\JSON;
use \phputil\RTTI;
use Illuminate\Database\Capsule\Manager as DB;

/**
 * Controladora de Recurso
 *
 * @author	Leonardo Carvalhães Bernardo
 * @version	1.0
 */
class ControladoraRecurso {

	private $params;
   private $colecaoRecurso;
   private $servicoLogin;

	function __construct($params, Sessao $sessao) {
		$this->params = $params;
		$this->colecaoRecurso = Dice::instance()->create('ColecaoRecurso');
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

			$objetos = $this->colecaoRecurso->todos($dtr->start, $dtr->length);

         $contagem = $this->colecaoRecurso->contagem();

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
			throw new Exception("Erro ao listar recursos.");
      }

		return  RTTI::getAttributes($conteudo, RTTI::allFlags());
   }

	function todosParaArvore() {
		try {
			if($this->servicoLogin->verificarSeUsuarioEstaLogado() == false) {
				throw new Exception('Erro ao acessar página.');
			}

			$objetos = $this->colecaoRecurso->todos();

         $conteudo = $objetos;

         $conteudo = new DataTablesResponse(
            null,
            null,
            $objetos,
            null,
            null
         );
		}
		catch (\Exception $e)
		{
			throw new Exception($e->getMessage());
      }

		return  RTTI::getAttributes($conteudo, RTTI::allFlags());
   }
}
?>
