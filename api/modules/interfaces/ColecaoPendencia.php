<?php

/**
 *	Coleção de Pendência
 *
 *  @author		Rafael Vinicius Barros
 *  @version	1.0
 */

interface ColecaoPendencia extends Colecao{
	function todos($limite = 0, $pulo = 0, $search = '');
}
?>