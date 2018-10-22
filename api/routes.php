<?php
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

// Início das rotas para Medicamentos Precificados
$app->get('/categorias', function(Request $req,  Response $res, $args = []) use ($app)
{
	$this->logger->addInfo("Acessando listagem de categorias");
	
	Debuger::printr($req->getQueryParams());
	// $session = new Session();
	// $sessaoUsuario = new Sessao($session);
	// $ctrl = new ControladoraCategoria($params);
	// $ctrl->todos();
});
?>