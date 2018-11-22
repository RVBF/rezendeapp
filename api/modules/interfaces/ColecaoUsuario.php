<?php

/**
 *	Coleção de Usuario
 *
 *  @author		Rafael Vinicius Barros
 *  @version	0.1
 */

interface ColecaoUsuario extends Colecao {
    function novaSenha($senhaAtual, $novaSenha, $confirmacaoSenha);
	function comLogin($login);
}
?>