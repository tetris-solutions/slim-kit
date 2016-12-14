<?php

namespace Tetris\Middlewares;

use Tetris\Component;
use Slim\Http\Response;
use Slim\Http\Request;
use Tetris\Services\ApiService;
use Tetris\Services\AuthService;

class AuthMiddleware extends Component
{
    function __invoke(Request $req, Response $res, callable $next): Response
    {
        $path = $req->getUri()->getPath();

        $publicPrefix = '/public/';
        $isPublicPath = strpos($path, $publicPrefix) === 0;

        if (!$isPublicPath) {
            /**
             * @var ApiService $api
             */
            $api = $this->container['api'];
            /**
             * @var AuthService $auth
             */
            $auth = $this->container['auth'];
            $auth->user = $api->fetchCurrentUser();
        }

        return $next($req, $res);
    }
}