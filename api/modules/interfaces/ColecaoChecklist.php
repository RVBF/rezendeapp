<?php

/**
 *	Coleção de Checklist
 *
 *  @author		Rafael Vinicius Barros
 *  @version	1.0
 */

interface ColecaoChecklist extends Colecao{
	function comPerguntaId($id);
	function todosComLojaIds($limite = 0, $pulo = 0, $search = '', $idsLojas = []);
	function contagem($idsLojas = []);
	function listagemTemporalcomLojasIds($pageHome = 0,$pageLength = 10, $search = '', $idsLojas = []);
}
?>