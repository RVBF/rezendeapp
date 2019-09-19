<?php

/**
 *	Coleção de Checklist
 *
 *  @author		Rafael Vinicius Barros
 *  @version	0.1
 */

interface ColecaoChecklist extends Colecao{
	function comPerguntaId($id);
	function todosComLojaIds($limite = 0, $pulo = 0, $search = '', $idsLojas = []);
	function contagem($idsLojas = []);
}
?>