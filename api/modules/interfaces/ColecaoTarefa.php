<?php

/**
 *	Coleção de Tarefa
 *
 *  @author		Rafael Vinicius Barros
 *  @version	0.1
 */

interface ColecaoTarefa extends Colecao{
	function comPerguntaId($id);
	function todosComLojaIds($limite = 0, $pulo = 0, $search = '', $idsLojas = []);
	function contagem($idsLojas = []);
}
?>