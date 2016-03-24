<?php
declare(strict_types = 1);

namespace Tetris;

use stdClass;
use Slim\Http\Response;
use Slim\Http\Request;

class AuthMiddleware extends Component
{
    public function __invoke(Request $req, Response $res, callable $next): Response
    {
        /**
         * @var ApiService $api
         */
        $api = $this->container['api'];
        $this->container['auth']->user = $api->fetchCurrentUser();

        return $next($req, $res);
    }
}