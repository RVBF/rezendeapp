<?php

use phputil\di\DI;
use Illuminate\Database\Capsule\Manager as DB;

/**
 *  Envólucro para container de injeção de dependência.
 *
 *  @author	Rafael Vinicius Barros Ferreira
 *  @version 0.1
 */
class Dice {

	private $container = null;

	private function __construct()	{}
	private function __clone()	{}
	private function __wakeup()	{}
    private static $singleton = null;

	static function instance()
	{
		if (null == self::$singleton)
		{
            self::$singleton = new Dice;
			return self::$singleton;
        }
        
		return self::$singleton;
	}

	/**
	 *  Retorna um novo objeto para a classe informada.
	 */
	function create($className) {
		$this->makeContainer();
		return DI::create($className);
	}

	private function makeContainer() {
		DI::config(DI::let('ColecaoUsuario')->create('ColecaoUsuarioEmBDR'));
		DI::config(DI::let('ColecaoSetor')->create('ColecaoSetorEmBDR'));
		DI::config(DI::let('ColecaoLoja')->create('ColecaoLojaEmBDR'));
		DI::config(DI::let('ColecaoChecklist')->create('ColecaoChecklistEmBDR'));
		DI::config(DI::let('ColecaoAnexo')->create('ColecaoAnexoEmBDR'));
		DI::config(DI::let('ColecaoUsuario')->create('ColecaoUsuarioEmBDR'));
		DI::config(DI::let('ColecaoGrupoUsuario')->create('ColecaoGrupoUsuarioEmBDR'));
		DI::config(DI::let('ColecaoFormularioRespondido')->create('ColecaoFormularioRespondidoEmBDR'));
		DI::config(DI::let('ColecaoColaborador')->create('ColecaoColaboradorEmBDR'));
		DI::config(DI::let('ColecaoPermissaoAdministrativa')->create('ColecaoPermissaoAdministrativaEmBDR'));
		DI::config(DI::let('ColecaoQuestionario')->create('ColecaoQuestionarioEmBDR'));
		DI::config(DI::let('ColecaoQuestionamento')->create('ColecaoQuestionamentoEmBDR'));	
		DI::config(DI::let('ColecaoPlanoAcao')->create('ColecaoPlanoAcaoEmBDR'));		
		DI::config(DI::let('ColecaoPendencia')->create('ColecaoPendenciaEmBDR'));
		DI::config(DI::let('ColecaoHistoricoResponsabilidade')->create('ColecaoHistoricoResponsabilidadeEmBDR'));	
	}
}

?>