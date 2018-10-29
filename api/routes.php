<?php
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;
use \phputil\JSON;


// Início das rotas para Medicamentos Precificados
$app->get('/categorias', function(Request $req,  Response $res, $args = []) use ($app)
{
	$this->logger->addInfo("Acessando listagem de categorias");
	
	$ctrl = new ControladoraCategoria($req->getQueryParams());
	$categorias = $ctrl->todos();
	return $res->withHeader('Content-type', 'application/json; charset=UTF-8')->withJson(json_decode(JSON::encode($categorias)));

});

$app->post('/categorias', function(Request $req,  Response $res, $args = []) use ($app)
{
	$this->logger->addInfo("Acessando o cadastro de categorias");
	$ctrl = new ControladoraCategoria($req->getParsedBody());
	$categoriaResponse = $ctrl->adicionar();
	return $res->withHeader('Content-type', 'application/json; charset=UTF-8')->withJson(json_decode(json_encode($categoriaResponse)));

});
?>