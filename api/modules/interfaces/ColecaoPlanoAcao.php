<?php

/**
 *	Coleção de Plano De Acao
 *
 *  @author		Rafael Vinicius Barros
 *  @version	0.1
 */

interface ColecaoPlanoAcao extends Colecao{
	function todos($limite = 0, $pulo = 0, $search = '');
}
?>