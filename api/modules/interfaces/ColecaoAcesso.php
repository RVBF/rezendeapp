<?php

/**
 *	Coleção de Acesso
 *
 *  @author Leonardo Carvalhães Bernardo
 *  @version 1.0
 */

interface ColecaoAcesso extends Colecao{
   function comUsuarioId($id);
   function comGrupoId($id);
   function comRecursoId($id);
}
?>