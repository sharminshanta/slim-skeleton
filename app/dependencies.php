<?php
use Slim\Container;
use Slim\Http\Request;
use Slim\Http\Response;
use Monolog\Logger;
use Illuminate\Database\Capsule\Manager as Capsule;
use Illuminate\Events\Dispatcher;
use Monolog\Handler\StreamHandler;

//Initialize the container
$container = $app->getContainer();

/**
 * @param Container $container
 * @return \Slim\Views\Twig
 * @throws \Interop\Container\Exception\ContainerException
 */
$container['view'] = function (Container $container) {
    $settings = $container->get('settings');
    $twig = new \Slim\Views\Twig($settings['view']['template_path'], $settings['view']['twig']);
    $twig->addExtension(new \Slim\Views\TwigExtension(
        $container['router'],
        $container['request']->getUri()
    ));

    $twig->addExtension(new Twig_Extension_Debug());

    $twigEnvironment = $twig->getEnvironment();
    $twigEnvironment->addGlobal('session', $_SESSION);
    $twigEnvironment->addGlobal('config', $settings);

    return $twig;
};

/**
 * @param \Psr\Container\ContainerInterface $container
 * @return \Noodlehaus\Config
 */
$container['config'] = function (\Psr\Container\ContainerInterface $container) {
    return new \Noodlehaus\Config(dirname(__DIR__) . DIRECTORY_SEPARATOR . "config.php");
};

/**
 * @param Container $container
 * @return Logger
 * @throws \Interop\Container\Exception\ContainerException
 */
$container['logger'] = function (Container $container) {
    $config = $container->get('config');
    $logger = new Logger($config['app']['logger']['name']);
    $logger->pushProcessor(new \Monolog\Processor\ProcessIdProcessor());
    $logger->pushProcessor(new \Monolog\Processor\PsrLogMessageProcessor());
    $logger->pushProcessor(new \Monolog\Processor\WebProcessor());
    $logger->pushHandler(new StreamHandler($config['app']['logger']['path']));

    return $logger;
};

/**
 * @param $container
 * @return \Slim\Flash\Messages
 */
$container['flash'] = function () {
    return new \Slim\Flash\Messages();
};

/**
 * @param Container $container
 * @return \Sharminshanta\Web\Accounts\Model\ModelLoader
 */
$container['model'] = function (Container $container) {
    $models = new \Sharminshanta\Web\Accounts\Model\ModelLoader();
    return $models;
};

/**
 * @param \Psr\Container\ContainerInterface $container
 * @return \Sharminshanta\Web\Accounts\Controller\Email
 * @throws \Psr\Container\ContainerExceptionInterface
 * @throws \Psr\Container\NotFoundExceptionInterface
 */
$container['email'] = function (\Psr\Container\ContainerInterface $container) {
    $config = $container->get('config');
    $logger = $container->get('logger');
    return new \Sharminshanta\Web\Accounts\Controller\Email($config, $logger);
};

/**
 * @param \Psr\Container\ContainerInterface $container
 *
 * @return Closure
 */
$container['notFoundHandler'] = function (\Psr\Container\ContainerInterface $container) {
    return function (\Slim\Http\Request $request, \Slim\Http\Response $response) use ($container) {
        return $response->withStatus(404)->withHeader('Content-Type', 'text/html')->write("Not found");
    };
};

/**
 * @param \Psr\Container\ContainerInterface $container
 *
 * @return \Psr\Http\Message\ResponseInterface|Closure
 */
$container['errorHandler'] = function (\Psr\Container\ContainerInterface $container) {
    return function (\Slim\Http\Request $request, \Slim\Http\Response $response, Exception $exception
    ) use ($container) {
        /** @var $logger Logger * */
        $logger = $container->get('logger');
        $logger->error($exception->getMessage(), ['trace' => $exception->getTraceAsString()]);
        return $response->withStatus(500)->withHeader('Content-Type',
            'text/html')->write("Internal server error");
    };
};

//Database configuration with illuminate Database
$capsule = new Capsule;

foreach ($container['settings']['databases'] as $key => $value) {
    $capsule->addConnection($value, $key);
}

$events = new Dispatcher(new \Illuminate\Container\Container());
$events->listen('Illuminate\Database\Events\QueryExecuted', function ($query) use ($container) {
    $logger = $container->get('logger');
    $logger->info(sprintf("[mysql_query] %s executed in %f milliseconds", $query->sql, $query->time),
        ['pdo_bindings' => $query->bindings]);
});

$capsule->setEventDispatcher($events);
$capsule->setAsGlobal();
$capsule->bootEloquent();

