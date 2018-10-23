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
	function create($className)
	{
		$this->makeContainer();
		return DI::create($className);
	}

	private function makeContainer()
	{
		DI::config( DI::let( 'Db')->create( 'ColecaoCategoriaEmBDR')->shared());

		DI::config( DI::let( 'ColecaoCategoria' )->create( 'ColecaoCategoriaEmBDR'));
	}

	private function makeBD()
	{
		$config = Config::getInstance();

		if($config->getConfiguration('debug')) {
			$dbConfig = $config->getConfiguration('development');

			$run = new Whoops\Run;
			$handler = new Whoops\Handler\PrettyPageHandler;
			$run->pushHandler($handler);
			$run->register();
		} else {
			$dbConfig = $config->getConfiguration('production');
		}

		$db = new Db;

		$db->addConnection([
			'driver'    => 'mysql',
			'host'      => $dbConfig['host'],
			'database'  => $dbConfig['name'],
			'username'  => $dbConfig['user'],
			'password'  => $dbConfig['pass'],
			'charset'   => 'utf8',
			'collation' => 'utf8_unicode_ci',
			'prefix'    => '',
		]);

		$db->setAsGlobal();
		$db->bootEloquent();

		return $db;
	}
}

?>