<?php

/**
 *	Coleção de Colaborador
 *
 *  @author		Rafael Vinicius Barros
 *  @version	1.0
 */

interface ColecaoColaborador extends Colecao {
    function atualizarAvatar(&$obj);
    function todos($limite = 0, $pulo = 0, $search = '');

}
?>