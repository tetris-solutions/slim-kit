<?php

namespace Tetris;

use Slim\App;

use Tetris\Services\ErrorHandlerService;
use Tetris\Services\FlagsService;
use Tetris\Services\AuthService;
use Tetris\Services\ApiService;

use Tetris\Middlewares\AuthMiddleware;
use Tetris\Middlewares\CrossOriginResourceSharingMiddleware;
use Tetris\Middlewares\InitializeMiddleware;

class Slimmest extends App
{
    public function run($silent = false)
    {
        $container = $this->getContainer();

        $container['errorHandler'] = new ErrorHandlerService($this);
        $container['flags'] = new FlagsService($this);
        $container['auth'] = new AuthService($this);
        $container['api'] = new ApiService($this);

        $this->add(
            isset($container['settings']['authMiddleware'])
                ? $container['settings']['authMiddleware']
                : new AuthMiddleware($this)
        );
        $this->add(new CrossOriginResourceSharingMiddleware($this));
        $this->add(new InitializeMiddleware($this));

        return parent::run($silent);
    }
}