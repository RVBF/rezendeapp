<?php
use \phputil\JSON;
use \phputil\RTTI;
use Illuminate\Database\Capsule\Manager as DB;

/**
 * Controladora de PermissaoAdministrativa
 *
 * @author	Rafael Vinicius Barros Ferreira
 * @version	0.1
 */
class ControladoraPermissaoAdministrativa {

	private $params;
	private $colecaoUsuario;
    private $colecaoGrupoDeUsuario;
	private $colecaoPermissao;
    
	private $servicologin;
	
	function __construct($params, Sessao $sessao) {
		$this->params = $params;
		$this->colecaoUsuario = Dice::instance()->create('ColecaoUsuario');
        $this->colecaoGrupoDeUsuario = Dice::instance()->create('ColecaoGrupoUsuario');
        $this->colecaoPermissao = Dice::instance()->create('ColecaoPermissaoAdministrativa');
		$this->servicoLogin = new ServicoLogin($sessao);
	}

    function configurar() {
		DB::beginTransaction();

		try {
			if($this->servicoLogin->verificarSeUsuarioEstaLogado() == false) {
				throw new Exception("Erro ao acessar página.");				
			}

			if(!$this->servicoLogin->eAdministrador()){
				throw new Exception("Usuário sem permissão para executar ação.");
			}

			$inexistentes = \ArrayUtil::nonExistingKeys(['grupos', 'usuarios'], $this->params);
			if(is_array($inexistentes) ? count($inexistentes) > 0 : 0) {
				$msg = 'Os seguintes campos obrigatórios não foram enviados: ' . implode(', ', $inexistentes);

				throw new Exception($msg);
			}			
		
			// $grupos = $this->colecaoGrupoDeUsuario->todosComIds($this->params['grupos']);

			// if(!isset($grupos) and !($grupos instanceof GrupoUsuario)){
			// 	throw new Exception("Grupo não encontrado na base de dados.");
            // }
            
            // $usuarios = $this->colecaoUsuario->todosComIds($this->params['usuarios']);

			// if(!isset($usuarios) and !($usuarios instanceof Usuario)){
			// 	throw new Exception("Grupo não encontrado na base de dados.");
			// }

			$permissao = new PermissaoAdministrativa($this->params['grupos'],$this->params['usuarios']);

			$this->colecaoPermissao->configurar($permissao);

			DB::commit();

			$resposta = ['status' => true, 'mensagem'=> 'Permissão configurada com sucesso.']; 
		}
		catch (\Exception $e) {
			DB::rollback();
			$resposta = ['status' => false, 'mensagem'=> $e->getMessage()]; 
		}

		return $resposta;
    }
}
?>
				