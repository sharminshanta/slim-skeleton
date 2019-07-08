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

    $view = new \Slim\Views\Twig(ROOT_DIR . DIRECTORY_SEPARATOR . "templates", [
        'debug' => true,
        'auto_reload' => true,
    ]);

    // Instantiate and add Slim specific extension
    $router = $container->get('router');
    $uri = \Slim\Http\Uri::createFromEnvironment(new \Slim\Http\Environment($_SERVER));
    $view->addExtension(new \Slim\Views\TwigExtension($router, $uri));
    $view->addExtension(new Twig_Extension_Debug());

    $twigEnvironment = $view->getEnvironment();
    $twigEnvironment->addGlobal('message', $container->get('flash')->getMessages());
    $twigEnvironment->addGlobal('session', $_SESSION);

    return $view;
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
 * @return \Sharminshanta\Web\Accounts\Model\ModelLoader
 */
$container['models'] = function (\Psr\Container\ContainerInterface $container) {
    $modelLoader = new \Sharminshanta\Web\Accounts\Model\ModelLoader();
    $container->get("logger") instanceof LoggerInterface ? $modelLoader->setLogger($container->get("logger")) : null;
    $container->get("cache") instanceof CacheItemPoolInterface ? $modelLoader->setCache($container->get("cache")) : null;
    return $modelLoader;
};

/**
 * @param Container $container
 * @return Logger
 */
$container['logger'] = function (\Slim\Container $container) {
    $logger = new Logger(getenv('LOGGER_NAME'));
    $logger->pushProcessor(new \Monolog\Processor\ProcessIdProcessor());
    $logger->pushProcessor(new \Monolog\Processor\PsrLogMessageProcessor());
    $logger->pushProcessor(new \Monolog\Processor\WebProcessor());

    if (getenv('LOGGER_HANDLER') === 'syslog') {
        $logger->pushHandler(new \Monolog\Handler\SyslogHandler(getenv('LOGGER_NAME')));
    }

    if (getenv('LOGGER_HANDLER') === 'file') {
        $logger->pushHandler(new StreamHandler(getenv('LOGGER_FILE_PATH')));
    }

    return $logger;
};
/**
 * @param \Psr\Container\ContainerInterface $container
 * @return Pool
 */
$container['cache'] = function (\Psr\Container\ContainerInterface $container){
    if(class_exists("Memcached") || class_exists("Memcache")){
        $driver = new Memcache(array('servers' => array('127.0.0.1', '11211')));
    }else{
        $driver = new FileSystem();
    }

    $pool = new Pool($driver);
    $pool->setNamespace("DokanApp");

    return $pool;
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

