<?php

use phputil\di\DI;
use Illuminate\Database\Capsule\Manager as Db;

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
		DI::config(DI::let('ColecaoCategoria')->create('ColecaoCategoriaEmBDR'));
		DI::config(DI::let('ColecaoChecklist')->create('ColecaoChecklistEmBDR'));
		DI::config(DI::let('ColecaoLoja')->create('ColecaoLojaEmBDR'));
		DI::config(DI::let('ColecaoTarefa')->create('ColecaoTarefaEmBDR'));
		DI::config(DI::let('ColecaoPergunta')->create('ColecaoPerguntaEmBDR'));
		DI::config(DI::let('ColecaoResposta')->create('ColecaoRespostaEmBDR'));
		DI::config(DI::let('ColecaoAnexo')->create('ColecaoAnexoEmBDR'));
		DI::config(DI::let('ColecaoUsuario')->create('ColecaoUsuarioEmBDR'));
		DI::config(DI::let('ColecaoGrupoUsuario')->create('ColecaoGrupoUsuarioEmBDR'));
	}
}

?>