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
		return $res->withHeader('Content-type', 'application/json; charset=UTF-8')->withJson($response);

	});

	$app->post('/categorias', function(Request $req,  Response $res, $args = []) use ($app, $session) {
		$this->logger->addInfo("Acessando o cadastro de categorias");
		$sessaoUsuario = new Sessao($session);
		$ctrl = new ControladoraCategoria($req->getParsedBody(), $sessaoUsuario);
		$response = $ctrl->adicionar();
		return $res->withHeader('Content-type', 'application/json; charset=UTF-8')->withJson($response);
	});

	$app->put('/categorias', function(Request $req,  Response $res, $args = []) use ($app, $session) {
		$this->logger->addInfo("Acessando a atualização de categorias");
		$sessaoUsuario = new Sessao($session);
		$ctrl = new ControladoraCategoria($req->getParsedBody(), $sessaoUsuario);
		$response = $ctrl->atualizar();
		return $res->withHeader('Content-type', 'application/json; charset=UTF-8')->withJson($response);

	});

	$app->delete('/categorias/{id}', function(Request $req,  Response $res, $args = []) use ($app, $session) {
		$this->logger->addInfo("Deletando a categoria de id ". $args['id'] . '.');
		$sessaoUsuario = new Sessao($session);
		$ctrl = new ControladoraCategoria($req->getParsedBody(), $sessaoUsuario);
		$response = $ctrl->remover($args['id']);
		return $res->withHeader('Content-type', 'application/json; charset=UTF-8')->withJson($response);

	});
// Fim das rotas para categorias

// Início das rotas para setor
	$app->get('/setor', function(Request $req,  Response $res, $args = []) use ($app, $session) {
		$this->logger->addInfo("Acessando listagem de setores");	
		$sessaoUsuario = new Sessao($session);
		$ctrl = new ControladoraSetor($req->getQueryParams(), $sessaoUsuario);
		$response = $ctrl->todos();

		return $res->withHeader('Content-type', 'application/json; charset=UTF-8')->withJson($response);
	});

	$app->post('/setor', function(Request $req,  Response $res, $args = []) use ($app, $session) {
		$this->logger->addInfo("Acessando o cadastro de setor");
		$sessaoUsuario = new Sessao($session);
		$ctrl = new ControladoraSetor($req->getParsedBody(), $sessaoUsuario);
		$response = $ctrl->adicionar();
		return $res->withHeader('Content-type', 'application/json; charset=UTF-8')->withJson($response);

	});

	$app->put('/setor', function(Request $req,  Response $res, $args = []) use ($app, $session) {
		$this->logger->addInfo("Acessando a atualização de categorias");
		$sessaoUsuario = new Sessao($session);
		$ctrl = new ControladoraSetor($req->getParsedBody(), $sessaoUsuario);
		$response = $ctrl->atualizar();
		return $res->withHeader('Content-type', 'application/json; charset=UTF-8')->withJson($response);

	});

	$app->delete('/setor/{id}', function(Request $req,  Response $res, $args = []) use ($app, $session) {
		$this->logger->addInfo("Deletando a categoria de id ". $args['id'] . '.');
		$sessaoUsuario = new Sessao($session);
		$ctrl = new ControladoraSetor($req->getParsedBody(), $sessaoUsuario);
		$response = $ctrl->remover($args['id']);
		return $res->withHeader('Content-type', 'application/json; charset=UTF-8')->withJson($response);
	});

// Fim das rotas para Setor

// Início das rotas para Questionários
	$app->get('/questionario', function(Request $req,  Response $res, $args = []) use ($app, $session) {
		$this->logger->addInfo("Acessando listagem de questionarioes");	
		$sessaoUsuario = new Sessao($session);
		$ctrl = new ControladoraQuestionario($req->getQueryParams(), $sessaoUsuario);
		$response = $ctrl->todos();
		return $res->withHeader('Content-type', 'application/json; charset=UTF-8')->withJson($response);
	});

	$app->post('/questionario', function(Request $req,  Response $res, $args = []) use ($app, $session) {
		$this->logger->addInfo("Acessando o cadastro de questionario");
		$sessaoUsuario = new Sessao($session);
		$ctrl = new ControladoraQuestionario($req->getParsedBody(), $sessaoUsuario);
		$response = $ctrl->adicionar();
		return $res->withHeader('Content-type', 'application/json; charset=UTF-8')->withJson($response);

	});

	$app->put('/questionario', function(Request $req,  Response $res, $args = []) use ($app, $session) {
		$this->logger->addInfo("Acessando a atualização de categorias");
		$sessaoUsuario = new Sessao($session);
		$ctrl = new ControladoraQuestionario($req->getParsedBody(), $sessaoUsuario);
		$response = $ctrl->atualizar();
		return $res->withHeader('Content-type', 'application/json; charset=UTF-8')->withJson($response);

	});

	$app->delete('/questionario/{id}', function(Request $req,  Response $res, $args = []) use ($app, $session) {
		$this->logger->addInfo("Deletando a categoria de id ". $args['id'] . '.');
		$sessaoUsuario = new Sessao($session);
		$ctrl = new ControladoraQuestionario($req->getParsedBody(), $sessaoUsuario);
		$response = $ctrl->remover($args['id']);
		return $res->withHeader('Content-type', 'application/json; charset=UTF-8')->withJson($response);
	});
// Fim das rotas para Questionários

// Início das rotas para loja
	$app->get('/loja', function(Request $req,  Response $res, $args = []) use ($app, $session) {
		$this->logger->addInfo("Acessando listagem de lojas");	
		$sessaoUsuario = new Sessao($session);
		$ctrl = new ControladoraLoja($req->getQueryParams(), $sessaoUsuario);
		$response = $ctrl->todos();
		return $res->withHeader('Content-type', 'application/json; charset=UTF-8')->withJson($response);
	});

	$app->post('/loja', function(Request $req,  Response $res, $args = []) use ($app, $session) {
		$this->logger->addInfo("Acessando o cadastro de loja");
		$sessaoUsuario = new Sessao($session);
		$ctrl = new ControladoraLoja($req->getParsedBody(), $sessaoUsuario);
		$response = $ctrl->adicionar();
		return $res->withHeader('Content-type', 'application/json; charset=UTF-8')->withJson($response);

	});

	$app->put('/loja', function(Request $req,  Response $res, $args = []) use ($app, $session) {
		$this->logger->addInfo("Acessando a atualização de categorias");
		$sessaoUsuario = new Sessao($session);
		$ctrl = new ControladoraLoja($req->getParsedBody(), $sessaoUsuario);
		$response = $ctrl->atualizar();
		return $res->withHeader('Content-type', 'application/json; charset=UTF-8')->withJson($response);

	});

	$app->delete('/loja/{id}', function(Request $req,  Response $res, $args = []) use ($app, $session) {
		$this->logger->addInfo("Deletando a categoria de id ". $args['id'] . '.');
		$sessaoUsuario = new Sessao($session);
		$ctrl = new ControladoraLoja($req->getParsedBody(), $sessaoUsuario);
		$response = $ctrl->remover($args['id']);
		return $res->withHeader('Content-type', 'application/json; charset=UTF-8')->withJson($response);
	});
// Fim das rotas para loja

// Início das rotas para questionamento
	$app->get('/questionamento', function(Request $req,  Response $res, $args = []) use ($app, $session) {
		$this->logger->addInfo("Acessando listagem de lojas");	
		$sessaoUsuario = new Sessao($session);
		$ctrl = new ControladoraQuestionamento($req->getQueryParams(), $sessaoUsuario);
		$response = $ctrl->todos();
		return $res->withHeader('Content-type', 'application/json; charset=UTF-8')->withJson($response);
	});

	$app->post('/questionamento', function(Request $req,  Response $res, $args = []) use ($app, $session) {
		$this->logger->addInfo("Acessando o cadastro de loja");
		$sessaoUsuario = new Sessao($session);
		$ctrl = new ControladoraQuestionamento($req->getParsedBody(), $sessaoUsuario);
		$response = $ctrl->executar();
		return $res->withHeader('Content-type', 'application/json; charset=UTF-8')->withJson($response);

	});

	$app->put('/questionamento', function(Request $req,  Response $res, $args = []) use ($app, $session) {
		$this->logger->addInfo("Acessando a atualização de categorias");
		$sessaoUsuario = new Sessao($session);
		$ctrl = new ControladoraQuestionamento($req->getParsedBody(), $sessaoUsuario);
		$response = $ctrl->atualizar();
		return $res->withHeader('Content-type', 'application/json; charset=UTF-8')->withJson($response);

	});

	$app->delete('/questionamentos/{id}', function(Request $req,  Response $res, $args = []) use ($app, $session) {
		$this->logger->addInfo("Deletando a categoria de id ". $args['id'] . '.');
		$sessaoUsuario = new Sessao($session);
		$ctrl = new ControladoraQuestionamento($req->getParsedBody(), $sessaoUsuario);
		$response = $ctrl->remover($args['id']);
		return $res->withHeader('Content-type', 'application/json; charset=UTF-8')->withJson($response);
	});
// fim das rotas para questionamentos

// Início das rotas para checklist
	$app->get('/checklist', function(Request $req,  Response $res, $args = []) use ($app, $session) {
		$this->logger->addInfo("Acessando listagem de tarefa");	
		$sessaoUsuario = new Sessao($session);
		$ctrl = new ControladoraChecklist($req->getQueryParams(), $sessaoUsuario);
		$response = $ctrl->todos();
		return $res->withHeader('Content-type', 'application/json; charset=UTF-8')->withJson($response);
	});

	$app->get('/checklist/questionamentos/{id}', function(Request $req,  Response $res, $args = []) use ($app, $session) {
		$this->logger->addInfo("Acessando listagem de tarefa");	
		$sessaoUsuario = new Sessao($session);
		$ctrl = new ControladoraChecklist($req->getQueryParams(), $sessaoUsuario);
		$response = $ctrl->getQuestionamentosParaExecucao($args['id']);
		return $res->withHeader('Content-type', 'application/json; charset=UTF-8')->withJson($response);
	});

	$app->put('/checklist', function(Request $req,  Response $res, $args = []) use ($app, $session) {
		$this->logger->addInfo("Acessando a atualização de tarefas");
		$sessaoUsuario = new Sessao($session);
		$ctrl = new ControladoraChecklist($req->getParsedBody(), $sessaoUsuario);
		$response = $ctrl->atualizar();
		return $res->withHeader('Content-type', 'application/json; charset=UTF-8')->withJson($response);

	});

	$app->post('/checklist', function(Request $req,  Response $res, $args = []) use ($app, $session) {
		$this->logger->addInfo("Acessando o cadastro de tarefa");
		$sessaoUsuario = new Sessao($session);
		$ctrl = new ControladoraChecklist($req->getParsedBody(), $sessaoUsuario);
		$response = $ctrl->adicionar();
		return $res->withHeader('Content-type', 'application/json; charset=UTF-8')->withJson($response);

	});

	$app->delete('/checklist/{id}', function(Request $req,  Response $res, $args = []) use ($app, $session) {
		$this->logger->addInfo("Deletando a categoria de id ". $args['id'] . '.');
		$sessaoUsuario = new Sessao($session);
		$ctrl = new ControladoraChecklist($req->getParsedBody(), $sessaoUsuario);
		$response = $ctrl->remover($args['id']);
		return $res->withHeader('Content-type', 'application/json; charset=UTF-8')->withJson($response);
	});
// Fim das rotas para checklist

// Início das rotas para plano-acao
	$app->get('/plano-acao', function(Request $req,  Response $res, $args = []) use ($app, $session) {
		$this->logger->addInfo("Acessando listagem de plano-acao");
		$sessaoUsuario = new Sessao($session);
		$ctrl = new ControladoraPlanoAcao($req->getQueryParams(), $sessaoUsuario);
		$response = $ctrl->todos();
		return $res->withHeader('Content-type', 'application/json; charset=UTF-8')->withJson($response);
	});

	$app->get('/plano-acao/pendentes/{id}', function(Request $req,  Response $res, $args = []) use ($app, $session) {
		$this->logger->addInfo("Acessando listagem de plano-acao");
		$sessaoUsuario = new Sessao($session);
		$ctrl = new ControladoraPlanoAcao($req->getQueryParams(), $sessaoUsuario);
		$response = $ctrl->todosPendentes($args['id']);
		return $res->withHeader('Content-type', 'application/json; charset=UTF-8')->withJson($response);
	});

	$app->get('/plano-acao/{id}', function(Request $req,  Response $res, $args = []) use ($app, $session) {
		$this->logger->addInfo("Acessando listagem de plano-acao");
		$sessaoUsuario = new Sessao($session);
		$ctrl = new ControladoraPlanoAcao($req->getQueryParams(), $sessaoUsuario);
		$response = $ctrl->comId($args['id']);
		return $res->withHeader('Content-type', 'application/json; charset=UTF-8')->withJson($response);

	});


	$app->post('/plano-acao', function(Request $req,  Response $res, $args = []) use ($app, $session) {
		$this->logger->addInfo("Acessando o cadastro de plano-acao");
		$sessaoUsuario = new Sessao($session);
		$ctrl = new ControladoraPlanoAcao($req->getParsedBody(), $sessaoUsuario);
		$response = $ctrl->adicionar();
		return $res->withHeader('Content-type', 'application/json; charset=UTF-8')->withJson($response);
	});

	$app->post('/plano-acao/confirmar-responsabilidade', function(Request $req,  Response $res, $args = []) use ($app, $session) {
		$this->logger->addInfo("Acessando o cadastro de plano-acao");
		$sessaoUsuario = new Sessao($session);
		$ctrl = new ControladoraPlanoAcao($req->getParsedBody(), $sessaoUsuario);
		$response = $ctrl->confirmarResponsabilidade();
		return $res->withHeader('Content-type', 'application/json; charset=UTF-8')->withJson($response);
	});

	$app->post('/plano-acao/executar', function(Request $req,  Response $res, $args = []) use ($app, $session) {
		$this->logger->addInfo("Acessando o cadastro de plano-acao");
		$sessaoUsuario = new Sessao($session);
		$ctrl = new ControladoraPlanoAcao($req->getParsedBody(), $sessaoUsuario);
		$response = $ctrl->executar();
		return $res->withHeader('Content-type', 'application/json; charset=UTF-8')->withJson($response);
	});

	$app->put('/plano-acao', function(Request $req,  Response $res, $args = []) use ($app, $session) {
		$this->logger->addInfo("Acessando a atualização de plano-acao");
		$sessaoUsuario = new Sessao($session);
		$ctrl = new ControladoraPlanoAcao($req->getParsedBody(), $sessaoUsuario);
		$response = $ctrl->atualizar();
		return $res->withHeader('Content-type', 'application/json; charset=UTF-8')->withJson($response);

	});

	$app->delete('/plano-acao/{id}', function(Request $req,  Response $res, $args = []) use ($app, $session) {
		$this->logger->addInfo("Deletando a categoria de id ". $args['id'] . '.');
		$sessaoUsuario = new Sessao($session);
		$ctrl = new ControladoraPlanoAcao($req->getParsedBody(), $sessaoUsuario);
		$response = $ctrl->remover($args['id']);
		return $res->withHeader('Content-type', 'application/json; charset=UTF-8')->withJson($response);

	});
// Fim das rotas para plano-acao

// Início das rotas para pendencia
	$app->get('/pendencia', function(Request $req,  Response $res, $args = []) use ($app, $session) {
		$this->logger->addInfo("Acessando listagem de pendencia");
		$sessaoUsuario = new Sessao($session);
		$ctrl = new ControladoraPendencia($req->getQueryParams(), $sessaoUsuario);
		$response = $ctrl->todos();

		return $res->withHeader('Content-type', 'application/json; charset=UTF-8')->withJson($response);

	});

	$app->get('/pendencia/pendentes/{id}', function(Request $req,  Response $res, $args = []) use ($app, $session) {
		$this->logger->addInfo("Acessando listagem de plano-acao");
		$sessaoUsuario = new Sessao($session);
		$ctrl = new ControladoraPendencia($req->getQueryParams(), $sessaoUsuario);
		$response = $ctrl->todosPendentes($args['id']);
		return $res->withHeader('Content-type', 'application/json; charset=UTF-8')->withJson($response);
	});

	$app->get('/pendencia/{id}', function(Request $req,  Response $res, $args = []) use ($app, $session) {
		$this->logger->addInfo("Acessando listagem de pendencia");
		$sessaoUsuario = new Sessao($session);
		$ctrl = new ControladoraPendencia($req->getQueryParams(), $sessaoUsuario);
		$response = $ctrl->comId($args['id']);
		return $res->withHeader('Content-type', 'application/json; charset=UTF-8')->withJson($response);
	});


	$app->post('/pendencia', function(Request $req,  Response $res, $args = []) use ($app, $session) {
		$this->logger->addInfo("Acessando o cadastro de pendencia");
		$sessaoUsuario = new Sessao($session);
		$ctrl = new ControladoraPendencia($req->getParsedBody(), $sessaoUsuario);
		$response = $ctrl->adicionar();
		return $res->withHeader('Content-type', 'application/json; charset=UTF-8')->withJson($response);
	});

	$app->post('/pendencia/confirmar-responsabilidade', function(Request $req,  Response $res, $args = []) use ($app, $session) {
		$this->logger->addInfo("Acessando o cadastro de pendencia");
		$sessaoUsuario = new Sessao($session);
		$ctrl = new ControladoraPendencia($req->getParsedBody(), $sessaoUsuario);
		$response = $ctrl->confirmarResponsabilidade();
		return $res->withHeader('Content-type', 'application/json; charset=UTF-8')->withJson($response);
	});

	$app->get('/pendencia/executar/{id}', function(Request $req,  Response $res, $args = []) use ($app, $session) {
		$this->logger->addInfo("Acessando o cadastro de pendencia");
		$sessaoUsuario = new Sessao($session);
		$ctrl = new ControladoraPendencia($req->getParsedBody(), $sessaoUsuario);
		$response = $ctrl->executar($args['id']);
		return $res->withHeader('Content-type', 'application/json; charset=UTF-8')->withJson($response);
	});

	$app->put('/pendencia', function(Request $req,  Response $res, $args = []) use ($app, $session) {
		$this->logger->addInfo("Acessando a atualização de pendencia");
		$sessaoUsuario = new Sessao($session);
		$ctrl = new ControladoraPendencia($req->getParsedBody(), $sessaoUsuario);
		$response = $ctrl->atualizar();
		return $res->withHeader('Content-type', 'application/json; charset=UTF-8')->withJson($response);

	});

	$app->delete('/pendencia/{id}', function(Request $req,  Response $res, $args = []) use ($app, $session) {
		$this->logger->addInfo("Deletando a categoria de id ". $args['id'] . '.');
		$sessaoUsuario = new Sessao($session);
		$ctrl = new ControladoraPendencia($req->getParsedBody(), $sessaoUsuario);
		$response = $ctrl->remover($args['id']);
		return $res->withHeader('Content-type', 'application/json; charset=UTF-8')->withJson($response);

	});
// Fim das rotas para  pendencia


// Início das rotas para usuario
	$app->get('/usuario', function(Request $req,  Response $res, $args = []) use ($app, $session) {
		$this->logger->addInfo("Acessando listagem de usuario");	
		$sessaoUsuario = new Sessao($session);
		$ctrl = new ControladoraUsuario($req->getQueryParams(), $sessaoUsuario);
		$response = $ctrl->todos();
		return $res->withHeader('Content-type', 'application/json; charset=UTF-8')->withJson($response);
	});

	$app->post('/usuario', function(Request $req,  Response $res, $args = []) use ($app, $session) {
		$this->logger->addInfo("Acessando o cadastro de usuario");
		$sessaoUsuario = new Sessao($session);
		$ctrl = new ControladoraUsuario($req->getParsedBody(), $sessaoUsuario);
		$response = $ctrl->adicionar();
		return $res->withHeader('Content-type', 'application/json; charset=UTF-8')->withJson($response);

	});

	$app->put('/usuario', function(Request $req,  Response $res, $args = []) use ($app, $session) {
		$this->logger->addInfo("Acessando a atualização de usuario.");
		$sessaoUsuario = new Sessao($session);
		$ctrl = new ControladoraUsuario($req->getParsedBody(), $sessaoUsuario);
		$response = $ctrl->atualizar();
		return $res->withHeader('Content-type', 'application/json; charset=UTF-8')->withJson($response);

	});

	$app->delete('/usuario/{id}', function(Request $req,  Response $res, $args = []) use ($app, $session) {
		$this->logger->addInfo("Deletando a usuario de id ". $args['id'] . '.');
		$sessaoUsuario = new Sessao($session);
		$ctrl = new ControladoraUsuario($req->getParsedBody(), $sessaoUsuario);
		$response = $ctrl->remover($args['id']);
		return $res->withHeader('Content-type', 'application/json; charset=UTF-8')->withJson($response);
	});
// Fim das rotas para usuario


// Início das rotas para login
	$app->post('/login', function(Request $req,  Response $res, $args = []) use ($app, $session) {
		$sessaoUsuario = new Sessao($session);
		$ctrl = new ControladoraLogin($req->getParsedBody(), $sessaoUsuario);
		$response = $ctrl->logar();
		return $res->withHeader('Content-type', 'application/json; charset=UTF-8')->withJson($response);
	});

	$app->post('/logout', function(Request $req,  Response $res, $args = []) use ($app, $session) {
		$sessaoUsuario = new Sessao($session);
		$ctrl = new ControladoraLogin($req->getParsedBody(), $sessaoUsuario);
		$resposta = $ctrl->sair();
		return $res->withHeader('Content-type', 'application/json; charset=UTF-8')->withJson($resposta);
	});
// Fim das rotas para login

// Início das rotas para sessão
	$app->post('/sessao/verificar-sessao', function(Request $req,  Response $res, $args = []) use ($app, $session) {
		$sessaoUsuario = new Sessao($session);
		$ctrl = new ControladoraSessao($req->getParsedBody(), $sessaoUsuario);
		$resposta = $ctrl->estaAtiva();
		if($resposta['status']) return $res->withHeader('Content-type', 'application/json; charset=UTF-8')->withJson($resposta);
		else return $res->withStatus(401)->withJson($resposta);
	});
// Fim das rotas para sessão

// Início das rotas para grupos de usuário
	$app->get('/grupo-usuario', function(Request $req,  Response $res, $args = []) use ($app, $session) {
		$this->logger->addInfo("Acessando listagem de Grupos de usuario");	
		$sessaoUsuario = new Sessao($session);
		$ctrl = new ControladoraGrupoUsuario($req->getQueryParams(), $sessaoUsuario);
		$response = $ctrl->todos();
		return $res->withHeader('Content-type', 'application/json; charset=UTF-8')->withJson($response);

	});

	$app->post('/grupo-usuario', function(Request $req,  Response $res, $args = []) use ($app, $session) {
		$this->logger->addInfo("Acessando o cadastro de tarefa");
		$sessaoUsuario = new Sessao($session);
		$ctrl = new ControladoraGrupoUsuario($req->getParsedBody(), $sessaoUsuario);
		$response = $ctrl->adicionar();
		return $res->withHeader('Content-type', 'application/json; charset=UTF-8')->withJson($response);

	});

	$app->put('/grupo-usuario', function(Request $req,  Response $res, $args = []) use ($app, $session) {
		$this->logger->addInfo("Acessando a atualização de tarefas");
		$sessaoUsuario = new Sessao($session);
		$ctrl = new ControladoraGrupoUsuario($req->getParsedBody(), $sessaoUsuario);
		$response = $ctrl->atualizar();
		return $res->withHeader('Content-type', 'application/json; charset=UTF-8')->withJson($response);

	});

	$app->delete('/grupo-usuario/{id}', function(Request $req,  Response $res, $args = []) use ($app, $session) {
		$this->logger->addInfo("Deletando o grupo de usuário de id ". $args['id'] . '.');
		$sessaoUsuario = new Sessao($session);
		$ctrl = new ControladoraGrupoUsuario($req->getParsedBody(), $sessaoUsuario);
		$response = $ctrl->remover($args['id']);
		return $res->withHeader('Content-type', 'application/json; charset=UTF-8')->withJson($response);
	});
// Fim das rotas para grupos de usuário

// Início para permissoes
	$app->post('/permissoes', function(Request $req,  Response $res, $args = []) use ($app, $session) {
		$this->logger->addInfo("Acessando o cadastro de tarefa");
		$sessaoUsuario = new Sessao($session);
		$ctrl = new ControladoraPermissaoAdministrativa($req->getParsedBody(), $sessaoUsuario);
		$response = $ctrl->configurar();
		return $res->withHeader('Content-type', 'application/json; charset=UTF-8')->withJson($response);
	});

	$app->get('/permissoes', function(Request $req,  Response $res, $args = []) use ($app, $session) {
		$this->logger->addInfo("Acessando o cadastro de tarefa");
		$sessaoUsuario = new Sessao($session);
		$ctrl = new ControladoraPermissaoAdministrativa($req->getParsedBody(), $sessaoUsuario);
		$response = $ctrl->todosComPermissao();
		return $res->withHeader('Content-type', 'application/json; charset=UTF-8')->withJson($response);
	});

	$app->get('/index/tem-permissao', function(Request $req,  Response $res, $args = []) use ($app, $session) {
		$this->logger->addInfo("Acessando o cadastro de tarefa");
		$sessaoUsuario = new Sessao($session);
		$ctrl = new ControladoraUsuario($req->getParsedBody(), $sessaoUsuario);

		$resposta = $ctrl->comId($sessaoUsuario->idUsuario());
		$temPermissão = false;
		
		$resposta['status'] = ($resposta['conteudo']['administrador']) ? true : false;

		if(!$resposta['status']){
			if(isset($resposta['conteudo']['gruposUsuario']) and count($resposta['conteudo']['gruposUsuario']) > 0){
				foreach ($resposta['conteudo']['gruposUsuario'] as $grupo) {
					if($grupo->getAdministrador()) 
					{
						$resposta['status'] = true;
						break;
					}
				}
			}
		}
		
		if($resposta['status']) $resposta['mensagem'] = 'Usuario autorizado.';
		else $resposta['mensagem'] = 'Usuario não possui permissão para acessar funcionalidade';
		return $res->withHeader('Content-type', 'application/json; charset=UTF-8')->withJson($resposta);
	});

	$app->get('/index/atividades-usuario', function(Request $req,  Response $res, $args = []) use ($app, $session) {
		$this->logger->addInfo("Acessando o cadastro de tarefa");
		$sessaoUsuario = new Sessao($session);
		$ctrl = new ControladoraUsuario($req->getParsedBody(), $sessaoUsuario);
		$resposta = $ctrl->comId($sessaoUsuario->idUsuario());
		
		return $res->withHeader('Content-type', 'application/json; charset=UTF-8')->withJson($resposta);
	});
// Início para permissoes
?>
