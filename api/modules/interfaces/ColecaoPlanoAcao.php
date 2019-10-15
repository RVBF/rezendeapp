<?php

/**
 *	Coleção de Plano De Acao
 *
 *  @author		Rafael Vinicius Barros
 *  @version	1.0
 */

interface ColecaoPlanoAcao extends Colecao{
	function todos($limite = 0, $pulo = 0, $search = '');
}
?>