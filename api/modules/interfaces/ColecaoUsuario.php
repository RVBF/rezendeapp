<?php

/**
 *	Coleção de Usuario
 *
 *  @author		Rafael Vinicius Barros
 *  @version	1.0
 */

interface ColecaoUsuario extends Colecao {
    function novaSenha($id, $senhaAtual, $novaSenha, $confirmacaoSenha);
    function comLogin($login);
    function todosComIds($ids = []);
    function todos($limite = 0, $pulo = 0, $search = '');
}
?>