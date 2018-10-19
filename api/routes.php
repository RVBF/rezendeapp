<?php

// Início das rotas para Medicamentos Precificados
$app->get('/categorias', function() use ($app)
{
	$params = $app->request->get();
	$geradoraResposta = new GeradoraRespostaComSlim($app);
	$session = new Session();
	$sessaoUsuario = new Sessao($session);
	$ctrl = new ControladoraMedicamentoPrecificado($geradoraResposta, $params, $sessaoUsuario);
	$ctrl->todos();
});
?>