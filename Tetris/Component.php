<?php

namespace Tetris;

use Slim\App;
use Slim\Container;

class Component
{

    /**
     * @var App
     */
    protected $app;

    /**
     * @var Container
     */
    protected $container;

    public function __construct(App $app)
    {
        $this->app = $app;
        $this->container = $app->getContainer();
    }
}