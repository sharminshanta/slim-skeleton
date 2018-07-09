<?php

namespace Sharminshanta\Web\Accounts\Controller;

use Illuminate\Database\Capsule\Manager;
use Sharminshanta\Web\Accounts\Model\DefaultModel;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Illuminate\Database\Query\Builder;
use Slim\Http\Request;
use Slim\Http\Response;

/**
 * Class DefaultController
 * @package Sharminshanta\Web\Accounts\Controller
 */
class DefaultController extends AppController
{
    /**
     * @param Request $request
     * @param Response $response
     * @param $args
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    public function defaultView(Request $request, Response $response, $args)
    {
        //var_dump($this->getModel()->getDefault()->getAll()->toArray()); die();

        $this->getView()->render($response, 'default.twig');
    }
}