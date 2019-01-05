<?php

/**
 *	Coleção de Grupo de Usuario
 *
 *  @author		Rafael Vinicius Barros
 *  @version	0.1
 */

interface ColecaoGrupoUsuario extends Colecao {
    function todosComIds($ids = []);
    function comUsuarioId($id = 0);
    function todos($limite = 0, $pulo = 0, $search = '');
}
?>