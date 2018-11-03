<?php
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;
use \phputil\JSON;

// Início das rotas para categorias
$app->get('/categorias', function(Request $req,  Response $res, $args = []) use ($app) {
	$this->logger->addInfo("Acessando listagem de categorias");
	
	$ctrl = new ControladoraCategoria($req->getQueryParams());
	$categorias = $ctrl->todos();
	return $res->withHeader('Content-type', 'application/json; charset=UTF-8')->withJson(json_decode(JSON::encode($categorias)));

});

$app->post('/categorias', function(Request $req,  Response $res, $args = []) use ($app) {
	$this->logger->addInfo("Acessando o cadastro de categorias");
	$ctrl = new ControladoraCategoria($req->getParsedBody());
	$categoriaResponse = $ctrl->adicionar();
	return $res->withHeader('Content-type', 'application/json; charset=UTF-8')->withJson(JSON::decode(json_encode($categoriaResponse)));

});


$app->put('/categorias', function(Request $req,  Response $res, $args = []) use ($app) {
	$this->logger->addInfo("Acessando a atualização de categorias");
	$ctrl = new ControladoraCategoria($req->getParsedBody());
	$categoriaResponse = $ctrl->atualizar();
	return $res->withHeader('Content-type', 'application/json; charset=UTF-8')->withJson(JSON::decode(json_encode($categoriaResponse)));

});

$app->delete('/categorias/{id}', function(Request $req,  Response $res, $args = []) use ($app) {
	$this->logger->addInfo("Deletando a categoria de id ". $args['id'] . '.');
	$ctrl = new ControladoraCategoria($req->getParsedBody());
	$categoriaResponse = $ctrl->remover($args['id']);
	return $res->withHeader('Content-type', 'application/json; charset=UTF-8')->withJson(JSON::decode(json_encode($categoriaResponse)));

});

// Início das rotas para checklist
$app->get('/checklist', function(Request $req,  Response $res, $args = []) use ($app) {
	$this->logger->addInfo("Acessando listagem de checklist");	
	$ctrl = new ControladoraChecklist($req->getQueryParams());
	$checkListResponse = $ctrl->todos();
	return $res->withHeader('Content-type', 'application/json; charset=UTF-8')->withJson(json_decode(JSON::encode($checkListResponse)));

});

$app->delete('/checklist/{id}', function(Request $req,  Response $res, $args = []) use ($app) {
	$this->logger->addInfo("Deletando a categoria de id ". $args['id'] . '.');
	$ctrl = new ControladoraChecklist($req->getParsedBody());
	$checkListResponse = $ctrl->remover($args['id']);
	return $res->withHeader('Content-type', 'application/json; charset=UTF-8')->withJson(JSON::decode(json_encode($checkListResponse)));

});
?>