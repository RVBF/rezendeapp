<?php

/**
 *	Coleção de Plano De Acao
 *
 *  @author		Rafael Vinicius Barros
 *  @version	1.0
 */

interface ColecaoPlanoAcao extends Colecao{
	function todosComResponsavelId($limite = 0, $pulo = 0, $search = '', $responsavelId = 0);
	function contagem($responsavelId = 0);
	function todosComChecklistId($limite = 0, $pulo = 10, $search = '', $colaboradorId = 0, $checklistId = 0);
	function executar(&$obj);
}
?>