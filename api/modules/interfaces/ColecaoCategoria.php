<?php

/**
 *	Coleção de CAtegoria
 *
 *  @author		Rafael Vinicius Barros
 *  @version	0.1
 */

interface ColecaoCategoria extends Colecao {
    function todos($limite = 0, $pulo = 0, $search = '');

}
?>