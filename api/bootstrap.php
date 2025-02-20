<?php

include __DIR__.'/vendor/autoload.php';

// use Config;
use Illuminate\Database\Capsule\Manager as DB;

$config = Config::getInstance();

if ($config->getConfiguration('debug')) {
    $dbConfig = $config->getConfiguration('development');

    $run = new Whoops\Run;
    $handler = new Whoops\Handler\PrettyPageHandler;
    $run->pushHandler($handler);
    $run->register();
} else {
    $dbConfig = $config->getConfiguration('production');
}

$db = new Db;

$db->addConnection([
    'driver'    => 'mysql',
    'host'      => $dbConfig['host'],
    'database'  => $dbConfig['name'],
    'username'  => $dbConfig['user'],
    'password'  => $dbConfig['pass'],
    'charset'   => 'utf8',
    'collation' => 'utf8_unicode_ci',
    'prefix'    => '',
]);

$db->setAsGlobal();
$db->bootEloquent();

include __DIR__.'/modules/comum/app.php'; //nova linha