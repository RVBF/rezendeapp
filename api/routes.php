<?php
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

// Início das rotas para Medicamentos Precificados
$app->get('/categorias', function(Request $req,  Response $res, $args = []) use ($app)
{
	$this->logger->addInfo("Acessando listagem de categorias");
	
	$ctrl = new ControladoraCategoria($req->getQueryParams());
	$categorias =  $ctrl->todos();
});
?>