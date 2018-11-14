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

$app->put('/checklist', function(Request $req,  Response $res, $args = []) use ($app) {
	$this->logger->addInfo("Acessando a atualização de categorias");
	$ctrl = new ControladoraChecklist($req->getParsedBody());
	$response = $ctrl->atualizar();
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

// Início das rotas para tarefa
$app->get('/checklist/{idChecklist}/tarefa', function(Request $req,  Response $res, $args = []) use ($app) {
	$this->logger->addInfo("Acessando listagem de tarefa");	
	$ctrl = new ControladoraTarefa($req->getQueryParams());
	$response = $ctrl->todos($args['idChecklist']);
	return $res->withHeader('Content-type', 'application/json; charset=UTF-8')->withJson(json_decode(JSON::encode($response)));

});

$app->post('/checklist/{idChecklist}/tarefa', function(Request $req,  Response $res, $args = []) use ($app) {
	$this->logger->addInfo("Acessando o cadastro de tarefa");
	$ctrl = new ControladoraTarefa($req->getParsedBody());
	$response = $ctrl->adicionar($args['idChecklist']);
	return $res->withHeader('Content-type', 'application/json; charset=UTF-8')->withJson(JSON::decode(json_encode($response)));

});

$app->put('/checklist/{idChecklist}/tarefa', function(Request $req,  Response $res, $args = []) use ($app) {
	$this->logger->addInfo("Acessando a atualização de tarefas");
	$ctrl = new ControladoraTarefa($req->getParsedBody());
	$response = $ctrl->atualizar($args['idChecklist']);
	return $res->withHeader('Content-type', 'application/json; charset=UTF-8')->withJson(JSON::decode(json_encode($response)));

});

$app->delete('/checklist/{idChecklist}/tarefa/{id}', function(Request $req,  Response $res, $args = []) use ($app) {
	$this->logger->addInfo("Deletando a categoria de id ". $args['id'] . '.');
	$ctrl = new ControladoraTarefa($req->getParsedBody());
	$response = $ctrl->remover($args['id'], $args['idChecklist']);
	return $res->withHeader('Content-type', 'application/json; charset=UTF-8')->withJson(JSON::decode(json_encode($response)));
});

// Início das rotas para pergunta
$app->get('/tarefa/{idTarefa}/pergunta', function(Request $req,  Response $res, $args = []) use ($app) {
	$this->logger->addInfo("Acessando listagem de tarefa");	
	$ctrl = new ControladoraPergunta($req->getQueryParams());
	$response = $ctrl->todos($args['idTarefa']);
	return $res->withHeader('Content-type', 'application/json; charset=UTF-8')->withJson(json_decode(JSON::encode($response)));

});

$app->post('/tarefa/{idTarefa}/pergunta', function(Request $req,  Response $res, $args = []) use ($app) {
	$this->logger->addInfo("Acessando o cadastro de tarefa");
	$ctrl = new ControladoraPergunta($req->getParsedBody());
	$response = $ctrl->adicionar($args['idTarefa']);
	return $res->withHeader('Content-type', 'application/json; charset=UTF-8')->withJson(JSON::decode(json_encode($response)));

});

$app->post('/tarefa/{idTarefa}/pergunta/cadastrar-varias', function(Request $req,  Response $res, $args = []) use ($app) {
	$this->logger->addInfo("Acessando o cadastro de tarefa");
	$ctrl = new ControladoraPergunta($req->getParsedBody());
	$response = $ctrl->adicionarTodas($args['idTarefa']);
	return $res->withHeader('Content-type', 'application/json; charset=UTF-8')->withJson(JSON::decode(json_encode($response)));

});

$app->put('/tarefa/{idTarefa}/pergunta', function(Request $req,  Response $res, $args = []) use ($app) {
	$this->logger->addInfo("Acessando a atualização de tarefas");
	$ctrl = new ControladoraPergunta($req->getParsedBody());
	$response = $ctrl->atualizar($args['idTarefa']);
	return $res->withHeader('Content-type', 'application/json; charset=UTF-8')->withJson(JSON::decode(json_encode($response)));

});

$app->delete('/tarefa/{idTarefa}/pergunta/{id}', function(Request $req,  Response $res, $args = []) use ($app) {
	$this->logger->addInfo("Deletando a categoria de id ". $args['id'] . '.');
	$ctrl = new ControladoraPergunta($req->getParsedBody());
	$response = $ctrl->remover($args['id'], $args['idTarefa']);
	return $res->withHeader('Content-type', 'application/json; charset=UTF-8')->withJson(JSON::decode(json_encode($response)));
});

?>