<?php

/**
 *	Coleção de Grupo de Usuario
 *
 *  @author		Rafael Vinicius Barros
 *  @version	1.0
 */

interface ColecaoGrupoUsuario extends Colecao {
	function todosComIdsDeUsuario($ids = []);
	function comUsuarioId($id = 0);
	function todos($limite = 0, $pulo = 0, $search = '');
}
?>