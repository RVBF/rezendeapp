<?php    
    use Slim\App;
    use Slim\Container;

    $config = Config::getInstance();
    
    //Começa aqui a alteração
    $slimConfig = [
        'settings' => [
            'displayErrorDetails' => $config->getConfiguration('debug'),
        ]
    ];

    $container = new Slim\Container($slimConfig);
    
    SlimWhoops::init();

    $container['errorHandler'] = function ($container) {
        return function ($request, $response, $exception) use ($container) {
            SlimWhoops::integrate($exception);
        };
    };

    $app = new Slim\App($container);
?>