<?php
use Illuminate\Database\Capsule\Manager as DB;
/**
 *	Coleção de Permissaões administrativa em Banco de Dados Relacional.
 *
 *  @author		Rafael Vinicius Barros Ferreira
 *	@version	1.0
 */

class ColecaoPermissaoAdministrativaEmBDR implements ColecaoPermissaoAdministrativa
{

	const TABELA_GRUPO_USUARIOS = 'grupo_usuario';
	const TABELA_USUARIOS = 'usuario';

	function __construct(){}

    function configurar(&$obj) {
        try {	
            DB::statement('SET FOREIGN_KEY_CHECKS=0;');
            
            DB::table(self::TABELA_GRUPO_USUARIOS)->where('administrador', true)->whereNotIn('id', $obj->getGrupos())->update(['administrador' => false]);
            DB::table(self::TABELA_USUARIOS)->where('administrador', true)->whereNotIn('id', $obj->getUsuarios())->update(['administrador' => false]);
 
 
            DB::table(self::TABELA_GRUPO_USUARIOS)->whereIn('id', $obj->getGrupos())->update(['administrador' => true]);
            DB::table(self::TABELA_USUARIOS)->whereIn('id', $obj->getUsuarios())->update(['administrador' => true]);


            DB::statement('SET FOREIGN_KEY_CHECKS=1;');
        }
        catch (\Exception $e)
        {
            throw new ColecaoException("Erro ao salvar permissões no banco de dados.", $e->getCode(), $e);
        }
    }
}

?>