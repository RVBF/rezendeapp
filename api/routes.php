<?php
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;
use \phputil\JSON;

// Início das rotas para categorias
$app->get('/categorias', function(Request $req,  Response $res, $args = []) use ($app, $session) {
	$this->logger->addInfo("Acessando listagem de categorias");
	$sessaoUsuario = new Sessao($session);
	$ctrl = new ControladoraCategoria($req->getQueryParams(), $sessaoUsuario);
	$response = $ctrl->todos();
	return $res->withHeader('Content-type', 'application/json; charset=UTF-8')->withJson(json_decode(JSON::encode($response)));

});

$app->post('/categorias', function(Request $req,  Response $res, $args = []) use ($app, $session) {
	$this->logger->addInfo("Acessando o cadastro de categorias");
	$sessaoUsuario = new Sessao($session);
	$ctrl = new ControladoraCategoria($req->getParsedBody(), $sessaoUsuario);
	$response = $ctrl->adicionar();
	return $res->withHeader('Content-type', 'application/json; charset=UTF-8')->withJson(JSON::decode(json_encode($response)));
});

$app->put('/categorias', function(Request $req,  Response $res, $args = []) use ($app, $session) {
	$this->logger->addInfo("Acessando a atualização de categorias");
	$sessaoUsuario = new Sessao($session);
	$ctrl = new ControladoraCategoria($req->getParsedBody(), $sessaoUsuario);
	$response = $ctrl->atualizar();
	return $res->withHeader('Content-type', 'application/json; charset=UTF-8')->withJson(JSON::decode(json_encode($response)));

});

$app->delete('/categorias/{id}', function(Request $req,  Response $res, $args = []) use ($app, $session) {
	$this->logger->addInfo("Deletando a categoria de id ". $args['id'] . '.');
	$sessaoUsuario = new Sessao($session);
	$ctrl = new ControladoraCategoria($req->getParsedBody(), $sessaoUsuario);
	$response = $ctrl->remover($args['id']);
	return $res->withHeader('Content-type', 'application/json; charset=UTF-8')->withJson(JSON::decode(json_encode($response)));

});

// Início das rotas para checklist
$app->get('/checklist', function(Request $req,  Response $res, $args = []) use ($app, $session) {
	$this->logger->addInfo("Acessando listagem de checklist");	
	$sessaoUsuario = new Sessao($session);
	$ctrl = new ControladoraChecklist($req->getQueryParams(), $sessaoUsuario);
	$response = $ctrl->todos();
	return $res->withHeader('Content-type', 'application/json; charset=UTF-8')->withJson(json_decode(JSON::encode($response)));

});

$app->post('/checklist', function(Request $req,  Response $res, $args = []) use ($app, $session) {
	$this->logger->addInfo("Acessando o cadastro de checklist");
	$sessaoUsuario = new Sessao($session);
	$ctrl = new ControladoraChecklist($req->getParsedBody(), $sessaoUsuario);
	$response = $ctrl->adicionar();
	return $res->withHeader('Content-type', 'application/json; charset=UTF-8')->withJson(JSON::decode(json_encode($response)));

});

$app->put('/checklist', function(Request $req,  Response $res, $args = []) use ($app, $session) {
	$this->logger->addInfo("Acessando a atualização de categorias");
	$sessaoUsuario = new Sessao($session);
	$ctrl = new ControladoraChecklist($req->getParsedBody(), $sessaoUsuario);
	$response = $ctrl->atualizar();
	return $res->withHeader('Content-type', 'application/json; charset=UTF-8')->withJson(JSON::decode(json_encode($response)));

});

$app->delete('/checklist/{id}', function(Request $req,  Response $res, $args = []) use ($app, $session) {
	$this->logger->addInfo("Deletando a categoria de id ". $args['id'] . '.');
	$sessaoUsuario = new Sessao($session);
	$ctrl = new ControladoraChecklist($req->getParsedBody(), $sessaoUsuario);
	$response = $ctrl->remover($args['id']);
	return $res->withHeader('Content-type', 'application/json; charset=UTF-8')->withJson(JSON::decode(json_encode($response)));
});

// Início das rotas para loja
$app->get('/loja', function(Request $req,  Response $res, $args = []) use ($app, $session) {
	$this->logger->addInfo("Acessando listagem de lojas");	
	$sessaoUsuario = new Sessao($session);
	$ctrl = new ControladoraLoja($req->getQueryParams(), $sessaoUsuario);
	$response = $ctrl->todos();
	return $res->withHeader('Content-type', 'application/json; charset=UTF-8')->withJson(json_decode(JSON::encode($response)));
});

$app->post('/loja', function(Request $req,  Response $res, $args = []) use ($app, $session) {
	$this->logger->addInfo("Acessando o cadastro de loja");
	$sessaoUsuario = new Sessao($session);
	$ctrl = new ControladoraLoja($req->getParsedBody(), $sessaoUsuario);
	$response = $ctrl->adicionar();
	return $res->withHeader('Content-type', 'application/json; charset=UTF-8')->withJson(JSON::decode(json_encode($response)));

});

$app->put('/loja', function(Request $req,  Response $res, $args = []) use ($app, $session) {
	$this->logger->addInfo("Acessando a atualização de categorias");
	$sessaoUsuario = new Sessao($session);
	$ctrl = new ControladoraLoja($req->getParsedBody(), $sessaoUsuario);
	$response = $ctrl->atualizar();
	return $res->withHeader('Content-type', 'application/json; charset=UTF-8')->withJson(JSON::decode(json_encode($response)));

});

$app->delete('/loja/{id}', function(Request $req,  Response $res, $args = []) use ($app, $session) {
	$this->logger->addInfo("Deletando a categoria de id ". $args['id'] . '.');
	$sessaoUsuario = new Sessao($session);
	$ctrl = new ControladoraLoja($req->getParsedBody(), $sessaoUsuario);
	$response = $ctrl->remover($args['id']);
	return $res->withHeader('Content-type', 'application/json; charset=UTF-8')->withJson(JSON::decode(json_encode($response)));
});

// Início das rotas para tarefa
$app->get('/checklist/{idChecklist}/tarefa', function(Request $req,  Response $res, $args = []) use ($app, $session) {
	$this->logger->addInfo("Acessando listagem de tarefa");	
	$sessaoUsuario = new Sessao($session);
	$ctrl = new ControladoraTarefa($req->getQueryParams(), $sessaoUsuario);
	$response = $ctrl->todos($args['idChecklist']);
	return $res->withHeader('Content-type', 'application/json; charset=UTF-8')->withJson(json_decode(JSON::encode($response)));

});

$app->post('/checklist/{idChecklist}/tarefa', function(Request $req,  Response $res, $args = []) use ($app, $session) {
	$this->logger->addInfo("Acessando o cadastro de tarefa");
	$sessaoUsuario = new Sessao($session);
	$ctrl = new ControladoraTarefa($req->getParsedBody(), $sessaoUsuario);
	$response = $ctrl->adicionar($args['idChecklist']);
	return $res->withHeader('Content-type', 'application/json; charset=UTF-8')->withJson(JSON::decode(json_encode($response)));

});

$app->put('/checklist/{idChecklist}/tarefa', function(Request $req,  Response $res, $args = []) use ($app, $session) {
	$this->logger->addInfo("Acessando a atualização de tarefas");
	$sessaoUsuario = new Sessao($session);
	$ctrl = new ControladoraTarefa($req->getParsedBody(), $sessaoUsuario);
	$response = $ctrl->atualizar($args['idChecklist']);
	return $res->withHeader('Content-type', 'application/json; charset=UTF-8')->withJson(JSON::decode(json_encode($response)));

});

$app->delete('/checklist/{idChecklist}/tarefa/{id}', function(Request $req,  Response $res, $args = []) use ($app, $session) {
	$this->logger->addInfo("Deletando a categoria de id ". $args['id'] . '.');
	$sessaoUsuario = new Sessao($session);
	$ctrl = new ControladoraTarefa($req->getParsedBody(), $sessaoUsuario);
	$response = $ctrl->remover($args['id'], $args['idChecklist']);
	return $res->withHeader('Content-type', 'application/json; charset=UTF-8')->withJson(JSON::decode(json_encode($response)));
});


// Início das rotas para pergunta
$app->get('/tarefa/{idTarefa}/pergunta', function(Request $req,  Response $res, $args = []) use ($app, $session) {
	$this->logger->addInfo("Acessando listagem de tarefa");	
	$sessaoUsuario = new Sessao($session);
	$ctrl = new ControladoraPergunta($req->getQueryParams(), $sessaoUsuario);
	$response = $ctrl->todos($args['idTarefa']);
	return $res->withHeader('Content-type', 'application/json; charset=UTF-8')->withJson(json_decode(JSON::encode($response)));

});

$app->get('/tarefa/{idTarefa}/pergunta/tarefa-com-id', function(Request $req,  Response $res, $args = []) use ($app, $session) {
	$this->logger->addInfo("Acessando listagem de tarefa");	
	$sessaoUsuario = new Sessao($session);
	$ctrl = new ControladoraPergunta($req->getQueryParams(), $sessaoUsuario);
	$response = $ctrl->comTarefaId($args['idTarefa']);
	return $res->withHeader('Content-type', 'application/json; charset=UTF-8')->withJson(json_decode(JSON::encode($response)));

});

$app->post('/tarefa/{idTarefa}/pergunta', function(Request $req,  Response $res, $args = []) use ($app, $session) {
	$this->logger->addInfo("Acessando o cadastro de tarefa");
	$sessaoUsuario = new Sessao($session);
	$ctrl = new ControladoraPergunta($req->getParsedBody(), $sessaoUsuario);
	$response = $ctrl->adicionar($args['idTarefa']);
	return $res->withHeader('Content-type', 'application/json; charset=UTF-8')->withJson(JSON::decode(json_encode($response)));

});

$app->post('/tarefa/{idTarefa}/pergunta/cadastrar-varias', function(Request $req,  Response $res, $args = []) use ($app, $session) {
	$this->logger->addInfo("Acessando o cadastro de tarefa");
	$sessaoUsuario = new Sessao($session);
	$ctrl = new ControladoraPergunta($req->getParsedBody(), $sessaoUsuario);
	$response = $ctrl->adicionarTodas($args['idTarefa']);
	return $res->withHeader('Content-type', 'application/json; charset=UTF-8')->withJson(JSON::decode(json_encode($response)));

});

$app->put('/tarefa/{idTarefa}/pergunta', function(Request $req,  Response $res, $args = []) use ($app, $session) {
	$this->logger->addInfo("Acessando a atualização de tarefas");
	$sessaoUsuario = new Sessao($session);
	$ctrl = new ControladoraPergunta($req->getParsedBody(), $sessaoUsuario);
	$response = $ctrl->atualizar($args['idTarefa']);
	return $res->withHeader('Content-type', 'application/json; charset=UTF-8')->withJson(JSON::decode(json_encode($response)));

});

$app->delete('/tarefa/{idTarefa}/pergunta/{id}', function(Request $req,  Response $res, $args = []) use ($app, $session) {
	$this->logger->addInfo("Deletando a categoria de id ". $args['id'] . '.');
	$sessaoUsuario = new Sessao($session);
	$ctrl = new ControladoraPergunta($req->getParsedBody()->getStream(), $sessaoUsuario);
	$response = $ctrl->remover($args['id'], $args['idTarefa']);
	return $res->withHeader('Content-type', 'application/json; charset=UTF-8')->withJson(JSON::decode(json_encode($response)));
});


$app->post('/anexo/upload-anexo', function(Request $req,  Response $res, $args = []) use ($app, $session) {
	$this->logger->addInfo("Deletando a categoria de id ". $args['id'] . '.');
	$sessaoUsuario = new Sessao($session);//, $sessaoUsuario 

	$ctrl = new ControladoraPergunta($req->getParsedBody());
	// $response = $ctrl->remover($args['id'], $args['idTarefa']);
	return $res->withHeader('Content-type', 'application/json; charset=UTF-8')->withJson(JSON::decode(json_encode($response)));
});


// Início das rotas para usuario
$app->get('/usuario', function(Request $req,  Response $res, $args = []) use ($app, $session) {
	$this->logger->addInfo("Acessando listagem de usuario");	
	$sessaoUsuario = new Sessao($session);
	$ctrl = new ControladoraUsuario($req->getQueryParams(), $sessaoUsuario);
	$response = $ctrl->todos();
	return $res->withHeader('Content-type', 'application/json; charset=UTF-8')->withJson(json_decode(JSON::encode($response)));

});

$app->post('/usuario', function(Request $req,  Response $res, $args = []) use ($app, $session) {
	$this->logger->addInfo("Acessando o cadastro de usuario");
	$sessaoUsuario = new Sessao($session);
	$ctrl = new ControladoraUsuario($req->getParsedBody(), $sessaoUsuario);
	$response = $ctrl->adicionar();
	return $res->withHeader('Content-type', 'application/json; charset=UTF-8')->withJson(JSON::decode(json_encode($response)));

});

$app->put('/usuario', function(Request $req,  Response $res, $args = []) use ($app, $session) {
	$this->logger->addInfo("Acessando a atualização de usuario.");
	$sessaoUsuario = new Sessao($session);
	$ctrl = new ControladoraUsuario($req->getParsedBody(), $sessaoUsuario);
	$response = $ctrl->atualizar();
	return $res->withHeader('Content-type', 'application/json; charset=UTF-8')->withJson(JSON::decode(json_encode($response)));

});

$app->delete('/usuario/{id}', function(Request $req,  Response $res, $args = []) use ($app, $session) {
	$this->logger->addInfo("Deletando a usuario de id ". $args['id'] . '.');
	$sessaoUsuario = new Sessao($session);
	$ctrl = new ControladoraUsuario($req->getParsedBody(), $sessaoUsuario);
	$response = $ctrl->remover($args['id']);
	return $res->withHeader('Content-type', 'application/json; charset=UTF-8')->withJson(JSON::decode(json_encode($response)));
});

// Início das rotas para login
$app->post('/login', function(Request $req,  Response $res, $args = []) use ($app, $session) {
	$sessaoUsuario = new Sessao($session);
	$ctrl = new ControladoraLogin($req->getParsedBody(), $sessaoUsuario);
	$response = $ctrl->logar();
	return $res->withHeader('Content-type', 'application/json; charset=UTF-8')->withJson(JSON::decode(json_encode($response)));
});

$app->post('/logout', function(Request $req,  Response $res, $args = []) use ($app, $session) {
	$sessaoUsuario = new Sessao($session);
	$ctrl = new ControladoraLogin($req->getParsedBody(), $sessaoUsuario);
	$resposta = $ctrl->sair();
	return $res->withHeader('Content-type', 'application/json; charset=UTF-8')->withJson(JSON::decode(json_encode($resposta)));
});
// Fim das rotas para login

// Início das rotas para sessão
$app->post('/sessao/verificar-sessao', function(Request $req,  Response $res, $args = []) use ($app, $session) {
	$sessaoUsuario = new Sessao($session);
	$ctrl = new ControladoraSessao($req->getParsedBody(), $sessaoUsuario);
	$resposta = $ctrl->estaAtiva();
	if($resposta['status']) return $res->withHeader('Content-type', 'application/json; charset=UTF-8')->withJson(JSON::decode(json_encode($resposta)));
	else return $res->withStatus(401)->withJson(JSON::decode(json_encode($resposta)));
});
// Fim das rotas para sessão

// Início das rotas para grupos de usuário
$app->get('/grupo-usuario', function(Request $req,  Response $res, $args = []) use ($app, $session) {
	$this->logger->addInfo("Acessando listagem de Grupos de usuario");	
	$sessaoUsuario = new Sessao($session);
	$ctrl = new ControladoraGrupoUsuario($req->getQueryParams(), $sessaoUsuario);
	$response = $ctrl->todos();
	return $res->withHeader('Content-type', 'application/json; charset=UTF-8')->withJson(json_decode(JSON::encode($response)));

});

$app->post('/grupo-usuario', function(Request $req,  Response $res, $args = []) use ($app, $session) {
	$this->logger->addInfo("Acessando o cadastro de tarefa");
	$sessaoUsuario = new Sessao($session);
	$ctrl = new ControladoraTarefa($req->getParsedBody(), $sessaoUsuario);
	$response = $ctrl->adicionar($args['idChecklist']);
	return $res->withHeader('Content-type', 'application/json; charset=UTF-8')->withJson(JSON::decode(json_encode($response)));

});

$app->put('/grupo-usuario', function(Request $req,  Response $res, $args = []) use ($app, $session) {
	$this->logger->addInfo("Acessando a atualização de tarefas");
	$sessaoUsuario = new Sessao($session);
	$ctrl = new ControladoraTarefa($req->getParsedBody(), $sessaoUsuario);
	$response = $ctrl->atualizar($args['idChecklist']);
	return $res->withHeader('Content-type', 'application/json; charset=UTF-8')->withJson(JSON::decode(json_encode($response)));

});

$app->delete('/grupo-usuario', function(Request $req,  Response $res, $args = []) use ($app, $session) {
	$this->logger->addInfo("Deletando a categoria de id ". $args['id'] . '.');
	$sessaoUsuario = new Sessao($session);
	$ctrl = new ControladoraTarefa($req->getParsedBody(), $sessaoUsuario);
	$response = $ctrl->remover($args['id'], $args['idChecklist']);
	return $res->withHeader('Content-type', 'application/json; charset=UTF-8')->withJson(JSON::decode(json_encode($response)));
});


?>