<?php
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;
use \phputil\JSON;

// Início das rotas para categorias
$app->get('/categorias', function(Request $req,  Response $res, $args = []) use ($app) {
	$this->logger->addInfo("Acessando listagem de categorias");
	
	$ctrl = new ControladoraCategoria($req->getQueryParams());
	$response = $ctrl->todos();
	return $res->withHeader('Content-type', 'application/json; charset=UTF-8')->withJson(json_decode(JSON::encode($response)));

});

$app->post('/categorias', function(Request $req,  Response $res, $args = []) use ($app) {
	$this->logger->addInfo("Acessando o cadastro de categorias");
	$ctrl = new ControladoraCategoria($req->getParsedBody());
	$response = $ctrl->adicionar();
	return $res->withHeader('Content-type', 'application/json; charset=UTF-8')->withJson(JSON::decode(json_encode($response)));

});


$app->put('/categorias', function(Request $req,  Response $res, $args = []) use ($app) {
	$this->logger->addInfo("Acessando a atualização de categorias");
	$ctrl = new ControladoraCategoria($req->getParsedBody());
	$response = $ctrl->atualizar();
	return $res->withHeader('Content-type', 'application/json; charset=UTF-8')->withJson(JSON::decode(json_encode($response)));

});

$app->delete('/categorias/{id}', function(Request $req,  Response $res, $args = []) use ($app) {
	$this->logger->addInfo("Deletando a categoria de id ". $args['id'] . '.');
	$ctrl = new ControladoraCategoria($req->getParsedBody());
	$response = $ctrl->remover($args['id']);
	return $res->withHeader('Content-type', 'application/json; charset=UTF-8')->withJson(JSON::decode(json_encode($response)));

});

// Início das rotas para checklist
$app->get('/checklist', function(Request $req,  Response $res, $args = []) use ($app) {
	$this->logger->addInfo("Acessando listagem de checklist");	
	$ctrl = new ControladoraChecklist($req->getQueryParams());
	$response = $ctrl->todos();
	return $res->withHeader('Content-type', 'application/json; charset=UTF-8')->withJson(json_decode(JSON::encode($response)));

});

$app->post('/checklist', function(Request $req,  Response $res, $args = []) use ($app) {
	$this->logger->addInfo("Acessando o cadastro de checklist");
	$ctrl = new ControladoraChecklist($req->getParsedBody());
	$response = $ctrl->adicionar();
	return $res->withHeader('Content-type', 'application/json; charset=UTF-8')->withJson(JSON::decode(json_encode($response)));

});

$app->delete('/checklist/{id}', function(Request $req,  Response $res, $args = []) use ($app) {
	$this->logger->addInfo("Deletando a categoria de id ". $args['id'] . '.');
	$ctrl = new ControladoraChecklist($req->getParsedBody());
	$response = $ctrl->remover($args['id']);
	return $res->withHeader('Content-type', 'application/json; charset=UTF-8')->withJson(JSON::decode(json_encode($response)));
});

// Início das rotas para loja
$app->get('/loja', function(Request $req,  Response $res, $args = []) use ($app) {
	$this->logger->addInfo("Acessando listagem de lojas");	
	$ctrl = new ControladoraLoja($req->getQueryParams());
	$response = $ctrl->todos();
	return $res->withHeader('Content-type', 'application/json; charset=UTF-8')->withJson(json_decode(JSON::encode($response)));
});

?>