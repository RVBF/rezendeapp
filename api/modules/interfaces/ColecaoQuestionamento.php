<?php

/**
 *	Coleção de Questionamento
 *
 *  @author		Rafael Vinicius Barros
 *  @version	1.0
 */

interface ColecaoQuestionamento extends Colecao{
    function adicionarTodos($objetos = []);
    function questionamentosParaExecucao($checklistId);
    function questionamentosComStatus($status = []);
    function questionamentosComChecklistId($checklistId = 0);
}
?>