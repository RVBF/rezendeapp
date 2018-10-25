<?php
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;
/**
*  @author Rafael Vinicius Barros Fereira
*/

// Realiza ajustes de zona, data e hora do servidor
date_default_timezone_set('America/Sao_Paulo' );

require_once 'bootstrap.php';

require_once 'routes.php';

$container['logger'] = function($c) {
    $logger = new \Monolog\Logger('my_logger');
    $file_handler = new \Monolog\Handler\StreamHandler('../logs/app.log');
    $logger->pushHandler($file_handler);
    return $logger;
};

// Execução
$app->run();
?>
