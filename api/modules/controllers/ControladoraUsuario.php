<?php
use phputil\datatables\DataTablesRequest;
use phputil\datatables\DataTablesResponse;
use Symfony\Component\Validator\Validation as Validacao;
use \phputil\JSON;
use \phputil\RTTI;


/**
 * Controladora de Usuario
 *
 * @author	Rafael Vinicius Barros Ferreira
 * @version	0.1
 */
class ControladoraUsuario {

	private $params;
	private $colecaoUsuario;
	
	function __construct($params,  Sessao $sessao) {
		$this->params = $params;
		$this->colecaoUsuario = Dice::instance()->create('ColecaoUsuario');
	}

	function todos() {
		$dtr = new DataTablesRequest($this->params);
		$contagem = 0;
		$objetos = [];
		$erro = null;

		try
		{
			$objetos = $this->colecaoUsuario->todos($dtr->start, $dtr->length);

			$contagem = $this->colecaoUsuario->contagem();
		}
		catch (\Exception $e )
		{
			throw new Exception("Erro ao listar categorias");
		}

		$conteudo = new DataTablesResponse(
			$contagem,
			$contagem, //count($objetos ),
			$objetos,
			$dtr->draw,
			$erro
		);

		return $conteudo;
    }
    
    function adicionar() {
		$inexistentes = \ArrayUtil::nonExistingKeys(['id', 'login','senha'], $this->params);
		$resposta = [];

		try {
			$hash = HashSenha::instance();
			
			if(count($inexistentes) > 0) {
				$msg = 'Os seguintes campos obrigatórios não foram enviados: ' . implode(', ', $inexistentes);

				throw new Exception($msg);
            }
			$usuario = new Usuario( 0, \ParamUtil::value($this->params, 'login'), $hash->gerarHashDeSenhaComSaltEmMD5(\ParamUtil::value($this->params, 'senha')));
			$resposta = ['checklist'=> RTTI::getAttributes($this->colecaoUsuario->adicionar($usuario), RTTI::allFlags()), 'status' => true, 'mensagem'=> 'Usuário cadastrado com sucesso.']; 
		}
		catch (\Exception $e) {
			$resposta = ['status' => false, 'mensagem'=> $e->getMessage()]; 
		}

		return $resposta;
	}
}
?>