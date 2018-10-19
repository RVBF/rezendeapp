<?php
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

/**
*  @author Rafael Vinicius Barros Fereira
*/

require_once 'vendor/autoload.php';

// Realiza ajustes de zona, data e hora do servidor
date_default_timezone_set('America/Sao_Paulo' );


// Cria a aplicação Slim
$app = new \Slim\App;
$app->get('/hello/{name}', function (Request $request, Response $response, array $args) {
	print_r("Cheguei");

	$name = $args['name'];
    $response->getBody()->write("Hello, $name");

    return $response;
});
// CORS
// $app->map('/:x+', function($x ) use ($app)
// 	{
// 	$app->response->header('Access-Control-Allow-Methods', 'HEAD, GET, POST, PATCH, PUT, DELETE, OPTIONS' );
//     $app->response->setStatus(200 );
// } )->via('OPTIONS' );

// Seta erro como o status default, ao invés de sucesso!
// $app->response->setStatus(400 );

// require_once 'routes.php';

// Execução
$app->run();
?>
