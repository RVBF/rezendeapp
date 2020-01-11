<?php
require_once __DIR__.'/../bootstrap.php';

use Illuminate\Database\Capsule\Manager as DB;

$debug = true;

if($debug) echo PHP_EOL . 'Obtendo os dados de recursos e rotas...' . PHP_EOL;

$recursosMatriz = Recurso::todosOsRecursosERotas();
// Util::printr(Recurso::todosOsRecursosERotas());
if($debug) echo PHP_EOL . 'Esvaziando as tabelas de recursos e rotas...' . PHP_EOL;

DB::table('recurso')->truncate();
DB::table('rota')->truncate();

foreach($recursosMatriz as $recursoArray) {
   if($debug) echo PHP_EOL . 'Salvando o recurso ' . $recursoArray['nome'] . ' (' . $recursoArray['model'] . ')...' . PHP_EOL;

   $recurso = DB::table('recurso')->insert([
      'id' => $recursoArray['id'],
      'nome' => $recursoArray['nome'],
      'model' => $recursoArray['model']
   ]);

   foreach($recursoArray['rotas'] as $rotaArray) {
      if($debug) echo 'Salvando a rota ' . $rotaArray['caminho'] . ' (' . $rotaArray['metodo'] . ')...' . PHP_EOL;

      $recurso = DB::table('rota')->insert([
         'id' => $rotaArray['id'],
         'caminho' => $rotaArray['caminho'],
         'metodo' => $rotaArray['metodo'],
         'recursoId' => $recursoArray['id']
      ]);
   }
}
?>