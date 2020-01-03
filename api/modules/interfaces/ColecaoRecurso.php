<?php

/**
 *	Coleção de Recurso
 *
 *  @author Leonardo Carvalhães Bernardo
 *  @version 1.0
 */

interface ColecaoRecurso {
   function comNome($nome);
   function comModel($model);
   function todosComIds($ids = []);
   function todos($limite = 0, $pulo = 0);
   function contagem();
}
?>