<?php

/**
 *	Coleção de Setor
 *
 *  @author		Rafael Vinicius Barros
 *  @version	0.1
 */

interface ColecaoSetor extends Colecao{
    function todos($limite = 0, $pulo = 0, $search = '');
}
?>