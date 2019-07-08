<?php

namespace Sharminshanta\Web\Accounts\Controller;

use Noodlehaus\Config;
use Psr\Container\ContainerInterface;
use Monolog\Logger;
use Sharminshanta\Web\Accounts\Model\ModelLoader;

/**
 * Class AppController
 * @package Previewtechs\Web\CareerWebsite\Controller
 */
class AppController
{
    /**
     * @var ContainerInterface
     */
    protected $container;

    /**
     * @var \Memcached
     */
    protected $memcache;

    /**
     * @var Logger
     */
    protected $logger;

    /**
     * @var \Slim\Views\Twig
     */
    protected $view;

    /**
     * @var array
     */
    protected $settings;

    /**
     * @var
     */
    public $homeUrl;

    /**
     * @var
     */
    public $config;

    /**
     * AppController constructor.
     * @param ContainerInterface $container
     */
    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
        $home = (isset($_SERVER['HTTPS']) ? "https" : "http") . "://" . $_SERVER['HTTP_HOST'];
        $this->homeUrl = rtrim($home, '/');
    }

    /**
     * @return Logger
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    public function getLogger()
    {
        return $this->container->get('logger');
    }

    /**
     * @return \Slim\Views\Twig
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    public function getView()
    {
        return $this->container->get('view');
    }

    /**
     * @return mixed
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    public function getSettings()
    {
        return $this->container->get('settings');
    }

    /**
     * @return \Slim\Flash\Messages
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    public function getFlash()
    {
        return $this->container->get('flash');
    }

    /**
     * @return Email
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    public function getEmail()
    {
        return $this->container->get('email');
    }

    /**
     * @return Config
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     * @return config info
     */
    public function getConfig()
    {
        return $this->container->get('config');
    }

    /**
     * @return ModelLoader
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    public function getModel()
    {
        return $this->container->get('model');
    }
}