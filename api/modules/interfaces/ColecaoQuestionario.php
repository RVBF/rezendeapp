<?php

/**
 *	Coleção de Questionario
 *
 *  @author		Rafael Vinicius Barros
 *  @version	1.0
 */

interface ColecaoQuestionario extends Colecao{
    function todos($limite = 0, $pulo = 0, $search = '');
}
?>