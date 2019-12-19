<?php
use phputil\datatables\DataTablesRequest;
use phputil\datatables\DataTablesResponse;
use Symfony\Component\Validator\Validation as Validacao;
use \phputil\JSON;
use \phputil\RTTI;
use Illuminate\Database\Capsule\Manager as DB;



/**
 * Controladora de Loja
 *
 * @author	Rafael Vinicius Barros Ferreira
 * @version	1.0
 */
class ControladoraLoja {

	private $params;
	private $colecaoLoja;
	private $servicoLogin;
	private $colecaoEndereco;
	
	function __construct($params,  Sessao $sessao) {
		$this->params = $params;
		$this->colecaoLoja = Dice::instance()->create('ColecaoLoja');
		$this->colecaoEndereco = Dice::instance()->create('ColecaoEndereco');
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
			$objetos = $this->colecaoLoja->todos($dtr->start, $dtr->length, (isset($dtr->search->value)) ? $dtr->search->value : '');
			$contagem = $this->colecaoLoja->contagem();
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

		return RTTI::getAttributes($conteudo, RTTI::allFlags() );
	}

	function adicionar() {
		DB::beginTransaction();

		try {
			if($this->servicoLogin->verificarSeUsuarioEstaLogado() == false) {
				throw new Exception("Erro ao acessar página.");				
			}
			
			if(!$this->servicoLogin->eAdministrador()){
				throw new Exception("Usuário sem permissão para executar ação.");
			}
			$inexistentes = \ArrayUtil::nonExistingKeys(['id', 'razaoSocial','nomeFantasia', 'endereco'], $this->params);
			$resposta = [];

			if(is_array($inexistentes) ? count($inexistentes) > 0 : 0) {
				$msg = 'Os seguintes campos obrigatórios não foram enviados: ' . implode(', ', $inexistentes);

				throw new Exception($msg);
			}

			$endereco = new Endereco(
				0,
				\ParamUtil::value($this->params['endereco'], 'cep'),
				\ParamUtil::value($this->params['endereco'], 'logradouro'),
				\ParamUtil::value($this->params['endereco'], 'numero'),
				\ParamUtil::value($this->params['endereco'], 'complemento'),
				\ParamUtil::value($this->params['endereco'], 'bairro'),
				\ParamUtil::value($this->params['endereco'], 'cidade'),
				\ParamUtil::value($this->params['endereco'], 'estado')
			);

			$this->colecaoEndereco->adicionar($endereco);

			$loja = new Loja(
				0,
				\ParamUtil::value($this->params, 'razaoSocial'),
				\ParamUtil::value($this->params, 'nomeFantasia'),
				$endereco
			);
		
			$this->colecaoLoja->adicionar($loja);

			$resposta = ['status' => true, 'mensagem'=> 'Loja cadastrada com sucesso.']; 
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
			
			$inexistentes = \ArrayUtil::nonExistingKeys(['id', 'razaoSocial','nomeFantasia', 'endereco'], $this->params);
			$resposta = [];

			if(is_array($inexistentes) ? count($inexistentes) > 0 : 0) {
				$msg = 'Os seguintes campos obrigatórios não foram enviados: ' . implode(', ', $inexistentes);

				throw new Exception($msg);
			}

			$loja = new Loja(); $loja->fromArray($this->colecaoLoja->comId(\ParamUtil::value($this->params, 'id')));

			if(!($loja instanceof Loja)) throw new Exception("Loja não encontrada na base de dados");

			$endereco = new Endereco(); $endereco->fromArray($loja->getEndereco());

			$loja->setRazaoSocial(\ParamUtil::value($this->params, 'razaoSocial'));
			$loja->setNomeFantasia(\ParamUtil::value($this->params, 'nomeFantasia'));
			$this->colecaoLoja->atualizar($loja);

			$endereco->setCep(\ParamUtil::value($this->params['endereco'], 'cep'));
			$endereco->setLogradouro(\ParamUtil::value($this->params['endereco'], 'logradouro'));
			$endereco->setNumero(\ParamUtil::value($this->params['endereco'], 'numero'));
			$endereco->setComplemento(\ParamUtil::value($this->params['endereco'], 'complemento'));
			$endereco->setBairro(\ParamUtil::value($this->params['endereco'], 'bairro'));
			$endereco->setCidade(\ParamUtil::value($this->params['endereco'], 'cidade'));
			$endereco->setUf(\ParamUtil::value($this->params['endereco'], 'estado'));

			$this->colecaoEndereco->atualizar($endereco);

			$resposta = ['status' => true, 'mensagem'=> 'Loja atualizada com sucesso.']; 
		
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

			$loja = new Loja(); $loja->fromArray($this->colecaoLoja->comId($id));
		
			$resposta = ['conteudo'=> $loja->toArray(), 'status' => true]; 
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
			if($this->servicoLogin->verificarSeUsuarioEstaLogado() == false) throw new Exception("Erro ao acessar página.");				

			$loja = new Loja(); $loja->fromArray($this->colecaoLoja->comId($id));

			if(!($loja instanceof Loja)) throw new Exception("Loja não encontrada na base de dados");
			$endereco = new Endereco(); $endereco->fromArray($loja->getEndereco());
			$this->colecaoLoja->remover($loja->getId());
			$this->colecaoEndereco->remover($endereco->getId());

			return ['status' => true, 'mensagem'=> 'Loja removida com sucesso.']; 
			
			DB::commit();
		}
		catch (\Exception $e) {
			DB::rollback();

			return ['status' => false, 'mensagem'=> $e->getMessage()]; 
		}
	}
}
?>