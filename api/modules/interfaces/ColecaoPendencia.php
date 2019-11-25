<?php

/**
 *	Coleção de Pendência
 *
 *  @author		Rafael Vinicius Barros
 *  @version	1.0
 */

interface ColecaoPendencia extends Colecao{
	function todos($limite = 0, $pulo = 0, $search = '');
	function todosComResponsavelId($limite = 0, $pulo = 0, $search = '', $responsavelId = 0);
	function todosComChecklistId($limite = 0, $pulo = 10, $search = '', $colaboradorId = 0, $checklistId = 0);
}
?>