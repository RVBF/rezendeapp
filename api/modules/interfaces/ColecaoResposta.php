<?php

/**
 *	Coleção de Resposta
 *
 *  @author		Rafael Vinicius Barros
 *  @version	1.0
 */

interface ColecaoResposta extends Colecao {
  function todosComTarefaId($limite = 0, $pulo = 0, $tarefaid = 0,  $search = '');
  function comPerguntaId($id = 0);
	function contagem($tarefaId = 0);
}
?>