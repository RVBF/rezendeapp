<?php

/**
 *	Coleção de Loja
 *
 *  @author		Rafael Vinicius Barros
 *  @version	1.0
 */

interface ColecaoLoja extends Colecao {
    function todosComIds($ids = []);
    function comColaboradorId($id);
    function todos($limite = 0, $pulo = 0, $search = '');
}
?>