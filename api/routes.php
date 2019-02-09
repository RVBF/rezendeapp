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
	return $res->withHeader('Content-type', 'application/json; charset=UTF-8')->withJson(json_decode(stripslashes(JSON::encode($response))));

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

// Início das rotas para setor
$app->get('/setor', function(Request $req,  Response $res, $args = []) use ($app, $session) {
	$this->logger->addInfo("Acessando listagem de setor");	
	$sessaoUsuario = new Sessao($session);
	$ctrl = new ControladoraSetor($req->getQueryParams(), $sessaoUsuario);
	$response = $ctrl->todos();
	return $res->withHeader('Content-type', 'application/json; charset=UTF-8')->withJson(json_decode(stripslashes(JSON::encode($response))));
});

$app->post('/setor', function(Request $req,  Response $res, $args = []) use ($app, $session) {
	$this->logger->addInfo("Acessando o cadastro de setor");
	$sessaoUsuario = new Sessao($session);
	$ctrl = new ControladoraSetor($req->getParsedBody(), $sessaoUsuario);
	$response = $ctrl->adicionar();
	return $res->withHeader('Content-type', 'application/json; charset=UTF-8')->withJson(JSON::decode(json_encode($response)));

});

$app->put('/setor', function(Request $req,  Response $res, $args = []) use ($app, $session) {
	$this->logger->addInfo("Acessando a atualização de categorias");
	$sessaoUsuario = new Sessao($session);
	$ctrl = new ControladoraSetor($req->getParsedBody(), $sessaoUsuario);
	$response = $ctrl->atualizar();
	return $res->withHeader('Content-type', 'application/json; charset=UTF-8')->withJson(JSON::decode(json_encode($response)));

});

$app->delete('/setor/{id}', function(Request $req,  Response $res, $args = []) use ($app, $session) {
	$this->logger->addInfo("Deletando a categoria de id ". $args['id'] . '.');
	$sessaoUsuario = new Sessao($session);
	$ctrl = new ControladoraSetor($req->getParsedBody(), $sessaoUsuario);
	$response = $ctrl->remover($args['id']);
	return $res->withHeader('Content-type', 'application/json; charset=UTF-8')->withJson(JSON::decode(json_encode($response)));
});

// Início das rotas para loja
$app->get('/loja', function(Request $req,  Response $res, $args = []) use ($app, $session) {
	$this->logger->addInfo("Acessando listagem de lojas");	
	$sessaoUsuario = new Sessao($session);
	$ctrl = new ControladoraLoja($req->getQueryParams(), $sessaoUsuario);
	$response = $ctrl->todos();
	return $res->withHeader('Content-type', 'application/json; charset=UTF-8')->withJson(json_decode(stripslashes(JSON::encode($response))));
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
$app->get('/tarefa', function(Request $req,  Response $res, $args = []) use ($app, $session) {
	$this->logger->addInfo("Acessando listagem de tarefa");	
	$sessaoUsuario = new Sessao($session);
	$ctrl = new ControladoraTarefa($req->getQueryParams(), $sessaoUsuario);
	$response = $ctrl->todos();
	return $res->withHeader('Content-type', 'application/json; charset=UTF-8')->withJson(json_decode(stripslashes(JSON::encode($response))));
});

$app->put('/tarefa', function(Request $req,  Response $res, $args = []) use ($app, $session) {
	$this->logger->addInfo("Acessando a atualização de tarefas");
	$sessaoUsuario = new Sessao($session);
	$ctrl = new ControladoraTarefa($req->getParsedBody(), $sessaoUsuario);
	$response = $ctrl->atualizar();
	return $res->withHeader('Content-type', 'application/json; charset=UTF-8')->withJson(JSON::decode(json_encode($response)));

});

$app->post('/tarefa', function(Request $req,  Response $res, $args = []) use ($app, $session) {
	$this->logger->addInfo("Acessando o cadastro de tarefa");
	$sessaoUsuario = new Sessao($session);
	$ctrl = new ControladoraTarefa($req->getParsedBody(), $sessaoUsuario);
	$response = $ctrl->adicionar();
	return $res->withHeader('Content-type', 'application/json; charset=UTF-8')->withJson(JSON::decode(json_encode($response)));

});

$app->delete('/tarefa/{id}', function(Request $req,  Response $res, $args = []) use ($app, $session) {
	$this->logger->addInfo("Deletando a categoria de id ". $args['id'] . '.');
	$sessaoUsuario = new Sessao($session);
	$ctrl = new ControladoraTarefa($req->getParsedBody(), $sessaoUsuario);
	$response = $ctrl->remover($args['id']);
	return $res->withHeader('Content-type', 'application/json; charset=UTF-8')->withJson(json_encode($response));
});

// Início das rotas para pergunta
$app->get('/tarefa/{idTarefa}/pergunta', function(Request $req,  Response $res, $args = []) use ($app, $session) {
	$this->logger->addInfo("Acessando listagem de tarefa");	
	$sessaoUsuario = new Sessao($session);
	$ctrl = new ControladoraPergunta($req->getQueryParams(), $sessaoUsuario);
	$response = $ctrl->todos($args['idTarefa']);
	return $res->withHeader('Content-type', 'application/json; charset=UTF-8')->withJson(json_decode(stripslashes(JSON::encode($response))));

});

$app->get('/tarefa/{idTarefa}/pergunta/tarefa-com-id', function(Request $req,  Response $res, $args = []) use ($app, $session) {
	$this->logger->addInfo("Acessando listagem de tarefa");	
	$sessaoUsuario = new Sessao($session);
	$ctrl = new ControladoraPergunta($req->getQueryParams(), $sessaoUsuario);
	$response = $ctrl->comTarefaId($args['idTarefa']);
	return $res->withHeader('Content-type', 'application/json; charset=UTF-8')->withJson(json_decode(stripslashes(JSON::encode($response))));

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
	$ctrl = new ControladoraPergunta($req->getParsedBody(), $sessaoUsuario);
	$response = $ctrl->remover($args['id'], $args['idTarefa']);
	return $res->withHeader('Content-type', 'application/json; charset=UTF-8')->withJson(JSON::decode(json_encode($response)));
});

$app->get('/pergunta/{id}', function(Request $req,  Response $res, $args = []) use ($app, $session) {
	$this->logger->addInfo("Procurando a categoria de id ". $args['id'] . '.');
	$sessaoUsuario = new Sessao($session);
	$ctrl = new ControladoraPergunta($req->getParsedBody(), $sessaoUsuario);
	$response = $ctrl->comIdPergunta($args['id']);
	return $res->withHeader('Content-type', 'application/json; charset=UTF-8')->withJson(JSON::decode(json_encode($response)));
});

// Início das rotas para usuario
$app->get('/usuario', function(Request $req,  Response $res, $args = []) use ($app, $session) {
	$this->logger->addInfo("Acessando listagem de usuario");	
	$sessaoUsuario = new Sessao($session);
	$ctrl = new ControladoraUsuario($req->getQueryParams(), $sessaoUsuario);
	$response = $ctrl->todos();
	return $res->withHeader('Content-type', 'application/json; charset=UTF-8')->withJson(json_decode(stripslashes(JSON::encode($response))));

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
	return $res->withHeader('Content-type', 'application/json; charset=UTF-8')->withJson(json_decode(stripslashes(JSON::encode($response))));

});

$app->post('/grupo-usuario', function(Request $req,  Response $res, $args = []) use ($app, $session) {
	$this->logger->addInfo("Acessando o cadastro de tarefa");
	$sessaoUsuario = new Sessao($session);
	$ctrl = new ControladoraGrupoUsuario($req->getParsedBody(), $sessaoUsuario);
	$response = $ctrl->adicionar();
	return $res->withHeader('Content-type', 'application/json; charset=UTF-8')->withJson(JSON::decode(json_encode($response)));

});

$app->put('/grupo-usuario', function(Request $req,  Response $res, $args = []) use ($app, $session) {
	$this->logger->addInfo("Acessando a atualização de tarefas");
	$sessaoUsuario = new Sessao($session);
	$ctrl = new ControladoraGrupoUsuario($req->getParsedBody(), $sessaoUsuario);
	$response = $ctrl->atualizar();
	return $res->withHeader('Content-type', 'application/json; charset=UTF-8')->withJson(JSON::decode(json_encode($response)));

});

$app->delete('/grupo-usuario/{id}', function(Request $req,  Response $res, $args = []) use ($app, $session) {
	$this->logger->addInfo("Deletando o grupo de usuário de id ". $args['id'] . '.');
	$sessaoUsuario = new Sessao($session);
	$ctrl = new ControladoraGrupoUsuario($req->getParsedBody(), $sessaoUsuario);
	$response = $ctrl->remover($args['id']);
	return $res->withHeader('Content-type', 'application/json; charset=UTF-8')->withJson(JSON::decode(json_encode($response)));
});

$app->get('/resposta/{tarefaId}', function(Request $req,  Response $res, $args = []) use ($app, $session) {
	$this->logger->addInfo("Acessando o cadastro de tarefa");
	$sessaoUsuario = new Sessao($session);
	$ctrl = new ControladoraResposta($req->getQueryParams(), $sessaoUsuario);
	$response = $ctrl->todos($args['tarefaId']);
	return $res->withHeader('Content-type', 'application/json; charset=UTF-8')->withJson(json_decode(stripslashes(JSON::encode($response))));
});


// $app->get('/tarefa', function(Request $req,  Response $res, $args = []) use ($app, $session) {
// 	$this->logger->addInfo("Acessando listagem de tarefa");	
// 	$sessaoUsuario = new Sessao($session);
// 	$ctrl = new ControladoraTarefa($req->getQueryParams(), $sessaoUsuario);
// 	$response = $ctrl->todos();
// 	return $res->withHeader('Content-type', 'application/json; charset=UTF-8')->withJson(json_decode(stripslashes(JSON::encode($response))));
// });
$app->post('/resposta', function(Request $req,  Response $res, $args = []) use ($app, $session) {
	$this->logger->addInfo("Acessando o cadastro de tarefa");
	$sessaoUsuario = new Sessao($session);
	$ctrl = new ControladoraResposta($req->getParsedBody(), $sessaoUsuario);
	$response = $ctrl->adicionar();
	return $res->withHeader('Content-type', 'application/json; charset=UTF-8')->withJson(JSON::decode(json_encode($response)));
});


// Início para permissoes
$app->post('/permissoes', function(Request $req,  Response $res, $args = []) use ($app, $session) {
	$this->logger->addInfo("Acessando o cadastro de tarefa");
	$sessaoUsuario = new Sessao($session);
	$ctrl = new ControladoraPermissaoAdministrativa($req->getParsedBody(), $sessaoUsuario);
	$response = $ctrl->configurar();
	return $res->withHeader('Content-type', 'application/json; charset=UTF-8')->withJson(JSON::decode(json_encode($response)));
});

$app->get('/permissoes', function(Request $req,  Response $res, $args = []) use ($app, $session) {
	$this->logger->addInfo("Acessando o cadastro de tarefa");
	$sessaoUsuario = new Sessao($session);
	$ctrl = new ControladoraPermissaoAdministrativa($req->getParsedBody(), $sessaoUsuario);
	$response = $ctrl->todosComPermissao();
	return $res->withHeader('Content-type', 'application/json; charset=UTF-8')->withJson(JSON::decode(json_encode($response)));
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
	return $res->withHeader('Content-type', 'application/json; charset=UTF-8')->withJson(JSON::decode(json_encode($resposta)));
});
?>