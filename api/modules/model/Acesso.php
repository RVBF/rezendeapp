<?php
use Illuminate\Database\Capsule\Manager as DB;

use phputil\traits\ToArray;
use phputil\traits\GetterSetterWithBuilder;
use phputil\traits\FromArray;
/**
 *	Acesso
 *
 *  @author Leonardo Carvalhães Bernardo
 *  @version	1.0
 */
class Acesso {
   use GetterSetterWithBuilder;
   use ToArray;
   use FromArray;

   private $id;
   private $recurso;
   private $acessante;
   private $acao;

   const TABELA = 'acesso';

   const PERMITIR = 'Permitir';
   const NEGAR = 'Negar';

   function __construct($id = 0, $recurso = null, $acessante = null, $acao = self::PERMITIR) {
      $this->id = $id;
      $this->recurso = $recurso;
      $this->acessante = $acessante;
      $this->acao = $acao;
   }

   public static function vericarAcesso($usuarioId, $caminho, $metodo) {
      $gruposIds = DB::table(GrupoUsuario::TABELA)
                  ->select(GrupoUsuario::TABELA . '.id')
                  ->join(GrupoUsuario::TABELA_RELACAO_USUARIO, GrupoUsuario::TABELA . '.id', '=', GrupoUsuario::TABELA_RELACAO_USUARIO . '.grupo_usuario_id')
                  ->where(GrupoUsuario::TABELA_RELACAO_USUARIO . '.usuario_id', $usuarioId)
                  ->lists('id');

      if(empty($gruposIds)) $gruposIds = [0];

      $query = DB::table(self::TABELA)->select(self::TABELA . '.*')
                     ->join(Recurso::TABELA, Recurso::TABELA . '.id', '=', Acesso::TABELA . '.recursoId')
                     ->join(Rota::TABELA, Recurso::TABELA . '.id', '=', Rota::TABELA . '.recursoId')
                     ->where(function($query) use ($usuarioId, $gruposIds) {
                        $query->where(function($query) use ($usuarioId) {
                           $query->where(Acesso::TABELA . '.acessanteId', $usuarioId)
                                 ->where(Acesso::TABELA . '.acessanteTipo', Usuario::class);
                        })
                        ->orWhere(function($query) use ($gruposIds) {
                           $query->whereIn(Acesso::TABELA . '.acessanteId', $gruposIds)
                                 ->where(Acesso::TABELA . '.acessanteTipo', GrupoUsuario::class);
                        });
                     })
                     ->where(Rota::TABELA . '.caminho', $caminho)
                     ->where(Rota::TABELA . '.metodo', $metodo)
                     ->where(self::TABELA . '.acao', self::PERMITIR);

      $acessos = $query->get();

      return !empty($acessos);
   }
}
?>