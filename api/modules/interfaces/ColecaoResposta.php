<?php

/**
 *	Coleção de Resposta
 *
 *  @author		Rafael Vinicius Barros
 *  @version	0.1
 */

interface ColecaoResposta extends Colecao {
  function todosComTarefaId($limite = 0, $pulo = 0, $tarefaid = 0);
  function comPerguntaId($id = 0);
}
?>