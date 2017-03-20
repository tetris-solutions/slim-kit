<?php

namespace Tetris\Middlewares;

use Tetris\Component;
use Slim\Http\Response;
use Slim\Http\Request;
use Tetris\Services\ApiService;
use Tetris\Services\AuthService;

class AuthMiddleware extends Component
{
    public function __invoke(Request $req, Response $res, callable $next): Response
    {
        /**
         * @var ApiService $api
         */
        $api = $this->container['api'];
        /**
         * @var AuthService $auth
         */
        $auth = $this->container['auth'];
        $userResp = $api->requestUser();

        if (isset($userResp->headers['authorization'])) {
            $authHeader = $userResp->headers['authorization'];
            $res = $res->withHeader('Authorization', $authHeader);
        }

        $auth->user = $api->responseToObject($userResp);

        return $next($req, $res);
    }
}