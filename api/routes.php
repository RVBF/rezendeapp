<?php
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;
use \phputil\JSON;

// Início das rotas para setor
	$caminho = '/setor';
	$metodo = 'get';

	$app->get($caminho, function(Request $req, Response $res, $args = []) use ($app, $session, $caminho, $metodo) {
		$sessaoUsuario = new Sessao($session);

		if(Acesso::vericarAcesso($sessaoUsuario->idUsuario(), $caminho, $metodo)) {
			$this->logger->addInfo("Acessando listagem de setores");

			$ctrl = new ControladoraSetor($req->getQueryParams(), $sessaoUsuario);
			$response = $ctrl->todos();
		} else {
			$response = [
				'acessoNegado'=> true,
				'metodo'=> $metodo
			];
		}

		return $res->withHeader('Content-type', 'application/json; charset=UTF-8')->withJson($response);
	});


	$caminho = '/setor/opcoes';
	$metodo = 'get';

	$app->get($caminho, function(Request $req, Response $res, $args = []) use ($app, $session, $caminho, $metodo) {
		$this->logger->addInfo('Acessando as opções de setores.');

		$sessaoUsuario = new Sessao($session);
		$ctrl = new ControladoraSetor($req->getQueryParams(), $sessaoUsuario);
		$response = $ctrl->todosOpcoes();

		return $res->withHeader('Content-type', 'application/json; charset=UTF-8')->withJson($response);
	});


	$caminho = '/setor/{id}';
	$metodo = 'get';

	$app->get($caminho, function(Request $req, Response $res, $args = []) use ($app, $session, $caminho, $metodo) {
		$sessaoUsuario = new Sessao($session);

		if(Acesso::vericarAcesso($sessaoUsuario->idUsuario(), $caminho, $metodo)) {
			$this->logger->addInfo("Acessando listagem de setores");

			$ctrl = new ControladoraSetor($req->getQueryParams(), $sessaoUsuario);
			$response = $ctrl->comId($args['id']);
		} else {
			$response = [
				'acessoNegado'=> true,
				'metodo'=> $metodo
			];
		}

		return $res->withHeader('Content-type', 'application/json; charset=UTF-8')->withJson($response);
	});


	$caminho = '/setor';
	$metodo = 'post';

	$app->post($caminho, function(Request $req, Response $res, $args = []) use ($app, $session, $caminho, $metodo) {
		$sessaoUsuario = new Sessao($session);

		if(Acesso::vericarAcesso($sessaoUsuario->idUsuario(), $caminho, $metodo)) {
			$this->logger->addInfo("Acessando o cadastro de setor");

			$ctrl = new ControladoraSetor($req->getParsedBody(), $sessaoUsuario);
			$response = $ctrl->adicionar();
		} else {
			$response = [
				'acessoNegado'=> true,
				'metodo'=> $metodo
			];
		}

		return $res->withHeader('Content-type', 'application/json; charset=UTF-8')->withJson($response);
	});


	$caminho = '/setor';
	$metodo = 'put';

	$app->put($caminho, function(Request $req, Response $res, $args = []) use ($app, $session, $caminho, $metodo) {
		$sessaoUsuario = new Sessao($session);

		if(Acesso::vericarAcesso($sessaoUsuario->idUsuario(), $caminho, $metodo)) {
			$this->logger->addInfo("Acessando a atualização de setors");

			$ctrl = new ControladoraSetor($req->getParsedBody(), $sessaoUsuario);
			$response = $ctrl->atualizar();
		} else {
			$response = [
				'acessoNegado'=> true,
				'metodo'=> $metodo
			];
		}

		return $res->withHeader('Content-type', 'application/json; charset=UTF-8')->withJson($response);
	});


	$caminho = '/setor/{id}';
	$metodo = 'delete';

	$app->delete($caminho, function(Request $req, Response $res, $args = []) use ($app, $session, $caminho, $metodo) {
		$sessaoUsuario = new Sessao($session);

		if(Acesso::vericarAcesso($sessaoUsuario->idUsuario(), $caminho, $metodo)) {
			$this->logger->addInfo("Deletando a setor de id ". $args['id'] . '.');

			$ctrl = new ControladoraSetor($req->getParsedBody(), $sessaoUsuario);
			$response = $ctrl->remover($args['id']);
		} else {
			$response = [
				'acessoNegado'=> true,
				'metodo'=> $metodo
			];
		}

		return $res->withHeader('Content-type', 'application/json; charset=UTF-8')->withJson($response);
	});
// Fim das rotas para Setor

// Início das rotas para Questionários
	$caminho = '/questionario';
	$metodo = 'get';

	$app->get($caminho, function(Request $req, Response $res, $args = []) use ($app, $session, $caminho, $metodo) {
		$sessaoUsuario = new Sessao($session);

		if(Acesso::vericarAcesso($sessaoUsuario->idUsuario(), $caminho, $metodo)) {
			$this->logger->addInfo("Acessando listagem de questionarioes");

			$ctrl = new ControladoraQuestionario($req->getQueryParams(), $sessaoUsuario);
			$response = $ctrl->todos();
		} else {
			$response = [
				'acessoNegado'=> true,
				'metodo'=> $metodo
			];
		}

		return $res->withHeader('Content-type', 'application/json; charset=UTF-8')->withJson($response);
	});


	$caminho = '/questionario/opcoes';
	$metodo = 'get';

	$app->get($caminho, function(Request $req, Response $res, $args = []) use ($app, $session, $caminho, $metodo) {
		$this->logger->addInfo('Acessando as opções de questionários.');

		$sessaoUsuario = new Sessao($session);
		$ctrl = new ControladoraQuestionario($req->getQueryParams(), $sessaoUsuario);
		$response = $ctrl->todosOpcoes();

		return $res->withHeader('Content-type', 'application/json; charset=UTF-8')->withJson($response);
	});


	$caminho = '/questionario/{id}';
	$metodo = 'get';

	$app->get($caminho, function(Request $req, Response $res, $args = []) use ($app, $session, $caminho, $metodo) {
		$sessaoUsuario = new Sessao($session);

		if(Acesso::vericarAcesso($sessaoUsuario->idUsuario(), $caminho, $metodo)) {
			$this->logger->addInfo("Acessando listagem de questionarioes");

			$ctrl = new ControladoraQuestionario($req->getQueryParams(), $sessaoUsuario);
			$response = $ctrl->comId($args['id']);
		} else {
			$response = [
				'acessoNegado'=> true,
				'metodo'=> $metodo
			];
		}

		return $res->withHeader('Content-type', 'application/json; charset=UTF-8')->withJson($response);
	});


	$caminho = '/questionario';
	$metodo = 'post';

	$app->post($caminho, function(Request $req, Response $res, $args = []) use ($app, $session, $caminho, $metodo) {
		$sessaoUsuario = new Sessao($session);

		if(Acesso::vericarAcesso($sessaoUsuario->idUsuario(), $caminho, $metodo)) {
			$this->logger->addInfo("Acessando o cadastro de questionario");

			$ctrl = new ControladoraQuestionario($req->getParsedBody(), $sessaoUsuario);
			$response = $ctrl->adicionar();
		} else {
			$response = [
				'acessoNegado'=> true,
				'metodo'=> $metodo
			];
		}

		return $res->withHeader('Content-type', 'application/json; charset=UTF-8')->withJson($response);
	});


	$caminho = '/questionario';
	$metodo = 'put';

	$app->put($caminho, function(Request $req, Response $res, $args = []) use ($app, $session, $caminho, $metodo) {
		$sessaoUsuario = new Sessao($session);

		if(Acesso::vericarAcesso($sessaoUsuario->idUsuario(), $caminho, $metodo)) {
			$this->logger->addInfo("Acessando a atualização de questionários");

			$ctrl = new ControladoraQuestionario($req->getParsedBody(), $sessaoUsuario);
			$response = $ctrl->atualizar();
		} else {
			$response = [
				'acessoNegado'=> true,
				'metodo'=> $metodo
			];
		}

		return $res->withHeader('Content-type', 'application/json; charset=UTF-8')->withJson($response);
	});


	$caminho = '/questionario/{id}';
	$metodo = 'delete';

	$app->delete($caminho, function(Request $req, Response $res, $args = []) use ($app, $session, $caminho, $metodo) {
		$sessaoUsuario = new Sessao($session);

		if(Acesso::vericarAcesso($sessaoUsuario->idUsuario(), $caminho, $metodo)) {
			$this->logger->addInfo("Deletando a questionário de id ". $args['id'] . '.');

			$ctrl = new ControladoraQuestionario($req->getParsedBody(), $sessaoUsuario);
			$response = $ctrl->remover($args['id']);
		} else {
			$response = [
				'acessoNegado'=> true,
				'metodo'=> $metodo
			];
		}

		return $res->withHeader('Content-type', 'application/json; charset=UTF-8')->withJson($response);
	});
// Fim das rotas para Questionários

// Início das rotas para loja
	$caminho = '/loja';
	$metodo = 'get';

	$app->get($caminho, function(Request $req, Response $res, $args = []) use ($app, $session, $caminho, $metodo) {
		$sessaoUsuario = new Sessao($session);

		if(Acesso::vericarAcesso($sessaoUsuario->idUsuario(), $caminho, $metodo)) {
			$this->logger->addInfo('Acessando listagem de lojas');

			$ctrl = new ControladoraLoja($req->getQueryParams(), $sessaoUsuario);
			$response = $ctrl->todos();
		} else {
			$response = [
				'acessoNegado'=> true,
				'metodo'=> $metodo
			];
		}

		return $res->withHeader('Content-type', 'application/json; charset=UTF-8')->withJson($response);
	});


	$caminho = '/loja/opcoes';
	$metodo = 'get';

	$app->get($caminho, function(Request $req, Response $res, $args = []) use ($app, $session, $caminho, $metodo) {
		$this->logger->addInfo('Acessando listagem de lojas');

		$sessaoUsuario = new Sessao($session);
		$ctrl = new ControladoraLoja($req->getQueryParams(), $sessaoUsuario);
		$response = $ctrl->todosOpcoes();

		return $res->withHeader('Content-type', 'application/json; charset=UTF-8')->withJson($response);
	});


	$caminho = '/loja/{id}';
	$metodo = 'get';

	$app->get($caminho, function(Request $req, Response $res, $args = []) use ($app, $session, $caminho, $metodo) {
		$sessaoUsuario = new Sessao($session);

		if(Acesso::vericarAcesso($sessaoUsuario->idUsuario(), $caminho, $metodo)) {
			$this->logger->addInfo("Acessando a loja de ID " . $args['id'] . ".");

			$ctrl = new ControladoraLoja($req->getQueryParams(), $sessaoUsuario);
			$response = $ctrl->comId($args['id']);
		} else {
			$response = [
				'acessoNegado'=> true,
				'metodo'=> $metodo
			];
		}

		return $res->withHeader('Content-type', 'application/json; charset=UTF-8')->withJson($response);
	});


	$caminho = '/loja';
	$metodo = 'post';

	$app->post($caminho, function(Request $req, Response $res, $args = []) use ($app, $session, $caminho, $metodo) {
		$sessaoUsuario = new Sessao($session);

		if(Acesso::vericarAcesso($sessaoUsuario->idUsuario(), $caminho, $metodo)) {
			$this->logger->addInfo("Acessando o cadastro de loja");

			$ctrl = new ControladoraLoja($req->getParsedBody(), $sessaoUsuario);
			$response = $ctrl->adicionar();
		} else {
			$response = [
				'acessoNegado'=> true,
				'metodo'=> $metodo
			];
		}

		return $res->withHeader('Content-type', 'application/json; charset=UTF-8')->withJson($response);
	});


	$caminho = '/loja';
	$metodo = 'put';

	$app->put($caminho, function(Request $req, Response $res, $args = []) use ($app, $session, $caminho, $metodo) {
		$sessaoUsuario = new Sessao($session);

		if(Acesso::vericarAcesso($sessaoUsuario->idUsuario(), $caminho, $metodo)) {
			$this->logger->addInfo("Acessando a atualização de lojas");

			$ctrl = new ControladoraLoja($req->getParsedBody(), $sessaoUsuario);
			$response = $ctrl->atualizar();
		} else {
			$response = [
				'acessoNegado'=> true,
				'metodo'=> $metodo
			];
		}

		return $res->withHeader('Content-type', 'application/json; charset=UTF-8')->withJson($response);
	});


	$caminho = '/loja/{id}';
	$metodo = 'delete';

	$app->delete($caminho, function(Request $req, Response $res, $args = []) use ($app, $session, $caminho, $metodo) {
		$sessaoUsuario = new Sessao($session);

		if(Acesso::vericarAcesso($sessaoUsuario->idUsuario(), $caminho, $metodo)) {
			$this->logger->addInfo("Deletando a loja de id ". $args['id'] . '.');

			$ctrl = new ControladoraLoja($req->getParsedBody(), $sessaoUsuario);
			$response = $ctrl->remover($args['id']);
		} else {
			$response = [
				'acessoNegado'=> true,
				'metodo'=> $metodo
			];
		}

		return $res->withHeader('Content-type', 'application/json; charset=UTF-8')->withJson($response);
	});
// Fim das rotas para loja

// Início das rotas para questionamento
	$caminho = '/questionamento';
	$metodo = 'get';

	$app->get($caminho, function(Request $req, Response $res, $args = []) use ($app, $session, $caminho, $metodo) {
		$sessaoUsuario = new Sessao($session);

		if(Acesso::vericarAcesso($sessaoUsuario->idUsuario(), $caminho, $metodo)) {
			$this->logger->addInfo("Acessando listagem de lojas");

			$ctrl = new ControladoraQuestionamento($req->getQueryParams(), $sessaoUsuario);
			$response = $ctrl->todos();
			Util::printr($response);
		} else {
			$response = [
				'acessoNegado'=> true,
				'metodo'=> $metodo
			];
		}

		return $res->withHeader('Content-type', 'application/json; charset=UTF-8')->withJson($response);
	});


	$caminho = '/questionamento/{id}';
	$metodo = 'get';

	$app->get($caminho, function(Request $req, Response $res, $args = []) use ($app, $session, $caminho, $metodo) {
		$sessaoUsuario = new Sessao($session);

		if(Acesso::vericarAcesso($sessaoUsuario->idUsuario(), $caminho, $metodo)) {
			$this->logger->addInfo("Acessando listagem de lojas");

			$ctrl = new ControladoraQuestionamento($req->getQueryParams(), $sessaoUsuario);
			$response = $ctrl->todos($args['id']);
		} else {
			$response = [
				'acessoNegado'=> true,
				'metodo'=> $metodo
			];
		}

		return $res->withHeader('Content-type', 'application/json; charset=UTF-8')->withJson($response);
	});


	$caminho = '/questionamento';
	$metodo = 'post';

	$app->post($caminho, function(Request $req, Response $res, $args = []) use ($app, $session, $caminho, $metodo) {
		$sessaoUsuario = new Sessao($session);

		if(Acesso::vericarAcesso($sessaoUsuario->idUsuario(), $caminho, $metodo)) {
			$this->logger->addInfo("Acessando o cadastro de loja");

			$ctrl = new ControladoraQuestionamento($req->getParsedBody(), $sessaoUsuario);
			$response = $ctrl->executar();
		} else {
			$response = [
				'acessoNegado'=> true,
				'metodo'=> $metodo
			];
		}

		return $res->withHeader('Content-type', 'application/json; charset=UTF-8')->withJson($response);
	});


	$caminho = '/questionamento';
	$metodo = 'put';

	$app->put($caminho, function(Request $req, Response $res, $args = []) use ($app, $session, $caminho, $metodo) {
		$sessaoUsuario = new Sessao($session);

		if(Acesso::vericarAcesso($sessaoUsuario->idUsuario(), $caminho, $metodo)) {
			$this->logger->addInfo("Acessando a atualização de questionamentos");

			$ctrl = new ControladoraQuestionamento($req->getParsedBody(), $sessaoUsuario);
			$response = $ctrl->atualizar();
		} else {
			$response = [
				'acessoNegado'=> true,
				'metodo'=> $metodo
			];
		}

		return $res->withHeader('Content-type', 'application/json; charset=UTF-8')->withJson($response);
	});


	$caminho = '/questionamentos/{id}';
	$metodo = 'delete';

	$app->delete($caminho, function(Request $req, Response $res, $args = []) use ($app, $session, $caminho, $metodo) {
		$sessaoUsuario = new Sessao($session);

		if(Acesso::vericarAcesso($sessaoUsuario->idUsuario(), $caminho, $metodo)) {
			$this->logger->addInfo("Deletando a questionamento de id ". $args['id'] . '.');

			$ctrl = new ControladoraQuestionamento($req->getParsedBody(), $sessaoUsuario);
			$response = $ctrl->remover($args['id']);
		} else {
			$response = [
				'acessoNegado'=> true,
				'metodo'=> $metodo
			];
		}

		return $res->withHeader('Content-type', 'application/json; charset=UTF-8')->withJson($response);
	});
// Fim das rotas para questionamentos

// Início das rotas para checklist
	$caminho = '/checklist';
	$metodo = 'get';

	$app->get($caminho, function(Request $req, Response $res, $args = []) use ($app, $session, $caminho, $metodo) {
		$sessaoUsuario = new Sessao($session);

		if(Acesso::vericarAcesso($sessaoUsuario->idUsuario(), $caminho, $metodo)) {
			$this->logger->addInfo("Acessando listagem de tarefa");

			$ctrl = new ControladoraChecklist($req->getQueryParams(), $sessaoUsuario);
			$response = $ctrl->todos();
		} else {
			$response = [
				'acessoNegado'=> true,
				'naoRedirecionar'=> true,
				'metodo'=> $metodo
			];
		}

		return $res->withHeader('Content-type', 'application/json; charset=UTF-8')->withJson($response);
	});


	$caminho = '/checklist/questionamentos/{id}';
	$metodo = 'get';

	$app->get($caminho, function(Request $req, Response $res, $args = []) use ($app, $session, $caminho, $metodo) {
		$sessaoUsuario = new Sessao($session);

		if(Acesso::vericarAcesso($sessaoUsuario->idUsuario(), $caminho, $metodo)) {
			$this->logger->addInfo("Acessando listagem de checklist");

			$ctrl = new ControladoraChecklist($req->getQueryParams(), $sessaoUsuario);
			$response = $ctrl->getQuestionamentosParaExecucao($args['id']);
		} else {
			$response = [
				'acessoNegado'=> true,
				'metodo'=> $metodo
			];
		}

		return $res->withHeader('Content-type', 'application/json; charset=UTF-8')->withJson($response);
	});


	$caminho = '/checklist';
	$metodo = 'put';

	$app->put($caminho, function(Request $req, Response $res, $args = []) use ($app, $session, $caminho, $metodo) {
		$sessaoUsuario = new Sessao($session);

		if(Acesso::vericarAcesso($sessaoUsuario->idUsuario(), $caminho, $metodo)) {
			$this->logger->addInfo("Acessando a atualização de checklists");

			$ctrl = new ControladoraChecklist($req->getParsedBody(), $sessaoUsuario);
			$response = $ctrl->atualizar();
		} else {
			$response = [
				'acessoNegado'=> true,
				'metodo'=> $metodo
			];
		}

		return $res->withHeader('Content-type', 'application/json; charset=UTF-8')->withJson($response);
	});


	$caminho = '/checklist';
	$metodo = 'post';

	$app->post($caminho, function(Request $req, Response $res, $args = []) use ($app, $session, $caminho, $metodo) {
		$sessaoUsuario = new Sessao($session);

		if(Acesso::vericarAcesso($sessaoUsuario->idUsuario(), $caminho, $metodo)) {
			$this->logger->addInfo("Acessando o cadastro de checklist");

			$ctrl = new ControladoraChecklist($req->getParsedBody(), $sessaoUsuario);
			$response = $ctrl->adicionar();
		} else {
			$response = [
				'acessoNegado'=> true,
				'metodo'=> $metodo
			];
		}

		return $res->withHeader('Content-type', 'application/json; charset=UTF-8')->withJson($response);
	});


	$caminho = '/checklist/{id}';
	$metodo = 'get';

	$app->get($caminho, function(Request $req, Response $res, $args = []) use ($app, $session, $caminho, $metodo) {
		$sessaoUsuario = new Sessao($session);

		if(Acesso::vericarAcesso($sessaoUsuario->idUsuario(), $caminho, $metodo)) {
			$this->logger->addInfo("Acessando listagem de checklists");

			$ctrl = new ControladoraChecklist($req->getQueryParams(), $sessaoUsuario);
			$response = $ctrl->comId($args['id']);
		} else {
			$response = [
				'acessoNegado'=> true,
				'metodo'=> $metodo
			];
		}

		return $res->withHeader('Content-type', 'application/json; charset=UTF-8')->withJson($response);
	});


	$caminho = '/checklist/{id}';
	$metodo = 'delete';

	$app->delete($caminho, function(Request $req, Response $res, $args = []) use ($app, $session, $caminho, $metodo) {
		$sessaoUsuario = new Sessao($session);

		if(Acesso::vericarAcesso($sessaoUsuario->idUsuario(), $caminho, $metodo)) {
			$this->logger->addInfo("Deletando a checklist de id ". $args['id'] . '.');

			$ctrl = new ControladoraChecklist($req->getParsedBody(), $sessaoUsuario);
			$response = $ctrl->remover($args['id']);
		} else {
			$response = [
				'acessoNegado'=> true,
				'metodo'=> $metodo
			];
		}

		return $res->withHeader('Content-type', 'application/json; charset=UTF-8')->withJson($response);
	});
// Fim das rotas para checklist

// Início das rotas para plano-acao
	$caminho = '/plano-acao';
	$metodo = 'get';

	$app->get($caminho, function(Request $req, Response $res, $args = []) use ($app, $session, $caminho, $metodo) {
		$sessaoUsuario = new Sessao($session);

		if(Acesso::vericarAcesso($sessaoUsuario->idUsuario(), $caminho, $metodo)) {
			$this->logger->addInfo("Acessando listagem de plano-acao");

			$ctrl = new ControladoraPlanoAcao($req->getQueryParams(), $sessaoUsuario);
			$response = $ctrl->todos();
		} else {
			$response = [
				'acessoNegado'=> true,
				'metodo'=> $metodo
			];
		}

		return $res->withHeader('Content-type', 'application/json; charset=UTF-8')->withJson($response);
	});


	$caminho = '/plano-acao/pendentes/{id}';
	$metodo = 'get';

	$app->get($caminho, function(Request $req, Response $res, $args = []) use ($app, $session, $caminho, $metodo) {
		$sessaoUsuario = new Sessao($session);

		if(Acesso::vericarAcesso($sessaoUsuario->idUsuario(), $caminho, $metodo)) {
			$this->logger->addInfo("Acessando listagem de plano-acao");

			$ctrl = new ControladoraPlanoAcao($req->getQueryParams(), $sessaoUsuario);
			$response = $ctrl->todosPendentes($args['id']);
		} else {
			$response = [
				'acessoNegado'=> true,
				'metodo'=> $metodo
			];
		}

		return $res->withHeader('Content-type', 'application/json; charset=UTF-8')->withJson($response);
	});


	$caminho = '/plano-acao/{id}';
	$metodo = 'get';

	$app->get($caminho, function(Request $req, Response $res, $args = []) use ($app, $session, $caminho, $metodo) {
		$sessaoUsuario = new Sessao($session);

		if(Acesso::vericarAcesso($sessaoUsuario->idUsuario(), $caminho, $metodo)) {
			$this->logger->addInfo("Acessando listagem de plano-acao");

			$ctrl = new ControladoraPlanoAcao($req->getQueryParams(), $sessaoUsuario);
			$response = $ctrl->comId($args['id']);
		} else {
			$response = [
				'acessoNegado'=> true,
				'metodo'=> $metodo
			];
		}

		return $res->withHeader('Content-type', 'application/json; charset=UTF-8')->withJson($response);
	});


	$caminho = '/plano-acao';
	$metodo = 'post';

	$app->post($caminho, function(Request $req, Response $res, $args = []) use ($app, $session, $caminho, $metodo) {
		$sessaoUsuario = new Sessao($session);

		if(Acesso::vericarAcesso($sessaoUsuario->idUsuario(), $caminho, $metodo)) {
			$this->logger->addInfo("Acessando o cadastro de plano-acao");

			$ctrl = new ControladoraPlanoAcao($req->getParsedBody(), $sessaoUsuario);
			$response = $ctrl->adicionar();
		} else {
			$response = [
				'acessoNegado'=> true,
				'metodo'=> $metodo
			];
		}

		return $res->withHeader('Content-type', 'application/json; charset=UTF-8')->withJson($response);
	});


	$caminho = '/plano-acao/confirmar-responsabilidade';
	$metodo = 'post';

	$app->post($caminho, function(Request $req, Response $res, $args = []) use ($app, $session, $caminho, $metodo) {
		$sessaoUsuario = new Sessao($session);

		if(Acesso::vericarAcesso($sessaoUsuario->idUsuario(), $caminho, $metodo)) {
			$this->logger->addInfo("Acessando o cadastro de plano-acao");

			$ctrl = new ControladoraPlanoAcao($req->getParsedBody(), $sessaoUsuario);
			$response = $ctrl->confirmarResponsabilidade();
		} else {
			$response = [
				'acessoNegado'=> true,
				'metodo'=> $metodo
			];
		}

		return $res->withHeader('Content-type', 'application/json; charset=UTF-8')->withJson($response);
	});


	$caminho = '/plano-acao/executar';
	$metodo = 'post';

	$app->post($caminho, function(Request $req, Response $res, $args = []) use ($app, $session, $caminho, $metodo) {
		$sessaoUsuario = new Sessao($session);

		if(Acesso::vericarAcesso($sessaoUsuario->idUsuario(), $caminho, $metodo)) {
			$this->logger->addInfo("Acessando o cadastro de plano-acao");

			$ctrl = new ControladoraPlanoAcao($req->getParsedBody(), $sessaoUsuario);
			$response = $ctrl->executar();
		} else {
			$response = [
				'acessoNegado'=> true,
				'metodo'=> $metodo
			];
		}

		return $res->withHeader('Content-type', 'application/json; charset=UTF-8')->withJson($response);
	});


	$caminho = '/plano-acao';
	$metodo = 'put';

	$app->put($caminho, function(Request $req, Response $res, $args = []) use ($app, $session, $caminho, $metodo) {
		$sessaoUsuario = new Sessao($session);

		if(Acesso::vericarAcesso($sessaoUsuario->idUsuario(), $caminho, $metodo)) {
			$this->logger->addInfo("Acessando a atualização de plano de ação");

			$ctrl = new ControladoraPlanoAcao($req->getParsedBody(), $sessaoUsuario);
			$response = $ctrl->atualizar();
		} else {
			$response = [
				'acessoNegado'=> true,
				'metodo'=> $metodo
			];
		}

		return $res->withHeader('Content-type', 'application/json; charset=UTF-8')->withJson($response);
	});


	$caminho = '/plano-acao/{id}';
	$metodo = 'delete';

	$app->delete($caminho, function(Request $req, Response $res, $args = []) use ($app, $session, $caminho, $metodo) {
		$sessaoUsuario = new Sessao($session);

		if(Acesso::vericarAcesso($sessaoUsuario->idUsuario(), $caminho, $metodo)) {
			$this->logger->addInfo("Deletando a plano de ação de id ". $args['id'] . '.');

			$ctrl = new ControladoraPlanoAcao($req->getParsedBody(), $sessaoUsuario);
			$response = $ctrl->remover($args['id']);
		} else {
			$response = [
				'acessoNegado'=> true,
				'metodo'=> $metodo
			];
		}

		return $res->withHeader('Content-type', 'application/json; charset=UTF-8')->withJson($response);
	});
// Fim das rotas para plano-acao

// Início das rotas para pendencia
	$caminho = '/pendencia';
	$metodo = 'get';

	$app->get($caminho, function(Request $req, Response $res, $args = []) use ($app, $session, $caminho, $metodo) {
		$sessaoUsuario = new Sessao($session);

		if(Acesso::vericarAcesso($sessaoUsuario->idUsuario(), $caminho, $metodo)) {
			$this->logger->addInfo("Acessando listagem de pendencia");

			$ctrl = new ControladoraPendencia($req->getQueryParams(), $sessaoUsuario);
			$response = $ctrl->todos();
		} else {
			$response = [
				'acessoNegado'=> true,
				'metodo'=> $metodo
			];
		}

		return $res->withHeader('Content-type', 'application/json; charset=UTF-8')->withJson($response);
	});


	$caminho = '/pendencia/pendentes/{id}';
	$metodo = 'get';

	$app->get($caminho, function(Request $req, Response $res, $args = []) use ($app, $session, $caminho, $metodo) {
		$sessaoUsuario = new Sessao($session);

		if(Acesso::vericarAcesso($sessaoUsuario->idUsuario(), $caminho, $metodo)) {
			$this->logger->addInfo("Acessando listagem de plano-acao");

			$ctrl = new ControladoraPendencia($req->getQueryParams(), $sessaoUsuario);
			$response = $ctrl->todosPendentes($args['id']);
		} else {
			$response = [
				'acessoNegado'=> true,
				'metodo'=> $metodo
			];
		}

		return $res->withHeader('Content-type', 'application/json; charset=UTF-8')->withJson($response);
	});


	$caminho = '/pendencia/{id}';
	$metodo = 'get';

	$app->get($caminho, function(Request $req, Response $res, $args = []) use ($app, $session, $caminho, $metodo) {
		$sessaoUsuario = new Sessao($session);

		if(Acesso::vericarAcesso($sessaoUsuario->idUsuario(), $caminho, $metodo)) {
			$this->logger->addInfo("Acessando listagem de pendencia");

			$ctrl = new ControladoraPendencia($req->getQueryParams(), $sessaoUsuario);
			$response = $ctrl->comId($args['id']);
		} else {
			$response = [
				'acessoNegado'=> true,
				'metodo'=> $metodo
			];
		}

		return $res->withHeader('Content-type', 'application/json; charset=UTF-8')->withJson($response);
	});


	$caminho = '/pendencia';
	$metodo = 'post';

	$app->post($caminho, function(Request $req, Response $res, $args = []) use ($app, $session, $caminho, $metodo) {
		$sessaoUsuario = new Sessao($session);

		if(Acesso::vericarAcesso($sessaoUsuario->idUsuario(), $caminho, $metodo)) {
			$this->logger->addInfo("Acessando o cadastro de pendência");

			$ctrl = new ControladoraPendencia($req->getParsedBody(), $sessaoUsuario);
			$response = $ctrl->adicionar();
		} else {
			$response = [
				'acessoNegado'=> true,
				'metodo'=> $metodo
			];
		}

		return $res->withHeader('Content-type', 'application/json; charset=UTF-8')->withJson($response);
	});


	$caminho = '/pendencia/confirmar-responsabilidade';
	$metodo = 'post';

	$app->post($caminho, function(Request $req, Response $res, $args = []) use ($app, $session, $caminho, $metodo) {
		$sessaoUsuario = new Sessao($session);

		if(Acesso::vericarAcesso($sessaoUsuario->idUsuario(), $caminho, $metodo)) {
			$this->logger->addInfo("Acessando o cadastro de pendência");

			$ctrl = new ControladoraPendencia($req->getParsedBody(), $sessaoUsuario);
			$response = $ctrl->confirmarResponsabilidade();
		} else {
			$response = [
				'acessoNegado'=> true,
				'metodo'=> $metodo
			];
		}

		return $res->withHeader('Content-type', 'application/json; charset=UTF-8')->withJson($response);
	});


	$caminho = '/pendencia/executar';
	$metodo = 'post';

	$app->post($caminho, function(Request $req, Response $res, $args = []) use ($app, $session, $caminho, $metodo) {
		$sessaoUsuario = new Sessao($session);

		if(Acesso::vericarAcesso($sessaoUsuario->idUsuario(), $caminho, $metodo)) {
			$this->logger->addInfo("Acessando o cadastro de pendência");

			$ctrl = new ControladoraPendencia($req->getParsedBody(), $sessaoUsuario);
			$response = $ctrl->executar();
		} else {
			$response = [
				'acessoNegado'=> true,
				'metodo'=> $metodo
			];
		}

		return $res->withHeader('Content-type', 'application/json; charset=UTF-8')->withJson($response);
	});


	$caminho = '/pendencia';
	$metodo = 'put';

	$app->put($caminho, function(Request $req, Response $res, $args = []) use ($app, $session, $caminho, $metodo) {
		$sessaoUsuario = new Sessao($session);

		if(Acesso::vericarAcesso($sessaoUsuario->idUsuario(), $caminho, $metodo)) {
			$this->logger->addInfo("Acessando a atualização de pendência");

			$ctrl = new ControladoraPendencia($req->getParsedBody(), $sessaoUsuario);
			$response = $ctrl->atualizar();
		} else {
			$response = [
				'acessoNegado'=> true,
				'metodo'=> $metodo
			];
		}

		return $res->withHeader('Content-type', 'application/json; charset=UTF-8')->withJson($response);
	});


	$caminho = '/pendencia/{id}';
	$metodo = 'delete';

	$app->delete($caminho, function(Request $req, Response $res, $args = []) use ($app, $session, $caminho, $metodo) {
		$sessaoUsuario = new Sessao($session);

		if(Acesso::vericarAcesso($sessaoUsuario->idUsuario(), $caminho, $metodo)) {
			$this->logger->addInfo("Deletando a pendência de id ". $args['id'] . '.');

			$ctrl = new ControladoraPendencia($req->getParsedBody(), $sessaoUsuario);
			$response = $ctrl->remover($args['id']);
		} else {
			$response = [
				'acessoNegado'=> true,
				'metodo'=> $metodo
			];
		}

		return $res->withHeader('Content-type', 'application/json; charset=UTF-8')->withJson($response);
	});
// Fim das rotas para  pendencia

// Início das rotas para acessos
	$caminho = '/acesso';
	$metodo = 'get';

	$app->get($caminho, function(Request $req, Response $res, $args = []) use ($app, $session, $caminho, $metodo) {
		$sessaoUsuario = new Sessao($session);

		if(Acesso::vericarAcesso($sessaoUsuario->idUsuario(), $caminho, $metodo)) {
			$this->logger->addInfo('Acessando listagem de acessos');

			$ctrl = new ControladoraAcesso($req->getQueryParams(), $sessaoUsuario);
			$response = $ctrl->todosParaArvore();
		} else {
			$response = [
				'acessoNegado'=> true,
				'metodo'=> $metodo
			];
		}

		return $res->withHeader('Content-type', 'application/json; charset=UTF-8')->withJson($response);
	});


	$caminho = '/acesso/{acessanteTipo}/{acessanteId}';
	$metodo = 'get';

	$app->get($caminho, function(Request $req, Response $res, $args = []) use ($app, $session, $caminho, $metodo) {
		$sessaoUsuario = new Sessao($session);

		if(Acesso::vericarAcesso($sessaoUsuario->idUsuario(), $caminho, $metodo)) {
			$this->logger->addInfo('Acessando listagem de acessos');

			$ctrl = new ControladoraAcesso($req->getQueryParams(), $sessaoUsuario);
			$response = $ctrl->comAcessanteParaArvore($args['acessanteTipo'], $args['acessanteId']);
		} else {
			$response = [
				'acessoNegado'=> true,
				'metodo'=> $metodo
			];
		}

		return $res->withHeader('Content-type', 'application/json; charset=UTF-8')->withJson($response);
	});


	$caminho = '/acesso';
	$metodo = 'post';

	$app->post($caminho, function(Request $req, Response $res, $args = []) use ($app, $session, $caminho, $metodo) {
		$sessaoUsuario = new Sessao($session);

		if(Acesso::vericarAcesso($sessaoUsuario->idUsuario(), $caminho, $metodo)) {
			$this->logger->addInfo('Acessando o cadastro de acesso');

			$ctrl = new ControladoraAcesso($req->getParsedBody(), $sessaoUsuario);
			$response = $ctrl->adicionar();
		} else {
			$response = [
				'acessoNegado'=> true,
				'metodo'=> $metodo
			];
		}

		return $res->withHeader('Content-type', 'application/json; charset=UTF-8')->withJson($response);
	});


	$caminho = '/acesso/{recursoId}/{acessanteTipo}/{acessanteId}';
	$metodo = 'delete';

	$app->delete($caminho, function(Request $req, Response $res, $args = []) use ($app, $session, $caminho, $metodo) {
		$sessaoUsuario = new Sessao($session);

		if(Acesso::vericarAcesso($sessaoUsuario->idUsuario(), $caminho, $metodo)) {
			$this->logger->addInfo('Deletando a acesso de recurso ID '. $args['recursoId'] . ', acessante tipo ' . $args['acessanteTipo'] . ' e acessante ID ' . $args['acessanteId'] . '.');

			$ctrl = new ControladoraAcesso($req->getParsedBody(), $sessaoUsuario);
			$response = $ctrl->remover($args['recursoId'], $args['acessanteTipo'], $args['acessanteId']);
		} else {
			$response = [
				'acessoNegado'=> true,
				'metodo'=> $metodo
			];
		}

		return $res->withHeader('Content-type', 'application/json; charset=UTF-8')->withJson($response);
	});
// Fim das rotas para acessos

// Início das rotas para recursos
	$caminho = '/recurso';
	$metodo = 'get';

	$app->get($caminho, function(Request $req, Response $res, $args = []) use ($app, $session, $caminho, $metodo) {
		$sessaoUsuario = new Sessao($session);

		if(Acesso::vericarAcesso($sessaoUsuario->idUsuario(), $caminho, $metodo)) {
			$this->logger->addInfo('Acessando listagem de recursos');

			$ctrl = new ControladoraRecurso($req->getQueryParams(), $sessaoUsuario);
			$response = $ctrl->todosParaArvore();
		} else {
			$response = [
				'acessoNegado'=> true,
				'metodo'=> $metodo
			];
		}

		return $res->withHeader('Content-type', 'application/json; charset=UTF-8')->withJson($response);
	});
// Fim das rotas para recursos

// Início das rotas para usuario
	$caminho = '/usuario';
	$metodo = 'get';

	$app->get($caminho, function(Request $req, Response $res, $args = []) use ($app, $session, $caminho, $metodo) {
		$sessaoUsuario = new Sessao($session);

		if(Acesso::vericarAcesso($sessaoUsuario->idUsuario(), $caminho, $metodo)) {
			$this->logger->addInfo('Acessando listagem de usuario');

			$ctrl = new ControladoraUsuario($req->getQueryParams(), $sessaoUsuario);
			$response = $ctrl->todos();
		} else {
			$response = [
				'acessoNegado'=> true,
				'metodo'=> $metodo
			];
		}

		return $res->withHeader('Content-type', 'application/json; charset=UTF-8')->withJson($response);
	});


	$caminho = '/usuario/opcoes';
	$metodo = 'get';

	$app->get($caminho, function(Request $req, Response $res, $args = []) use ($app, $session, $caminho, $metodo) {
		$this->logger->addInfo('Acessando as opções de usuários.');

		$sessaoUsuario = new Sessao($session);
		$ctrl = new ControladoraUsuario($req->getQueryParams(), $sessaoUsuario);
		$response = $ctrl->todosOpcoes();

		return $res->withHeader('Content-type', 'application/json; charset=UTF-8')->withJson($response);
	});


	$caminho = '/usuario';
	$metodo = 'post';

	$app->post($caminho, function(Request $req, Response $res, $args = []) use ($app, $session, $caminho, $metodo) {
		$sessaoUsuario = new Sessao($session);

		if(Acesso::vericarAcesso($sessaoUsuario->idUsuario(), $caminho, $metodo)) {
			$this->logger->addInfo('Acessando o cadastro de usuario');

			$ctrl = new ControladoraUsuario($req->getParsedBody(), $sessaoUsuario);
			$response = $ctrl->adicionar();
		} else {
			$response = [
				'acessoNegado'=> true,
				'metodo'=> $metodo
			];
		}

		return $res->withHeader('Content-type', 'application/json; charset=UTF-8')->withJson($response);
	});


	$caminho = '/usuario';
	$metodo = 'put';

	$app->put($caminho, function(Request $req, Response $res, $args = []) use ($app, $session, $caminho, $metodo) {
		$sessaoUsuario = new Sessao($session);

		if(Acesso::vericarAcesso($sessaoUsuario->idUsuario(), $caminho, $metodo)) {
			$this->logger->addInfo('Acessando a atualização de usuario.');

			$ctrl = new ControladoraUsuario($req->getParsedBody(), $sessaoUsuario);
			$response = $ctrl->atualizar();
		} else {
			$response = [
				'acessoNegado'=> true,
				'metodo'=> $metodo
			];
		}

		return $res->withHeader('Content-type', 'application/json; charset=UTF-8')->withJson($response);
	});


	$caminho = '/usuario/atualizar-senha';
	$metodo = 'put';

	$app->put($caminho, function(Request $req, Response $res, $args = []) use ($app, $session, $caminho, $metodo) {
		$sessaoUsuario = new Sessao($session);

		if(Acesso::vericarAcesso($sessaoUsuario->idUsuario(), $caminho, $metodo)) {
			$this->logger->addInfo('Acessando a atualização de usuario.');

			$ctrl = new ControladoraUsuario($req->getParsedBody(), $sessaoUsuario);
			$response = $ctrl->atualizarSenha();
		} else {
			$response = [
				'acessoNegado'=> true,
				'metodo'=> $metodo
			];
		}

		return $res->withHeader('Content-type', 'application/json; charset=UTF-8')->withJson($response);
	});


	$caminho = '/usuario/{id}';
	$metodo = 'delete';

	$app->delete($caminho, function(Request $req, Response $res, $args = []) use ($app, $session, $caminho, $metodo) {
		$sessaoUsuario = new Sessao($session);

		if(Acesso::vericarAcesso($sessaoUsuario->idUsuario(), $caminho, $metodo)) {
			$this->logger->addInfo('Deletando a usuario de id '. $args['id'] . '.');

			$ctrl = new ControladoraUsuario($req->getParsedBody(), $sessaoUsuario);
			$response = $ctrl->remover($args['id']);
		} else {
			$response = [
				'acessoNegado'=> true,
				'metodo'=> $metodo
			];
		}

		return $res->withHeader('Content-type', 'application/json; charset=UTF-8')->withJson($response);
	});
// Fim das rotas para usuario

// Início das rotas para colaborador
	$caminho = '/colaborador';
	$metodo = 'get';

	$app->get($caminho, function(Request $req, Response $res, $args = []) use ($app, $session, $caminho, $metodo) {
		$sessaoUsuario = new Sessao($session);

		if(Acesso::vericarAcesso($sessaoUsuario->idUsuario(), $caminho, $metodo)) {
			$this->logger->addInfo('Acessando listagem de colaborador!');

			$ctrl = new ControladoraColaborador($req->getQueryParams(), $sessaoUsuario);
			$response = $ctrl->todos();
		} else {
			$response = [
				'acessoNegado'=> true,
				'metodo'=> $metodo
			];
		}

		return $res->withHeader('Content-type', 'application/json; charset=UTF-8')->withJson($response);
	});


	$caminho = '/colaborador/{id}';
	$metodo = 'get';

	$app->get($caminho, function(Request $req, Response $res, $args = []) use ($app, $session, $caminho, $metodo) {
		$sessaoUsuario = new Sessao($session);

		if(Acesso::vericarAcesso($sessaoUsuario->idUsuario(), $caminho, $metodo)) {
			$this->logger->addInfo('Acessando listagem de colaborador!');

			$ctrl = new ControladoraColaborador($req->getQueryParams(), $sessaoUsuario);
			$response = $ctrl->comId($args['id']);
		} else {
			$response = [
				'acessoNegado'=> true,
				'metodo'=> $metodo
			];
		}

		return $res->withHeader('Content-type', 'application/json; charset=UTF-8')->withJson($response);
	});


	$caminho = '/colaborador';
	$metodo = 'post';

	$app->post($caminho, function(Request $req, Response $res, $args = []) use ($app, $session, $caminho, $metodo) {
		$sessaoUsuario = new Sessao($session);

		if(Acesso::vericarAcesso($sessaoUsuario->idUsuario(), $caminho, $metodo)) {
			$this->logger->addInfo('Acessando o cadastro de colaborador');

			$ctrl = new ControladoraColaborador($req->getParsedBody(), $sessaoUsuario);
			$response = $ctrl->adicionar();
		} else {
			$response = [
				'acessoNegado'=> true,
				'metodo'=> $metodo
			];
		}

		return $res->withHeader('Content-type', 'application/json; charset=UTF-8')->withJson($response);
	});


	$caminho = '/colaborador';
	$metodo = 'put';

	$app->put($caminho, function(Request $req, Response $res, $args = []) use ($app, $session, $caminho, $metodo) {
		$sessaoUsuario = new Sessao($session);

		if(Acesso::vericarAcesso($sessaoUsuario->idUsuario(), $caminho, $metodo)) {
			$this->logger->addInfo('Acessando a atualização de colaboradorusuario.');

			$ctrl = new ControladoraColaborador($req->getParsedBody(), $sessaoUsuario);
			$response = $ctrl->atualizar();
		} else {
			$response = [
				'acessoNegado'=> true,
				'metodo'=> $metodo
			];
		}

		return $res->withHeader('Content-type', 'application/json; charset=UTF-8')->withJson($response);
	});


	$caminho = '/colaborador/{id}';
	$metodo = 'delete';

	$app->delete($caminho, function(Request $req, Response $res, $args = []) use ($app, $session, $caminho, $metodo) {
		$sessaoUsuario = new Sessao($session);

		if(Acesso::vericarAcesso($sessaoUsuario->idUsuario(), $caminho, $metodo)) {
			$this->logger->addInfo('Deletando a colaborador de id '. $args['id'] . '.');

			$ctrl = new ControladoraColaborador($req->getParsedBody(), $sessaoUsuario);
			$response = $ctrl->remover($args['id']);
		} else {
			$response = [
				'acessoNegado'=> true,
				'metodo'=> $metodo
			];
		}

		return $res->withHeader('Content-type', 'application/json; charset=UTF-8')->withJson($response);
	});
// Fim das rotas para colaborador

// Início das rotas para login
	$caminho = '/login';
	$metodo = 'post';

	$app->post($caminho, function(Request $req, Response $res, $args = []) use ($app, $session, $caminho, $metodo) {
		$sessaoUsuario = new Sessao($session);

		$ctrl = new ControladoraLogin($req->getParsedBody(), $sessaoUsuario);
		$response = $ctrl->logar();

		return $res->withHeader('Content-type', 'application/json; charset=UTF-8')->withJson($response);
	});


	$caminho = '/logout';
	$metodo = 'post';

	$app->post($caminho, function(Request $req, Response $res, $args = []) use ($app, $session, $caminho, $metodo) {
		$sessaoUsuario = new Sessao($session);

		$ctrl = new ControladoraLogin($req->getParsedBody(), $sessaoUsuario);
		$response = $ctrl->sair();

		return $res->withHeader('Content-type', 'application/json; charset=UTF-8')->withJson($response);
	});
// Fim das rotas para login

// Início das rotas para sessão
	$caminho = '/sessao/verificar-sessao';
	$metodo = 'post';

	$app->post($caminho, function(Request $req, Response $res, $args = []) use ($app, $session, $caminho, $metodo) {
		$sessaoUsuario = new Sessao($session);
		$ctrl = new ControladoraSessao($req->getParsedBody(), $sessaoUsuario);
		$resposta = $ctrl->estaAtiva();

		if($resposta['status']) return $res->withHeader('Content-type', 'application/json; charset=UTF-8')->withJson($resposta);
		else return $res->withStatus(401)->withJson($resposta);
	});
// Fim das rotas para sessão

// Início das rotas para grupos de usuário
	$caminho = '/grupo-usuario';
	$metodo = 'get';

	$app->get($caminho, function(Request $req, Response $res, $args = []) use ($app, $session, $caminho, $metodo) {
		$sessaoUsuario = new Sessao($session);

		if(Acesso::vericarAcesso($sessaoUsuario->idUsuario(), $caminho, $metodo)) {
			$this->logger->addInfo('Acessando listagem de Grupos de usuario');

			$ctrl = new ControladoraGrupoUsuario($req->getQueryParams(), $sessaoUsuario);
			$response = $ctrl->todos();
		} else {
			$response = [
				'acessoNegado'=> true,
				'metodo'=> $metodo
			];
		}

		return $res->withHeader('Content-type', 'application/json; charset=UTF-8')->withJson($response);
	});


	$caminho = '/grupo-usuario/{id}';
	$metodo = 'get';

	$app->get($caminho, function(Request $req, Response $res, $args = []) use ($app, $session, $caminho, $metodo) {
		$sessaoUsuario = new Sessao($session);

		if(Acesso::vericarAcesso($sessaoUsuario->idUsuario(), $caminho, $metodo)) {
			$this->logger->addInfo('Acessando listagem de Grupos de usuario');

			$ctrl = new ControladoraGrupoUsuario($req->getQueryParams(), $sessaoUsuario);
			$response = $ctrl->comId($args['id']);
		} else {
			$response = [
				'acessoNegado'=> true,
				'metodo'=> $metodo
			];
		}

		return $res->withHeader('Content-type', 'application/json; charset=UTF-8')->withJson($response);
	});


	$caminho = '/grupo-usuario';
	$metodo = 'post';

	$app->post($caminho, function(Request $req, Response $res, $args = []) use ($app, $session, $caminho, $metodo) {
		$sessaoUsuario = new Sessao($session);

		if(Acesso::vericarAcesso($sessaoUsuario->idUsuario(), $caminho, $metodo)) {
			$this->logger->addInfo('Acessando o cadastro de tarefa');

			$ctrl = new ControladoraGrupoUsuario($req->getParsedBody(), $sessaoUsuario);
			$response = $ctrl->adicionar();
		} else {
			$response = [
				'acessoNegado'=> true,
				'metodo'=> $metodo
			];
		}

		return $res->withHeader('Content-type', 'application/json; charset=UTF-8')->withJson($response);
	});


	$caminho = '/grupo-usuario';
	$metodo = 'put';

	$app->put($caminho, function(Request $req, Response $res, $args = []) use ($app, $session, $caminho, $metodo) {
		$sessaoUsuario = new Sessao($session);

		if(Acesso::vericarAcesso($sessaoUsuario->idUsuario(), $caminho, $metodo)) {
			$this->logger->addInfo('Acessando a atualização de tarefas');

			$ctrl = new ControladoraGrupoUsuario($req->getParsedBody(), $sessaoUsuario);
			$response = $ctrl->atualizar();
		} else {
			$response = [
				'acessoNegado'=> true,
				'metodo'=> $metodo
			];
		}

		return $res->withHeader('Content-type', 'application/json; charset=UTF-8')->withJson($response);
	});


	$caminho = '/grupo-usuario/{id}';
	$metodo = 'delete';

	$app->delete($caminho, function(Request $req, Response $res, $args = []) use ($app, $session, $caminho, $metodo) {
		$sessaoUsuario = new Sessao($session);

		if(Acesso::vericarAcesso($sessaoUsuario->idUsuario(), $caminho, $metodo)) {
			$this->logger->addInfo('Deletando o grupo de usuário de id '. $args['id'] . '.');

			$ctrl = new ControladoraGrupoUsuario($req->getParsedBody(), $sessaoUsuario);
			$response = $ctrl->remover($args['id']);
		} else {
			$response = [
				'acessoNegado'=> true,
				'metodo'=> $metodo
			];
		}

		return $res->withHeader('Content-type', 'application/json; charset=UTF-8')->withJson($response);
	});
// Fim das rotas para grupos de usuário

// Início para Dashboard
	$caminho = '/dashboard/contadores';
	$metodo = 'get';

	$app->get($caminho, function(Request $req,  Response $res, $args = []) use ($app, $session, $caminho, $metodo) {
		$sessaoUsuario = new Sessao($session);

		if(Acesso::vericarAcesso($sessaoUsuario->idUsuario(), $caminho, $metodo)) {
			$this->logger->addInfo('Acessando o cadastro de tarefa');
			$ctrl = new ControladoraRelatorio($req->getParsedBody(), $sessaoUsuario);
			$response = $ctrl->contadores();
		} else {
			$response = [
				'acessoNegado'=> true,
				'metodo'=> $metodo
			];
		}

		return $res->withHeader('Content-type', 'application/json; charset=UTF-8')->withJson($response);
	});

	$caminho = '/dashboard/checklists-status';
	$metodo = 'get';

	$app->get($caminho, function(Request $req,  Response $res, $args = []) use ($app, $session, $caminho, $metodo) {
		$sessaoUsuario = new Sessao($session);

		if(Acesso::vericarAcesso($sessaoUsuario->idUsuario(), $caminho, $metodo)) {
			$this->logger->addInfo('Acessando o cadastro de tarefa');
			$ctrl = new ControladoraRelatorio($req->getParsedBody(), $sessaoUsuario);
			$response = $ctrl->checklistsPorStatus();
		} else {
			$response = [
				'acessoNegado'=> true,
				'metodo'=> $metodo
			];
		}

		return $res->withHeader('Content-type', 'application/json; charset=UTF-8')->withJson($response);
	});

	$caminho = '/dashboard/quantidade-pa-pe';
	$metodo = 'get';

	$app->get($caminho, function(Request $req,  Response $res, $args = []) use ($app, $session, $caminho, $metodo) {
		$sessaoUsuario = new Sessao($session);

		if(Acesso::vericarAcesso($sessaoUsuario->idUsuario(), $caminho, $metodo)) {
			$this->logger->addInfo('Acessando o cadastro de tarefa');
			$ctrl = new ControladoraRelatorio($req->getParsedBody(), $sessaoUsuario);
			$response = $ctrl->quantidadePaPE();
		} else {
			$response = [
				'acessoNegado'=> true,
				'metodo'=> $metodo
			];
		}

		return $res->withHeader('Content-type', 'application/json; charset=UTF-8')->withJson($response);
	});
// Fim para Dashboard
?>